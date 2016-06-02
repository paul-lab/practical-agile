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

	$user_Row = $DBConn->directsql($sql );
	$Toggle=0;
	if (count($user_Row) > 0)	{
		$rowcnt=0;
		do	{
			$Toggle = ($Toggle + 1) % 2;
			echo
				'<tr valign="top" class="alternate'.$Toggle.'"><td>';
					if (empty($_REQUEST['PID'])) {
						echo '<a href="user_Edit.php?id='.$user_Row[$rowcnt]['ID'].'"><img src="images/edit.png"></a> &nbsp; ';
					}else{
					echo '&nbsp;';
					}
					echo '</td><td>';
					if ($user_Row[$rowcnt]['Disabled_User']==1){
						echo 'Yes';
					}else{
						echo '&nbsp;';
					}
					echo '</td><td>';
					if ($user_Row[$rowcnt]['Admin_User']==1){
						echo 'Yes';
					}else{
						echo '&nbsp;';
					}
			echo 		'</td>'.
					'<td>'.$user_Row[$rowcnt]['EMail'].'</td>'.
					'<td><b>'.$user_Row[$rowcnt]['Initials'].'</b></td>'.
					'<td>'.$user_Row[$rowcnt]['Friendly_Name'].'</td><td>';

					// get current user projects
					$sqlp = 'SELECT Name, ID,user_project.project_Admin as padmin, user_project.Readonly as preadonly  FROM project LEFT JOIN user_project ON project.ID = user_project.Project_ID where user_project.User_ID='.$user_Row[$rowcnt]['ID'];
					$proj_Row =  $DBConn->directsql($sqlp);
					if (count($proj_Row) > 0){
						$pcnt=0;
						do	{
							echo '-'.$proj_Row[$pcnt]['Name'];
							if($proj_Row[$pcnt]['padmin']==1) {echo ' : <b>[Admin]</b>';}
							if($proj_Row[$pcnt]['preadonly']!=0) {echo ' : <b>[Read-Only]</b>';}
							echo '<br>';
							$pcnt+=1;
						} while ($pcnt < count($proj_Row));
					}
				echo	'<td>';
					if ($user_Row[$rowcnt]['ID']<>1){
						if (empty($_REQUEST['PID'])) {
// only delete people that are not story or task owners
							$sql='select ((select count(ID) from story where story.Owner_ID ='.$user_Row[$rowcnt]['ID'].')+ (select count(ID) from task where task.User_ID ='.$user_Row[$rowcnt]['ID'].'))as counted';
							$row = $DBConn->directsql($sqlp);
							if ($row[0]['counted']==0)	{
								echo '<a href="user_Delete.php?id='.$user_Row[$rowcnt]['ID'].'&desc='.$user_Row[$rowcnt]['EMail'].'-'.$user_Row[$rowcnt]['Friendly_Name'].'"><img src="images/delete.png"></a>';
							}
						}else{
							echo '&nbsp;';
						}
					}else{
						echo '&nbsp;';
					}
				echo	'</td>'.
				'</tr>';
				$rowcnt +=1;
		}
		while ($rowcnt < count($user_Row));
	}
	echo '</table>';
	include 'include/footer.inc.php';
?>