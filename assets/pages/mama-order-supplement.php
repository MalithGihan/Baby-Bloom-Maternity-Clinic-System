<?php

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
                            <div class="username">Jane Doe</div>
                            <div class="useremail">janedoe@gmail.com</div>
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
                <div class="quota-container d-flex flex-column align-items-center">
                    <p>Monthly Supplement Ordering Quota : </p>
                    <div class="quota-sub-container d-flex">
                        <p class="quota-value">1</p>
                        <p class="quota-static-value"> / 1</p>
                    </div>
                </div>
                <form method="POST" class="quota-form d-flex flex-column align-items-center">
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
    </script>
</body>
</html>
