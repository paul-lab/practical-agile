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

	/* include login functions */
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');
	auditit(0,0,$_SESSION['Email'],'Logout');
	session_destroy();

?>
<meta http-equiv="refresh" content="0; url=index.php">
<h1>
<center>
<br>
Logged Out
</h1>