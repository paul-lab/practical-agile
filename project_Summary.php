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
echo Get_Project_Name($_REQUEST['PID']);
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

<script type="text/javascript" src="scripts/micromenu-hash0dc02c21be13adc33614481961b31b0c.js"></script>

<link class="include" rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.min.css" />

<?php

function print_Size_Type_Dropdown($current)
{
	Global $DBConn;

	$sql = 'select * from size_type where ID='.$current;
	$result = $DBConn->directsql($sql);
	return $result[0]['Desc'];
}
	$showForm = true;

	if ($showForm)	{
		if (!empty($_REQUEST['PID']))	{
			$project_Res = $DBConn->directsql('SELECT * FROM project WHERE ID = '.$_REQUEST['PID']);
			$project_Row = $project_Res[0];
		}else{
			$project_Row = $_REQUEST;
		}
		echo '<table align="center" width=90%><tr><td align="center">';
		print_summary($project_Row['Points_Object_ID'],True); // with velocity
		echo '</td></tr><tr><td align="center">';
		print_Graphx($project_Row['Points_Object_ID'], False); // Not Small
		echo '</td></tr></table>';
	echo
	'<div class="hidden" id="phpnavicons" align="Left">'.
		'<a title="Add new story to backlog" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$project_Row['Backlog_ID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;'.
		'&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$project_Row['Backlog_ID'].'"><img src="images/tree-large.png"></a>'.
		'&nbsp; &nbsp;<a  title="Backlog List" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$project_Row['Backlog_ID'].'"><img src="images/list-large.png"></a>'.
	'</div>';
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">';
?>
		<tr>
			<td>
				<br><?=$project_Row['Category'];?>
			</td>
			<td colspan=3>
				<b><?=$project_Row['Name'];?></b>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td colspan=3>
				<?=$project_Row['Desc'];?>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Use As A:</td>
			<td>
				<?=$project_Row['As_A'] == 1 ? 'Yes' : 'No';?>
			</td>
			<td align="right"><b>Use I Need:</td>
			<td>
				<?=$project_Row['Col_2'] == 1 ? 'Yes' : 'No';?>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Use Acceptance Criteria:</td>
			<td>
				<?=$project_Row['Acceptance'] == 1 ? 'Yes' : 'No';?>
			</td>
			<td align="right"><b>Enable Story Tasks (on Scrum Board):</td>
			<td>
				<?=$project_Row['Enable_Tasks'] == 1 ? 'Yes' : 'No';?>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Project Size Type:</td>
			<td>
				<?=print_Size_Type_Dropdown($project_Row['Project_Size_ID']+0);?>
			</td>

			<td align="right"><b>Archived:</td>
			<td>
				<?=$project_Row['Archived'] == 1 ? 'Yes' : 'No';?>
			</td>
		</tr>
		<tr>
			<td align="right"><b>Average Story Size:</td>
			<td>
				<b><?=$project_Row['Average_Size'];?></b>
			</td>
			<td align="right"><b>Current Velocity:</td>
			<td>
				<b><?=$project_Row['Velocity'];?></b> &nbsp; (average of <b><?=$project_Row['Vel_Iter'];?></B> most recent completed iterations.)
			</td>
		</tr>
		<tr><td align=right><b>Show recent History</td><td>
			<a class="auditpopup" id="auditp<?=$project_Row['ID'];?>" href="" onclick="javascript: return false;" title="Show Recent history (200 records)"><img src="images/history-small.png"></a> &nbsp;
		</tr>
	</table>
	<div class="auditdialog hidden" id="allaudits_<?=$project_Row['ID'];?>"></div>
	<table align="center" cellpadding="6" cellspacing="0" border="0">
		<tr>
			<td>&nbsp;</td>
			<td  class="larger" ><b>
<?php
			$thisdate =  Date("Y-m-d");
// get the current iteration
			$sql = 'SELECT * FROM iteration where iteration.Project_ID='.$_REQUEST['PID'].' and iteration.Name <> "Backlog" and iteration.Start_Date<="'.$thisdate.'" and iteration.End_Date>="'.$thisdate.'"';
			$iteration_Row = $DBConn->directsql($sql);
			if ($iteration_Row){
				$iteration_Row = $iteration_Row[0];
				echo '<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row['ID'].'" title = "Current Iteration" >'.
				substr($iteration_Row['Name'], 0, 14).'</a> &nbsp; ('.$iteration_Row['Start_Date'].'->'.$iteration_Row['End_Date'].')</b> &nbsp;';
				print_summary($iteration_Row['Points_Object_ID'], False);
			}else{
				echo '&nbsp;';
			}
			echo '</td>';
			echo '<td>&nbsp;</td><td class="larger" ><b><a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$project_Row['Backlog_ID'].'" title = "Backlog" >Backlog</a></b></td></tr>';
			$left=1;
			$sql = 'SELECT * FROM iteration  where iteration.Project_ID='.$_REQUEST['PID'].' and iteration.Name <> "Backlog"  order by iteration.End_Date DESC';
			$iteration_Row = $DBConn->directsql($sql);
			if ($iteration_Row)	{
				echo '<tr>';
				$rowcnt=0;
				do {
					$left+=1;
					echo '<td>&nbsp;</td><td><a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[$rowcnt]['ID'].
						'" title = "'.$iteration_Row[$rowcnt]['Name'].'" >'.$iteration_Row[$rowcnt]['Name'].'</a>'.
						' &nbsp; ('.$iteration_Row[$rowcnt]['Start_Date'].'--->'.$iteration_Row[$rowcnt]['End_Date'].') ';
					if ($iteration_Row[$rowcnt]['Locked']==1)	{
						echo '<br><b>Locked</b>';
					}
					print_summary($iteration_Row[$rowcnt]['Points_Object_ID'], False);
					echo '</td>';
					if($left % 2==1){
						echo '</tr><tr>';
					}
					$rowcnt +=1;
				} while ($rowcnt < count($iteration_Row));
				echo '</tr>';
			}
?>
			</td>
		</tr>
	</table>
<?php
	}else{
		header("Location:project_List.php");
	}
	include 'include/footer.inc.php';

?>
