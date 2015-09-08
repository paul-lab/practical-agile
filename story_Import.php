<?PHP

include 'include/header.inc.php';

$showForm = true;

if (isset($_POST['acceptImport'])) header('Location: project_Summary.php?PID='.$_POST['PID']);

	if (isset($_POST['saveUpload']))
	{
		$hasError = false;
//		echo $_FILES["file"]["type"];

		if ($_FILES["file"]["error"] > 0)
		{
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
			$user_Res = mysqli_query($DBConn, $sql);
			$user_id['']=0;
			if ($user_Row = mysqli_fetch_assoc($user_Res))
			{
				do
				{
					$user_id[$user_Row['Friendly_Name']] = $user_Row['ID'];
				}while ($user_Row = mysqli_fetch_assoc($user_Res));
			}
//echo '####>';
//echo $user_id['User Name'];
//echo '<####';

// fetch all the iterations
			$sql = 'SELECT * FROM iteration where iteration.Project_ID='.$_REQUEST['PID'];
			$iter_Res = mysqli_query($DBConn, $sql);
			if ($iter_Row = mysqli_fetch_assoc($iter_Res))
			{
				do
				{
					$iter_id[$iter_Row['Name']] = $iter_Row['ID'];
				}while ($iter_Row = mysqli_fetch_assoc($iter_Res));
			}
//echo '####>';
//echo $iter_id['Iteration 35'];
//echo '<####';

// fetch all the releases
			$release_id['']=0;
			$sql = 'SELECT * FROM release_details';
			$release_Res = mysqli_query($DBConn, $sql);
			if ($release_Row = mysqli_fetch_assoc($release_Res))
			{
				do
				{
					$release_id[$release_Row['Name']] = $release_Row['ID'];
				}while ($release_Row = mysqli_fetch_assoc($release_Res));
			}
//echo '####>';
//echo $release_id['Release 1'];
//echo '<####';
			while (($data = fgetcsv($fp)) !== FALSE)	// loop through the records to be imported.
			{
				
				if ($data[8] && $data[11]) // Status and Summary must be present, if not then skip
				{
					// no existing story I so it must be an insert
					if ($data[0] + 0 == 0) {
						$sql_method = 'INSERT INTO story SET '.
						'Created_By_ID="'.$_SESSION['ID'].'", '.
						'ID=(select IFNULL(MAX(prj.ID), 0)+1 from story as prj where Project_ID='.$_REQUEST['PID'].') , ';
						$whereClause = '';
					}else{
						$sql_method = 'UPDATE story SET ';
						$whereClause = ' WHERE Project_ID="'.$_REQUEST['PID'].'" and ID = '.($data[0] + 0);
					}
					
					if (($data[0] + 0 != 0) && ($data[5]=="** Delete **"))
					{
						$sql = 'DELETE FROM story ';
						$sql_method = $sql;
					}else {
						$sql = $sql_method;
						$sql .='Epic_Rank="'.$data[1].'", '.
						'Iteration_Rank="'.$data[2].'", ';
						$sql .='Release_ID="'.$release_id[$data[4]].'", ';
						if (isset($iter_id[$data[5]])){
							$sql .='Iteration_ID="'.$iter_id[$data[5]].'", ';
						}else{
							$sql .='Iteration_ID="'.$iter_id['Backlog'].'", ';
						}
						if (isset($user_id[$data[6]])){
							$sql .='Owner_ID="'.$user_id[$data[6]].'", ';
						}else{
							$sql .='Owner_ID="", ';
						}
						if(Is_Numeric ($data[3])){	// we have a parent
							if ($data[0] + 0 == 0){		
								$sql .=' Parent_Story_ID="0", ';	// ignore for a new record
							}else{
								$sql .=' Parent_Story_ID=( select s from (select AID s from story where ID='.$data[3].' and Project_ID='.$_REQUEST['PID'].') as s2 ), ';
							}
						}else{
							$sql .=' Parent_Story_ID="", ';
						}
						$sql .='Type="'.$data[7].'", '.
						'Project_ID="'.$_REQUEST['PID'].'", '.
						'Status="'.trim($data[8]).'", '.
						'Size="'.$data[9].'", '.
						'Blocked="'.$data[10].'", '.
						'Summary="'.htmlentities( $data[11],ENT_QUOTES).'", '.
						'Col_1="'.htmlentities( $data[12],ENT_QUOTES).'", '.
						'As_A="'.htmlentities( $data[13],ENT_QUOTES).'", '.
						'Col_2="'.htmlentities( $data[14],ENT_QUOTES).'", '.
						'Acceptance="'.htmlentities( $data[15],ENT_QUOTES).'", '.
						'Tags="'.$data[16].'"';
					}
					$sql .= $whereClause;
					mysqli_query($DBConn, $sql);
					if(mysqli_error($DBConn))
					{
						$hasError = true;
						echo('<br>Error on record '.$data[0].' - '.$data[8].' '.$data[9].' '.$data[11]);
						echo('<br>'.mysqli_error($DBConn)).'<br>';
//echo('<br>'.$sql.'<br>');
					}else{
						echo '<br>'. substr($sql_method,0,18).' '.$data[0].' - '.$data[8].' ('.$data[9].') '.$data[11];
					}
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
				<br><input type="submit" name="acceptImport" value="OK">
				</form></center>
<?php
}

	if ($showForm)
	{

		echo	'<center><p><form  enctype="multipart/form-data" method="post" action="?">';
?>
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
				Import Stories: <input type="file" size="50" name="file" /><p>
				<input type="hidden" name="PID" value="<?=$_REQUEST['PID'];?>">
				<input type="hidden" name="IID" value="<?=$story_Row['Iteration_ID'];?>">
				
			
<?php
if(!$isReadonly)
{
echo '<input type="submit" name="saveUpload" value="Import">';
}
?>
	</form></center>
<br><br>Notes:<br>
<ul>
<li><b>The first row of the import file MUST contain the correct column headers.</b>
<li>You can only import into the current project.
<li>Parent records must exist before child records cn be assigned to them
</ul>
<ul>
<li>Where a story ID is presents in the import file, the story will be updated. 
<br>If there is no story with that ID is is <b>NOT</b> added, it is skipped.
<li>Where there is No story ID in the import file, the story will be ADDED
<li>To DELETE a story, set the Iteration to "** Delete **" (without the quotes)
<li>If an error occurs during the import, that story is skipped and the next record imported.
<li>If the iteration does not exist, the story is put in the backlog.
<li>An attempt to assign the correct owner is made, but if they can't be found the owner is set to an empty string.
<li>The Parent for a story is only added/updated if there is a numeric value as a Parent Story Id and we are not adding a new record (to remove a story's parent, set it it 0 or empty).
</ul>
<ul>
<li><b>When using Excel, make sure you do not include any empty records in you export file!<br> You will end up with a load of empty stories in your backlog
</ul>


<?php
	}
//	include 'include/footer.inc.php';
?>
