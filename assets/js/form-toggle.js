// Form Toggle JavaScript - Generic form show/hide functionality
document.addEventListener('DOMContentLoaded', function() {
    var addRecordBtn = document.getElementById("add-report-btn");
    var hideRecordBtn = document.getElementById("frm-close-btn");
    var recordForm = document.getElementById("add-report-form");

    if (addRecordBtn && recordForm) {
        addRecordBtn.addEventListener("click", function() {
            recordForm.style.display = "flex";
        });
    }

    if (hideRecordBtn && recordForm) {
        hideRecordBtn.addEventListener("click", function() {
            recordForm.style.display = "none";
        });
    }
});