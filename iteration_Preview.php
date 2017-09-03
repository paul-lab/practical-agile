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


?>
<script src="jquery/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="css/story_Preview.css" />
<?php

	Get_Project_Name($_REQUEST['PID']);

	$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID='.$_REQUEST['IID'].' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
	$Res =  $DBConn->directsql($sql);
	$Toggle=0;
	foreach ($Res as $Row){
		$Toggle = ($Toggle + 1) % 2;
		if ($Toggle==1)	{
			echo 	'<div id="container">';
		}else{
			echo 	'<div id="containerr">';
		}
//############################
		echo '<div class="left">'.
			'<a title="Edit Story" href="story_Edit.php?AID='.$Row['AID'].'&PID='.$Row['Project_ID'].'&IID='.$Row['Iteration_ID'].'">'.
			$Row['Type'].': #'.$Row['ID'].'</A>';
			$istring=Get_Iteration_Name($Row['Iteration_ID'],False);
			if ($istring!='Backlog') {
				echo ' - '.$istring;
			}
			if ($Row['Owner_ID']!=0) {
				echo ' ('.Get_User($Row['Owner_ID'],0).')';
			}
		echo '</div>';
		echo '<div class="right">'.
			$Row['Size'].' pts.'.
		'</div>';
			echo '<div id="summary">'.
			' '.$Row['Summary'].' '.
		'</div>';
		echo 	'<div id="detail">';
		if(strlen($Row['As_A']) > 0 ){
			echo '<b>As a:</b> ' .html_entity_decode($Row['As_A'],ENT_QUOTES).'<br>';
		}
		echo '<b>'.$Project['Desc_1'].'</b>'.html_entity_decode($Row['Col_1'],ENT_QUOTES).'<br>';
		if(strlen($Row['Col_2']) > 0 ){
			echo '<b>'.$Project['Desc_2'].'</b> '.html_entity_decode($Row['Col_2'],ENT_QUOTES).'<br>';
			}
		if(strlen($Row['Acceptance']) > 0 ){
			echo '<b>Acceptance Criteria:</b> '.html_entity_decode($Row['Acceptance'],ENT_QUOTES).'<br>';
		}
		echo '</div>';
		echo '<div id="extra">';
		echo '</div>';
		echo '<div class="left">'.$Row['Tags'];
		echo '</div>';

		echo '<div class="right">';
		if($Row['Parent_Story_ID'] != 0) {
// a list of parents
		if (dbdriver=='mysql'){
			$parentssql='SELECT @r AS _aid, ( SELECT @r := Parent_Story_ID FROM story WHERE AID = _aid ) AS parent FROM (SELECT  @r := '.$Row['AID'].') vars, story h WHERE @r <> 0';
		}else{
			$parentssql='WITH RECURSIVE  xid(aid,level) AS ( VALUES('.$Row['AID'].',0) UNION ALL SELECT story.parent_story_id, xid.level+1 FROM story JOIN xid ON story.aid=xid.aid where parent_story_id <>0 )SELECT aid as parent FROM xid where level <> 0 order by level desc ';
		}

			$parents_Res = $DBConn->directsql($parentssql);
			foreach ($parents_Res as $parents_row){
				if($parents_row['parent']!=NULL){
					$parentsql='select ID, AID, Summary, Size from story where AID='.$parents_row['parent'].' and AID<>0';
					$parent_Row =  $DBConn->directsql($parentsql);
					if (count($parent_Row) ==1){
						echo ' #'.$parent_Row[0]['ID'].' ('.$parent_Row[0]['Size'].' pts)</a>&nbsp;&nbsp;';
					}
				}
			}
		}
		echo '</div>';
//############################
		echo '</div>';	// Container
	}
?>