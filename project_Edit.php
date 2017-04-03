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

function print_Size_Type_Dropdown($current){
	Global $DBConn;
	$resultres=$DBConn->directsql('select * from size_type');
	if($current < 1 ) $current=1;
	$Start = '<select name="Project_Size_ID"><option value="' . $current . '">';
	$end .= '';
	foreach ($resultres as $result) {
		$end .= '<option value="' . $result['ID'] . '">' . $result['ID'].' - ' .$result['Desc'] . '</option>';
		if ($current == $result['ID'] + 0) {
			$Start .= $current .' - '.$result['Desc'];
		}
	}
	unset($resultres);
	$Start .= '</option>';
	$end .= '</select>';
	$menu = $Start.$end;
	return $menu;
}

function NextbacklogID($thisproject){
	Global $DBConn;
	$today = date_create(Date("Y-m-d"));
	$today = date_format($today , 'Y-m-d');
	$data=array(
		'Points_Object_ID'	=> NextPointsObject($thisproject),
		'Project_ID'		=> $thisproject,
		'Start_Date'		=> $today,
		'End_Date'			=>'2299-12-31 23:59:59',
		'Comment_Object_ID'	=> NextIterationCommentObject(),
		'Name'				=> 'Backlog'
	);
	return ($DBConn->create('iteration',$data));
}

	$showForm = true;


	if (isset($_POST['saveUpdate'])){
		$data=array(
			'Name' 				=> htmlentities($_REQUEST['Name']),
			'Category' 			=> htmlentities($_REQUEST['Category']),
			'Desc' 				=> htmlentities($_REQUEST['Desc']),
			'Desc_1' 			=> $_REQUEST['Desc_1'],
			'As_A' 				=> ((isset($_REQUEST['As_A'])) ? 1 : 0),
			'Col_2' 			=> $_REQUEST['Col_2'],
			'Desc_2' 			=> $_REQUEST['Desc_2'],
			'Acceptance' 		=> ((isset($_REQUEST['Acceptance'])) ? 1 : 0),
			'Enable_Tasks' 		=> ((isset($_REQUEST['Enable_Tasks'])) ? 1 : 0),
			'Project_Size_ID'	=> $_REQUEST['Project_Size_ID'],
			'Vel_Iter'	=> $_REQUEST['Vel_Iter']
		);
		if ($Usr['Admin_User']==1){
			$data['Archived'] = $_REQUEST['Archived'];
		}
		if (empty($_REQUEST['PID'])){
			$button_name = 'Add';
			$data['Backlog_ID']	= 0;
			$data['Archived']	= 0;
			$result=$DBConn->create('project',$data);
		}else{
			$button_name = 'Save';
			$whereClause = 'ID = '.($_REQUEST['PID']+ 0);
			$result=$DBConn->update('project',$data,$whereClause);
		}

		if ($result<>0)	{
			if (empty($_REQUEST['PID'])){
				$thisproject = $result;
				$RPBID = NextbacklogID($thisproject);
				$RPPOBJ = NextPointsObject($thisproject);
				$data=array(
					'Backlog_ID' 		=> $RPBID,
					'Points_Object_ID'	=> $RPPOBJ
				);
				$whereClause = 'ID = '.$thisproject;
				$result=$DBConn->update('project',$data,$whereClause);
				$DBConn->directsql( 'INSERT INTO story_type (Project_ID, `Desc`, `Order` )  SELECT "'.$thisproject.'", `Desc`, `Order` FROM story_type WHERE Project_ID=1');
				$DBConn->directsql( 'INSERT INTO story_status (Project_ID, `Desc`, `Order`, RGB, policy)  SELECT "'.$thisproject.'", `Desc`, `Order`, RGB, Policy FROM story_status WHERE Project_ID=1');
				// give admin user rights
				$sql = 'INSERT INTO user_project (`Project_ID`, `User_ID`, `Readonly`) VALUES('.$thisproject.',1,0)';
				$DBConn->directsql($sql);
			}else{
				$thisproject = $_REQUEST['PID'];
			}
			Update_Project_Points($thisproject);
			$showForm = false;
		}else{
			if($DBConn->error){
				$error = 'The form failed to process correctly.'.'<br>'.$DBConn->error;
			}else{
				$showForm = false;
			}
		}
	}

	if (!empty($error))	echo '<div class="error">'.$error.'</div>';

	if ($showForm)	{
		if (!empty($_REQUEST['PID'])){
			$project_Row = $DBConn->directsql('SELECT * FROM project WHERE ID = '.$_REQUEST['PID']);
		}else{
			$project_Row = $DBConn->directsql('SELECT As_A,  Desc_1, Col_2, Desc_2, Acceptance, Project_Size_ID, Vel_Iter FROM project WHERE ID = 1');
		}
		$project_Row=$project_Row[0];
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
			<td>Iterations for Velocity:</td>
			<td>
				<input type="text" size=2 title="Number of completed Iterations to use to calculate Project velocity" name="Vel_Iter" value="<?=$project_Row['Vel_Iter'];?>">
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
			if(!$isReadonly){
				echo '<input type="submit" name="saveUpdate" value="Update">';
			}
?>
			</td>
		</tr>

	</table>
	</form>

<?php
	}else{
		header("Location:project_List.php");
	}

	include 'include/footer.inc.php';
?>