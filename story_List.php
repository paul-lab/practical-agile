<?php
	include 'include/header.inc.php';
	if (empty($_REQUEST['PID']) && empty($_REQUEST['RID'])) header("Location:project_List.php");

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

	<script type="text/javascript" src="scripts/comment_Edit-hash360ad24403a16925129c7a8bcdb76ddc.js"></script>
	<link rel="stylesheet" type="text/css" href="css/comment.css" />

	<link rel="stylesheet" type="text/css" href="css/story_List.css" />
	<script type="text/javascript" src="scripts/story_List-hash46f811748c7b271318b0f73e2fb008c5.js"></script>

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

	<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="jqplot/excanvas.js"></script><![endif]-->
	<script type="text/javascript" src="jqplot/jquery.jqplot.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.categoryAxisRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.highlighter.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.canvasTextRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.canvasAxisTickRenderer.min.js"></script>
	<script type="text/javascript" src="jqplot/plugins/jqplot.enhancedLegendRenderer.min.js"></script>
	<link class="include" rel="stylesheet" type="text/css" href="jqplot/jquery.jqplot.min.css" />

<?php
	Global $statuscolour;
	Global $Iterationcount;
	Global $OIterationcount;
	Global $Sizecount;
	Global $OSizecount;
	Global $Toggle;
	Global $LockedIteration;

	$LockedIteration=0;

// QUICK adding/removing  things to/from  a release
	if (isset($_POST['AddToRelease']))
	{
		// Project
		if ($_REQUEST['PARID']=='P')
		{
			$sql= 'UPDATE story SET story.Release_ID='.$_REQUEST['RID'].' WHERE story.Project_ID='.$_REQUEST['PID'].' AND story.Release_ID=0 ';
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added entire Project',Get_Project_Name($_REQUEST['PID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		// all Done Work
		}elseif ($_REQUEST['PARID']=='D')
		{
			$sql= 'UPDATE story SET story.Release_ID='.$_REQUEST['RID'].' WHERE story.Status="Done" AND story.Release_ID=0 and story.Project_id='.$_REQUEST['PID'];
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added all DONE work','for Project: '.Get_Project_Name($_REQUEST['PID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		// Not Done work
		}elseif ($_REQUEST['PARID']=='N')
		{
			$sql= 'UPDATE story SET story.Release_ID='.$_REQUEST['RID'].' WHERE story.Status<>"Done" AND story.Release_ID=0 and story.Project_id='.$_REQUEST['PID'];
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added all NOT DONE work','for Project: '.Get_Project_Name($_REQUEST['PID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		}else{
			$sql= 'UPDATE story SET story.Release_ID='.$_REQUEST['RID'].' WHERE story.Parent_Story_ID='.$_REQUEST['PARID'].' AND story.Release_ID=0 ';
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added Epic',$_REQUEST['PARID'].' to Release: '.Get_Release_Name($_REQUEST['RID']));
		}
		mysqli_query($DBConn, $sql);
	}
	if (isset($_POST['DeleteFromRelease']))
	{
		if ($_REQUEST['PARID']=='P')
		{
			$sql= 'UPDATE story SET story.Release_ID=0 WHERE story.Project_ID='.$_REQUEST['PID'].' AND story.Release_ID='.$_REQUEST['RID'];
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed entire Project',Get_Project_Name($_REQUEST['PID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}elseif ($_REQUEST['PARID']=='D')
		{
			$sql= 'UPDATE story SET story.Release_ID=0 WHERE story.Status="Done" AND story.Release_ID='.$_REQUEST['RID'].' and story.Project_id='.$_REQUEST['PID'];
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed all Done work','for Project: '.Get_Project_Name($_REQUEST['PID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}elseif ($_REQUEST['PARID']=='N')
		{
			$sql= 'UPDATE story SET story.Release_ID=0 WHERE story.Status<>"Done" AND story.Release_ID='.$_REQUEST['RID'].' and story.Project_id='.$_REQUEST['PID'];
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed all NOT DONE work',' in Project: '.Get_Project_Name($_REQUEST['PID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}else{
			$sql= 'UPDATE story SET story.Release_ID=0 WHERE (story.Parent_Story_ID='.$_REQUEST['PARID'].' AND story.Release_ID='.$_REQUEST['RID'].') or story.AID='.$_REQUEST['PARID'];
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed Epic',$_REQUEST['PARID'].' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}
		mysqli_query($DBConn, $sql);
	}



function GetTreeRoot ($sql,$flag='')
{
	Global $DBConn;
	$tree_Res = mysqli_query($DBConn, $sql);
	echo '<br>&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	echo '&nbsp;&nbsp;<a href="#" class="btnCollapseAll" id="">Collapse</a>/';
	echo '<a href="#" class="btnExpandAll" id="">Expand</a>';

	echo '<div class="tree" id="tree">';
		echo '<ul>';
			GetTree ($tree_Res,$flag);
		echo '</ul>';
	echo '</div>';
}

function GetTree ($tree_Res,$flag='')
{
	Global $DBConn;
	if ($tree_Row = mysqli_fetch_assoc($tree_Res))
	{
		do
		{
			if (empty($_REQUEST['RID']) || ($tree_Row['Release_ID']==$_REQUEST['RID'] || Num_Children($tree_Row['AID'])!=0)){
				echo	'<li id="'.$tree_Row['AID'].'" data-nodndflag="'.$flag.'" data-iteration="'.Get_Iteration_Name($tree_Row['Iteration_ID'],False).'" data-iid="'.$tree_Row['Iteration_ID'].'" data-pid="'.$tree_Row['Project_ID'].'" >';
					echo '<div class="treebox">';
						PrintStory ($tree_Row);
					echo '</div>';
					// if i have children, then go and fetch them
					$sql='SELECT * FROM story WHERE story.Parent_Story_ID='.$tree_Row['AID'].' order by story.Epic_Rank';
					$Child_Res = mysqli_query($DBConn, $sql);
					if ($Child_Res)
					{
						echo '<ul>';
						GetTree($Child_Res,$flag);
						echo '</ul>';
					}
				echo '</li>';
		}
		}while ($tree_Row = mysqli_fetch_assoc($tree_Res));
	}
}


// Make sure that we have an iteration to display if this is not a release
if (empty($_REQUEST['IID']) && empty($_REQUEST['RID']) ){
	$_REQUEST['IID']=$Project['Backlog_ID'];
}

// this is not a release so lets get the project iterations
if (empty($_REQUEST['RID'])){
	//===

	echo '<div style="display: none" class="iterationdialog" id="iter_'.$_REQUEST['IID'].'" title="Choose Iteration">';
	echo GetIterationsforpop($_REQUEST['PID'],$_REQUEST['IID'],$Project['Backlog_ID']);
	echo '</div>';

}

echo '<div style="display: none" class="statusdialog" id="siter_'.$_REQUEST['IID'].'" title="Set status">';
echo buildstatuspop($_REQUEST['PID']);
echo '</div>';

//===========================
	echo '<div class="hidden" id="phpnavicons" align="Left">'.'<a title="Add new story" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;';
	if (isset($_REQUEST['PID'])&&isset($_REQUEST['IID']))
	{
		echo '&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$Project['Backlog_ID'].'"><img src="images/tree-large.png"></a>';
		echo '&nbsp; &nbsp;<a  title="Scrum Board" href="story_List.php?Type=board&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/board-large.png"></a>';
		echo '&nbsp; &nbsp;<a  title="Story List" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/list-large.png"></a>';
	}
	echo '</div>';

echo '<div id="msg_div">';
echo '&nbsp;</div>';

if ($_REQUEST['Type']=="search"){

	echo '<br>&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	$cond="";
	$sel = "SELECT * FROM story where story.Project_ID=".$_REQUEST['PID']." and (";
	$psel = "SELECT sum(Size) as points FROM story where story.Project_ID=".$_REQUEST['PID']." and (";

	// an Empty QID this is a search, otherwise a qry has been passed in
	if (empty($_REQUEST['QID'])){
		if (substr($_REQUEST['searchstring'],0,1)=='#') {
			$cond='story.ID='.substr($_REQUEST['searchstring'],1);
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,7))=='status:'){
			$cond='story.Status like "%'.substr($_REQUEST['searchstring'],7).'%"';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,6))=='owner:'){
			$cond='story.Owner_ID=(select ID from user where user.Initials="'.substr($_REQUEST['searchstring'],6).'")';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,4))=='tag:'){
			$cond='story.Tags like "%'.substr($_REQUEST['searchstring'],4).'%"';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,5))=='size:'){
			$cond='story.Size='.substr($_REQUEST['searchstring'],5).'';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,5))=='type:'){
			$cond='story.Type="'.substr($_REQUEST['searchstring'],5).'"';
		} else{
			$cond=' story.Col_1 like "%'.$_REQUEST['searchstring'].'%" '.
			' or story.Col_2 like "%'.$_REQUEST['searchstring'].'%" '.
			' or story.Acceptance like "%'.$_REQUEST['searchstring'].'%" '.
			' or story.Summary like "%'.$_REQUEST['searchstring'].'%" '.
			' or (0<(select count(ID) from comment where comment.Story_AID = story.AID and Comment_Text like"%'.$_REQUEST['searchstring'].'%")) '.
			' or (0<(select count(ID) from task where task.Story_AID = story.AID and task.Desc like"%'.$_REQUEST['searchstring'].'%")) ';
		}
		$sql ="{$sel}{$cond})";
		$psql ="{$psel}{$cond})";
		echo '<br>Search "'.$_REQUEST['searchstring'].'"';
	}else{
		$qsql = 'SELECT QSQL, Qorder, queries.Desc FROM queries where ID='.$_REQUEST['QID'];
		$QRes = mysqli_query($DBConn, $qsql);
		$QRow = mysqli_fetch_assoc($QRes);
		$cond=" ".$QRow['QSQL'];
		$cond= str_replace('{User}', $_SESSION['ID'], $cond);
		$cond= str_replace('{Iteration}', $_REQUEST['IID'], $cond);
		$cond= str_replace('{Project}', $_REQUEST['PID'], $cond);
		$cond= str_replace('{Backlog}', $Project['Backlog_ID'], $cond);
		$sql =$sel.$cond.') '.$QRow['Qorder'];
		$psql =$psel.$cond.') '.$QRow['Qorder'];
		echo '<br>"'.$QRow['Desc'].'"';
	}

	$pres=mysqli_query($DBConn, $psql);
	$pts=mysqli_fetch_assoc($pres);
	if ($story_Res = mysqli_query($DBConn, $sql))
	{
		echo ' returns <b>'.mysqli_num_rows($story_Res).'</b> Stories and <b>'.$pts['points'].'</b> points';
		echo '<ul id="sortable">';
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
}

if (empty($_REQUEST['Type'])){
// a Standard story list for the iteraton or backlog.
	echo '<table align="center" width=90%><tr><td align="center">';
	print_summary($Iteration['Points_Object_ID'],True); // with velocity
	echo '</td></tr><tr><td align="center">';
	print_Graphx($Iteration['Points_Object_ID'], False); // Not Small
	echo '</td></tr></table>';
	$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID='.$_REQUEST['IID'].' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
	$story_Res = mysqli_query($DBConn, $sql);

	echo '<div class="left-box">';
	echo '&nbsp;<a title = "Iteration Tree" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'&Type=tree&Root=iteration"><img src="images/tree.png"></a>';
	echo '&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	echo '</div>';

	echo '<div class="inline right-box">';
	echo '<div class="inline" id="comment_count_i_'.$Iteration['Comment_Object_ID'].'">';
	$tsql = 'SELECT count(*) as count FROM comment where comment.Comment_Object_ID='.$Iteration['Comment_Object_ID'].' and Comment_Object_ID<>0';
						$tres=mysqli_query($DBConn, $tsql);
						$t_row = mysqli_fetch_assoc($tres);
						if ($t_row['count'] >0){
							echo ' ('.$t_row['count'].')';
						}
	echo'</div>';
	echo '<a class="commentpopup" id="commenti_'.$Iteration['Comment_Object_ID'].'" href="" onclick="javascript: return false;" title="Show Comments"><img src="images/comment-small.png"></a> &nbsp;';

	echo '</div><br>';
	echo '<div class="commentsdialog" id="commentspopi_'.$Iteration['Comment_Object_ID'].'"></div>';

	$Toggle=0;
	$Sizecount=0;
	$OSizecount=0;
	$Iterationcount=1;
	$OIterationcount=1;

	echo '<ul id="sortable">';
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

if ($_REQUEST['Type']=='tree'){

	$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and ID='.$_REQUEST['Root'].' order by story.Epic_Rank';

//Iteration Tree
	if ($_REQUEST['Root']=='iteration') {
		echo '<table align="center" width=90%><tr><td align="center">';
		print_summary($Iteration['Points_Object_ID'],True); // with velocity
		echo '</td></tr><tr><td align="center">';
		print_Graphx($Iteration['Points_Object_ID'], False); // Not Small
		echo '</td></tr></table>';
		$sqlp = 'SELECT AID FROM story where story.Iteration_ID='.$_REQUEST['IID'];
		$Res = mysqli_query($DBConn, $sqlp);
		if ($Res)
		{
			while  ($Row = mysqli_fetch_assoc($Res))
			{
				$instr.=Top_Parent($Row['AID']).',';
			}
		}
		$instr = rtrim($instr, ",");
		$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and AID IN('.$instr.') order by story.Epic_Rank';
		GetTreeRoot ($sql,'nodnd');

	}elseif ($_REQUEST['Root']=='release')
	{
//Release Tree
		// release info start, end etc
		$sqlp='select * from release_details where id ='.$_REQUEST['RID'];
		$Res = mysqli_query($DBConn, $sqlp);
		if ($Res)
		{
			$RelRow = mysqli_fetch_assoc($Res);
			echo '&nbsp;<div class="inline larger"><b>'.$RelRow['Name'].' ('.$RelRow['Start'].' -> '.$RelRow['End'].')</b></div>';
		}

		// release statistics stories & points)
		$tsql = 'SELECT Status, count(*) as relcount, sum(Size) as relsize FROM story where story.Release_ID='.$_REQUEST['RID'].' and story.Status IS NOT NULL group by story.Status';

		print_releasesummary("Release",$tsql);

		echo '<br>&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';

		//only show projects that this user is a project admin forprojects
		if ($Usr['Admin_User']==1)
		{
			$tsql = 'SELECT distinct(Project_ID) as relproj  FROM story where story.Release_ID='.$_REQUEST['RID'];
		}else{
			$tsql = 'SELECT distinct(story.Project_ID) as relproj FROM story left join user_project on story.Project_ID = user_project.Project_ID  where story.Release_ID='.$_REQUEST['RID'].' and user_project.User_ID='.$_SESSION['ID'].' and user_project.Project_Admin=1';
		}

		$Resp=mysqli_query($DBConn, $tsql);
		while  ($Rowp = mysqli_fetch_assoc($Resp))
		{
		$dummy = buildstatuspop($Rowp['relproj']);
		$instr='';
		// stories in release
			$sqlp = 'SELECT AID FROM story where story.Release_ID='.$_REQUEST['RID'].' and story.Project_ID='.$Rowp['relproj'];
			$Res = mysqli_query($DBConn, $sqlp);
			if ($Res)
			{
				while  ($Row = mysqli_fetch_assoc($Res))
				{
					$instr.=Top_Parent($Row['AID']).',';
				}
			}
			// print project stats for release
			$ptsql = 'SELECT Status, count(*) as relcount, sum(Size) as relsize FROM story where story.Project_Id ='.$Rowp['relproj'].' and story.Release_ID='.$_REQUEST['RID'].' and story.Status IS NOT NULL group by story.Status';

			print_releasesummary($Rowp['relproj'],$ptsql);

			if ($RelRow['Locked']==0)
			{
				echo '<form method="post" action="?">';
				echo '&nbsp; &nbsp; <input type="submit" name="AddToRelease" value="Add" title="Add stories in this epic, to this release. (No child Epics)">';
				echo '&nbsp; &nbsp; <input type="submit" name="DeleteFromRelease" value="Remove" title="Remove stories in this epic, from this release. (Not Chid Epics)">';

				$menu = '&nbsp; <select name="PARID">';
				$menu .= '<option value="0"></option>';
				$menu .= '<option value="P"> ** ENTIRE PROJECT **</option>';
				$menu .= '<option value="D"> ** All "Done" work **</option>';
				$menu .= '<option value="N"> ** All work NOT "Done" **</option>';
				$menu .= '<option value="0"></option>';
				$sql = 'select AID, ID, Summary from story where Project_ID='.$Rowp['relproj'].' and 0<(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by ID';
				$queried = mysqli_query($DBConn, $sql);
				while ($result = mysqli_fetch_array($queried)) {
					$menu .= '<option value="' . $result['AID'] . '">Epic ' .$result['ID'].' - '. $result['Summary'] .'</option>';
				}
				$menu .= '</select> to / from this release (Only work NOT already in a release will be added)';
				echo $menu;

				echo '	<input type="hidden" name="Type" value="tree">';
				echo '	<input type="hidden" name="Root" value="release">';
				echo '	<input type="hidden" name="RID" value="'.$_REQUEST['RID'].'">';
				echo '	<input type="hidden" name="PID" value="'.$Rowp['relproj'].'">';
				echo '</form>';
			}

			// print the tree
			$instr = rtrim($instr, ",");

			$sql = 'SELECT * FROM story where project_ID='.$Rowp['relproj'].' and AID IN('.$instr.') order by story.project_ID, story.Epic_Rank';
			$tree_Res = mysqli_query($DBConn, $sql);
			echo '&nbsp; &nbsp;<a href="#" class="btnCollapseAll" id="'.$Rowp['relproj'].'">Collapse</a>/';
			echo '<a href="#" class="btnExpandAll" id="'.$Rowp['relproj'].'">Expand</a>';
			echo '<div class="tree" id="tree'.$Rowp['relproj'].'"><ul><li class="larger">'.Get_Project_Name($Rowp['relproj']).'<ul>';
				GetTree ($tree_Res,'nodnd');
				echo '</li></ul></ul>';
			echo '</div>';
		}

	}elseif ($_REQUEST['Root']==0)
	{
// Project Tree
		echo '<table align="center" width=90%><tr><td align="center">';
		print_summary($Project['Points_Object_ID'],True); // with velocity
		echo '</td></tr><tr><td align="center">';
		print_Graphx($Project['Points_Object_ID'], False); // Not Small
		echo '</td></tr></table>';
		$sql = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and Parent_Story_ID=0 order by story.Epic_Rank';
		GetTreeRoot ($sql);
	}else{

		GetTreeRoot ($sql);
	}
}


// start Scrum Board
if ($_REQUEST['Type']=='board'){
	$colcount==0;
	echo '<br>';
	echo '<span id="'.$_REQUEST['PID'].'">';
	$sql = 'SELECT * FROM story_status where story_status.Project_ID='.$_REQUEST['PID'].' and LENGTH(story_status.Desc)>0 order by story_status.Order';
	$status_Res = mysqli_query($DBConn, $sql);
	if ($status_Row = mysqli_fetch_assoc($status_Res))
	{
		do
		{
			$colcount=$colcount+1;
			echo '<ul name= "'.$_REQUEST['IID'].'" id="status'.$status_Row['Order'].'" class="connectedSortable">';
			echo '<li class="scrumtitle" style="background: #'.$status_Row['RGB'].';">'.$status_Row['Desc'].'</li>';
			$sqls = 'SELECT * FROM story where story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID='.
				$_REQUEST['IID'].' and  0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID)'.
				' and story.Status="'.$status_Row['Desc'].'" order by story.Iteration_Rank';
			$story_Res = mysqli_query($DBConn, $sqls);
			if ($story_Row = mysqli_fetch_assoc($story_Res))
			{
				do
				{
					echo '<li class="scrumdetail" id="'.$story_Row['AID'].'">'.
		 				'<a href="story_Edit.php?AID='.$story_Row['AID'].'&PID='.$_REQUEST['PID'].'&IID='.$story_Row['Iteration_ID'].'" title="Edit Story">#'.$story_Row['ID'].'</a>'.
						' - '.substr($story_Row['Summary'], 0, 120).
						'<br>'.html_entity_decode ($story_Row['Col_1'],ENT_QUOTES).'&nbsp;'.
						'<br>'.$story_Row['Type'].'&nbsp;'.
						'&nbsp;['.$story_Row['Size'].']&nbsp;'.
						'&nbsp;'.Get_User($story_Row['Owner_ID'],1).'&nbsp;'.
						'&nbsp;';
						if($story_Row['Parent_Story_ID'] != 0) {
						$parentssql='SELECT @id := (SELECT Parent_Story_ID FROM story WHERE AID = @id and Parent_Story_ID <> 0) AS parent FROM (SELECT @id :='.$story_Row['AID'].') vars STRAIGHT_JOIN story  WHERE @id is not NULL';
						$parents_Res = mysqli_query($DBConn, $parentssql);
						if ($parents_row = mysqli_fetch_assoc($parents_Res))
						{
							do
							{
						  		if($parents_row['parent']!=NULL)
								{
									$parentsql='select ID, Summary, Size from story where AID='.$parents_row['parent'].' and AID<>0';
									$parent_Res = mysqli_query($DBConn, $parentsql);
									if ($parent_row = mysqli_fetch_assoc($parent_Res))
									{
										echo '<a  title="'.$parent_row ['Summary'].'"';
 										echo ' href="story_List.php?Type=tree&Root='.$parent_row ['ID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
										echo ' #'.$parent_row ['ID'].'('.$parent_row ['Size'].')</a>, &nbsp;';									}
								}
							}
							while ($parents_row = mysqli_fetch_assoc($parents_Res));
						}
					}
					echo 	'</li>';
				}
				while ($story_Row = mysqli_fetch_assoc($story_Res));
			}
			echo '</ul>';
		}while ($status_Row = mysqli_fetch_assoc($status_Res));
	}
	echo '</span>';
?>

<script>
// column width for the scrum board
	var cwi= ((100/<?=$colcount;?>)-(<?=$colcount;?>/20))+'%';
	$('.connectedSortable').css("width", cwi);
</script>

<?php

}

// End Scrum Board

	include 'include/footer.inc.php';


?>
