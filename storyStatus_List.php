<?php
	include 'include/header.inc.php';


	if (empty($_REQUEST['PID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo 'Story Status';
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
		'<div align="center">&nbsp;</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr>'.
				'<td>&nbsp;</td>'.
				'<td>Order</td>'.
				'<td>RGB</td>'.
				'<td>Status</td>'.
				'<td>Policy</td>'.
			'</tr>';
	$sql = 'SELECT * FROM story_status where story_status.Project_ID='.$_REQUEST['PID'].' order by story_status.`Order`';
	$storyStatus_Res = $DBConn->directsql($sql);
	if (count($storyStatus_Res) > 0){
		foreach($storyStatus_Res as $storyStatus_Row)		{
			echo
				'<tr>'.
					'<td>'.
						'<a href="storyStatus_Edit.php?id='.$storyStatus_Row['ID'].'&PID='.$_REQUEST['PID'].'"><img src="images/edit.png"></a> &nbsp;'.
					'</td>'.
					'<td>'.$storyStatus_Row['Order'].'</td>'.
					'<td>'.$storyStatus_Row['RGB'].'</td>'.
					'<td bgcolor="#'.$storyStatus_Row['RGB'].'">'.$storyStatus_Row['Desc'].'</td>'.
					'<td>'.$storyStatus_Row['Policy'].'</td>'.
				'</tr>';
		}
	}
	echo '</table>';

	include 'include/footer.inc.php';

?>