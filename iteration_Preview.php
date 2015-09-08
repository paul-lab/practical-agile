<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

?>
<script src="jquery/jquery.js"></script>    
	<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
	<link rel="stylesheet" type="text/css" href="css/story_Preview.css" />
<?php

	$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID='.$_REQUEST['IID'].' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';

	Get_Project_Name($_REQUEST['PID']);

	$Res = mysqli_query($DBConn, $sql);
	$Toggle=0;
	if ($Row = mysqli_fetch_assoc($Res))
	{
		do
		{
			$Toggle = ($Toggle + 1) % 2;	
			if ($Toggle==1)
			{
				echo 	'<div id="container">';
			}else{
				echo 	'<div id="containerr">';
			}
//############################
			echo '<div class="left">'.
				'<a title="Edit Story" href="story_Edit.php?AID='.$Row['AID'].'&PID='.$Row['Project_ID'].'&IID='.$Row['Iteration_ID'].'">'.
				' Story: #'.$Row['ID'].'</A>';
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
//				ViewGetTasks($Row['AID']);
//				ViewCommentsBlock($Row['AID']);
			echo '</div>';

			echo '<div class="left">'.
				$Row['Tags'];
			echo '</div>';

			echo '<div class="right">';
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

//############################
		echo '</div>';	// Container
		} while ($Row = mysqli_fetch_assoc($Res));
	}
?>

