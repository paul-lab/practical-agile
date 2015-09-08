<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php'); 

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

	$comment_text = mysqli_real_escape_string($DBConn, $_REQUEST['comment_text']);


	if($_REQUEST['Type']=="s"){
		$q = "INSERT INTO comment (Parent_ID, User_Name, Story_AID, Comment_Text) VALUES (".$_REQUEST['Parent_ID'].", '".$_REQUEST[User_Name]."', ".$_REQUEST['Story_AID'].", '".$comment_text."' )";
		auditit($_REQUEST['PID'],$_REQUEST['Story_AID'],$_SESSION['Email'],'Added Comment','',$_REQUEST['comment_text']);
	}else{
		if ($_REQUEST['Story_AID']==0) // this means an iteration that has no comments against it.
		{
			$icoid=NextIterationCommentObject(); // so get the next comment object id		
			$q='Update Iteration set Comment_Object_ID='.$icoid.' where ID='.$_REQUEST['Iteration_ID']; // and set it
			$row = mysqli_query($DBConn, $q);	
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added Iteration Comment','',$_REQUEST['comment_text']);

		} else {
			$icoid=$_REQUEST['Story_AID'];
		}
		$q = "INSERT INTO comment (Parent_ID, User_Name, Comment_Object_ID, Comment_Text) VALUES (".$_REQUEST['Parent_ID'].", '".$_REQUEST[User_Name]."', ".$icoid.", '".$comment_text."' )";
	}

	$row = mysqli_query($DBConn, $q);
	$id = mysqli_insert_id($DBConn);

	if(mysqli_affected_rows($DBConn)==1) {
		$r = mysqli_query($DBConn, 'select * from comment where ID ='.$id);
		$row = mysqli_fetch_assoc($r);
		GetComments($row, $_REQUEST['replyid'], $_REQUEST['Type']);
	}
	else {
		echo $q; 
		echo "Comment cannot be posted. Please try again.";
	} 
?>
