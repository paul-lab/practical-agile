<?php
	include 'include/header.inc.php';

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo 'Report List</a>';
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

	<link rel="stylesheet" type="text/css" href="css/overrides.css" />

<?php

	echo
	'<div class="hidden" id="phpnavicons" align="Left">'.
	'</div>';

	echo
		'<div align="center">'.
			'<a href="report_Edit.php">add a new Report</a>'.
		'</div>'.
		'<p><center>Reports Should act on the currently selected  Project or if appropriate Currently selected Iteration.</center><p>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr><b>'.
				'<td>&nbsp;</td>'.
				'<td>&nbsp;</td>'.
				'<td>Desc</td>'.
				'<td>&nbsp;</td>'.
			'</b></tr>';

	$sql = 'select * from queries where ID >0 order by Qseq';
	$Qry_Res = mysqli_query($DBConn, $sql);
	$Toggle=0;
	if ($Qry_Row = mysqli_fetch_assoc($Qry_Res))
	{
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo	'<tr valign="top" class="alternate'.$Toggle.'">'.
				'<td>';
				if ($Usr['Admin_User']==1 ){
					echo '<a href="report_Edit.php?ID='.$Qry_Row['ID'].'"><img src="images/edit.png"></a>';
				}
				echo '</td><td>'.$Qry_Row['Qseq'].'</td>'.
					'<td>'.$Qry_Row['Desc'].'</td>';
			echo		'<td>';
			if ($Usr['Admin_User']==1 ){
				echo '<a href="report_Delete.php?ID='.$Qry_Row['ID'].'&desc='.$Qry_Row['Desc'].'"><img src="images/delete.png"></a>';
			}
			echo		'</td></tr>';
		}
		while ($Qry_Row = mysqli_fetch_assoc($Qry_Res));
	}
	echo '</table>';
	include 'include/footer.inc.php';
?>
