<?PHP
/*
* Practical Agile Scrum tool
*
* Copyright 2013-2017, P.P. Labuschagne

* Released under the MIT license.
* https://github.com/paul-lab/practical-agile/blob/master/_Licence.txt
*
* Homepage:
*   	http://practicalagile.co.uk
*	http://practicalagile.uk
*
*/

include 'include/header.inc.php';

$showForm = true;

if (isset($_POST['acceptImport'])) header('Location: project_Summary.php?PID='.$_POST['PID']);

	if (isset($_POST['saveUpload'])){
		$hasError = false;
		if ($_FILES["file"]["error"] > 0){
			echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
		}else{
			//echo $_FILES["file"]["tmp_name"];
			//echo file_get_contents($_FILES['file']['tmp_name']);

// Check the number of columns in the import file
			$fileTemp = $_FILES["file"]["tmp_name"];
			$fp = fopen($fileTemp,'r');
			if (($header = fgetcsv($fp)) !== FALSE){
			// we now have a header
				if (count($header)!== 17 and count($header)!== 18){
					echo '<br> Invalid Number of Columns, expected 17/18 received '.count($header);
					die;
				}

// fetch all the users
				$sql = 'SELECT * FROM user';
				$user_Res = $DBConn->directsql($sql);
				$user_id['']=0;
				foreach ($user_Res  as $user_Row){
					$user_id[$user_Row['Friendly_Name']] = $user_Row['ID'];
				}
				unset($user_Res);

// fetch all the iterations
				$sql = 'SELECT * FROM iteration where iteration.Project_ID= ?';
				$iter_Res = $DBConn->directsql($sql, $_REQUEST['PID']);
				foreach ($iter_Res as $iter_Row){
					$iter_id[$iter_Row['Name']] = $iter_Row['ID'];
				}

// fetch all the releases
				$release_id['']=0;
				$sql = 'SELECT * FROM release_details';
				$release_Res = $DBConn->directsql($sql);
				foreach ($release_Res as $release_Row){
					$release_id[$release_Row['Name']] = $release_Row['ID'];
				}
				unset($release_Res);

				while (($data = fgetcsv($fp)) !== FALSE){	// loop through the records to be imported.
					if ($data[11]) { //  Summary must be present, if not then skip
						$datao=array(
							'Type'			=> $data[7],
							'Project_ID'	=> $_REQUEST['PID'],
							'Status'		=> (trim($data[8])),
							'Size'			=> $data[9],
							'Blocked'		=> $data[10],
							'Summary'		=> (htmlentities( $data[11],ENT_QUOTES)),
							'Col_1'			=> (htmlentities( $data[12],ENT_QUOTES)),
							'As_A'			=> (htmlentities( $data[13],ENT_QUOTES)),
							'Col_2'			=> (htmlentities( $data[14],ENT_QUOTES)),
							'Acceptance'	=> (htmlentities( $data[15],ENT_QUOTES)),
							'Tags'			=> $data[16],
							'Epic_Rank'		=> $data[1],
							'Iteration_Rank'=> $data[2],
							'Parent_Story_ID' =>0,
							'Release_ID'	=> $release_id[$data[4]+0]
						);

						if (isset($iter_id[$data[5]]) && in_array($data[5], $iter_id)){
							$datao['Iteration_ID'] = $iter_id[$data[5]];
						}else{
							$datao['Iteration_ID'] = $iter_id['Backlog'];
						}

						if (isset($user_id[$data[6]])){
							$datao['Owner_ID']	= $user_id[$data[6]];
						}else{
							$datao['Owner_ID']	= "";
						}

						if(Is_Numeric ($data[3])){	// we have a parent
							if ($data[0] + 0 == 0){
								$datao['Parent_Story_ID']="0";	// ignore for a new record
							}else{
								$psql = 'select s from (select AID s from story where ID= ? and Project_ID= ?) as s2 )';
								$pres = $DBConn->directsql($psql, array($data[3], $_REQUEST['PID']));
								if (count($pres==1)){
									$datia['Parent_Story_ID']=$pres[0]['s2'];
								}
							}
						}else{
							$data['Parent_Story_ID']="0";
						}

						$whereClause = 'Project_ID= ? and ID = ?';
						$bind = array($_REQUEST['PID'], $data[0] + 0);

						// no existing story ID in import file so it must be an insert
						if ($data[0] + 0 == 0 )  {
							$sql_method = 'CREATE';
							$datao['Created_By_ID'] = $_SESSION['ID'];
							$tsql='select (IFNULL(MAX(ID), 0)+1) as SID from story  where Project_ID= ?';
							$tresult=$DBConn->directsql($tsql, $_REQUEST['PID']);;
							$datao['ID']	= $tresult[0]['SID'];
						}else{
							$sql_method = 'UPDATE';
						}

						//  delete record if iteration is set to  ** Delete** and there is an ID
						if (($data[0] + 0 != 0) && ($data[5]=="** Delete **")){
							$sql_method = 'DELETE';
						}

						if ($sql_method == 'UPDATE'){
							$result=$DBConn->update('story',$datao,$whereClause, $bind);
						}elseif($sql_method == 'CREATE'){
							$result=$DBConn->create('story',$datao);
						}elseif($sql_method == 'DELETE'){
							$result=$DBConn->delete('story',$whereClause, $bind);
						}else{
							$result=0;
						}
						if($DBConn->error || $result==0){
							echo('<br>Error unable to '.$sql_method.' story #'.$data[0].' - '.$data[8].' '.$data[9].' '.$data[11]);
							echo'<br>'.$result.$DBConn->error;
						}else{
							echo '<br>'. $sql_method.' story '.$data[0].' - '.$data[8].' ('.$data[9].') '.$data[11];
						}
					}else{
						echo '<br>Missing Story  Summary';
					}
				}
				foreach ($iter_id as $value) {
					Update_Iteration_Points($value);
				}
			}
		}
		$showForm = false;
?>
				<center><p><p><form  enctype="multipart/form-data" method="post" action="?">
				<input type="hidden" name="PID" value="<?=$_REQUEST['PID'];?>">
				<input type="hidden" name="IID" value="<?=$story_Row['Iteration_ID'];?>">
				<br><input class="btn" type="submit" name="acceptImport" value="OK">
				</form></center>
<?php
	}

	if ($showForm){
		echo	'<center><p><form  enctype="multipart/form-data" method="post" action="?">';
?>
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
				Import Stories: <input type="file" size="50" name="file" /><p>
				<input type="hidden" name="PID" value="<?=$_REQUEST['PID'];?>">
				<input type="hidden" name="IID" value="<?=$story_Row['Iteration_ID'];?>">


<?php
		if(!$isReadonly){
			echo '<input class="btn" type="submit" name="saveUpload" value="Import">';
		}
?>
		</form></center>
		<br><br>Notes:<br>
		<ul>
		<li><b>The first row of the import file MUST contain the correct column headers in the correct order.</b>
		<li>You can only import into the current project.
		</ul>
		<ul>
		<li>Where there is No story ID in the import file, the story will be ADDED. (Multiple imports will create duplicate stories!)
		<li>Where a story ID is presents in the import file, the story will be updated.
		<br>&nbsp; &nbsp; &nbsp; If there is no story with that ID is is <b>NOT</b> added, it is <b>skipped</b>.
		<li>If the iteration does not exist, the story is put in the backlog.
		<li>An attempt to assign the correct owner is made, but if they can't be found the owner is set to an empty string.
		<li>AS Parent records must exist before child records can be assigned to them, no story hiearchy is created for new stories.
		<li>The Parent for a story is only added/updated if there is a Parent Story Id and we are updating an existing story (to remove a story's parent, set it it 0).
		</ul>
		<ul>
		<li>To DELETE a story, set the Iteration to "** Delete **" (without the quotes)
		</ul>
		<ul>
		<li>If an error occurs during the import, that story is skipped and the next record imported.

		</ul>
<?php
	}
	include 'include/footer.inc.php';
?>