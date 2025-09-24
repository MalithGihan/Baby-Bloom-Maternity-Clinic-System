// Mama Registration Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    var marriedStatus = document.getElementById("mar-status");
    var bloodRelStatus = document.getElementById("blood-rel-input");
    var hubDataRow = document.getElementById("mama-hub-data-row");
    var hubTitle = document.getElementById("mama-hub-title");

    if (marriedStatus) {
        marriedStatus.addEventListener("change", function() {
            var marValue = marriedStatus.value;
            if (marValue == "Married") {
                if (hubDataRow) hubDataRow.style.display = "flex";
                if (hubTitle) hubTitle.style.display = "flex";
                if (bloodRelStatus) bloodRelStatus.style.display = "flex";
            } else if (marValue == "Unmarried") {
                if (hubDataRow) hubDataRow.style.display = "none";
                if (hubTitle) hubTitle.style.display = "none";
                if (bloodRelStatus) bloodRelStatus.style.display = "none";
            }
        });
    }
});