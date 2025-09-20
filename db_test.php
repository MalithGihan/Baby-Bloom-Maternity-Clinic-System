<?php
require __DIR__ . '/path/to/your/db-connection-file.php'; 
$r = mysqli_query($con, "SHOW TABLES");
if (!$r) { die(mysqli_error($con)); }
echo "<h3>Connected. Tables:</h3><ul>";
while ($row = mysqli_fetch_row($r)) { echo "<li>{$row[0]}</li>"; }
echo "</ul>";
