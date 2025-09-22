<?php
session_start();

// Redirect if not logged in as mama
if (!isset($_SESSION["mamaEmail"])) {
    header("Location: ../../auth/mama-login.php");
    exit();
}

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../appointment.php");
    exit();
}

// Verify CSRF token presence and validity
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
) {
    $_SESSION['appointment_error'] = "Invalid request. Please try again.";
    header("Location: ../appointment.php");
    exit();
}

include '../../shared/db-access.php';

// Initialize variables
$error_message = "";
$success_message = "";

try {
    $NIC = $_SESSION["NIC"];

    // Check if it's a booking request
    if (!isset($_POST['book'])) {
        header("Location: ../appointment.php");
        exit();
    }

    // Get and validate input
    $date = trim($_POST['date'] ?? "");
    $time = trim($_POST['time'] ?? "");

    if (empty($date)) {
        $error_message = "Please select a date!";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    if (empty($time)) {
        $error_message = "Please select a time!";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    // Validate date format and ensure it's not in the past
    $selectedDate = DateTime::createFromFormat('Y-m-d', $date);
    $today = new DateTime();
    $today->setTime(0, 0, 0); // Set to start of day for comparison

    if (!$selectedDate || $selectedDate->format('Y-m-d') !== $date) {
        $error_message = "Please select a valid date.";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    if ($selectedDate < $today) {
        $error_message = "Cannot book appointments for past dates.";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    // Validate time slot
    $validTimes = ['09:00', '10:00', '11:00', '14:00', '15:00', '16:00']; // Example time slots
    if (!in_array($time, $validTimes)) {
        $error_message = "Please select a valid time slot.";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    // Check if appointment slot is already taken
    $checkSql = "SELECT * FROM appointments WHERE app_date = ? AND app_time = ?";
    $checkStmt = $con->prepare($checkSql);

    if ($checkStmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    $checkStmt->bind_param("ss", $date, $time);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows > 0) {
        $error_message = "This appointment slot is already booked. Please select another time.";
        $_SESSION['appointment_error'] = $error_message;
        $checkStmt->close();
        header("Location: ../appointment.php");
        exit();
    }

    $checkStmt->close();

    // Check if user already has an appointment on the same date
    $userCheckSql = "SELECT * FROM appointments WHERE app_date = ? AND NIC = ?";
    $userCheckStmt = $con->prepare($userCheckSql);

    if ($userCheckStmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    $userCheckStmt->bind_param("ss", $date, $NIC);
    $userCheckStmt->execute();
    $userResult = $userCheckStmt->get_result();

    if ($userResult->num_rows > 0) {
        $error_message = "You already have an appointment on this date.";
        $_SESSION['appointment_error'] = $error_message;
        $userCheckStmt->close();
        header("Location: ../appointment.php");
        exit();
    }

    $userCheckStmt->close();

    // Book the appointment
    $bookSql = "INSERT INTO appointments (app_date, app_time, appointment_status, NIC) VALUES (?, ?, 'Booked', ?)";
    $bookStmt = $con->prepare($bookSql);

    if ($bookStmt === false) {
        error_log('Database prepare failed for booking: ' . $con->error);
        $error_message = "Booking failed. Please try again later.";
        $_SESSION['appointment_error'] = $error_message;
        header("Location: ../appointment.php");
        exit();
    }

    $bookStmt->bind_param("sss", $date, $time, $NIC);

    if (!$bookStmt->execute()) {
        error_log('Failed to book appointment: ' . $bookStmt->error);
        $error_message = "Booking failed. Please try again later.";
        $_SESSION['appointment_error'] = $error_message;
        $bookStmt->close();
        header("Location: ../appointment.php");
        exit();
    }

    $bookStmt->close();

    // Regenerate token so old forms can't be replayed
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    // Booking successful
    $_SESSION['appointment_success'] = "Appointment booked successfully for " . date('F j, Y', strtotime($date)) . " at " . $time . "!";
    header("Location: ../appointment.php");
    exit();

} catch (Exception $e) {
    error_log('Appointment booking error: ' . $e->getMessage());
    $error_message = "Booking failed. Please try again later.";
    $_SESSION['appointment_error'] = $error_message;
    header("Location: ../appointment.php");
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>