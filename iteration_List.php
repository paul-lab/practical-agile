<?php
	include 'include/header.inc.php';

	if (empty($_REQUEST['PID'])) header("Location:project_List.php");	

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>';
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


<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="jqplot/excanvas.js"></script><![endif]-->
<script type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.highlighter.min.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
<script type="text/javascript" src="jqplot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
<link class="include" rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.min.css" />


<?php

	echo
		'<div align="center">';
		
		if ($isProjectAdmin ) echo '<a href="iteration_Edit.php?PID='.$_REQUEST['PID'].'">add a new iteration</a>';
	echo	'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr>'.
				'<td>&nbsp;</td>'.
				'<td>Name</td>'.
				'<td>Objective</td>'.
				'<td>&nbsp;</td>'.
				'<td>&nbsp;</td>'.
			'</tr>';
	$sql = 'SELECT * FROM iteration where iteration.Project_ID='.$_REQUEST['PID'].' order by iteration.End_Date desc';
	$sql = 'select *, (select count(*) from story where Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID = iteration.id) as nums FROM iteration where iteration.Project_ID='.$_REQUEST['PID'].' order by iteration.End_Date desc';
	$iteration_Res = mysqli_query($DBConn, $sql);
	$Toggle=1;
	if ($iteration_Row = mysqli_fetch_assoc($iteration_Res))
	{
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo
				'<tr valign="center" class="alternate'.$Toggle.'">';
					if ($iteration_Row['ID']!=$Project['Backlog_ID'])
					{
						if ($isProjectAdmin) {
							echo '<td>'.'<a href="iteration_Edit.php?IID='.$iteration_Row['ID'].'&PID='.$_REQUEST['PID'].'"><img src="images/edit.png"></a> &nbsp;'.'</td>';
						} else {
							echo '<td>&nbsp;</td>';
						}
					}else{
						echo '<td>&nbsp;</td>';
					}
			echo		'<td>'.'<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row['ID'].'"'.
					' title="'.$iteration_Row['Start_Date'].' -> '.$iteration_Row['End_Date'].'">'.
					substr($iteration_Row['Name'], 0, 32).'</a>';
					if ($iteration_Row['ID']!=$Project['Backlog_ID'])
					{
						echo '<br><center>'.$iteration_Row['Start_Date'].
						'<br>to<br> '.$iteration_Row['End_Date'];
					}else{
						echo '<div class="evenlarger"><center><b>Velocity<br>'.$Project['Velocity'].'</b></center></div>';
					}
			if ($iteration_Row['Locked']==1)
			{
				echo '<p><b>Locked</b>';
			}
			echo '</center></td>'.
					'<td>'.substr($iteration_Row['Objective'], 0, 64).'</td>'.
					'<td>'.
					'<table><tr><td>';
					print_summary($iteration_Row['Points_Object_ID'], False); // without velocity
					echo '</td><td>&nbsp;';
 					print_Graphx($iteration_Row['Points_Object_ID'], True); // Not Small
					echo '</td></tr></table></td>'.
					'<td>';
					if ($iteration_Row['Name']!='Backlog' )
					{
						if ($isProjectAdmin and $iteration_Row['nums']==0) {
							echo '<a href="iteration_Delete.php?IID='.$iteration_Row['ID'].'&PID='.$_REQUEST['PID'].'&OID='.$iteration_Row['Object_ID'].'"><img src="images/delete.png"></a>';
						}else{
							echo '&nbsp;';
						}
					}
			echo 	'&nbsp;</td>'.
				'</tr>';
		}
		while ($iteration_Row = mysqli_fetch_assoc($iteration_Res));
	}
	echo '</table>';

	include 'include/footer.inc.php';
?>