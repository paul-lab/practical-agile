<?php
	include 'include/header.inc.php';

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="sizetype_List.php">Size Type List</a>->';
echo 'Size Type Edit';
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
		 if (mysqli_query($DBConn, "{$sql_method} size_type SET 			size_type.Desc = '".$_REQUEST['Desc']."' {$whereClause}"))
		{
			$showForm = false;
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
			$sizeType_Res = mysqli_query($DBConn, 'SELECT * FROM size_type WHERE ID = '.$_REQUEST['id']);
			$sizeType_Row = mysqli_fetch_assoc($sizeType_Res);
		}
		else
		{
			$sizeType_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>

	<tr>
		<td>Desc:</td>

		<td>

			<input type="text" name="Desc" value="<?=$sizeType_Row['Desc'];?>">

		</td>

	</tr>


		<tr>
			<td colspan="2">
				<input type="hidden" name="id" value="<?=$_REQUEST['id'];?>">
				<input type="submit" name="saveUpdate" value="Update">
			</td>
		</tr>
	</form>
</table>
<?php
	}
	else
	{
		header("Location:sizeType_List.php");
	}

	include 'include/footer.inc.php';

?>
