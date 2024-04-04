<?php

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Registration</title>
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
            .frm-row{
                justify-content: space-between;
            }
            .frm-col{
                width:100%;
            }
            input,select{
                outline:0px;
                border:2px solid var(--light-txt);
                border-radius:10rem;
                background-color:var(--bg);
                text-align:center;
                width:30%;
                margin:1rem 0rem;
                padding:0.5rem 0rem;
            }
            .frm-dt-row{
                justify-content: space-between !important;
            }
            .frm-dt-l-col{
                align-items: flex-start;
            }
            .frm-dt-r-col{
                align-items:flex-end;
            }
            #birthday,#lrmp{
                margin-top:0px !important;
                width:70%
            }
            .reg-btn{
                background-color:var(--light-txt);
                color:var(--bg);
                font-family: 'Inter-Bold';
                align-self:flex-end;
                transition:0.6s;
            }
            .reg-btn:hover{
                background-color:var(--dark-txt);
                border:2px solid var(--dark-txt);
                color:var(--bg);
                transition:0.6s;
            }
            .reg-return-btn{
                margin-top:-2rem;
            }
            .frm-section-title{
                font-family: 'Inter-Bold';
                font-size:1.5rem;
                color:var(--light-txt);
            }
            label{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
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
            <form method="POST" class="d-flex flex-column">
                <div class="frm-section">
                    <p class="frm-section-title">Mother Basic Details</p>
                    <div class="frm-row d-flex">
                        <input type="text" id="fname" name="mom-first-name" placeholder="First name" required>
                        <input type="text" id="mname" name="mom-mid-name" placeholder="Middle name">
                        <input type="text" id="lname" name="mom-last-name" placeholder="Last name" required>
                    </div>
                    <div class="frm-row frm-dt-row d-flex">
                        <div class="frm-col frm-dt-l-col d-flex flex-column">
                            <label for="birthday">Birthdate</label>
                            <input type="date" id="birthday" name="mom-bday" placeholder="Birthday" required>
                        </div>
                        <div class="frm-col frm-dt-r-col d-flex flex-column">
                            <label for="birthday" class="lrmp-label">Last Regualar Menstrual Period</label>
                            <input type="date" id="lrmp" name="mom-lrmp" placeholder="Last Regualar Menstrual Period" required>
                        </div>
                    </div>
                    <div class="frm-row d-flex">
                        <input type="text" id="address" name="mom-address" placeholder="Home address" required>
                        <input type="tel" id="phone" name="mom-phone" pattern="[0-9]{3}-[0-9]{2}-[0-9]{3}" placeholder="Enter phone number" required>
                    </div>
                    <div class="frm-row d-flex">
                        <select name="marital-status" id="mar-status" required>
                            <option value="Married">Married</option>
                            <option value="Unmarried">Unmarried</option>
                        </select>
                        <input type="text" id="hub-name" name="mom-hub-name" placeholder="Husband's name">
                        <input type="text" id="hub-job" name="mom-hub-job" placeholder="Husband's occupation">
                    </div>
                    <p class="frm-section-title">Mother Account Details</p>
                    <div class="frm-row d-flex">
                        <input type="email" id="email" name="mom-email" placeholder="Enter your email" required>
                        <input type="password" id="pwd" name="mom-pwd" placeholder="Enter password" required>
                        <input type="password" id="repwd" name="mom-repwd" placeholder="Re enter password" required>
                    </div>
                </div>
                <input type="submit" value="Register" class="reg-btn">
            </form>
            <a href="../pages/mama-login.php">
                <button class="reg-return-btn main-footer-btn">Return</button>
            </a>
        </main>
    </div>

    <script>
    </script>
</body>
</html>
