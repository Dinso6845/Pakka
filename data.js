// ฟังก์ชันที่ใช้แสดงป๊อปอัพเพื่อยืนยันการบันทึก
function confirmSave(event) {
    event.preventDefault(); // ป้องกันการส่งฟอร์มทันที

    // แสดงป๊อปอัพยืนยันการบันทึกข้อมูล
    document.getElementById('popup').style.display = 'block';

    // เมื่อกดปุ่มยืนยันในป๊อปอัพ
    document.getElementById('confirmBtn').addEventListener('click', function() {
        document.getElementById('dataForm').submit(); // ส่งฟอร์ม
        document.getElementById('popup').style.display = 'none'; // ซ่อนป๊อปอัพ
    });

    // เมื่อกดปุ่มยกเลิกในป๊อปอัพ
    document.getElementById('cancelBtn').addEventListener('click', function() {
        document.getElementById('popup').style.display = 'none'; // ซ่อนป๊อปอัพ
        alert("ยกเลิกการบันทึกข้อมูล"); // แจ้งเตือน
    });
}

// ฟังก์ชันเพิ่มแถวใหม่เมื่อกดปุ่ม "เพิ่มข้อมูลอีก"
document.getElementById('addRow').addEventListener('click', function () {
    const inputContainer = document.getElementById('inputContainer');
    const newRow = document.createElement('div');
    newRow.classList.add('inputRow');
    newRow.innerHTML = `
        <input type="text" name="em_roomNo[]" placeholder="Room Number" required>
        <input type="text" name="em_meterID[]" placeholder="Meter Serial Number" required>
    `;
    inputContainer.appendChild(newRow);
});

// ฟังก์ชันไปที่หน้า editDatabase.html เมื่อกดปุ่ม "จัดการข้อมูล"
document.getElementById('manage').addEventListener('click', function() {
    window.location.href = 'editDatabase.html';
});

// เพิ่ม event listener ให้ปุ่มบันทึก
document.getElementById('saveBtn').addEventListener('click', confirmSave);


const baseURL = "http://127.0.0.1/Electricity/frontend/data.html";
        const encodedURL = encodeURIComponent(baseURL);
    
        window.history.replaceState({}, "", `?url=${encodedURL}`);