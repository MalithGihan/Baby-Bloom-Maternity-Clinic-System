<?php
include '../shared/db-access.php';

$NIC = $_POST['mama-nic']; 
$mamaHeight = floatval($_POST['mama-height']);
$mamaBloodGroup = $_POST['mama-blood-group'];
$mamaHubBloodGroup = $_POST['hub-blood-group'];

$sql = "INSERT INTO basic_checkups (height, blood_group, hub_blood_group, NIC) VALUES (?,?,?,?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("dsss", $mamaHeight, $mamaBloodGroup, $mamaHubBloodGroup, $NIC);
$stmt->execute();

//echo $NIC;
//echo $vccName;
echo '<script>';
echo 'alert("Basic health details added successfully!");';
echo 'window.location.href="mw-health-details.php?id=' . $NIC . '";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$stmt->close();
$con->close();
?>