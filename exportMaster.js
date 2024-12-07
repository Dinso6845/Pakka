// ลบฟังก์ชัน openExportModal ออก
// ฟังก์ชัน Export ข้อมูล
function exportData() {
    // สร้าง URL สำหรับส่งคำร้องขอไปยัง PHP
    const url = `../backend/exportMaster.php`;  // ไม่มีพารามิเตอร์วันที่

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
                link.download = `Electricity_Export_All.xls`;  // ใช้ชื่อไฟล์ที่ไม่ขึ้นกับวันที่
                link.click();
            } else {
                alert(data);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message);
        });
}
