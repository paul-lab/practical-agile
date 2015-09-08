<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo 'Story Size Type';
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

	echo
		'<div align="center">'.
			'<a href="sizeType_Edit.php">add a new size Type</a>'.
		'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr><b>'.
				'<td>&nbsp;</td>'.
				'<td>&nbsp;</td>'.

				'<td>Desc</td>'.

				'<td>&nbsp;</td>'.
			'</b></tr>';

	$sql = 'select st.ID, st.Desc, pr.Project_Size_ID PRID from size_type as st left join `project` pr on st.ID=pr.Project_Size_ID group by st.ID';
	$sizeType_Res = mysqli_query($DBConn, $sql);
	$Toggle=0;
	if ($sizeType_Row = mysqli_fetch_assoc($sizeType_Res))
	{
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo	'<tr valign="top" class="alternate'.$Toggle.'">'.
				'<td><a href="sizeType_Edit.php?id='.$sizeType_Row['ID'].'"><img src="images/edit.png"></a></td>'.					'<td>'.$sizeType_Row['ID'].'</td>'.
					'<td>'.$sizeType_Row['Desc'].'</td>';
			if ($sizeType_Row['PRID']==NULL){
				echo		'<td><a href="sizeType_Delete.php?id='.$sizeType_Row['ID'].'"><img src="images/delete.png"></a>';
			}
			echo		'</td></tr>';
		}
		while ($sizeType_Row = mysqli_fetch_assoc($sizeType_Res));
	}
	echo '</table>';

	include 'include/footer.inc.php';

?>
