<?php 
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'UPDATE task SET task.Done='.$_GET['DONE'].' WHERE task.ID='.$_GET['TID'];
    	mysqli_query($DBConn, $sql);

	if($_GET['DONE']==0){$Status='Todo';};
	if($_GET['DONE']==1){$Status='Doing';};
	if($_GET['DONE']==2){$Status='Done';};
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Update task status',$_GET['TID'].'-'.$_GET['desc'],$Status);
?>