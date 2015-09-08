<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo 'Story Size';
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

function print_Size_Type($current)
{
	Global $DBConn;

    	$sql='select * from size_type where ID='.$current;
	$queried = mysqli_query($DBConn, $sql);
	$result = mysqli_fetch_assoc($queried);
	return $result['Desc'];
}

	
	echo
		'<div align="center">'.
			'<a href="size_Edit.php">add a new size</a>'.
		'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr><b>'.
				'<td>&nbsp; </td>'.
				'<td>Type</td>'.
				'<td>Value</td>'.
				'<td>Order</td>'.
				'<td>&nbsp;</td>'.
			'</b></tr>';

	$size_Res = mysqli_query($DBConn, 'SELECT * FROM size order by Type, size.Order');
	$Toggle=0;
	if ($size_Row = mysqli_fetch_assoc($size_Res))
	{
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo
				'<tr valign="top" class="alternate'.$Toggle.'">'.
					'<td>'.'<a href="size_Edit.php?id='.$size_Row['ID'].'"><img src="images/edit.png"></a> &nbsp;'.'</td>'.
					'<td>'.print_Size_Type($size_Row['Type']).'</td>'.
					'<td>'.$size_Row['Value'].'</td>'.
					'<td>'.$size_Row['Order'].'</td>'.
					'<td>'.
						'<a href="size_Delete.php?id='.$size_Row['ID'].'"><img src="images/delete.png"></a>'.
					'</td>'.
				'</tr>';
		}
		while ($size_Row = mysqli_fetch_assoc($size_Res));
	}
	echo '</table>';

	include 'include/footer.inc.php';

?>
