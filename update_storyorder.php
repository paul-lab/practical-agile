<?php

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}
	$rank=0;
	foreach($_GET['story'] as $key=>$value) {
		$rank+=10;
		$sql= 'UPDATE story SET Iteration_Rank='.$rank.' WHERE AID='.$value;
    	$DBConn->directsql($sql);;
	}
	if($_GET[rank]==='i')	{
		$act='Increased Rank';
	}else{
		$act='Decreased Rank';
	}
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],$act);
?>