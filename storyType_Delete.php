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
					'Are you sure you want to delete this story type?<p>'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input class="btn" type="submit" name="delete" value="Yes, Delete">&nbsp; '.
					'<input class="btn" type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}
	else
	{
		header('Location:storyType_List.php?PID='.$_REQUEST['PID']);
	}

	include 'include/footer.inc.php';

?>
