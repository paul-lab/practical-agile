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
	require_once('include/dbconfig.inc.php');

 	// check version
	$sql = 'Select * from dbver where ID=1';
	$row=$DBConn->directsql($sql);
	$row=$row[0];
?>
<script>
	document.title = 'Practical Agile:About';
</script>
<link rel="stylesheet" type="text/css" href="css/stylesheet.css" />
<head>
</head>
<body>
<center>
<h1>Practical Agile Scrum tool</h1>
<p><h2><?php echo 'Application ver: '.$row['appver']; ?></h2>
<p><h2><?php echo 'Database ver: '.$row['CurrVer'] ?></h2>
<p>
<p><a href="help/help.html" title="Help (.html)">Help (.html)</a>
<p><a href="help/help.pdf" title="Help (.pdf)">Help (.pdf)</a>
<p>
<p><a href="mailto:scrumtool@practicalagile.co.uk?subject=Scrum%20tool%20feedback">Email us your feedback</a>
</xcenter>
<p><table><tr><td>

<pre>
<?php
	include('_Releasenote.txt');
?>
</pre>
</td></tr></table>
</body>
