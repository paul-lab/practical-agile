<?php
	include 'include/header.inc.php';


	if (empty($_REQUEST['PID'])) header("Location:project_List.php");

	echo '<a href="project_List.php">My Projects</a>->';
	echo '<a href="project_Edit.php?PID='.$_REQUEST['PID'].'">';
	echo Get_Project_Name($_REQUEST['PID']);
	echo '</a>';


	echo
		'<div align="center">'.
			'<a href="storyType_Edit.php?PID='.$_REQUEST['PID'].'">add a new storyType</a>'.
		'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr>'.
				'<td>&nbsp;</td>'.
				'<td>Desc.</td>'.
				'<td>Order</td>'.
				'<td>&nbsp;</td>'.
			'</tr>';
	$sql = 'SELECT *, (select count(*) from story where story.Project_ID='.$_REQUEST['PID'].' and story.Type = story_type.Desc) as Used FROM story_type where story_type.Project_ID='.$_REQUEST['PID'].' ORDER by story_type.`Order`';
	$storyType_Res = $DBConn->directsql($sql);

	if (count($storyType_Res)> 0)	{
		foreach($storyType_Res AS $storyType_Row)	{
			echo
				'<tr>'.
					'<td>'.
						'<a href="storyType_Edit.php?id='.$storyType_Row['ID'].'&PID='.$_REQUEST['PID'].'"><img src="images/edit.png"></a> &nbsp;'.
					'</td>'.
					'<td>'.$storyType_Row['Desc'].'</td>'.
					'<td>'.$storyType_Row['Order'].'</td>'.
					'<td>';
					if ($storyType_Row['Used'] == 0){
						echo '<a href="storyType_Delete.php?id='.$storyType_Row['ID'].'&PID='.$_REQUEST['PID'].'"><img src="images/delete.png"></a>';
					}else{
						echo '&nbsp;';
					}
			echo		'</td>'.
				'</tr>';
		}
	}
	echo '</table>';

	include 'include/footer.inc.php';

?>
