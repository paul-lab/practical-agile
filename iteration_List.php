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
		if ($isProjectAdmin ) echo '<br><a class="btnlink" href="iteration_Edit.php?PID='.$_REQUEST['PID'].'">add a new iteration</a>';
	echo	'</div>'.
		'<table align="center" cellpadding="6" cellspacing="0">'.
			'<tr>'.
				'<td>&nbsp;</td>'.
				'<td>Name</td>'.
				'<td>Objective</td>'.
				'<td>&nbsp;</td>'.
				'<td>&nbsp;</td>'.
			'</tr>';
	$sql = 'select *, (select count(*) from story where Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID = iteration.id) as nums FROM iteration where iteration.Project_ID='.$_REQUEST['PID'].' order by iteration.End_Date desc';
	$iteration_Row = $DBConn->directsql($sql);
	$Toggle=1;
	if (count($iteration_Row) > 0 )	{
		$rowcnt=0;
		do
		{
			$Toggle = ($Toggle + 1) % 2;
			echo
				'<tr valign="center" class="alternate'.$Toggle.'">';
					if ($iteration_Row[$rowcnt]['ID']!=$Project['Backlog_ID'])
					{
						if ($isProjectAdmin) {
							echo '<td>'.'<a href="iteration_Edit.php?IID='.$iteration_Row[$rowcnt]['ID'].'&PID='.$_REQUEST['PID'].'"><img src="images/edit.png"></a> &nbsp;'.'</td>';
						} else {
							echo '<td>&nbsp;</td>';
						}
					}else{
						echo '<td>&nbsp;</td>';
					}
			echo		'<td>'.'<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[$rowcnt]['ID'].'"'.
					' title="'.$iteration_Row[$rowcnt]['Start_Date'].' -> '.$iteration_Row[$rowcnt]['End_Date'].'">'.
					substr($iteration_Row[$rowcnt]['Name'], 0, 32).'</a>';
					if ($iteration_Row[$rowcnt]['ID']!=$Project['Backlog_ID'])
					{
						echo '<br><center>'.$iteration_Row[$rowcnt]['Start_Date'].
						'<br>to<br> '.$iteration_Row[$rowcnt]['End_Date'];
					}else{
						echo '<div class="evenlarger"><center><b>Velocity<br>'.$Project['Velocity'].'</b></center></div>';
					}
			if ($iteration_Row[$rowcnt]['Locked']==1)	{
				echo '<p><b>Locked</b>';
			}
			echo '</center></td>'.
					'<td>'.substr($iteration_Row[$rowcnt]['Objective'], 0, 64).'</td>'.
					'<td>'.
					'<table><tr><td>';
					print_summary($iteration_Row[$rowcnt]['Points_Object_ID'], False); // without velocity
					echo '</td><td>&nbsp;';
 					print_Graphx($iteration_Row[$rowcnt]['Points_Object_ID'], True); // Not Small
					echo '</td></tr></table></td>'.
					'<td>';
					if ($iteration_Row[$rowcnt]['Name']!='Backlog' )					{
						if ($isProjectAdmin and $iteration_Row[$rowcnt]['nums']==0) {
							echo '<a href="iteration_Delete.php?IID='.$iteration_Row[$rowcnt]['ID'].'&PID='.$_REQUEST['PID'].'&POID='.$iteration_Row[$rowcnt]['Points_Object_ID'].'&COID='.$iteration_Row[$rowcnt]['Comment_Object_ID'].'"><img src="images/delete.png"></a>';
						}else{
							echo '&nbsp;';
						}
					}
			echo 	'&nbsp;</td>'.
				'</tr>';
			$rowcnt += 1;
		}
		while ($rowcnt < count($iteration_Row));
	}
	echo '</table>';

	include 'include/footer.inc.php';
?>