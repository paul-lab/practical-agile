<?php
	include 'include/header.inc.php';

	$showForm = true;
	if ($_REQUEST['delete']){
		if (readonly($_REQUEST['PID']) == 0 ){
			$sql = 'select count(*) as nums from story where Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID = '.$_REQUEST['IID'];
			$iteration_Row = $DBConn->directsql($sql);
			var_dump($iteration_Row);
			if ($iteration_Row[0]['nums']==0){
				$result=$DBConn->directsql('DELETE FROM iteration WHERE ID='.($_REQUEST['IID']));
				$result=$DBConn->directsql('delete from points_log where Object_ID='.$_REQUEST['POID']);
				$result=$DBConn->directsql('delete from comment where Comment_Object_ID='.$_REQUEST['COID']);
				$showForm = false;
				$deleted = true;
			}
		}
	}

	if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		echo '<form method="post" action="?"><p><h2>'.Get_Iteration_Name($_REQUEST['IID']).'</h2><p>'.
					'Are you sure you want to delete this Iteration?<br />'.
					'<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">'.
					'<input type="hidden" name="IID" value="'.$_REQUEST['IID'].'">'.
					'<input type="hidden" name="POID" value="'.$_REQUEST['POID'].'">'.
					'<input type="hidden" name="COID" value="'.$_REQUEST['COID'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}else{
		header('Location:iteration_List.php?PID='.$_REQUEST['PID']);
	}

	include 'include/footer.inc.php';

?>