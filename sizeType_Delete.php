<?php
	include 'include/header.inc.php';

	$showForm = true;
	if ($_REQUEST['delete'])	{
		$DBConn->directsql('Delete FROM size WHERE `Type`='.$_REQUEST['id']);
		$DBConn->directsql('DELETE FROM size_type WHERE ID='.$_REQUEST['id']);
		$showForm = false;
		$deleted = true;
	}	else if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		echo '<form method="post" action="?">'.
					'Are you sure you want to delete this Size type?<br />'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}	else{
		header('Location:sizeType_List.php');
	}

	include 'include/footer.inc.php';

?>