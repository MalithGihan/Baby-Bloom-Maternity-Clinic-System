<?php
session_start();

if (!isset($_SESSION["mamaEmail"])) {
    header("Location: mama-login.php"); // Redirect to pregnant mother login page
    exit();
}

include 'dbaccess.php';

$NIC = $_SESSION["NIC"];

//echo $NIC;
//To get current date
$todayDate = date("Y-m-d");
$currentMonth = date("m");
echo $todayDate;

$sql = "SELECT * FROM supplement_quota WHERE NIC='$NIC'";

$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($result)){
    $momQuota = $row['orderedTimes'];
    $momNIC = $row['NIC'];
}
echo "<br>";
echo $momQuota;
//This code is responsible for resetting the quota in each month
if($momQuota==0){
    $resetSQL = "SELECT * FROM supplement_request WHERE NIC='$NIC' ORDER BY ordered_date DESC LIMIT 1";

    $resetResult = mysqli_query($con,$resetSQL);
    while($resetRow = mysqli_fetch_assoc($resetResult)){
        $ordDate = $resetRow['ordered_date'];
        $storedMonth = date('m', strtotime($ordDate));
    }

    echo $ordDate;
    
    if($currentMonth!=$storedMonth){
        $resetQSQL = "UPDATE supplement_quota SET orderedTimes=1 WHERE NIC='$NIC'";
        mysqli_query($con, $resetQSQL);
    }

    echo "<br>";
    echo "Your quota has resetted successfully!";
}

$sql = "SELECT * FROM supplement_quota WHERE NIC='$NIC'";

$result = mysqli_query($con,$sql);
while($row = mysqli_fetch_assoc($result)){
    $momQuota = $row['orderedTimes'];
    $momNIC = $row['NIC'];
}

//echo $momFname;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $deliveryMethod = $_POST["delivery-method"];
    $reqStatus = "Pending";

    $sql = "INSERT INTO supplement_request (ordered_date,delivery,status,NIC) VALUES (?,?,?,?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ssss",$todayDate,$deliveryMethod,$reqStatus,$NIC);
    $stmt->execute();

    $sql1 = "UPDATE supplement_quota SET orderedTimes = '0' WHERE NIC = '$NIC'";
    mysqli_query($con,$sql1);

    echo '<script>';
    echo 'alert("Your supplement order placed successfully!");';
    echo 'window.location.href="mama-order-supplement.php";';
    //Page redirection after successfull insertion
    echo '</script>';
    exit();

            
    // Close the database connection
    $stmt->close();
    $con->close();
	
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Dashboard</title>
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

            body{
                margin:0 !important;
                padding:0 !important;
                background-color: var(--bg) !important;
            }
            .quota-form{
                gap:1rem;
            }
            .quota-container{
                font-family: 'Inter-Bold';
            }
            .quota-sub-container{
                font-size:2rem;
            }
            .deliver-method{
                font-family: 'Inter-Light';
                flex-direction:row;
                gap:1rem;
            }
            .quota-form-btn{
                background-color:var(--light-txt);
                color:var(--bg);
                font-family: 'Inter-Bold';
                border:0px;
                border-radius:10rem;
                padding:0.8rem 2rem;
                transition: 0.6s;
            }
            .quota-form-btn:hover{
                background-color:var(--dark-txt);
                color:var(--bg);
                transition: 0.6s;
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
                <h2 class="main-header-title">ORDER SUPPLEMENT</h2>
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
            <div class="main-content d-flex flex-column">
                <div class="quota-container d-flex flex-column align-items-center">
                    <p>Monthly Supplement Ordering Quota : </p>
                    <div class="quota-sub-container d-flex">
                        <p class="quota-value" id="quota-value"><?php echo $momQuota; ?></p>
                        <p class="quota-static-value"> / 1</p>
                    </div>
                    <h3 class="usr-msg" id="usr-msg"></h3>
                </div>
                <form action="" method="POST" class="quota-form flex-column align-items-center" id="quota-form">
                    <div class="deliver-method d-flex">
                        <input type="radio" id="home-deliver" name="delivery-method" value="Home Delivery">
                        <label for="home-deliver">Home Delivery</label><br>
                        <input type="radio" id="pickup" name="delivery-method" value="Pickup">
                        <label for="pickup">Pickup</label>
                    </div>
                    <input type="submit" value="Order" class="quota-form-btn">
                </form>
            </div>
            <div class="main-footer d-flex flex-row justify-content-start">
                <a href="../pages/mama-dashboard.php">
                    <button class="main-footer-btn">Return</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        var quotaValue = document.getElementById("quota-value");
        var quotaText = document.getElementById("usr-msg");
        var orderForm = document.getElementById("quota-form");

        if(quotaValue.innerHTML>0){
            quotaText.innerHTML = "You can order supplements!";
            orderForm.style.display = "flex";
        }
        else{
            quotaText.innerHTML = "You cannot order supplements for a month!";
            orderForm.style.display = "none";
        }
    </script>
</body>
</html>
