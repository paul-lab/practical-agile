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

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	if($_REQUEST['Type']=="s"){
		$q = "INSERT INTO comment (Parent_ID, User_Name, Story_AID, Comment_Text) VALUES (?, ?, ?, ?)";
		$bind = array($_REQUEST['Parent_ID'], $_REQUEST['User_Name'], $_REQUEST['Story_AID'], $_REQUEST['comment_text']);
		auditit($_REQUEST['PID'],$_REQUEST['Story_AID'],$_SESSION['Email'],'Added Comment','',$_REQUEST['comment_text']);
	}else{
		if ($_REQUEST['Story_AID']==0) { // this means an iteration that has no comments against it.
			$icoid=NextIterationCommentObject(); // so get the next comment object id
			$q='Update Iteration set Comment_Object_ID= ? where ID= ?'; // and set it
			$DBConn->directsql($q, array($icoid, $_REQUEST['Iteration_ID']));
		}else{
			$icoid=$_REQUEST['Story_AID'];
		}

		$q = "INSERT INTO comment (Parent_ID, User_Name, Comment_Object_ID, Comment_Text) VALUES (?, ?, ?, ?)";
		$bind = array($_REQUEST['Parent_ID'], $_REQUEST['User_Name'], $icoid, $_REQUEST['comment_text']);
		auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added Iteration Comment','',$_REQUEST['comment_text']);
	}
	$row = $DBConn->directsql($q, $bind);

	if($row!=0) {
		$r = 'select * from comment where ID= ?';
		$row = $DBConn->directsql($r, $row);
		GetComments($row[0], $_REQUEST['replyid'], $_REQUEST['Type']);
	}else {
		echo $q;
		echo "Comment cannot be posted. Please try again.";
	}
?>
