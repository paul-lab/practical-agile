<?php
	include 'include/header.inc.php';


	$showForm = true;
	if ($_REQUEST['delete'])
	{
		if (mysqli_query($DBConn, 'DELETE FROM size_type WHERE ID = '.($_REQUEST['id'] + 0)))
		{
			$Res=mysqli_query($DBConn, 'Delete FROM size WHERE Type='.$_REQUEST['id']+ 0);
			$showForm = false;
			$deleted = true;
		}
	}
	else if ($_REQUEST['nodelete'])
	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)
	{
		echo '<form method="post" action="?">'.
					'Are you sure you want to delete this Size type?<br />'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}
	else
	{
		header('Location:sizeType_List.php');
	}

	include 'include/footer.inc.php';

?>