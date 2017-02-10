<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

?>
<script src="jquery/jquery.js"></script>
	<link rel="stylesheet" type="text/css" href="css/story_Preview.css" />
	<script type="text/javascript" src="scripts/story_Preview-hash14788f305b7f6142fe83667910ab8ac0.js"></script>

<?php

function ViewTasks($thisproject, $ThisStory){
	Global $DBConn;

	$task_sql = 'SELECT * FROM task where task.Story_AID='.$ThisStory.' order by task.Rank';
	$task_Res =  $DBConn->directsql($task_sql);
	foreach($task_Res as $task_Row){
		echo '<div class="taskRow">'.
				'<div class="taskCell"><input class="done" id="done_'.$task_Row['ID'].'" '.( $task_Row['Done'] == 1 ? 'checked' : '').' value="1" disabled="disabled" type="checkbox" name="Done"></div>'.
				'<div class="taskCell"><input size="80" id="desc_'.$task_Row['ID'].'" type="text" disabled="disabled" value="'.$task_Row['Desc'].'"/></div>'.
				'<div class="taskCell">'.Show_Project_Users($thisproject, $task_Row['User_ID'],"user_".$task_Row['ID'],1).'</div>'.
				'<div class="taskCell"><input id="expected_'.$task_Row['ID'].'" type="text" disabled="disabled" size="2" value="'.$task_Row['Expected_Hours'].'"/></div>'.
				'<div class="taskCell"><input id="actual_'.$task_Row['ID'].'" type="text" disabled="disabled" size="2" value="'.$task_Row['Actual_Hours'].'"/></div>'.
			'</div>';
	}
}

function CommentsBlock($ThisStory){
	Global $DBConn;

	echo '<div class="commentsdialog" id="commentspop_'.$ThisStory.'"><ul id=commentlist_'.$ThisStory.'> ';
	$q = "SELECT * FROM comment WHERE Story_AID = ".$ThisStory." and Parent_ID=0 order by ID";
	$r =  $DBConn->directsql($q);
	foreach ($r as $row){
		PreviewGetComments($row);
	}
	echo '</ul>';
	echo '</div>  ';
}

function PreviewGetComments($row){
	Global $DBConn;
	Global $Project;

	echo '<li class="comment" id="comment_'.$row['ID'].'">';
 	echo '<div class="comment-body" id="comment_body_'.$row['ID'].'">'.$row['Comment_Text'].'</div>';
	echo "<div class='aut'>By: ".$row['User_Name'].' @ '. $row['Comment_Date']."</div>";

	/* The following sql checks whether there's any reply for the comment */
	$q = "SELECT * FROM comment WHERE Parent_ID = ".$row['ID'];
	$r = $DBConn->directsql($q);
	if (count($r) > 0){
		echo '<ul id="commentreply_'.$row['ID'].'">';
		foreach ($r as $row){
			PreviewGetComments($row);
		}
		echo "</ul>";
	}
	echo "</li>";
}

	$Row=fetchusingID('*',$_REQUEST['id'],'story');
	Get_Project_Name($Row['Project_ID']);
	echo 	'<div id="container">';
			echo '<div class="left">'.
				$Row['Type'].': #'.$Row['ID'];

				$istring=Get_Iteration_Name($Row['Iteration_ID'],False);
				if ($istring!='Backlog') {
					echo ' - '.$istring;
				}
				if ($Row['Owner_ID']!=0) {
					echo ' ('.Get_User($Row['Owner_ID'],0).')';
				}
			echo '</div>'.
			'<div class="right">'.
				$Row['Size'].' pts.'.
			'</div>'.
			'<div title="Click here to toggle expanded view" id="summary">'.
				' '.$Row['Summary'].' '.
			'</div>'.
			'<div id="detail">';
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
				ViewTasks($Row['Project_ID'], $Row['AID']);
				CommentsBlock($Row['AID']);
			echo '</div>';
			echo '<div class="left">'.
				$Row['Tags'].
			'</div>'.
			'<div class="right">';
			if($Row['Parent_Story_ID'] != 0) {
				$parentssql='SELECT @id :=(SELECT Parent_Story_ID FROM story WHERE AID = @id and Parent_Story_ID <> 0 ) AS parent FROM (SELECT @id :='.$Row['AID'].') vars STRAIGHT_JOIN story  WHERE @id is not NULL';
				$parents_Res = $DBConn->directsql($parentssql);
				foreach ($parents_Res as $parents_Row){
					if($parents_row['parent']!=NULL){
						$parentsql='select ID, AID, Summary, Size from story where AID='.$parents_row['parent'].' and AID<>0';
						$parent_row = $DBConn->directsql( $parentsql);
						if (count($parent_row) > 0){
							echo ' #'.$parent_row[0]['ID'].' ('.$parent_row[0]['Size'].' pts)</a>&nbsp;&nbsp;';
						}
					}
				}
			}
			echo '</div>';
		echo '</div>';
?>