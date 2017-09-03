<?php
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

echo '<a href="project_List.php">All</a>->';
echo '<a href="project_Edit.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo Get_Iteration_Name($_REQUEST['IID']);


	$showForm = true;
	if ($_REQUEST['delete']){
		if (readonly($_REQUEST['PID']) ==0 ){
			$asql='SELECT * from story where AID='.$_REQUEST['id'];
			$aresult = $DBConn->directsql($asql);
			// for each field auditit
			foreach ($aresult as $key => $value){
				if ($aresult[$key]){auditit($_REQUEST['PID'],$_REQUEST['id'],$_SESSION['Email'],'Deleted '.$key,$aresult[$key]);}
			}

			if ($DBConn->directsql('DELETE FROM story WHERE AID='.$_REQUEST['id']. ' AND Project_ID='.$_REQUEST['PID'])==1)	{
				$asql='delete from task where Story_AID='.$_REQUEST['id'];
				$DBConn->directsql($asql);
				$asql='delete from comment where Story_AID='.$_REQUEST['id'];
				$DBConn->directsql($asql);

				$asql= "select upload.Name, upload.Desc, HEX(Name) as HName, upload.Type FROM upload WHERE upload.AID=".$_REQUEST['id'];
				$aqry=$DBConn->directsql($asql);
				foreach ($aqry as $aresult) {
					if (unlink('upload/'.$aresult['HName'].'.'.$aresult['Type'])){
						auditit($_REQUEST['PID'],$_REQUEST['id'],$_SESSION['Email'],'Deleted uploaded file ',$aresult[HName],$aresult[Desc]);
					}
				}
				$asql= "DELETE FROM upload WHERE upload.AID=".$_REQUEST['id'];
				$aqry=$DBConn->directsql($asql);
				$showForm = false;
				$deleted = true;
				Update_Iteration_Points($_REQUEST['IID']);
			}
		}
	}else if ($_REQUEST['nodelete']){
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		$Row=$DBConn->directsql('SELECT ID, Summary, Size FROM story WHERE AID='.$_REQUEST['id']. ' AND Project_ID='.$_REQUEST['PID']);
		$Row=$Row[0];
		echo '<form method="post" action="?">'.
			'<p><b>#'.$Row['ID'].' - '.$Row['Summary'].' ('.$Row['Size'].' pts.)</b><p>'.
			'Are you sure you want to delete this story, Its tasks, comments and uploaded files?<P>'.
			'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
			'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
			'<input type="hidden" name="IID" value="'.$_REQUEST['IID'].'">'.
			'<input class="btn" type="submit" name="delete" value="Yes, Delete">'.
			' &nbsp; &nbsp; '.
			'<input class="btn" type="submit" name="nodelete" value="No, Don\'t Delete">'.
		 '</form>';
	}else{
		header('Location:story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID']);
	}

	include 'include/footer.inc.php';
?>