<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Deleted File:'.$_GET['Name'].'.'.$_GET['Type']);

	$sql= "DELETE FROM upload WHERE upload.Name=UNHEX('".$_GET['Name']."')";
	mysqli_query($DBConn, $sql);
	if(!mysqli_error($DBConn))
	{
		unlink('upload/'.$_GET['Name'].'.'.$_GET['Type']);
	}
?>
