<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Materity Clinic System</title>
        <link rel="icon" type="image/x-icon" href="assets/images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.min.css">
        <script rel="script" type="text/js" href="assets/js/bootstrap.min.js"></script>
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
                background-color: (--bg) !important;
                height:100vh;
                justify-content: center;
                align-items: center;
                text-align: center !important;
            }
            @font-face {
                font-family: 'Inter-Bold'; /* Heading font */
                src: url('assets/font/Inter-Bold.ttf') format('truetype'); 
                font-weight: 700;
            }
            @font-face {
                font-family: 'Inter-Light'; /* Text font */
                src: url('assets/font/Inter-Light.ttf') format('truetype'); 
                font-weight: 300;
            }
            .index-title{
                font-family: 'Inter-Bold';
                color:(--light-txt);
                font-size:2.5rem !important;
            }
            .index-subtitle{
                font-family: 'Inter-Light';
                color:var(--light-txt);
                font-size:1rem !important;
            }
            .index-txt-field img{
                width:40vw !important;
            }
            .index-btn-group{
                width:100vw;
                margin-top:2rem;
                flex-direction: column !important;
                gap: 2rem;
            }
            .index-btn-group a button{
                background-color: var(--dark-txt) !important;
                color: var(--light-txt2);
                font-family: 'Inter-Bold';
                font-size:1.5rem !important;
                outline:0px !important;
                border:2px solid var(--dark-txt);
                border-radius:10rem;
                padding:0.8rem 2rem;
                transition:0.6s;
            }
            .index-btn-group a button:hover{
                background-color: var(--light-txt) !important;
                color: var(--bg);
                transition:0.6s;
            }
            .index-btn-group a button{
                font-size:1rem !important;
            }

            /* Tablet media query */
            @media only screen and (min-width:768px){
                .index-title{
                    font-size:3rem !important;
                }
                .index-subtitle{
                    font-size:1.5rem !important;
                }
                .index-txt-field img{
                    width:10rem !important;
                }

                .index-btn-group{
                    justify-content: center;
                    margin-top:2rem;
                    flex-direction: row !important;
                    gap: 2rem;
                }
                .index-btn-group a button{
                    font-size:1.5rem !important;
                }
            }

            /* Laptop media query */
            @media only screen and (min-width:1280px){
                .index-title{
                    font-size:4rem !important;
                }
                .index-subtitle{
                    font-size:2rem !important;
                }
                .index-txt-field img{
                    width:10rem !important;
                }
                
                .index-btn-group a button{
                    font-size:1rem !important;
                }
            }
        </style>
    </head>
    <body class="d-flex flex-column">
        <div class="index-txt-field">
            <img src="assets/images/logos/babybloom-main-logo.webp" alt="BabyBloom main logo">
            <h1 class="index-title">Baby Bloom</h1>
            <h3 class="index-subtitle"> Maternity Clinic System</h3>
        </div>
        <div class="index-btn-group d-flex">
            <a href="assets/pages/auth/mama-login.php">
                <button class="mama-loign-btn">Mama Login</button>
            </a>
            <a href="assets/pages/auth/staff-login.php">
                <button class="staff-loign-btn">Staff Login</button>
            </a>
        </div>
    </body>
</html>