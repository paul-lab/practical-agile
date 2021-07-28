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
	require_once('include/common.php');
	require_once('include/dbconfig.inc.php');

$APP_VER='2.59';

    /*if user wants to login*/
    if(isset($_POST['username'])){
	    $user_data = login_user( $_POST['username'] , $_POST['password'] );
	    if (!$user_data){
	    	$error = 'Login failed.<br> Your account may have been disabled.';
	    }else{
			$_SESSION['user_identifier'] = $user_data['session_identifier'];
			$_SESSION['ID'] = $user_data['ID'];
			$_SESSION['Email'] = $user_data['Email'];
			$_SESSION['Name'] = $user_data['Friendly_Name'];
			$fromhere = $_GET['gobackto'];
			if(!$fromhere){
				header("Location:project_List.php");
			}else{
				if ($_COOKIE['cbadcnt']>1){
					header("Location:index.php");
				}else{
			        	header("Location:".$fromhere);
				}
			}
		}
	}else{
		// check database and app version and update database if needed
		$row =  $DBConn->read('dbver', 'ID=1');
		if (count($row) > 0) {
		//echo 'DBver: '.$row[0]['CurrVer'].' Using: '.dbdriver;
		$Ufile='_UpdateFrom-'.$row[0]['CurrVer'].'.txt';
		if (file_exists($Ufile)){
			$lines = file($Ufile);
			if (is_array($lines)){
				foreach ($lines as $line_num => $line){
						$sql = $line;
						$row=$DBConn->directsql($sql);
				}
			}
			header("Location:index.php?dbu=true");
		}
		if($APP_VER!=$row[0]['appver']){
			$DBConn->update('dbver', array('appver' => $APP_VER ), 'ID = 1');}
		}else{
	        // no dbver table so we must be very very old
			$sql = "CREATE TABLE `dbver` (  `ID` integer, `CurrVer` text);";
			$row=$DBConn->directsql($sql );
			$DBConn->create('dbver ', array('ID' => '1', 'CurrVer' => '1.0'));
			header("Location:index.php");
		}
	}
?>

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
	echo 'DBver: '.$row[0]['CurrVer'].' Using: '.dbdriver;
?>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="md5/md5.min.js"></script>
<script>
function hashit(){
// we really do not want to use the default admin password
	var phash=document.getElementById('pwd').value;
	if (phash=='admin')
	{
		alert('Please change the default "admin" password!');
	}
	document.getElementById('pwd').value=md5(phash);
}
</script>
<title>Practical Agile Scrum tool: Please login</title>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
</head>
<body>

<div class="header noPrint">
	<a href="http://www.practicalagile.co.uk"><img src="images/logo-large.png"></a>
	<a class="hint" href="help/help.html" target="_blank" title="Help"><b>Help</b><br>App ver.<?php echo $APP_VER;?> &nbsp; </a>
</div>

<center>
<h2>Practical Agile</h2>
<?php
if ($_GET['dbu']=='true')
{
	echo '<h3><font color="red">DATABASE UPDATED</h3></font>';
}
?>
<h3> The Scrum tool that only does what it needs to.</h3>
       <br>
       <form method="post" action="" id="form_Login">
		<table>
			<tr>
				<td>e-Mail/User :</td>
				<td><input type="text" id="nam" name="username" value="" />
			</tr>
			<tr>
				<td align="Right">Password :
			        <td><input type="password" id="pwd" name="password" value="" />
			</tr>
			<tr><td colspan=2>&nbsp;</td></tr>
			<tr>
				<td>&nbsp;<td><input  class="btn" type="submit" title="Click here to login" onclick="hashit();" value="Login" />
			</tr>
		</table>
		<?php echo $error; ?>
        </form>
</center>
<script>
document.getElementById('nam').focus();
</script>
</body>
</html>
