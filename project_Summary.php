<?php
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
<link class="include" rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.min.css" />



<?php

function print_Size_Type_Dropdown($current)
{
	Global $DBConn;

	$sql = 'select * from size_type where ID='.$current;
    	$queried = mysqli_query($DBConn, $sql );
	$result = mysqli_fetch_assoc($queried);
	return $result['Desc'];
}

	$showForm = true;

	if ($showForm)
	{
		if (!empty($_REQUEST['PID']))
		{
			$project_Res = mysqli_query($DBConn, 'SELECT * FROM project WHERE ID = '.$_REQUEST['PID']);
			$project_Row = mysqli_fetch_assoc($project_Res);
		}
		else
		{
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
		<td>
			<b><?=$project_Row['Name'];?></b>
		</td>
	</tr>

	<tr>
		<td>&nbsp;</td>
		<td>
			<?=$project_Row['Desc'];?>
		</td>
	</tr>
	<tr>
		<td>Use As A:</td>
		<td>
			<?=$project_Row['As_A'] == 1 ? 'Yes' : 'No';?>
		</td>
	</tr>
	<tr>
		<td>Use I Need:</td>
		<td>
			<?=$project_Row['Col_2'] == 1 ? 'Yes' : 'No';?>
		</td>
	</tr>
	<tr>
		<td>Use Acceptance Criteria:</td>
		<td>
			<?=$project_Row['Acceptance'] == 1 ? 'Yes' : 'No';?>
		</td>
	</tr>
	<tr>
		<td>Enable Story Tasks (on Scrum Board):</td>
		<td>
			<?=$project_Row['Enable_Tasks'] == 1 ? 'Yes' : 'No';?>
		</td>
	</tr>
	<tr>
		<td>Project Size Type:</td>
		<td>
			<?=print_Size_Type_Dropdown($project_Row['Project_Size_ID']+0);?>
		</td>
	</tr>

	<tr>
		<td>Archived:</td>
		<td>
			<?=$project_Row['Archived'] == 1 ? 'Yes' : 'No';?>
		</td>
	</tr>

	<tr>
		<td>Average Story Size:</td>
		<td>
			<b><?=$project_Row['Average_Size'];?></b>
		</td>
	</tr>
	<tr>
		<td>Current Velocity:</td>
		<td>
			<b><?=$project_Row['Velocity'];?></b> &nbsp; (average of most recent 5 completed iterations.)
		</td>
	</tr>

	<tr>
		<td>Current Iteration:</td>
		<td  class="larger" ><b>
<?php
		$thisdate =  Date("Y-m-d");
		$sql = 'SELECT * FROM iteration where iteration.Project_ID='.$_REQUEST['PID'].' and iteration.Name <> "Backlog" and iteration.Start_Date<="'.$thisdate.'" and iteration.End_Date>="'.$thisdate.'"';
		$iteration_Res = mysqli_query($DBConn, $sql);
		$iteration_Row = mysqli_fetch_assoc($iteration_Res);
		echo '<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row['ID'].'" title = "Current Iteration" >'.
		substr($iteration_Row['Name'], 0, 14).'</a> &nbsp; ('.$iteration_Row['Start_Date'].'->'.$iteration_Row['End_Date'].')</b> &nbsp;';
		print_summary($iteration_Row['Points_Object_ID'], False);
		echo '</td></tr>';
		echo '<tr><td>&nbsp;</td><td class="larger" ><b><a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$project_Row['Backlog_ID'].'" title = "Backlog" >Backlog</a></b></td></tr>';


		$sql = 'SELECT * FROM iteration  where iteration.Project_ID='.$_REQUEST['PID'].' and iteration.Name <> "Backlog"  order by iteration.End_Date desc';
		$iteration_Res = mysqli_query($DBConn, $sql);
		if ($iteration_Row = mysqli_fetch_assoc($iteration_Res))
		{
			do {
				echo '<tr><td>&nbsp;</td><td><a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row['ID'].
					'" title = "'.$iteration_Row['Name'].'" >'.$iteration_Row['Name'].'</a>'.
					' &nbsp; ('.$iteration_Row['Start_Date'].'->'.$iteration_Row['End_Date'].') ';
if ($iteration_Row['Locked']==1)
{
echo '<br><b>Locked</b>';
}

				print_summary($iteration_Row['Points_Object_ID'], False);
				echo '</td></tr>';
			} while ($iteration_Row = mysqli_fetch_assoc($iteration_Res));
		}
?>
		</td>
	</tr>
	

</table>
<?php
	}
	else
	{
		header("Location:project_List.php");
	}
	include 'include/footer.inc.php';

?>
