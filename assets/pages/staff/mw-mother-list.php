<?php
session_start();

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
        <style>
            .report-row{
                justify-content: space-between;
            }

            /*Normal btn*/
            .bb-n-btn{
                background-color: var(--dark-txt);
                color: var(--bg);
                font-family: 'Inter-Bold';
                font-size: 1rem;
                border:0px !important;
                outline:none !important;
                border-radius: 10rem;
                padding: 0.5rem 1.5rem;
                transition: 0.6s;
            }
            .bb-n-btn:hover{
                background-color: var(--light-txt) !important;
                cursor: pointer;
                transition: 0.6s;
            }

            .scan-qr-btn,#mom-search-btn{
                font-family: 'Inter-Bold';
                font-size:1rem;
                background-color:var(--light-txt);
                color:var(--bg);
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                transition:0.6s;
            }
            .scan-qr-btn:hover,#mom-search-btn:hover{
                background-color:var(--dark-txt);
                transition:0.6s;
            }
            .mom-search-continer{
                gap:1rem;
            }
            #mom-nic-search{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
                outline:none;
                background-color:var(--bg);
                border:2px solid var(--light-txt);
                border-radius:10rem;
                width:30vw;
                text-align: center;
            }
            .mom-list-btn{
                background-color:var(--dark-txt);
                color:var(--bg);
                font-family: 'Inter-Bold';
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                text-decoration: none;
                transition:0.6s;
            }
            .mom-list-btn:hover{
                background-color:var(--light-txt);
                color:var(--bg);
                transition:0.6s;
            }
            th,td{
                background-color:var(--bg) !important;
            }
            td{
                color:var(--light-txt);
                font-family: 'Inter-Light';
            }
            .table-btn-container{
                gap:1rem;
            }
            #preview-window{
                border:2px solid var(--light-txt);
                border-radius:1rem;
                padding:1rem;
            }
            #preview{
                width:80vw;
                height:40vh;
            }

            @media only screen and (min-width:768px){
                .scan-qr-btn,#mom-search-btn{
                    font-family: 'Inter-Bold';
                    font-size:1rem;
                    background-color:var(--light-txt);
                    color:var(--bg);
                    border:0px;
                    border-radius:10rem;
                    padding:0.5rem 2rem;
                    transition:0.6s;
                }
                .scan-qr-btn:hover,#mom-search-btn:hover{
                    background-color:var(--dark-txt);
                    transition:0.6s;
                }
                .mom-search-continer{
                    gap:1rem;
                }
                #mom-nic-search{
                    font-family: 'Inter-Bold';
                    font-size:0.8rem;
                    color:var(--light-txt);
                    outline:none;
                    background-color:var(--bg);
                    border:2px solid var(--light-txt);
                    border-radius:10rem;
                    width:30vw;
                    text-align: center;
                }
                .mom-list-btn{
                    background-color:var(--dark-txt);
                    color:var(--bg);
                    font-family: 'Inter-Bold';
                    border:0px;
                    border-radius:10rem;
                    padding:0.5rem 2rem;
                    text-decoration: none;
                    transition:0.6s;
                }
                .mom-list-btn:hover{
                    background-color:var(--light-txt);
                    color:var(--bg);
                    transition:0.6s;
                }
                th,td{
                    background-color:var(--bg) !important;
                }
                td{
                    color:var(--light-txt);
                    font-family: 'Inter-Light';
                }
                .table-btn-container{
                    gap:1rem;
                }
                #preview-window{
                    border:2px solid var(--light-txt);
                    border-radius:1rem;
                    padding:1rem;
                }
                #preview{
                    width:80vw;
                    height:40vh;
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
                <h2 class="main-header-title">REGISTERED MOTHERS</h2>
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
                <div class="report-row flex-column align-items-center" id="preview-window" style="display:none;">
                    <video id="preview"></video>
                    <div class="bb-a-btn" id="scan-close" style="margin-top:1rem;">Close</div>
                </div>
                <table class="table">
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
                            $mamaSearchSQL = "SELECT * FROM pregnant_mother WHERE NIC = '$search'";
                            $mamaSResult = mysqli_query($con,$mamaSearchSQL);
                            if($mamaSResult){
                                $num = mysqli_num_rows($mamaSResult);
                                echo "$num results found.";
                                if($num > 0){
                                    while($searchRow = mysqli_fetch_assoc($mamaSResult)){
                                        echo '
                                        <tbody>
                                            <tr class="vaccine-results">
                                                <td>'.$searchRow['firstName'].'</td>
                                                <td>'.$searchRow['surname'].' </td>
                                                <td>'.$searchRow['NIC'].'</td>
                                                <td class="table-btn-container d-flex flex-row justify-content-center">
                                                    <a class="mom-list-btn" href="../health/mw-health-details.php?id='.$searchRow["NIC"].'">Health report</a>
                                                    <a class="mom-list-btn" href="../health/mw-vaccination-details.php?id='.$searchRow["NIC"].'">Vaccination report</a>
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
                                            <td>'.$row['firstName'].'</td>
                                            <td>'.$row['surname'].' </td>
                                            <td>'.$row['NIC'].'</td>
                                            <td class="table-btn-container d-flex flex-row justify-content-center">
                                                <a class="mom-list-btn" href="../health/mw-health-details.php?id='.$row["NIC"].'">Health report</a>
                                                <a class="mom-list-btn" href="../health/mw-vaccination-details.php?id='.$row["NIC"].'">Vaccination report</a>
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

    <script>
        var qrBtn = document.getElementById("scan-qr-btn");
        var qrCBtn = document.getElementById("scan-close");
        var camWindow = document.getElementById("preview-window");

        qrBtn.addEventListener("click",function(){
            camWindow.style.display = "flex";

            let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
            scanner.addListener('scan', function (content) {
                console.log(content);
            });
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                scanner.start(cameras[0]);
                } else {
                console.error('No cameras found.');
                }
            }).catch(function (e) {
                console.error(e);
            });

            scanner.addListener('scan',function(c){
                document.getElementById("mom-nic-search").value = c;

                camWindow.style.display = "none";

                scanner.stop();
            })

            //Close the window and release the camera resource
            qrCBtn.addEventListener("click",function(){
                camWindow.style.display = "none";

                scanner.stop();
            })
        })

        
        
    </script>
</body>
</html>
