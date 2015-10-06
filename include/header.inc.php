<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	/*user details based on the data saved in session
	  if login is not correct, send user to the login page*/
	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		setcookie('cbadcnt',$_COOKIE['cbadcnt']+1);
		$fromhere = substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1).'?'.$_SERVER['QUERY_STRING'];
		header("Location:index.php?gobackto=".urlencode($fromhere));
		exit();
	}
	setcookie('cbadcnt',0);
?>
<!DOCTYPE html>

<html>
	<head>
		<meta http-equiv="content-type" content="text/html; charset=windows-1250">
		<meta http-equiv="X-UA-Compatible" content="IE=edge" >
		<title>Practical Agile:</title>
		<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
		<script src="jquery/jquery.js"></script>    
		<script src="jquery/jquery-ui.js"></script>
 		<link rel="stylesheet" href="jquery/jquery-ui.css" />
		<script src="scripts/header-hash18c1e4ac59611718ad1a1569fc46ab99.js"></script>
	</head>
	
<div class="header noPrint">
<div id="breadcrumbs"></div>
<div id="navicons"></div>

<?php

if (!empty($_REQUEST['PID'])){
	// create a class with the id of the current project that we can access from anywhere via javascript
	echo '<div class="thisproject hidden" id='.$_REQUEST['PID'].'></div>';
	echo '<div class="thisiteration hidden" id='.$_REQUEST['IID'].'></div>';
	echo '<div class="suserlist" >';
	echo '&nbsp; &nbsp;<a title="User List" href="user_List.php?PID='.$_REQUEST['PID'].'" target="_blank"><img src="images/userlist-large.png"></a>';
	echo '</div>';
	echo '<div class="search" >';	
	echo '<form method="get" action="story_List.php">';
		echo '&nbsp;<input size="24" title="searchstring, #, owner:, status:, size:, tag:, type: " type="text" name="searchstring" id="searchstring" > ';
		echo '<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">';
		echo '<input type="submit" name="Type" value="search">';
	echo '</form>';
	echo '</div>';

}
	require_once('include/side_menu.php');
?>
</div>
<div id=”container”> 
