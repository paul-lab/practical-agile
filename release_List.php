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
echo '<div class="hidden" id="phpbread">';
echo 'Releases';
echo '</div>';
?>
<script>
$(function() {
	document.title = 'Practical Agile: '+$("#phpbread").text();
	$("#breadcrumbs").html($("#phpbread").html());
	if ($("#phpnavicons")){
		$("#navicons").html($("#phpnavicons").html());
	}
});
</script>

<?php

	echo
		'<div align="center"><p>'.
			'<a class = "btnlink" href="releaseDetails_Edit.php">Add a new Release</a>'.
		'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr>'.
				'<td>&nbsp;</td>'.
				'<td>Start Date</td>'.
				'<td>End/Delivery Date</td>'.
				'<td>Name</td>'.
				'<td>&nbsp;</td>'.
				'<td>&nbsp;</td>'.
			'</tr>';

	$releaseDetails_Res =$DBConn->directsql('SELECT * FROM release_details order by End');
	foreach ($releaseDetails_Res as $releaseDetails_Row)		{
		$Toggle = ($Toggle + 1) % 2;
		echo
			'<tr valign="top" class="alternate'.$Toggle.'">'.
				'<td><a title="Edit Release details" href="releaseDetails_Edit.php?id='.$releaseDetails_Row['ID'].'"><img src="images/edit.png"></a></td>'.
				'<td>'.$releaseDetails_Row['Start'].'</td>'.
				'<td>'.$releaseDetails_Row['End'].'</td>'.
				'<td>'.$releaseDetails_Row['Name'];
		if ($releaseDetails_Row['Locked']==1){
				echo '<br><b>Locked</b>';
		}
		echo '</td>';
		echo	'<td><a title="View and Edit release contents." href="story_List.php?RID='.$releaseDetails_Row['ID'].'&Type=tree&Root=release"><img src="images/eye-edit.png"></a>'.'</td>';
				// count the # of stories in this release
				$tsql = 'SELECT count(*) as relcount, sum(Size) as relsize FROM story where story.Release_ID='.$releaseDetails_Row['ID'];
				$trow =$DBConn->directsql($tsql);
				if ($trow[0]['relcount'] == 0){
					echo	'<td><a title="Delete release" href="releaseDetails_Delete.php?id='.$releaseDetails_Row['ID'].'"><img src="images/delete.png"></a>'.'</td>';
				}else{
					echo	'<td>'.$t_row['relcount'].' cards and '.$t_row['relsize'].' points</td>';
				}
		echo	'</tr>';
	}
	echo '</table>';

	include 'include/footer.inc.php';
?>