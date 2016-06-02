<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="size_List.php">Size List</a>->';
echo 'Size Edit';
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

function print_Story_Size_Type_Dropdown($current)
{
	Global $DBConn;

	$current=$current+0;
	$sql = 'select * from size_type where size_type.ID='.$current;
	$result=$DBConn->directsql($sql);

	$menu = '<select name="Type">';
	$menu .= '<option value="' . $current . '">' . $result[0]['Desc'] . '</option>';
	$sql = 'select * from size_type where size_type.ID<>'.$current;
	$Row=$DBConn->directsql($sql);
	foreach ($Row as $result) {
		$menu .= '<option value="' . $result['ID'] . '">' . $result['Desc'] .'</option>';
	}
	$menu .= '</select>';
	return $menu;
}

	$showForm = true;
	if (isset($_POST['saveUpdate']))
	{
		$data=array(
			'Type'	=> $_REQUEST['Type'],
			'Value' => $_REQUEST['Value'],
			'Order' 	=> $_REQUEST['Order']
		);
		if (empty($_REQUEST['id']))
		{
			$button_name = 'Add';
			$result=$DBConn->create('size',$data);
		}
		else
		{
			$button_name = 'Save';
			$whereClause = 'ID = '.($_REQUEST['id'] + 0);
			$result=$DBConn->update('size',$data,$whereClause);
		}
		 if ($result>0)
		{
			$showForm = false;
		}
		else
		{
			$error = 'The form failed to process correctly.';
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if (!empty($_REQUEST['id']))
		{
			$size_Row = $DBConn->directsql( 'SELECT * FROM size WHERE ID = '.$_REQUEST['id']);
			$size_Row = $size_Row[0];
		}
		else
		{
			$size_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>

	<tr>
		<td>Type:</td>
		<td>
<?=print_Story_Size_Type_Dropdown($size_Row['Type']);?>

		</td>
	</tr>
	<tr>
		<td>Value:</td>
		<td>
			<input type="text" name="Value" value="<?=$size_Row['Value'];?>">
		</td>
	</tr>
	<tr>
		<td>Order:</td>
		<td>
			<input type="text" name="Order" value="<?=$size_Row['Order'];?>">
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
		header('Location:size_List.php');
	}

	include 'include/footer.inc.php';

?>