<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

		// add it
		$ssql = 'INSERT INTO story (Project_ID, Release_ID, Iteration_ID, Parent_Story_ID, Created_Date'.
		', Status, Epic_Rank, Iteration_Rank, Size, Blocked'.
		', Summary, Col_1, As_A, Col_2, Acceptance, Tags, Type, Created_By_ID, ID)'.
		' SELECT Project_ID, Release_ID, Iteration_ID, Parent_Story_ID, Created_Date'.
		', Status, Epic_Rank, Iteration_Rank, Size, Blocked'.
		', Summary, Col_1, As_A, Col_2, Acceptance, Tags, Type, '.
		'"'.$_SESSION['ID'].'", (SELECT max(local.ID)+1 from story as local where Project_ID=story.Project_ID)'.
		' FROM story WHERE story.AID='.$_GET['SAID'];
		// Add the record
		$result=$DBConn->directsql($ssql);
		// Fecth new local ID
		$sql = 'select ID, AID, Project_ID, Iteration_ID, Summary from story where AID='.$result;
		$RecRow=$DBConn->directsql($sql);
		$RecRow=$RecRow[0];
		$story_Row['ID'] = $RecRow['ID'];

		$ssql='UPDATE story set `Summary` ="'.'(Dup of #'.$RecRow['ID'].') - '.$RecRow['Summary'].'" where AID='.$RecRow['AID'];
		$DBConn->directsql($ssql);
		Update_Iteration_Points($RecRow['Iteration_ID']);


	echo 'New story <a title="Edit story #'.$story_Row['ID'].'" href="story_Edit.php?AID='.$RecRow['AID'].'&PID='.$RecRow['Project_ID'].'&IID='.$RecRow['Iteration_ID'].'">#'.$story_Row['ID'].'</a> Duplicated from this story';
// and now dupcate the tasks
	if ($_GET['TASKS']=='True')	{
		$sql = 'INSERT INTO task (Story_AID, User_ID, Rank, task.Desc, Done, Expected_Hours, Actual_Hours, Task_Date)'.
		' SELECT '.$RecRow['AID'].', 0, Rank, `Desc`, 0, Expected_Hours, 0, Task_Date'.
		' FROM task WHERE task.Story_AID='.$_GET['SAID'];
		$DBConn->directsql($sql);
	}

	auditit($RecRow['Project_ID'],$RecRow['AID'],$_SESSION['Email'],'Duplicated Story from',$_GET['SAID'], $_GET['SAID'].'-'.$RecRow['Summary'],'Story #'.$RecRow['ID']);
	auditit($RecRow['Project_ID'],$_GET['SAID'],$_SESSION['Email'],'Duplicated Story to',$_GET['SAID'].'-'.$RecRow['Summary'],'Story #'.$RecRow['ID']);
?>