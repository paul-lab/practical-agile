<?php
	include 'include/header.inc.php';
	Global $IterationLocked;
?>
	<script type="text/javascript" src="scripts/story_edit-hash8bba5955b102916e4ad786af1f91aecc.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" src="scripts/tag-it-hashaf1b4b7f2214f80bb9aa05528150c662.js" type="text/javascript" charset="utf-8"></script>
	<link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">


	<link rel="stylesheet" type="text/css" href="css/task_List.css" />
	<script type="text/javascript" src="scripts/task_Edit-hash833cdce054777866e208426320edbc66.js"></script>

	<script type="text/javascript" src="scripts/comment_Edit-hashde980ce44a0d25c08c2403843c7981f7.js"></script>
	<link rel="stylesheet" type="text/css" href="css/comment.css" />

	<link rel="stylesheet" type="text/css" href="css/upload_List.css" />
	<script type="text/javascript" src="scripts/upload_Edit-hashe640af5f5ce65fc3e8079302883335f2.js"></script>

	<script type="text/javascript" src="scripts/audit_List-hashcffb8e35f4f703c886ddd181171d59af.js"></script>

	<script type="text/javascript" src="jhtml/scripts/jHtmlArea-0.8.js"></script>
    	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.css" />
	<script type="text/javascript" src="jhtml/scripts/jHtmlArea.ColorPickerMenu-0.8.js"></script>
	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.ColorPickerMenu.css" />

	<link href="css/story_Edit.css" rel="stylesheet" type="text/css">
<?php

if (empty($_REQUEST['PID'])) header("Location:project_List.php");

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
echo Get_Project_Name($_REQUEST['PID']);
echo '</a>->';
echo '<a href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
echo Get_Iteration_Name($_REQUEST['IID']);
echo '</a>';
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


function checksummary(theForm) {
	if (theForm.Summary.value.length == 0)
	{
        	theForm.Summary.style.background = 'MistyRose';
		alert("You should at least have a summary for the story!.\n");
	   	return false;
	}
	return true;
}

</script>

<?php

echo
'<div class="hidden" id="phpnavicons" align="Left">'.
	'<a title="Add new story" href="story_Edit.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/storyadd-large.png"></a>&nbsp; &nbsp;'.
	'&nbsp; &nbsp;<a  title="Project Epic tree" href="story_List.php?Type=tree&Root=0&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/tree-large.png"></a>'.
	'&nbsp; &nbsp;<a  title="Scrum Board" href="story_List.php?Type=board&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/board-large.png"></a>'.
	'&nbsp; &nbsp;<a  title="Story List" href="story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'"><img src="images/list-large.png"></a>'.
'</div>';



function print_Story_Type_Dropdown($current)
{
	Global $DBConn;
	if (empty($current)) $current = 'Feature';
	$sql = 'select story_type.Desc from story_type where story_type.Project_ID='.$_REQUEST['PID'].' AND story_type.Desc <> "'.$current.'" order by story_type.Order';
	$queried = mysqli_query($DBConn, $sql);
	$menu = '<select name="Type">';
	$menu .= '<option value="' . $current . '">' . $current . '</option>';
		while ($result = mysqli_fetch_array($queried)) {
		$menu .= '<option value="' . $result['Desc'] . '">' . $result['Desc'] . '</option>';
	    }
	$menu .= '</select>';
	return $menu;
}

function print_Story_Status_Dropdown($current)
{
	Global $DBConn;

	if (empty($current)) $current = 'Todo';
	$sql = 'select story_status.Desc from story_status where story_status.Project_ID='.$_REQUEST['PID'].' AND story_status.Desc <> "'.$current.'" and LENGTH(story_status.Desc)>0 order by story_status.order';
	$queried = mysqli_query($DBConn, $sql);
	$menu = '<select name="Status">';
	$menu .= '<option value="' . $current . '">' . $current . '</option>';
		while ($result = mysqli_fetch_array($queried)) {
		$menu .= '<option value="' . $result['Desc'] . '">' . $result['Desc'] . '</option>';
	    }
	$menu .= '</select>';
	return $menu;
}


function print_Release_Dropdown($current)
{
	Global $DBConn;
	$current+=0;
	if ($current==0)
	{
		$current = '';
		$name = '';
	}else{
		$sql = 'select * from release_details where ID='.$current;
		$queried = mysqli_query($DBConn, $sql);
		$result = mysqli_fetch_array($queried);
		$name= $result['Name'].' ('.$result['Start'].'>'.$result['End'].')';
	}

	$sql = 'select * from release_details where Locked=0';
	$queried = mysqli_query($DBConn, $sql);
	$menu = '<select name="Release">';

	if ($result['Locked']==1)
	{
		$menu = $result['Name'].'<select class="hidden" name="Release">';
	}

	$menu .= '<option value="' . $current . '">' . $name . '</option>';
	$menu .= '<option value="0"></option>';
		while ($result = mysqli_fetch_array($queried)) {
		$menu .= '<option value="' . $result['ID'] . '">'.$result['Name'].' ('.$result['Start'].'>'.$result['End'].')</option>';
	    }
	$menu .= '</select>';
	return $menu;
}


function print_Story_Size_Radio($current,$type)
{
	Global $DBConn;

	if ($current=='') $current='?';
	$sql = 'select * from size where size.Type=(select Project_Size_ID from Project as p where p.ID='.$_REQUEST['PID'].') order by size.Order';
	$queried = mysqli_query($DBConn, $sql);
	$menu = '<select name="Size">';
	$menu = '<div id="sizediv">';
	while ($result = mysqli_fetch_array($queried)) {
		$menu .= '<input class="sizediv" type="radio" name="Size" value="' . $result['Value'].'"';
		if ($current==$result['Value'])
		{
			$menu .= ' checked';
		}
		$menu .= '>' . $result['Value'] .' &nbsp; </input>';
	    }
	$menu .= '</div>';
	return $menu;
}

function print_Possible_Parent($Project, $current=0)
{
	Global $DBConn;

	$current+=0;
	// Fetch Current Parent.
	$sql = 'SELECT AID, ID, Summary FROM story where story.AID ='.$current;
	$queried = mysqli_query($DBConn, $sql);
	$result = mysqli_fetch_array($queried);
	$menu = '<select name="Parent_Story_ID">';

	if ($current==0){
		$menu .= '<option value="0"></option>';
	}else{
		$menu .= '<option value="' . $result['AID'] . '">' .$result['ID'].' - '. $result['Summary'] .'</option>';
		$menu .= '<option value="0"></option>';

		$stree.='&nbsp;<a  title="Show my parent and all its children"';
		$stree .=' href="story_List.php?Type=tree&Root='.$result['ID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
		$stree .='<img src="images/tree.png"></a>';
	}

	$sql = 'select AID, ID, Summary from story where Project_ID='.$_REQUEST['PID'].' and 0<(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by ID';
	$queried = mysqli_query($DBConn, $sql);
	while ($result = mysqli_fetch_array($queried)) {
		$menu .= '<option value="' . $result['AID'] . '">' .$result['ID'].' - '. $result['Summary'] .'</option>';
	}
	$menu .= '</select>';

	$menu .=$stree;

	return $menu;
}

function Get_manual_Parent($current=0)
{
	Global $DBConn;

	$current+=0;
	// Fetch Current Parent.
	$sql = 'SELECT AID, ID FROM story where story.ID ='.$current.' and story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID=(select Backlog_ID from project where project.ID='.$_REQUEST['PID'].') and (story.Status="Todo" or story.Status IS NULL)';
	$queried = mysqli_query($DBConn, $sql);
	if($result = mysqli_fetch_array($queried))
	{
		return $result['AID'];
	}
	return 0;
}


function  Update_Project_Tags($PID,$Tags)
{
	Global $DBConn;
	$sql= 'SELECT tags.Desc from tags where tags.Project_ID='.$PID;
	$tag_Res = mysqli_query($DBConn, $sql);
	if ($tag_Row = mysqli_fetch_array($tag_Res))
	{
		$newTags = implode(",",array_unique(explode(",", $tag_Row['Desc'].",".$Tags)));
		$sql='UPDATE tags SET tags.Desc="'.$newTags.'" where tags.Project_ID='.$PID;
	}else{
		$newTags = $Tags;
		$sql='INSERT INTO tags ( Project_ID, tags.Desc) VALUES('.$PID.',"'.$newTags.'")';
	}
 		mysqli_query($DBConn, $sql);	
}


	echo '<div id="msg_div">&nbsp;</div>';
	$showForm = true;
	if (isset($_POST['saveUpdate']))
	{
		setcookie('ctorb',$_REQUEST['torb']);
		if (!empty($_REQUEST['manualParent']))
		{
			$manualPar=Get_manual_Parent($_REQUEST['manualParent']+0);
			if ($manualPar!=0)
			{
				$_REQUEST['Parent_Story_ID']=$manualPar;
			}
		}

		if (empty($_REQUEST['AID']))
		{
			$sql_method = 'INSERT INTO';
			$whereClause = '';
			$Insertsql =	', story.ID=(select IFNULL(MAX(prj.ID), 0)+1  from story as prj where prj.Project_ID='.$_REQUEST['PID'].')';

			if ($_REQUEST['torb']=='b')
			{
				$Insertsql .=	', story.Iteration_Rank=(select IFNULL(MAX(prj.Iteration_Rank), 0)+100  from story as prj where Project_ID='.$_REQUEST['PID'].')';
			}else{
				$Insertsql .=	', story.Iteration_Rank=(select IFNULL(MIN(prj.Iteration_Rank), 0)-1  from story as prj where Project_ID='.$_REQUEST['PID'].')';
			}
			$Insertsql .=	', story.Epic_Rank=(select IFNULL(MAX(prj.Epic_Rank), 0)+100  from story as prj where Project_ID='.$_REQUEST['PID'].')'.
					', Created_By_ID = "'.$_SESSION['ID'].'" ';
		}
		else
		{
			$sql_method = 'UPDATE';
			$Insertsql = '';
			$whereClause = 'WHERE AID = '.($_REQUEST['AID'] + 0);
		}

		$sql= $sql_method." story SET Project_ID = '".$_REQUEST['PID'].
			"', Type = '".$_REQUEST['Type'].
			"', Status = '".$_REQUEST['Status'];

		// if no children then update the size (Parent points are calculated on the fly.)
		if (Num_Children($_REQUEST['AID'] + 0)==0)
		{
			$sql.=	"', Size = '".$_REQUEST['Size'];
		}

// some common conversions to help with the auditing
		$_REQUEST['Summary'] = htmlentities($_REQUEST['Summary'],ENT_QUOTES);
		$_REQUEST['Col_1'] = htmlentities($_REQUEST['Col_1'],ENT_QUOTES);
		$_REQUEST['As_A'] = htmlentities($_REQUEST['As_A'],ENT_QUOTES);
		$_REQUEST['Col_2'] = htmlentities($_REQUEST['Col_2'],ENT_QUOTES);
		$_REQUEST['Acceptance'] = htmlentities($_REQUEST['Acceptance'],ENT_QUOTES);


		$sql.=	"', Blocked = '".$_REQUEST['Blocked'].
			"', Iteration_ID = '".$_REQUEST['Iteration_ID'].
			"', Owner_ID = '".$_REQUEST['Owner_ID'].
			"', Release_ID = '".$_REQUEST['Release'].
			"', Parent_Story_ID = '".$_REQUEST['Parent_Story_ID'].
			"', Summary = '".$_REQUEST['Summary'].
			"', Col_1 = '".$_REQUEST['Col_1'].
			"', As_A = '".$_REQUEST['As_A'].
			"', Col_2 = '".$_REQUEST['Col_2'].
			"', Acceptance = '".$_REQUEST['Acceptance'].
			"', Tags = '".addslashes($_REQUEST['Tags'])."' ".$Insertsql." ".$whereClause;


			if ($sql_method==='UPDATE')
			{
				$aaction='Update story ';
				$osql='select * from story where story.AID='.$_REQUEST['AID'];
				$ores=mysqli_query($DBConn, $osql);
				$orow=mysqli_fetch_assoc($ores);
			}else{
				$aaction='Add new Story ';
			}

		if (mysqli_query($DBConn, $sql))
		{
//Audit
			//If we are updating
			if ($sql_method==='UPDATE')
			{

				// for each field  that appears in a story record
				foreach ($orow as $key => $value)
				{
					// that is passed in (Blocked wants some special handling)
					if($_REQUEST[$key] || ($key=='Blocked' && $orow['Blocked']==1))
					{
						// and somethng has changed then log it
						if($_REQUEST[$key]!==$orow[$key] )
						{
							echo '# n'.$key.' '.$_REQUEST[$key]. ' - o'.$orow[$key].'</br>';
							auditit($_REQUEST['PID'],$_REQUEST['AID'],$_SESSION['Email'],$aaction.$key,$orow[$key],$_REQUEST[$key]);
						}
					}
				}
			}else{
				auditit($_REQUEST['PID'],0,$_SESSION['Email'],$aaction,'',$_REQUEST['Summary']);
			}

			$showForm = false;
			Update_Parent_Points($_REQUEST['AID']);
			Update_Iteration_Points($_REQUEST['Iteration_ID']);
			Update_Project_Tags($_REQUEST['PID'],$_REQUEST['Tags']);

			if (!empty($_REQUEST['gobackto']))
			{
				header('Location:'.$_REQUEST['gobackto']);
			}else{
				header('Location:story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID']);
			}
		}else{
			$error = 'The form failed to process correctly.'.mysqli_error($DBConn);
		}
	}
	if (!empty($error))
	{
		echo '<div class="error">'.$sql.'<p>'.$error.'</div>';
	}



	if ($showForm)
	{
		if (!empty($_REQUEST['AID']))
		{
			$story_Res = mysqli_query($DBConn, 'SELECT * FROM story WHERE AID = '.$_REQUEST['AID'].' and Project_ID='.$_REQUEST['PID']);
			$story_Row = mysqli_fetch_assoc($story_Res);
		}else{
			$story_Row = $_REQUEST;
		}


		// if a new story, then default to the iteration we are currently in.
		if (empty($story_Row['Iteration_ID']))
		{
			$story_Row['Iteration_ID']=$_REQUEST['IID'];
		}

		$Num_Children=Num_Children($story_Row['AID']);

?>
<form method="post"  onsubmit="return checksummary(this)" action="?">


<?php

?>
	<table align="center" cellpadding="6" cellspacing="0" width=95% >
	<tr>
		<td width="97">Story : <img title="Duplicate this Story without tasks" class="dupestory hidden" id="dup<?=$story_Row['AID'];?>" src="images/duplicate.png">&nbsp;
			<img title="Duplicate this Story and all its tasks (Owner, Status and Actual hours are reset )" class="dupestory hidden" id="dut<?=$story_Row['AID'];?>" src="images/duplicateandtasks.png"></td>
		<td>
<?php
		if ($Num_Children!=0)
		{
			echo '<a  title="Show my children (#'.$story_Row['ID'].') as the root of the tree)"';
			echo ' href="story_List.php?Type=tree&Root='.$story_Row['ID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
			echo '<img src="images/tree.png"></a>&nbsp; &nbsp;';
		}
			echo '#'.$story_Row['ID'].'&nbsp; &nbsp;';
			echo 'created by: '.Get_User($story_Row['Created_By_ID'],0).'&nbsp;on&nbsp;'.$story_Row['Created_Date'];

		if ($Num_Children==0)
		{
			echo '<span style="float: right;">';
			echo 'Release: ';
			echo print_Release_Dropdown($story_Row['Release_ID']);
			echo '</span>';
		}

?>
		</td>
	</tr>
	<tr>
		<td>Summary:</td>
		<td>
			<input type="text" name="Summary" class="w100" value="<?=$story_Row['Summary'];?>">
		</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>


<?php
		if ($Num_Children==0)
		{
			echo print_Story_Size_Radio($story_Row['Size'],$Project['Project_Size_ID']);
			echo '<br>';
			echo iterations_Dropdown($story_Row['Project_ID'], $story_Row['Iteration_ID']);
			echo '&nbsp;&nbsp;&nbsp;';
			echo print_Story_Status_Dropdown($story_Row['Status']);
		}else{
			echo $story_Row['Size'].' points &nbsp;&nbsp;&nbsp;';
			echo 'No Iteration';
			echo '&nbsp;&nbsp;&nbsp;';
			echo 'N/A';
			echo '	<input type="hidden" name="Iteration_ID" value="'.$story_Row['Iteration_ID'].'">';
			echo '	<input type="hidden" name=""Status"" value="">';
		}
		echo '&nbsp;&nbsp;&nbsp;';

			echo print_Story_Type_Dropdown($story_Row['Type']).'&nbsp;&nbsp;&nbsp;';
			echo Show_Project_Users($_REQUEST['PID'], $story_Row['Owner_ID'],"Owner_ID");
			echo '&nbsp;&nbsp;&nbsp;';
?>
			Blocked? <input <?=$story_Row['Blocked'] == 1 ? 'checked' : '0';?> value="1" type="checkbox" name="Blocked">&nbsp;&nbsp;&nbsp;
		</td>
	</tr>
	<tr>
		<td>Parent:</td>
		<td>
			<input type="text" title="Parent must be in the backlog and in a Todo Status to be used as a parent" name="manualParent" size=3 value="">&nbsp; &nbsp;
			<?=print_Possible_Parent($_REQUEST['PID'],$story_Row['Parent_Story_ID']);?>
		<td>
	</tr>


<?php
if ($Project['As_A']){
?>
	<tr>
		<td>As A:</td>
		<td>
			<input type="text" name="As A" class="w50" value="<?=$story_Row['As_A'];?>">
		</td>
	</tr>
<?php
}
?>
	<tr>
		<td valign="top"><?=$Project['Desc_1'];?></td>
		<td><textarea name="Col_1" class="w100 col1h"><?=$story_Row['Col_1'];?></textarea>
		</td>
	</tr>



<?php
if ($Project['Col_2']){
?>
	<tr>
		<td valign="top"><?=$Project['Desc_2'];?></td>
		<td><textarea name="Col_2" class="w100 col2h"><?=$story_Row['Col_2'];?></textarea>
		</td>
	</tr>


<?php
}

if ($Project['Acceptance']){
?>
	<tr>
		<td valign="top">Acceptance:</td>
		<td>
		<textarea name="Acceptance" class="w100 accepth"><?=$story_Row['Acceptance'];?></textarea>
		</td>
	</tr>


<?php
}
?>
	<tr>
		<td>Tags:</td>
		<td class="smaller nopadding">
			<input type="text" id="singleFieldTags" name="Tags" size=100% value="<?=$story_Row['Tags'];?>">
		</td>
	</tr>
	<tr>

	<td>&nbsp;</td>
	<td class="nopadding">

<?php
// can only add comments and tasks if we know what story to attach them to
if (!empty($_REQUEST['AID']))
{
	printMicromenu($story_Row['AID']);

	echo '<div class="taskdialog inline" id="alltasks_'.$story_Row['AID'].'"></div>';
	echo '<div class="commentsdialog" id="commentspops_'.$story_Row['AID'].'"></div> ';
	echo '<div class="uploaddialog" id="allupload_'.$story_Row['AID'].'"></div> ';
	echo '<div class="auditdialog hidden" id="allaudits_'.$story_Row['AID'].'"></div> ';
}
	echo '<td></tr></table>';
	echo '	<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">';
	echo '	<input type="hidden" name="IID" value="'.$_REQUEST['IID'].'">';
	echo '	<input type="hidden" name="AID" value="'.$story_Row['AID'].'">';
	echo '	<input type="hidden" name="gobackto" value="'.substr($_SERVER["HTTP_REFERER"],strrpos($_SERVER["HTTP_REFERER"],"/")+1).'">';

if(!$isReadonly)
{
		echo '	<input type="submit" name="saveUpdate" value="Update">';
		if (empty($_REQUEST['AID']))
		{
			if ($_COOKIE['ctorb']=='b')
			{
				echo '<input type="radio" name="torb" value="t">Top or</input>';
				echo '<input type="radio" name="torb" value="b" checked >Bottom of Backlog/Iteration</input>';
			}else{
				echo '<input type="radio" name="torb" value="t" checked >Top or</input>';
				echo '<input type="radio" name="torb" value="b">Bottom of Backlog/Iteration</input>';
			}
		}

		if ( Num_Children($story_Row['AID']) ==0 && $IterationLocked==0 ){
			echo '<a class="storyeditdelete" href="story_Delete.php?id='.$story_Row['AID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'" title="Delete Story"><img src="images/delete-large.png"></a>';
		}
	}
}


?>
	</form><br>


<script>
	document.forms[1].Summary.focus();
</script>
<?php
	include 'include/footer.inc.php';
?>
