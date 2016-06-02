<?php

// List and manage tasks for a Story

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function GetTasks($ThisProject, $ThisStory)
{
	Global $DBConn;

// The task list must be wrapped inside a div in the host document as follows.
//	echo '<div class="taskdialog" id="alltasks_'.$ThisStory.'">';

	echo	'<ul id="sortabletask'.$ThisStory.'">';

	$task_sql = 'SELECT * FROM task where Story_AID='.$ThisStory.' order by `Rank`, `ID`';
	$task_Res =$DBConn->directsql($task_sql);
#	if (count($task_Res)>0)	{
		foreach($task_Res as $task_Row)	{
			echo	'<li class="divRow" id=task_'.$task_Row['ID'].'>'.
					'<div class="divCell1 edittask" id="edittask_'.$task_Row['ID'].'">'.
						'<img src="images/edit-small.png">'.
					'</div>'.
					'<div class="divCell1 savetask" id="savetask_'.$task_Row['ID'].'">'.
						'<img src="images/tick-small.png">'.
					'</div>'.
					'<div class="divCell1"><input class="done indet'.$task_Row['Done'].'" id="done_'.$task_Row['ID'].'" '.( $task_Row['Done'] == 2 ? 'checked' : '').' value="'.$task_Row['Done'].'" type="checkbox" name="Done"></div>'.
						'<div class="divCell1"><input size="80" id="desc_'.$task_Row['ID'].
						'" type="text" disabled="disabled" value="'.$task_Row['Desc'].'"/></div>'.
						'<div class="divCell1">'.Show_Project_Users($ThisProject, $task_Row['User_ID'],"user_".$task_Row['ID'],1).'</div>'.
						'<div class="divCell1"><input id="expected_'.$task_Row['ID'].'" type="text" disabled="disabled" size="2" value="'.$task_Row['Expected_Hours'].'"/></div>'.
						'<div class="divCell1"><input id="actual_'.$task_Row['ID'].'" type="text" disabled="disabled" size="2" value="'.$task_Row['Actual_Hours'].'"/></div>'.
					'<div class="divCell1 deletetask"><img src="images/delete-small.png"></div>'.
			'</li>';
		}
#	}

	echo '</ul>';
		echo
			'<div class="micromenudiv-input" id="newrow_'.$ThisStory.'">'.
				'<img class="savenew" src="images/add-small.png"> '.
				'<input id="ndesc_'.$ThisStory.'" title="Task Description" name = "Desc" value="" size="80">'.
				Show_Project_Users($ThisProject,"0","taskuser_".$ThisStory).
				' <input id="nexph_'.$ThisStory.'"  title="Expected hours" name = "Expected_Hours" value="" size="2">'.
				'<input id="nacth_'.$ThisStory.'"  title="Actual Hours" name = "Actual_Hours" value="" size="2">'.
				' <input type="hidden" id="pid_'.$ThisStory.'" name = "pid" value="'.$_REQUEST['PID'].'"/>';
		echo	'</div>';

//	echo '</div>';
}

GetTasks($_GET['pid'],$_GET['aid']);

?>