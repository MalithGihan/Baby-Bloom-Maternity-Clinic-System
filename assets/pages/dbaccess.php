<?php
$host='127.0.0.1';
$user='bbadmin';       
$pass='qwertyuiop';    
$db  ='babybloom';

$con = mysqli_connect($host,$user,$pass,$db);
if (!$con) { die('DB connection failed: ' . mysqli_connect_error()); }
mysqli_set_charset($con,'utf8mb4');
