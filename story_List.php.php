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


	<link rel="stylesheet" type="text/css" href="css/story_List.css" />
	<script type="text/javascript" src="scripts/story_List-hasha5f1cf7ea7cc4e09f5efaeb031acb709.js"></script>

	<link href="fancytree/skin-win7/ui.fancytree.css" rel="stylesheet" type="text/css">
	<script src="fancytree/jquery.fancytree.min.js" type="text/javascript"></script>
	<script src="fancytree/jquery.fancytree.dnd.js" type="text/javascript"></script>
	<script type="text/javascript" src="jhtml/scripts/jHtmlArea-0.8-min.js"></script>
	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.css" />
	<script type="text/javascript" src="jhtml/scripts/jHtmlArea.ColorPickerMenu-0.8-min.js"></script>
	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.ColorPickerMenu.css" />
	<link rel="stylesheet" type="text/css" href="css/micro_menu.css" />
	<link rel="stylesheet" type="text/css" href="css/overrides.css" />
	<script type="text/javascript" src="scripts/micromenu-hash0dc02c21be13adc33614481961b31b0c.js"></script>

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
		$_REQUEST['IID']=substr($_REQUEST['PARID'],1,64);
		$_REQUEST['PARID']=substr($_REQUEST['PARID'],0,1);
		// Project
		if ($_REQUEST['PARID']=='P')
		{
			$sql= 'UPDATE story SET Release_ID= ? WHERE story.Project_ID= ? AND story.Release_ID=0 ';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added entire Project',Get_Project_Name($_REQUEST['PID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		// all Done Work
		}elseif ($_REQUEST['PARID']=='D')
		{
			$sql= 'UPDATE story SET Release_ID= ? WHERE story.Status="Done" AND story.Release_ID=0 and story.Project_id= ?';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added all DONE work','for Project: '.Get_Project_Name($_REQUEST['PID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		// Not Done work
		}elseif ($_REQUEST['PARID']=='N')
		{
			$sql= 'UPDATE story SET Release_ID= ? WHERE story.Status<>"Done" AND story.Release_ID=0 and story.Project_id= ?';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added all NOT DONE work','for Project: '.Get_Project_Name($_REQUEST['PID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		// Iteration
		}elseif ($_REQUEST['PARID']=='I')
		{
			$sql= 'UPDATE story SET Release_ID= ? WHERE story.Iteration_ID= ? AND story.Release_ID=0 and story.Project_id= ?';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['IID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added Iteration',Get_Iteration_Name($_REQUEST['IID']).' to Release: '.Get_Release_Name($_REQUEST['RID']));
		// Epic Contents
		}elseif ($_REQUEST['PARID']=='E')
		{
			$sql= 'UPDATE story SET Release_ID= ? WHERE story.Parent_Story_ID= ? AND story.Release_ID=0 ';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['IID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Added Epic',$_REQUEST['IID'].' to Release: '.Get_Release_Name($_REQUEST['RID']));
		}
	}
	if (isset($_POST['DeleteFromRelease']))
	{
		$_REQUEST['IID']=substr($_REQUEST['PARID'],1,64);
		$_REQUEST['PARID']=substr($_REQUEST['PARID'],0,1);
		if ($_REQUEST['PARID']=='P')
		{ //project
			$sql= 'UPDATE story SET Release_ID=0 WHERE story.Project_ID= ? AND story.Release_ID= ?';
			$DBConn->directsql($sql, array($_REQUEST['PID'], $_REQUEST['RID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed entire Project',Get_Project_Name($_REQUEST['PID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}elseif ($_REQUEST['PARID']=='D')
		{ //done
			$sql= 'UPDATE story SET Release_ID=0 WHERE story.Status="Done" AND story.Release_ID= ? and story.Project_id= ?';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed all Done work','for Project: '.Get_Project_Name($_REQUEST['PID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}elseif ($_REQUEST['PARID']=='N')
		{ //not done
			$sql= 'UPDATE story SET Release_ID=0 WHERE story.Status<>"Done" AND story.Release_ID= ? and story.Project_id= ?';
			$DBConn->directsql($sql, array($_REQUEST['RID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed all NOT DONE work',' in Project: '.Get_Project_Name($_REQUEST['PID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}elseif ($_REQUEST['PARID']=='I')
		{ //iteration
			$sql= 'UPDATE story SET Release_ID=0 WHERE story.Iteration_ID= ? AND story.Release_ID= ? and story.Project_id= ?';
			$DBConn->directsql($sql, array($_REQUEST['IID'], $_REQUEST['RID'], $_REQUEST['PID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed Iteration',Get_Iteration_Name($_REQUEST['IID']).' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}elseif ($_REQUEST['PARID']=='E')
		{ //epic
			$sql= 'UPDATE story SET Release_ID=0 WHERE (story.Parent_Story_ID= ? AND story.Release_ID= ?) or story.AID= ?';
			$DBConn->directsql($sql, array($_REQUEST['IID'], $_REQUEST['RID'], $_REQUEST['IID']));
			auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Removed Epic',$_REQUEST['IID'].' from Release: '.Get_Release_Name($_REQUEST['RID']));
		}
	}

function GetTreeRoot ($sql,$flag='')
{
	Global $DBConn;
	$tree_Res = $DBConn->directsql($sql);
	echo '<br>&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	echo '&nbsp;&nbsp;<a href="#" class="btnCollapseAll" id="">Collapse</a>/';
	echo '<a href="#" class="btnExpandAll" id="">Expand</a>';

	echo '<div class="tree" id="tree">';
		echo '<ul>';
			GetTree ($tree_Res,$flag);
		echo '</ul>';
	echo '</div>';
}

function GetTree ($tree_Res,$flag=''){
	Global $DBConn;
	foreach ($tree_Res as $tree_Row ){
		if (empty($_REQUEST['RID']) || ($tree_Row['Release_ID']==$_REQUEST['RID'] || Num_Children($tree_Row['AID'])!=0)){
			echo	'<li id="'.$tree_Row['AID'].'" data-nodndflag="'.$flag.'" data-iteration="'.Get_Iteration_Name($tree_Row['Iteration_ID'],False).'" data-iid="'.$tree_Row['Iteration_ID'].'" data-pid="'.$tree_Row['Project_ID'].'" >';
				echo '<div class="treebox">';
					PrintStory ($tree_Row);
				echo '</div>';
				// if i have children, then go and fetch them
				$sql='SELECT * FROM story WHERE story.Parent_Story_ID= ? order by story.Epic_Rank';
				$Child_Res = $DBConn->directsql($sql, $tree_Row['AID']);
				if (count($Child_Res) > 0)	{
					echo '<ul>';
					GetTree($Child_Res,$flag);
					echo '</ul>';
				}
			echo '</li>';
		}
	}
}


// Make sure that we have an iteration to display if this is not a release
if (empty($_REQUEST['IID']) && empty($_REQUEST['RID']) ){
#if (empty($_REQUEST['IID']) ){
	$_REQUEST['IID']=$Project['Backlog_ID'];
}

// this is not a release so lets get the project iterations
if (empty($_REQUEST['RID'])){
	//===

	echo '<div class=" hidden iterationdialog" id="iter_'.$_REQUEST['IID'].'" title="Choose Iteration">';
	echo GetIterationsforpop($_REQUEST['PID'],$_REQUEST['IID'],$Project['Backlog_ID']);
	echo '</div>';

}

echo '<div class="hidden statusdialog" id="siter_'.$_REQUEST['IID'].'" title="Set status">';
echo buildstatuspop($_REQUEST['PID']);
echo '</div>';

//===========================
	echo '<div class="hidden" id="phpnavicons" align="Left">'.'<a title="Add new card" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;';
	if (isset($_REQUEST['PID'])&&isset($_REQUEST['IID']))
	{
		echo '&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$Project['Backlog_ID'].'"><img src="images/tree-large.png"></a>';
		echo '&nbsp; &nbsp;<a  title="Scrum Board" href="story_List.php?Type=board&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/board-large.png"></a>';
		echo '&nbsp; &nbsp;<a  title="Story List" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/list-large.png"></a>';
	}
	echo '</div>';

#echo '<div id="msg_div">';
#echo '&nbsp;</div>';

if ($_REQUEST['Type']=="search"){

	echo '<br>&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	$cond="";
	$sel = "SELECT * FROM story where story.Project_ID= ? and (";
	$psel = "SELECT sum(Size) as points FROM story where story.Project_ID= ? and (";
	$bind = array($_REQUEST['PID']);

	// an Empty QID this is a search, otherwise a qry has been passed in
	if (empty($_REQUEST['QID'])){
		if (substr($_REQUEST['searchstring'],0,1)=='#') {
			$cond='story.ID= ?';
			$bind[] = substr($_REQUEST['searchstring'],1);
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,7))=='status:'){
			$cond='story.Status like ?';
			$bind[] = '%'.substr($_REQUEST['searchstring'],7).'%';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,2))=='s:'){
			$cond='story.Status like ?';
			$bind[] = '%'.substr($_REQUEST['searchstring'],2).'%';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,6))=='owner:'){
			$cond='story.Owner_ID=(select ID from user where user.Initials= ?)';
			$bind[] = trim(substr($_REQUEST['searchstring'],6));
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,2))=='o:'){
			$cond='story.Owner_ID=(select ID from user where user.Initials= ?)';
			$bind[] = trim(substr($_REQUEST['searchstring'],2));
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,8))=='release:'){
			$cond='story.Release_ID=(select ID from release_details where release_details.Name= ?)';
			$bind[] = trim(substr($_REQUEST['searchstring'],8));
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,2))=='r:'){
			$cond='story.Release_ID=(select ID from release_details where release_details.Name= ?)';
			$bind[] = trim(substr($_REQUEST['searchstring'],2));
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,4))=='tag:'){
			$cond='story.Tags like ?';
			$bind[] = '%'.substr($_REQUEST['searchstring'],4).'%';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,2))=='t:'){
			$cond='story.Tags like ?';
			$bind[] = '%'.substr($_REQUEST['searchstring'],2).'%';
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,5))=='size:'){
			if (substr($_REQUEST['searchstring'],5)=='?'){
				$cond="story.Size='?'";
			}else{
				$cond='story.Size= ?';
				$bind[] = substr($_REQUEST['searchstring'],5);
			}
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,2))=='i:'){
			if (substr($_REQUEST['searchstring'],2)=='?'){
				$cond="story.Size='?'";
			}else{
				$cond='story.Size= ?';
				$bind[] = substr($_REQUEST['searchstring'],2);
			}
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,5))=='type:'){
			$cond='story.Type= ?';
			$bind[] = substr($_REQUEST['searchstring'],5);
		} elseif (strtolower(substr($_REQUEST['searchstring'],0,2))=='y:'){
			$cond='story.Type= ?';
			$bind[] = substr($_REQUEST['searchstring'],2);
		} else{
			$cond=' story.Col_1 like ? or story.Col_2 like ? or story.Acceptance like ? or story.Summary like ? or (0<(select count(ID) from comment where comment.Story_AID = story.AID and Comment_Text like ?)) or (0<(select count(ID) from task where task.Story_AID = story.AID and task.Desc like ?)) ';
			$bind[] = '%'.$_REQUEST['searchstring'].'%';
			$bind[] = '%'.$_REQUEST['searchstring'].'%';
			$bind[] = '%'.$_REQUEST['searchstring'].'%';
			$bind[] = '%'.$_REQUEST['searchstring'].'%';
			$bind[] = '%'.$_REQUEST['searchstring'].'%';
			$bind[] = '%'.$_REQUEST['searchstring'].'%';
		}
		$sql ="{$sel}{$cond})";
		$psql ="{$psel}{$cond})";
		echo '<br>Search "'.$_REQUEST['searchstring'].'"';
	}else{
		$qsql = 'SELECT QSQL, Qorder, queries.Desc FROM queries where ID= ?';
		$QRow = $DBConn->directsql($qsql, $_REQUEST['QID']);
		$QRow = $QRow[0];
		$cond=" ".$QRow['QSQL'];
		$cond= str_replace('{User}', $_SESSION['ID'], $cond);
		$cond= str_replace('{Iteration}', $_REQUEST['IID'], $cond);
		$cond= str_replace('{Project}', $_REQUEST['PID'], $cond);
		$cond= str_replace('{Backlog}', $Project['Backlog_ID'], $cond);
		$sql =$sel.$cond.') '.$QRow['Qorder'];
		$psql =$psel.$cond.') '.$QRow['Qorder'];
		echo '<br>"'.$QRow['Desc'].'"';
	}

	$pts=$DBConn->directsql($psql, $bind);
	$pts=$pts[0];
	$story_Res = $DBConn->directsql($sql, $bind);
	if (count($story_Res) > 0)	{
		echo ' returns <b>'.count($story_Res).'</b> Stories and <b>'.$pts['points'].'</b> points';
		echo '<ul id="sortable">';
		foreach ($story_Res as $story_Row){
			echo	'<li class="storybox" id=story_'.$story_Row['AID'].'>';
			PrintStory ($story_Row);
			echo	'</li>';
		}
		echo '</ul>';
	}
}

if (empty($_REQUEST['Type'])){
// a Standard story list for the iteraton or backlog.
	echo '<table align="center" width=90%><tr><td align="center">';
	print_summary($Iteration['ID'],True); // with velocity
	echo '</td></tr><tr><td align="center">';

	// if not the backlog include iteration start and end dates for graph
	if ($Iteration['ID']== $Project['Backlog_ID']){
		print_Graphx($Iteration['Points_Object_ID'], False); // Not Small
	} else {
		print_Graphx($Iteration['Points_Object_ID'], False, $Iteration['Start_Date'], $Iteration['End_Date']); // Not Small
	}

	echo '</td></tr></table>';

	echo '<div class="left-box">';
	echo '&nbsp;<a title = "Iteration Tree" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'&Type=tree&Root=iteration"><img src="images/tree.png"></a>';
	echo '&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';
	echo '</div>';

	echo '<div class="inline right-box">';
	echo '<div class="inline" id="comment_count_i_'.$Iteration['Comment_Object_ID'].'">';
	$tsql = 'SELECT count(*) as count FROM comment where comment.Comment_Object_ID= ? and Comment_Object_ID<>0';
	$t_row=$DBConn->directsql($tsql, $Iteration['Comment_Object_ID']);
	$t_row =$t_row [0];
	if ($t_row['count'] >0){
		echo ' ('.$t_row['count'].')';
	}
	echo'</div>';
	echo '<a class="commentpopup" id="commenti_'.$Iteration['Comment_Object_ID'].'" href="" onclick="javascript: return false;" title="Show Comments"><img src="images/comment-small.png"></a> &nbsp;';

	echo '</div><br>';
	echo '<div class="hidden" id="commentspopi_'.$Iteration['Comment_Object_ID'].'"></div>';

	$Toggle=0;
	$Sizecount=0;
	$OSizecount=0;
	$Iterationcount=1;
	$OIterationcount=1;

	echo '<ul id="sortable">';
	$sql = 'SELECT * FROM story where story.Project_ID= ? and story.Iteration_ID= ? and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by story.Iteration_Rank';
	$story_Res = $DBConn->directsql($sql, array($_REQUEST['PID'], $_REQUEST['IID']));
	if (count($story_Res)>0 ){
		foreach ($story_Res as $story_Row){
			echo	'<li class="storybox" id=story_'.$story_Row['AID'].'>';
			PrintStory ($story_Row);
			echo	'</li>';
		}
	}
	echo '</ul>';
}

if ($_REQUEST['Type']=='tree'){

	$sql = 'SELECT * FROM story where story.Project_ID= ? and ID= ? order by story.Epic_Rank';
	$bind = array($_REQUEST['PID'], $_REQUEST['Root']);

//Iteration Tree
	if ($_REQUEST['Root']=='iteration') {
		echo '<table align="center" width=90%><tr><td align="center">';
		print_summary($Iteration['ID'],True); // with velocity
		echo '</td></tr><tr><td align="center">';

		if ($Iteration['ID']== $Project['Backlog_ID']){
			print_Graphx($Iteration['Points_Object_ID'], False); // Not Small
		} else {
			print_Graphx($Iteration['Points_Object_ID'], False, $Iteration['Start_Date'], $Iteration['End_Date']); // Not Small
		}

		echo '</td></tr></table>';
		$sqlp = 'SELECT AID FROM story where story.Iteration_ID= ?';
		$Res = $DBConn->directsql($sqlp, $_REQUEST['IID']);
		foreach($Res as $Row){
			$instr.=Top_Parent($Row['AID']).',';
		}
		$instr = rtrim($instr, ",");
		$sql = 'SELECT * FROM story where story.Project_ID= ? and AID IN('.$instr.') order by story.Epic_Rank';
		$bind = array($_REQUEST['PID']);
		GetTreeRoot ($sql,'nodnd');

	}elseif ($_REQUEST['Root']=='release')
	{
//Release Tree
		// release info start, end etc
		$RelRow = fetchusingID('*',$_REQUEST['RID'],'release_details');
		if (count($RelRow)>0)		{
			echo '&nbsp;<div class="inline larger"><b>'.$RelRow['Name'].' ('.$RelRow['Start'].' -> '.$RelRow['End'].')</b></div>';
			if ($RelRow['Locked']!=0){
				echo ' <b>Locked</b><br>';
			}
		}

		// release statistics stories & points)
		$tsql = 'SELECT Status, count(*) as relcount, sum(Size) as relsize FROM story where story.Release_ID= ? and story.Status IS NOT NULL group by story.Status';
		print_releasesummary("",$tsql, $_REQUEST['RID']);

		echo '<br>&nbsp;&nbsp;<img id="1line" src="images/1line.png" title="One line story display"> <img id="2line" src="images/2line.png" title="Two line story display"> <img id="3line" src="images/3line.png" title="Three line story display">';

		//only show projects that this user is a project admin forprojects
		if ($Usr['Admin_User']==1)
		{
			$tsql = 'SELECT distinct(Project_ID) as relproj  FROM story where story.Release_ID= ?';
			$bind = array($_REQUEST['RID']);
		}else{
			$tsql = 'SELECT distinct(story.Project_ID) as relproj FROM story left join user_project on story.Project_ID = user_project.Project_ID  where story.Release_ID= ? and user_project.User_ID= ? and user_project.Project_Admin=1';
			$bind = array($_REQUEST['RID'], $_SESSION['ID']);
		}

		$Resp = $DBConn->directsql($tsql, $bind);
		foreach($Resp as $Rowp)	{
			$dummy = buildstatuspop($Rowp['relproj']);
			$instr='';
		// stories in release
			$sqlp = 'SELECT AID FROM story where story.Release_ID= ? and story.Project_ID= ?';
			
			$Res =$DBConn->directsql($sqlp, array($_REQUEST['RID'], $Rowp['relproj']));
			foreach($Res as $Row){
				$instr.=Top_Parent($Row['AID']).',';
			}
			// print project stats for release
			$ptsql = 'SELECT Status, count(*) as relcount, sum(Size) as relsize FROM story where story.Project_Id = ? and story.Release_ID= ? and story.Status IS NOT NULL group by story.Status';
			print_releasesummary($Rowp['relproj'],$ptsql, array($Rowp['relproj'], $_REQUEST['RID']));

			if ($RelRow['Locked']==0)
			{
				echo '<form method="post" action="?">';
				echo '&nbsp; &nbsp; <input class="btn" type="submit" name="AddToRelease" value="Add" title="Add stories in this epic, to this release. (No child Epics)">';
				echo '&nbsp; &nbsp; <input class="btn" type="submit" name="DeleteFromRelease" value="Remove" title="Remove stories in this epic, from this release. (Not Chid Epics)">';

				$menu = '&nbsp; <select name="PARID">';
				$menu .= '<option value="0"></option>';
				$menu .= '<option value="P"> ** ENTIRE PROJECT **</option>';
				$menu .= '<option value="D"> ** All "Done" work **</option>';
				$menu .= '<option value="N"> ** All work NOT "Done" **</option>';
				$menu .= '<option value="0"></option>';
// Epics
				$sql = 'select AID, ID, Summary from story where Project_ID= ? and 0<(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by ID';
				$queried = $DBConn->directsql($sql, $Rowp['relproj']);
				foreach($queried as $result) {
					$menu .= '<option value="E' . $result['AID'] . '">Epic ' .$result['ID'].' - '. $result['Summary'] .'</option>';
				}
				$menu .= '<option value="0"></option>';
// Iterations
				$topdate = date_create(Date("Y-m-d"));
				date_add($topdate , date_interval_create_from_date_string('3 months'));
				$topdate = date_format($topdate , 'Y-m-d');
				$sql = 'SELECT ID, Name, Start_Date, End_Date FROM iteration where iteration.Project_ID = ? and ( Start_Date<= ? and iteration.ID<>(select Backlog_ID from project where ID= ?)) order by iteration.End_Date desc LIMIT 10';
				$iter_Res = $DBConn->directsql($sql, array($Rowp['relproj'], $topdate, $Rowp['relproj']));
				foreach ($iter_Res as $iter_Row){
					$menu .= '<option value="I'.$iter_Row['ID'].'">'.$iter_Row['Name'].'</option>';
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

			$sql = 'SELECT * FROM story where project_ID= ? and AID IN('.$instr.') order by story.project_ID, story.Epic_Rank';
			$tree_Res = $DBConn->directsql($sql, $Rowp['relproj']);
			echo '&nbsp; &nbsp;<b><a href="#" class="btnCollapseAll" id="'.$Rowp['relproj'].'">Collapse All</a> / ';
			echo '<a href="#" class="btnExpandAll" id="'.$Rowp['relproj'].'">Expand All</a></b>';
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
		$sql = 'SELECT * FROM story where story.Project_ID= ? and Parent_Story_ID=0 order by story.Epic_Rank';
		$bind = array($_REQUEST['PID']);
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
		$sql = 'SELECT * FROM story_status where story_status.Project_ID= ? and LENGTH(story_status.Desc)>0 order by story_status.`Order`';
		$status_Res = $DBConn->directsql($sql, $_REQUEST['PID']);
		foreach($status_Res as $status_Row ){
		$colcount=$colcount+1;
		echo '<ul name= "'.$_REQUEST['IID'].'" id="status'.$status_Row['Order'].'" class="connectedSortable">';
		echo '<li class="scrumtitle" style="background: #'.$status_Row['RGB'].';">'.$status_Row['Desc'].'</li>';
		$sqls = 'SELECT * FROM story where story.Project_ID= ? and story.Iteration_ID= ? and  0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) and story.Status= ? order by story.Iteration_Rank';
		$story_Res = $DBConn->directsql($sqls, array($_REQUEST['PID'], $_REQUEST['IID'], $status_Row['Desc']));
		foreach ($story_Res as $story_Row){
			echo '<li class="scrumdetail';
			if ($story_Row['Blocked'] != 0){
				echo ' blocked"';
			}else{
				echo '" ';
			}

			echo 'id="'.$story_Row['AID'].'">'.
 				'<a href="story_Edit.php?AID='.$story_Row['AID'].'&PID='.$_REQUEST['PID'].'&IID='.$story_Row['Iteration_ID'].'" title="Edit Story">#'.$story_Row['ID'].'</a>'.
				' - '.substr($story_Row['Summary'], 0, 120).
				'<br>'.html_entity_decode ($story_Row['Col_1'],ENT_QUOTES).'&nbsp;';

			echo '<br>'.$story_Row['Type'].'&nbsp;'.
				'&nbsp;['.$story_Row['Size'].']&nbsp;'.
				'&nbsp;'.Get_User($story_Row['Owner_ID'],1).'&nbsp;'.
				'&nbsp;';
			if( $Project["Enable_Tasks"]==1){
				printMicromenu($story_Row['AID']);
				echo '<div class="hidden" id="alltasks_'.$story_Row['AID'].'"></div>';
				echo '<div class="hidden" id="commentspops_'.$story_Row['AID'].'"></div> ';
				echo '<div class="hidden" id="allupload_'.$story_Row['AID'].'"></div> ';
				echo '<div class="auditdialog hidden" id="allaudits_'.$story_Row['AID'].'"></div> ';
			}
			if($story_Row['Parent_Story_ID'] != 0) {

				$parentssql = 'SELECT @id := (SELECT Parent_Story_ID FROM story WHERE AID = @id and Parent_Story_ID <> 0) AS parent FROM (SELECT @id := ?) vars STRAIGHT_JOIN story  WHERE @id is not NULL';

				$parents_Res =  $DBConn->directsql($parentssql, $story_Row['AID']);
				foreach($parents_Res as $parents_row){
			  		if($parents_row['parent']!=NULL){
						$parentsql='select ID, Summary, Size from story where AID= ? and AID<>0';
						$parent_Row = $DBConn->directsql($parentsql, $parents_row['parent']);
						if (count($parent_Row) == 1)	{
							echo '<a  title="'.$parent_Row[0]['Summary'].'"';
								echo ' href="story_List.php?Type=tree&Root='.$parent_Row[0]['ID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
							echo ' #'.$parent_row[0]['ID'].'('.$parent_Row[0]['Size'].')</a>, &nbsp;';
						}
					}
				}
			}
			echo 	'</li>';
		}
		echo '</ul>';
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
