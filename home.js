// สร้างตัวแปรสำหรับเก็บ instance ของ QR scanner
let qrCodeScanner = null;

// ฟังก์ชันสำหรับเริ่มการสแกน QR Code
async function startQRScanner() {
    try {
        // ตรวจสอบว่า library พร้อมใช้งาน
        if (typeof Html5Qrcode === 'undefined') {
            throw new Error('QR Code scanner library ไม่พร้อมใช้งาน กรุณารีเฟรชหน้าเว็บ');
        }

        // หยุดการสแกนที่กำลังทำงานอยู่ (ถ้ามี)
        if (qrCodeScanner) {
            await stopQRScanner();
        }

        // สร้าง scanner ใหม่
        qrCodeScanner = new Html5Qrcode("qr-reader");

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 },
            aspectRatio: 1.0,
            showTorchButtonIfSupported: true
        };

        // แสดง QR reader ก่อนเริ่มสแกน
        document.getElementById('qr-reader').style.display = 'block';

        // เริ่มสแกนด้วยกล้องหลัง
        await qrCodeScanner.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                document.getElementById("input1").value = decodedText;
                stopQRScanner();
                alert("สแกน QR Code สำเร็จ: " + decodedText);
            },
            (errorMessage) => {
                // ไม่ต้องแสดง error ในการสแกนปกติ
                console.log(errorMessage);
            }
        );

    } catch (error) {
        console.error("Error starting QR scanner:", error);
        alert("ไม่สามารถเริ่มการสแกนได้: " + error.message);
        
        // หากเกิดข้อผิดพลาด ให้ทำความสะอาด instance เก่า
        if (qrCodeScanner) {
            await qrCodeScanner.clear();
            qrCodeScanner = null;
        }
        document.getElementById('qr-reader').style.display = 'none';
    }
}

// ฟังก์ชันสำหรับหยุดการสแกน
async function stopQRScanner() {
    try {
        if (qrCodeScanner) {
            if (qrCodeScanner.isScanning) {
                await qrCodeScanner.stop();
            }
            await qrCodeScanner.clear();
            qrCodeScanner = null;
            document.getElementById('qr-reader').style.display = 'none';
        }
    } catch (error) {
        console.error("Error stopping QR scanner:", error);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener สำหรับปุ่ม Camera
    const cameraBtn = document.getElementById("cameraBtn");
    if (cameraBtn) {
        cameraBtn.addEventListener("click", async () => {
            try {
                // ขออนุญาตใช้กล้อง
                await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: "environment",
                        width: { ideal: 1280 },
                        height: { ideal: 720 }
                    }
                });
                
                // เริ่มการสแกน
                await startQRScanner();
                
            } catch (error) {
                console.error("Error accessing camera:", error);
                alert("ไม่สามารถเข้าถึงกล้องได้ กรุณาตรวจสอบการอนุญาตใช้งานกล้อง");
            }
        });
    }

    // Event listener สำหรับปุ่ม Upload
    const uploadBtn = document.getElementById("uploadBtn");
    if (uploadBtn) {
        uploadBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = handleImageUpload;
            input.click();
        });
    }
});

// ฟังก์ชันสำหรับจัดการการอัพโหลดรูปภาพ
function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100%';
            document.querySelector('.upload-box').appendChild(img);
        };
        reader.readAsDataURL(file);
    }
}
