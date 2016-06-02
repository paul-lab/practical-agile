<?php
	include 'include/header.inc.php';
echo '<div class="hidden" id="phpbread"><a href="releaseDetails_List.php">Releases</a>->';
echo 'Release Details';
echo '</div>';
?>
<script>
$(function() {
	document.title = 'Practical Agile: '+$("#phpbread").text().substring(17);
	$("#breadcrumbs").html($("#phpbread").html());
	if ($("#phpnavicons")){
		$("#navicons").html($("#phpnavicons").html());
	}

	$('.date').datepicker({
   		numberOfMonths: 2,
  		dateFormat: "yy-mm-dd",
		showButtonPanel: true
	});
});
</script>
<?php
	$showForm = true;
	if ($_REQUEST['delete']){
		// count the # of stories in this release and only delete if zero
		$tsql = 'SELECT count(*) as relcount, sum(Size) as relsize FROM story where story.Release_ID='.($_REQUEST['id'] + 0);
		$t_row = $DBConn->directsql($tsql);
		$t_row = $t_row[0];
		if ($t_row['relcount'] == 0){
			$DBConn->directsql('DELETE FROM release_details WHERE ID = '.($_REQUEST['id'] + 0));
			$showForm = false;
			$deleted = true;
		}
	}
	else if ($_REQUEST['nodelete']){
		$showForm = false;
		$deleted = false;
	}

	if ($showForm){
		$Row= fetchusingID('*',$_REQUEST['id'],'release_details');
		echo '<form method="post" action="?">'.
					'<p>Are you sure you want to delete release <p><b>'.$Row['Name'].' ('.$Row['Start'].' to '.$Row['End'].')?</b><p>'.
					'<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'.
					'<input type="submit" name="delete" value="Yes, Delete"> &nbsp; '.
					'<input type="submit" name="nodelete" value="No, Don\'t Delete">'.
				 '</form>';
	}else{
		header('Location:releaseDetails_List.php');
	}

	include 'include/footer.inc.php';

?>