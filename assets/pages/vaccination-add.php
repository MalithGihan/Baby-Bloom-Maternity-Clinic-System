<?php
include 'dbaccess.php';

$vccName = $_POST['vaccine-name'];
$vccDate = $_POST['vaccine-date'];
$vccBatch = $_POST['vaccine-batch'];
$momNIC = $_POST['mama-nic'];
$appBy = $_POST['vaccine-approved'];
$vccBy = $_POST['vaccine-doneby'];

$sql = "INSERT INTO vaccination_report (vaccination,date,batchNo,NIC,approvedBy,vaccinatedBy) VALUES (?,?,?,?,?,?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssssss",$vccName,$vccDate,$vccBatch,$momNIC,$appBy,$vccBy);
$stmt->execute();

//echo $NIC;
//echo $vccName;
echo '<script>';
echo 'alert("Vaccination details added successfully!");';
echo 'window.location.href="mw-vaccination-details.php?id=' . $momNIC . '";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$stmt->close();
$con->close();
?>