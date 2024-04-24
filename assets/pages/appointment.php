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
        if($date==""){
            echo '<script>';
            echo 'alert("Please select a date!");';
            echo 'window.location.href="appointment.php";';
            //Page redirection after successfull insertion
            echo '</script>';
        }
        else if($time==""){
            echo '<script>';
            echo 'alert("Please select a time!");';
            echo 'window.location.href="appointment.php";';
            //Page redirection after successfull insertion
            echo '</script>';
        }
        else{
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
            :root{
                --bg: #EFEBEA;
                --light-txt: #0D4B53;
                --light-txt2:#000000;
                --dark-txt: #86B6BB;
            }
            @font-face {
                font-family: 'Inter-Bold'; /* Heading font */
                src: url('../font/Inter-Bold.ttf') format('truetype'); 
                font-weight: 700;
            }
            @font-face {
                font-family: 'Inter-Light'; /* Text font */
                src: url('../font/Inter-Light.ttf') format('truetype'); 
                font-weight: 300;
            }

            .main-content{
                flex-direction: row !important;
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
                padding: 1rem;
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
                <div class="right-column">
                    <form method="post" id="appointment-form">
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
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../pages/mama-dashboard.php">
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
