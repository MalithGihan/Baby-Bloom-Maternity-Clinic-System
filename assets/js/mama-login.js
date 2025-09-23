// Mama Login Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    console.log("Mama login page loaded");

    var loginBtnContainer = document.getElementById("login-btn-container");
    var loginContainer = document.getElementById("login-container");
    var resetContainer = document.getElementById("login-reset-container");

    var backBtn = document.getElementById("back-btn");
    var logSelectBtn = document.getElementById("login-btn");
    var resetSelectBtn = document.getElementById("login-reset-btn");

    if (backBtn) {
        backBtn.addEventListener("click", function() {
            if (loginBtnContainer) loginBtnContainer.style.display = "flex";
            if (loginContainer) loginContainer.style.display = "none";
            if (resetContainer) resetContainer.style.display = "none";
        });
    }

    if (logSelectBtn) {
        logSelectBtn.addEventListener("click", function() {
            if (backBtn) backBtn.style.display = "block";
            if (loginBtnContainer) loginBtnContainer.style.display = "none";
            if (loginContainer) loginContainer.style.display = "flex";
        });
    }

    if (resetSelectBtn) {
        resetSelectBtn.addEventListener("click", function() {
            if (loginContainer) loginContainer.style.display = "none";
            if (resetContainer) resetContainer.style.display = "flex";
        });
    }

    var loginImg = document.getElementById("bb-logo");
    if (loginImg) {
        loginImg.addEventListener("click", function() {
            window.location.href = "../../index.php";
        });
    }

    // Auto-show login form if there's an error message (check for error div)
    var errorDiv = document.querySelector('.error-message');
    if (errorDiv && errorDiv.textContent.trim()) {
        if (backBtn) backBtn.style.display = "block";
        if (loginBtnContainer) loginBtnContainer.style.display = "none";
        if (loginContainer) loginContainer.style.display = "flex";
    }
});