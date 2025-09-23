<?php
session_start();

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

include '../shared/db-access.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - TODAY APPOINTMENTS</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
        <script src="../../js/adapter.min.js"></script>
        <script src="../../js/vue.min.js"></script>
        <script src="../../js/instascan.min.js"></script>
        <script src="../../js/script.js"></script>
        <script src="../../js/qr-scanner.js"></script>
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
                <h2 class="main-header-title">TODAY APPOINTMENTS</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../../images/midwife-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo $_SESSION['staffFName']; ?> <?php echo $_SESSION['staffSName']; ?></div>
                            <div class="useremail"><?php echo $_SESSION['staffEmail']; ?></div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="../auth/staff-logout.php">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex flex-column">
                <div class="report-row d-flex align-items-center">
                    <button class="scan-qr-btn" id="scan-qr-btn">Scan QR</button>
                    <form class="mom-search-continer d-flex" method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="text" id="mom-nic-search" name="mama-search" placeholder="Enter Mother NIC">
                        <input type="submit" name="submit" value="Search" id="mom-search-btn">
                        <input type="submit" name="clear" value="Clear Search" class="bb-n-btn" id="clear-results-btn">
                    </form>
                </div>
                <div class="report-row d-flex">
                    <a href="../staff/mw-mother-list.php">
                        <button class="bb-n-btn">Registered Mothers List</button>
                    </a>
                </div>
                <div class="report-row flex-column align-items-center" id="preview-window">
                    <video id="preview"></video>
                    <div class="bb-a-btn" id="scan-close">Close</div>
                </div>
                <table class="table">
                    <thead>
                        <tr>
                            <th class="dd">NIC</th>
                            <th class="dd">Appointment status</th>
                            <th class="dd">Appointment date</th>
                            <th class="dd">Appointment time</th>
                        </tr>
                    </thead>
                    <?php
                    //echo date_default_timezone_get();
                    $today = date('Y-m-d');

                    //Below content is loaded if the search form is submitted.
                    if(isset($_POST['submit'])){    
                        if (
                            !isset($_POST['csrf_token']) ||
                            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
                        ) {
                            echo '<script>alert("Invalid request. Please try again."); window.location.href="appointments-list.php";</script>';
                            exit();
                        }

                        $search = $_POST['mama-search'];

                        //This conditions will check the entered value is NULL or not
                        if($search==""){
                            echo '<script>';
                            echo 'alert("Enter mother NIC number!!!");';
                            echo 'window.location.href="appointments-list.php";';
                            echo '</script>';
                        }
                        else{
                            $mamaSearchSQL = "SELECT * FROM appointments WHERE NIC = ? AND app_date = ?";
                            $mamaSearchStmt = $con->prepare($mamaSearchSQL);
                            $mamaSearchStmt->bind_param("ss", $search, $today);
                            $mamaSearchStmt->execute();
                            $mamaSResult = $mamaSearchStmt->get_result();
                            if($mamaSResult){
                                $num = mysqli_num_rows($mamaSResult);
                                echo "Found $num appointment in today";
                                if($num > 0){
                                    while($searchRow = mysqli_fetch_assoc($mamaSResult)){
                                        if($searchRow['appointment_status'] == 'Booked'){
                                            $mamaNIC = $searchRow['NIC'];
                                            echo '
                                            <tbody>
                                            <tr class="vaccine-results">
                                                <td><a class="mom-list-btn d-flex flex-row justify-content-center" href="mw-health-details.php?id='.$searchRow["NIC"].'"> '.$searchRow["NIC"].' </a></td>
                                                <td><div class="app-status"><b>'.$searchRow['appointment_status'].'</b></div></td>
                                                <td>'.$searchRow['app_date'].'</td>
                                                <td>'.$searchRow['app_time'].'</td>
                                                <td class="table-btn-container d-flex flex-row justify-content-center">
                                            ';
                                            ?>
                                            <form method="POST" action="appointment-confirm.php" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo (int)$searchRow['appointment_id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <button type="submit" class="mom-list-btn">Confirm</button>
                                            </form>

                                            <form method="POST" action="appointment-delete.php" style="display:inline;" onsubmit="return confirm('Delete this appointment?');">
                                            <input type="hidden" name="id" value="<?php echo (int)$searchRow['appointment_id']; ?>">
                                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                            <button type="submit" class="mom-list-btn-remove">Remove</button>
                                            </form>
                                            <?php
                                            echo '
                                                </td>
                                            </tr>
                                            </tbody>';
                                        }
                                        else{
                                            echo '
                                            <tbody>
                                                <tr class="vaccine-results">
                                                    <td><a class="mom-list-btn d-flex flex-row justify-content-center" href="mw-health-details.php?id='.$row["NIC"].'"> '.$row["NIC"].' </a></td>
                                                    <td><div class="app-status"><b>'.$row['appointment_status'].'</b></div></td>
                                                    <td>'.$row['app_date'].'</td>
                                                    <td>'.$row['app_time'].'</td>
                                                </tr>
                                            </tbody>';
                                        }
                                    }
                                }
                            }
                        }

                    }
                    //Below content is loaded if the search form is not submitted. (Default view)
                    else if (!isset($_POST['submit'])){
                        $sql = "SELECT * FROM appointments WHERE app_date = ?";
                        $stmt = $con->prepare($sql);
                        $stmt->bind_param("s", $today);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        if($result){
                            $num = mysqli_num_rows($result);
                            if($num > 0){
                                echo "There are $num appointments in today";
                                while($row = mysqli_fetch_assoc($result)){
                                    if($row['appointment_status'] == 'Booked'){
                                        $mamaNIC = $row['NIC'];
                                        echo '
                                        <tbody>
                                        <tr class="vaccine-results">
                                            <td><a class="mom-list-btn d-flex flex-row justify-content-center" href="mw-health-details.php?id='.$row["NIC"].'"> '.$row["NIC"].' </a></td>
                                            <td><div class="app-status"><b>'.$row['appointment_status'].'</b></div></td>
                                            <td>'.$row['app_date'].'</td>
                                            <td>'.$row['app_time'].'</td>
                                            <td class="table-btn-container d-flex flex-row justify-content-center">
                                        ';
                                        ?>
                                        <form method="POST" action="appointment-confirm.php" style="display:inline;">
                                        <input type="hidden" name="id" value="<?php echo (int)$row['appointment_id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <button type="submit" class="mom-list-btn">Confirm</button>
                                        </form>

                                        <form method="POST" action="appointment-delete.php" style="display:inline;" onsubmit="return confirm('Delete this appointment?');">
                                        <input type="hidden" name="id" value="<?php echo (int)$row['appointment_id']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                                        <button type="submit" class="mom-list-btn-remove">Remove</button>
                                        </form>
                                        <?php
                                        echo '
                                            </td>
                                        </tr>
                                        </tbody>';
                                    }
                                    else{
                                        echo '
                                        <tbody>
                                            <tr class="vaccine-results">
                                                <td><a class="mom-list-btn d-flex flex-row justify-content-center" href="mw-health-details.php?id='.$row["NIC"].'"> '.$row["NIC"].' </a></td>
                                                <td><div class="app-status"><b>'.$row['appointment_status'].'</b></div></td>
                                                <td>'.$row['app_date'].'</td>
                                                <td>'.$row['app_time'].'</td>
                                            </tr>
                                        </tbody>';
                                    }
                                }
                            }
                            else{
                                echo "There are no appointments in today";
                            }
                        }
                    }
                    //This is code that reset the search form
                    else if(isset($_POST['clear'])){
                        if (
                            !isset($_POST['csrf_token']) ||
                            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
                        ) {
                            echo '<script>alert("Invalid request. Please try again."); window.location.href="appointments-list.php";</script>';
                            exit();
                        }
                        // Redirect to the same page without any POST data
                        header("Location: ".$_SERVER['PHP_SELF']);
                        exit;
                    } 

                    ?>
                </table>
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../dashboard/staff-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

</body>
</html>
