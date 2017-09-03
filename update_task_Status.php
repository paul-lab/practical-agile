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

	$sql= 'UPDATE task SET Done='.$_GET['DONE'].' WHERE ID='.$_GET['TID'];
    $DBConn->directsql($sql);

	if($_GET['DONE']==0){$Status='Todo';};
	if($_GET['DONE']==1){$Status='Doing';};
	if($_GET['DONE']==2){$Status='Done';};
	auditit($_GET['PID'],$_GET['AID'],$_SESSION['Email'],'Update task status',$_GET['TID'].'-'.$_GET['desc'],$Status);
?>