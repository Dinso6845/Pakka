function docReady(fn) {
    // ตรวจสอบว่า DOM พร้อมใช้งานหรือยัง
    if (document.readyState === "complete" || document.readyState === "interactive") {
        setTimeout(fn, 1);
    } else {
        document.addEventListener("DOMContentLoaded", fn);
    }
}

docReady(function() {
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
            resultContainer.innerHTML += `<div>[${countResults}] - ${decodedText}</div>`;

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
});
