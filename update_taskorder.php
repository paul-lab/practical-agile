<?php

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	foreach($_GET['task'] as $key=>$value) {
		$key = ($key+1) * 100;
		$sql= 'UPDATE task SET Rank='.$key.' WHERE ID='.$value;
    	$DBConn->directsql($sql);
	}
?>