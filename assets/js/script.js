console.log("Script loaded!");

document.addEventListener("DOMContentLoaded", function() {
    // Get all elements with class "usr-image"
    const usrImages = document.querySelectorAll('.usr-image');

    // Loop through each element and add event listener
    usrImages.forEach(function(usrImage) {
        usrImage.addEventListener('click', function() {
            window.location.href="staff-profile.php";
        });
    });
});