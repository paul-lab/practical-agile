<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread">My Projects';
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
	echo
		'<table align="center"  cellpadding="6" cellspacing="0" border = 0>'.
			'<tr><b>'.
				'<td><b>Category</td>'.
				'<td><b>Project Name</td>'.
				'<td><b>Velocity</td>'.
				'<td><b>Current<br>Iteration</td>'.
				'<td><b>Size</td>'.
				'<td><b>Backlog</td>'.
				'<td><b>Archived</td>'.
				'<td>&nbsp;</td>'.
			'</b></tr>';
	$sql = 'SELECT distinct ID, Category, Name, Velocity, Backlog_ID, Points_Object_ID, Archived FROM project LEFT JOIN user_project ON project.ID = user_project.Project_ID ';
	if ($Usr['Admin_User'] != 1 && $isProjectAdmin==0){
		$sql .=' where user_project.User_ID='.$_SESSION['ID'].' and project.Archived<>1 ';
	}
	$sql.=' order by Category, Name';
	$project_Res=$DBConn->directsql($sql);
	$Toggle=0;

	// if only have access to a single project, then go to that project.
	if ( count($project_Res)==1) header("Location:project_Summary.php?PID=".$project_Row[0]['ID']);

	foreach ($project_Res as $project_Row){
		$Project['ID'] = $project_Row['ID'];
		$Toggle = ($Toggle + 1) % 2;
		echo
			'<tr valign="top" class="alternate'.$Toggle.'">'.
			'<td>'.$project_Row['Category'].'</td>'.
			'<td><a href="project_Summary.php?PID='.$project_Row['ID'].'">'.$project_Row['Name'].'</a></td>'.
			'<td>'.$project_Row['Velocity'].'</td>'.
			'<td>';
		$thisdate =  Date("Y-m-d");
		$sql = 'SELECT distinct ID, Name FROM iteration where iteration.Project_ID='.$project_Row['ID'].' and iteration.Name <> "Backlog" and iteration.Start_Date<="'.$thisdate.'" and iteration.End_Date>="'.$thisdate.'"';
		$iteration_Row =$DBConn->directsql($sql);
		echo '<a href="story_List.php?PID='.$project_Row['ID'].'&IID='.$iteration_Row[0]['ID'].'" title = "Current Iteration" >'.
			substr($iteration_Row['Name'], 0, 14).'</a>';
		echo '</td>';
		echo '<td>';
		print_summary($project_Row['Points_Object_ID']);
		echo '</td>';
		echo '<td><a href="story_List.php?PID='.$project_Row['ID'].'&IID='.$project_Row['Backlog_ID'].'">Backlog</a></td><td>';
		if ($project_Row['Archived']==1){
			echo 'Yes';
		}else{
			echo 'No';
		}
		echo '</td><td>';
		if ($Usr['Admin_User']==1 && $project_Row['ID']<>1){
			echo '<a href="project_Delete.php?PID='.$project_Row['ID'].'"><img src="images/delete.png" title="Delete"></a>';
		}
		echo	'</td>';
		echo '</tr>';
	}

	echo '</table>';

	include 'include/footer.inc.php';

?>