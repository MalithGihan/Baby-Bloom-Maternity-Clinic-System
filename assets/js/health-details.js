// Health Details JavaScript - Chart and form functionality
document.addEventListener('DOMContentLoaded', function() {
    // BMI Status styling
    var BMIStatus = document.getElementById("mom-bmi-status");
    if (BMIStatus) {
        console.log(BMIStatus.innerHTML);
        switch(BMIStatus.innerHTML) {
            case "Underweight":
                BMIStatus.style.backgroundColor = "Orange";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            case "Healthy":
            case "healthy":
                BMIStatus.style.backgroundColor = "Green";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            case "Overweight":
            case "overweight":
                BMIStatus.style.backgroundColor = "Orange";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            case "Obese":
            case "obese":
                BMIStatus.style.backgroundColor = "Red";
                BMIStatus.style.padding = "0.5rem 1rem";
                BMIStatus.style.color = "#EFEBEA";
                break;
            default:
                BMIStatus.style.backgroundColor = "#EFEBEA";
                BMIStatus.style.padding = "0rem 0rem";
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

    // Chart creation with data from PHP (accessed via JSON script tag)
    if (typeof Highcharts !== 'undefined') {
        var chartDataElement = document.getElementById('chart-data');
        var chartData = {};
        var dates = [];
        var heartRateData = [];
        var cholesterolData = [];
        var weightData = [];

        if (chartDataElement) {
            try {
                chartData = JSON.parse(chartDataElement.textContent);
                dates = chartData.dates || [];
                heartRateData = chartData.heartRate || [];
                cholesterolData = chartData.cholesterol || [];
                weightData = chartData.weight || [];
            } catch (e) {
                console.error('Error parsing chart data:', e);
            }
        }

        // Heart Rate Chart
        if (dates.length > 0 && heartRateData.length > 0 && document.getElementById('heartRateChart')) {
            Highcharts.chart('heartRateChart', {
                    chart: {
                        backgroundColor: 'transparent'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: dates
                    },
                    yAxis: {
                        title: {
                            text: 'Heart Rate (bpm)'
                        }
                    },
                    series: [{
                        name: 'Heart Rate',
                        data: heartRateData
                    }]
                });
            }

            // Cholesterol Level Chart
            if (dates.length > 0 && cholesterolData.length > 0 && document.getElementById('cholChart')) {
                Highcharts.chart('cholChart', {
                    chart: {
                        backgroundColor: 'transparent'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: dates
                    },
                    yAxis: {
                        title: {
                            text: 'Cholesterol Level (mg/dL)'
                        }
                    },
                    series: [{
                        name: 'Cholesterol Level',
                        data: cholesterolData
                    }]
                });
            }

            // Weight Chart
            if (dates.length > 0 && weightData.length > 0 && document.getElementById('weightChart')) {
                Highcharts.chart('weightChart', {
                    chart: {
                        backgroundColor: 'transparent'
                    },
                    title: {
                        text: ''
                    },
                    xAxis: {
                        categories: dates
                    },
                    yAxis: {
                        title: {
                            text: 'Weight (kg)'
                        }
                    },
                    series: [{
                        name: 'Weight',
                        data: weightData
                    }]
                });
            }
    }

    // Form toggle functionality
    var addRecordBtn = document.getElementById("add-report-btn");
    var hideRecordBtn = document.getElementById("frm-close-btn");
    var recordForm = document.getElementById("add-report-form");

    if (addRecordBtn && recordForm) {
        addRecordBtn.addEventListener("click", function() {
            recordForm.style.display = "flex";
            console.log("Form opened");
        });
    }

    if (hideRecordBtn && recordForm) {
        hideRecordBtn.addEventListener("click", function() {
            recordForm.style.display = "none";
        });
    }
});