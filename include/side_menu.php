<link rel="stylesheet" type="text/css" href="css/side_menu.css" />
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

echo '<div id="sidemenucontainer">';
echo '<ul id="nav" class="drop">';
echo '<li><a title="Practical Agile" href="http://www.practicalagile.co.uk"><img src="images/logo.png"></a></li>';

// global is this a Admin bloke Stuff/readonly
	$sql='select user.Admin_User from user where user.ID='.$_SESSION['ID'];
	$Usrx=$DBConn->directsql($sql);
	$Usr=$Usrx[0];
	$isProjectAdmin = projectadmin($_REQUEST['PID']);
	$isReadonly=readonly($_REQUEST['PID']);
	echo '<script>';
	echo '	var JisReadonly='.$isReadonly.'+0;';
	echo '</script>';

// Logged on User Stuff
	echo '<li><a href="#" title="'.$_SESSION['Name'].'">&nbsp;'.$_SESSION['Name'].'&nbsp;</a><ul>';
	echo '<li><a href="project_List.php" title="My Projects">My Projects</a>';
	$sql = 'SELECT distinct ID, Category, Name, Velocity, Backlog_ID, Points_Object_ID, Archived FROM project LEFT JOIN user_project ON project.ID = user_project.Project_ID ';
	if ($Usr['Admin_User'] != 1 ){
		$sql .=' where user_project.User_ID='.$_SESSION['ID'].' and project.Archived <> 1';
	}
	$sql.=' order by Category, Name';

	$project_Res=$DBConn->directsql($sql);
	echo '<ul class="l2">';
	foreach ($project_Res as $project_Row){
	echo '<li><a href="project_Summary.php?PID='.$project_Row['ID'].'">&nbsp;- '.$project_Row['Name'].'</a></li>';
	}

	echo '</ul></li>';
		echo '<li><a href="user_Edit.php?id='.$_SESSION['ID'].'" title="Edit My Details">Edit My Details</a></li>';
		echo '<li><a href="_faq.txt" target="_blank" title="FAQ">FAQ</a></li>';
		echo '<li><a href="help/help.html" target="_blank" title="Help">Help</a></li>';
		echo '<li><a href="about.php" target="_blank" title="About">About</a></li>';
		echo '<li><a href="_Licence.txt" target="_blank" title="License">License (MIT)</a></li>';
		echo '<li></li>';
		echo '<li><a href="logout.php" title="Logout">Logout</a></li>';
	echo '</ul></li>';



if ($Usr['Admin_User']==1 || $isProjectAdmin)
{
	$sql = 'SELECT * FROM release_details';
	$sql.=' order by End';
	$rel_Row = $DBConn->directsql($sql);
	$rels= '';
	if (count($rel_Row) >0)
	{
		$rels= '<ul class="l2">';
		$rowcnt=0;
		do
		{
			$rels.=  '<li><a href="story_List.php?RID='.$rel_Row[$rowcnt]['ID'].'&Type=tree&Root=release">&nbsp;- '.$rel_Row[$rowcnt]['Name'].'</a></li>';
			$rowcnt+=1;
		}
		while ($rowcnt<count($rel_Row));
	$rels.='</ul>';
	}
}

if ($Usr['Admin_User']==1)
{
	echo '<li><a href="#" title="Organisation Admin">&nbsp;Org. Config.&nbsp;</a><ul>';
		echo '<li><a href="releaseDetails_List.php">Release Planning</a>';
		echo $rels;
		echo '</li><li></li>';
		echo '<li><a href="project_Edit.php">New Project</a></li>';
		echo '<li><a href="user_List.php">User Admin</a></li>';
		echo '<li><a href="sizeType_List.php">Story Size Type</a></li>';
		echo '<li><a href="size_List.php">Story Sizes</a></li>';
		echo '<li><a href="report_List.php">Reports Admin</a></li>';
		echo '<li><a href="hint_Import.php">Import Hints</a></li>';
		echo '<li>';
		echo '<li><a href="audit_Truncate.php">Truncate Audit table</a></li>';
	echo '</ul></li>';
}

// Project specific Stuff
if (isset($_REQUEST['PID']))
{
	echo '<li><a href="#">&nbsp;'.Get_Project_Name($_REQUEST['PID']).'&nbsp;</a><ul>';
	echo '<li><a href="project_Summary.php?PID='.$_REQUEST['PID'].'">Project Summary</a></li>';
	echo '<li><a href="iteration_List.php?PID='.$_REQUEST['PID'].'">Iterations (& history)</a></li>';
	echo '<li></li>';
	if ($isProjectAdmin )
	{
		echo '<li><a href="releaseDetails_List.php">Release Planning</a>';
		echo $rels;
		echo '</li>';
		echo '<li><a href="project_Edit.php?PID='.$_REQUEST['PID'].'">Edit Project</a></li>';
		echo '<li><a href="storyType_List.php?PID='.$_REQUEST['PID'].'">Story Type</a></li>';
		echo '<li><a href="storyStatus_List.php?PID='.$_REQUEST['PID'].'">Story Status</a></li>';
		echo '<li><a href="tags_Reset.php?PID='.$_REQUEST['PID'].'">Clear unused Tags</a></li>';
		echo '<li></li>';
	}
	echo '<li><a href="story_Import.php?PID='.$_REQUEST['PID'].'&etype=project">Import Stories</a></li>';
	echo '<li><a href="story_Export.php?PID='.$_REQUEST['PID'].'&etype=project">Export Project</a></li>';
	echo '</ul></li>';

// Reports
	echo '<li><a href="#">&nbsp;Reports&nbsp;</a><ul>';
	$sql = 'SELECT queries.ID, queries.Desc, queries.External FROM queries where  queries.External!=0 order by Qseq';
	$Row = $DBConn->directsql($sql);
	if (count($Row) >0)
	{
	$rowcnt=0;
		do
		{
			if($Row[$rowcnt]['External']==1){$rtyp="story";}else{$rtyp="reportraw";};
			echo '<li>'.
				'<a title = "Export '.$Row[$rowcnt]['Desc'].'" href="'.$rtyp.'_Export.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'&etype='.$Row[$rowcnt]['Desc'].'&QID='.$Row[$rowcnt]['ID'].'"><img src="images/export-small.png"></a>&nbsp;&nbsp;'.
				'<a title = "'.$Row[$rowcnt]['Desc'].'" href="'.$rtyp.'_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'&Type=search&QID='.$Row[$rowcnt]['ID'].'">'.$Row[$rowcnt]['Desc'].'</a>'.
			'</li>';
			$rowcnt+=1;
		} while ($rowcnt < count($Row));
	}
	echo '</ul></li>';

// Iterations Stuff
	$topdate = $thisdate =  date_create(Date("Y-m-d"));
	$thisdate = date_format($thisdate , 'Y-m-d');
	date_add($topdate , date_interval_create_from_date_string('3 months'));
	$topdate = date_format($topdate , 'Y-m-d');
	echo '<li><a href="#">&nbsp;Sprint&nbsp;</a><ul>';
		if (isset($_REQUEST['IID'])) {
			echo '<li><a href="story_Export.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">Export '.Get_Iteration_Name($_REQUEST['IID'],False).'</a></li>';
		}
// Iteration stuff
//	echo '<li></li>';
		echo '<li><a href="iteration_Planning.php?PID='.$_REQUEST['PID'].'">Sprint Planning</a></li>';
		echo '<li><a href="story_Estimation.php?PID='.$_REQUEST['PID'].'">Story Estimation</a></li>';
	echo '<li></li>';
// fetch the backlog (no scrum board option)
	$sql = 'SELECT ID, Name, ( select count(AID) from story where story.Iteration_ID=(select project.Backlog_ID from project where project.ID="'.$_REQUEST['PID'].'") and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) ) as NumStories, ( select Sum(Size) from story where story.Iteration_ID=(select project.Backlog_ID from project where project.ID="'.$_REQUEST['PID'].'") and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) ) as SumPoints FROM iteration where iteration.ID =(select project.Backlog_ID from project where project.ID='.$_REQUEST['PID'].')';

	$iteration_Row = $DBConn->directsql($sql);
	if (count($iteration_Row) >0)
	{
		echo
			'<li>'.
			'<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[0]['ID'].'" title = "Product Backlog">'.
			substr($iteration_Row[0]['Name'], 0, 14).'</a>';
			echo '<div class="smaller">';
			if ($iteration_Row[0]['NumStories']>0)
			{
				echo '<a title = "Sprint Epic Tree" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[0]['ID'].'&Type=tree&Root=iteration"><img src="images/tree-small.png"></a>';
				echo
					'&nbsp;&nbsp;'.$iteration_Row[0]['SumPoints'].' pts.'.
					'&nbsp;&nbsp;&nbsp;'.
					$iteration_Row[0]['NumStories'].' stories';
					echo '<a target="_blank" title = "Print Story Cards" href="iteration_Preview.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[0]['ID'].'"> <img src="images/preview-small.png"></a>';
			}
			echo	'</div>';
		echo '</li>';
	}
	echo '<li></li>';
// fetch the iterations
	$sql = 'SELECT ID, Name, Start_Date, End_Date, (select count(story.ID) from story where story.Iteration_ID = iteration.ID) as NumStories, (select sum(story.Size) from story where story.Iteration_ID = iteration.ID) as SumPoints FROM iteration where iteration.Project_ID ='.$_REQUEST['PID'].' and ( Start_Date<="'.$topdate.'" and iteration.ID<>(select Backlog_ID from project where ID="'.$_REQUEST['PID'].'")) order by iteration.End_Date desc LIMIT 10';
	$iteration_Row = $DBConn->directsql($sql);
	if (count($iteration_Row)>0)
	{
		$rowcnt=0;
		do
		{
			echo '<li><a title = "Radiator Board" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[$rowcnt]['ID'].'&Type=board"><img src="images/board-small.png"></a>'.
				'&nbsp;&nbsp;<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[$rowcnt]['ID'].'"'.
				' title="Iteration list '.$iteration_Row[$rowcnt]['Start_Date'].' -> '.$iteration_Row[$rowcnt]['End_Date'].'">';
			$current_date = Date("Y-m-d");
			if ( ( $current_date >= $iteration_Row[$rowcnt]['Start_Date'] ) && ( $current_date <= $iteration_Row[$rowcnt]['End_Date'] ) ){
				echo '<b>'.substr($iteration_Row[$rowcnt]['Name'], 0, 14).'</b>';
			}else{
				echo substr($iteration_Row[$rowcnt]['Name'], 0, 14);
			}
			echo '</a>';
			echo '<div class="smaller">';
			if ($iteration_Row[$rowcnt]['NumStories']>0)
			{
				echo '<a title = "Iteration Epic Tree" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[$rowcnt]['ID'].'&Type=tree&Root=iteration"><img src="images/tree-small.png"></a>';
				echo	'&nbsp;&nbsp;'.$iteration_Row[$rowcnt]['SumPoints'].' pts.'.
					'&nbsp;&nbsp;&nbsp;'.
					$iteration_Row[$rowcnt]['NumStories'].' stories';
					echo '<a target="_blank" title = "Print Story Cards" href="iteration_Preview.php?PID='.$_REQUEST['PID'].'&IID='.$iteration_Row[$rowcnt]['ID'].'"> <img src="images/preview-small.png"></a>';
			}
			echo	'</div>';
			echo '</li>';
			$rowcnt+=1;
		}
		while ($rowcnt < count($iteration_Row));
	}
	echo'<li><a href="iteration_List.php?PID='.$_REQUEST['PID'].'" title="More Iterations">More ... </a></li>';
	echo '</ul></li>';
}

	echo '</ul></div>';
?>
