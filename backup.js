// document.addEventListener("DOMContentLoaded", () => {
//     let monthnumber = '';
    
//     fetch('../backend/backupMonth.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//     })
//     .then(response => response.json())
//     .then(result => {
//         console.log(result);
//         monthnumber = result.currentMonth;  // ใช้ currentMonth ที่ได้จาก PHP

//         // ถ้าเดือนที่บันทึกแล้ว (มีข้อมูล), ปิดปุ่ม
//         if (monthnumber != null && monthnumber != '') {
//             document.getElementById("backupButton").disabled = true;
//             // alert('ข้อมูลของเดือนนี้ถูกบันทึกเรียบร้อยแล้ว');
//         } else {
//             document.getElementById("backupButton").disabled = false;
//         }
//     })
//     .catch(error => {
//         console.error("Error:", error);
//         alert("เกิดข้อผิดพลาดในการสำรองข้อมูล");
//     });
    
//     // เมื่อคลิกปุ่มสำรองข้อมูล
//     document.getElementById("backupButton").addEventListener("click", () => {
//         // ตรวจสอบว่าเดือนนี้ถูกบันทึกแล้วหรือยัง
//         if (document.getElementById("backupButton").disabled) {
//             alert("ข้อมูลของเดือนนี้ถูกบันทึกเรียบร้อยแล้ว");
//             return; 
//         }
//         showPopup(); 
//     });
// });
  
// // ฟังก์ชันแสดง Popup
// function showPopup() {
//     const popupOverlay = document.getElementById('popupOverlay');
//     popupOverlay.classList.add('active'); 
// }
  
// // ฟังก์ชันปิด Popup
// function closePopup() {
//     const popupOverlay = document.getElementById('popupOverlay');
//     popupOverlay.classList.remove('active');
// }
  
// // ฟังก์ชันยืนยันการสำรองข้อมูล
// function confirmBackup() {
//     fetch('../backend/backupData.php', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//         },
//     })
//     .then(response => response.json())
//     .then(result => {
//         alert(result.message); 
//         closePopup(); 
//     })
//     .catch(error => {
//         console.error("Error:", error);
//         alert("เกิดข้อผิดพลาดในการสำรองข้อมูล");
//         closePopup(); 
//     });
// }
