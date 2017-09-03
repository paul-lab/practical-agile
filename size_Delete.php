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

	$showForm = true;
	if ($_REQUEST['delete'])
	{
		$DBConn->directsql('DELETE FROM size WHERE ID = '.($_REQUEST['id'] + 0));
		$showForm = false;
		$deleted = true;
	}
	else if ($_REQUEST['nodelete'])
	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)
	{
		echo '<form method="post" action="?">'.
					'Are you sure you want to delete this Size?<p>'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input class="btn" type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input class="btn" type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}
	else
	{
		header('Location:size_List.php');
	}

	include 'include/footer.inc.php';

?>