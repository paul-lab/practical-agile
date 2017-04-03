<?PHP

include 'include/header.inc.php';
$showForm = true;

if (isset($_POST['acceptImport'])) header('Location: project_List.php');

if (isset($_POST['delete_existing']))
{
	if (dbdriver=='mysql'){
		$sql = 'TRUNCATE TABLE hint';
		$DBConn->directsql($sql);
	}else{
		$sql = 'delete from hint';
		$DBConn->directsql($sql);
		$sql2="delete from sqlite_sequence where name='hint'";
		$DBConn->directsql($sql2);
	}
		auditit(0, 0,$_SESSION['Email'],'Deleted existing hints');
}

	if (isset($_POST['saveUpload']))
	{
		$hasError = false;
		// loop through the records to be imported.
		$handle = fopen($_FILES["file"]["tmp_name"], "r");
		if ($handle){
			while (($data = fgets($handle)) !== false)	{
				$sql = 'INSERT INTO hint (Hint_Text) values("'.htmlentities( $data,ENT_QUOTES).'")';
				$cnt= $DBConn->directsql($sql);
				if($cnt >0){
					echo '<br> Imported :'. $data;
				}else{
					$hasError = true;
					echo('<br>Error on record '.$data);
				}
			}
		}else{
			echo 'Unable to open hint file<p>';
			echo $_FILES['file']['name'];
			$hasError = true;
		}
		$showForm = false;
		auditit(0, 0,$_SESSION['Email'],'Imported new hints',$_FILES['file']['name']);
?>
				<center><p><p><form  enctype="multipart/form-data" method="post" action="?">
				<br><input type="submit" name="acceptImport" value="OK">
				</form></center>
<?php
	}
	if ($showForm)
	{
		echo	'<center><p><form  enctype="multipart/form-data" method="post" action="?">';
?>
				<input type="hidden" name="MAX_FILE_SIZE" value="2097152" />
				Import hints: <input type="file" size="50" name="file" /><p>
				Delete all existing hints : <input 'checked' value="1" type="checkbox" name="delete_existing" /><p>
				<input type="submit" name="saveUpload" value="Import">
			</form></center>

<br><br>Notes:<br>
<ul>
<li>One Hint per line.
<li>Empty lines will be imported.
<li>HTML Code will be escaped out
</ul>


<?php
	}
//	include 'include/footer.inc.php';
?>
