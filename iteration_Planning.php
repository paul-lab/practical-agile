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
if (empty($_REQUEST['PID']))
{
	$_REQUEST['PID']=$_POST['PID'];
}
	include 'include/header.inc.php';
//	if (empty($_REQUEST['PID']) && empty($_REQUEST['RID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a></div>';
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


	<link rel="stylesheet" type="text/css" href="css/story_List.css" />
	<script type="text/javascript" src="scripts/story_List-hash6e425f6d9c30a8356feb21b4ead6a72a.js"></script>


	<link href="fancytree/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
	<script src="fancytree/jquery.fancytree.min.js" type="text/javascript"></script>
	<script src="fancytree/jquery.fancytree.dnd.js" type="text/javascript"></script>

	<link rel="stylesheet" type="text/css" href="css/overrides.css" />

	<script type="text/javascript" src="scripts/micromenu-hash0dc02c21be13adc33614481961b31b0c.js"></script>

	<script type="text/javascript" src="jhtml/scripts/jHtmlArea-0.8-min.js"></script>
    	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.css" />
	<script type="text/javascript" src="jhtml/scripts/jHtmlArea.ColorPickerMenu-0.8-min.js"></script>
	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.ColorPickerMenu.css" />

<?php
	Global $statuscolour;
	Global $Iterationcount;
	Global $OIterationcount;
	Global $Sizecount;
	Global $OSizecount;
	Global $Toggle;
	Global $LockedIteration;

	$LockedIteration=0;

// check if we have iterations to display or initialise if not

	if ((empty($_REQUEST['IID']) && empty($_REQUEST['RID'])) || $_REQUEST['IID']==='undefined'){
		$_REQUEST['IID']=$Project['Backlog_ID'];
	}


	if (empty($_REQUEST['LeftIID'])){
		$_REQUEST['LeftIID']=0;
	}

	if (empty($_REQUEST['RightIID'])){
		$_REQUEST['RightIID']=0;

	}

//===========================
	echo '<div class="hidden" id="phpnavicons" align="Left">'.'<a title="Add new story" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;';
	if (isset($_REQUEST['PID'])&&isset($_REQUEST['IID']))
	{
		echo '&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$Project['Backlog_ID'].'"><img src="images/tree-large.png"></a>';
	}
	echo '</div>';


	echo '<div class="hidden statusdialog" id="siter_'.$_REQUEST['IID'].'" title="Set status">';
		echo buildstatuspop($_REQUEST['PID']);
	echo '</div>';

	#echo '<div id="msg_div">';
	#echo '&nbsp;</div>';

// a Standard story list for the iteraton or backlog.
	echo '<div class="left-box">';
	echo '&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	echo '</div><br>';
	echo '<div class="right-box evenlarger">';
	echo 'Current Velocity: '.$Project['Velocity'].'&nbsp;';
	echo '</div>';

	$Toggle=0;
	$Sizecount=0;
	$OSizecount=0;
	$Iterationcount=1;
	$OIterationcount=1;

	echo '<br><table width=100% border=1><tr><td width=48%>';
	echo '<form id="SetIteration" method="post" action="?">';
	echo 'Select Iteration: '.iterations_Dropdown($_REQUEST['PID'], $_REQUEST['LEFTIID'], "LIID");
	echo '<div id="leftsize" class="evenlarger right-box"></div>';
	echo '</td><td width=48%>';

	echo 'Select Iteration: '.iterations_Dropdown($_REQUEST['PID'], $_REQUEST['RIGHTIID'], "RIID");
	echo '	<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">';

	echo '<div id="rightsize" class="evenlarger right-box"></div>';
	echo '</form>';
	echo '</td></tr>';
	echo '<tr valign="top"><td>';

//  display on the left then list the stories

		echo '<div class="LIID mh15">';
			// this is populated by js onchange
		echo '</div>';



	echo '</td><td>';

//  display on the right then list the stories

		echo '<div class="RIID mh15">';
			// this is populated by js onchange
		echo '</div>';


echo '</td></table>';

include 'include/footer.inc.php';
?>
