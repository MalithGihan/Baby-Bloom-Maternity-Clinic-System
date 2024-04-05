<?php
include 'dbaccess.php';

  $NIC = $_POST['mama-nic']; 
  $date = $_POST['hr-date'];
  $appx_Next_date = $_POST['hr-appx-date'];
  $heartRate = $_POST['hr-heart-rate'];
  $bloodPressure = $_POST['hr-blood-pss']; 
  $cholesterolLevel = $_POST['hr-chol-lvl'];
  $heartRateConclution = $_POST['hr-heart-conclusion']; 
  $bloodPressureConclution = $_POST['hr-blood-pss-conclusion'];
  $bloodGroup = $_POST['blood-grp']; 
  $height = $_POST['hr-height']; 
  $weight = $_POST['hr-weight']; 
  $allergy = $_POST['hr-allergy'];
  $babyMovement = $_POST['hr-baby-movement'];
  $babyHeartbeat = $_POST['hr-baby-heart-rate']; 
  $scanConclution = $_POST['hr-scan-conclusion'];
  $vcabnormalitiescBy = $_POST['hr-abnormalities'];
  $sprcial_Instruction = $_POST['hr-spec-instructions'];
  

$sql = "INSERT INTO health_report (date, heartRate, bloodPressure, cholesterolLevel, weight, height, allergy, bloodGroup, heartRateConclusion, bloodPressureConclusion, babyMovement, babyHeartbeat, scanConclusion, abnormalities, special_Instruction, appx_Next_date, NIC) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
$stmt = $con->prepare($sql);
$stmt->bind_param("sisiddsssssisssss", $date, $heartRate, $bloodPressure, $cholesterolLevel, $weight, $height, $allergy, $bloodGroup, $heartRateConclution, $bloodPressureConclution, $babyMovement, $babyHeartbeat, $scanConclution, $vcabnormalitiescBy, $sprcial_Instruction, $appx_Next_date, $NIC);
$stmt->execute();

//echo $NIC;
//echo $vccName;
echo '<script>';
echo 'alert("Health details added successfully!");';
echo 'window.location.href="mw-health-details.php?id=' . $NIC . '";';
//Page redirection after successfull insertion
echo '</script>';
exit();

        
// Close the database connection
$stmt->close();
$con->close();
?>