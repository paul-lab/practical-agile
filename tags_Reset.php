<?php
	include 'include/header.inc.php';


	$showForm = true;
	if ($_REQUEST['reset'])
	{
		if (readonly($_REQUEST['PID']) ==0 )
		{
			$sql = 'delete from tags where Project_ID='.$_REQUEST['PID'];
			$tags_Res = mysqli_query($DBConn, $sql);

			$sql = "insert into tags (Project_ID, tags.Desc) select Project_ID, GROUP_CONCAT(distinct(Tags) SEPARATOR ',') as Tags from story where length(Tags)> 0 and Project_ID=".$_REQUEST['PID']." group by Project_ID";
			$tags_Res = mysqli_query($DBConn, $sql);

			$sql = 'select * from tags where Project_ID='.$_REQUEST['PID'];
			$tags_Res = mysqli_query($DBConn, $sql);
			$tags_Row = mysqli_fetch_assoc($tags_Res);
			$newTags = implode(",",array_unique(explode(",", $tags_Row['Desc'])));
			$sql='UPDATE tags SET tags.Desc="'.$newTags.'" where tags.Project_ID='.$_REQUEST['PID'];
			$tags_Res = mysqli_query($DBConn, $sql);
			$showForm = false;

			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Clear Unused Tags','','');

		}
	}
	
	if ($_REQUEST['noreset'])
	{
		$showForm = false;
		$resetd = false;
		header('Location:project_Summary.php?PID='.$_REQUEST['PID']);
	}

	if ($showForm)
	{
		echo '<form method="post" action="?"><p><h2>'.
					'Are you sure you want to Clear unused Tags for this project?<br /></h2><p>'.
					'<p>'.
					'<input type="submit" name="reset" value="Yes, Reset"> &nbsp; '.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input type="submit" name="noreset" value="No, Don\'t Reset">'.
					 '</form>';
	}
	else
	{
		header('Location:project_Summary.php?PID='.$_REQUEST['PID']);		
	}

	include 'include/footer.inc.php';

?>