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

    // สร้างคำสั่ง SQL สำหรับเพิ่มข้อมูลในตาราง electricity
    $sql_electricity = "INSERT INTO `electricity` (`Roomno`, `SN`, `em_sum`) 
                        VALUES (?, ?, ?)";
    
    // สร้างคำสั่ง SQL สำหรับเพิ่มข้อมูลในตาราง ewgreport
    $sql_ewgreport = "INSERT INTO `ewgreport` (`Roomno`, `SN`) 
                      VALUES (?, ?)";

    // เตรียมคำสั่ง SQL
    $stmt_electricity = $conn->prepare($sql_electricity);
    $stmt_ewgreport = $conn->prepare($sql_ewgreport);

    if ($stmt_electricity === false || $stmt_ewgreport === false) {
        die("SQL Error: " . $conn->error);
    }

    // วนลูปเพื่อเพิ่มข้อมูลทีละแถว
    for ($i = 0; $i < count($Roomno); $i++) {
        $sum = $Roomno[$i] . "-" . $SN[$i];  // ใช้ $SN แทน $SN[$i]

        // Binding ข้อมูลสำหรับตาราง electricity
        $stmt_electricity->bind_param('sss', $Roomno[$i], $SN[$i], $sum);

        // Binding ข้อมูลสำหรับตาราง ewgreport
        // สมมติว่าค่า Meter12, Meter11, Meter10, Meter09 มีค่าเริ่มต้นเป็น 0
        $meter12 = $meter11 = $meter10 = $meter09 = 0;
        $stmt_ewgreport->bind_param('ss', $Roomno[$i], $SN[$i]);

        // Execute การเพิ่มข้อมูลในทั้งสองตาราง
        if (!$stmt_electricity->execute()) {
            echo "Error inserting into electricity: Roomno={$Roomno[$i]}, SN={$SN[$i]}: {$stmt_electricity->error}<br>";
        }
        
        if (!$stmt_ewgreport->execute()) {
            echo "Error inserting into ewgreport: Roomno={$Roomno[$i]}, SN={$SN[$i]}: {$stmt_ewgreport->error}<br>";
        }
    }

    // ปิดการเชื่อมต่อ
    $stmt_electricity->close();
    $stmt_ewgreport->close();
    $conn->close();

    echo "<script>window.location.href = '../frontend/data.html';</script>";
} else {
    echo "<script>alert('ข้อมูลไม่ครบถ้วน กรุณาตรวจสอบข้อมูล'); window.location.href = '../frontend/data.html';</script>";
}
?>
