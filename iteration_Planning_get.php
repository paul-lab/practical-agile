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
	$sumpts=0;

	$sql = 'SELECT * FROM story where story.Project_ID= ? and story.Iteration_ID= ? and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
	$story_Res = $DBConn->directsql($sql, array($_GET['PID'], $_GET['IID']));
	echo '<ul id="sortable-'.$_GET['LorR'].'" class="connectedSortable mh15">';
	if($story_Res){
		foreach ($story_Res as $story_Row)	{
			echo	'<li class="storybox" id=story_'.$story_Row['AID'].'>';
			PrintStory ($story_Row);
			echo	'</li>';
			$sumpts+=$story_Row['Size'];
		}
	}
	echo '</ul>';
	echo '{6B89778E-1B36-4E75-A7F2-301656217750}'.$sumpts;
?>
