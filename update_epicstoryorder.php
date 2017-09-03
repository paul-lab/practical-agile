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

	foreach($_GET['story'] as $key=>$value) {
		$key = ($key+1) * 10;
		$sql= 'UPDATE story SET Epic_Rank='.$key.' WHERE AID='.$value;
    	$DBConn->directsql($sql);
	}

	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Changed Epic rank');
?>