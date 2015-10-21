<?php
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

	<script type="text/javascript" src="scripts/comment_Edit-hash360ad24403a16925129c7a8bcdb76ddc.js"></script>
	<link rel="stylesheet" type="text/css" href="css/comment.css" />

	<link rel="stylesheet" type="text/css" href="css/story_List.css" />
	<script type="text/javascript" src="scripts/story_List-hashc27d28e4b39c17fca5782a8619b32a9c.js"></script>


	<link href="fancytree/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
	<script src="fancytree/jquery.fancytree.min.js" type="text/javascript"></script>
	<script src="fancytree/jquery.fancytree.dnd.js" type="text/javascript"></script>

	<script type="text/javascript" src="jhtml/scripts/jHtmlArea-0.8.js"></script>
    	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.css" />
	<script type="text/javascript" src="jhtml/scripts/jHtmlArea.ColorPickerMenu-0.8.js"></script>
	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.ColorPickerMenu.css" />

	<link rel="stylesheet" type="text/css" href="css/task_List.css" />
	<script type="text/javascript" src="scripts/task_Edit-hash0345c30db9df13d7cceb8fd7f22e787f.js"></script>


	<link rel="stylesheet" type="text/css" href="css/upload_List.css" />
	<script type="text/javascript" src="scripts/upload_Edit-hash751302ad9a9df9274ea1f132fd97e8f5.js"></script>

	<script type="text/javascript" src="scripts/audit_List-hashb9af7a8b5dba1b62019406ba138e2d09.js"></script>

	<link rel="stylesheet" type="text/css" href="css/overrides.css" />

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


	//echo '<div style="display: none" class="iterationdialog" id="iter_'.$iteration.'" title="Choose Iteration">';
	//echo GetIterationsforpop($_REQUEST['PID'],$_REQUEST['IID'],$Project['Backlog_ID']);
	///echo '</div>';

//===========================
	echo '<div class="hidden" id="phpnavicons" align="Left">'.'<a title="Add new story" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;';
	if (isset($_REQUEST['PID'])&&isset($_REQUEST['IID']))
	{
		echo '&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$Project['Backlog_ID'].'"><img src="images/tree-large.png"></a>';
	}
	echo '</div>';


	echo '<div style="display: none" class="statusdialog" id="siter_'.$_REQUEST['IID'].'" title="Set status">';
		echo buildstatuspop($_REQUEST['PID']);
	echo '</div>';

	echo '<div id="msg_div">';
	echo '&nbsp;</div>';

// a Standard story list for the iteraton or backlog.
	echo '<div class="left-box">';
	echo '&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	echo '</div><br>';

	$Toggle=0;
	$Sizecount=0;
	$OSizecount=0;
	$Iterationcount=1;
	$OIterationcount=1;

	echo '<br><table width=100% border=1><tr><td width=48%>';
	echo '<form id="SetIteration" method="post" action="?">';
	echo 'Select Iteration: '.iterations_Dropdown($_REQUEST['PID'], $_REQUEST['LEFTIID'], "LIID");
	echo '<div id="leftsize" class="evenlarger hint"></div>';
	echo '</td><td width=48%>';

	echo 'Select Iteration: '.iterations_Dropdown($_REQUEST['PID'], $_REQUEST['RIGHTIID'], "RIID");
	echo '	<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">';
	echo '<div id="rightsize" class="evenlarger hint"></div>';
	echo '</form>';
	echo '</td></tr>';
	echo '<tr valign="top"><td>';

//  display on the left then list the stories

		echo '<div class="LIID mh15">';

//Todo this is basically Iterpatino _Planning_get.php duplicated and should be cleaned up.
		if ($_REQUEST['LeftIID']>0)
		{
			$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID='.$_REQUEST['LeftIID'].' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
		
			$story_Res = mysqli_query($DBConn, $sql);
			echo '<ul id="sortable-left" class="connectedSortable mh15">';
			if ($story_Row = mysqli_fetch_assoc($story_Res))
			{
				do
				{
					echo	'<li class="storybox" id=story_'.$story_Row['AID'].'>';
					PrintStory ($story_Row);
					echo	'</li>';
				}
				while ($story_Row = mysqli_fetch_assoc($story_Res));
			}
			//echo '<li>&nbsp</li>';
			echo '</ul>';
		}
		echo '</div>';



	echo '</td><td>';

//  display on the right then list the stories

		echo '<div class="RIID mh15" id='.$_POST['RIID'].'>';
//Todo this is basically Iterpatino _Planning_get.php duplicated and should be cleaned up.
		if ($_REQUEST['RightIID']>0)
		{
			$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID='.$_REQUEST['RightIID'].' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
		
			$story_Res = mysqli_query($DBConn, $sql);
			echo '<ul id="sortable-right" class="connectedSortable mh15">';
			if ($story_Row = mysqli_fetch_assoc($story_Res))
			{
				do
				{
					echo	'<li class="storybox" id=story_'.$story_Row['AID'].'>';
					PrintStory ($story_Row);
					echo	'</li>';
				}
				while ($story_Row = mysqli_fetch_assoc($story_Res));
			}
			echo '</ul>';
		}

		echo '</div>';


echo '</td></table>';

include 'include/footer.inc.php';
?>
