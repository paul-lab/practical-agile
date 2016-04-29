<?php
$dir = '../../usr/local/sqlite/practicalagile.db';
$db  = new SQLite3($dir) or die("cannot open the database");

$results = $db->query("SELECT * FROM user");

while ($row = $results->fetchArray()) {

    var_dump($row);

}
?>