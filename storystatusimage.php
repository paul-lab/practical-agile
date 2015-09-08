<? 
header("Content-type: image/png");
// size the indicator appropriately. 
	if ($_REQUEST['ST']<10)
	{ 
		$wide=11;
	}elseif ($_REQUEST['ST']>99 && $_REQUEST['ST']<=999)
	{ 
		$wide=26;
	}elseif ($_REQUEST['ST']>999)
	{ 
		$wide=35;
	}else{
		$wide=19;
	}

	$im = imagecreatetruecolor($wide,16);
	imagefill($im, 0, 0, '0x'.$_REQUEST['RGB']);
	$text_color = imagecolorallocate($im, 0, 0, 0);
	imagestring($im,4, 1, 0,  $_REQUEST['ST'],$text_color);
	imagepng ( $im); 

// Output the image & free up resources
	imagegd2($im);
	imagedestroy($im);
 ?>