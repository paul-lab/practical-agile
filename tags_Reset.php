<?php
	include 'include/header.inc.php';


	$showForm = true;
	if ($_REQUEST['reset'])	{
		if (readonly($_REQUEST['PID']) == 0 )		{
			$sql = 'delete from tags where Project_ID='.$_REQUEST['PID'];
			$DBConn->directsql($sql);

			$sql = "insert into tags (`Project_ID`, `Desc`) select Project_ID, GROUP_CONCAT(Tags) as Tags from story where length(Tags)> 0 and Project_ID=".$_REQUEST['PID']." group by Project_ID";
			$DBConn->directsql($sql);
			$sql = 'select * from tags where Project_ID='.$_REQUEST['PID'];
			$tags_Row = $DBConn->directsql($sql);
			$tags_Row = $tags_Row[0];
			$newTags = implode(",",array_unique(explode(",", $tags_Row['Desc'])));
			$sql='UPDATE tags SET `Desc`="'.$newTags.'" where Project_ID='.$_REQUEST['PID'];
			$tags_Res = $DBConn->directsql($sql);
			$showForm = false;
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Clear Unused Tags','','');
		}
	}

	if ($_REQUEST['noreset'])	{
		$showForm = false;
		$resetd = false;
		header('Location:project_Summary.php?PID='.$_REQUEST['PID']);
	}

	if ($showForm)	{
		echo '<form method="post" action="?"><p><h2>'.
					'Are you sure you want to Clear unused Story Tags for this project?<br /></h2><p>'.
					'<p>'.
					'<input type="submit" name="reset" value="Yes, Clear"> &nbsp; '.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input type="submit" name="noreset" value="No, Don\'t clear">'.
					 '</form>';
	}else{
		header('Location:project_Summary.php?PID='.$_REQUEST['PID']);
	}

	include 'include/footer.inc.php';

?>