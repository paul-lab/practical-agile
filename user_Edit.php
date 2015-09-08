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

	if (isset($_POST['saveUpdate'])) 
	{	
		if (empty($_REQUEST['id']))
		{
			$sql_method = 'INSERT INTO';
			$button_name = 'Add';
			$whereClause = '';
			$password = md5($_REQUEST['Password']);
		}
		else
		{
			$sql_method = 'UPDATE';
			$button_name = ' Save';
			$whereClause = ' WHERE ID = '.($_REQUEST['id'] + 0);
			if (strlen($_REQUEST['Password'])>0) {
				$password = md5($_REQUEST['Password']);
			}
			else{
				$password = $_REQUEST['md5'];
			}
		}

if ($Usr['Admin_User'] == 1) 
{
	if ($_REQUEST['Admin_User']==1) 
	{
		$whereClause = ', Admin_User=1 '.$whereClause;
	}else{
		$whereClause = ', Admin_User=0 '.$whereClause;
	}
}

	if ($Usr['Admin_User'] == 1 || $_REQUEST['id'] == $_SESSION['ID'])
	{

		$sql= $sql_method.' user SET Initials = "'.trim($_REQUEST['Initials'], "\t\n\r\0" ).'" ,'.
				' Disabled_User = "'.$_REQUEST['Disabled_User'].'" ,'.
				' Friendly_Name = "'.$_REQUEST['Friendly_Name'].'" ,'.
				' EMail = "'.strtolower(trim($_REQUEST['EMail'], "\t\n\r\0" )).'"';
		if (strlen(trim($password, "\t\n\r\0" ))>0)
		{
			$sql.= ', Password ="'.trim($password, "\t\n\r\0" ).'" ';
		}
		$sql.= $whereClause;

			if (mysqli_query($DBConn, $sql))
			{
				if ($sql_method=='INSERT INTO')
				{
					auditit(0,0,$_SESSION['Email'],'Added User',$_REQUEST['EMail'].' - '.$_REQUEST['Friendly_Name']);
				}
				$showForm = false;
				if ($Usr['Admin_User']==1)
				{
					mysqli_query($DBConn, 'DELETE from user_project where User_ID ='.$_REQUEST['id']);
					if ($_REQUEST['proj'])
					{	
						foreach($_REQUEST['proj'] as $proj) {
							$sql= 'INSERT into user_project set User_ID='.$_REQUEST['id'].', Project_ID='.$proj.' ';
							$audit=" ";
							if (isset($_REQUEST['Readonly'.$proj])){$sql.= ', Readonly =1'; $audit.='Read only: True';}
							if (isset($_REQUEST['proj_admin'.$proj])){$sql.= ', Project_Admin=1'; $audit.='Proj Admin: True';}
							mysqli_query($DBConn, $sql);
							auditit($proj,0,$_SESSION['Email'],'Alter access',$_REQUEST['EMail'],Get_Project_Name($proj).'" '.$audit);
						}
					}
				}
			}
			else
			{
				$error = 'The form failed to process correctly.'.mysqli_error($DBConn);
			}
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if ($Usr['Admin_User'] == 1 || $_REQUEST['id'] == $_SESSION['ID']){
			if (!empty($_REQUEST['id']))
			{
				$user_Res = mysqli_query($DBConn, 'SELECT * FROM user WHERE ID = '.$_REQUEST['id']);
				$user_Row = mysqli_fetch_assoc($user_Res);
			}
			else
			{
				$user_Row = $_REQUEST;
			}
			echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.'<form method="post" action="?">';
?>


<tr><td>EMail/Username:</td>
	<td><input type="text" name="EMail" value="<?=$user_Row['EMail'];?>"></td>
</tr>
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

	if ($Usr['Admin_User']==1)
	{


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

	if ($Usr['Admin_User']==1)
	{


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
		if ($Usr['Admin_User']==1 && isset($_REQUEST['id']))
		{

			$psql='SELECT p.ID ID, p.Name Name, up.User_ID Access, up.Readonly, up.Project_Admin Admin from project p left join user_project up on up.Project_ID = p.ID and up.User_ID='.$_REQUEST['id'].' where p.Archived=0';
			$proj_Res = mysqli_query($DBConn, $psql);
			$proj_Row = mysqli_fetch_assoc($proj_Res);
			echo '<tr><td/><td>Can Access</td><td>Proj.Admin</td><td>Read Only</TD></tr>';
			do
			{
				$chkbox = '<tr><TD>&nbsp</td><td align=left><input type="checkbox" name=proj[] ';
				if($proj_Row['Access'] > 0) $chkbox .= ' checked ';
				$chkbox .= ' value='.$proj_Row['ID'].'>'.$proj_Row['ID'].' - '.$proj_Row['Name'].'</td>';
				
				$chkbox .= '<td align=left>';
				$chkbox .= '<input type="checkbox" name=proj_admin'.$proj_Row['ID'];
				if($proj_Row['Admin'] > 0) $chkbox .= ' checked ';
				$chkbox .= ' value="'.$proj_Row['Admin'].'" ><br>';
				$chkbox .= '</td>';
				
				$chkbox .= '<td align=left>';
				$chkbox .= '<input type="checkbox" name=Readonly'.$proj_Row['ID'];
				if($proj_Row['Readonly'] > 0) $chkbox .= ' checked ';
				$chkbox .= ' value="'.$proj_Row['Readonly'].'" ><br>';
				$chkbox .= '</td></tr>';

				echo $chkbox;
			} while ($proj_Row = mysqli_fetch_assoc($proj_Res));
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
	}
	else
	{
		if ($Usr['Admin_User'] == 1){header("Location:user_List.php");}else{header("Location:project_List.php");}
	}

	include 'include/footer.inc.php';

?>