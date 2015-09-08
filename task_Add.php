<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'INSERT INTO task SET Story_AID="'.$_GET['AID'].
		'", task.User_ID="'.$_GET['user'].
		'", task.Rank="30000'.
		'", task.Desc="'.mysqli_real_escape_string($DBConn, $_GET['desc']).
		'",  Done="0'.
		'", Expected_Hours="'.$_GET['exph'].
		'", Actual_Hours="'.$_GET['acth'].'";';
	mysqli_query($DBConn, $sql);
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Added task',$_GET['desc'].' Assign to:'.Get_User($_GET['user']).' Expect. h:'.$_GET['exph'].' Act. h:'.$_GET['acth']);

?>
