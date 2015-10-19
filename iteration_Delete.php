<?php
	include 'include/header.inc.php';


	$showForm = true;
	if ($_REQUEST['delete'])
	{
		if (readonly($_REQUEST['PID']) ==0 )
		{
			$sql = 'select count(*) as nums from story where Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID = '.$_REQUEST['IID'];
			$iteration_Res = mysqli_query($DBConn, $sql);
			$iteration_Row = mysqli_fetch_assoc($iteration_Res);
			if ($iteration_Row['nums']!=0)
			{
				if (mysqli_query($DBConn, 'DELETE FROM iteration WHERE ID='.($_REQUEST['IID'])))
				{
					$showForm = false;
					$deleted = true;
					$sql ='delete from points_log where Object_ID='.$_REQUEST['OID'];
					mysqli_query($DBConn, $sql);
				}
			}
		}
	}
	
	if ($_REQUEST['nodelete'])
	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)
	{
		echo '<form method="post" action="?"><p><h2>'.Get_Iteration_Name($_REQUEST['IID']).'</h2><p>'.
					'Are you sure you want to delete this Iteration?<br />'.
					'<input type="hidden" name="IID" value="'.$_REQUEST['IID'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}
	else
	{
		header('Location:iteration_List.php?PID='.$_REQUEST['PID']);		
	}

	include 'include/footer.inc.php';

?>