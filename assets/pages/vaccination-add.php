<?php
include 'dbaccess.php';

$vccName = $_POST['vaccine-name'];
$vccDate = $_POST['vaccine-date'];
$vccBatch = $_POST['vaccine-batch'];
$momNIC = $_POST['mama-nic'];
$appBy = $_POST['vaccine-approved'];
$vccBy = $_POST['vaccine-doneby'];

echo $vccName;
?>