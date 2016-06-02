<?php
	include 'include/header.inc.php';


echo '<a href="project_List.php">All</a>->';
echo '<a href="project_Edit.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>';


	$showForm = true;
	if ($_REQUEST['delete'])	{
		$DBConn->directsql('DELETE FROM story_type WHERE ID = '.($_REQUEST['id'] + 0));
		$showForm = false;
		$deleted = true;
	}	else if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		echo '<form method="post" action="?">'.
					'Are you sure you want to delete this story type?<br />'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete">&nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}
	else
	{
		header('Location:storyType_List.php?PID='.$_REQUEST['PID']);
	}

	include 'include/footer.inc.php';

?>
