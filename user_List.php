<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo 'User List';
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
		'<div align="center">';
// have a projectId so probably came from the search.
		if (empty($_REQUEST['PID'])) {
			echo '<a href="user_Edit.php">add a new user</a>';
		}else{
			echo '&nbsp;';
		}
	echo 	'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr><b>'.
				'<td>&nbsp</td>'.
				'<td>Disabled</td>'.
				'<td>Global<br>Admin</td>'.
				'<td>EMail</td>'.
				'<td>Initials</td>'.
				'<td>Friendly_Name</td>'.
				'<td>Project/s</td>'.
				'<td>&nbsp;</td>'.
			'</b></tr>';
	$sql = 'SELECT * FROM user';
	if ($_REQUEST['PID']+0>0) 
	{
		$sql = 'SELECT user.ID, user.Initials, user.Friendly_Name, user.EMail, user.Admin_User FROM user LEFT JOIN user_project ON user.ID = user_project.User_ID where user_project.Project_ID='.$_REQUEST['PID'];
	}

	$user_Res = mysqli_query($DBConn, $sql);
	$Toggle=0;
	if ($user_Row = mysqli_fetch_assoc($user_Res))
	{
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo
				'<tr valign="top" class="alternate'.$Toggle.'"><td>';
					if (empty($_REQUEST['PID'])) {
						echo '<a href="user_Edit.php?id='.$user_Row['ID'].'"><img src="images/edit.png"></a> &nbsp; ';
					}else{
					echo '&nbsp;';
					}
					echo '</td><td>';
					if ($user_Row['Disabled_User']==1){
						echo 'Yes';
					}else{
						echo '&nbsp;';
					}
					echo '</td><td>';
					if ($user_Row['Admin_User']==1){
						echo 'Yes';
					}else{
						echo '&nbsp;';
					}
			echo 		'</td>'.
					'<td>'.$user_Row['EMail'].'</td>'.
					'<td><b>'.$user_Row['Initials'].'</b></td>'.
					'<td>'.$user_Row['Friendly_Name'].'</td><td>';
					
					// get current user projects
					$sqlp = 'SELECT Name, ID,user_project.project_Admin as padmin, user_project.Readonly as preadonly  FROM project LEFT JOIN user_project ON project.ID = user_project.Project_ID where user_project.User_ID='.$user_Row['ID'];
					$proj_Res = mysqli_query($DBConn, $sqlp);
					if ($proj_Row = mysqli_fetch_assoc($proj_Res))
					{ 
						do
						{
							echo '-'.$proj_Row['Name'];
							if($proj_Row['padmin']==1) {echo ' : <b>[Admin]</b>';}
							if($proj_Row['preadonly']!=0) {echo ' : <b>[Read-Only]</b>';}
							echo '<br>';
						} while ($proj_Row = mysqli_fetch_assoc($proj_Res));
					}
				echo	'<td>';
					if ($user_Row['ID']<>1){
						if (empty($_REQUEST['PID'])) {
// only delete people that are not story or task owners
							$sql='select ((select count(ID) from story where story.Owner_ID ='.$user_Row['ID'].')+ (select count(ID) from task where task.User_ID ='.$user_Row['ID'].'))as counted';
							$res=mysqli_query($DBConn, $sql);
							$row=mysqli_fetch_assoc($res);
							if ($row['counted']==0)
							{
								echo '<a href="user_Delete.php?id='.$user_Row['ID'].'&desc='.$user_Row['EMail'].'-'.$user_Row['Friendly_Name'].'"><img src="images/delete.png"></a>';
							}
						}else{
							echo '&nbsp;';
						}
					}else{
						echo '&nbsp;';
					}
				echo	'</td>'.
				'</tr>';
		}
		while ($user_Row = mysqli_fetch_assoc($user_Res));
	}
	echo '</table>';
	include 'include/footer.inc.php';
?>