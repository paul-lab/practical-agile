<?php
/*
* Practical Agile Scrum tool
*
* Copyright 2013-2015, P.P. Labuschagne

* Released under the MIT license.
* https://github.com/paul-lab/practical-agile/blob/master/_Licence.txt
*
* Homepage:
*   	http://practicalagile.co.uk
*	http://practicalagile.uk
*
*/

/*start the sesion to save login variables*/
	session_name('MY_PRACTICALAGILE_LOGIN');
	session_start();

function login_user( $user , $password )
{
	Global $DBConn;

	$sql = "SELECT * FROM user  WHERE EMail = '".$user."'   AND Disabled_User = 0 AND Password = '".md5($password)."'";
	$query = mysqli_query($DBConn, $sql );
	if( mysqli_num_rows($query) > 0 )
	{
		$result = mysqli_fetch_assoc( $query );
		auditit(0,0,$result['EMail'],'Login');
		return  array(
			'session_identifier'=>md5($result['EMail'].$result['Password']),
			'ID'=>$result['ID'],
			'Email'=>$result['EMail'],
			'Friendly_Name'=>$result['Friendly_Name']
			);
	}else{
	//fudge the old password handling
		$sql = "SELECT * FROM user  WHERE email = '".$user."' AND Disabled_User = 0 AND password = '".$password."'";
		$query = mysqli_query($DBConn, $sql );
		// a valid old password
		if( mysqli_num_rows($query) > 0 ){
			$result = mysqli_fetch_assoc( $query );
			// update to the new one
			$sqlu = "Update user SET Password='".md5($password)."' WHERE email = '".$user."'";
			$query = mysqli_query($DBConn, $sqlu );
		}else{
			auditit(0,0,$user,'Unsuccessful Login');
			return false;
		}
	}
}

function check_user( $md5_sum )
{
	Global $DBConn;
	if (empty($_REQUEST['PID'])) {
		$sql = "SELECT * FROM user WHERE Disabled_User = 0 AND MD5(CONCAT(email,password)) = '".$md5_sum."'";
	} else {
		$sql = "SELECT * FROM user  LEFT JOIN user_project ON user.ID = user_project.User_ID WHERE Disabled_User = 0 AND MD5(CONCAT(email,password)) = '".$md5_sum."' and user_project.Project_id=".$_REQUEST['PID'];
	}
	$query = mysqli_query($DBConn, $sql )or die( mysqli_error() );
	$result = mysqli_fetch_assoc( $query );
	return count( $result ) > 0 ? $result : false;
}

function projectadmin($thisproject)
{
	Global $DBConn;

	$sql='select user.Admin_User from user where user.ID='.$_SESSION['ID'];
	$Res=mysqli_query( $DBConn, $sql);
	$Usr=mysqli_fetch_assoc($Res);
	if ($Usr['Admin_User']==1) return 1;

	$sql = 'select Project_Admin from user_project where Project_ID='.$thisproject.' and User_ID='.$_SESSION['ID'];
	if($query = mysqli_query($DBConn, $sql ))
	{
		$result = mysqli_fetch_assoc( $query );
		return count( $result ) > 0 ? $result['Project_Admin'] : false;
	}else{
		return false;
	}
}
function readonly($thisproject)
{
	Global $DBConn;

	$sql = 'select Readonly from user_project where Project_ID='.$thisproject.' and User_ID='.$_SESSION['ID'];
	if($query = mysqli_query($DBConn, $sql ))
	{
		$result = mysqli_fetch_assoc( $query );
		return count( $result ) > 0 ? $result['Readonly'] : false;
	}else{
		return false;
	}
}
//##################################################################

function Start_Timer()
{
	return(microtime(true));
}

function End_Timer($time_start)
{
	$time_end = microtime(true);
	return ($time_end - $time_start);
}



function logit($recstr)
{
	$recstr.="\n";
	file_put_contents('debug.log', $recstr, FILE_APPEND);
}

function auditit($pid='', $aid=0, $user='', $action='', $from='', $to='')
{
	Global $DBConn;
	$sql = "insert into audit (audit.PID, audit.AID, audit.User, audit.action, audit.From, audit.To) VALUES (".$pid.",".$aid.",'".$user."','".$action."','".$from."','".$to."')";
//echo $sql.'<br>';
	$query = mysqli_query($DBConn, $sql );
}


function fetchusingID($col,$val,$tabl)
{
	Global $DBConn;
	if ($tabl=='story')
	{
		$sql='SELECT '.$col.' FROM '.$tabl.' WHERE ID='.$val;
	}else{
		$sql='SELECT '.$col.' FROM '.$tabl.' WHERE AID='.$val;
	}
	$qry = mysqli_query($DBConn, $sql );
	$row = mysqli_fetch_assoc($qry);
	return $row[$col];
}

function PrintStory ($story_Row)
{
	Global $statuscolour;
	Global $Project;
	Global $Sizecount;
	Global $OSizecount;
	Global $Toggle;
	Global $Iterationcount;
	Global $OIterationcount;
	Global $DBConn;
	Global $LockedIteration;

// only for the backlog
	if ($Project['Backlog_ID']==$story_Row['Iteration_ID'])
	{
	// update predictions
	// use average card size or current velocity is ave > velocity for unsized cards.
		if ($story_Row[Size]=="?")
		{
			if ($Project['Velocity'] > $Project['Average_Size'])
			{
				 $Add_This = $Project['Average_Size'];
			}else{
				 $Add_This = $Project['Velocity'];
			}
		}else{
			$Add_This = $story_Row[Size];
		}

		// add the next story even if it overflows (Best Case)
		$OSizecount += $Add_This;
		if ($OSizecount  >= $Project['Velocity'])
		{
			$OSizecount = 0 ;
			$OIterationcount +=1;
		}

		// only use complete stories that fit (Worst Case)
		if ($Sizecount + $Add_This  > $Project['Velocity'])
		{
			$Iterationcount +=1;
			$Sizecount = $Add_This ;
			// toggle the colour bands in the iteration for current velocity
			$Toggle = ($Toggle + 1) % 3;
		} else{
			$Sizecount += $Add_This;
		}
	}

	$Num_Children = Num_Children($story_Row['AID']);

	$class= 'storybox-div ';

	if ($_REQUEST['Type']!='tree'){
		$class.=' alternate'.$Toggle.' ';
	}else{
		$class.=' smaller ';
	}

	if ($story_Row['Blocked'] != 0){
		$class.=' blocked';
	}

// special handling for releases as they cover multiple projects
	if ($_REQUEST['Root']=='iteration' || $_REQUEST['Root']=='release')
	{
		if (!empty($_REQUEST['IID']))
		{
			if ($story_Row['Iteration_ID']==$_REQUEST['IID']){
				$class.='thisiteration';
			}
		}
	}

	echo	'<div class="'.$class.'" id="storybox'.$story_Row['AID'].'">';

	echo '<div class="right-box">';
		if ($_REQUEST['Type']!='tree'){
			echo '<div class="minimenu-div" id="menu_div_'.$story_Row['AID'].'">'.
					'<a href="story_Preview.php?id='.$story_Row['AID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'" target="_blank" title="Print preview a story (Opens in new tab)"><img src="images/preview.png"></a> &nbsp;'.
					'<a class="quickview" id="quickview'.$story_Row['ID'].'" href="" onclick="javascript: return false;" title="Show more/less detail"><img src="images/more.png"></a> &nbsp;'.
					'<a class="statuspopup" href="" onclick="javascript: return false;" title="Change Story Status"><img src="images/status.png"></a> &nbsp;'.

				 	'<a class="iterationpopup" href="" onclick="javascript: return false;" title="Move to different Iteration"><img src="images/move.png"></a> &nbsp;'.
					'<a href="story_Edit.php?AID='.$story_Row['AID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'" title="Edit Story"><img src="images/edit.png"></a> &nbsp;';
			if($LockedIteration==0)
			{
				echo	'<a href="story_Delete.php?id='.$story_Row['AID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'" title="Delete Story"><img src="images/delete.png"></a>';
			}
			echo '</div>';
		}

		echo '<div class="type-div">'.$story_Row['Type'].'</div>';
		echo '<div class="size-div" title="Story Size">&nbsp;';
		echo $story_Row['Size'].'&nbsp;';

// print probable iteration based on current velocity if on backlog
		if (empty($_REQUEST['Type'])){
			if ($Project['Backlog_ID']==$story_Row['Iteration_ID'])
			{
				echo '<div class="predicted-div" title="Predicted last/first iteration after last \'loaded\' iteration">(+'.$Iterationcount.'/'.$OIterationcount.')&nbsp;</div>';
			}
		}
		echo '</div>';	// size-div

	echo '</div>';	//right-box

// set background of drag and drop handle to that of the status (for stories that can be worked on.)
	if($Num_Children == 0)
	{
		echo '<div title="'.$statuspolicy[$story_Row['Status']].'" class="storystatus" style="background: #'.$statuscolour[$story_Row['Status']].'" id="span_div'.$story_Row['AID'].'"></div>';
	}else{
		echo '<div class="parentstorystatus" id="span_div'.$story_Row['AID'].'"></div>';
	}

	echo '<div class="storybody">';

		echo '<div class="line-1-div">';

//display status of any child stories along with the sum of points for that status
		echo '<div class="childrenstatus-div"> ';
		if($Num_Children != 0)
		{
			$astatus=explode(",", $story_Row['Children_Status']);
			for ($i = 0; $i <count($astatus); $i++) {
				$SSize= Get_Status_Points($story_Row['AID'],$astatus[$i],0);
				if ($SSize!=0)
				{
					if ($statuscolour[$astatus[$i]]=='')
					{
						echo '<img title="'.$SSize.' '.$astatus[$i].' points" src="storystatusimage.php?RGB=bfbfbf&ST='.$SSize.'" >';
					}else{
						echo '<img title="'.$SSize.' '.$astatus[$i].' points" src="storystatusimage.php?RGB='.$statuscolour[$astatus[$i]].'&ST='.$SSize.'" >';
					}
				}
			}

	// let me get to a small tree
			echo '<a  title="Show my children (#'.$story_Row['ID'].') as the root of the tree)"';
			echo ' href="story_List.php?Type=tree&Root='.$story_Row['ID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'">';
			echo '<img src="images/tree-small.png"></a>';
		}
		echo '</div>';

		echo '<a href="story_Edit.php?AID='.$story_Row['AID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'" title="Edit Story">#'.$story_Row['ID'].'</a> &nbsp;'.
		' - '.substr($story_Row['Summary'], 0, 150);
		echo '</div>'; // line 1 div

		echo '<div class="line-2-div" id="line-2-div'.$story_Row['ID'].'">';
			echo '<b>'.$Project['Desc_1'].'</b>&nbsp;'.html_entity_decode($story_Row['Col_1'],ENT_QUOTES);
			if ($Project['As_A']){ echo '<div><b>As A: </b>'.html_entity_decode($story_Row['As_A'],ENT_QUOTES).'</div>';}
			if ($Project['Col_2']){ echo '<div><b>'.$Project['Desc_2'].'</b>&nbsp;'.html_entity_decode($story_Row['Col_2'],ENT_QUOTES).'</div>';}
			if ($Project['Acceptance']){ echo '<div><b>Acceptance: </b>'.html_entity_decode($story_Row['Acceptance'],ENT_QUOTES).'</div>';}
		echo '</div>';		// line-2-div

		echo '<div class="line-3-div" id="line-3-div'.$story_Row['ID'].'">';

		if($Num_Children == 0){
	 		echo '<div class="status-div statuspopup" title="Change Story Status" style="background: #'.$statuscolour[$story_Row['Status']].'" id="status_div'.$story_Row['AID'].'">'.$story_Row['Status'].'</div>';
		}
	 		echo '<div class="iteration-div" id="status_div'.$story_Row['AID'].'"> ';
				echo '<a href="story_List.php?&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'#'.$story_Row['AID'].'" title="Goto Iteration">';
			echo Get_Iteration_Name($story_Row['Iteration_ID'],False).'</a></div>';
// print the micromenu
			printMicromenu($story_Row['AID']);

			echo '<div class="owner-div">| '.Get_User($story_Row['Owner_ID'],0).'</div>';

// If I am a child show all my parents
	 		echo '<div class="parents-div"> | ';
			if($story_Row['Parent_Story_ID'] != 0) {
				$parentssql='SELECT @id :=(SELECT Parent_Story_ID FROM story WHERE AID = @id and Parent_Story_ID <> 0 ) AS parent FROM (SELECT @id :='.$story_Row['AID'].') vars STRAIGHT_JOIN story  WHERE @id is not NULL';
				$parents_Res = mysqli_query($DBConn, $parentssql);
				if ($parents_row = mysqli_fetch_assoc($parents_Res))
				{
					do
					{
				  		if($parents_row['parent']!=NULL)
						{
							$parentsql='select ID, AID, Summary, Size from story where AID='.$parents_row['parent'].' and AID<>0';
							$parent_Res = mysqli_query($DBConn, $parentsql);
							if ($parent_row = mysqli_fetch_assoc($parent_Res))
							{
								echo '<a  title="'.$parent_row ['Summary'].'"';
								echo ' href="story_List.php?Type=tree&Root='.$parent_row ['ID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'">';
								echo ' #'.$parent_row ['ID'].' ('.$parent_row ['Size'].' pts)</a>&nbsp;';
							}
						}
					}
					while ($parents_row = mysqli_fetch_assoc($parents_Res));
				}
			}
			echo '</div>';	//Parents

	 		echo '|<div class="tags-div">';
			if(strlen($story_Row['Tags'])!=0){
				$aTags=explode(",",$story_Row['Tags']);
				foreach($aTags as $Tag) {
					echo '<a class="tags-each ui-corner-all" title="Search for tag:'.$Tag.'" href="story_List.php?PID='.$story_Row['Project_ID'].'&searchstring=tag:'.$Tag.'&Type=search">'.$Tag.'</a>';
				}
			}
			echo '</div>';	//tags-div
			echo '<div class="inline right-box" >';
			echo getReleaseName($story_Row['Release_ID']);
			echo '</div>';
			echo '<div class="taskdialog" id="alltasks_'.$story_Row['AID'].'"></div>';
			echo '<div class="commentsdialog" id="commentspops_'.$story_Row['AID'].'"></div> ';
			echo '<div class="uploaddialog" id="allupload_'.$story_Row['AID'].'"></div> ';
			echo '<div class="auditdialog hidden" id="allaudits_'.$story_Row['AID'].'"></div> ';

		echo '</div>'; //line-3-div
	echo '</div>';   // storybody divline-3-div
	echo '</div>';	// storybox-div


}	// Printstory

function GetIterationsforpop($project, $iteration="", $backlog="")
{
	Global $DBConn;
	Global $LockedIteration;

	//===
	$sql = 'SELECT * from iteration where iteration.ID ='.$iteration;
	if($iteration_Res = mysqli_query($DBConn, $sql))
	{
		$IterationList=buildpopup($iteration_Res,$thisdate );
	}

	if ($LockedIteration==0)
	{
// Create the iteration popup
		$thisdate =  date_create(Date("Y-m-d"));
		$thisdate = date_format($thisdate , 'Y-m-d');

// Fetch  future Iterations
		$sql = 'SELECT * from iteration where iteration.Project_ID ='.$project.' and Start_Date>"'.$thisdate.'"'.
		' and ID<>'.$iteration.' order by iteration.Start_Date desc LIMIT 6';
		if($iteration_Res = mysqli_query($DBConn, $sql))
		{
			$IterationList=buildpopup($iteration_Res,$thisdate );
		}

// Fetch the backlog and 4 previous Iterations (if not on the backlog)
		$sql = 'SELECT * from iteration where iteration.Project_ID ='.$project.' and Start_Date<>"0000-00-00" and (Start_Date<="'.$thisdate.'"';
		if ($iteration==$backlog){
			$sql .='and ID<>'.$backlog.')';
		}else{
			$sql .='or ID='.$backlog.')';
		}
		$sql .=' and ID<>'.$iteration.' order by iteration.End_Date desc LIMIT 6';
		if($iteration_Res = mysqli_query($DBConn, $sql))
		{
			$IterationList.=buildpopup($iteration_Res,$thisdate );
		}

// Fetch  No date Iterations
		$sql = 'SELECT * from iteration where iteration.Project_ID ='.$project.' and ID<>'.$iteration.' and Start_Date="0000-00-00"';
		if($iteration_Res = mysqli_query($DBConn, $sql))
		{
			$IterationList.=buildpopup($iteration_Res,$thisdate );
		}
	}
	return $IterationList;
} //GetIterationsforpop


function buildpopup($iteration_Res,$thisdate ){
// build iteration popup
	Global $LockedIteration;
	$IterationList='';

	if(iteration_Res)
	{
		if ($iteration_Row = mysqli_fetch_assoc($iteration_Res))
		{
			do
			{
				if ($iteration_Row['Locked']==0)
				{
					$IterationList.='<button id="'.$iteration_Row['ID'].'"';
					//highlight the current iteration
					if( ($iteration_Row['Start_Date']<=$thisdate) && ($iteration_Row['End_Date'] >= $thisdate) && ($iteration_Row['Name'] != 'Backlog')) {
						$IterationList.=' style="background:#66CCFF" ';
					}
					$IterationList.=' class="ui-button ui-state-default ui-corner-all">';
					$IterationList.=substr($iteration_Row['Name'], 0, 14);
					$IterationList.='</button>&nbsp;&nbsp;';
					$LockedIteration = 0;
				}else{
					if ($iteration_Row['ID']==$_REQUEST['IID'])
					{
						$LockedIteration = 1;
						return 'This iteration is locked!';
					}
				}
			}while ($iteration_Row = mysqli_fetch_assoc($iteration_Res));
		}
	}
	return $IterationList;

}

function buildstatuspop($proj){
	global $statuscolour;
	Global $DBConn;
// Fetch the status and create the status popup.
	$sql = 'SELECT story_status.RGB, story_status.Desc, story_status.Policy FROM story_status where story_status.Project_ID='.$proj.' and LENGTH(story_status.Desc)>0 order by story_status.Order';
	$status_Res = mysqli_query($DBConn, $sql);
	if ($status_Row = mysqli_fetch_assoc($status_Res))
	{
		$statusList='';
		do
		{
			$statuscolour[$status_Row['Desc']] = $status_Row['RGB'];
			$statusList.='&nbsp;&nbsp;<button title="'.$status_Row['Policy'].'" id="'.$status_Row['Desc'];
			$statusList.='" style="background:#'.$status_Row['RGB'].'" class="ui-button ui-state-default ui-corner-all">&nbsp;';
			$statusList.=$status_Row['Desc'];
			$statusList.='&nbsp;</button>';
		}while ($status_Row = mysqli_fetch_assoc($status_Res));
	}
	return ($statusList);
}


function iterations_Dropdown($project, $current,$iterationname='Iteration_ID')
{
	Global $DBConn;
	Global $IterationLocked;

	$current+=0;
	$current_date = Date("Y-m-d");

	// Fetch Current Iteration.
	$sql = 'SELECT * FROM iteration where iteration.ID ='.$current;
	$queried = mysqli_query($DBConn, $sql);
	$result = mysqli_fetch_array($queried);
	$menu = '<select name="'.$iterationname.'"><option value="' . $result['ID'] . '">' . substr($result['Name'], 0, 14) .'</option>';

	$IterationLocked = $result['Locked'];

	if ($result['Locked']==1)
	{
	$menu =$result['Name'].'<select  class="hidden"  name="Iteration_ID"><option value="' . $result['ID'] . '">' . substr($result['Name'], 0, 14) .'</option>';

	}

	// Fetch other iteratons
	$sql = 'SELECT * FROM iteration where iteration.Project_ID ='.$project.' and iteration.ID<>'.$current.' AND Locked=0 order by iteration.End_Date desc';
    	$queried = mysqli_query($DBConn, $sql);

	if ($queried){
		while ($result = mysqli_fetch_array($queried)) {
			// highlight current iteration
			if (( $current_date >= $result['Start_Date'] ) && ( $current_date <= $result['End_Date'] ) && $result['Name'] <> "Backlog"){
				$menu .= '<option value="' . $result['ID'] . '">* ' . $result['Name'] . ' *</option>';
			}else{
				$menu .= '<option value="' . $result['ID'] . '">' . $result['Name'] . '</option>';
			}
		}
	}
	$menu .= '</select>';
	return $menu;

}


function getReleaseName($relid)
{
	Global $DBConn;
	$sql = 'select Name from release_details where ID='.$relid;
	$res = mysqli_query($DBConn, $sql);
	if ($res)
	{
		$rec = mysqli_fetch_assoc($res );
		return $rec['Name'] ;
	}else{
		return '';
	}
} // getRelease


function printMicromenu($AID)
{
Global $DBConn;
	echo '<div class="micromenu-div">';

		echo '<div title="(#done/#count)" class="inline" id="task_count_'.$AID.'">';
			// count the # of tasks and # completed
			$tsql = 'SELECT count(*) as all_count,( select count(*) FROM task where task.story_AID='.$AID.' and task.Done = 2) as done_count FROM task where task.story_AID='.$AID;
			$tres=mysqli_query($DBConn, $tsql);
			$t_row = mysqli_fetch_assoc($tres);
			if ($t_row['all_count'] >0){
				echo ' ('.$t_row['done_count'].'/'.$t_row['all_count'].')';
			}

		echo '</div>';
		echo '<a class="taskpopup" id="'.$AID.'" href="" onclick="javascript: return false;" title="Show Tasks"><img src="images/task-small.png"></a> &nbsp;';

		echo '<div title="Comment count" class="inline" id="comment_count_s_'.$AID.'">';
			$tsql = 'SELECT count(*) as count FROM comment where comment.Story_AID='.$AID;
			$tres=mysqli_query($DBConn, $tsql);
			$t_row = mysqli_fetch_assoc($tres);
			if ($t_row['count'] >0){
				echo ' ('.$t_row['count'].')';
			}
		echo '</div>';
		echo '<a class="commentpopup" id="comments_'.$AID.'" href="" onclick="javascript: return false;" title="Show Comments"><img src="images/comment-small.png"></a> &nbsp;';

		echo '<div title="(# uploads)" class="inline" id="upload_count_'.$AID.'">';
			// count the # of tasks and # completed
			$tsql = 'SELECT count(*) as all_count FROM upload where upload.AID='.$AID;
			$tres=mysqli_query($DBConn, $tsql);
			$t_row = mysqli_fetch_assoc($tres);
			if ($t_row['all_count'] >0){
				echo ' ('.$t_row['all_count'].')';
			}

		echo '</div>';
		echo '<a class="uploadpopup" id="up'.$AID.'" href="" onclick="javascript: return false;" title="Show Uploads"><img src="images/upload-small.png"></a> &nbsp;';

		echo '<a class="auditpopup" id="audits'.$AID.'" href="" onclick="javascript: return false;" title="Show Audit Records"><img src="images/history-small.png"></a> &nbsp;';


	echo '</div>'; // micromenu-div
}

function GetComments($row, $ThisID, $Thiskey)
{
	Global $DBConn;

	echo '<li class="comment" id="comment_'.$row['ID'].'">';
 	echo '<div class="comment-body" id="comment_body_'.$row['ID'].'">'.$row['Comment_Text'].'</div>';
	echo "<div class='aut'>".$row['User_Name']."</div>";
	echo "<div class='timestamp'>".$row['Comment_Date']."</div>";

	echo '<a href="#commentspop'.$Thiskey.'_'.$ThisID.'" class="reply" id="'.$row['ID'].'">Reply</a>';
	/* The following sql checks to see if there are any replies for the comment */
	$q = "SELECT * FROM comment WHERE Parent_ID = ".$row['ID'];
	$r = mysqli_query($DBConn, $q);
	if(mysqli_num_rows($r)>0) // there is at least reply
	{
		echo '<ul id="commentreply_'.$row['ID'].'">';
		while($row = mysqli_fetch_assoc($r)) {
			getComments($row, $ThisID, $Thiskey);
		}
		echo "</ul>";
	} else{
		// can only delete your own comments id there are no replies
		if($row['User_Name']==$_SESSION['Name']){
			echo '<a class="deletecomment" id="deletecomment'.$Thiskey.'_'.$row['ID'].'" href="" onclick="javascript: return false;" title="Delete comment"><img src="images/delete-small.png"></a> &nbsp;';
		}
		echo '<ul id="commentreply_'.$row['ID'].'"></ul>';
	}
	echo "</li>";
} //GetComments

function Get_Iteration_Name($thisiteration,$withdate=True)
{
	Global $DBConn;

	if(!empty($thisiteration))
	{
		GLOBAL $Iteration;

		$SQL='SELECT * FROM iteration where iteration.ID ='.$thisiteration;
		$Res = mysqli_query($DBConn, $SQL);
		$Iteration = mysqli_fetch_assoc($Res);
		if ($Iteration['Locked']==1)
		{
			$prefix='Locked: ';
		}else{
			$prefix='';
		}
		if ($withdate==True and $Iteration['Name']<>'Backlog' ) {
			return $prefix.$Iteration['Name'].' ('.$Iteration['Start_Date'].' -> '.$Iteration['End_Date'].')';
		}else{
			return $Iteration['Name'];
		}
	}
} // Get_Iteration_Name

function Get_Project_Name($thisproject)
{
	Global $DBConn;

	if(!empty($thisproject))
	{
		GLOBAL $Project;
		// leave this at a select *
		$SQL='SELECT * FROM project where project.ID ='.$thisproject;
		$Res = mysqli_query($DBConn,$SQL);
		$Project = mysqli_fetch_assoc($Res);
		return $Project['Name'];
	}
}

function Get_Release_Name($thisrelease)
{
	Global $DBConn;

	if(!empty($thisrelease))
	{
		$SQL='SELECT Name FROM release_details where ID ='.$thisrelease;
		$Res = mysqli_query($DBConn,$SQL);
		$Release = mysqli_fetch_assoc($Res);
		return $Release['Name'];
	}
	return '';
}

function Get_Project_Backlog($thisproject)
{
	Global $DBConn;

	if(!empty($thisproject))
	{
		$SQL='SELECT Backlog_ID FROM project where project.ID ='.$thisproject;
		$Res = mysqli_query($DBConn,$SQL);
		$Project = mysqli_fetch_assoc($Res);
		return $Project['Backlog_ID'];
	}
}

function NextIterationCommentObject()
{
	Global $DBConn;

	$switch = 1;
	do {
		$rand = mt_rand();
		$SQL='SELECT count(ID) AS CNT FROM iteration where `Comment_Object_ID` ='.$rand;
		$Res = mysqli_query($DBConn, $SQL);
		$Row = mysqli_fetch_assoc($Res);
		$switch = $Row['CNT'];
	} while ($switch != 0);

	return $rand;
}


function NextPointsObject()
{
	Global $DBConn;

	$switch = 1;
	do {
		$rand = mt_rand();
		$SQL='SELECT Count(ID) AS CNT FROM points_log where `Object_ID` ='.$rand;
		$Res = mysqli_query($DBConn, $SQL);
		$Row = mysqli_fetch_assoc($Res);
		$switch = $Row['CNT'];
	} while ($switch != 0);
	mysqli_query($DBConn, "INSERT INTO points_log SET Object_ID = {$rand}");
	return $rand;
}


function Show_Project_Users($ThisProject=0, $current,$name,$disabled=0)
{
	Global $DBConn;;
	if ($ThisProject>0)
	{
		if (empty($current)) $current = '0';
		if (empty($disabled)) $disabled = '0';
		$menu = '<select id="'.$name.'" name="'.$name.'" ';
		if ($disabled==1){$menu .= ' disabled="disabled"';}
		$menu .= '>';
		$sql = 'SELECT ID, Friendly_Name FROM user WHERE user.ID='.$current;
		$queried = mysqli_query($DBConn, $sql);
		$result = mysqli_fetch_array($queried);
		$menu .= '<option value="' . $result['ID'] . '">' . $result['Friendly_Name'] .'</option>';
		if (!empty($current)) $menu .='<option value=""></option>';

		$sql = 'SELECT ID, Friendly_Name FROM user LEFT JOIN user_project ON user.ID = user_project.USER_ID where user_project.Project_ID='.$ThisProject.' and user.Disabled_User = 0 ORDER BY Friendly_Name';
		$queried = mysqli_query($DBConn, $sql);

		while ($result = mysqli_fetch_array($queried)) {
		$menu .= '<option value="' . $result['ID'] . '">' . $result['Friendly_Name'] .'</option>';
	    }
	$menu .= '</select>';
	$story_Row['Size']=$result['Value'];
	return $menu;
	}
}

function Get_User($current,$initials=0)
{
	Global $DBConn;

	if (!empty($current))
	{
		$sql = 'SELECT ID, Initials, Friendly_Name FROM user Where ID='.$current;
		$queried = mysqli_query($DBConn, $sql);
		$result = mysqli_fetch_array($queried);
		if ($initials==0){
			return $result['Friendly_Name'];
		}else{
			return $result['Initials'];
		}
	}
}


function Get_Hint()
{
	Global $DBConn;

	$sql = 'SELECT * FROM `hint` WHERE ID >= (SELECT FLOOR( MAX(ID) * RAND()) FROM `hint` ) ORDER BY id LIMIT 1';
	$queried = mysqli_query($DBConn, $sql);
	$result = mysqli_fetch_array($queried);
	return $result['Hint_Text'];
}

function Update_Parent_Points($thisstory)
{
	Global $DBConn;
// a list of parents
	$sql='SELECT @r AS _aid, ( SELECT @r := Parent_Story_ID FROM story WHERE AID = _aid ) AS parent FROM (SELECT  @r := '.$thisstory.') vars, story h WHERE @r <> 0';
	if ($parent_res = mysqli_query($DBConn, $sql))
	{
		If ($prow = mysqli_fetch_array($parent_res))
		{
			do
			{
				$psql = 'Update story set Status=NULL, Size = (select sum(Size) from (select * from story) as p where p.Parent_Story_ID='.$prow[1].') where story.AID ='.$prow[1];
				mysqli_query($DBConn, $psql);
				Update_Parent_Status($prow[1]);
			} While ($prow = mysqli_fetch_array($parent_res));
		}
	}
}

function Update_oldParent_Points($thisstory)
{
	Global $DBConn;
	$psql = 'Update story set Size = (select sum(Size) from (select * from story) as p where p.Parent_Story_ID='.$thisstory.') where story.AID ='.$thisstory;
	mysqli_query($DBConn, $psql);
	$psql = 'select Iteration_ID from story where story.AID ='.$thisstory;
	$Res = mysqli_query($DBConn, $psql);
	$Row = mysqli_fetch_assoc($Res);
	Update_Iteration_Points($Row['Iteration_ID']);
	Update_Parent_Status($thisstory);
}


function Update_Parent_Status($thisstory)
{
	Global $DBConn;
// update  child story status if there are still children, other wise reset the status to todo
	if (Num_Children($thisstory)>0)
	{
		$sql = 'Select story.Children_Status, story.Status, story.Iteration_ID from story where Parent_Story_ID='.$thisstory. ' order by story.Status';
		$Res=mysqli_query($DBConn, $sql);
		$status='';
		While ($Row = mysqli_fetch_assoc($Res))
		{
			if ($Row['Status'])
			{
				$status.=$Row['Status'].',';
			}
			if ($Row['Children_Status'])
			{
				$status.=$Row['Children_Status'].',';
			}
		}
		$astatus=explode(",", $status);
		// concatenate the unique status values and trim the extra comma at the end
		$status = substr(implode(",", array_unique($astatus)), 0, -1);

		$sql = 'Update story set story.Children_Status="'.$status.'" where story.AID='.$thisstory;
		$Res=mysqli_query($DBConn, $sql);
	}else{
		$sql = 'Update story set story.Status="Todo", story.Children_Status="" where story.AID='.$thisstory;
		$Res=mysqli_query($DBConn, $sql);
	}
}

function Top_Parent($StoryAID)
{
	Global $DBConn;
	$sql = 'SELECT  @r AS _id, (SELECT  @r := Parent_Story_ID FROM story WHERE AID = _id) AS parent, @l := @l + 1 AS level FROM (SELECT  @r :='.$StoryAID.', @l := 0) vars, story WHERE @r <> 0 order by level desc limit 1';
	$Res=mysqli_query($DBConn, $sql);
	if ($Res)
	{
		$Row = mysqli_fetch_assoc($Res);
		return $Row['_id'];

	}else{
		return 0;
	}
}

function Num_Children($storyAID)
{
	Global $DBConn;
	if (empty($storyAID)) return 0;

	$psql = 'select count(AID) as children from story where Parent_Story_ID='.$storyAID;
	if ($res=mysqli_query($DBConn, $psql))
	{
		$rec=mysqli_fetch_assoc($res);
		return $rec['children'];
	}else{
		return 0;
	}
}


function Get_Status_Points($thisstory,$thisstatus,$sumx)
{
	Global $DBConn;
	$sum+=sumx;

	$sql='SELECT story.AID, story.Size, story.Status from story where story.Parent_Story_ID='.$thisstory;
	if ($res = mysqli_query($DBConn, $sql))
	{
		If ($prow = mysqli_fetch_array($res))
		{
			do
			{
				if (Num_Children($prow['AID'])>0)
				{
					 $sum+=Get_Status_Points($prow['AID'],$thisstatus,$sum);
				}else{
					if ($prow['Status']==$thisstatus)
					{
						$sum+=$prow['Size'];
					}
				}
			} While ($prow = mysqli_fetch_array($res));
		}
	}
	return $sum;
}

function Update_Iteration_Points($thisiteration)
{
	Global $DBConn;
	// this is a LOCAL $Iteration, not the global one for the current oteration.
	$sql='select * from iteration where iteration.ID='.$thisiteration;
	$Iter=mysqli_query($DBConn, $sql);
	$Iteration=mysqli_fetch_assoc($Iter);

	$today = date_create(Date("Y-m-d"));
	$today = date_format($today , 'Y-m-d');

// make sure that changes in the iteration are reflected in the iteration
	if ($today > $Iteration['End_Date']){
		$today = $Iteration['End_Date'];
	}
	if ($today < $Iteration['Start_Date']){
		$today = $Iteration['Start_Date'];
	} 

	$thisproject = $Iteration['Project_ID'];

// delete any points for today
	$qry='delete from points_log where points_log.Object_ID ='.$Iteration['Points_Object_ID'].' and (Points_Date="'.$today.' 00:00:00" or Points_Date="0000-00-00 00:00:00")';

	$result=mysqli_query($DBConn, $qry);

//get the points for each status for the iteration
	$sql='select Project_ID, count(ID) as Story_Count, story.Status, sum(story.Size) as Size from story where story.Iteration_ID='.$thisiteration.' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) group by story.Status';
	$iSize=0;
	$story_Res = mysqli_query($DBConn, $sql);
	if ($story_Row = mysqli_fetch_assoc($story_Res))
	{
		do
		{

			$qry2='insert into points_log set '.
			' Project_ID ='.$Iteration['Project_ID'].
			', Points_Claimed ='.$story_Row['Size'].
			', Story_Count ='.$story_Row['Story_Count'].
			', points_log.Object_ID = '.$Iteration['Points_Object_ID'].
			', Points_Date="'.$today.'"'.
			', points_log.Status="'.$story_Row['Status'].'"';
			$result2=mysqli_query($DBConn, $qry2);
			$iSize+=$story_Row['Size'];
		} while ($story_Row = mysqli_fetch_assoc($story_Res));
	}
	
	Update_Project_Points($thisproject);
	// Iteration total points
	return $iSize;
}

function Update_Project_Points($thisproject)
{
	Global $DBConn;

	$today = date_create(Date("Y-m-d"));
	$today = date_format($today , 'Y-m-d');

	$qry='delete from points_log where points_log.Object_ID = (select Points_Object_ID from Project where Project.ID='.$thisproject.') and (Points_Date="'.$today.'" or Points_Date="0000-00-00 00:00:00")';
	$result=mysqli_query($DBConn, $qry);
	$sql = 'select Status, Project_ID, sum(Size) as Sizes, count(AID) as Story_Count from story where story.Project_ID ='.$thisproject.' and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) group by Status';
	$Project_Res = mysqli_query($DBConn, $sql);
	if ($Project_Res){
		if ($Project_Row = mysqli_fetch_assoc($Project_Res))
		{
			do
			{
				$qry2='insert into points_log set '.
					' Project_ID ='.$thisproject.
					', Points_Claimed ='.$Project_Row['Sizes'].
					', Story_Count ='.$Project_Row['Story_Count'].
					', points_log.Object_ID ='. '(select Points_Object_ID from project where project.ID='.$thisproject.')'.
					', Points_Date="'.$today.'"'.
					', points_log.Status="'.$Project_Row['Status'].'"';
				$result2=mysqli_query($DBConn, $qry2);
			}
			while ($Project_Row = mysqli_fetch_assoc($Project_Res));
		}
	}

// this is to make sure that velocity is calculated correctly where we have less than 5 completed iterations in the project.
	$tsql = "SELECT Count(ID) as Done_Iterations from iteration where iteration.Project_ID=".$thisproject." and iteration.End_Date <= '".$today."' and iteration.Name <>'Backlog'";
	$tres = mysqli_query($DBConn, $tsql);
	$trow=mysqli_fetch_assoc($tres);
	$Done_Iterations=$trow['Done_Iterations'];
	if ($Done_Iterations > 5) $Done_Iterations = 5;
	if ($Done_Iterations < 1) $Done_Iterations = 1;

// update project velocity. (the average of the most recent 5 iterations [including the current one.])
// in other words, what would our velocity be if the iteration ended now.
// Select most recent 5 or fewer iterations based on Start date to make sure we incude the current iteration.
// $sqlii= "SELECT SUBSTRING_INDEX(GROUP_CONCAT(ID ORDER BY  Start_Date DESC), ',', ".$Done_Iterations.") as IDS from iteration where iteration.Project_ID=".$thisproject." and iteration.Start_Date <= '".$today."' and iteration.Name <>'Backlog'";


//Select most recent 5 (or less) most recent COMPLETED iterations  to get historic velocity. (velocity will be accurate if the iteration ends on the current date.)
	$sqlii= "SELECT SUBSTRING_INDEX(GROUP_CONCAT(ID ORDER BY  End_Date DESC), ',', ".$Done_Iterations.") as IDS from iteration where iteration.Project_ID=".$thisproject." and iteration.End_Date <= '".$today."' and iteration.Name <>'Backlog'";
	$iires=mysqli_query($DBConn, $sqlii);
	$iirow=mysqli_fetch_assoc($iires);
	$pieces = count(explode(",", $iirow['IDS']));
	if ($pieces > 0){
		$sqli= "select sum(Size)/".$pieces." from  story  where story.Iteration_ID in (".$iirow['IDS'].") and story.Status='Done'";
		$sql='Update project Set Velocity=('. $sqli.') where project.ID='.$thisproject;
		mysqli_query($DBConn, $sql);
	}
// update project card Average_Size to use when predicting unsized cards.
	$sql= "Update Project set Average_Size=(SELECT avg(Size) as Average_Size from story where story.Project_ID=".$thisproject." and Size> 0) where project.ID=".$thisproject;
	mysqli_query($DBConn, $sql);

}

function print_releasesummary($proj,$tsql){
	Global $DBConn;
	$Res=mysqli_query($DBConn, $tsql);
	$s='<table align="center" width=20%><tr><td class="larger">'.Get_Project_Name($proj).'</td>';
	$c='<tr><td bgcolor="#F2F2F2" align="Right" class="larger">Cards</td>';
	$p='<tr><td bgcolor="#F2F2F2" align="Right" class="larger">Points</td>';
	while  ($Row = mysqli_fetch_assoc($Res))
	{
		$s.='<td align="center" class="larger"  bgcolor="#F2F2F2"><b>'.$Row['Status'].'<b></td>';
		$c.='<td align="center" bgcolor="#FaF2F2">'.$Row['relcount'].'</td>';
		$p.='<td align="center" bgcolor="#FaF2F2">'.$Row['relsize'].'</td>';
	}
	$s.='</tr>';
	$p.='</tr>';
	$c.='</tr></table>';
	echo $s.$p.$c;

}


function print_summary($object, $WithVelocity=False){
	Global $DBConn;
	Global $Project;
	echo '<img class="showSummary" id="'.$object.'" src="images/add-small.png" title="Show Summary">';
	$sql = 'select Points_Date, Status, Story_Count, Points_Claimed as Size from points_log where points_log.Object_ID='.$object.' and Points_Date <> "2199-12-31" group by Points_Date Desc, (select min(story_status.Order) from story_status where story_status.Project_ID='.$Project['ID'].' and story_status.Desc = points_log.Status)';

    	$queried = mysqli_query($DBConn, $sql);

	$l1='';
	$l2='';
	$l3='';

	if ($queried)
	{
		if ($result = mysqli_fetch_assoc($queried))
		{
			$last_Date=$result['Points_Date'];
			$l1='<th bgcolor="#F2F2F2"><img class="hideSummary" src="images/minus-small.png" title="Hide Summary"></th>';
			$l2='<td bgcolor="#F2F2F2" align="Right">Cards:</td>';
			$l3='<td bgcolor="#F2F2F2" align="Right">Points:</td>';
			$t1=0;
			$t2=0;
			do
			{
				$l1.=	'<th bgcolor="#F2F2F2">'.$result['Status'].'</th>';
				$l2.=	'<td align="center" bgcolor="#F2F2F2" class="larger">'.$result['Story_Count'].'</td>';
				$l3.=	'<td align="center" bgcolor="#F2F2F2" class="larger">'.$result['Size'].'</td>';
				$t1+=$result['Story_Count'];
				$t2+=$result['Size'];
			}
			while ($result = mysqli_fetch_assoc($queried) and $last_Date==$result['Points_Date']);
			$l1.=	'<th bgcolor="#F2F2F2">Total</th>';
			$l2.=	'<td bgcolor="#F2F2F2" class="larger"><b>'.$t1.'</b></td>';
			$l3.=	'<td bgcolor="#F2F2F2" class="larger"><b>'.$t2.'</b></td>';
		}
	}
	echo '<table class="SummaryTable" cellpadding="2" cellspacing="1" border="0" >';

	if ($WithVelocity==True)
	{
		$l1.='<th>&nbsp;&nbsp;</th><th title="Average of 5 most recent completed iterations" align="center" class="evenlarger">Velocity</th>';
		$l3.='<td>&nbsp;&nbsp;</td><td align="center" class="evenlarger"><b>'.$Project['Velocity'].'</b></td>';
		$l2.='<td>&nbsp;&nbsp;</td><td align="center" class="larger">(5 sprint <span style="text-decoration: overline" >x</span>)</td>';
	}
	echo '<tr>'.$l1.'</tr>';
	echo '<tr>'.$l3.'</tr>';
	echo '<tr>'.$l2.'</tr>';
	echo '</table>';
}


function print_Graphx($object, $small=False){
	Global $DBConn;

	if ($small==False)
	{
		echo '<div class="chart_div" id="chart2'.$object.'" style="width:70%; height: 250px;"></div>';
	}else{
		echo '<div class="chart_div" id="chart2'.$object.'" style="width:250px; height: 100px;"></div>';
	}
	$d1='series: [';

	$dd1='????: [';
	$o1='seriesColors: [';
	$tick='[';

// Get all the different status values applicable to this project.
	$sql = 'SELECT * FROM story_status where Project_ID='.$_REQUEST['PID'].' order by story_status.Order';
	$sat_Res = mysqli_query($DBConn, $sql);
	$idx=0;
	if ($sta_Row = mysqli_fetch_assoc($sat_Res))
	{
		do
		{
			if (strlen($sta_Row['Desc'])!=0){
				$idx+=1;
				$d1.='{label: "'.$sta_Row['Desc'].'"},';
				$o1.='"#'.$sta_Row['RGB'].'",';
				$sta_id[$sta_Row['Desc']] = $idx;
			}
		}while ($sta_Row = mysqli_fetch_assoc($sat_Res));
		$o1 = substr($o1, 0, -1);
		$d1 = substr($d1, 0, -1);
		$o1.='], ';
		$d1.='], ';
	}

// limit the number of ticks on the x axis to 8+2=10
	$sql ='select count(distinct Points_Date) as ndates from points_log where points_log.Object_ID='.$object;
    	$queried = mysqli_query($DBConn, $sql );
	if ($result = mysqli_fetch_assoc($queried))
	{
		$modit = ceil($result['ndates']/8);
		$top=$result['ndates'];
	}else{
		$modit = 1;
	}

// get the dates
	$sql ='select distinct Points_Date  from points_log where points_log.Object_ID='.$object.' and Points_Date <> "2199-12-31" order by Points_Date';
    	$queried = mysqli_query($DBConn, $sql );

	$idx=0;
	if ($result = mysqli_fetch_assoc($queried))
	{
		do
		{
			$idx+=1;
			$dat_id[$result['Points_Date']]=$idx;
			if ($idx % $modit == 0 || $idx ==1 || $idx==$top)
			{
				$tick .="[".$idx.",'".substr($result['Points_Date'],0,10)."'],";
			}
		}while ($result = mysqli_fetch_assoc($queried));
	}

	$tick = substr($tick ,0, -1);
	$tick .=']';
  	//create a 2d array [status][date]
	$a = array_fill(0, count($sta_id)+1, array_fill(0,$idx, 0));

	$sql = 'select Points_Date, Status, Story_Count, Points_Claimed as Size from points_log where points_log.Object_ID='.$object.' order by  Points_Date, Status';

    	$queried = mysqli_query($DBConn, $sql );
	if ($result = mysqli_fetch_assoc($queried))
	{
		do
		{
			$a[$sta_id[$result ['Status']]][$dat_id[$result ['Points_Date']]-1] = $result ['Size'];
		}
		while ($result = mysqli_fetch_assoc($queried));
	}


	echo '<div id="customTooltipDiv">tooltip.</div>';
	echo '<script> $(document).ready(function(){';
	$va='';
	for ($i = 1; $i<= count($sta_id); $i++)
	{
		echo 'var a'.$i.'=['.implode(',',$a[$i]).'];';
		$va .= ' a'.$i.',';
	}
	$va = substr($va, 0, -1);
	echo 'var ticks='.$tick.';';
	echo " plot2".$object." = $.jqplot('chart2".$object."',[";
	echo $va;
	echo '],{
	stackSeries: true,
	       showMarker: false,
	       highlighter: {
	        show: true,
	        showTooltip: false
	       },
	       seriesDefaults: {
	           fill: true,
       },';
	if ($small==False)
	{
		echo '	legend: {
		renderer: $.jqplot.EnhancedLegendRenderer,
	        show: true,
		location: "n",
		rendererOptions: {
	        numberRows: 1
		},
		 placement: "insideGrid",
	       },';
	}

	echo	 $d1.' '.
   	' '.$o1.' '.
       'grid: {
        drawBorder: true,
        shadow: false
       },  axes: {
		yaxis:{ min: 0},
           xaxis: {
              ticks: ticks,
              tickRenderer: $.jqplot.CanvasAxisTickRenderer,
              tickOptions: {';
	if ($small==True)
	{
		echo 'show: false,';
	}
		echo ' angle: -20
              },
              drawMajorGridlines: true
          }
        }
    });';

	echo  "   // capture the highlighters highlight event and show a custom tooltip.
    $('#chart2".$object."').bind('jqplotHighlighterHighlight',
        function (ev, seriesIndex, pointIndex, data, plot) {
            var content = plot.series[seriesIndex].label + ', ' + data[1];
            var elem = $('#customTooltipDiv');
            elem.html(content);
            var h = elem.outerHeight();
            var w = elem.outerWidth();
            var left = ev.pageX - w - 10;
            var top = ev.pageY - h - 10;
            elem.stop(true, true).css({left:left, top:top}).fadeIn(200);
        }
    );
    // Hide the tooltip when unhighliting.
    $('#chart2".$object."').bind('jqplotHighlighterUnhighlight',
        function (ev) {
            $('#customTooltipDiv').fadeOut(300);
      }
   );";

echo' });
</script>';

?>

<style type="text/css">
.jqplot-target {
    margin: 10px;
}

#customTooltipDiv {
    position: absolute;
    display: none;
    color: #333333;
    font-size: 0.8em;
    border: 1px solid #666666;
    background-color: rgba(160, 160, 160, 0.2);
    padding: 2px;
}
</style>

<?php

};	//  print_Graphx

?>