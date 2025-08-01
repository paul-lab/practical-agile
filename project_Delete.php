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

	if (empty($_REQUEST['PID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo Get_Project_Name($_REQUEST['PID']);
echo '</div>';
?>
<script>
$(function() {
	document.title = 'Practical Agile: '+$("#phpbread").text().substring(13);
	$("#breadcrumbs").html($("#phpbread").html());
	if ($("#phpnavicons")){
		$("#navicons").html($("#phpnavicons").html());
	}
});
</script>


<?php

	$showForm = true;
	if ($_REQUEST['delete']){
			// delete points object, tasks,comments object, stories,Sprints story-status
			$DBConn->directsql('DELETE FROM points_log WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM comment WHERE Comment_Object_ID = ?', $_REQUEST['PID']);
			$asql= "select upload.Name, upload.Desc, HEX(Name) as HName, upload.Type, upload.AID FROM upload left Join story s on upload.AID = s.AID where s.Project_ID= ?";
			$aqry=$DBConn->directsql($asql, $_REQUEST['PID']);
			// remove uploaded files
			foreach($aqry as $aresult)	{
				if (unlink('upload/'.$aresult['HName'].'.'.$aresult['Type'])){
						auditit($_REQUEST['PID'],$aresult['Type'],$_SESSION['Email'],'Deleted uploaded file ',$aresult['HName'],$aresult['Desc']);
				}
			}
			// remove upload entry
			$asql='DELETE FROM upload where AID = (select AID from story WHERE Project_ID = ?)';
			$aqry=$DBConn->directsql($asql, $_REQUEST['PID']);
			$result=$DBConn->directsql('SELECT AID from story where Project_ID = ?', $_REQUEST['PID']);
			foreach ($Res as $Result){
				$DBConn->directsql('DELETE FROM task WHERE Story_AID= ?', $Result['AID']);
			}
			$DBConn->directsql('DELETE FROM story WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM story_status WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM story_type WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM iteration WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM tags WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM user_project WHERE Project_ID = ?', $_REQUEST['PID']);
			$DBConn->directsql('DELETE FROM audit WHERE PID = ?', $_REQUEST['PID']);
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Deleted Project ',Get_Project_Name($_REQUEST['PID']));
			$cnt= $DBConn->directsql('DELETE FROM project WHERE ID = ?', $_REQUEST['PID']);
		if ($cnt > 0){
			$showForm = false;
			$deleted = true;
			header('Location:project_List.php');
		}
	} else if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm){
		echo	'<br>Are you sure you want to delete:<br><ul>'.
			'<li>points</li>'.
			'<li>comments</li>'.
			'<li>object</li>'.
			'<li> stories</li>'.
			'<li>Sprints</li>'.
			'<li>story-status</li>'.
			'<li>story type and</li>'.
			'<li>tags</li>'.
			'<li>audit logs</li>'.
			'<li>Uploaded files</li>'.
			'</ul> as well as this project?<p>';

		echo '<br>Users are <b>not</b> deleted<br>';
		echo '<form method="post" action="?">'.
					'Are you sure you want to delete this Project?<br />'.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input class="btn" type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input class="btn" type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	} else{
		header('Location:project_List.php');
	}
	include 'include/footer.inc.php';
?>