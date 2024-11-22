<?php
// เชื่อมต่อฐานข้อมูล
include('connect.php');
$conn = dbconnect(); // ใช้ฟังก์ชัน dbconnect ที่เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับข้อมูลจากฟอร์ม
    $roomNos = $_POST['em_roomNo']; // รับข้อมูล Room Number
    $meterIDs = $_POST['em_meterID']; // รับข้อมูล Meter Serial Number

    // ใช้คำสั่ง SQL สำหรับการเพิ่มข้อมูล
    $sql = "INSERT INTO `electricity` (`em_roomNo`, `em_meterID`, `em_sum`) 
            VALUES (?, ?, ?)";

    // เตรียมคำสั่ง SQL ด้วย MySQLi
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("ERROR: " . $conn->error); // ถ้ามีข้อผิดพลาดในการเตรียมคำสั่ง SQL
    }

    // ใช้การวนลูปเพื่อบันทึกข้อมูลทีละแถว
    for ($i = 0; $i < count($roomNos); $i++) {
        // สร้างค่า em_sum จากการรวม em_roomNo และ em_meterID ด้วย "-"
        $sum = $roomNos[$i] . "-" . $meterIDs[$i];

        // Binding ข้อมูลในแต่ละแถว
        $stmt->bind_param('sss', $roomNos[$i], $meterIDs[$i], $sum);

        // Execute การบันทึกข้อมูล
        $stmt->execute();
    }

    // แจ้งว่าเพิ่มข้อมูลสำเร็จ
    echo "<script>alert('บันทึกข้อมูลสำเร็จ!'); window.location.href = '../frontend/data.html';</script>";
} else {
    echo "<script>alert('ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบข้อมูล'); window.location.href = '../frontend/data.html';</script>";
}
?>
