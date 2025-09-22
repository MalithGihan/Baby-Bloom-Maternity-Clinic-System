<?php
session_start();

// Redirect if not logged in as mama
if (!isset($_SESSION["mamaEmail"])) {
    header("Location: ../../auth/mama-login.php");
    exit();
}

// Only process POST requests
if($_SERVER["REQUEST_METHOD"] !== "POST"){
    header("Location: ../mama-order-supplement.php");
    exit();
}

include '../../shared/db-access.php';

// Initialize variables
$error_message = "";
$success_message = "";

try {
    $NIC = $_SESSION["NIC"];
    $todayDate = date("Y-m-d");

    // Get and validate input
    $deliveryMethod = trim($_POST["delivery-method"] ?? "");

    if (empty($deliveryMethod)) {
        $error_message = "Please select a delivery method.";
        $_SESSION['supplement_error'] = $error_message;
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    // Validate delivery method
    $validMethods = ['Pickup', 'Delivery'];
    if (!in_array($deliveryMethod, $validMethods)) {
        $error_message = "Invalid delivery method selected.";
        $_SESSION['supplement_error'] = $error_message;
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    // Check quota availability
    $sql = "SELECT orderedTimes FROM supplement_quota WHERE NIC = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        error_log('Database prepare failed: ' . $con->error);
        $error_message = "System error. Please try again later.";
        $_SESSION['supplement_error'] = $error_message;
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    $stmt->bind_param("s", $NIC);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $error_message = "No quota record found for your account.";
        $_SESSION['supplement_error'] = $error_message;
        $stmt->close();
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    $row = $result->fetch_assoc();
    $momQuota = $row['orderedTimes'];
    $stmt->close();

    // Check if quota is available
    if ($momQuota <= 0) {
        $error_message = "You have no remaining supplement quota for this month.";
        $_SESSION['supplement_error'] = $error_message;
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    // Start transaction
    $con->autocommit(FALSE);

    // Insert supplement request
    $reqStatus = "Pending";
    $sql = "INSERT INTO supplement_request (ordered_date, delivery, status, NIC) VALUES (?, ?, ?, ?)";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        $con->rollback();
        error_log('Database prepare failed for supplement_request: ' . $con->error);
        $error_message = "Order failed. Please try again later.";
        $_SESSION['supplement_error'] = $error_message;
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    $stmt->bind_param("ssss", $todayDate, $deliveryMethod, $reqStatus, $NIC);

    if (!$stmt->execute()) {
        $con->rollback();
        error_log('Failed to insert supplement_request: ' . $stmt->error);
        $error_message = "Order failed. Please try again later.";
        $_SESSION['supplement_error'] = $error_message;
        $stmt->close();
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    $stmt->close();

    // Update quota
    $sql1 = "UPDATE supplement_quota SET orderedTimes = orderedTimes - 1 WHERE NIC = ?";
    $stmt1 = $con->prepare($sql1);

    if ($stmt1 === false) {
        $con->rollback();
        error_log('Database prepare failed for quota update: ' . $con->error);
        $error_message = "Order failed. Please try again later.";
        $_SESSION['supplement_error'] = $error_message;
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    $stmt1->bind_param("s", $NIC);

    if (!$stmt1->execute()) {
        $con->rollback();
        error_log('Failed to update supplement_quota: ' . $stmt1->error);
        $error_message = "Order failed. Please try again later.";
        $_SESSION['supplement_error'] = $error_message;
        $stmt1->close();
        header("Location: ../mama-order-supplement.php");
        exit();
    }

    $stmt1->close();

    // Commit transaction
    $con->commit();

    // Order successful
    $_SESSION['supplement_success'] = "Your supplement order has been placed successfully!";
    header("Location: ../mama-order-supplement.php");
    exit();

} catch (Exception $e) {
    // Rollback transaction on error
    if (isset($con)) {
        $con->rollback();
    }
    error_log('Supplement order error: ' . $e->getMessage());
    $error_message = "Order failed. Please try again later.";
    $_SESSION['supplement_error'] = $error_message;
    header("Location: ../mama-order-supplement.php");
    exit();
} finally {
    // Clean up database resources
    if (isset($con)) {
        $con->close();
    }
}
?>