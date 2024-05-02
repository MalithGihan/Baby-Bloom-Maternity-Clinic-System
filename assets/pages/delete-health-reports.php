<?php
session_start();

include 'dbaccess.php';

if ($_SESSION["staffPosition"] != "Sister" ) {
    header("Location: staff-dashboard.php"); // Redirect to staff dashboard if the logged in user isn't a Sister
    exit();
}

$HR_ID = $_GET['id'];
$NIC = $_GET['NIC'];

$sql = "DELETE FROM health_report WHERE HR_ID=?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $HR_ID);
$stmt->execute();

//echo $NIC;
//echo $vccName;
echo '<script>';
echo 'alert("Health report removed successfully!");';
echo 'window.location.href="mw-health-details.php?id='.$NIC.'#mama-health-records";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$stmt->close();
$con->close();

?>