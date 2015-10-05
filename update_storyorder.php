<?php
//	include 'include/header.inc.php';

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	foreach($_GET['story'] as $key=>$value) {
		$key = ($key+1) * 10;
		$sql= 'UPDATE story SET story.Iteration_Rank='.$key.' WHERE story.AID='.$value;
    		mysqli_query($DBConn, $sql);
	} 
	
	if($_GET[rank]==='i')
	{
		$act='Increased Rank';
	}else{
		$act='Decreased Rank';
	}

	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],$act);

?>