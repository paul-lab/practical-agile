<?php
/*
* Practical Agile Scrum tool
*
* Copyright 2013-2017, P.P. Labuschagne

* Released under the MIT license.
* https://github.com/paul-lab/practical-agile/blob/master/_Licence.txt
*
* Homepage:
*   	http://practicalagile.co.uk
*	http://practicalagile.uk
*
*/
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$sql= 'UPDATE story SET Iteration_ID='.$_GET['IID'].' WHERE story.AID='.$_GET['AID'];
	$DBConn->directsql($sql);
	if ($_GET['mov']=='ltr')	{
		echo Update_Iteration_Points($_GET['IID']);
	}else{
		Update_Iteration_Points($_GET['IID']);
	}

	if ($_GET['mov']=='rtl')	{
		echo Update_Iteration_Points($_GET['OIID']);
	}else{
		Update_Iteration_Points($_GET['OIID']);
	}

	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Move story',Get_Iteration_Name($_GET['OIID'],false),Get_Iteration_Name($_GET['IID'],false));
?>