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
// Global $DBConn;

// c3284d0f94606de1fd2af172aba15bf3
	$sql= 'DELETE from project_estimate WHERE PID='.$_GET['PID'];
	$DBConn->directsql($sql);
	$sql= 'INSERT into project_estimate(PID,EMail,Estimate) VALUES('.$_GET['PID'].',"c3284d0f94606de1fd2af172aba15bf3",-1)';
	$DBConn->directsql($sql);
	$sql='Insert into project_estimate (PID, EMail, Estimate) SELECT '.$_GET[PID].', Email,0 FROM User LEFT JOIN user_project ON user.ID  = user_project.User_ID  WHERE user_project.Project_ID='.$_GET['PID'].' and Disabled_User !=1';
	$DBConn->directsql($sql);
?>
