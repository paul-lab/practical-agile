<?php
	require_once('include/dbconfig.inc.php');
	require_once('include/common.php');

	$user_details = check_user($_SESSION['user_identifier']);
	if(!$user_details){
		exit();
	}

function v4() {
    return sprintf('%04x%04x%04x%04x%04x%04x%04x%04x',

      // 32 bits for "time_low"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff),

      // 16 bits for "time_mid"
      mt_rand(0, 0xffff),

      // 16 bits for "time_hi_and_version",
      // four most significant bits holds version number 4
      mt_rand(0, 0x0fff) | 0x4000,

      // 16 bits, 8 bits for "clk_seq_hi_res",
      // 8 bits for "clk_seq_low",
      // two most significant bits holds zero and one for variant DCE1.1
      mt_rand(0, 0x3fff) | 0x8000,

      // 48 bits for "node"
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
  }


// a list of valid filetypes (MUST end with a ,
include('include/validfiletypes.php');


$message = 'No file uploaded!';
//if they DID upload a file...
if($_FILES['file']['name'])
{
	$message ='';
	//if no errors...
	if(!$_FILES['file']['error'])
	{
		//now is the time to modify the future file name and validate the file
		$fext = explode(".", $_FILES['file']['name']);
		$fileType = $fext[count($fext)-1];
		$new_file_name = strtoupper(v4()); //rename file

		// Check valid filetypes
		$pos = strrpos($validfiletypes,$fileType.',');
		if ($pos === false) { // note: three equal signs
			$valid_file = false;
			$message = 'Bad File type.';
		}else{
			$valid_file = true;
		}
		if($_FILES['file']['size'] > (2097152)) //can't be larger than 2 MB
		{
			$valid_file = false;
			$message = 'Oops!  Your file size is to large. (Max 2MB)';
		}
		//if the file has passed the tests
		if($valid_file)
		{
			//move it to where we want it to be
			move_uploaded_file($_FILES['file']['tmp_name'], getcwd().'/upload/'.$new_file_name.'.'.$fileType);
			$fileSize =  $_FILES['file']['size'];
			$query = "INSERT INTO upload (`AID`, `Name`, `Desc`, `Size`, `Type` ) ".
			"VALUES (".$_REQUEST['AID'].", UNHEX('".$new_file_name."'),'".$_FILES['file']['name']."', ".$fileSize.", '".$fileType."')";
			$DBConn->directsql($query);
			$message = '';
			auditit($_REQUEST['PID'],$_REQUEST['AID'],$_SESSION['Email'],'Uploaded File',$_FILES['file']['name'],$new_file_name);
		}else{
			$message = 'Invalid file.';
		}
	}
	//if there is an error...
	else
	{
		//set that to be the returned message
		$message = 'Ooops!  Your upload triggered the following error:  '.$_FILES['file']['error'];
	}
}
echo $message;
?>
