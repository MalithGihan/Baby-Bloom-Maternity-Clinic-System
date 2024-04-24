<?php
session_start();

if (!isset($_SESSION["mamaEmail"])) {
    header("Location: mama-login.php"); // Redirect to pregnant mother login page
    exit();
}

include 'dbaccess.php';

$NIC = $_SESSION["NIC"];
echo $NIC;

// Function to check if an appointment exists for a given date and time slot
function isAppointmentExist($date, $time, $con) {
    $sql = "SELECT * FROM appointments WHERE app_date = '$date' AND app_time = '$time'";
    $result = mysqli_query($con, $sql);
    return mysqli_num_rows($result) > 0;
}

// Function to book an appointment
function bookAppointment($date, $time, $NIC, $con) {
    $sql = "INSERT INTO appointments (app_date, app_time, appointment_status, NIC) VALUES ('$date', '$time', 'Booked' , '$NIC')";
    mysqli_query($con, $sql);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['book'])) {
        $date = $_POST['date'];
        $time = $_POST['time'];
        if (!isAppointmentExist($date, $time, $con)) {
            bookAppointment($date, $time, $NIC, $con);
            echo '<script>';
            echo 'alert("Appointment made successfully!");';
            echo 'window.location.href="appointment.php";';
            //Page redirection after successfull insertion
            echo '</script>';
            exit();
        } else {
            // Optionally, display an error message that the slot is already booked
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Book Appointment</title>
        <link rel="icon" type="image/x-icon" href="../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../css/style.css">
        <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
        <script rel="script" type="text/js" href="../js/bootstrap.min.js"></script>
        <style>
            .main-content{
                flex-direction: row !important;
            }
            .l-col{
                flex:70%;
            }
            .r-col{
                flex:30%;
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
            }

            .day {
                flex: 1;
                text-align: center;
            }

            .date {
                text-align: center;
                border: 1px solid #ccc;
                padding: 5px;
                cursor: pointer;
            }

            .date:hover {
                background-color: #f0f0f0;
            }

            .date.selected {
                background-color: #007bff;
                color: #fff;
            }

            .time-slot {
                display: block;
                width: 100%;
                margin-bottom: 5px;
                color: black;
                border: 2px solid black;
                padding: 10px;
                text-align: center;
                cursor: pointer;
            }

            .selected{
                color:white;
                background-color: blue;
            }

            .time-slot:disabled {
                background-color: #ccc;
                cursor: not-allowed;
            }
        </style>
    </head>
<body>
    <div class="common-container d-flex">
        <header class="d-flex flex-row justify-content-between align-items-center">
            <img src="../images/logos/bb-top-logo.webp" alt="BabyBloom top logo" class="common-header-logo">
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
                        <img src="../images/mama-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo $_SESSION['First_name']; ?> <?php echo $_SESSION['Last_name']; ?></div>
                            <div class="useremail"><?php echo $_SESSION['mamaEmail']; ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="mama-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex">
                <div class="l-col">
                <div class="days">
                            <div class="day">Monday</div>
                            <div class="day">Tuesday</div>
                            <div class="day">Wednesday</div>
                            <div class="day">Thursday</div>
                            <div class="day">Friday</div>
                            <div class="day">Saturday</div>
                            <div class="day">Sunday</div>
                        </div>
                    <div class="calendar">
                        
                        <?php
                        $currentMonth = date('n');
                        $currentYear = date('Y');
                        $firstDayOfMonth = date('N', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
                        $daysInMonth = date('t', mktime(0, 0, 0, $currentMonth, 1, $currentYear));
                        for ($i = 1; $i <= $daysInMonth; $i++) {
                            $date = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $i, $currentYear));
                            $dayOfWeek = date('N', mktime(0, 0, 0, $currentMonth, $i, $currentYear));
                            if ($i == 1) {
                                for ($j = 1; $j < $firstDayOfMonth; $j++) {
                                    echo '<div class="date"></div>';
                                }
                            }
                            echo '<div class="date" data-date="' . $date . '">' . $i . '</div>';
                        }
                        ?>

                        
                    </div>
                </div>
                <div class="r-col">
                    <form method="post">
                        <?php
                        $timeSlots = array("9:00", "9:15", "9:30", "9:45", "10:00", "10:15", "10:30", "10:45", "11:00", "11:15", "11:30", "11:45", "13:00", "13:15", "13:30", "13:45", "14:00", "14:15", "14:30", "14:45", "15:00", "15:15", "15:30", "15:45");
                        foreach ($timeSlots as $time) {
                            $disabled = isAppointmentExist(date('Y-m-d'), $time, $con) ? 'disabled' : '';
                            echo '<button class="time-slot" value="' . $time . '" ' . $disabled . '>' . $time . '</button>';
                        }
                        ?>
                        <input type="hidden" name="date" id="selected-date">
                        <input type="hidden" name="time" id="selected-time">
                        <button type="submit" name="book">Book Appointment</button>
                    </form>
                </div>
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../pages/mama-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        // JavaScript to handle date and time slot selection
        document.querySelectorAll('.date').forEach(item => {
            item.addEventListener('click', event => {
                document.querySelectorAll('.date').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');
                document.getElementById('selected-date').value = item.getAttribute('data-date');
            })
        });

        document.querySelectorAll('.time-slot').forEach(item => {
            item.addEventListener('click', event => {
                document.querySelectorAll('.time-slot').forEach(el => el.classList.remove('selected'));
                item.classList.add('selected');
                document.getElementById('selected-time').value = item.value;
            })
        });

        // Prevent form submission on time slot click
        document.querySelectorAll('.time-slot').forEach(item => {
            item.addEventListener('click', event => {
                event.preventDefault();
            });
        });
    </script>
</body>
</html>
