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
	$sql= 'SELECT `Desc` from tags where tags.Project_ID='.$_GET['PID'];
	$tag_Row = $DBConn->directsql($sql);
	if (count($tag_Row) > 0)
	{
		echo $tag_Row[0]['Desc'];
    	}else{
		echo '';
	}
?>