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
	$rets='{"c3284d0f94606de1fd2af172aba15bf3":"1"}';
	$sql= 'select * from project_estimate WHERE PID='.$_GET['PID'];
	$usr_Row = $DBConn->directsql($sql);
	if (count($usr_Row) > 0){
		$rets='{';
		foreach ($usr_Row as $result){
			$rets.='"'.$result[EMail].'":"';
			$rets.=$result[Estimate].'",';
		}
		$rets=  substr($rets,0,-1).'}';
	}
	echo $rets;
?>
