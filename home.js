// สร้างตัวแปรสำหรับเก็บ instance ของ QR scanner
let qrCodeScanner = null;

// ฟังก์ชันสำหรับเริ่มการสแกน QR Code
async function startQRScanner() {
    try {
        if (qrCodeScanner) {
            await stopQRScanner();
        }

        // ซ่อนเนื้อหาเดิมและแสดงกล้อง
        document.getElementById('upload-content').style.display = 'none';
        document.getElementById('camera-container').style.display = 'block';

        qrCodeScanner = new Html5Qrcode("camera-container");

        const config = {
            fps: 10,
            qrbox: { width: 200, height: 200 },
            aspectRatio: 1.0
        };

        await qrCodeScanner.start(
            { facingMode: "environment" },
            config,
            (decodedText) => {
                document.getElementById("input1").value = decodedText;
                alert("สแกน QR Code สำเร็จ");
            },
            (errorMessage) => {
                console.log(errorMessage);
            }
        );

    } catch (error) {
        console.error("Error starting QR scanner:", error);
        alert("ไม่สามารถเริ่มการสแกนได้");
        
        if (qrCodeScanner) {
            await qrCodeScanner.clear();
            qrCodeScanner = null;
        }
        document.getElementById('camera-container').style.display = 'none';
        document.getElementById('upload-content').style.display = 'flex';
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
        }
        document.getElementById('camera-container').style.display = 'none';
        document.getElementById('upload-content').style.display = 'flex';
    } catch (error) {
        console.error("Error stopping QR scanner:", error);
    }
}

// ฟังก์ชันสำหรับจัดการการอัพโหลดรูปภาพและสแกน
async function handleImageUpload(event) {
    const file = event.target.files[0];
    if (file) {
        const imageUrl = URL.createObjectURL(file);

        try {
            // สร้าง instance ใหม่ของ Html5Qrcode
            const html5QrCode = new Html5Qrcode("qr-reader");
            const decodedText = await html5QrCode.scanFile(file, true);

            // แสดงผลลัพธ์ที่สแกนได้
            document.getElementById("input1").value = decodedText;
            alert("สแกน QR Code สำเร็จ");
            
            // เคลียร์ตัวสแกน
            await html5QrCode.clear();
        } catch (error) {
            console.error("Error scanning file:", error);
            alert("ไม่สามารถสแกนภาพได้");
        }
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    const cameraBtn = document.getElementById("cameraBtn");
    if (cameraBtn) {
        cameraBtn.addEventListener("click", startQRScanner);
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

    // เพิ่ม event listener สำหรับ form
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // เคลียร์ค่าใน input ทุกช่อง
            const inputs = form.querySelectorAll('input');
            inputs.forEach(input => {
                input.value = '';
            });
            
            // สร้างและแสดงข้อความแจ้งเตือน
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert-message';
            alertDiv.textContent = 'บันทึกสำเร็จ';
            document.body.appendChild(alertDiv);
            
            // ลบข้อความแจ้งเตือนหลังจาก 2 วินาที
            setTimeout(() => {
                alertDiv.remove();
            }, 1000);
        });
    }
});
    