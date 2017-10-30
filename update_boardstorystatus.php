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

//STAID= status order id for his project.
	$sql= 'UPDATE story SET Status=(select Desc from story_status where Project_ID = '.$_GET['PID'].' and `story_status`.`Order`='.$_GET['STAID'].') WHERE AID='.$_GET['AID'];

	echo $sql;
	$DBConn->directsql($sql);
	Update_Iteration_Points($_GET['IID']);

//this updates both the parent points as well as the parent status
	Update_Parent_Points($_GET['AID']);
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Update Status','',$_GET['AID']);
?>
