<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}


	$sql= 'UPDATE story SET story.Parent_Story_ID="'.$_GET['NPAR'].'" WHERE story.AID='.$_GET['SID'];
	mysqli_query($DBConn, $sql);
	Update_Parent_Points($_GET['SID']);
	Update_oldParent_Points($_GET['OPAR']);

	auditit($_GET['PID'],$_GET['SID'],$_SESSION['Email'],'Update parent',fetchusingID('Summary',$_GET['OPAR'],'story'),fetchusingID('Summary',$_GET['NPAR'],'story'));
fetchusingID($col,$val,$tabl)

?>
