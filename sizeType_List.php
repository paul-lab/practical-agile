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
		'<div align="center"><p>'.
			'<a class="btnlink" href="sizeType_Edit.php">Add a new size Type</a>'.
		'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr><b>'.
				'<td>&nbsp;</td>'.
				'<td>&nbsp;</td>'.

				'<td>Desc</td>'.

				'<td>&nbsp;</td>'.
			'</b></tr>';

	$sql = 'select st.ID, st.Desc, pr.Project_Size_ID PRID , si.Type from size_type as st left join `project` pr on st.ID=pr.Project_Size_ID left join `size` si on st.ID=si.Type
group by st.ID';
	$sizeType_Row = $DBConn->directsql($sql);
	$Toggle=0;
	if (count($sizeType_Row) > 0)	{
	$rowcnt = 0;
		do		{
			$Toggle = ($Toggle + 1) % 2;
			echo	'<tr valign="top" class="alternate'.$Toggle.'">'.
					'<td><a href="sizeType_Edit.php?id='.$sizeType_Row[$rowcnt]['ID'].'"><img src="images/edit.png"></a></td>'.
					'<td>'.$sizeType_Row[$rowcnt]['ID'].'</td>'.
					'<td>'.$sizeType_Row[$rowcnt]['Desc'].'</td>';
			if ($sizeType_Row[$rowcnt]['PRID']==NULL && $sizeType_Row[$rowcnt]['Type']==NULL){
				echo		'<td><a href="sizeType_Delete.php?id='.$sizeType_Row[$rowcnt]['ID'].'"><img src="images/delete.png"></a>';
			}
			echo		'</td></tr>';
			$rowcnt+=1;
		}
		while ($rowcnt < count($sizeType_Row));
	}
	echo '</table>';

	include 'include/footer.inc.php';

?>
