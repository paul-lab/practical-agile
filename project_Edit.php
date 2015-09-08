<?php
	include 'include/header.inc.php';

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo Get_Project_Name($_REQUEST['PID']);
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
</script>



<?php

function print_Size_Type_Dropdown($current)
{
	Global $DBConn;

    	$queried = mysqli_query($DBConn, 'select * from size_type');
	if($current < 1 ) $current=1;
	$Start = '<select name="Project_Size_ID"><option value="' . $current . '">';
	$end .= '';
		while ($result = mysqli_fetch_assoc($queried)) {
			$end .= '<option value="' . $result['ID'] . '">' . $result['ID'].' - ' .$result['Desc'] . '</option>';
			if ($current == $result['ID'] + 0) {
				$Start .= $current .' - '.$result['Desc'];
			}
		    }
	$Start .= '</option>';
	$end .= '</select>';
	$menu = $Start.$end;
	return $menu;
}

function NextbacklogID()
{
	Global $DBConn;

	$today = date_create(Date("Y-m-d"));
	$today = date_format($today , 'Y-m-d');
	$isql="INSERT INTO iteration SET Points_Object_ID=".NextPointsObject().
	", Project_ID=0, Start_Date='".$today."', End_Date='2299-12-31',".
	" Comment_Object_ID=".NextIterationCommentObject().", iteration.Name = 'Backlog'";

	mysqli_query($DBConn, $isql);
	$rand=mysqli_insert_id($DBConn);
	return $rand;
}

	$showForm = true;
	if (isset($_POST['saveUpdate'])) 
	{
		if (empty($_REQUEST['PID']))
		{
			$sql_method = 'INSERT INTO';
			$button_name = 'Add';
			$whereClause = '';
			$RPBID = NextbacklogID();
			$RCIID = $RPBID;
			$RPPOBJ = NextPointsObject();
			$Insertsql = 
				',Backlog_ID = "'.$RPBID.'"'.	
				',Points_Object_ID = "'.$RPPOBJ.'"';
		}
		else
		{
			$sql_method = 'UPDATE';
			$button_name = 'Save';
			$whereClause = 'WHERE ID = '.($_REQUEST['PID']+ 0);
		}

		if ($Usr['Admin_User']==1){
			$adminsql = ', Archived = "'.$_REQUEST['Archived'].'"';
		}else{
			$adminsql = ' ';
		}
		 if (mysqli_query($DBConn, "{$sql_method} project SET 
			    Name = '".mysqli_real_escape_string($DBConn, $_REQUEST['Name']).
			"', project.Category = '".mysqli_real_escape_string($DBConn, $_REQUEST['Category']).
			"', project.Desc = '".mysqli_real_escape_string($DBConn, $_REQUEST['Desc']).
			"', Desc_1 = '".$_REQUEST['Desc_1'].
			"', As_A = '".$_REQUEST['As_A'].
			"', Col_2 = '".$_REQUEST['Col_2'].
			"', Desc_2 = '".$_REQUEST['Desc_2'].
			"', Acceptance = '".$_REQUEST['Acceptance'].
			"', Enable_Tasks = '".$_REQUEST['Enable_Tasks'].
			"', Project_Size_ID = '".$_REQUEST['Project_Size_ID'].
			"'  {$adminsql} {$Insertsql} {$whereClause}"))
		{
			if (empty($_REQUEST['PID'])){
				// set projet backlog iteration ID
				$thisproject = mysqli_insert_id($DBConn);
				$sql = 'UPDATE iteration SET Project_ID='.$thisproject.' where ID ='.$RPBID;
				mysqli_query($DBConn, $sql);
				$res = mysqli_query($DBConn, "SELECT max(ID) as qq from project"); 
				// copy template story type and status to new project
				$newproject = mysqli_fetch_assoc($res);
				
				mysqli_query($DBConn, 'INSERT INTO story_type (story_type.Project_ID, story_type.Desc, story_type.Order )  SELECT "'.$newproject[qq].'", story_type.Desc, story_type.Order FROM story_type WHERE Project_ID = 1');
				mysqli_query($DBConn, 'INSERT INTO story_status (story_status.Project_ID, story_status.Desc, story_status.Order, RGB, policy)  SELECT "'.$newproject[qq].'", story_status.Desc, story_status.Order, RGB, Policy FROM story_status WHERE Project_ID = 1');
				// give admin user rights
				$sql = 'INSERT INTO user_project  SET user_project.Project_ID ='.$thisproject.', user_project.User_ID=1, Readonly=0';
				mysqli_query($DBConn, $sql);
			}
			$showForm = false;
		}
		else
		{
			$error = 'The form failed to process correctly.'.mysqli_error($DBConn);
		}
	}
	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if (!empty($_REQUEST['PID']))
		{
			$project_Res = mysqli_query($DBConn, 'SELECT * FROM project WHERE ID = '.$_REQUEST['PID']);
			$project_Row = mysqli_fetch_assoc($project_Res);
		}
		else
		{
			$project_Res = mysqli_query($DBConn, 'SELECT As_A,  Desc_1, Col_2, Desc_2, Acceptance, Project_Size_ID FROM project WHERE ID = 1');
			$project_Row = mysqli_fetch_assoc($project_Res);
		}
		echo '<form method="post" action="?">';
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">';
					
?>
	<tr>
		<td>Category:</td>
		<td>
			<input type="text" title="Use category to group related projects in the 'My Projects' list" name="Category" value="<?=$project_Row['Category'];?>">
		</td>
	</tr>
	<tr>
		<td>Name:</td>
		<td>
			<input type="text" name="Name" value="<?=$project_Row['Name'];?>">
		</td>
	</tr>
	<tr>
		<td>Desc:</td>
		<td>
			<textarea cols="40" rows="5" wrap="soft" name="Desc"><?=$project_Row['Desc'];?></textarea>
		</td>
	</tr>

	<tr>
		<td>Col 1 Desc.:</td>
		<td>
			<input type="text" title="This is how you want to label the first large text area of a card eg Details, So That ..." name="Desc_1" value="<?=$project_Row['Desc_1'];?>">
		</td>
	</tr>
	<tr>
		<td>Enable As A:</td>
		<td>
			<input <?=$project_Row['As_A'] == 1 ? 'checked' : '';?> value="1" title="This will enable the role field for cards for this project." type="checkbox" name=" As_A">
		</td>
	</tr>

	<tr>
		<td>Enable Col 2:</td>
		<td>
			<input <?=$project_Row['Col_2'] == 1 ? 'checked' : '';?> value="1" title="enable a second text area for cards in this project. (I Need: possibly)"type="checkbox" name="Col_2">
		</td>
	</tr>
	<tr>
		<td>Col 2: Desc</td>
		<td>
			<input type="text" name="Desc_2" value="<?=$project_Row['Desc_2'];?>">
		</td>
	</tr>


	<tr>
		<td>Enable Acceptance Criteria:</td>
		<td>
			<input <?=$project_Row['Acceptance'] == 1 ? 'checked' : '';?> value="1" title="Enable that all important acceptance criteria section of a card" type="checkbox" name="Acceptance">
		</td>
	</tr>
	<tr>
		<td>Enable Story tasks (on Scrum Board):</td>
		<td>
			<input <?=$project_Row['Enable_Tasks'] == 1 ? 'checked' : '';?> value="1" type="checkbox" name="Enable_Tasks"> (Not Implemented yet!)
		</td>
	</tr>
	<tr>
		<td>Story Size Scale:</td>
		<td>
			<?=print_Size_Type_Dropdown($project_Row['Project_Size_ID']+0);?>
		</td>
	</tr>
	<tr>
		<td>Archived:</td>
		<td>
			<input <?=$project_Row['Archived'] == 1 ? 'checked' : '';?> value="1" title="Archived projects only appear to Global administrators" type="checkbox" name="Archived">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="hidden" name="PID" value="<?=$_REQUEST['PID'] + 0;?>">
<?php
if(!$isReadonly)
{
			echo '<input type="submit" name="saveUpdate" value="Update">';
}
?>
		</td>
	</tr>

</table>
</form>

<?php
	}
	else
	{
		header("Location:project_List.php");
	}

	include 'include/footer.inc.php';

?>
