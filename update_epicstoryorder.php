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
		$sql= 'UPDATE story SET story.Epic_Rank='.$key.' WHERE story.AID='.$value;
    		mysqli_query($DBConn, $sql);
	} 

	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Changed Epic rank');
?>