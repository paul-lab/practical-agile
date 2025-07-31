<?PHP
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

include 'include/header.inc.php';

?>
<script>
$(function() {
var date = new Date();
date.setMonth(date.getMonth() - 12);

	$('.date').datepicker({

		numberOfMonths: 1,
		dateFormat: "yy-mm-dd",
		showButtonPanel: true

	});
});

$(document).ready(function(){
	var date = new Date();
	date.setMonth(date.getMonth() - 12);
	$("#Start_Date").datepicker( "setDate" , date  );
});
</script>
<?PHP
$showForm = true;

if (isset($_POST['ok'])) header('Location: project_List.php');

if (isset($_POST['truncateit'])){

	$sql = 'TRUNCATE TABLE audit';
	$DBConn->directsql($sql);


	auditit(0,0,$_SESSION['Email'],'Audit log truncated','All records deleted and index reset');
	echo '<center><P><B>All Audit records Deleted.</B><P>';
	$showForm = false;
}

if (isset($_POST['beforedate'])){
	$sql = "DELETE FROM audit where audit.When< ?";
	$DBConn->directsql($sql, $_REQUEST['Start_Date']);
	auditit(0,0,$_SESSION['Email'],'Audit log truncated','All records before '.$_REQUEST['Start_Date'].' deleted');
	echo '<center><P><B>All records before '.$_REQUEST['Start_Date'].' deleted</B><P>';
	$showForm = false;
}

	if ($showForm==false)
	{
		echo '<center><p><p><form  enctype="multipart/form-data" method="post" action="?">';
		echo '			<br><input  class="btn" type="submit" name="ok" value="OK">';
		echo '			</form></center>';
	}

	if ($showForm)
	{
		if ($Usr['Admin_User']==1 )
		{
?>
			<center><P>
			<form  enctype="multipart/form-data" method="post" action="?">
<table>
<tr><td><td><p>
<tr><td><input  class="btn" type="submit" name="beforedate" value="Delete">
</td><td> all audit history for <b>all</b> projects before <input type="text" class="date" id="Start_Date" name="Start_Date"></td></tr>

<tr><td><td><p>
<tr><td><input  class="btn" type="submit" name="truncateit" value="Delete">
</td><td> <b>all</b> Audit history for <b>all</b> projects and reset the log</td></tr>

</table>
			</form>
			</center>
<?php

		}
	}

	include 'include/footer.inc.php';
?>
