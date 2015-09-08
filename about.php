<?php
	require_once('include/dbconfig.inc.php');

 	// check version 
        $sql = 'Select * from dbver where ID=1';
        $res = mysqli_query($DBConn, $sql);
	$row=mysqli_fetch_assoc($res);

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
<p><h2><?php echo 'Database ver: '.$row['CurrVer']; ?></h2>
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
