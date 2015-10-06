<?php
include 'include/header.inc.php';

echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
echo 'Report Edit';
echo '</div>';



function QryType($current)
{

    	if($current < 1 ) $current=1;
	$end = '<select id="external" name="External">';
	if($current==1)
	{
		$end .= '<option selected value="1">Story List output</option>';
		$end .= '<option value="2">Raw Data output</option>';
	}else{
		$end .= '<option value="1">Story List output</option>';
		$end .= '<option selected value="2">Raw Data output</option>';
	}
	$end .= '</option>';
	$end .= '</select>';
	return $end;
}

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
<script type="text/javascript" src="scripts/report_edit-hash8446299ec575e00da191f4c95ed2fa6a.js" type="text/javascript" charset="utf-8"></script>
<?php

	$showForm = true;
	if (isset($_POST['saveUpdate']))
	{

		if (empty($_REQUEST['ID']))
		{
			$sql_method = 'INSERT INTO';
			$button_name = 'Add';
			$whereClause = '';
		}else{
			$sql_method = 'UPDATE';
			$button_name = 'Save';
			$whereClause = 'WHERE ID = '.($_REQUEST['ID'] + 0);
		}
		$_REQUEST['Desc']=str_replace('"', '""', $_REQUEST['Desc']);	// double up on Quotes
		$_REQUEST['Desc']=str_replace("'", "''", $_REQUEST['Desc']);
		$_REQUEST['QSQL']=str_replace('"', '""', $_REQUEST['QSQL']);	// double up on Quotes
		$_REQUEST['QSQL']=str_replace("'", "''", $_REQUEST['QSQL']);

		if (mysqli_query($DBConn, "{$sql_method} queries SET
			queries.Desc = '".$_REQUEST['Desc']."',
			queries.Qseq = '".$_REQUEST['Qseq']."',
			queries.QSQL = '".$_REQUEST['QSQL']."',
			queries.External = '".$_REQUEST['External']."',
			queries.Qorder = '".$_REQUEST['Qorder']."' {$whereClause}"))
		{
			$showForm = false;

		}else{
			$error = '<br>The form failed to process correctly.'.mysqli_error($DBConn);
		}
	}

	if (!empty($error))
		echo '<div class="error">'.$error.'</div>';

	if ($showForm)
	{
		if (!empty($_REQUEST['ID']))
		{
			$Qry_Res = mysqli_query($DBConn, 'SELECT * FROM queries WHERE ID = '.$_REQUEST['ID']);
			$Qry_Row = mysqli_fetch_assoc($Qry_Res);
		}
		else
		{
			$Qry_Row = $_REQUEST;
		}
		echo '<table align="center" cellpadding="6" cellspacing="0" border="0">'.
					'<form method="post" action="?">';
?>

	<tr>
		<td>&nbsp;</td>
		<td>
			<b> Edit Report</b>
		</td>
	</tr>
	<tr>
		<td>ID:</td>
		<td>
			<?=$Qry_Row['ID'];?>
		</td>
	</tr>
	<tr>
		<td>Seq:</td>
		<td>
			<input type="text" name="Qseq" value="<?=$Qry_Row['Qseq'];?>">
		</td>
	</tr>
		<tr>
		<td>Desc:</td>
		<td>
			<input type="text" name="Desc" value="<?=$Qry_Row['Desc'];?>">
		</td>
	</tr>


<?php
 echo '<tr><td>'.QryType($Qry_Row['External']).'</td><td><div id="extrasql">';

if ($Qry_Row['External']!=2)
{
echo 'SELECT * FROM story where story.Project_ID="{Project}" and (';
}else{
echo '&nbsp';
}
echo '</div></td></tr>';
?>
	<tr>
		<td>SQL:</td>
		<td>


<textarea cols="60" rows="10" wrap="soft" name="QSQL"><?=$Qry_Row['QSQL'];?></textarea>
		</td>
	</tr>
<?php
if ($Qry_Row['External']!=2)
{
echo '<tr><td>&nbsp;</td><td><div id="extrasqlend">)</div></td></tr>';
}
?>
	<tr>
		<td>Order by:</td>
		<td>
			<input type="text" class="w100" name="Qorder" value="<?=$Qry_Row['Qorder'];?>">
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<input type="hidden" name="ID" value="<?=$Qry_Row['ID'];?>">
			<input type="submit" name="saveUpdate" value="Update">
		</td>
	</tr>
	</form>
</table>

<?php
	}
	else
	{
		header('Location:report_List.php');
	}
	include 'include/footer.inc.php';
?>
