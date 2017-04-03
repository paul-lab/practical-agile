<?php
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
//	$str = nl2br(htmlentities($str));
//	$str = nl2br($str);

	if(preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
		$str = "'$str";
	}
	if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
}
	// filename for download
	if (empty($_GET['QID'])){
		$filename = "xxx_export.csv";
	}else{
		$filename = $_GET['etype']."_export.csv";
	}

	header("Content-Disposition: attachment; filename=\"$filename\"");
	header("Content-Type: text/csv; charset=UTF-16LE");
	$out = fopen("php://output", 'w');
	$flag = false;

// Make a simple SELECT query

	$qsql = 'SELECT QSQL, Qorder, queries.Desc FROM queries where ID='.$_REQUEST['QID'];
	$QRow = $DBConn->directsql($qsql);
	$QRow =	$QRow[0];
	$cond=" ".$QRow['QSQL'];
	$cond= str_replace('{User}', $_SESSION['ID'], $cond);
	$cond= str_replace('{Iteration}', $_REQUEST['IID'], $cond);
	$cond= str_replace('{Project}', $_REQUEST['PID'], $cond);
	$cond= str_replace('{Backlog}', $Project['Backlog_ID'], $cond);

	$q =$sel.$cond.' '.$QRow['Qorder'];

//$q= 'select task.ID, User_ID, Friendly_Name,  Task.Desc, Done, Expected_Hours, Actual_Hours from Story, Task  left JOIN user on task.User_ID = User.ID  where Story.Iteration_ID = 1 and task.Story_AID = story.AID';

//echo $q;

// do we have any results
	$r = $DBConn->directsql($q);
	if ($r){
		foreach($r as $row)	{
			if(!$flag) {
				// display field/column names as first row
				fputcsv($out, array_keys($row), ',', '"');
				$flag = true;
			}
			array_walk($row, 'cleanData');
			fputcsv($out, array_values($row), ',', '"');
		}
	}

	fclose($out);
	exit;

?>
