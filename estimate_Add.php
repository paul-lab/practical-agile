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

	$sql= 'UPDATE project_estimate SET Estimate= ? WHERE PID= ? and EMail= ?';
	$DBConn->directsql($sql, array($_GET['EST'], $_GET['PID'], $_GET['WHO']));
	$sql= 'UPDATE project_estimate SET Estimate=0 WHERE PID= ? and EMail="c3284d0f94606de1fd2af172aba15bf3"';
	$DBConn->directsql($sql, $_GET['PID']);

?>
