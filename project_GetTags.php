<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');
	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}
	$sql= 'SELECT tags.Desc from tags where tags.Project_ID='.$_GET['PID'];
	$tag_Res = mysqli_query($DBConn, $sql);
	if ($tag_Row = mysqli_fetch_array($tag_Res))
	{
		echo $tag_Row['Desc'];		
    	}else{
		echo '';
	}
?>