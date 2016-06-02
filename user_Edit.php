<?php
require_once('include/header.inc.php');


echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo 'User Edit';
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
<script src="md5/md5.min.js"></script>
<script>
function hashit(){
	var phash=document.getElementById('pwd').value;
	if (phash.length>0)
	{
		document.getElementById('pwd').value=md5(phash);
	}


}
</script>
<?php
	$showForm = true;

	if (isset($_POST['saveUpdate'])){
		$data=array(
			'Initials' 		=> trim($_REQUEST['Initials'], "\t\n\r\0" ),
			'Disabled_User' => ((isset($_REQUEST['Disabled_User'])) ? 1 : 0),
			'Friendly_Name' => $_REQUEST['Friendly_Name'],
			'EMail' 		=> strtolower(trim($_REQUEST['EMail'], "\t\n\r\0" ))
		);
		if (empty($_REQUEST['id']))	{
			$button_name = 'Add';
			$data['Password'] = md5(trim($_REQUEST['Password'], "\t\n\r\0" ));
			$result=$DBConn->create('user',$data);
			auditit(0,0,$_SESSION['Email'],'Added User',$_REQUEST['EMail'].' - '.$_REQUEST['Friendly_Name']);
		}else{
			$button_name = ' Save';
			if (strlen($_REQUEST['Password'])>0) {
				$data['Password'] = md5(trim($_REQUEST['Password'], "\t\n\r\0" ));
			}else{
				$data['Password'] = $_REQUEST['md5'];
			}
			if ($Usr['Admin_User'] == 1) {
				if ($_REQUEST['Admin_User']==1) {
					$data['Admin_User'] =1;
				}else{
					$$data['Admin_User']=0;
				}
			}
			$whereClause = 'ID = '.($_REQUEST['id'] + 0);
			$result=$DBConn->update('user',$data,$whereClause);
		}
// updated the user, now do their access
		if ($result!=0){
			$showForm = false;
			if ($Usr['Admin_User']==1){
				$sql='DELETE from user_project where User_ID ='.$_REQUEST['id'];
				$up=$DBConn->directsql($sql);
				if ($_REQUEST['proj']){
					foreach($_REQUEST['proj'] as $proj) {
						$data=array(
							'User_ID' => $_REQUEST['id'],
							'Project_ID' => $proj
						);
						$audit=" ";
						if (isset($_REQUEST['Readonly'.$proj])){
							$data['Readonly']=1;
							$audit='Read only: True';
						}else{
							$data['Readonly']=0;
							$audit='Read only: False';
						}
						if (isset($_REQUEST['proj_admin'.$proj])){
							$data['Project_Admin']=1;
							$audit='Proj Admin: True';
						}else{
							$data['Project_Admin']=0;
							$audit='Projadmin: False';
						}
						$prj=$DBConn->create('user_project',$data);
						auditit($proj,0,$_SESSION['Email'],'User Access Updated',$_REQUEST['EMail'],Get_Project_Name($proj).'" '.$audit);
					}
				}
			}
		}else{
			$error = 'The form failed to process correctly.';
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)	{
		if ($Usr['Admin_User'] == 1 || $_REQUEST['id'] == $_SESSION['ID']){
			if (!empty($_REQUEST['id'])){
				$sql = 'SELECT * FROM user WHERE ID = '.$_REQUEST['id'];
				$user_Row  = $DBConn->directsql($sql);
				$user_Row = $user_Row[0];
			}else{
				$user_Row = $_REQUEST;
			}
			echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.'<form method="post" action="?">';

	if ($Usr['Admin_User']==1)	{
		echo '<tr><td>EMail/Username:</td>';
		echo '<td><input type="text" name="EMail" value="'.$user_Row['EMail'].'"></td></tr>';
	}else{
		echo '<tr><td>EMail/Username:</td>';
		echo '<td><input type="hidden" name="EMail" value="'.$user_Row['EMail'].'">'.$user_Row['EMail'].'</td></tr>';
	}

?>
<tr><td>Password:</td>
	<td><input type="password" id="pwd" name="Password" value=""> empty Passwords wont be changed!</td>
</tr>
<tr><td>Initials:</td>
	<td><input type="text" name="Initials" value="<?=$user_Row['Initials'];?>"></td>
</tr>
<tr><td>Friendly_Name:</td>
	<td><input type="text" name="Friendly_Name" value="<?=$user_Row['Friendly_Name'];?>"></td>
</tr>
<tr><td>Global Admin User:</td>
	<td>

<?php

	if ($Usr['Admin_User']==1)	{
?>
	<input <?=$user_Row['Admin_User'] == 1 ? 'checked' : '';?> value="1" type="checkbox" name="Admin_User">
<?php
		echo '</td></tr>';
	} else {
		echo 'No</td></tr>';
	}
?>

<tr><td>Disable User:</td>
	<td>
<?php

	if ($Usr['Admin_User']==1)	{
?>
	<input <?=$user_Row['Disabled_User'] == 1 ? 'checked' : '';?> value="1" type="checkbox" name="Disabled_User">
<?php
		echo '</td></tr>';
	} else {
		echo 'No</td></tr>';
	}
?>


<?php
	// Fetch all projects and show which ones the user has access to
		if ($Usr['Admin_User']==1 && !empty($_REQUEST['id'])){

			$psql='SELECT p.ID ID, p.Name Name, up.User_ID Access, up.Readonly, up.Project_Admin Admin from project p left join user_project up on up.Project_ID = p.ID and up.User_ID='.$_REQUEST['id'].' where p.Archived<>1';
			$proj_Row = $DBConn->directsql($psql);
			echo '<tr><td/><td>Can Access</td><td>Proj.Admin</td><td>Read Only</TD></tr>';
			if (count($proj_Row) > 0){
				$pcnt = 0;
				do	{
					$chkbox = '<tr><TD>&nbsp</td><td align=left><input type="checkbox" name=proj[] ';
					# do not change this to an ==1 , it is the user ID
					if($proj_Row[$pcnt]['Access'] <> 0) $chkbox .= ' checked ';
					$chkbox .= ' value='.$proj_Row[$pcnt]['ID'].'>'.$proj_Row[$pcnt]['ID'].' - '.$proj_Row[$pcnt]['Name'].'</td>';

					$chkbox .= '<td align=left>';
					$chkbox .= '<input type="checkbox" name=proj_admin'.$proj_Row[$pcnt]['ID'];
					if($proj_Row[$pcnt]['Admin'] ==1) $chkbox .= ' checked ';
					$chkbox .= ' value="'.$proj_Row[$pcnt]['Admin'].'" ><br>';
					$chkbox .= '</td>';

					$chkbox .= '<td align=left>';
					$chkbox .= '<input type="checkbox" name=Readonly'.$proj_Row[$pcnt]['ID'];
					if($proj_Row[$pcnt]['Readonly'] ==1) $chkbox .= ' checked ';
					$chkbox .= ' value="'.$proj_Row[$pcnt]['Readonly'].'" ><br>';
					$chkbox .= '</td></tr>';

					echo $chkbox;
					$pcnt += 1;
				} while ($pcnt < count($proj_Row));
			}
		}
?>
	<tr>
		<td colspan="2">
			<input type="hidden" name="id" value="<?=$_REQUEST['id'];?>">
			<input type="hidden" name="md5" value="<?=$user_Row['Password'];?>">
			<input type="submit" onclick="hashit()"  name="saveUpdate" value="Update">
		</td>
	</tr>
	</form>
</table>

<?php
		}
	}else{
		if ($Usr['Admin_User'] == 1){header("Location:user_List.php");}else{header("Location:project_List.php");}
	}

	include 'include/footer.inc.php';

?>