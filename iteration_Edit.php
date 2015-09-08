<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo Get_Iteration_Name($_REQUEST['IID']);
echo '</div>';
?>
<script>
$(function() {
	document.title = 'Practical Agile: '+$("#phpbread").text().substring(13);
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
	if (isset($_POST['saveUpdate'])) 
	{
		
		if (empty($_REQUEST['IID']))
		{
			$sql_method = 'INSERT INTO';
			$button_name = 'Add';
			$whereClause = '';
			$Insertsql = ', Points_Object_ID = '.NextPointsObject().',Comment_Object_ID= '.NextIterationCommentObject().' ';
		}
		else
		{
			$sql_method = 'UPDATE';
			$button_name = 'Save';
			$whereClause = 'WHERE ID = '.($_REQUEST['IID'] + 0);
		}
		 if (mysqli_query($DBConn, "{$sql_method} iteration SET
			Project_ID = '".$_REQUEST['PID']."',
			Locked = '".$_REQUEST['Locked']."',
			Name = '".htmlentities($_REQUEST['Name'],ENT_QUOTES)."',
			Objective = '".mysqli_real_escape_string($DBConn, $_REQUEST['Objective'])."',
			Start_Date = '".$_REQUEST['Start_Date']."',
			End_Date = '".$_REQUEST['End_Date']."' {$Insertsql} {$whereClause}"))
		{
			$showForm = false;
			header('Location:iteration_List.php?PID='.$_REQUEST['PID']);
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
		if (!empty($_REQUEST['IID']))
		{
			$iteration_Res = mysqli_query($DBConn, 'SELECT * FROM iteration WHERE ID = '.$_REQUEST['IID']);
			$iteration_Row = mysqli_fetch_assoc($iteration_Res);
		}
		else
		{
			$iteration_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>
	<tr>
		<td>Name:</td>
		<td>
			<input type="text" name="Name" value="<?=$iteration_Row['Name'];?>">
		</td>
	</tr>
	<tr>
		<td>Objective:</td>
		<td>
			<textarea rows="4" cols="50" name="Objective"><?=$iteration_Row['Objective'];?></textarea>
		</td>
	</tr>
	<tr>
		<td>Start Date:</td>
		<td>
			<input type="text" class="date" name="Start_Date" value="<?=$iteration_Row['Start_Date'];?>">

		</td>
	</tr>
	<tr>
		<td>End Date:</td>
		<td>
			<input type="text" class="date" name="End_Date" value="<?=$iteration_Row['End_Date'];?>">
		</td>
	</tr>
	<tr>
		<td>Lock Iteration:</td>
		<td>
			<input <?=$iteration_Row['Locked'] == 1 ? 'checked' : '';?> value="1" title="This will lock the iteration contents." type="checkbox" name=" Locked">
		</td>
	</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="IID" value="<?=$_REQUEST['IID'];?>">
				<input type="hidden" name="PID" value="<?=$_REQUEST['PID'];?>">
<?php
if(!$isReadonly)
{
				echo '<input type="submit" name="saveUpdate" value="Update">';
}
?>
				
			</td>
		</tr>
	</form>
</table>
<?php
	}
	include 'include/footer.inc.php';
?>
