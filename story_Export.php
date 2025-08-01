<?PHP
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
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function cleanData(&$str) {

//	if($str == 't') $str = 'True';
//	if($str == 'f') $str = 'False';
//	$str = preg_replace("/\t/", "\\t", $str); 		// Tab
//	$str = preg_replace("/\r?\n/", "\\n", $str);		// New line
	$str = html_entity_decode($str,ENT_QUOTES);
//	$str = nl2br(htmlentities($str));
//	$str = nl2br($str);

	if(preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
		$str = "'$str";
	}
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';

}
	// filename for download
	if (empty($_GET['etype'])){
		$filename = "iteration_export.csv";
	}else{
		$filename = $_GET['etype']."_export.csv";
	}
	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: text/csv; charset=UTF-16LE");
	$out = fopen("php://output", 'w');
	$flag = false;
	$sql='SELECT ID as Story, Epic_Rank, Iteration_Rank, '.
		' (select a.ID from story as a where a.AID = story.Parent_Story_ID) as Parent_Story_ID, '.
		' (select release_details.Name from release_details where release_details.ID = story.Release_ID) as Release_Name, '.
		' (select iteration.Name from iteration where iteration.ID = story.Iteration_ID) as Iteration, '.
		' (select user.Friendly_Name from user where user.ID = story.Owner_ID) as Owner, '.
		' Type, Status, Size, Blocked, Summary, Col_1, As_A, Col_2, Acceptance, Tags';

	//add numchildren if we are exporting the project
	if (!empty($_GET['etype'])) {
		$sql.=', (select count(c.ID) from story as c where c.Project_ID= ? and c.Parent_Story_ID = story.AID ) as Num_Children';
		$audittext=' Project '.Get_Project_Name($_GET['PID']);
	}else{
		$audittext=' Iteration '.Get_Iteration_Name($_GET['IID']);
	}
		$sql.=' FROM story where story.Project_ID= ?';
		$bind = array($_GET['PID'], $_GET['PID']);
	if (!empty($_GET['QID'])){

		$qsql = 'SELECT QSQL, Qorder, queries.Desc as qdesc FROM queries where ID= ?';
		$QRow = $DBConn->directsql($qsql, $_REQUEST['QID']);
		$QRow = $QRow[0];
		$audittext=' Query '.$_GET['QID'].' '.$QRow['qdesc'] ;
		$cond=" ".$QRow['QSQL'];
		$cond= str_replace('{User}', $_SESSION['ID'], $cond);
		$cond= str_replace('{Iteration}', $_REQUEST['IID'], $cond);
		$cond= str_replace('{Project}', $_REQUEST['PID'], $cond);
		$cond= str_replace('{Backlog}', $Project['Backlog_ID'], $cond);
		$sql .= ' and '.$cond.' ' .$QRow['Qorder'];
	}

	// make sure that we dont get parent  stories when this is an iteration export (only really applies for the backlog.)
	if (empty($_GET['etype'])) { $sql.=' and story.Iteration_ID= ? and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) ';
		$bind[] = $_GET['IID'];
	}
	if (empty($_GET['QID'])){ $sql.= ' ORDER BY Iteration_Rank';}
	$result = $DBConn->directsql($sql, $bind) or die('Query failed!');

	foreach ($result as $row)		{
		if(!$flag) {
			// display field/column names as first row
			fputcsv($out, array_keys($row), ',', '"');
			$flag = true;
		}
		array_walk($row, 'cleanData');
		fputcsv($out, array_values($row), ',', '"');
	}

	fclose($out);
	auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Exported',$audittext,$filename);
	exit;
?>