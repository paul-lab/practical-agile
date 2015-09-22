<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');


?>
<script src="jquery/jquery.js"></script>    
	<link rel="stylesheet" type="text/css" href="css/story_Preview.css" />
	<script type="text/javascript" src="scripts/story_Preview-hashc402c838d9a3998076e9298456831c2c.js"></script>


<?php



function ViewTasks($thisproject, $ThisStory)
{
	Global $DBConn;

	$task_sql = 'SELECT * FROM task where task.Story_AID='.$ThisStory.' order by task.Rank';

	$task_Res = mysqli_query($DBConn, $task_sql);
	if ($task_Row = mysqli_fetch_array($task_Res))
	{
		do
		{
			echo	'<div class="taskRow">'.
					'<div class="taskCell"><input class="done" id="done_'.$task_Row['ID'].'" '.( $task_Row['Done'] == 1 ? 'checked' : '').' value="1" disabled="disabled" type="checkbox" name="Done"></div>'.
						'<div class="taskCell"><input size="80" id="desc_'.$task_Row['ID'].'" type="text" disabled="disabled" value="'.$task_Row['Desc'].'"/></div>'.
						'<div class="taskCell">'.Show_Project_Users($thisproject, $task_Row['User_ID'],"user_".$task_Row['ID'],1).'</div>'.
						'<div class="taskCell"><input id="expected_'.$task_Row['ID'].'" type="text" disabled="disabled" size="2" value="'.$task_Row['Expected_Hours'].'"/></div>'.
						'<div class="taskCell"><input id="actual_'.$task_Row['ID'].'" type="text" disabled="disabled" size="2" value="'.$task_Row['Actual_Hours'].'"/></div>'.
				
				'</div>';
		}
		while ($task_Row = mysqli_fetch_array($task_Res));
	}

}


function CommentsBlock($ThisStory)
{
	Global $DBConn;

	echo '<div class="commentsdialog" id="commentspop_'.$ThisStory.'"><ul id=commentlist_'.$ThisStory.'> ';

	$q = "SELECT * FROM comment WHERE Story_AID = ".$ThisStory." and Parent_ID=0 order by ID";
	$r = mysqli_query($DBConn, $q);   
	while($row = mysqli_fetch_assoc($r))  :
		PreviewGetComments($row);   
	endwhile;   

	echo '</ul>';
	echo '</div>  ';
}

function PreviewGetComments($row)
{
	Global $DBConn;
	Global $Project;

	echo '<li class="comment" id="comment_'.$row['ID'].'">';
 	echo '<div class="comment-body" id="comment_body_'.$row['ID'].'">'.$row['Comment_Text'].'</div>';
	echo "<div class='aut'>By: ".$row['User_Name'].' @ '. $row['Comment_Date']."</div>";
	
	/* The following sql checks whether there's any reply for the comment */
	$q = "SELECT * FROM comment WHERE Parent_ID = ".$row['ID'];   
	$r = mysqli_query($DBConn, $q);
	if(mysqli_num_rows($r)>0) // there is at least reply
	{
		echo '<ul id="commentreply_'.$row['ID'].'">';
		while($row = mysqli_fetch_assoc($r)) {
			PreviewGetComments($row);
		}
		echo "</ul>";
	}
	echo "</li>";
}

	$Res=mysqli_query($DBConn, 'SELECT * FROM story WHERE AID='.$_REQUEST['id']);
	$Row=mysqli_fetch_assoc($Res);
	Get_Project_Name($Row['Project_ID']);
	echo 	'<div id="container">';


			echo '<div class="left">'.
				' Story: #'.$Row['ID'];
		
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
				$parents_Res = mysqli_query($DBConn, $parentssql);
				if ($parents_row = mysqli_fetch_assoc($parents_Res))
				{
					do
					{
				  		if($parents_row['parent']!=NULL)
						{
							$parentsql='select ID, AID, Summary, Size from story where AID='.$parents_row['parent'].' and AID<>0';
							$parent_Res = mysqli_query($DBConn, $parentsql);
							if ($parent_row = mysqli_fetch_assoc($parent_Res))
							{
								echo ' #'.$parent_row ['ID'].' ('.$parent_row ['Size'].' pts)</a>&nbsp;&nbsp;';
							}
						}
					}
					while ($parents_row = mysqli_fetch_assoc($parents_Res));
				}
			}
			echo '</div>';


		echo '</div>';
?>

