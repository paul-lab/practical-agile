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
	Global $IterationLocked;
?>
	<script type="text/javascript" src="scripts/story_edit-hasha9c7e65e35b97225ecb8b5d288368aef.js" type="text/javascript" charset="utf-8"></script>

	<script type="text/javascript" src="scripts/tag-it-hashf66e5c5daffc1248e495628cd1881f21.js" type="text/javascript" charset="utf-8"></script>
	<link href="css/jquery.tagit.css" rel="stylesheet" type="text/css">

	<script type="text/javascript" src="jhtml/scripts/jHtmlArea-0.8-min.js"></script>
    	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.css" />
	<script type="text/javascript" src="jhtml/scripts/jHtmlArea.ColorPickerMenu-0.8-min.js"></script>
	<link rel="Stylesheet" type="text/css" href="jhtml/style/jHtmlArea.ColorPickerMenu.css" />

	<link href="css/story_Edit.css" rel="stylesheet" type="text/css">
<?php

if (empty($_REQUEST['PID'])) header("Location:project_List.php");
if (empty($_REQUEST['IID']))$_REQUEST['IID']=$Project['Backlog_ID'];

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
		$('#msg_div').html('You need at least have a summary for the story!');
		$('#msg_div').show();
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
	$sql = 'select story_type.Desc from story_type where story_type.Project_ID='.$_REQUEST['PID'].' AND story_type.Desc <> "'.$current.'" order by story_type.`Order`';
	$queried = $DBConn->directsql($sql);
	$menu = '<select name="Type">';
	$menu .= '<option value="' . $current . '">' . $current . '</option>';
		foreach ($queried as $result) {
		$menu .= '<option value="' . $result['Desc'] . '">' . $result['Desc'] . '</option>';
	    }
	$menu .= '</select>';
	return $menu;
}

function print_Story_Status_Dropdown($current)
{
	Global $DBConn;
	if (empty($current)) $current = 'Todo';
	$sql = 'select story_status.Desc from story_status where story_status.Project_ID='.$_REQUEST['PID'].' AND story_status.Desc <> "'.$current.'" and LENGTH(story_status.Desc)>0 order by story_status.`Order`';
	$queried = $DBConn->directsql($sql);
	$menu = '<select name="Status">';
	$menu .= '<option value="' . $current . '">' . $current . '</option>';
		foreach ($queried as $result) {
		$menu .= '<option value="' . $result['Desc'] . '">' . $result['Desc'] . '</option>';
	    }
	$menu .= '</select>';
	return $menu;
}


function print_Release_Dropdown($current){
	Global $DBConn;
	$current+=0;
	if ($current==0){
		$name = '';
	}else{
		$sql = 'select * from release_details where ID='.$current;
		$result = $DBConn->directsql($sql);
		$name= $result[0]['Name'].' ('.$result[0]['Start'].'>'.$result[0]['End'].')';
	}

	$sql = 'select * from release_details where Locked=0';
	$queried = $DBConn->directsql($sql);
	$menu = '<select name="Release">';

	$menu .= '<option value="' . $current . '">' . $name . '</option>';
	$menu .= '<option value="0"></option>';
	foreach($queried as $result) {
		$menu .= '<option value="' . $result['ID'] . '">'.$result['Name'].' ('.$result['Start'].'>'.$result['End'].')</option>';
	}
	$menu .= '</select>';
	return $menu;
}


function print_Story_Size_Radio($current,$type){
	Global $DBConn;

	if ($current=='') $current='?';
	$sql = 'select * from size where size.Type=(select Project_Size_ID from project as p where p.ID='.$_REQUEST['PID'].') order by size.`Order`';
	$queried = $DBConn->directsql($sql);
	$menu = '<div id="sizediv">';
	foreach ($queried as $result) {
		$menu .= '<input class="sizediv" type="radio" name="Size" value="' . $result['Value'].'"';
		if ($current==$result['Value'])		{
			$menu .= ' checked';
		}
		$menu .= '>' . $result['Value'] .' &nbsp; </input>';
	}
	$menu .= '</div>';
	return $menu;
}

function print_Possible_Parent($Project, $current=0){
	Global $DBConn;

	$current+=0;
	$menu = '<select name="Parent_Story_ID">';
	if ($current==0){
		$menu .= '<option value="0"></option>';
	}else{
// Fetch Current Parent.
		$sql = 'SELECT AID, ID, Summary FROM story where story.AID ='.$current;
		$result = $DBConn->directsql($sql);
		$result = $result[0];
		$menu .= '<option value="' . $result['AID'] . '">' .$result['ID'].' - '. $result['Summary'] .'</option>';
		$menu .= '<option value="0"></option>';
		$stree.='&nbsp;<a  title="Show my parent and all its children"';
		$stree .=' href="story_List.php?Type=tree&Root='.$result['ID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
		$stree .='<img src="images/tree.png"></a>';
	}

	$sql = 'select AID, ID, Summary from story where Project_ID='.$_REQUEST['PID'].' and 0<(select count(Parent_Story_ID) from story as p where p.Parent_Story_ID = story.AID) order by ID';
	$queried =  $DBConn->directsql($sql);
	foreach ($queried as $result) {
		$menu .= '<option value="' . $result['AID'] . '">' .$result['ID'].' - '. $result['Summary'] .'</option>';
	}
	$menu .= '</select>';
	$menu .=$stree;
	return $menu;
}

function Get_manual_Parent($current=0){
	Global $DBConn;
	$current+=0;
	// Fetch Current Parent.
	$sql = 'SELECT AID, ID FROM story where story.ID ='.$current.' and story.Project_ID='.$_REQUEST['PID'].' and story.Iteration_ID=(select Backlog_ID from project where project.ID='.$_REQUEST['PID'].') and (story.Status="Todo" or story.Status IS NULL)';
	$result =  $DBConn->directsql($sql);
	if(count($result)==1){
		return $result[0]['AID'];
	}
	return 0;
}


function  Update_Project_Tags($PID,$Tags){
	Global $DBConn;
	$sql= 'SELECT tags.`Desc` from tags where tags.Project_ID='.$PID;
	$tag_Row =$DBConn->directsql($sql);
	if (count($tag_Row) == 1){
		$newTags = implode(",",array_unique(explode(",", $tag_Row[0]['Desc'].",".$Tags)));
		$sql='UPDATE tags SET `Desc`="'.$newTags.'" where tags.Project_ID='.$PID;
	}else{
		$newTags = $Tags;
		$sql='INSERT INTO tags ( Project_ID, `Desc`) VALUES('.$PID.',"'.$newTags.'")';
	}
 		$DBConn->directsql($sql);
}


	echo '<div class="noshow" id="msg_div">&nbsp;</div>';
	$showForm = true;
	if (isset($_POST['saveUpdate'])){
		setcookie('ctorb',$_REQUEST['torb']);
		if (!empty($_REQUEST['manualParent'])){
			$manualPar=Get_manual_Parent($_REQUEST['manualParent']+0);
			if ($manualPar!=0){
				$_REQUEST['Parent_Story_ID']=$manualPar;
			}
		}
// some common conversions to help with the auditing
		$_REQUEST['Summary'] = htmlentities($_REQUEST['Summary'],ENT_QUOTES);
		$_REQUEST['Col_1'] = htmlentities($_REQUEST['Col_1'],ENT_QUOTES);
		$_REQUEST['As_A'] = htmlentities($_REQUEST['As_A'],ENT_QUOTES);
		$_REQUEST['Col_2'] = htmlentities($_REQUEST['Col_2'],ENT_QUOTES);
		$_REQUEST['Acceptance'] = htmlentities($_REQUEST['Acceptance'],ENT_QUOTES);
		$data=array(
			'Blocked'			=> ((isset($_REQUEST['Blocked'])) ? 1 : 0),
			'Iteration_ID' 		=> $_REQUEST['Iteration_ID'],
			'Owner_ID' 			=> $_REQUEST['Owner_ID'],
			'Release_ID' 		=> $_REQUEST['Release'],
			'Parent_Story_ID'	=> ((isset($_REQUEST['Parent_Story_ID'])) ? $_REQUEST['Parent_Story_ID'] : 0),
			'Summary' 			=> $_REQUEST['Summary'],
			'Col_1' 			=> $_REQUEST['Col_1'],
			'As_A' 				=> $_REQUEST['As_A'],
			'Col_2' 			=> $_REQUEST['Col_2'],
			'Acceptance' 		=> $_REQUEST['Acceptance'],
			'Tags' 				=> (addslashes($_REQUEST['Tags'])),
			'Type' 				=> $_REQUEST['Type'],
			'Status' 			=> $_REQUEST['Status']
		);

		if (empty($_REQUEST['AID'])){

			$temp					= $DBConn->directsql('select IFNULL(MAX(ID), 0)+1 as tmpn from story where Project_ID='.$_REQUEST['PID']);
			$data['ID']				= $temp[0]['tmpn'];
			$data['Project_ID'] 	= $_REQUEST['PID'];
			$temp					= $DBConn->directsql('select IFNULL(MAX(Epic_Rank), 0)+100 as tmpn from story where Project_ID='.$_REQUEST['PID']);
			$data['Epic_Rank']		= $temp[0]['tmpn'];
			$data['Created_By_ID'] 	= $_SESSION['ID'];
			$data['Size'] 			= $_REQUEST['Size'];


			if ($_REQUEST['torb']=='b'){
				$sql='select IFNULL(MAX(Iteration_Rank), 0)+100 as tmpn from story  where Iteration_ID='.$_REQUEST['IID'];
				$temp	= $DBConn->directsql($sql);
				$data['Iteration_Rank']	= $temp[0]['tmpn'];
			}else{
				$sql='select IFNULL(MIN(Iteration_Rank), 0)-1 as tmpn from story  where Iteration_ID='.$_REQUEST['IID'];
				$temp	= $DBConn->directsql($sql);
				$data['Iteration_Rank']	= $temp[0]['tmpn'];
			}
			$result=$DBConn->create('story',$data);
			if ($result>0) auditit($_REQUEST['PID'],0,$_SESSION['Email'],'Add new Story ','',$_REQUEST['Summary']);
		}else{
			if (Num_Children($_REQUEST['AID'] + 0)==0)	{
				$data['Size'] = $_REQUEST['Size'];
			}
			$whereClause = ' AID = '.($_REQUEST['AID'] + 0);
			$aaction='Update story ';
			$result=$DBConn->update('story',$data,$whereClause);
			if ($result>0){
				$orow= fetchusingID('*',$_REQUEST['AID'],'story');
				foreach ($orow as $key => $value){
					// that is passed in (Blocked wants some special handling)
					if($_REQUEST[$key] || ($key=='Blocked' && $orow['Blocked']==1))					{
						// and somethng has changed then log it
						if($_REQUEST[$key]!==$orow[$key] ){
							echo '# n'.$key.' '.$_REQUEST[$key]. ' - o'.$orow[$key].'</br>';
							auditit($_REQUEST['PID'],$_REQUEST['AID'],$_SESSION['Email'],$aaction.$key,$orow[$key],$_REQUEST[$key]);
						}
					}
				}
			}
		}

		if (!empty($_REQUEST['gobackto'])){
			header('Location:'.$_REQUEST['gobackto']);
		}else{
			header('Location:story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID']);
		}
		if ($result!=0){
			$showForm = false;
			Update_Parent_Points($_REQUEST['AID']);
			Update_Iteration_Points($_REQUEST['IID']);
			Update_Iteration_Points($_REQUEST['Iteration_ID']);
			Update_Project_Tags($_REQUEST['PID'],$_REQUEST['Tags']);

		}else{
			if  ($DBConn->error){
				$error = 'The form failed to process correctly.'.'<br>'.$DBConn->error;
			} else{
				$showForm = false;
			}
		}
	}
	if (!empty($error)){
		echo '<div class="error">'.$sql.'<p>'.$error.'</div>';
	}

	if ($showForm){
		if (!empty($_REQUEST['AID']))		{
			$story_Row = fetchusingID('*',$_REQUEST['AID'],'story');
			if (!$story_Row) header('Location:story_List.php?PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID']);
		}else{
			$story_Row = $_REQUEST;
		}

// if a new story, then default to the iteration we are currently in.
		if (empty($story_Row['Iteration_ID'])){
			$story_Row['Iteration_ID']=$_REQUEST['IID'];
		}
		if (empty($story_Row['Project_ID'])){
			$story_Row['Project_ID']=$_REQUEST['PID'];
		}

		$Num_Children=Num_Children($story_Row['AID']);

?>
<form method="post"  onsubmit="return checksummary(this)" action="?">

	<table align="center" border=0 cellpadding="2" cellspacing="0" width=95% >
	<tr>
		<td width="102">Story :	</td><td>
<?php
		if ($Num_Children!=0){
			echo '<a  title="Show my children (#'.$story_Row['ID'].') as the root of the tree)"';
			echo ' href="story_List.php?Type=tree&Root='.$story_Row['ID'].'&PID='.$_REQUEST['PID'].'&IID='.$_REQUEST['IID'].'">';
			echo '<img src="images/tree.png"></a>&nbsp; &nbsp;';
		}
			echo '#'.$story_Row['ID'].'&nbsp; &nbsp;';
			echo 'created by: '.Get_User($story_Row['Created_By_ID'],0).'&nbsp;on&nbsp;'.$story_Row['Created_Date'];
			echo '&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;';
			echo '<img title="Duplicate this Story without tasks" class="dupestory hidden" id="dup'.$story_Row['AID'].'" src="images/duplicate.png">&nbsp;';
			echo '<img title="Duplicate this Story and all its tasks (Owner, Status and Actual hours are reset )" class="dupestory hidden" id="dut'.$story_Row['AID'].'" src="images/duplicateandtasks.png">';


		if ($Num_Children==0){
			echo '<span class="hint">';
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
		<td>Size:</td>
		<td>

<?php
		if ($Num_Children==0){
			echo print_Story_Size_Radio($story_Row['Size'],$Project['Project_Size_ID']);
			echo '</td></tr><tr><td>&nbsp;</td><td>';
			echo iterations_Dropdown($story_Row['Project_ID'], $story_Row['Iteration_ID']);
			echo '&nbsp;&nbsp;&nbsp;';
			echo print_Story_Status_Dropdown($story_Row['Status']);
		}else{
			echo $story_Row['Size'].' points &nbsp;&nbsp;&nbsp;';
			echo '</td></tr><tr><td>&nbsp;</td><td>';
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
			<div class="blocked" > &nbsp; <input id="c1" <?=$story_Row['Blocked'] == 1 ? 'checked' : '0';?> value="1" type="checkbox" name="Blocked"><label for="c1">&nbsp;&nbsp;Blocked?&nbsp;&nbsp;</label></div>
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
	if (!empty($_REQUEST['AID'])){
		printMicromenu($story_Row['AID']);
		echo '<div class="hidden inline" id="alltasks_'.$story_Row['AID'].'"></div>';
		echo '<div class="hidden" id="commentspops_'.$story_Row['AID'].'"></div> ';
		echo '<div class="hidden" id="allupload_'.$story_Row['AID'].'"></div> ';
		echo '<div class="auditdialog hidden" id="allaudits_'.$story_Row['AID'].'"></div> ';
	}
	echo '<td></tr></table>';
	echo '	<input type="hidden" name="PID" value="'.$_REQUEST['PID'].'">';
	echo '	<input type="hidden" name="IID" value="'.$_REQUEST['IID'].'">';
	echo '	<input type="hidden" name="AID" value="'.$story_Row['AID'].'">';

	if (empty($_REQUEST['gobackto'])){
		echo '	<input type="hidden" name="gobackto" value="'.substr($_SERVER["HTTP_REFERER"],strrpos($_SERVER["HTTP_REFERER"],"/")+1).'">';
	}else{
		echo '	<input type="hidden" name="gobackto" value="'.$_REQUEST['gobackto'].'">';
	}

	if(!$isReadonly){
		echo '	<input class="btn" type="submit" name="saveUpdate" value="Update">';
		if (empty($_REQUEST['AID'])){
			if ($_COOKIE['ctorb']=='b'){
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

	<link rel="stylesheet" type="text/css" href="css/micro_menu.css" />
	<script type="text/javascript" src="scripts/micromenu-hash0dc02c21be13adc33614481961b31b0c.js"></script>

<script>
	document.forms[1].Summary.focus();
</script>
<?php
	include 'include/footer.inc.php';
?>
