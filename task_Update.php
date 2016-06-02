<?php
// update single task details  excluding task status

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}
	if (empty($_REQUEST['exph'])) $_REQUEST['exph']=0;
	if (empty($_REQUEST['acth'])) $_REQUEST['acth']=0;

	$sql= 'UPDATE task SET User_ID="'.$_REQUEST['user'].
		'", `Desc`="'.addslashes($_REQUEST['desc']).
		'", Expected_Hours="'.$_REQUEST['exph'].
		'", Actual_Hours="'.$_REQUEST['acth'].'" WHERE task.ID='.$_REQUEST['TID'];
	$DBConn->directsql($sql);
	auditit($_REQUEST['PID'],$_REQUEST['AID'],$_SESSION['Email'],'Update task',$_REQUEST['desc'].' Assign to:'.Get_User($_REQUEST['user']).' Expect. h:'.$_REQUEST['exph'].' Act. h:'.$_REQUEST['acth']);

?>
