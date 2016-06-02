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
	if ($ThisType=='PID'){
		$audit_sql.=' limit 200';
	}
	$audit_Row = $DBConn->directsql($audit_sql);
	if (count($audit_Row) > 0)	{
	$rowcnt=0;
		do	{
			echo	'<li>';
				echo $audit_Row[$rowcnt]['User'].' '.$audit_Row[$rowcnt]['Action'].' @ '.$audit_Row[$rowcnt]['When'];
				// Only print if we need to
				if ($audit_Row[$rowcnt]['From'] || $audit_Row[$rowcnt]['To'])
				{
					if (strlen($audit_Row[$rowcnt]['From'])> 500)
					{
						$audit_Row[$rowcnt]['From']=substr($audit_Row[$rowcnt]['From'],0,500).'...';
					}
					if (strlen($audit_Row[$rowcnt]['To'])> 500)
					{
						$audit_Row[$rowcnt]['To']=substr($audit_Row[$rowcnt]['To'],0,500).'...';
					}
					echo '<table width=100% cellspacing="2" border=0><tr><td  bgcolor="#F2F2F2" width=50%>';
					echo 'From: '.$audit_Row[$rowcnt]['From'];
					echo '<td  bgcolor="#F2F2F2" width=50%>';
					echo 'To: '.$audit_Row[$rowcnt]['To'];
					echo '</table>';
				}
			'</li>';
			$rowcnt+=1;
		}
		while ($rowcnt < count($audit_Row));
	}
}

Getaudit($_GET['type'],$_GET['id']);

?>