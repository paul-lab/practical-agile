<?php

// List audits for a Story

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function GetAudit($ThisType, $ThisID)
{
	Global $DBConn;

// The audit list must be wrapped inside a div in the host document as follows.
//	echo '<div class="auditdialog" id="allaudits_'.$ThisID.'">';

	echo	'<ul id="sortableaudit'.$ThisID.'">';

	$audit_sql = 'SELECT * FROM audit where audit.'.$ThisType.'='.$ThisID.' order by ID Desc';
	if ($ThisType=='PID')
	{
		$audit_sql.=' limit 200';
	}

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
					if (strlen($audit_Row['From'])> 500)
					{
						$audit_Row['From']=substr($audit_Row['From'],0,500).'...';
					}
					if (strlen($audit_Row['To'])> 500)
					{
						$audit_Row['To']=substr($audit_Row['To'],0,500).'...';
					}
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

Getaudit($_GET['type'],$_GET['id']);

?>