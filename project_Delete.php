<?php
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
	if ($_REQUEST['delete'])
	{
			// delete points object, comments object, stories,iteration story-status
			mysqli_query($DBConn, 'DELETE FROM points_log WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM task WHERE Object_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM comment WHERE Object_ID = '.($_REQUEST['PID']+ 0));

			$asql= "select upload.Name, upload.Desc, HEX(Name) as HName, upload.Type, upload.AID FROM upload left Join story s on upload.AID = s.AID where s.Project_ID=".($_REQUEST['PID']+ 0);
			$aqry=mysqli_query($DBConn, $asql);
			while ($aresult = mysqli_fetch_array($aqry)) 
			{
				if(!mysqli_error($DBConn))
				{
					if (unlink('upload/'.$aresult['HName'].'.'.$aresult['Type']))
					{
						auditit($_REQUEST['PID'],$aresult['Type'],$_SESSION['Email'],'Deleted uploaded file ',$aresult[HName],$aresult[Desc]);
					}
				}
			}

			mysqli_query($DBConn, 'DELETE FROM from upload u left Join story s  on u.AID = s.AID where s.Project_ID ='.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM story WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM story_status WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM story_type WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM iteration WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM tags WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM user_project WHERE Project_ID = '.($_REQUEST['PID']+ 0));
			mysqli_query($DBConn, 'DELETE FROM audit WHERE PID = '.($_REQUEST['PID']+ 0));
		if (mysqli_query($DBConn, 'DELETE FROM project WHERE ID = '.$_REQUEST['PID']))
		{

			$showForm = false;
			$deleted = true;
			header('Location:project_List.php');
		}
	} 
	else if ($_REQUEST['nodelete'])
	{
		auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Deleted Project ',Get_Project_Name($_REQUEST['PID']));
		$showForm = false;
		$deleted = false;
	}



	if ($showForm)
	{
		echo	'<br>Are you sure you want to delete:<br><ul>'.
			'<li>points</li>'.
			'<li>comments</li>'.
			'<li>object</li>'.
			'<li> stories</li>'.
			'<li>iteration</li>'.
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
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	} 
	else
	{
		header('Location:project_List.php');
	}

	include 'include/footer.inc.php';

?>