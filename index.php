<?php
	require_once('include/dbconfig.inc.php');
        require_once('include/common.php');

$APP_VER='2.55';

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
				if ($_COOKIE['cbadcnt']>1) 
				{
					header("Location:index.php");
				}else{
			        	header("Location:".$fromhere);
				}
		        }
	        }      
        }else{
        	// check version and update database if needed
	        $sql = 'Select * from dbver where ID=1';
	        $res = mysqli_query($DBConn, $sql);
	        if ($row = mysqli_fetch_assoc($res)) {
			echo 'DBver: '.$row['CurrVer'];
			$Ufile='_UpdateFrom-'.$row['CurrVer'].'.txt';
			if (file_exists($Ufile)){
				$lines = file($Ufile); 

				if (is_array($lines)){
					foreach ($lines as $line_num => $line) 
					{ 
						$sql = $line;
						mysqli_query($DBConn, $sql);
					}
				}
				header("Location:index.php?dbu=true");
			}
			if($APP_VER!=$row['CurrVer']){
				$sql = 'update dbver set appver = '.$APP_VER.' where ID=1';
				$res = mysqli_query($DBConn, $sql);
			}
	        }else{
	        // no dbver table so we must be very very old
		        $sql = "CREATE TABLE `dbver` (  `ID` integer, `CurrVer` text) ENGINE=InnoDB DEFAULT CHARSET=ascii;";
		        mysqli_query($DBConn, $sql);
		        $sql = "Insert into dbver set ID=1, CurrVer='1.0'";
		        mysqli_query($DBConn, $sql);
		        header("Location:index.php");
	        }

        }
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<script src="md5/md5.min.js"></script> 
<script>
function hashit(){
	var phash=document.getElementById('pwd').value;
	phash=md5(phash);
	if (phash=='21232f297a57a5a743894a0e4a801fc3')
	{
		alert('Please change the default password!');
	}
	document.getElementById('pwd').value=phash;
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
			        <td><input type="password" id="pwd"name="password" value="" />
			</tr>    
			<tr><td colspan=2>&nbsp</td></tr> 
			<tr>
				<td>&nbsp<td><input type="submit" title "Click here to login" onclick="hashit();"value="Login" />
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
