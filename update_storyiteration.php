<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'UPDATE story SET story.Iteration_ID='.$_GET['IID'].' WHERE story.AID='.$_GET['AID'];
	mysqli_query($DBConn, $sql);
	Update_Iteration_Points($_GET['IID']);
	Update_Iteration_Points($_GET['OIID']);

	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Move story',Get_Iteration_Name($_GET['OIID'],false),Get_Iteration_Name($_GET['IID'],false));
?>