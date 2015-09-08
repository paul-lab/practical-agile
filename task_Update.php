<?php
// update single task details  excluding task status

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'UPDATE task SET task.User_ID="'.$_GET['user'].
		'", task.Desc="'.addslashes($_GET['desc']).
		'", Expected_Hours="'.$_GET['exph'].
		'", Actual_Hours="'.$_GET['acth'].'" WHERE task.ID='.$_GET['TID'];
	mysqli_query($DBConn, $sql);
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Update task',$_GET['desc'].' Assign to:'.Get_User($_GET['user']).' Expect. h:'.$_GET['exph'].' Act. h:'.$_GET['acth']);

?>
