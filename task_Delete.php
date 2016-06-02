<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'DELETE FROM task WHERE task.ID='.$_GET['id'];
	$DBConn->directsql($sql);
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Deleted task',$_GET['id'].'-'.$_GET['desc']);

?>
