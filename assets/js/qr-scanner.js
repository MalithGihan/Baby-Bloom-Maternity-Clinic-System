// QR Scanner JavaScript
document.addEventListener('DOMContentLoaded', function() {
    var qrBtn = document.getElementById("scan-qr-btn");
    var qrCBtn = document.getElementById("scan-close");
    var camWindow = document.getElementById("preview-window");

    if (qrBtn) {
        qrBtn.addEventListener("click", function() {
            if (camWindow) camWindow.style.display = "flex";

            let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
            scanner.addListener('scan', function (content) {
                console.log(content);
            });
            Instascan.Camera.getCameras().then(function (cameras) {
                if (cameras.length > 0) {
                    scanner.start(cameras[0]);
                } else {
                    console.error('No cameras found.');
                }
            }).catch(function (e) {
                console.error(e);
            });

            scanner.addListener('scan', function(c) {
                var nicSearchInput = document.getElementById("mom-nic-search");
                if (nicSearchInput) {
                    nicSearchInput.value = c;
                }

                if (camWindow) camWindow.style.display = "none";
                scanner.stop();
            });

            // Close the window and release the camera resource
            if (qrCBtn) {
                qrCBtn.addEventListener("click", function() {
                    if (camWindow) camWindow.style.display = "none";
                    scanner.stop();
                });
            }
        });
    }
});