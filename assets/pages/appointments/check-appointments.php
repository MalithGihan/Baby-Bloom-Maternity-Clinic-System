<?php
include '../shared/db-access.php';

if (isset($_GET['date'])) {
    $selectedDate = $_GET['date'];
    
    // Query to check for booked appointments on the selected date
    $sql = "SELECT app_time FROM appointments WHERE app_date = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $selectedDate);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $bookedTimeSlots = array();
    while ($row = $result->fetch_assoc()) {
        //$bookedTimeSlots[] = $row['app_time'];
        $time = date('H:i', strtotime($row['app_time']));
        $bookedTimeSlots[] = $time;
    }
    
    // Send the response as JSON
    header('Content-Type: application/json');
    echo json_encode($bookedTimeSlots);
    exit(); // Stop further execution
}

// If $_GET['date'] is not set or invalid, return an empty JSON array
echo json_encode([]);
exit();
?>