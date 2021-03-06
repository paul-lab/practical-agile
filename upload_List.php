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

// List and manage uploads for a Story

	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function Getuploads($ThisProject, $ThisStory)
{
	Global $DBConn;

// The upload list must be wrapped inside a div in the host document as follows.
//	echo '<div class="uploaddialog" id="alluploads_'.$ThisStory.'"></div>';

	echo	'<ul id="tableupload'.$ThisStory.'">';

	$upload_sql = 'SELECT upload.AID, HEX(upload.Name) as Name, upload.Desc, upload.Size, upload.Type  FROM upload where upload.AID='.$ThisStory.' order by upload.Desc';
	$upload_Res = $DBConn->directsql($upload_sql);
	foreach ($upload_Res as $upload_Row)		{
		echo	'<li class="divRow" id=upload_'.$upload_Row['Type'].'>';
			echo	'<div class="divCell">';
			echo '<a target="_blank" href="upload/'.$upload_Row['Name'].'.'.$upload_Row['Type'].'">'.$upload_Row['Desc'].'</a>';
			echo '</div> '.
			'<div class="divCell1">'.$upload_Row['Size'].' b</div> '.
			'<div class="divCell1 deleteupload" id="'.$upload_Row['Name'].'"><img src="images/delete-small.png"></div>'.
			'</li>';
	}
	echo '</ul>';
		echo
			'<div class="micromenudiv-input" id="newrow_'.$ThisStory.'">'.
				'<img class="uploadnew" src="images/add-small.png"> '.
				'<input type="file" id="ndesc_'.$ThisStory.'" title="upload this file" value="" size="50">'.
				'<input type="hidden" name = "AID" value="'.$ThisStory.'"/>';
		echo	'</div>';

}

Getuploads($_GET['PID'],$_GET['AID']);

?>