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
echo '<div class="hidden" id="phpbread"><a href="releaseDetails_List.php">Releases</a>->';
echo 'Release Details';
echo '</div>';
?>

<link href="fancytree/ui.fancytree.css" rel="stylesheet" type="text/css">
	<script src="fancytree/jquery.fancytree.min.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="css/comment.css" />
	<link rel="stylesheet" type="text/css" href="css/overrides.css" />

<script>
$(function() {
	document.title = 'Practical Agile: '+$("#phpbread").text().substring(17);
	$("#breadcrumbs").html($("#phpbread").html());
	if ($("#phpnavicons")){
		$("#navicons").html($("#phpnavicons").html());
	}

	$('.date').datepicker({
		numberOfMonths: 2,
		dateFormat: "yy-mm-dd",
		showButtonPanel: true
	});
});
</script>

<script>
$(document).ready(function(){
});
</script>

<?php

	$showForm = true;
	if (isset($_POST['saveUpdate'])){
		$data=array(
			'Start' 			=> $_REQUEST['Start'],
			'End' 				=> $_REQUEST['End'],
			'Name' 				=> $_REQUEST['Name'],
			'Locked' 			=> ((isset($_REQUEST['Locked'])) ? 1 : 0),
			'Comment_Object_ID'	=> $_REQUEST['Comment_Object_ID']
		);

		if (empty($_REQUEST['id']))	{
			$button_name = 'Add';
			$whereClause = '';
			// releases dont have a single project so use 0
			$data['Points_Object_ID'] = NextPointsObject(0);
			$result=$DBConn->create('release_details',$data);
			auditit(0,$_REQUEST['AID'],$_SESSION['Email'],'Created release',$_REQUEST['id'],$_REQUEST['Name'].' ('.$_REQUEST['Start'].' to '.$_REQUEST['End']);
		}else{
			$button_name = 'Save';
			$whereClause = 'ID = '.($_REQUEST['id'] + 0);
			$result=$DBConn->update('release_details',$data,$whereClause);
			auditit(0,$_REQUEST['AID'],$_SESSION['Email'],'Updated release',$_REQUEST['id'],$_REQUEST['Name'].' ('.$_REQUEST['Start'].' to '.$_REQUEST['End']);
		}
		unset($data);
		if ($result>0)
		{
			$showForm = false;
			header('Location:releaseDetails_List.php');
		}else{
			if($DBConn->error){
				$error = 'The form failed to process correctly.'.'<br>'.$DBConn->error;
			}else{
				$showForm = false;
			}
		}
	}

	if (!empty($error))	echo '<div class="error">'.$error.'</div>';

	if ($showForm){
		if (!empty($_REQUEST['id'])){
			$releaseDetails_Row = fetchusingID('*',$_REQUEST['id'],release_details);
		}else{
			$releaseDetails_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>
		<td>Release Name:</td>
		<td colspan=3>
			<input type="text" name="Name" size=40 value="<?=$releaseDetails_Row['Name'];?>">
		</td>
	<tr>
		<td>Start Date:</td>
		<td>
			<input type="text" class="date" name="Start" value="<?=$releaseDetails_Row['Start'];?>">
		</td>
		<td>Release/End Date:</td>
		<td>
			<input type="text" class="date" name="End" value="<?=$releaseDetails_Row['End'];?>">
		</td>
	</tr>
	<tr>
		<td>Lock Release:</td>
		<td>
			<input <?=$releaseDetails_Row['Locked'] == 1 ? 'checked' : '';?> value="1" title="This will lock the release contents." type="checkbox" name=" Locked">
		</td>
	</tr>
	<tr>
		<td>
			<input type="hidden" name="Points_Object_ID" value="<?=$releaseDetails_Row['Points_Object_ID'];?>">
		</td>
		<td>
			<input type="hidden" name="Comment_Object_ID" value="<?=$releaseDetails_Row['Comment_Object_ID'];?>">
		</td>
	</tr>
	<tr>
			<td colspan="2">
				<input type="hidden" name="id" value="<?=$_REQUEST['id'];?>">
				<input class="btn" type="submit" name="saveUpdate" value="Update">
			</td>
	</tr>
	</form>
</table>

<?php
	}
	include 'include/footer.inc.php';
?>