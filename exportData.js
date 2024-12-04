// เปิด Modal สำหรับ Export ข้อมูล
function openExportModal() {
    document.getElementById('exportModal').style.display = 'block';
}

// ปิด Modal สำหรับ Export ข้อมูล
function closeExportModal() {
    document.getElementById('exportModal').style.display = 'none';
}

// ฟังก์ชัน Export ข้อมูล
function exportData() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    // ตรวจสอบว่าได้เลือกวันที่หรือไม่
    if (!startDate || !endDate) {
        alert("กรุณาเลือกวันเริ่มต้นและวันสิ้นสุด");
        return;
    }

    // สร้าง URL สำหรับส่งคำร้องขอไปยัง PHP
    const url = `../backend/exportData.php?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`;

    // ใช้ fetch เพื่อดึงข้อมูลจาก PHP
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('ไม่สามารถดึงข้อมูลได้: ' + response.statusText);
            }

            const contentType = response.headers.get('Content-Type');
            if (contentType.includes('application/vnd.ms-excel')) {
                return response.blob();
            } else {
                return response.text();
            }
        })
        .then(data => {
            if (data instanceof Blob) {
                const link = document.createElement('a');
                link.href = URL.createObjectURL(data);
                link.download = `Electricity_Export_${startDate}_to_${endDate}.xls`;
                link.click();
                closeExportModal();
            } else {
                alert(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
}
