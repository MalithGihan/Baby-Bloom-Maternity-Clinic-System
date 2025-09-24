<?php
session_start();
//This file is for Argon2 testing only

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

$charithPass = "charith";
$lalPass = "lal";
$saumyaPass = "saumya";
$sithaPass = "sitha";
$malaPass = "mala";
$mindulaPass = "mindula";
$shiPass = "shali88";

echo password_hash($charithPass, PASSWORD_ARGON2ID);
echo "<br>";
echo password_hash($lalPass, PASSWORD_ARGON2ID);
echo "<br>";
echo password_hash($saumyaPass, PASSWORD_ARGON2ID);
echo "<br>";
echo password_hash($sithaPass, PASSWORD_ARGON2ID);
echo "<br>";
echo password_hash($malaPass, PASSWORD_ARGON2ID);
echo "<br>";
echo password_hash($mindulaPass, PASSWORD_ARGON2ID);
echo "<br>";
echo password_hash($shiPass, PASSWORD_ARGON2ID);
echo "<br>";

?>