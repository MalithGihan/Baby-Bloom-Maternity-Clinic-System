// Health Details JavaScript - Chart and form functionality
document.addEventListener('DOMContentLoaded', function() {
    // BMI Status styling
    var BMIStatus = document.getElementById("mom-bmi-status");
    if (BMIStatus) {
        switch(BMIStatus.innerHTML) {
            case "Underweight":
                BMIStatus.style.backgroundColor = "Orange";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            case "healthy":
                BMIStatus.style.backgroundColor = "Green";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            case "overweight":
                BMIStatus.style.backgroundColor = "Red";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            case "obese":
                BMIStatus.style.backgroundColor = "DarkRed";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
        }
    }

    // Export functionality
    var exportBtn = document.getElementById("health-export-btn");
    if (exportBtn) {
        exportBtn.addEventListener("click", function() {
            if (typeof html2canvas !== 'undefined' && typeof jsPDF !== 'undefined') {
                html2canvas(document.getElementById("capture-section")).then((canvas) => {
                    let base64image = canvas.toDataURL('image/png');
                    let pdf = new jsPDF('p', 'px', [1250, 2203]);
                    pdf.addImage(base64image, 'png', 32, 32, 1156, 2203);
                    pdf.save('mother-health-report.pdf');
                });
            }
        });
    }

    // Chart functionality on window load
    window.addEventListener('load', function() {
        if (typeof Highcharts !== 'undefined' && window.weightData) {
            Highcharts.chart('weight-chart-container', {
                chart: {
                    type: 'line',
                    backgroundColor: '#EFEBEA'
                },
                title: {
                    text: ''
                },
                xAxis: {
                    categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8', 'Week 9', 'Week 10']
                },
                yAxis: {
                    title: {
                        text: 'Weight (kg)'
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                series: [{
                    name: 'Weight',
                    data: window.weightData
                }]
            });
        }
    });

    // Chart on DOM ready
    if (typeof Highcharts !== 'undefined' && window.weightData) {
        Highcharts.chart('weight-chart-container', {
            chart: {
                type: 'line',
                backgroundColor: '#EFEBEA'
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: ['Week 1', 'Week 2', 'Week 3', 'Week 4', 'Week 5', 'Week 6', 'Week 7', 'Week 8', 'Week 9', 'Week 10']
            },
            yAxis: {
                title: {
                    text: 'Weight (kg)'
                }
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: false
                }
            },
            series: [{
                name: 'Weight',
                data: window.weightData
            }]
        });
    }

    // Form toggle functionality
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