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

<script src="jquery/jquery-1.12.4.min.js"></script>

<script src="jquery/jquery-ui-1.12.min.js"></script>
<link rel="stylesheet" href="jquery/jquery-ui-1.12.min.css" />

		<script src="scripts/header-hash201af008be37c43617d23c6523c28878.js"></script>
	</head>

<div class="header noPrint">
	<div id="breadcrumbs"></div>
	<div id="navicons"></div>

<?php

	if (!empty($_REQUEST['PID'])){
		// create a class with the id of the current project that we can access from anywhere via javascript
		echo '<div class="thisproject hidden" id='.$_REQUEST['PID'].'></div>';
		if (!empty($_REQUEST['IID'])){
			echo '<div class="thisiteration hidden" id='.$_REQUEST['IID'].'></div>';
		}
		echo '<div class="suserlist" >';
		echo '&nbsp; &nbsp;<a title="User List" href="user_List.php?PID='.$_REQUEST['PID'].'" target="_blank"><img src="images/userlist-large.png"></a>';
		echo '</div>';

		echo '<div class="search hoverToShowInfo" >';
		echo '<form method="get" action="story_List.php">';
			echo '&nbsp;<input size="24" type="text" name="searchstring" id="searchstring" > ';
			echo '<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">';
			echo '<input  class="btn" type="submit" name="Type" value="search">';
		echo '</form>';
		echo '<div class="info">'.
			'Filter by <b>field value</b> e.g.'.
			'<table border=0>'.
				'<tr><td colspan=2>#23</td><td>(Story Number)</td></tr>'.
				'<tr><td>o:ppl</td><td>or &nbsp; owner:ppl</td><td>(Initials)</td></tr>'.
				'<tr><td>s:open</td><td>or &nbsp; status:Closed</td><td>(Status Value)</td></tr>'.
				'<tr><td>i:5</td><td>or &nbsp; size:8</td><td>(Size)</td></tr>'.
				'<tr><td>t:debt</td><td>or &nbsp; tag:hierachy</td><td>(Tag string)</td></tr>'.
				'<tr><td>y:Bug</td><td>or &nbsp; type:Feature</td><td>(Card Type)</td></tr>'.
				'<tr><td>r:Rel2</td><td>or &nbsp; release:Release 1</td><td>(Release Name)</td></tr>'.
				'<tr><td colspan=3>or <b>Search string</b> e.g.'.
				'<br>hierarchy or feather boa</td></tr>'.
			'</table>'.
			'<br>'.
			'Do <b>not</b> put quotes "around values or phrases"'.
			'eg release:Release 1 will work release:"Release 1" will not.'.
			'</div>';
		echo '</div>';
	}
	require_once('include/side_menu.php');
?>
</div>
<div id=”container”>