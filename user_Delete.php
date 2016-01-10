<?php
	include 'include/header.inc.php';

	if ($Usr['Admin_User'] == 1 )
	{
		$showForm = true;
	}else{
		$showForm = true;
	}
 
	if ($_REQUEST['delete'] && $Usr['Admin_User'] == 1)
	{
		auditit(0,0,$_SESSION['Email'],'Deleted User',$_REQUEST['id'].'-'.$_REQUEST['desc']);
		if (mysqli_query($DBConn, 'DELETE FROM user WHERE ID = '.($_REQUEST['id'] + 0)))
		{
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
					'<p>Are you sure you want to delete this User?<p>'.
					$_REQUEST['id'].' - '.$_REQUEST['desc'].'<p>'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="hidden" name="desc" value="'.$_REQUEST['desc'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}
	else
	{
		header('Location:user_List.php');
	}

	include 'include/footer.inc.php';

?>