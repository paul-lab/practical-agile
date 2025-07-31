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
	$sql = 'select * from size_type where size_type.ID= ?';
	$result=$DBConn->directsql($sql, $current);

	$menu = '<select name="Type">';
	$menu .= '<option value="' . $current . '">' . $result[0]['Desc'] . '</option>';
	$sql = 'select * from size_type where size_type.ID<> ?';
	$Row=$DBConn->directsql($sql, $current);
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
			$whereClause = 'ID = ?';
			$result=$DBConn->update('size',$data,$whereClause, $_REQUEST['id']);
		}
		 if ($result>0)
		{
			$showForm = false;
		}else{
			if($DBConn->error){
				$error = 'The form failed to process correctly.'.'<br>'.$DBConn->error;
			}else{
				$showForm = false;
			}
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if (!empty($_REQUEST['id']))
		{
			$size_Row = $DBConn->directsql( 'SELECT * FROM size WHERE ID = ?', $_REQUEST['id']);
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
				<input class="btn" type="submit" name="saveUpdate" value="Update">
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