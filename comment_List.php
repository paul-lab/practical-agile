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

// List and manage tasks for a Story

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function CommentsBlock($ThisID, $Thiskey)
{
	Global $DBConn;

	if ($Thiskey=='s')	{
		$q = "SELECT * FROM comment WHERE Story_AID = ? and Parent_ID=0 ORDER by ID";
	}
	if ($Thiskey=='i')	{
		$q = "SELECT * FROM comment WHERE Comment_Object_ID = ? and Comment_Object_ID <> 0 and Parent_ID=0 ORDER by ID";
	}

// comments must be wrapped in a div like this
//	echo '<div class="commentsdialog" id="commentspop'.$Thiskey.'_'.$ThisID.'"><ul id=commentlist'.$Thiskey.'_'.$ThisID.'> ';
	echo '<ul id=commentlist'.$Thiskey.'_'.$ThisID.'> ';

	$r = $DBConn->directsql($q, $ThisID);
	foreach ($r as $row){
		getComments($row, $ThisID,$Thiskey);
	}

	echo '</ul>';
	echo '<br><div class="smaller" id="replyto_'.$Thiskey.'_'.$ThisID.'"></div>';

	echo '<a href="" onclick="javascript: return false;" title="Add Comment"><img class="submit_button" id="submit_button'.$Thiskey.'_'.$ThisID.'" src="images/add-small.png"></a>';
	echo ' <textarea class="w80 h100" name="comment_text_'.$ThisID.'" rows="3" cols="80" id="comment_text_'.$ThisID.'"></textarea>  ';
	echo ' <input type="hidden" name="User_Name" id="User_Name_'.$ThisID.''.'" value="'.$_SESSION['Name'].'"/>  ';
	echo ' <input type="hidden" name="Parent_ID" id="Parent_ID_'.$Thiskey.'_'.$ThisID.'" value="0"/>  ';
	echo ' <input type="hidden" name="Iteration_ID" id="CIteration_ID" value="'.$_REQUEST['IID'].'"/>  ';

// remember that this is not the Story_AID for iterations it is the Comment_Object_ID
// being a lazy beggar it this is just easier
	echo ' <input type="hidden" name="Story_AID" id="Story_AID_'.$ThisID.''.'" value="'.$ThisID.'"/>  ';

}

CommentsBlock($_GET['id'], $_GET['key']);

?>