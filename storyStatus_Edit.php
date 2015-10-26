<?php
	include 'include/header.inc.php';

	if (empty($_REQUEST['PID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo 'Story Status';
echo '</div>';
?>
<script>
$(function() {
	document.title = 'Practical Agile: '+$("#phpbread").text().substring(13);
	$("#breadcrumbs").html($("#phpbread").html());
	if ($("#phpnavicons")){
		$("#navicons").html($("#phpnavicons").html());
	}
});
</script>
<?php
	$showForm = true;
	if (isset($_POST['saveUpdate']))
	{

		if (empty($_REQUEST['id']))
		{
			$sql_method = 'INSERT INTO';
			$button_name = 'Add';
			$whereClause = '';
		}
		else
		{
			$sql_method = 'UPDATE';
			$button_name = 'Save';
			$whereClause = 'WHERE ID = '.($_REQUEST['id'] + 0);
		}
		 if (mysqli_query($DBConn, "{$sql_method} story_status SET
 			Project_ID = '".$_REQUEST['PID']."',
			story_status.Desc = '".$_REQUEST['Desc']."',
			story_status.Policy = '".$_REQUEST['Policy']."',
			story_status.Order = '".$_REQUEST['Order']."',
			RGB = '".$_REQUEST['RGB']."' {$whereClause}"))
		{
			$sql='Update story set story.Status="'.$_REQUEST['Desc'].'" where story.Project_ID='.$_REQUEST['PID'].' and story.Status="'.$_REQUEST['ODesc'].'"';
			mysqli_query($DBConn, $sql);
			$sql='Update points_log set points_log.Status="'.$_REQUEST['Desc'].'" where points_log.Project_ID='.$_REQUEST['PID'].' and points_log.Status="'.$_REQUEST['ODesc'].'"';
			mysqli_query($DBConn, $sql);
			$showForm = false;
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Update Project Story Status','',$_REQUEST['Desc'].'-'.$_REQUEST['Policy'].'-'.$_REQUEST['RGB']);
		}
		else
		{
			$error = 'The form failed to process correctly.'.mysqli_error();
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if (!empty($_REQUEST['id']))
		{
			$storyStatus_Res = mysqli_query($DBConn, 'SELECT * FROM story_status WHERE ID = '.$_REQUEST['id']);
			$storyStatus_Row = mysqli_fetch_assoc($storyStatus_Res);
		}
		else
		{
			$storyStatus_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>
	<tr>
		<td>Order:</td>
		<td>
			<?=$storyStatus_Row['Order'];?>
		</td>
	</tr>
	<tr>
		<td>RGB:</td>
		<td>
			<input type="text" name="RGB" value="<?=$storyStatus_Row['RGB'];?>">
		</td>
	</tr>

	<tr>
		<td>Desc:</td>
		<td>
<?php
	if($storyStatus_Row['Order']==1 or $storyStatus_Row['Order']==10){
		echo $storyStatus_Row['Desc'];
	}else{
		echo '<input type="text" name="Desc" value="'.$storyStatus_Row['Desc'].'">';
	}
?>
		</td>
	</tr>
	<tr>
		<td>Policy:</td>
		<td>
			<input type="text" size=75 name="Policy" value="<?=$storyStatus_Row['Policy'];?>">
		</td>
	</tr>




	<tr>
			<td colspan="2">
				<input type="hidden" name="id" value="<?=$_REQUEST['id'];?>">
				<input type="hidden" name="PID" value="<?=$_REQUEST['PID'];?>">
				<input type="hidden" name="ODesc" value="<?=$storyStatus_Row['Desc'];?>">
				<input type="hidden" name="Order" value="<?=$storyStatus_Row['Order'];?>">
<?php
	if($storyStatus_Row['Order']==1 or $storyStatus_Row['Order']==10){
		echo '<input type="hidden" name="Desc" value="'.$storyStatus_Row['Desc'].'">';
	}
?>
				<input type="submit" name="saveUpdate" value="Update">

			</td>
		</tr>
	</form>
</table>
<?php
	}
	else
	{
		header('Location:storyStatus_List.php?PID='.$_REQUEST['PID']);
	}

	include 'include/footer.inc.php';
?>