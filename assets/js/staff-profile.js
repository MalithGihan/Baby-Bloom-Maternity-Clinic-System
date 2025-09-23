// Staff Profile JavaScript - Profile-specific form toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    var addRecordBtn = document.getElementById("add-report-btn");
    var hideRecordBtn = document.getElementById("frm-close-btn");
    var recordForm = document.getElementById("add-report-form");
    var stDataContainer = document.getElementById("staff-detail-container");

    if (addRecordBtn && recordForm && stDataContainer) {
        addRecordBtn.addEventListener("click", function() {
            stDataContainer.style.display = "none";
            addRecordBtn.style.display = "none";
            recordForm.style.display = "flex";
        });
    }

    if (hideRecordBtn && recordForm && stDataContainer && addRecordBtn) {
        hideRecordBtn.addEventListener("click", function() {
            stDataContainer.style.display = "flex";
            addRecordBtn.style.display = "block";
            recordForm.style.display = "none";
        });
    }
});