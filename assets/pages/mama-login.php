<?php
session_start();

include 'dbaccess.php';

if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Get the entered email and password
    $mamaEmail = $_POST["mama-email"];
    $mamaPass = $_POST["mama-password"];
	
    $sql = "SELECT * FROM pregnant_mother WHERE email = ?";
    $stmt = $con->prepare($sql);

    if ($stmt === false) {
        die('prepare() failed: ' . htmlspecialchars($con->error));
    }

    $stmt->bind_param("s",$mamaEmail);
    $stmt->execute();
    $stmt->store_result(); 

    // Check if a user with the provided email exists
    if ($stmt->num_rows === 1) {
        // Bind the result variables
        $stmt->bind_result($NIC, $fname, $mname, $sname, $address, $lrmp, $dob, $maritalstat, $husname, $husjob, $phone, $usremail, $usrpass);
        $stmt->fetch();
    
        // Verify the password
        if ($mamaPass==$usrpass) {
            // Password is correct, create a session
            
            $_SESSION["loggedin"] = true;
            $_SESSION["NIC"] = $NIC;
            $_SESSION["mamaEmail"] = $usremail;
            $_SESSION['First_name'] = $fname;
            $_SESSION['Last_name'] = $sname;
            
            // Redirect to a protected page or dashboard
            header("location: mama-dashboard.php");

        }
        else {
            echo '<script>';
            echo 'alert ("Incorrect password. Please try again.");';
            echo '</script>';
        }
    }
    else {
        echo '<script>';
        echo 'alert ("No user with that email address found.");';
        echo '</script>';
    }
        
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
        <title>Baby Bloom - Mama Login</title>
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
            body{
                margin:0 !important;
                padding:0 !important;
                background-color: var(--bg) !important;
                height:100vh;
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

            .mama-login-content{
                flex-direction: column;
            }
            .mama-login-logo,.mama-login-container{
                width:100%;
            }
            @media only screen and (max-width:767px){
                .mama-login-logo{
                    height:40vh;
                }
                .mama-login-container{
                    height:60vh;
                }
            }
            .bb-logo{
                width:30%;
            }
            .index-title{
                font-family: 'Inter-Bold';
                font-size:3rem;
            }
            .index-subtitle{
                font-family: 'Inter-Light';
                font-size:1.5rem;
            }
            .back-btn{
                width:3rem;
                align-self:flex-start;
            }
            .back-btn:hover{
                cursor:pointer;
            }
            .login-btn-container,.login-container,.login-reset-container{
                width:90%;
                height:60vh;
                border:2px solid var(--light-txt);
                border-radius:2rem;
                gap:1rem;
                padding:2rem 0rem;
            }
            .login-btn-container{
                display:flex;
            }
            .login-container,.login-reset-container{
                display:none;
            }
            .mama-register-btn{
                width:100%;
                text-decoration: none;
            }
            .register-btn,.login-submit-btn{
                background-color:var(--light-txt);
                color:var(--bg);
                padding:0.8rem 2rem;
                font-family: 'Inter-Bold';
                font-size:1rem;
                border-radius:10rem;
                border:2px solid var(--light-txt);
                width:90%;
                transition:0.6s;
            }
            .login-submit-btn,.login-reset-btn{
                width:100% !important;
            }
            .login-btn,.login-reset-btn{
                background-color:var(--bg);
                color:var(--light-txt);
                padding:0.8rem 2rem;
                font-family: 'Inter-Bold';
                font-size:1rem;
                border-radius:10rem;
                border:2px solid var(--light-txt);
                width:90% !important;
                transition:0.6s;
            }
            .login-reset-btn-r{
                background-color:var(--light-txt);
                color:var(--bg);
                padding:0.8rem 2rem;
                font-family: 'Inter-Bold';
                font-size:1rem;
                border-radius:10rem;
                border:2px solid var(--light-txt);
                width:60%;
                transition:0.6s;
            }
            .register-btn:hover,.login-submit-btn:hover,.login-reset-btn-r:hover{
                background-color:var(--dark-txt);
                color:var(--light-txt2);
                border:2px solid var(--dark-txt);
                transition:0.6s;
            }
            .login-btn:hover,.login-reset-btn:hover{
                background-color:var(--dark-txt);
                color:var(--light-txt2);
                border:2px solid var(--dark-txt);
                transition:0.6s;
            }
            .l-title{
                font-family: 'Inter-Bold';
                font-size:1rem;
            }
            form{
                width:100%;
                gap:1rem;
            }
            .login-input{
                background-color:var(--bg);
                outline:none;
                height:3rem;
                width:90%;
                border:2px solid var(--light-txt);
                border-radius:10rem;
                text-align:center;
            }
            .login-form-btn-group{
                gap:1rem;
                width:90%;
            }


            /* Tablet media query */
            @media only screen and (min-width:768px){
                .mama-login-content{
                    flex-direction: row;
                    height:100vh;
                }
                .mama-login-logo,.mama-login-container{
                    flex:50%;
                    width:100%;
                }
                .bb-logo{
                    width:30%;
                }
                .index-title{
                    font-family: 'Inter-Bold';
                    font-size:3rem;
                }
                .index-subtitle{
                    font-family: 'Inter-Light';
                    font-size:1.5rem;
                }
                .back-btn{
                    width:3rem;
                    align-self:flex-start;
                    display:none;
                }
                .back-btn:hover{
                    cursor:pointer;
                }
                .login-btn-container,.login-container,.login-reset-container{
                    width:80%;
                    height:60vh;
                    border:2px solid var(--light-txt);
                    border-radius:2rem;
                    gap:1rem;
                    padding:2rem 0rem;
                }
                .mama-register-btn{
                    width:100% !important;
                    text-decoration: none;
                }
                .register-btn,.login-submit-btn{
                    background-color:var(--light-txt);
                    color:var(--bg);
                    padding:0.8rem 2rem;
                    font-family: 'Inter-Bold';
                    font-size:1rem;
                    border-radius:10rem;
                    border:2px solid var(--light-txt);
                    width:90%;
                    transition:0.6s;
                }
                .login-submit-btn{
                    width:100% !important;
                }
                .login-reset-btn{
                    width:90% !important;
                }
                .login-btn,.login-reset-btn{
                    background-color:var(--bg);
                    color:var(--light-txt);
                    padding:0.8rem 2rem;
                    font-family: 'Inter-Bold';
                    font-size:1rem;
                    border-radius:10rem;
                    border:2px solid var(--light-txt);
                    width:60%;
                    transition:0.6s;
                }
                .login-reset-btn-r{
                    background-color:var(--light-txt);
                    color:var(--bg);
                    padding:0.8rem 2rem;
                    font-family: 'Inter-Bold';
                    font-size:1rem;
                    border-radius:10rem;
                    border:2px solid var(--light-txt);
                    width:60%;
                    transition:0.6s;
                }
                .register-btn:hover,.login-submit-btn:hover,.login-reset-btn-r:hover{
                    background-color:var(--dark-txt);
                    color:var(--light-txt2);
                    border:2px solid var(--dark-txt);
                    transition:0.6s;
                }
                .login-btn:hover,.login-reset-btn:hover{
                    background-color:var(--dark-txt);
                    color:var(--light-txt2);
                    border:2px solid var(--dark-txt);
                    transition:0.6s;
                }
                .l-title{
                    font-family: 'Inter-Bold';
                    font-size:1rem;
                }
                form{
                    width:100%;
                    gap:1rem;
                }
                .login-input{
                    background-color:var(--bg);
                    outline:none;
                    height:3rem;
                    width:90%;
                    border:2px solid var(--light-txt);
                    border-radius:10rem;
                    text-align:center;
                }
                .login-form-btn-group{
                    gap:1rem;
                    width:90%;
                }
            }

            /* Laptop media query */
            @media only screen and (min-width:1280px){}

        </style>
    </head>
<body>
    <div class="mama-login-content d-flex">
        <div class="mama-login-logo d-flex flex-column align-items-center justify-content-center">
            <img class="bb-logo" src="../images/logos/babybloom-main-logo.webp" alt="BabyBloom main logo">
            <h1 class="index-title">Baby Bloom</h1>
            <h3 class="index-subtitle"> Maternity Clinic System</h3>
        </div>
        <div class="mama-login-container d-flex flex-column align-items-center justify-content-center">
            <img src="../images/login-back-btn.png" class="back-btn" id="back-btn">
            <div class="login-btn-container flex-column align-items-center justify-content-center" id="login-btn-container">
                <a href="../pages/mama-registration.php" class="mama-register-btn d-flex flex-row justify-content-center">
                    <button class="register-btn">CLINIC REGISTRATION</button>
                </a>
                <button class="login-btn" id="login-btn">MAMA LOGIN</button>
            </div>
            <div class="login-container flex-column align-items-center justify-content-center" id="login-container">
                <h3 class="login-title l-title">MAMA LOGIN</h3>
                <form action="" method="post" class="d-flex flex-column align-items-center justify-content-center">
                    <input type="email" class="login-input" id="login-email" name="mama-email" placeholder="Enter your email address" required>
                    <input type="password" class="login-input" id="login-pass" name="mama-password" placeholder="Enter your password" required>
                    <div class="login-form-btn-group d-flex flex-row">
                        <input type="submit" value="LOGIN" class="login-submit-btn">
                    </div>
                </form>
                <button id="login-reset-btn" class="login-reset-btn">RESET PASSWORD</button>
            </div>
            <div class="login-reset-container flex-column align-items-center justify-content-center" id="login-reset-container">
                <h3 class="pass-reset-title l-title">RESET PASSWORD</h3>
                <form method="post" class="d-flex flex-column align-items-center justify-content-center">
                    <input type="email" class="login-input" id="login-reset-email" name="mama-reset-email" placeholder="Enter your email address">
                    <input type="submit" value="RESET PASSWORD" class="login-reset-btn-r">
                </form>
            </div>
        </div>
    </div>

    <script>
        console.log("GG WP");
        var loginBtnContainer = document.getElementById("login-btn-container");
        var loginContainer = document.getElementById("login-container");
        var resetContainer = document.getElementById("login-reset-container");

        var backBtn = document.getElementById("back-btn");
        var logSelectBtn = document.getElementById("login-btn");
        var resetSelectBtn = document.getElementById("login-reset-btn");


        backBtn.addEventListener("click",function(){
            loginBtnContainer.style.display = "flex";
            loginContainer.style.display = "none";
            resetContainer.style.display = "none";
        })

        logSelectBtn.addEventListener("click",function(){
            backBtn.style.display = "block";
            loginBtnContainer.style.display = "none";
            loginContainer.style.display = "flex";
        })

        resetSelectBtn.addEventListener("click",function(){
            loginContainer.style.display = "none";
            resetContainer.style.display = "flex";
        })

        
    </script>
</body>
</html>
