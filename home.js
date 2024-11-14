// สร้างตัวแปรสำหรับเก็บ instance ของ QR scanner
let qrCodeScanner = null;

// ฟังก์ชันสำหรับเริ่มการสแกน QR Code
async function startQRScanner() {
    try {
        // สร้าง scanner ใหม่ถ้ายังไม่มี
        if (!qrCodeScanner) {
            qrCodeScanner = new Html5Qrcode("qr-reader");
        }

        const config = {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        };

        // เริ่มสแกนด้วยกล้องหลัง
        await qrCodeScanner.start(
            { facingMode: 'environment' }, // กำหนดให้ใช้กล้องหลังเท่านั้น
            config,
            (decodedText) => {
                document.getElementById("input1").value = decodedText;
                stopQRScanner();
                alert("สแกน QR Code สำเร็จ: " + decodedText);
            },
            (errorMessage) => {
                console.log(errorMessage);
            }
        );

        // แสดง QR reader
        document.getElementById('qr-reader').style.display = 'block';

    } catch (error) {
        console.error("Error starting QR scanner:", error);
        alert("ไม่สามารถเริ่มการสแกนได้ กรุณาลองใหม่อีกครั้ง");
    }
}

// ฟังก์ชันสำหรับหยุดการสแกน
async function stopQRScanner() {
    try {
        if (qrCodeScanner && qrCodeScanner.isScanning) {
            await qrCodeScanner.stop();
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
        cameraBtn.addEventListener("click", () => {
            // ขออนุญาตใช้กล้องและเริ่มสแกนทันที
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
                .then(() => {
                    startQRScanner();
                })
                .catch((error) => {
                    console.error("Error accessing camera:", error);
                    alert("ไม่สามารถเข้าถึงกล้องได้ กรุณาตรวจสอบการอนุญาตใช้งานกล้อง");
                });
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
