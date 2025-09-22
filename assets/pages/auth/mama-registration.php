<?php
session_start();

// Get error or success message if exists
$error_message = $_SESSION['registration_error'] ?? "";
$success_message = $_SESSION['registration_success'] ?? "";

// Clear messages after displaying
if(isset($_SESSION['registration_error'])){
    unset($_SESSION['registration_error']);
}
if(isset($_SESSION['registration_success'])){
    unset($_SESSION['registration_success']);
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Baby Bloom - Mama Registration</title>
        <link rel="icon" type="image/x-icon" href="../../images/logos/bb-favicon.png">
        <link rel="stylesheet" type="text/css" href="../../css/style.css">
        <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" href="../../css/common-variables.css">
        <link rel="stylesheet" type="text/css" href="../../css/registration-forms.css">
        <script rel="script" type="text/js" src="../../js/bootstrap.min.js"></script>
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
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($success_message)): ?>
                <div class="success-message">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            <form action="handlers/mama-registration-handler.php" method="POST" class="d-flex flex-column">
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
                        <input type="text" id="mom-birthplace" name="mom-birthplace" placeholder="Mother's birthplace">
                        <div class="frm-col frm-dt-r-col d-flex flex-column">
                            <label for="birthday" class="lrmp-label">Last Regualar Menstrual Period</label>
                            <input type="date" id="lrmp" name="mom-lrmp" placeholder="Last Regualar Menstrual Period" required>
                        </div>
                    </div>
                    <div class="frm-row d-flex">
                        <input type="text" id="address" name="mom-address" placeholder="Home address" required>
                        <input type="text" id="nic" name="mom-nic" placeholder="N I C" required>
                        <input type="tel" id="phone" name="mom-phone" pattern="[0-9]{10}" placeholder="Enter phone number" required>
                    </div>
                    <hr>
                    <p class="frm-section-title">Mother Health Background Details</p>
                    <div class="frm-row d-flex">
                        <textarea id="" name="mom-health-conditions" placeholder="Mother known health conditions" maxlength="1000"></textarea>
                        <textarea id="" name="mom-allergies" placeholder="Mother known allergies" maxlength="1000"></textarea>
                    </div>
                    <!-- Rubella vaccination status option -->
                    <div class="frm-row d-flex rb-stat-container"> 
                        <p>Vaccinated against Rubella?</p>
                        <div class="d-flex rb-vcc-input">
                            <input type="radio" id="rbl-state-yes" name="rbl-status" value="Yes" required>
                            <label for="rbl-status">Yes</label><br>
                            <input type="radio" id="rbl-state-no" name="rbl-status" value="No" required>
                            <label for="rbl-status">No</label><br>
                        </div>
                    </div>
                    <hr>
                    <div class="frm-row d-flex">
                        <select name="marital-status" id="mar-status" required>
                            <option value="" disabled selected>Marital status</option>
                            <option value="Married">Married</option>
                            <option value="Unmarried">Unmarried</option>
                        </select>
                    </div>
                    <div class="frm-row rb-stat-container" id="blood-rel-input"> 
                        <p>Blood relative marriage?</p>
                        <div class="d-flex rb-vcc-input">
                            <input type="radio" id="blood-rel-yes" name="blood-relativity" value="Yes" required>
                            <label for="blood-relativity">Yes</label><br>
                            <input type="radio" id="blood-rel-no" name="blood-relativity" value="No" required>
                            <label for="blood-relativity">No</label><br>
                        </div>
                    </div>
                    <p class="frm-section-title" id="mama-hub-title">Husband Details</p>
                    <div class="mama-hub-data-row flex-column frm-row" id="mama-hub-data-row">
                        <div class="d-flex frm-row">
                            <input type="text" id="hub-name" name="mom-hub-name" placeholder="Husband's name">
                            <input type="text" id="hub-job" name="mom-hub-job" placeholder="Husband's occupation">
                            <input type="tel" id="phone" name="mom-hub-phone" pattern="[0-9]{10}" placeholder="Enter husband's phone number" required>
                        </div>
                        <div class="d-flex frm-row">
                            <div class="frm-col frm-dt-l-col d-flex flex-column">
                                <label for="birthday">Husband's Birthdate</label>
                                <input type="date" id="hub-birthday" name="mom-hub-bday" placeholder="Husband's Birthday" required>
                            </div>
                            <input type="text" id="hub-birthplace" name="mom-hub-birthplace" placeholder="Husband's birthplace">
                        </div>
                        <div class="d-flex frm-row">
                            <textarea id="" name="mama-hub-health-conditions" placeholder="Husband's known health conditions" maxlength="1000"></textarea>
                            <textarea id="" name="mama-hub-allergies" placeholder="Husband's known allergies" maxlength="1000"></textarea>
                        </div>
                    </div>
                    <hr>
                    <p class="frm-section-title">Mother Account Details</p>
                    <div class="frm-row d-flex">
                        <input type="email" id="email" name="mom-email" placeholder="Enter your email" required>
                        <input type="password" id="pwd" name="mom-pwd" placeholder="Enter password" required>
                        <input type="password" id="repwd" name="mom-repwd" placeholder="Re enter password" required>
                    </div>
                    <div class="frm-row d-flex flex-row frm-footer-btn-row">
                        <a href="../auth/mama-login.php">
                            <div class="reg-return-btn main-footer-btn">Return</div>
                        </a>
                        <input type="submit" value="Register" class="reg-btn">
                    </div>
                </div>
                
            </form>
            
        </main>
    </div>

    <script>
        var marriedStatus = document.getElementById("mar-status");
        var bloodRelStatus = document.getElementById("blood-rel-input");
        var hubDataRow = document.getElementById("mama-hub-data-row");
        var hubTitle = document.getElementById("mama-hub-title");

        marriedStatus.addEventListener("change",function(){//The if statement will trigger when the select element value changed
            var marValue = marriedStatus.value;
            if(marValue == "Married"){
                hubDataRow.style.display = "flex";
                hubTitle.style.display = "flex";
                bloodRelStatus.style.display = "flex";
            }
            else if(marValue == "Unmarried"){
                hubDataRow.style.display = "none";
                hubTitle.style.display = "none";
                bloodRelStatus.style.display = "none";
            }
        })
    </script>
</body>
</html>
