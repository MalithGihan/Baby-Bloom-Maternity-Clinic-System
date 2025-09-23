<?php
session_start();

if (!isset($_SESSION["mamaEmail"])) {
    header("Location: ../auth/mama-login.php"); // Redirect to pregnant mother login page
    exit();
}

include '../shared/db-access.php';

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
        <style>
            :root{
                --bg: #EFEBEA;
                --light-txt: #0D4B53;
                --light-txt2:#000000;
                --dark-txt: #86B6BB;
            }
            @font-face {
                font-family: 'Inter-Bold'; /* Heading font */
                src: url('../../font/Inter-Bold.ttf') format('truetype'); 
                font-weight: 700;
            }
            @font-face {
                font-family: 'Inter-Light'; /* Text font */
                src: url('../../font/Inter-Light.ttf') format('truetype'); 
                font-weight: 300;
            }

            .main-content{
                flex-direction: column !important;
            }
            .left-column{
                flex:70%;
            }
            .right-column{
                flex:30%;
                display: none;
            }
            .calendar {
                display: grid;
                grid-template-columns: repeat(7, 1fr);
                gap: 5px;
            }

            .days {
                display: flex;
                justify-content: space-between;
                font-weight: bold;
                font-family: 'Inter-Bold';
                color:var(--light-txt);
            }

            .day {
                flex: 1;
                text-align: center;
            }

            .date {
                text-align: center;
                color:var(--light-txt);
                background-color: var(--bg);
                border: 2px solid var(--dark-txt);
                border-radius:1rem;
                padding: 0.3rem;
                cursor: pointer;
            }

            .date:hover {
                background-color: var(--dark-txt);
            }

            .date.selected {
                background-color: var(--dark-txt);
                color: var(--bg);
            }

            .time-slot {
                display: block;
                width: 100%;
                margin-bottom: 5px;
                color:var(--light-txt);
                background-color: var(--bg);
                border: 2px solid var(--dark-txt);
                border-radius:1rem;
                padding: 10px;
                text-align: center;
                cursor: pointer;
            }
            .time-slot:hover{
                background-color:var(--dark-txt);
            }

            .selected{
                color:var(--light-txt);
                background-color: var(--dark-txt);
            }

            .booked {
                background-color: #ccc !important;
                cursor: not-allowed !important;
            }
            .app-book-btn{
                margin-top:2rem !important;
                width:100% !important;
                padding:0.8rem 1.5rem !important;
            }
            .disabled {
                background-color: #ccc !important;
                color: var(--dark-txt); /* Change the color of disabled dates */
                pointer-events: none; /* Disable pointer events */
                cursor: not-allowed !important;
            }
            .days{
                display:none;
            }
            .days-m{
                display:flex;
                justify-content: space-between;
                font-weight: bold;
                font-family: 'Inter-Bold';
                color:var(--light-txt);
            }

            @media only screen and (min-width:768px){
                .days{
                    display:flex;
                }
                .days-m{
                    display:none;
                }
                .date{  
                    padding: 1rem;
                }

            }

            @media only screen and (min-width:1280px){
                .main-content{
                    flex-direction: row !important;
                }
            }
        </style>
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
                <div class="error-message" style="color: #d32f2f; font-family: 'Inter-Bold'; font-size: 1rem; margin: 1rem auto; text-align: center; padding: 1rem; border: 2px solid #d32f2f; border-radius: 1rem; width: 90%; background-color: #ffeaea;">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success-message" style="color: #2e7d32; font-family: 'Inter-Bold'; font-size: 1rem; margin: 1rem auto; text-align: center; padding: 1rem; border: 2px solid #2e7d32; border-radius: 1rem; width: 90%; background-color: #e8f5e8;">
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
                        echo '<script>alert("System error. Please try again."); window.location.href="../dashboard/mama-dashboard.php";</script>';
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
                            <div class="time-slots-container" style="height: 40vh; overflow-y: auto;">
                                <?php
                                $timeSlots = array("09:00", "09:15", "09:30", "09:45", "10:00", "10:15", "10:30", "10:45", "11:00", "11:15", "11:30", "11:45", "13:00", "13:15", "13:30", "13:45", "14:00", "14:15", "14:30", "14:45", "15:00", "15:15", "15:30", "15:45");
                                foreach ($timeSlots as $time) {
                                    echo '<button class="time-slot" type="button" value="' . $time . '">' . $time . '</button>';
                                }
                                ?>
                            </div>
                            <input type="hidden" name="date" id="selected-date" required>
                            <input type="hidden" name="time" id="selected-time" required>
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

    <script>
        document.querySelectorAll('.date').forEach(item => {
            item.addEventListener('click', event => {
                document.querySelectorAll('.date').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');
                document.getElementById('selected-date').value = item.getAttribute('data-date');
                
                // Show the time slots after selecting a date
                document.querySelector('.right-column').style.display = 'block';
                
                // Disable booked time slots
                var selectedDate = item.getAttribute('data-date');
                console.log(selectedDate);
                disableBookedTimeSlots(selectedDate);
            })
        });

        document.querySelectorAll('.time-slot').forEach(item => {
            item.addEventListener('click', event => {
                document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');
                document.getElementById('selected-time').value = item.value;
            })
        });

        function disableBookedTimeSlots(selectedDate) {
            // AJAX request to check for existing appointments
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4) {
                    if (this.status == 200) {
                        try {
                            var response = JSON.parse(this.responseText);
                            console.log("Response:", response);
                            document.querySelectorAll('.time-slot').forEach(slot => {
                                var time = slot.value;
                                if (response.includes(time)) {
                                    slot.classList.add('booked');
                                } else {
                                    slot.classList.remove('booked');
                                }
                            });
                        } catch (error) {
                            console.error("Error parsing JSON:", error);
                            console.log("Raw response:", this.responseText);
                        }
                    } else {
                        console.error("Error:", this.status, this.statusText);
                    }
                }
            };
            xhttp.open("GET", "check-appointments.php?date=" + selectedDate, true);
            xhttp.send();
        }



        // Prevent form submission on time slot click
        document.querySelectorAll('.time-slot').forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault();
            });
        });
    </script>
</body>
</html>
