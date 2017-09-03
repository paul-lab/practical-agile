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
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Deleted File:'.$_GET['Name'].'.'.$_GET['Type']);
	$sql= "DELETE FROM upload WHERE upload.Name=UNHEX('".$_GET['Name']."')";
	$cnt=$DBConn->directsql($sql);
	if($cnt==1)
	{
		unlink(getcwd().'/upload/'.$_GET['Name'].'.'.$_GET['Type']);
	}
?>
