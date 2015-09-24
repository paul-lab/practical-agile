<?php
	include 'include/header.inc.php';

echo '<a href="project_List.php">All</a>->';
echo '<a href="project_Edit.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo Get_Iteration_Name($_REQUEST['IID']);


	$showForm = true;
	if ($_REQUEST['delete'])
	{
		if (readonly($_REQUEST['PID']) ==0 )
		{
			$asql='SELECT * from story where AID='.$_REQUEST['id'];
			$aqry=mysqli_query($DBConn,$asql);
			$aresult = mysqli_fetch_assoc($aqry);
			// for each field 
			foreach ($aresult as $key => $value)
			{					
				if ($aresult[$key]){auditit($_REQUEST['PID'],$_REQUEST['id'],$_SESSION['Email'],'Deleted '.$key,$aresult[$key]);}
			}

			if (mysqli_query($DBConn, 'DELETE FROM story WHERE AID='.$_REQUEST['id']. ' AND Project_ID='.$_REQUEST['PID']))
			{
				$showForm = false;
				$deleted = true;
				Update_Iteration_Points($_REQUEST['IID']);
			}
		}
	}
	else if ($_REQUEST['nodelete'])
	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)
	{
		$Res=mysqli_query($DBConn, 'SELECT ID, Summary, Size FROM story WHERE AID='.$_REQUEST['id']. ' AND Project_ID='.$_REQUEST['PID']);
		$Row=mysqli_fetch_assoc($Res);
		echo '<form method="post" action="?">'.
			'<p><b>#'.$Row['ID'].' - '.$Row['Summary'].' ('.$Row['Size'].' pts.)</b><p>'.
			'Are you sure you want to delete this story ?<br />'.
			'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
			'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
			'<input type="hidden" name="IID" value="'.$_REQUEST['IID'].'">'.
			'<input type="submit" name="delete" value="Yes, Delete">'.
			' &nbsp; &nbsp; '.
			'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
		 '</form>';
	}
	else
	{
		header('Location:story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID']);
	}

	include 'include/footer.inc.php';

?>
