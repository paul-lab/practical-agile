<?php
	include 'include/header.inc.php';

	if ($Usr['Admin_User'] == 1 )	{
		$showForm = true;
	}else{
		$showForm = false;
	}
	if ($_REQUEST['delete'] && $Usr['Admin_User'] == 1)	{
		$sql='select ((select count(ID) from story where story.Owner_ID ='.$_REQUEST['id'].')+ (select count(ID) from task where task.User_ID ='.$_REQUEST['id'].'))as counted';
		$row = $DBConn->directsql($sqlp);
		if ($row[0]['counted']==0)	{
			auditit(0,0,$_SESSION['Email'],'Deleted User',$_REQUEST['id'].'-'.$_REQUEST['desc']);
			$sql='DELETE FROM user_project WHERE User_ID = '.($_REQUEST['id'] + 0);
			$result=$DBConn->directsql($sql);
			$sql='DELETE FROM user WHERE ID = '.($_REQUEST['id'] + 0);
			$result=$DBConn->directsql($sql);
			if ($result>0){
				$showForm = false;
				$deleted = true;
			}
		}
	}	else if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		echo '<form method="post" action="?">'.
					'<p>Are you sure you want to delete this User?<p>'.
					$_REQUEST['id'].' - '.$_REQUEST['desc'].'<p>'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="hidden" name="desc" value="'.$_REQUEST['desc'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}	else	{
		header('Location:user_List.php');
	}

	include 'include/footer.inc.php';

?>