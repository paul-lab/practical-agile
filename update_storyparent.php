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
	if ($_GET['NPAR']=='root_1') $_GET['NPAR']=0;

	$sql= 'UPDATE story SET Parent_Story_ID="'.$_GET['NPAR'].'" WHERE AID='.$_GET['SID'];
	$DBConn->directsql($sql);
	Update_Parent_Points($_GET['SID']);
	Update_oldParent_Points($_GET['OPAR']);
	auditit($_GET['PID'],$_GET['SID'],$_SESSION['Email'],'Update parent',fetchusingID('Summary',$_GET['OPAR'],'story'),fetchusingID('Summary',$_GET['NPAR'],'story'));
?>
