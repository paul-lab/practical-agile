<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'UPDATE story SET `Status`="'.$_GET['SAID'].'" WHERE AID='.$_GET['AID'];
	$DBConn->directsql($sql);
	Update_Iteration_Points($_GET['IID']);
	echo $_GET['SAID'];

//this updates both the parent points as well as the parent status
	Update_Parent_Points($_GET['AID']);
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Update Status','',$_GET['SAID']);
?>
