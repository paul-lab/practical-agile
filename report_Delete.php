<?php
/*
* Practical Agile Scrum tool
*
* Copyright 2013-2017, P.P. Labuschagne

* Released under the MIT license.
* https://github.com/paul-lab/practical-agile/blob/master/_Licence.txt
*
* Homepage:
*   	http://practicalagile.co.uk
*	http://practicalagile.uk
*
*/
	include 'include/header.inc.php';

	$showForm = true;
	if ($_REQUEST['delete'])	{
		auditit(0,$_REQUEST['AID'],$_SESSION['Email'],'Deleted report',$_REQUEST['ID'],$_REQUEST['desc']);
		$DBConn->directsql('DELETE FROM queries WHERE ID = '.($_REQUEST['ID']));
		$showForm = false;
		$deleted = true;
	}	else if ($_REQUEST['nodelete'])	{
		$showForm = false;
		$deleted = false;
	}

	if ($showForm)	{
		echo '<form method="post" action="?">'.
					'<p>Are you sure you want to delete this Report?<p>'.
					$_REQUEST['ID'].' - '.$_REQUEST['desc'].'<p>'.
					'<input type="hidden" name="ID" value="'.$_REQUEST['ID'].'">'.
					'<input class="btn" type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input class="btn" type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}	else{
		header('Location:report_List.php');
	}

	include 'include/footer.inc.php';

?>