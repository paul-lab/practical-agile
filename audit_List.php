<?php

// List audits for a Story

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function GetAudit($ThisProject, $ThisStory)
{
	Global $DBConn;

// The audit list must be wrapped inside a div in the host document as follows.
//	echo '<div class="auditdialog" id="allaudits_'.$ThisStory.'">';

	echo	'<ul id="sortableaudit'.$ThisStory.'">';

	$audit_sql = 'SELECT * FROM audit where audit.AID='.$ThisStory.' order by ID Desc';
	$audit_Res = mysqli_query($DBConn, $audit_sql);
	if ($audit_Row = mysqli_fetch_array($audit_Res))
	{
		do
		{
			echo	'<li>';		
				echo $audit_Row['User'].' '.$audit_Row['Action'].' @ '.$audit_Row['When'];
				// Only print if we need to
				if ($audit_Row['From'] || $audit_Row['To'])
				{
					echo '<table width=100% cellspacing="2" border=0><tr><td  bgcolor="#F2F2F2" width=50%>';
					echo 'From: '.$audit_Row['From'];
					echo '<td  bgcolor="#F2F2F2" width=50%>';
					echo 'To: '.$audit_Row['To'];
					echo '</table>';
				}
			'</li>';
		}
		while ($audit_Row = mysqli_fetch_array($audit_Res));
	}
}

Getaudit($_GET['pid'],$_GET['aid']);

?>