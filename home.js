// สร้างตัวแปรสำหรับเก็บ instance ของ QR scanner
let qrCodeScanner = null;
let currentCamera = 'environment'; // เริ่มต้นใช้กล้องหลัง

// ฟังก์ชันสำหรับเริ่มการสแกน QR Code
async function startQRScanner() {
    try {
        if (qrCodeScanner === null) {
            qrCodeScanner = new Html5Qrcode("qr-reader");
        }

        const config = {
            fps: 10,
            qrbox: 250,
            aspectRatio: 1.0
        };

        await qrCodeScanner.start(
            { facingMode: currentCamera },
            config,
            (decodedText) => {
                document.getElementById("input1").value = decodedText;
                stopQRScanner();
                alert("สแกน QR Code สำเร็จ: " + decodedText);
            },
            (errorMessage) => {
                console.log("Error scanning QR Code: ", errorMessage);
            }
        );

        // แสดง QR reader และปุ่มสลับกล้อง
        document.getElementById('qr-reader').style.display = 'block';
        document.getElementById('camera-switch-container').style.display = 'block';
    } catch (error) {
        console.error("Error starting QR scanner:", error);
        alert("ไม่สามารถเริ่มการสแกนได้ กรุณาลองใหม่อีกครั้ง");
    }
}

// ฟังก์ชันสำหรับหยุดการสแกน
async function stopQRScanner() {
    if (qrCodeScanner && qrCodeScanner.isScanning) {
        try {
            await qrCodeScanner.stop();
            document.getElementById('qr-reader').style.display = 'none';
        } catch (error) {
            console.error("Error stopping QR scanner:", error);
        }
    }
}

// ฟังก์ชันสำหรับสลับกล้อง
async function switchCamera() {
    try {
        if (qrCodeScanner && qrCodeScanner.isScanning) {
            await stopQRScanner();
            currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
            await new Promise(resolve => setTimeout(resolve, 300)); // รอให้กล้องปิดสนิท
            await startQRScanner();
        }
    } catch (error) {
        console.error("Error switching camera:", error);
        alert("ไม่สามารถสลับกล้องได้ กรุณาลองใหม่อีกครั้ง");
    }
}

// เพิ่มฟังก์ชันสำหรับแสดง modal เลือกกล้อง
function showCameraSelectModal() {
    const modal = document.getElementById('camera-select-modal');
    modal.style.display = 'block';
}

// เพิ่มฟังก์ชันสำหรับซ่อน modal
function hideCameraSelectModal() {
    const modal = document.getElementById('camera-select-modal');
    modal.style.display = 'none';
}

// แก้ไข Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener สำหรับปุ่ม Camera
    const cameraBtn = document.getElementById("cameraBtn");
    if (cameraBtn) {
        cameraBtn.addEventListener("click", showCameraSelectModal);
    }

    // Event listeners สำหรับปุ่มเลือกกล้อง
    const frontCameraBtn = document.getElementById('frontCameraBtn');
    const backCameraBtn = document.getElementById('backCameraBtn');

    if (frontCameraBtn) {
        frontCameraBtn.addEventListener('click', async () => {
            currentCamera = 'user';
            hideCameraSelectModal();
            await startQRScanner();
        });
    }

    if (backCameraBtn) {
        backCameraBtn.addEventListener('click', async () => {
            currentCamera = 'environment';
            hideCameraSelectModal();
            await startQRScanner();
        });
    }

    // Event listener สำหรับปิด modal เมื่อคลิกพื้นหลัง
    const modal = document.getElementById('camera-select-modal');
    if (modal) {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                hideCameraSelectModal();
            }
        });
    }

    // Event listener สำหรับปุ่มสลับกล้อง
    const switchCameraBtn = document.getElementById('switchCameraBtn');
    if (switchCameraBtn) {
        switchCameraBtn.addEventListener('click', switchCamera);
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
