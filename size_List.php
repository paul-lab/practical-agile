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
	$sql='select * from size_type where ID= ?';
	$result = $DBConn->directsql($sql, $current);
	return $result[0]['Desc'];
}


	echo
		'<div align="center"><p>'.
			'<a class="btnlink" href="size_Edit.php">Add a new size</a>'.
		'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr><b>'.
				'<td>&nbsp; </td>'.
				'<td>Type</td>'.
				'<td>Value</td>'.
				'<td>Order</td>'.
				'<td>&nbsp;</td>'.
			'</b></tr>';

	$size_Row = $DBConn->directsql('SELECT * FROM size order by Type, size.`Order`');
	$Toggle=0;
	if (count($size_Row) > 0)	{
		$rowcount=0;
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo
				'<tr valign="top" class="alternate'.$Toggle.'">'.
					'<td>'.'<a href="size_Edit.php?id='.$size_Row[$rowcount]['ID'].'"><img src="images/edit.png"></a> &nbsp;'.'</td>'.
					'<td>'.print_Size_Type($size_Row[$rowcount]['Type']).'</td>'.
					'<td>'.$size_Row[$rowcount]['Value'].'</td>'.
					'<td>'.$size_Row[$rowcount]['Order'].'</td>'.
					'<td>'.
						'<a href="size_Delete.php?id='.$size_Row[$rowcount]['ID'].'"><img src="images/delete.png"></a>'.
					'</td>'.
				'</tr>';
				$rowcount+=1;
		}
		while ($rowcount < count($size_Row));
	}
	echo '</table>';

	include 'include/footer.inc.php';

?>
