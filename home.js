// สร้างตัวแปรสำหรับเก็บ instance ของ QR scanner
let qrCodeScanner = null;

// ฟังก์ชันสำหรับเริ่มการสแกน QR Code
function startQRScanner() {
    if (qrCodeScanner === null) {
        qrCodeScanner = new Html5Qrcode("qr-reader");
    }

    qrCodeScanner.start(
        { facingMode: "environment" },
        {
            fps: 10,
            qrbox: 250,
        },
        (decodedText) => {
            document.getElementById("input1").value = decodedText;
            stopQRScanner();
            alert("สแกน QR Code สำเร็จ: " + decodedText);
        },
        (errorMessage) => {
            console.log("Error scanning QR Code: ", errorMessage);
        }
    ).catch((error) => {
        console.log("Error starting QR Code scanner: ", error);
    });
}

// ฟังก์ชันสำหรับหยุดการสแกน
function stopQRScanner() {
    if (qrCodeScanner) {
        qrCodeScanner.stop().then(() => {
            document.getElementById('qr-reader').style.display = 'none';
        });
    }
}

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

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // ฟังก์ชันสำหรับเปิดกล้อง
    async function openCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: true });
            const videoElement = document.createElement('video');
            videoElement.srcObject = stream;
            videoElement.autoplay = true;
            document.querySelector('.upload-box').appendChild(videoElement);
        } catch (err) {
            console.error("Error accessing camera:", err);
            alert("ไม่สามารถเข้าถึงกล้องได้");
        }
    }

    // Event listener สำหรับกรอบ upload-box
    const uploadBox = document.querySelector('.upload-box');
    if (uploadBox) {
        uploadBox.addEventListener('click', function(e) {
            // ตรวจสอบว่าคลิกที่ปุ่มหรือไม่
            if (!e.target.matches('button')) {
                openCamera();
            }
        });
    }

    // Event listener สำหรับปุ่ม Camera
    const cameraBtn = document.getElementById("cameraBtn");
    if (cameraBtn) {
        cameraBtn.addEventListener("click", openCamera);
    }

    // Event listener สำหรับปุ่ม Upload
    const uploadBtn = document.getElementById("uploadBtn");
    if (uploadBtn) {
        uploadBtn.addEventListener("click", (e) => {
            e.stopPropagation(); // ป้องกันการ bubble ขึ้นไปที่ upload-box
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = handleImageUpload;
            input.click();
        });
    }
});
