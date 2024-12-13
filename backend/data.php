<?php
include('connect.php');
$conn = dbconnect(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $Roomno = $_POST['Roomno'];
    $SN = $_POST['SN'];

    // ตรวจสอบข้อมูลก่อน
    if (empty($Roomno) || empty($SN)) {
        die("<script>alert('ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบข้อมูล'); window.location.href = '../frontend/data.html';</script>");
    }

    // สร้างคำสั่ง SQL สำหรับเพิ่มข้อมูล
    $sql = "INSERT INTO `electricity` (`Roomno`, `SN`, `em_sum`) 
            VALUES (?, ?, ?)";

    // เตรียมคำสั่ง SQL
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("SQL Error: " . $conn->error);
    }

    // วนลูปเพื่อเพิ่มข้อมูลทีละแถว
    for ($i = 0; $i < count($Roomno); $i++) {
        // $SN = uniqid();  // สร้างค่า SN ที่ไม่ซ้ำกัน
        $sum = $Roomno[$i] . "-" . $SN[$i];  // ใช้ $SN แทน $SN[$i]

        // Binding ข้อมูลที่ต้องการเพิ่ม
        $stmt->bind_param('sss', $Roomno[$i], $SN[$i], $sum);  // เปลี่ยนจาก 'ssss' เป็น 'sss'

        // Execute การเพิ่มข้อมูล
        if (!$stmt->execute()) {
            echo "Error inserting Roomno={$Roomno[$i]}, SN={$SN[$i]}: {$stmt->error}<br>";
        }
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();

    echo "<script>window.location.href = '../frontend/data.html';</script>";
} else {
    echo "<script>alert('ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบข้อมูล'); window.location.href = '../frontend/data.html';</script>";
}
?>
