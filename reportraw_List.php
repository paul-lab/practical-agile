<?php
	include 'include/header.inc.php';
if (empty($_REQUEST['PID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>-><b>';
echo Get_Iteration_Name($_REQUEST['IID']);
echo '</b></div>';

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
		'<a title="Add new story" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;'.
		'&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/tree-large.png"></a>'.
		'&nbsp; &nbsp;<a  title="Scrum Board" href="story_List.php?Type=board&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/board-large.png"></a>'.
		'&nbsp; &nbsp;<a  title="Story List" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/list-large.png"></a>'.
	'</div>';

// Make a simple SELECT query

	$qsql = 'SELECT QSQL, Qorder, queries.Desc FROM queries where ID='.$_REQUEST['QID'];
	$QRes = mysqli_query($DBConn, $qsql);
	$QRow = mysqli_fetch_assoc($QRes);
	$cond=" ".$QRow['QSQL'];	
	$cond= str_replace('{User}', $_SESSION['ID'], $cond);
	$cond= str_replace('{Iteration}', $_REQUEST['IID'], $cond);
	$cond= str_replace('{Project}', $_REQUEST['PID'], $cond);
	$cond= str_replace('{Backlog}', $Project['Backlog_ID'], $cond);

	$q =$sel.$cond.' '.$QRow['Qorder'];
echo $q;
// do we have any results
if (	$r = mysqli_query($DBConn, $q)) {  

	// send to the screen
	if ($_REQUEST['Type']=="search"){
		// write the column headers
		echo '<br><table class="center" cellpadding="2" cellspacing="0"><tr  class="alternate2">';
	    	while ($finfo = mysqli_fetch_field($r)) {
		        echo  '<td><b>'.$finfo->name.'</b></td>';
		    }
		echo '</tr>';
		// and then the row details
		$cnt = mysqli_num_fields($r);
		$Toggle=0;
		while($row = mysqli_fetch_row($r)){  
			if ($row){
				$Toggle = ($Toggle + 1) % 2;
				echo '<tr class="alternate'.$Toggle.'">';
				for ($col = 0; $col < $cnt; $col++) {
					echo '<td>'.$row[$col].'</td>'; 
				}
				echo '</tr>';
			}
		} 
		echo '</table><center>-- End --</center>';
	}
}
?>
