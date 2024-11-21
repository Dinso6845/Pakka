document.addEventListener('DOMContentLoaded', function () {
    var resultContainer = document.getElementById('qr-reader-results');
    var lastResult, countResults = 0;

    // สร้างตัว scanner ของ QR Code
    var html5QrcodeScanner = new Html5QrcodeScanner(
        "qr-reader", { fps: 10, qrbox: 250 });

    // ฟังก์ชันเมื่อสแกน QR สำเร็จ
    function onScanSuccess(decodedText, decodedResult) {
        if (decodedText !== lastResult) {
            ++countResults;
            lastResult = decodedText;
            console.log(`ผลลัพธ์จากการสแกน = ${decodedText}`, decodedResult);

            // แสดงผลในผลลัพธ์
            resultContainer.innerHTML = `<div>[${countResults}] - ${decodedText}</div>`;

            // ใส่ข้อมูลลงในช่อง input1
            document.getElementById("input1").value = decodedText;

            // ตัวเลือก: หยุดการสแกนหลังจากได้ผลลัพธ์แรก
            // html5QrcodeScanner.clear();
        }
    }

    // ฟังก์ชันสำหรับข้อผิดพลาด
    function onScanError(qrCodeError) {
        console.error(`ข้อผิดพลาดจากการสแกน QR: ${qrCodeError}`);
    }

    // เริ่มการแสดงผล
    html5QrcodeScanner.render(onScanSuccess, onScanError);

    // ฟังก์ชันสำหรับการเริ่มต้น QR scanner เมื่อกดปุ่ม
    const cameraBtn = document.getElementById("cameraBtn");
    if (cameraBtn) {
        cameraBtn.addEventListener("click", function () {
            document.getElementById('upload-content').style.display = 'none';
            document.getElementById('camera-container').style.display = 'block';

            const qrCodeScanner = new Html5Qrcode("camera-container");
            qrCodeScanner
                .start(
                    { facingMode: "environment" },
                    { fps: 10, qrbox: 250 },
                    onScanSuccess,
                    onScanError
                )
                .catch((err) => {
                    console.error("Error starting QR scanner:", err);
                });
        });
    }

    // ฟังก์ชันหยุด QR scanner เมื่อไม่ต้องการใช้งาน
    const stopBtn = document.getElementById("stopBtn");
    if (stopBtn) {
        stopBtn.addEventListener("click", function () {
            const qrCodeScanner = new Html5Qrcode("camera-container");
            qrCodeScanner
                .stop()
                .then(() => {
                    qrCodeScanner.clear();
                    document.getElementById('camera-container').style.display = 'none';
                    document.getElementById('upload-content').style.display = 'flex';
                })
                .catch((err) => {
                    console.error("Error stopping QR scanner:", err);
                });
        });
    }
});
