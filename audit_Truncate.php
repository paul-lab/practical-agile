<?PHP

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
	if (dbdriver=='mysql'){
		$sql = 'TRUNCATE TABLE audit';
		$DBConn->directsql($sql);
	}else{
		$sql = 'delete from audit';
		$DBConn->directsql($sql);
		$sql2="delete from sqlite_sequence where name='audit'";
		$DBConn->directsql($sql2);
	}

	auditit(0,0,$_SESSION['Email'],'Audit log truncated','All records deleted and index reset');
	echo '<center><P><B>All Audit records Deleted.</B><P>';
	$showForm = false;
}

if (isset($_POST['beforedate'])){
	$sql = "DELETE FROM audit where audit.When<'".$_REQUEST['Start_Date']."'";
	$DBConn->directsql($sql);
	auditit(0,0,$_SESSION['Email'],'Audit log truncated','All records before '.$_REQUEST['Start_Date'].' deleted');
	echo '<center><P><B>All records before '.$_REQUEST['Start_Date'].' deleted</B><P>';
	$showForm = false;
}

	if ($showForm==false)
	{
		echo '<center><p><p><form  enctype="multipart/form-data" method="post" action="?">';
		echo '			<br><input type="submit" name="ok" value="OK">';
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
<tr><td><input type="submit" name="beforedate" value="Delete">
</td><td> all audit history for <b>all</b> projects before <input type="text" class="date" id="Start_Date" name="Start_Date"></td></tr>

<tr><td><td><p>
<tr><td><input type="submit" name="truncateit" value="Delete">
</td><td> <b>all</b> Audit history for <b>all</b> projects and reset the log</td></tr>

</table>
			</form>
			</center>
<?php

		}
	}

	include 'include/footer.inc.php';
?>
