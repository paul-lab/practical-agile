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
	include 'include/header.inc.php';


	echo '<div class="thisuser hidden" id='.$_SESSION['Email'].'></div>';
	echo '<div class="hidden" id="phpbread"><a href="project_List.php">My Projects</a>->';
	echo '<a href="project_Summary.php?PID='.$_REQUEST['PID'].'">';
		echo Get_Project_Name($_REQUEST['PID']);
	echo '</a>';
	echo '->Estimation</div>';

?>

	<script type="text/javascript" src="scripts/story_estimation-hash2484486448f3e59d39a04b37ea1faff3.js" type="text/javascript" charset="utf-8"></script>

<?php


function print_Story_Size_Radio($type){
	Global $DBConn;
	$sql = 'select * from size where size.Type=(select Project_Size_ID from project as p where p.ID= ?) order by size.`Order`';
	$queried = $DBConn->directsql($sql, $_REQUEST['PID']);
	$menu = '<div id="sizeselect">';
		foreach ($queried as $result) {
			$menu .= '<input id="sizediv" type="radio" name="Size" value="' . $result['Value'].'"';
			$menu .= '>' . $result['Value'] .' &nbsp; </input>';
		}

	$menu .= '</div>';
	return $menu;
}

function Print_Users(){
// get current valid project users
	Global $DBConn;
	$sqlp = 'SELECT * FROM User LEFT JOIN user_project ON user.ID  = user_project.User_ID  WHERE user_project.Project_ID= ? and Disabled_User !=1';
	$usr_Row =  $DBConn->directsql($sqlp, $_REQUEST['PID']);
	foreach ($usr_Row as $result){
		echo '<tr>';
		echo '<td><img class="hidden hideit" id="t'.$result['EMail'].'" src="images/tick-small.png"></td>';
		echo '<td>'.$result['EMail'];
		echo ' &nbsp; '.$result['Friendly_Name'];
		echo '<td class="larger clearit" id="s'.$result['EMail'].'">&nbsp;</td>';
		echo '</td></tr>';
	}
}

	if (!empty($error))	echo '<div class="error">'.$error.'</div>';

	echo '<table align="center" cellpadding="6" cellspacing="0" border="0">';

?>
		<tr>
			<td>&nbsp;</td>
			<td class="larger">
				Select '<b>Clear Votes</b>' to start voting session.<br>
			</td>
		</tr>
		<tr>
			<td><input class="btn" id="Clear" type="button" name="Clear" value="Clear Votes"></td>
			<td><input class="btn" id="Show" type="button" name="Show" value="Show Votes"></td>
		</tr>
		<tr>
			<td>Estimate:</td>
			<td>
				<?= print_Story_Size_Radio($Project['Project_Size_ID']);?>
			</td>
		</tr>
				<tr>
			<td>&nbsp;</td>
			<td>
			<table>
				<?=Print_Users();?>
			</table>
			</td>
		</tr>

		<tr>
		<td>Average:	</td>
		<td class="evenlarger clearit" id="estave">&nbsp;</td>
		</tr>

	</table>
	<p>
<center>
	Votes cast <b>after</b> 'Show Votes' will not be taken into account unless you click another 'Show Votes'.
</center>
<?php

	include 'include/footer.inc.php';
?>