// Clinic Status JavaScript - Dashboard tabs and charts functionality
document.addEventListener('DOMContentLoaded', function() {
    var stfBtn = document.getElementById("staff-btn");
    var momBtn = document.getElementById("mom-btn");
    var suppBtn = document.getElementById("supp-btn");
    var appBtn = document.getElementById("app-btn");

    var stfContainer = document.getElementById("staff-container");
    var momContainer = document.getElementById("mom-container");
    var suppContainer = document.getElementById("supplement-container");
    var appContainer = document.getElementById("appointment-container");

    // Set initial tab state
    if (stfBtn) {
        stfBtn.style.backgroundColor = "#0D4B53";
    }

    // Staff tab click handler
    if (stfBtn && stfContainer && momContainer && suppContainer && appContainer) {
        stfBtn.addEventListener("click", function() {
            stfContainer.style.display = "flex";
            momContainer.style.display = "none";
            suppContainer.style.display = "none";
            appContainer.style.display = "none";

            stfBtn.style.backgroundColor = "#0D4B53";
            if (momBtn) momBtn.style.backgroundColor = "#86B6BB";
            if (suppBtn) suppBtn.style.backgroundColor = "#86B6BB";
            if (appBtn) appBtn.style.backgroundColor = "#86B6BB";

            // Initialize staff chart if Highcharts is available
            if (typeof Highcharts !== 'undefined' && window.staffChartData) {
                Highcharts.chart('staff-chart-container', {
                    chart: {
                        type: 'pie',
                        backgroundColor: ''
                    },
                    title: {
                        text: ''
                    },
                    series: [{
                        name: 'Positions',
                        data: window.staffChartData
                    }],
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    }
                });
            }
        });
    }

    // Mom tab click handler
    if (momBtn && momContainer && stfContainer && suppContainer && appContainer) {
        momBtn.addEventListener("click", function() {
            momContainer.style.display = "flex";
            stfContainer.style.display = "none";
            suppContainer.style.display = "none";
            appContainer.style.display = "none";

            momBtn.style.backgroundColor = "#0D4B53";
            if (stfBtn) stfBtn.style.backgroundColor = "#86B6BB";
            if (suppBtn) suppBtn.style.backgroundColor = "#86B6BB";
            if (appBtn) appBtn.style.backgroundColor = "#86B6BB";

            // Initialize multiple mom charts if Highcharts is available
            if (typeof Highcharts !== 'undefined') {
                // Rubella chart
                if (window.momRubellaChartData) {
                    Highcharts.chart('rubella-chart-container', {
                        chart: { type: 'pie', backgroundColor: '' },
                        title: { text: '' },
                        series: [{ name: 'Rubella Status', data: window.momRubellaChartData }],
                        plotOptions: {
                            pie: {
                                allowPointSelect: true, cursor: 'pointer',
                                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %' },
                                showInLegend: true
                            }
                        }
                    });
                }

                // Toxoplasmosis chart
                if (window.momToxChartData) {
                    Highcharts.chart('tox-chart-container', {
                        chart: { type: 'pie', backgroundColor: '' },
                        title: { text: '' },
                        series: [{ name: 'Toxoplasmosis Status', data: window.momToxChartData }],
                        plotOptions: {
                            pie: {
                                allowPointSelect: true, cursor: 'pointer',
                                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %' },
                                showInLegend: true
                            }
                        }
                    });
                }

                // Blood Group chart
                if (window.momBgChartData) {
                    Highcharts.chart('bg-chart-container', {
                        chart: { type: 'pie', backgroundColor: '' },
                        title: { text: '' },
                        series: [{ name: 'Blood Groups', data: window.momBgChartData }],
                        plotOptions: {
                            pie: {
                                allowPointSelect: true, cursor: 'pointer',
                                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %' },
                                showInLegend: true
                            }
                        }
                    });
                }

                // Rhogam chart
                if (window.momRhogamChartData) {
                    Highcharts.chart('rhogam-chart-container', {
                        chart: { type: 'pie', backgroundColor: '' },
                        title: { text: '' },
                        series: [{ name: 'Rhogam Status', data: window.momRhogamChartData }],
                        plotOptions: {
                            pie: {
                                allowPointSelect: true, cursor: 'pointer',
                                dataLabels: { enabled: true, format: '<b>{point.name}</b>: {point.percentage:.1f} %' },
                                showInLegend: true
                            }
                        }
                    });
                }
            }
        });
    }

    // Supplement tab click handler
    if (suppBtn && suppContainer && stfContainer && momContainer && appContainer) {
        suppBtn.addEventListener("click", function() {
            suppContainer.style.display = "flex";
            stfContainer.style.display = "none";
            momContainer.style.display = "none";
            appContainer.style.display = "none";

            suppBtn.style.backgroundColor = "#0D4B53";
            if (stfBtn) stfBtn.style.backgroundColor = "#86B6BB";
            if (momBtn) momBtn.style.backgroundColor = "#86B6BB";
            if (appBtn) appBtn.style.backgroundColor = "#86B6BB";

            // Initialize supplement chart if Highcharts is available
            if (typeof Highcharts !== 'undefined' && window.suppChartData) {
                Highcharts.chart('supp-chart-container', {
                    chart: {
                        type: 'pie',
                        backgroundColor: ''
                    },
                    title: {
                        text: ''
                    },
                    series: [{
                        name: 'Supplements',
                        data: window.suppChartData
                    }],
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    }
                });
            }
        });
    }

    // Appointment tab click handler
    if (appBtn && appContainer && stfContainer && momContainer && suppContainer) {
        appBtn.addEventListener("click", function() {
            appContainer.style.display = "flex";
            stfContainer.style.display = "none";
            momContainer.style.display = "none";
            suppContainer.style.display = "none";

            appBtn.style.backgroundColor = "#0D4B53";
            if (stfBtn) stfBtn.style.backgroundColor = "#86B6BB";
            if (momBtn) momBtn.style.backgroundColor = "#86B6BB";
            if (suppBtn) suppBtn.style.backgroundColor = "#86B6BB";

            // Initialize appointment chart if Highcharts is available
            if (typeof Highcharts !== 'undefined' && window.appChartData) {
                Highcharts.chart('app-chart-container', {
                    chart: {
                        type: 'pie',
                        backgroundColor: ''
                    },
                    title: {
                        text: ''
                    },
                    series: [{
                        name: 'Appointments',
                        data: window.appChartData
                    }],
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %'
                            }
                        }
                    }
                });
            }
        });
    }

    // Export button handlers
    var staffExportBtn = document.getElementById("staff-export-btn");
    var momExportBtn = document.getElementById("mom-export-btn");
    var suppExportBtn = document.getElementById("supp-export-btn");
    var appExportBtn = document.getElementById("app-export-btn");

    if (staffExportBtn) {
        staffExportBtn.addEventListener("click", function() {
            window.location.href = "../shared/export-handler.php?type=staff";
        });
    }

    if (momExportBtn) {
        momExportBtn.addEventListener("click", function() {
            window.location.href = "../shared/export-handler.php?type=mama";
        });
    }

    if (suppExportBtn) {
        suppExportBtn.addEventListener("click", function() {
            window.location.href = "../shared/export-handler.php?type=supplement";
        });
    }

    if (appExportBtn) {
        appExportBtn.addEventListener("click", function() {
            window.location.href = "../shared/export-handler.php?type=appointment";
        });
    }
});