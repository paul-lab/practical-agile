<?php
	error_reporting (E_ALL ^ E_NOTICE);
	ob_start();
	date_default_timezone_set('Europe/London');
//	date_default_timezone_set('America/Detroit');

// mysqli performs a whole lot better if you use an IP address rather than a name
// If you are using the full install it is probably easier to leave things as they are

	Global $DBConn;
// 	                          ip,          user,   pwd,    database name,   port 
	$DBConn = mysqli_connect("127.0.0.1", "root", "root", "practicalagile","3311" );
	if (!$DBConn) {
	    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
	}
?>

