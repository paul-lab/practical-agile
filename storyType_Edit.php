<?php
	include 'include/header.inc.php';


	if (empty($_REQUEST['PID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo 'Story Type';
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
		 if (mysqli_query($DBConn, "{$sql_method} story_type SET 			story_type.Project_ID = '".$_REQUEST['PID']."',
			story_type.Desc = '".$_REQUEST['Desc']."',
			story_type.Order = '".$_REQUEST['Order']."' {$whereClause}"))
		{
			$showForm = false;
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'','Update Story Type',$_REQUEST['id'].'-'.$_REQUEST['Desc'].'-'.$_REQUEST['Order']);
		}
		else
		{
			$error = 'The form failed to process correctly.'.mysqli_error($DBConn);
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if (!empty($_REQUEST['id']))
		{
			$storyType_Res = mysqli_query($DBConn, 'SELECT * FROM story_type WHERE ID = '.$_REQUEST['id']);
			$storyType_Row = mysqli_fetch_assoc($storyType_Res);
		}
		else
		{
			$storyType_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>

	<tr>
		<td>Desc:</td>
		<td>
			<input type="text" name="Desc" value="<?=$storyType_Row['Desc'];?>">
		</td>
	</tr>
	<tr>
		<td>Order:</td>
		<td>
			<input type="text" name="Order" value="<?=$storyType_Row['Order'];?>">
		</td>
	</tr>


		<tr>
			<td colspan="2">
				<input type="hidden" name="id" value="<?=$_REQUEST['id'];?>">
				<input type="hidden" name="PID" value="<?=$_REQUEST['PID'];?>">
				<input type="submit" name="saveUpdate" value="Update">
			</td>
		</tr>
	</form>
</table>

<?php
	}
	else
	{
		header('Location:storyType_List.php?PID='.$_REQUEST['PID']);
	}

	include 'include/footer.inc.php';

?>
