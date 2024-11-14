// สร้างตัวแปรสำหรับเก็บ instance ของ QR scanner
let qrCodeScanner = null;
let currentCamera = 'environment'; // เริ่มต้นใช้กล้องหลัง

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

        // เริ่มสแกน
        await qrCodeScanner.start(
            { facingMode: currentCamera },
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
    try {
        if (qrCodeScanner && qrCodeScanner.isScanning) {
            await qrCodeScanner.stop();
            document.getElementById('qr-reader').style.display = 'none';
            document.getElementById('camera-switch-container').style.display = 'none';
        }
    } catch (error) {
        console.error("Error stopping QR scanner:", error);
    }
}

// ฟังก์ชันสำหรับแสดง modal เลือกกล้อง
function showCameraSelectModal() {
    const modal = document.getElementById('camera-select-modal');
    modal.style.display = 'block';
}

// ฟังก์ชันสำหรับซ่อน modal
function hideCameraSelectModal() {
    const modal = document.getElementById('camera-select-modal');
    modal.style.display = 'none';
}

// ฟังก์ชันสำหรับสลับกล้อง
async function switchCamera() {
    try {
        await stopQRScanner();
        currentCamera = currentCamera === 'environment' ? 'user' : 'environment';
        await new Promise(resolve => setTimeout(resolve, 500));
        await startQRScanner();
    } catch (error) {
        console.error("Error switching camera:", error);
        alert("ไม่สามารถสลับกล้องได้ กรุณาลองใหม่อีกครั้ง");
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Event listener สำหรับปุ่ม Camera
    const cameraBtn = document.getElementById("cameraBtn");
    if (cameraBtn) {
        cameraBtn.addEventListener("click", () => {
            // ขออนุญาตใช้กล้องก่อนแสดง modal
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(() => {
                    showCameraSelectModal();
                })
                .catch((error) => {
                    console.error("Error accessing camera:", error);
                    alert("ไม่สามารถเข้าถึงกล้องได้ กรุณาตรวจสอบการอนุญาตใช้งานกล้อง");
                });
        });
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
