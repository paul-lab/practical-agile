<?php
	include 'include/header.inc.php';

	$showForm = true;
	if ($_REQUEST['delete'])	{
		$DBConn->directsql('DELETE FROM queries WHERE ID = '.($_REQUEST['ID']));
		$showForm = false;
		$deleted = true;
	}	else if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		echo '<form method="post" action="?">'.
					'<p>Are you sure you want to delete this Report?<p>'.
					$_REQUEST['ID'].' - '.$_REQUEST['desc'].'<p>'.
					'<input type="hidden" name="ID" value="'.$_REQUEST['ID'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}	else{
		header('Location:report_List.php');
	}

	include 'include/footer.inc.php';

?>