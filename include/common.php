<?php
/*
* Practical Agile Scrum tool
*
* Copyright 2013-2017, P.P. Labuschagne
*
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

function login_user( $user , $password ){
	Global $DBConn;
	$sql = "SELECT * FROM user  WHERE EMail = ? AND Disabled_User = 0 AND Password = ?";
	$result=$DBConn->directsql($sql, array($user, md5($password)));
	if( count($result) == 1 )	{
		$result=$result[0];
		auditit(0,0,$result['EMail'],'Login');
		return  array(
			'session_identifier'=>md5($result['EMail']),
			'ID'=>$result['ID'],
			'Email'=>$result['EMail'],
			'Friendly_Name'=>$result['Friendly_Name']
			);
	}else{
	//fudge the old password handling
		$sql = "SELECT * FROM user  WHERE email = ? AND Disabled_User = 0 AND password = ?";
		$result=$DBConn->directsql($sql, array($user, $password));
		// a valid old password
		if( count($result) == 1 ){
			// update to the new one
			$sqlu = "Update user SET Password= ? WHERE email = ?";
			$result=$DBConn->directsql($sqlu, array(md5($password), $user));
		}else{
			auditit(0,0,$user,'Unsuccessful Login');
			return false;
		}
	}
}

function check_user( $md5_sum ){
	Global $DBConn;

	$md='';

	if (empty($_REQUEST['PID']) || $Usr['Admin']==1) {
		$sql = "SELECT * FROM user WHERE Disabled_User = 0 AND ".$md."MD5(email) = ?";
		$result=$DBConn->directsql($sql, $md5_sum);
	} else{
		$sql = "SELECT * FROM user LEFT JOIN user_project ON user.ID = user_project.User_ID WHERE Disabled_User = 0 AND ".$md."MD5(email) = ? and (user_project.Project_id= ? or user.Admin_User=1)";
		$result=$DBConn->directsql($sql, array($md5_sum, $_REQUEST['PID']));
	}

	return count( $result ) > 0 ? $result[0] : false;
}

function projectadmin($thisproject){
	Global $DBConn;
// is this a global admin user
	$sql='select user.Admin_User from user where user.ID= ?';
	$Usr=$DBConn->directsql($sql, $_SESSION['ID']);
	$Usr=$Usr[0];
	if ($Usr){	if ($Usr['Admin_User']==1) return 1;}

	if (!empty($thisproject)){
		$sql = 'select Project_Admin from user_project where Project_ID= ? and User_ID= ?';
		$result=$DBConn->directsql($sql, array($thisproject, $_SESSION['ID']));
		if($result)
		{
			return count( $result ) > 0 ? $result[0]['Project_Admin'] : false;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
function readonly($thisproject){
	Global $DBConn;
	if (!empty($thisproject)){
		$sql = 'select user_project.Readonly from user_project where Project_ID= ? and User_ID= ?';
		$result=$DBConn->directsql($sql, array($thisproject, $_SESSION['ID']));
		if(count($result)>0)
		{
			return count( $result ) > 0 ? $result[0]['Readonly'] : false;
		}else{
			return false;
		}
	}else{
		return false;
	}
}
//##################################################################

function auditit($pid='', $aid=0, $user='', $action='', $from='', $to=''){
	Global $DBConn;
	$sql = "insert into audit (`PID`, `AID`, `User`, `action`,`From`, `To`) VALUES (?,?,?,?,?,?)";
	$result = $DBConn->directsql($sql, array($pid, $aid, $user, $action, $from, $to));
}


function fetchusingID($col,$val,$tabl){
	Global $DBConn;
	if ($tabl=='story')	{
		$sql='SELECT '.$col.' FROM '.$tabl.' WHERE AID= ?';
	}else{
		$sql='SELECT '.$col.' FROM '.$tabl.' WHERE ID= ?';
	}
	$row = $DBConn->directsql($sql, $val);
	if (count($row) == 1){
		return $row[0];
	}else{
		return 'empty';
	}
}

function PrintStory ($story_Row){
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
	if ($Project['Backlog_ID']==$story_Row['Iteration_ID'])	{
	// update predictions
	// use average card size or current velocity is ave > velocity for unsized cards.
		if ($story_Row['Size']=="?")	{
			if ($Project['Velocity'] > $Project['Average_Size']){
				 $Add_This = $Project['Average_Size'];
			}else{
				 $Add_This = $Project['Velocity'];
			}
		}else{
			$Add_This = $story_Row['Size'];
		}

		// add the next story even if it overflows (Best Case)
		$OSizecount += $Add_This;
		if ($OSizecount  >= $Project['Velocity']){
			$OSizecount = 0 ;
			$OIterationcount +=1;
		}

		// only use complete stories that fit (Worst Case)
		if ($Sizecount + $Add_This  > $Project['Velocity'])	{
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
	if ($_REQUEST['Root']=='iteration' || $_REQUEST['Root']=='release')	{
		if (!empty($_REQUEST['IID']))		{
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
			if($LockedIteration==0)	{
				echo	'<a href="story_Delete.php?id='.$story_Row['AID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'" title="Delete Story"><img src="images/delete.png"></a>';
			}
			echo '</div>';
		}

		echo '<div class="type-div">'.$story_Row['Type'].'</div>';
		echo '<div class="size-div" title="Story Size">&nbsp;';
		echo $story_Row['Size'].'&nbsp;';

// print probable iteration based on current velocity if on backlog
		if (empty($_REQUEST['Type'])){
			if ($Project['Backlog_ID']==$story_Row['Iteration_ID'])	{
				echo '<div class="predicted-div" title="Predicted last/first iteration after last \'loaded\' iteration">(+'.$Iterationcount.'/'.$OIterationcount.')&nbsp;</div>';
			}
		}
		echo '</div>';	// size-div

	echo '</div>';	//right-box

// set background of drag and drop handle to that of the status (for stories that can be worked on.)
	if($Num_Children == 0)	{
		echo '<div title="'.$statuspolicy[$story_Row['Status']].'" class="storystatus" style="background: #'.$statuscolour[$story_Row['Status']].'" id="span_div'.$story_Row['AID'].'"></div>';
	}else{
		echo '<div class="parentstorystatus" id="span_div'.$story_Row['AID'].'"></div>';
	}

	echo '<div class="storybody">';

	echo '<div class="line-1-div">';

//display status of any child stories along with the sum of points for that status
		echo '<div class="childrenstatus-div"> ';
		if($Num_Children != 0)	{
			$astatus=explode(",", $story_Row['Children_Status']);
			for ($i = 0; $i <count($astatus); $i++) {
				$SSize= Get_Status_Points($story_Row['AID'],$astatus[$i],0);
				if ($SSize!=0){
					if ($statuscolour[$astatus[$i]]==''){
						echo '<div title="'.$SSize.' '.$astatus[$i].' points" style=" display: inline-block;background-color:#bfbfbf;">&nbsp;'.$SSize.'&nbsp;</div>&nbsp;';
					}else{
						echo '<div title="'.$SSize.' '.$astatus[$i].' points" style=" display: inline-block;background-color:#'.$statuscolour[$astatus[$i]].';">&nbsp;'.$SSize.'&nbsp;</div>&nbsp;';
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

				$parentssql = 'SELECT @id := (SELECT Parent_Story_ID FROM story WHERE AID = @id and Parent_Story_ID <> 0) AS parent FROM (SELECT @id :=?) vars STRAIGHT_JOIN story  WHERE @id is not NULL';

				$parents_Res = $DBConn->directsql($parentssql, $story_Row['AID']);
				foreach($parents_Res as $parents_row){
			  		if($parents_row['parent']!=NULL){
						$parentsql='select ID, AID, Summary, Size from story where AID= ? and AID<>0';
						$parent_row =$DBConn->directsql($parentsql, $parents_row['parent']);
						if (count($parent_row) == 1){
							$parent_row=$parent_row[0];
							echo '<a title="'.$parent_row ['Summary'].'"';
							echo ' href="story_List.php?Type=tree&Root='.$parent_row ['ID'].'&PID='.$story_Row['Project_ID'].'&IID='.$story_Row['Iteration_ID'].'">';
							echo ' #'.$parent_row ['ID'].' ('.$parent_row ['Size'].' pts)</a>&nbsp;';
						}
					}
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
			echo '<div class="hidden" id="alltasks_'.$story_Row['AID'].'"></div>';
			echo '<div class="hidden" id="commentspops_'.$story_Row['AID'].'"></div> ';
			echo '<div class="hidden" id="allupload_'.$story_Row['AID'].'"></div> ';
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
	$sql = 'SELECT * from iteration where iteration.ID = ?';
	$iteration_Res = $DBConn->directsql($sql, $iteration);
	if(count($iteration_Res) == 1){
		$IterationList=buildpopup($iteration_Res,$thisdate );
	}

	if ($LockedIteration==0){
// Create the iteration popup
		$thisdate =  date_create(Date("Y-m-d"));
		$thisdate = date_format($thisdate , 'Y-m-d');

// Fetch  future Iterations
		$sql = 'SELECT * from iteration where iteration.Project_ID = ? and Start_Date> ? and ID<> ? order by iteration.Start_Date desc LIMIT 6';
		$iteration_Res = $DBConn->directsql($sql, array($project, $thisdate, $iteration));
		if(count($iteration_Res) > 0){
			$IterationList=buildpopup($iteration_Res,$thisdate );
		}

// Fetch the backlog and 6 previous Iterations (if not on the backlog)
		$sql = 'SELECT * from iteration where iteration.Project_ID = ? and Start_Date<>"0000-00-00" and (Start_Date<=?';
		if ($iteration==$backlog){
			$sql .='and ID<> ?)';
			$bind = array($project, $thisdate, $backlog);
		}else{
			$sql .='or ID= ?)';
			$bind = array($project, $thisdate, $backlog);
		}
		$sql .=' and ID<> ? order by iteration.End_Date desc LIMIT 6';
		$bind[] = $iteration;
		$iteration_Res = $DBConn->directsql($sql, $bind);
		if(count($iteration_Res) > 0){
			$IterationList.=buildpopup($iteration_Res,$thisdate );
		}

// Fetch  No date Iterations
		$sql = 'SELECT * from iteration where iteration.Project_ID = ? and ID<> ? and Start_Date="0000-00-00"';
		$iteration_Res = $DBConn->directsql($sql, array($project, $iteration));
		if(count($iteration_Res) > 0){
			$IterationList.=buildpopup($iteration_Res,$thisdate );
		}
	}
	return $IterationList;
} //GetIterationsforpop


function buildpopup($iteration_Res,$thisdate ){
// build iteration popup
	Global $LockedIteration;
	$IterationList='';

	if($iteration_Res)	{
		foreach($iteration_Res as $iteration_Row){
			if ($iteration_Row['Locked']==0){
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
				if ($iteration_Row['ID']==$_REQUEST['IID']){
					$LockedIteration = 1;
					return 'This iteration is locked!';
				}
			}
		}
	}
	return $IterationList;
}

function buildstatuspop($proj){
	global $statuscolour;
	Global $DBConn;
	if ($proj+0 > 0){
// Fetch the status and create the status popup.
		$sql = 'SELECT story_status.RGB, story_status.Desc, story_status.Policy FROM story_status where story_status.Project_ID= ? and LENGTH(story_status.Desc)>0 order by story_status.`Order`';
		$status_Res = $DBConn->directsql($sql, $proj);
		$statusList='';
		if ($status_Res){
			foreach($status_Res as $status_Row)	{
				$statuscolour[$status_Row['Desc']] = $status_Row['RGB'];
				$statusList.='&nbsp;&nbsp;<button title="'.$status_Row['Policy'].'" id="'.$status_Row['Desc'];
				$statusList.='" style="background:#'.$status_Row['RGB'].'" class="ui-button ui-state-default ui-corner-all">&nbsp;';
				$statusList.=$status_Row['Desc'];
				$statusList.='&nbsp;</button>';
			}
		}
		return ($statusList);
	}else{
		return (' ');
	}
}


function iterations_Dropdown($project, $current,$iterationname='Iteration_ID')
{
	Global $DBConn;
	Global $IterationLocked;

	$current+=0;
	$current_date = Date("Y-m-d");

	// Fetch Current Iteration.
	$result = fetchusingID('*',$current,'iteration');

	if ($result<>'empty'){
		$menu = '<select name="'.$iterationname.'" id="'.$iterationname.'"><option value="' . $result['ID'] . '">' . substr($result['Name'], 0, 14) .'</option>';
		$IterationLocked = $result['Locked'];
		if ($result['Locked']==1){
			$menu =$result['Name'].'<select  class="hidden"  name="Iteration_ID"><option value="' . $result['ID'] . '">' . substr($result['Name'], 0, 14) .'</option>';
		}
	}else{
		$menu = '<select name="'.$iterationname.'" id="'.$iterationname.'"><option value=""></option>';
	}
	// Fetch other iteratons
	$sql = 'SELECT * FROM iteration where iteration.Project_ID = ? and iteration.ID<> ? AND Locked=0 order by iteration.End_Date desc';
    $queried = $DBConn->directsql($sql, array($project, $current));
	foreach ($queried as $result ) {
		// highlight current iteration
		if (( $current_date >= $result['Start_Date'] ) && ( $current_date <= $result['End_Date'] ) && $result['Name'] <> "Backlog"){
			$menu .= '<option value="' . $result['ID'] . '">* ' . $result['Name'] . ' *</option>';
		}else{
			$menu .= '<option value="' . $result['ID'] . '">' . $result['Name'] . '</option>';
		}
	}
	$menu .= '</select>';
	return $menu;
}


function getReleaseName($relid){
	Global $DBConn;
	if ($relid+0 >0){
		$sql = 'select Name from release_details where ID= ?';
		$rec = $DBConn->directsql($sql, $relid);
		if (count($rec)>0)	{
			return $rec[0]['Name'] ;
		}
	}
	return '';
} // getRelease


function printMicromenu($AID){
Global $DBConn;
	echo '<div class="micromenu-div">';

		echo '<div title="(#done/#count)" class="inline" id="task_count_'.$AID.'">';
			// count the # of tasks and # completed
			$tsql = 'SELECT count(*) as all_count,( select count(*) FROM task where task.story_AID= ? and task.Done = 2) as done_count FROM task where task.story_AID= ?';
			$t_row = $DBConn->directsql($tsql, array($AID, $AID));
			$t_row = $t_row[0];
			if ($t_row['all_count'] > 0){
				echo ' ('.$t_row['done_count'].'/'.$t_row['all_count'].')';
			}
		echo '</div>';
		echo '<a class="taskpopup" id="'.$AID.'" href="" onclick="javascript: return false;" title="Show Tasks"><img src="images/task-small.png"></a> &nbsp;';

		echo '<div title="Comment count" class="inline" id="comment_count_s_'.$AID.'">';
			$tsql = 'SELECT count(*) as count FROM comment where comment.Story_AID= ?';
			$t_row = $DBConn->directsql($tsql, $AID);
			$t_row = $t_row[0];
			if ($t_row['count'] >0){
				echo ' ('.$t_row['count'].')';
			}
		echo '</div>';
		echo '<a class="commentpopup" id="comments_'.$AID.'" href="" onclick="javascript: return false;" title="Show Comments"><img src="images/comment-small.png"></a> &nbsp;';

		echo '<div title="(# uploads)" class="inline" id="upload_count_'.$AID.'">';
			// count the # of tasks and # completed
			$tsql = 'SELECT count(*) as all_count FROM upload where upload.AID= ?';
			$t_row = $DBConn->directsql($tsql, $AID);
			$t_row = $t_row[0];
			if ($t_row['all_count'] >0){
				echo ' ('.$t_row['all_count'].')';
			}
		echo '</div>';
		echo '<a class="uploadpopup" id="up'.$AID.'" href="" onclick="javascript: return false;" title="Show Uploads"><img src="images/upload-small.png"></a> &nbsp;';
		echo '<a class="auditpopup" id="audits'.$AID.'" href="" onclick="javascript: return false;" title="Show Audit Records"><img src="images/history-small.png"></a> &nbsp;';

	echo '</div>'; // micromenu-div
}

function GetComments($row, $ThisID, $Thiskey){
	Global $DBConn;

	echo '<li class="comment" id="comment_'.$row['ID'].'">';
 	echo '<div class="comment-body" id="comment_body_'.$row['ID'].'">'.$row['Comment_Text'].'</div>';
	echo "<div class='aut'>".$row['User_Name']."</div>";
	echo "<div class='timestamp'>".$row['Comment_Date']."</div>";

	echo '<a href="#commentspop'.$Thiskey.'_'.$ThisID.'" class="reply" id="'.$row['ID'].'">Reply</a>';
	/* The following sql checks to see if there are any replies for the comment */
	$q = "SELECT * FROM comment WHERE Parent_ID = ?";
	$r = $DBConn->directsql($q, $row['ID']);
	// there is at least reply
	if(count($r) > 0) 	{
		echo '<ul id="commentreply_'.$row['ID'].'">';
		foreach($r as $row) {
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

function Get_Name($id, $table, $withdate = false){
	Global $DBConn;

	if(empty($id)) return '';

	$sql = "SELECT * FROM $table where ID = ?";
	$result = $DBConn->directsql($sql, $id);

	if(count($result) == 0) return '';

	$result = $result[0];

	if($table == 'iteration'){
		GLOBAL $Iteration;
		$Iteration = $result;
		if ($result['Locked']==1)		{
			$prefix='Locked: ';
		}else{
			$prefix='';
		}
		if ($withdate==True and $result['Name']<>'Backlog' ) {
			return $prefix.$result['Name'].' ('.$result['Start_Date'].' -> '.$result['End_Date'].')';
		}else{
			return $result['Name'];
		}
	} else if ($table == 'project'){
		GLOBAL $Project;
		$Project = $result;
		return $result['Name'];
	} else if ($table == 'release_details'){
		return $result['Name'];
	}
}

function Get_Iteration_Name($thisiteration,$withdate=True){
	return Get_Name($thisiteration, 'iteration', $withdate);
}

function Get_Project_Name($thisproject){
	return Get_Name($thisproject, 'project');
}

function Get_Release_Name($thisrelease){
	return Get_Name($thisrelease, 'release_details');
}

function Get_Project_Backlog($thisproject){
	Global $DBConn;

	if(!empty($thisproject)){
		$sql='SELECT Backlog_ID FROM project where project.ID = ?';
		$Project = $DBConn->directsql($sql, $thisproject);
		return $Project[0]['Backlog_ID'];
	}
}

function NextIterationCommentObject(){
	Global $DBConn;

	$switch = 1;
	do {
		$rand = mt_rand();
		$sql='SELECT count(ID) AS CNT FROM iteration where `Comment_Object_ID` = ?';
		$Row = $DBConn->directsql($sql, $rand);
		$switch = $Row[0]['CNT'];
	} while ($switch != 0);
	return $rand;
}

function NextPointsObject($pid){
	Global $DBConn;

	$switch = 1;
	do {
		$rand = mt_rand();
		$sql='SELECT Count(ID) AS CNT FROM points_log where `Object_ID` = ?';
		$Row = $DBConn->directsql($sql, $rand);
		$switch = $Row[0]['CNT'];
	} while ($switch != 0);
	$sql="INSERT INTO points_log ( 'Object_ID', 'Project_ID','Status','Story_Count','Points_Claimed') values(?,?,?,?,?)";
	$result=$DBConn->directsql($sql, array($rand, $pid, 'Todo', 0, 0));
	return $rand;
}

function Show_Project_Users($ThisProject=0, $current,$name,$disabled=0){
	Global $DBConn;;
	if ($ThisProject>0)	{
		if (empty($current)) $current = '0';
		if (empty($disabled)) $disabled = '0';
		$menu = '<select id="'.$name.'" name="'.$name.'" ';
		if ($disabled==1){$menu .= ' disabled="disabled"';}
		$menu .= '>';
		$sql = 'SELECT ID, Friendly_Name FROM user WHERE user.ID= ?';
		$result = $DBConn->directsql($sql, $current);

		$result = $result[0];
		$menu .= '<option value="' . $result['ID'] . '">' . $result['Friendly_Name'] .'</option>';

		if (!empty($current)) $menu .='<option value=""></option>';

		$sql = 'SELECT ID, Friendly_Name FROM user LEFT JOIN user_project ON user.ID = user_project.USER_ID where user_project.Project_ID= ? and user.Disabled_User = 0 ORDER BY Friendly_Name';

		$queried = $DBConn->directsql($sql, $ThisProject);

		foreach ($queried as $result) {
			$menu .= '<option value="' . $result['ID'] . '">' . $result['Friendly_Name'] .'</option>';
	    	}
	$menu .= '</select>';
	$story_Row['Size']=$result['Value'];
	return $menu;
	}
}

function Get_User($current,$initials=0){
	Global $DBConn;

	if (!empty($current)){
		$sql = 'SELECT ID, Initials, Friendly_Name FROM user Where ID= ?';
		$result = $DBConn->directsql($sql, $current);
		if ($initials==0){
			return $result[0]['Friendly_Name'];
		}else{
			return $result[0]['Initials'];
		}
	}
}

function Update_Parent_Points($thisstory){
	Global $DBConn;
	if ($thisstory+0 > 0){
// a list of parents
		$sql='SELECT @r AS _aid, ( SELECT @r := Parent_Story_ID FROM story WHERE AID = _aid ) AS _aid FROM (SELECT  @r := ?) vars, story h';

		$parent_res = $DBConn->directsql($sql, $thisstory);
		foreach ($parent_res as $prow){
			$psql = 'Update story set Status=NULL, Size = (select sum(Size) from (select * from story) as p where p.Parent_Story_ID= ?) where story.AID = ?';
			$result=$DBConn->directsql($psql, array($prow['_aid'], $prow['_aid']));
			Update_Parent_Status($prow['_aid']);
		}
		unset ($parent_res);
	}
}

function Update_oldParent_Points($thisstory){
	Global $DBConn;
	$psql = 'Update story set Size = (select sum(Size) from (select * from story) as p where p.Parent_Story_ID= ?) where story.AID = ?';
	$result = $DBConn->directsql($psql, array($thisstory, $thisstory));
	$psql = 'select Iteration_ID from story where story.AID = ?';
	$Row = $DBConn->directsql($psql, $thisstory);
	$Row = $Row[0];
	Update_Iteration_Points($Row['Iteration_ID']);
	Update_Parent_Status($thisstory);
}

function Update_Parent_Status($thisstory){
	Global $DBConn;
// update  child story status if there are still children, otherwise reset the status to todo
	if (Num_Children($thisstory)>0)	{
		$sql = 'Select story.Children_Status, story.Status, story.Iteration_ID from story where Parent_Story_ID= ? order by story.Status';
		$Res=$DBConn->directsql($sql, $thisstory);
		$status='';
		foreach ($Res as $Row)		{
			if ($Row['Status'])			{
				$status.=$Row['Status'].',';
			}
			if ($Row['Children_Status'])			{
				$status.=$Row['Children_Status'].',';
			}
		}
		$astatus=explode(",", $status);
// concatenate the unique status values and trim the extra comma at the end
		$status = substr(implode(",", array_unique($astatus)), 0, -1);

		$sql = 'Update story set Children_Status= ? where story.AID= ?';
		$Res=$DBConn->directsql($sql, array($status, $thisstory));
	}else{
		$sql = 'Update story set Status="Todo", Children_Status="" where story.AID= ?';
		$Res=$DBConn->directsql($sql, $thisstory);
	}
}

function Top_Parent($StoryAID){
	Global $DBConn;
// find the topmost parent for a story

	$sql = 'SELECT @r AS _id, (SELECT @r := Parent_Story_ID FROM story WHERE AID = _id) AS parent, @l := @l + 1 AS level FROM (SELECT @r := ?) vars, story';
	$Row = $DBConn->directsql($sql, $StoryAID);
	if (count($Row)>0)	{
		return $Row[0]['_id'];
	}else{
		return 0;
	}
}

function Num_Children($storyAID){
	Global $DBConn;
	if (empty($storyAID)) return 0;

	$sql = 'select count(AID) as children from story where Parent_Story_ID= ?';
	$rec=$DBConn->directsql($sql, $storyAID);
	return $rec[0]['children'];
}


function Get_Status_Points($thisstory,$thisstatus,$sumx){
	Global $DBConn;
	$sum+=$sumx;

	$sql='SELECT story.AID, story.Size, story.Status from story where story.Parent_Story_ID= ?';
	$res = $DBConn->directsql($sql, $thisstory);
	foreach($res as $prow){
		if (Num_Children($prow['AID'])>0){
			$sum+=Get_Status_Points($prow['AID'],$thisstatus,$sum);
		}else{
			if ($prow['Status']==$thisstatus){
				$sum+=$prow['Size'];
			}
		}
	}
	return $sum;
}

function Update_Iteration_Points($thisiteration){
	Global $DBConn;
	// this is a LOCAL $Iteration, not the global one for the current oteration.
	$sql='select * from iteration where iteration.ID= ?';
	$Iteration=$DBConn->directsql($sql, $thisiteration);
	$Iteration=$Iteration[0];
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
	$sql='delete from points_log where points_log.Object_ID = ? and (Points_Date= ? or Points_Date="0000-00-00 00:00:00")';
	$result=$DBConn->directsql($sql, array($Iteration['Points_Object_ID'], $today.' 00:00:00'));

//get the points for each status for the iteration
	$sql='select Project_ID, count(ID) as Story_Count, story.`Status`, sum(story.Size) as Size from story where story.Iteration_ID= ? and Status IS NOT NULL and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) group by Project_ID, story.`Status`';
	$status_Res = $DBConn->directsql($sql, $thisiteration);
	$iSize=0;
// and update the points log
	if ($status_Res){
		foreach ($status_Res as $status_Row)	{
			$iSize += $status_Row['Size'];
			$data=array(
			'Project_ID' =>				$thisproject,
			'Points_Date' => 			$today.' 00:00:00',
			'Object_ID' => 				$Iteration['Points_Object_ID'],
			'Status' => 				$status_Row['Status'],
			'Story_Count' => 			$status_Row['Story_Count'],
			'Points_Claimed' => 		$status_Row['Size']	);
			$result = $DBConn->create('points_log',$data);
		}
	}
	Update_Project_Points($thisproject);
	// Iteration total points
	return $iSize;
}

function Update_Project_Points($thisproject){
	Global $DBConn;
	$today = date_create(Date("Y-m-d"));
	$today = date_format($today , 'Y-m-d');
	$piid = fetchusingID('Points_Object_ID, Vel_Iter',$thisproject,'project');
	$numiterations = $piid['Vel_Iter'];
	$piid = $piid['Points_Object_ID'];

	$qry='delete from points_log where points_log.Object_ID = ? and (Points_Date= ? or Points_Date="0000-00-00 00:00:00")';
	$result=$DBConn->directsql($qry, array($piid, $today.' 00:00:00'));
	$sql = 'select Status, Project_ID, sum(Size) as Sizes, count(AID) as Story_Count from story where story.Project_ID = ? and Status IS NOT NULL and 0=(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) group by Project_ID, Status';
	$Status_Res = $DBConn->directsql($sql, $thisproject);
	if ($Status_Res){
		foreach ($Status_Res as $Status_Row)	{
			$data=array(
				'Project_ID' => 	$thisproject,
				'Points_Claimed' => $Status_Row['Sizes'],
				'Story_Count' => 	$Status_Row['Story_Count'],
				'Object_ID' => 		$piid,
				'Points_Date' => 	$today.' 00:00:00',
				'Status' => 		$Status_Row['Status']
				);
			$DBConn->create('points_log',$data);
		}
	}

// this is to make sure that velocity is calculated correctly where we have less than the specified completed iterations in the project.
	$tsql = "SELECT Count(ID) as Done_Iterations from iteration where iteration.Project_ID= ? and iteration.End_Date <= ? and iteration.Name <>'Backlog'";
	$trow = $DBConn->directsql($tsql, array($thisproject, $today));
	$trow = $trow[0];
	$Done_Iterations=$trow['Done_Iterations'];
	if ($Done_Iterations > $numiterations) $Done_Iterations = $numiterations;
	if ($Done_Iterations < 1) $Done_Iterations = 1;

// update project velocity. (the average of the most recent $numiterations iterations [including the current one.])
// in other words, what would our velocity be if the iteration ended now.
// Select most recent $numiterations or fewer iterations based on Start date to make sure we incude the current iteration.

	$sqlii= "SELECT ID as IDS from iteration where iteration.Project_ID= ? and iteration.End_Date <= ? and iteration.Name <>'Backlog' order by End_Date DESC LIMIT ".$Done_Iterations;
	$iires=$DBConn->directsql($sqlii, array($thisproject, $today));

	if (count($iires) > 0){
		$iterations='';
		foreach ($iires as $iirow){
			$iterations.=$iirow['IDS'].',';
		}
		$iterations = substr($iterations, 0, -1);
		$sqli= "(select ROUND((sum(Size)/".$Done_Iterations."),0) from  story  where story.Iteration_ID in (".$iterations.") and story.Status='Done')";
		$sql='Update project Set Velocity='. $sqli.' where project.ID= ?';
		$DBConn->directsql($sql, $thisproject);
	}
	unset ($iires);
// update project card Average_Size to use when predicting unsized cards.
	$sql= "Update Project set Average_Size=(SELECT ROUND(avg(Size),0) as Average_Size from story where story.Project_ID= ? and Size> 0) where project.ID= ?";
	$result = $DBConn->directsql($sql, array($thisproject, $thisproject));
}

function print_releasesummary($proj,$tsql){
	Global $DBConn;
	$Res=$DBConn->directsql($tsql);
	$s='<table align="center" width=20%><tr><td class="larger">'.Get_Project_Name($proj).'</td>';
	$c='<tr><td bgcolor="#F2F2F2" align="Right" class="larger">Cards</td>';
	$p='<tr><td bgcolor="#F2F2F2" align="Right" class="larger">Points</td>';
	foreach  ($Res as $Row)	{
		$s.='<td align="center" class="larger"  bgcolor="#F2F2F2"><b>'.$Row['Status'].'<b></td>';
		$c.='<td align="center" bgcolor="#FaF2F2">'.$Row['relcount'].'</td>';
		$p.='<td align="center" bgcolor="#FaF2F2">'.$Row['relsize'].'</td>';
	}
	unset($res);
	$s.='</tr>';
	$p.='</tr>';
	$c.='</tr></table>';
	echo $s.$p.$c;
}


function print_summary($object, $WithVelocity=False){
	Global $DBConn;
	Global $Project;
	echo '<img class="showSummary" id="'.$object.'" src="images/add-small.png" title="Show Summary">';
	$l1='';
	$l2='';
	$l3='';

	$sql = 'select Points_Date, Status, Story_Count, sum(Points_Claimed) as Size, (select min(story_status.`Order`) from story_status where story_status.Project_ID= ? and story_status.Desc = points_log.Status) as ststatus from points_log where points_log.Object_ID= ? and Points_Date < "2199-12-31" group by Points_Date DESC, ststatus';

	$result=$DBConn->directsql($sql, array($Project['ID'], $object));
	if ($result)	{
		$last_Date=$result[0]['Points_Date'];
		$l1='<th bgcolor="#F2F2F2"><img class="hideSummary" src="images/minus-small.png" title="Hide Summary"></th>';
		$l2='<td bgcolor="#F2F2F2" align="Right">Cards:</td>';
		$l3='<td bgcolor="#F2F2F2" align="Right">Points:</td>';
		$t1=0;
		$t2=0;
		$rowcnt=0;
		do	{
			$l1.=	'<th bgcolor="#F2F2F2">&nbsp;'.$result[$rowcnt]['Status'].'&nbsp;</th>';
			$l2.=	'<td align="right" bgcolor="#F2F2F2" class="larger">'.$result[$rowcnt]['Story_Count'].'&nbsp;&nbsp;</td>';
			$l3.=	'<td align="right" bgcolor="#F2F2F2" class="larger">'.$result[$rowcnt]['Size'].'&nbsp;&nbsp;</td>';
			$t1+=$result[$rowcnt]['Story_Count'];
			$t2+=$result[$rowcnt]['Size'];
			$rowcnt+=1;
		}
		while ($rowcnt<count($result) and $last_Date==$result[$rowcnt]['Points_Date']);
		$l1.=	'<th bgcolor="#F2F2F2">&nbsp;Total&nbsp;</th>';
		$l2.=	'<td align="right" bgcolor="#F2F2F2" class="larger"><b>'.$t1.'</b></td>';
		$l3.=	'<td align="right" bgcolor="#F2F2F2" class="larger"><b>'.$t2.'</b></td>';
	}
	unset($result);

	echo '<table class="SummaryTable" cellpadding="2" cellspacing="1" border="0" >';

	if ($WithVelocity==True)	{
		$l1.='<th>&nbsp;&nbsp;</th><th title="Average of '.$Project['Vel_Iter'].' most recent completed iterations" align="center" class="evenlarger">Velocity</th>';
		$l3.='<td>&nbsp;&nbsp;</td><td align="center" class="evenlarger"><b>'.$Project['Velocity'].'</b></td>';
		$l2.='<td>&nbsp;&nbsp;</td><td align="center" class="larger"></td>';
	}
	echo '<tr>'.$l1.'</tr>';
	echo '<tr>'.$l3.'</tr>';
	echo '<tr>'.$l2.'</tr>';
	echo '</table>';
}

// Points Object ID, Small|Regular Size, Iteration end date|0
function print_Graphx($object, $small=False, $iterstart=0, $iterend=0){
	Global $DBConn;
	if ($small==False)	{
		echo '<div class="chart_div" id="chart2'.$object.'" style="width:70%; height: 250px;"></div>';
	}else{
		echo '<div class="chart_div" id="chart2'.$object.'" style="width:250px; height: 100px;"></div>';
	}
	$d1='series: [';

	$dd1='????: [';
	$o1='seriesColors: [';
	$tick='[';

// Get all the different status values applicable to this project.
	$sql = 'SELECT * FROM story_status where Project_ID= ? order by story_status.`Order`';
	$sta_Row = $DBConn->directsql($sql, $_REQUEST['PID']);
	$idx=0;
	if (count($sta_Row) >0)	{
		$rowcnt=0;
		do	{
			if (strlen($sta_Row[$rowcnt]['Desc'])!=0){
				$idx+=1;
				$d1.='{label: "'.$sta_Row[$rowcnt]['Desc'].'"},';
				$o1.='"#'.$sta_Row[$rowcnt]['RGB'].'",';
				$sta_id[$sta_Row[$rowcnt]['Desc']] = $idx;
			}
			$rowcnt+=1;
		}while ($rowcnt <count($sta_Row));
		$o1 = substr($o1, 0, -1);
		$d1 = substr($d1, 0, -1);
		$o1.='], ';
		$d1.='], ';
	}

// limit the number of ticks on the x axis to 8+2=10
	$sql ='select count(distinct Points_Date) as ndates from points_log where points_log.Object_ID= ?';
	$result = $DBConn->directsql($sql, $object);
	if (count($result) >0)	{
		$modit = ceil($result[0]['ndates']/8);
		$top=$result[0]['ndates'];
	}else{
		$modit = 1;
	}

// get the dates
	$sql ='select distinct Points_Date  from points_log where points_log.Object_ID= ? and Points_Date < "2199-12-31" order by Points_Date';
	$result =$DBConn->directsql($sql, $object);

	$idx=0;

	if (count($result) > 0)	{
		$rowcnt=0;
		//todo  	only add this if no pointslog on the first  day of the sprint
		if ($iterstart!=0){
			if ($iterstart<substr($result[$rowcnt]['Points_Date'],0,10)){
				$idx+=1;
				$tick .="[".$idx.",'".$iterstart."'],";
			}
		}
		do	{
			$idx+=1;
			$dat_id[$result[$rowcnt]['Points_Date']]=$idx;
			if ($idx % $modit == 0 || $idx ==1 || $idx==$top){
				$tick .="[".$idx.",'".substr($result[$rowcnt]['Points_Date'],0,10)."'],";
			}
			$rowcnt+=1;
		}while ($rowcnt < count($result));
	}

	if ($iterend!=0){
		if ($iterend>substr($result[$rowcnt-1]['Points_Date'],0,10)){
			$iterlastpointsdate=$result[$rowcnt-1]['Points_Date'];
			$idx+=1;
			$tick .="[".$idx.",'".$iterend."'],";
		}
	}

	$tick = substr($tick ,0, -1);
	$tick .=']';

  	//create a 2d array [status][date]
	$a = array_fill(0, count($sta_id)+1, array_fill(0,$idx, 0));

	$sql = 'select Points_Date, Status, Story_Count, Points_Claimed as Size from points_log where points_log.Object_ID= ? order by  Points_Date, Status';
	$result = $DBConn->directsql($sql, $object);
	if (count($result)>0){
		$rowcnt=0;

		do {
			$a[$sta_id[$result[$rowcnt]['Status']]][$dat_id[$result[$rowcnt]['Points_Date']]-1] = $result[$rowcnt]['Size'];
			// if we have an iteration end date then carry the last points value on the sprint across to the end of the iteration
			if ($iterend!=0){
				if($result[$rowcnt]['Points_Date'] == $iterlastpointsdate){
					$a[$sta_id[$result[$rowcnt]['Status']]][$idx-1] = $result[$rowcnt]['Size'];
				}
			}

			$rowcnt+=1;
		}
		while ($rowcnt < count($result));
	}

	echo '<div id="customTooltipDiv">tooltip.</div>';
	echo '<script> $(document).ready(function(){';
	$va='';
	for ($i = 1; $i<= count($sta_id); $i++)	{
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
	if ($small==False){
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