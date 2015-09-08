<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}



	$sql= 'DELETE FROM comment WHERE ID='.$_GET['id'];
	mysqli_query($DBConn, $sql);
	$cnt= mysqli_affected_rows($DBConn);
	echo $cnt;				
	
	if ($cnt > 0)
	{
		if($_GET['type']=='s')
		{
			auditit($_GET['PID'], $_GET['AID'],$_SESSION['Email'],'Deleted story Comment',fetchusingID('Comment_Text',$_GET['id'],'comment'));
		}else {
			auditit($_GET['PID'], $_GET['AID'],$_SESSION['Email'],'Deleted iteration Comment',fetchusingID('Comment_Text',$_GET['id'],'comment'));
		}
	}

?>
