<?php
// Use secure session initialization for protected pages
require_once __DIR__ . '/../shared/session-init.php';

if (!isset($_SESSION["staffEmail"])) {
    header("Location: ../auth/staff-login.php"); // Redirect to pregnant mother login page
    exit();
}

include '../shared/db-access.php';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - REGISTERED MOTHERS</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../../css/common-variables.css">
        <link rel="stylesheet" type="text/css" href="../../css/staff-pages.css">
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
                <h2 class="main-header-title">REGISTERED MOTHERS</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../../images/midwife-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username"><?php echo htmlspecialchars($_SESSION['staffFName'], ENT_QUOTES, 'UTF-8'); ?> <?php echo htmlspecialchars($_SESSION['staffSName'], ENT_QUOTES, 'UTF-8'); ?></div>
                            <div class="useremail"><?php echo htmlspecialchars($_SESSION['staffEmail'], ENT_QUOTES, 'UTF-8'); ?></div>
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
                        <input type="text" id="mom-nic-search" name="mama-search" placeholder="Enter Mother NIC">
                        <input type="submit" name="submit" value="Search" id="mom-search-btn">
                        <input type="submit" name="clear" value="Clear Search" class="bb-n-btn" id="clear-results-btn">
                    </form>
                </div>
                <div class="report-row d-flex">
                    <a href="appointments-list.php">
                        <button class="bb-n-btn">View Appointments</button>
                    </a>
                </div>
                <div class="report-row flex-column align-items-center" id="preview-window">
                    <video id="preview"></video>
                    <div class="bb-a-btn" id="scan-close">Close</div>
                </div>
                <table class="table mw-mother-list">
                    <thead>
                        <tr>
                            <th>First name</th>
                            <th>Last name</th>
                            <th>NIC</th>
                        </tr>
                    </thead>
                    <?php
                    if(isset($_POST['submit'])){
                        $search = $_POST['mama-search'];

                        //This conditions will check the entered value is NULL or not
                        if($search==""){
                            echo '<script>';
                            echo 'alert("Enter mother NIC number!!!");';
                            echo 'window.location.href="mw-mother-list.php";';
                            echo '</script>';
                        }
                        else{
                            $mamaSearchSQL = "SELECT * FROM pregnant_mother WHERE NIC = ?";
                            $stmt = $con->prepare($mamaSearchSQL);
                            if ($stmt === false) {
                                echo '<script>alert("System error. Please try again."); window.location.href="mw-mother-list.php";</script>';
                                exit();
                            }
                            $stmt->bind_param("s", $search);
                            $stmt->execute();
                            $mamaSResult = $stmt->get_result();
                            if($mamaSResult){
                                $num = mysqli_num_rows($mamaSResult);
                                echo "$num results found.";
                                if($num > 0){
                                    while($searchRow = mysqli_fetch_assoc($mamaSResult)){
                                        echo '
                                        <tbody>
                                            <tr class="vaccine-results">
                                                <td>'.htmlspecialchars($searchRow['firstName'], ENT_QUOTES, 'UTF-8').'</td>
                                                <td>'.htmlspecialchars($searchRow['surname'], ENT_QUOTES, 'UTF-8').' </td>
                                                <td>'.htmlspecialchars($searchRow['NIC'], ENT_QUOTES, 'UTF-8').'</td>
                                                <td class="table-btn-container d-flex flex-row justify-content-center">
                                                    <a class="mom-list-btn" href="../health/mw-health-details.php?id='.urlencode($searchRow["NIC"]).'">Health report</a>
                                                    <a class="mom-list-btn" href="../health/mw-vaccination-details.php?id='.urlencode($searchRow["NIC"]).'">Vaccination report</a>
                                                </td>
                                            </tr>
                                        </tbody>';
                                    }
                                }
                                else{
                                    echo '<h3>Data not found</h3>';
                                }
                            }
                            $stmt->close();
                        }

                    }
                    else if (!isset($_POST['submit'])){
                        $sql = "SELECT * FROM pregnant_mother";
                        $result = mysqli_query($con,$sql);
                        if($result){
                            $num = mysqli_num_rows($result);
                            echo "$num results found.";
                            if($num > 0){
                                while($row = mysqli_fetch_assoc($result)){
                                    echo '
                                    <tbody>
                                        <tr class="vaccine-results">
                                            <td>'.htmlspecialchars($row['firstName'], ENT_QUOTES, 'UTF-8').'</td>
                                            <td>'.htmlspecialchars($row['surname'], ENT_QUOTES, 'UTF-8').' </td>
                                            <td>'.htmlspecialchars($row['NIC'], ENT_QUOTES, 'UTF-8').'</td>
                                            <td class="table-btn-container d-flex flex-row justify-content-center">
                                                <a class="mom-list-btn" href="../health/mw-health-details.php?id='.urlencode($row["NIC"]).'">Health report</a>
                                                <a class="mom-list-btn" href="../health/mw-vaccination-details.php?id='.urlencode($row["NIC"]).'">Vaccination report</a>
                                            </td>
                                        </tr>
                                    </tbody>';
                                }
                            }
                            else{
                                echo '<h3>Data not found</h3>';
                            }
                        }
                    }
                    else if(isset($_POST['clear'])){
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
