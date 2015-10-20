<?php

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){

		exit();
	}
	Global $statuscolour;
	Global $Project;
	Global $Sizecount;
	Global $OSizecount;
	Global $Toggle;
	Global $Iterationcount;
	Global $OIterationcount;
	Global $DBConn;
	Global $LockedIteration;


	$dummy = Get_Project_Name($_GET['PID']);
	$dummy = buildstatuspop($_GET['PID']);

	$sql = 'SELECT * FROM story where story.Project_ID='.$_GET['PID'].' and story.Iteration_ID='.$_GET['IID'].' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
	$story_Res = mysqli_query($DBConn, $sql);
	echo '<ul id="sortable-'.$_GET['LorR'].'" class="connectedSortable mh15">';
	if ($story_Row = mysqli_fetch_assoc($story_Res))
	{
		do
		{
			echo	'<li class="storybox" id=story_'.$story_Row['AID'].'>';
			PrintStory ($story_Row);
			echo	'</li>';
		}
		while ($story_Row = mysqli_fetch_assoc($story_Res));
	}
	echo '</ul>';
?>
