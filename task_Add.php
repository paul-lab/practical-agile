<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}
	if (empty($_REQUEST['user'])) $_REQUEST['user']='""';
	if (empty($_REQUEST['exph'])) $_REQUEST['exph']=0;
	if (empty($_REQUEST['acth'])) $_REQUEST['acth']=0;

	$sql= 'INSERT INTO task (Story_AID, User_ID,`Rank`,`Desc`,Done,Expected_Hours,Actual_Hours) VALUES('.
	$_REQUEST['AID'].', '.$_REQUEST['user'].', '.'30000'.', "'.htmlentities($_REQUEST['desc']).'", '.'0'.', '.$_REQUEST['exph'].', '.$_REQUEST['acth'].')';
	$DBConn->directsql($sql);
	auditit($_REQUEST['PID'],$_REQUEST['AID'],$_SESSION['Email'],'Added task',$_GET['desc'].' Assign to:'.Get_User($_REQUEST['user']).' Expect. h:'.$_REQUEST['exph'].' Act. h:'.$_REQUEST['acth']);
?>