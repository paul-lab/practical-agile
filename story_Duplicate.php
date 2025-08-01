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
		'?, (SELECT max(local.ID)+1 from story as local where Project_ID=story.Project_ID)'.
		' FROM story WHERE story.AID= ?';
		// Add the record
		$result=$DBConn->directsql($ssql, array($_SESSION['ID'], $_GET['SAID']));
		// Fetch new local ID
		$sql = 'select ID, AID, Project_ID, Iteration_ID, Summary from story where AID= ?';
		$RecRow=$DBConn->directsql($sql, $result);
		$RecRow=$RecRow[0];
		$story_Row['ID'] = $RecRow['ID'];

		$ssql='UPDATE story set `Summary` = ? where AID= ?';
		$DBConn->directsql($ssql, array("(Duplicate) - ".$RecRow['Summary'], $result));
		Update_Iteration_Points($RecRow['Iteration_ID']);


	echo 'New story <a title="Edit story #'.$story_Row['ID'].'" href="story_Edit.php?AID='.$RecRow['AID'].'&PID='.$RecRow['Project_ID'].'&IID='.$RecRow['Iteration_ID'].'">#'.$story_Row['ID'].'</a> Duplicated from this story';
// and now dupcate the tasks
	if ($_GET['TASKS']=='True')	{
		$sql = 'INSERT INTO task (Story_AID, User_ID, Rank, task.Desc, Done, Expected_Hours, Actual_Hours, Task_Date)'.
		' SELECT ?, 0, Rank, `Desc`, 0, Expected_Hours, 0, Task_Date'.
		' FROM task WHERE task.Story_AID= ?';
		$DBConn->directsql($sql, array($RecRow['AID'], $_GET['SAID']));
	}

	auditit($RecRow['Project_ID'],$RecRow['AID'],$_SESSION['Email'],'Duplicated Story from',$_GET['SAID'], $_GET['SAID'].'-'.$RecRow['Summary'],'Story #'.$RecRow['ID']);
	auditit($RecRow['Project_ID'],$_GET['SAID'],$_SESSION['Email'],'Duplicated Story to',$_GET['SAID'].'-'.$RecRow['Summary'],'Story #'.$RecRow['ID']);
?>