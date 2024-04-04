<?php

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Vaccination Details</title>
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
            .row-title{
                font-family: 'Inter-Bold';
                font-size:1rem;
                color:var(--dark-txt);
            }
            .report-mama-image{
                width:10vw;
                height:10vw;
            }
            .report-row{
                justify-content: space-between;
            }
            .data-title{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                color:var(--light-txt);
            }
            .data-value{
                font-family: 'Inter-Light';
                font-size:1rem;
                color:var(--light-txt);
            }
            .add-report-btn,#vaccine-search-btn{
                font-family: 'Inter-Bold';
                font-size:0.8rem;
                background-color:var(--light-txt);
                color:var(--bg);
                border:0px;
                border-radius:10rem;
                padding:0.5rem 2rem;
                transition:0.6s;
            }
            .add-report-btn:hover,#vaccine-search-btn:hover{
                background-color:var(--dark-txt);
                transition:0.6s;
            }
            .report-search-continer{
                gap:1rem;
            }
            #vaccine-name-search{
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
            #add-report-form{
                display:none;
            }
            #add-report-form,.add-vaccine-form-row{
                gap:1rem;
            }
            #add-report-form input{
                font-family: 'Inter-Light';
                font-size:1rem;
                color:var(--light-txt);
                outline:none;
                background-color:var(--bg);
                border:2px solid var(--light-txt);
                border-radius:10rem;
                width:50%;
                text-align: center;
                padding:0.5rem 0rem;
            }
            .add-vaccine-record-btn{
                font-family: 'Inter-Bold' !important;
                font-size:1rem !important;
                background-color:var(--light-txt) !important;
                color:var(--bg) !important;
                border:0px !important;
                border-radius:10rem !important;
                padding:0.5rem 0rem !important;
                width:20% !important;
                transition:0.6s !important;
            }
            .add-vaccine-record-btn:hover{
                background-color:var(--dark-txt) !important;
                transition:0.6s;
            }
            .frm-close-btn{
                font-family: 'Inter-Bold';
                font-size:1rem;
                background-color:var(--dark-txt);
                color:var(--bg);
                border:0px;
                border-radius:10rem;
                padding:0.5rem 0rem;
                width:20%;
                text-align: center;
                transition:0.6s;
            }
            .frm-close-btn:hover{
                cursor: pointer;
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
                <h2 class="main-header-title">MOTHER VACCINATION DETAILS</h2>
                <div class="main-usr-data d-flex flex-column">
                    <div class="usr-data-container d-flex">
                        <img src="../images/midwife-image.png" alt="User profile image" class="usr-image">
                        <div class="usr-data d-flex flex-column">
                            <div class="username">Jenny Doe</div>
                            <div class="useremail">jennydoe@gmail.com</div>
                        </div>
                    </div>
                    <div class="usr-logout-btn">
                        <a href="##">
                            <button class="usr-lo-btn">Log out</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="main-content d-flex flex-column">
                <div class="report-row d-flex">
                    <p class="row-title">MOTHER BASIC DATA</p>
                </div>
                <div class="report-row d-flex">
                    <img src="../images/midwife-dashboard/mama-img-in-reports.png" alt="Mother image" class="report-mama-image">
                    <div class="row-col d-flex flex-column">
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Full name</h3>
                            <p class="data-value">Jane Doe</p>
                        </div>
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Age</h3>
                            <p class="data-value">30</p>
                        </div>
                    </div>

                    <div class="row-col d-flex flex-column">
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">NIC number</h3>
                            <p class="data-value">991234567V</p>
                        </div>
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Address</h3>
                            <p class="data-value">257/3, GG WP road</p>
                        </div>
                    </div>

                    <div class="row-col d-flex flex-column">
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Birthdate</h3>
                            <p class="data-value">12/03/1999</p>
                        </div>
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Phone number</h3>
                            <p class="data-value">0702615423</p>
                        </div>
                    </div>
                </div>
                <div class="report-row d-flex">
                    <p class="row-title">HUSBAND DATA</p>
                </div>
                <div class="report-row d-flex">
                    <div class="row-col d-flex flex-column">
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Husband's name</h3>
                            <p class="data-value">John Doe</p>
                        </div>
                        <div class="data-row d-flex flex-column">
                            <h3 class="data-title">Husband's occupation</h3>
                            <p class="data-value">Engineer</p>
                        </div>
                    </div>
                </div>
                <div class="report-row d-flex">
                    <p class="row-title">MOTHER VACCINATION DATA</p>
                </div>
                <div class="report-row d-flex">
                    <button class="add-report-btn" id="add-report-btn">Add new</button>
                    <form class="report-search-continer d-flex">
                        <input type="text" id="vaccine-name-search" name="vaccine-name" placeholder="Search by vaccination name" required>
                        <input type="submit" value="Search" id="vaccine-search-btn">
                    </form>
                </div>
                <form action="add-vaccine.php" method="POST" class="report-row flex-column" id="add-report-form">
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <input type="text" id="vaccine-name" name="vaccine-name" placeholder="Enter vaccine name" required>
                        <input type="date" id="vaccine-date" name="vaccine-date" placeholder="Enter vaccinated date" required>
                    </div>
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <input type="text" id="vaccine-batch" name="vaccine-batch" placeholder="Enter vaccine batch number" required>
                        <input type="text" id="mama-nic" name="mama-nic" placeholder="Enter mother's NIC" required>
                    </div>
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <input type="text" id="vaccine-approved" name="vaccine-approved" placeholder="Who approved the vaccine?" required>
                        <input type="text" id="vaccine-doneby" name="vaccine-doneby" placeholder="Who done the vaccination?" required>
                    </div>
                    <div class="add-vaccine-form-row d-flex flex-row">
                        <div class="frm-close-btn" id="frm-close-btn">Cancel</div>
                        <input type="submit" value="Add" class="add-vaccine-record-btn"> 
                    </div>
                </form>
            </div>
            <div class="main-footer d-flex flex-row justify-content-between">
                <a href="../pages/mw-health-details.php">
                    <button class="main-footer-btn">Return</button>
                </a>
                <a href="../pages/midwife-dashboard.php">
                    <button class="main-footer-btn">Dashboard</button>
                </a>
            </div>
        </main>
    </div>

    <script>
        var addRecordBtn = document.getElementById("add-report-btn");
        var hideRecordBtn = document.getElementById("frm-close-btn");
        var recordForm = document.getElementById("add-report-form");

        addRecordBtn.addEventListener("click",function(){
            recordForm.style.display = "flex";
            console.log("GG");
        })
        hideRecordBtn.addEventListener("click",function(){
            recordForm.style.display = "none";
        })
    </script>
</body>
</html>
