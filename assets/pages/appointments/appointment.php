<?php
// Use secure session initialization for protected pages
require_once __DIR__ . '/../shared/session-init.php';

if (!isset($_SESSION["mamaEmail"])) {
    header("Location: ../auth/mama-login.php"); // Redirect to pregnant mother login page
    exit();
}

include '../shared/db-access.php';

// Generate CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$NIC = $_SESSION["NIC"];
// TODO: Remove debug statement below
echo $NIC;

// Get error or success message if exists
$error_message = $_SESSION['appointment_error'] ?? "";
$success_message = $_SESSION['appointment_success'] ?? "";

// Clear messages after displaying
if(isset($_SESSION['appointment_error'])){
    unset($_SESSION['appointment_error']);
}
if(isset($_SESSION['appointment_success'])){
    unset($_SESSION['appointment_success']);
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Book Appointment</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
        <script src="../../js/appointment.js"></script>
    </head>
<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
            <div class="d-flex flex-column">
                <h1 class="common-title">BabyBloom</h1>
                <h3 class="common-description">Maternity Clinic System</h3>
            </div>
        </header>
        <main>
            <div class="main-header d-flex">
                <h2 class="main-header-title">BOOK APPOINTMENT</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../../images/mama-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo htmlspecialchars($_SESSION['First_name'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($_SESSION['Last_name'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="useremail"><?php echo htmlspecialchars($_SESSION['mamaEmail'], ENT_QUOTES, 'UTF-8'); ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="../auth/mama-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <div class="main-content d-flex">
                <?php
                $currentMonth = date('m'); //Getting current month
                $currentYear = date('Y');//Getting current year

                $appSQL = "SELECT COUNT(*) AS count FROM appointments WHERE NIC = ? AND YEAR(app_date) = ? AND MONTH(app_date) = ?";

                $appStmt = $con->prepare($appSQL);
                $appStmt->bind_param("sii", $NIC, $currentYear, $currentMonth);
                $appStmt->execute();
                $appResult = $appStmt->get_result();
                $appRow = $appResult->fetch_assoc();

                $countAppointments = $appRow['count'];

                //This condition checks if there are previously created appointments avilable or not
                if ($countAppointments > 0) {

                    $currSql = "SELECT * FROM appointments WHERE NIC = ? AND YEAR(app_date) = ? AND MONTH(app_date) = ?";
                    $currStmt = $con->prepare($currSql);
                    if ($currStmt === false) {
                        $_SESSION['error_message'] = "System error. Please try again.";
                        header("Location: ../dashboard/mama-dashboard.php");
                        exit();
                    }
                    $currStmt->bind_param("sii", $NIC, $currentYear, $currentMonth);
                    $currStmt->execute();
                    $currResult = $currStmt->get_result();

                    if($currResult){
                        while($currRow = mysqli_fetch_assoc($currResult)){
                            $mamaCurrentAppointment = $currRow['app_date'];
                            $mamaCurrentAppTime = date('H:i', strtotime($currRow['app_time']));
                        }
                        $currStmt->close();
                    }
                    echo "Your next appointment is due on ".$mamaCurrentAppointment. " at ".$mamaCurrentAppTime;
                } else {
                    //This condition will hide the appointment form if user created another appointment
                    ?>
                    <div class="left-column">
                        <div class="days">
                            <div class="day">Monday</div>
                            <div class="day">Tuesday</div>
                            <div class="day">Wednesday</div>
                            <div class="day">Thursday</div>
                            <div class="day">Friday</div>
                            <div class="day">Saturday</div>
                            <div class="day">Sunday</div>
                        </div>
                        <div class="days-m">
                            <div class="day">M</div>
                            <div class="day">T</div>
                            <div class="day">W</div>
                            <div class="day">T</div>
                            <div class="day">F</div>
                            <div class="day">S</div>
                            <div class="day">S</div>
                        </div>
                        <div class="calendar">
                            <?php
                            $currentMonth = date('n');
                            $currentYear = date('Y');
                            $firstDayOfMonth = date('N', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
                            $daysInMonth = date('t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
                            $today = date('Y-m-d');

                            for ($i = 1; $i <= $daysInMonth; $i++) {
                                $date = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $i, $currentYear));
                                $dayOfWeek = date('N', mktime(0, 0, 0, $currentMonth, $i, $currentYear));
                                $disabledClass = ($date < $today) ? 'disabled' : ''; // Add disabled class for past dates
                                if ($i == 1) {
                                    for ($j = 1; $j < $firstDayOfMonth; $j++) {
                                        echo '<div class="date"></div>';
                                    }
                                }
                                echo '<div class="date ' . $disabledClass . '" data-date="' . $date . '">' . $i . '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <div class="right-column">
                        <form action="handlers/appointment-handler.php" method="post" id="appointment-form">
                            <div class="time-slots-container">
                                <?php
                                $timeSlots = array("09:00", "09:15", "09:30", "09:45", "10:00", "10:15", "10:30", "10:45", "11:00", "11:15", "11:30", "11:45", "13:00", "13:15", "13:30", "13:45", "14:00", "14:15", "14:30", "14:45", "15:00", "15:15", "15:30", "15:45");
                                foreach ($timeSlots as $time) {
                                    echo '<button class="time-slot" type="button" value="' . $time . '">' . $time . '</button>';
                                }
                                ?>
                            </div>
                            <input type="hidden" name="date" id="selected-date" required>
                            <input type="hidden" name="time" id="selected-time" required>
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                            <button type="submit" name="book" class="bb-a-btn app-book-btn">Book Appointment</button>
                        </form>
                    </div>
                <?php
                }
                ?>

                
                
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../dashboard/mama-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

</body>
</html>
