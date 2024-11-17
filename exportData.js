function exportData() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;

    if (!startDate || !endDate) {
        alert("กรุณาเลือกวันเริ่มต้นและวันสิ้นสุด");
        return;
    }

    // ส่งข้อมูลวันที่ที่เลือกไปที่ backend เพื่อให้มันสร้างไฟล์ Excel
    window.location.href = `exportExcel.php?start_date=${startDate}&end_date=${endDate}`;
}

function exportData() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;

    if (!startDate || !endDate) {
        alert("กรุณาเลือกวันที่เริ่มต้นและวันที่สิ้นสุด");
        return;
    }

    // สร้าง URL สำหรับการดึงข้อมูลจากฐานข้อมูล (สามารถส่งค่า startDate และ endDate ไปที่ backend)
    const url = `../backend/exportExcel.php?startDate=${startDate}&endDate=${endDate}`;

    // ส่งคำร้องขอข้อมูลจาก backend
    fetch(url)
        .then(response => response.blob())
        .then(blob => {
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'data_export.csv';  // ตั้งชื่อไฟล์ที่จะดาวน์โหลด
            link.click();
        })
        .catch(error => {
            console.error('Error exporting data:', error);
            alert('เกิดข้อผิดพลาดในการส่งข้อมูล');
        });
}
