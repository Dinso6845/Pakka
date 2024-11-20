<?php
include('connect.php');
$conn = dbconnect();

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงวันที่จาก URL
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// ตรวจสอบว่ามีวันที่หรือไม่
if (empty($startDate) || empty($endDate)) {
    die("กรุณาระบุวันที่เริ่มต้นและวันที่สิ้นสุด");
}<?php
include('connect.php');
$conn = dbconnect();

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงวันที่จาก URL
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$endDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

// ตรวจสอบว่ามีวันที่หรือไม่
if (empty($startDate) || empty($endDate)) {
    die("กรุณาระบุวันที่เริ่มต้นและวันที่สิ้นสุด");
}

// แปลงวันที่จาก format ที่ส่งมาเป็น 'Y-m-d' เพื่อให้สามารถใช้ในคำสั่ง SQL ได้
$startDate = date('Y-m-d', strtotime($startDate));
$endDate = date('Y-m-d', strtotime($endDate));

// คำสั่ง SQL สำหรับดึงข้อมูลตามช่วงวันที่เลือก และจัดเรียงตาม em_id
$sql = "SELECT em_id, em_timestamp, em_roomNo, em_meterID, em_addNumber FROM electricity WHERE em_timestamp BETWEEN '$startDate' AND '$endDate' ORDER BY em_id DESC";
$result = $conn->query($sql);

// ตรวจสอบว่าได้รับผลลัพธ์จากการ query หรือไม่
if ($result->num_rows > 0) {
    // ตั้งชื่อไฟล์ CSV
    $filename = "Electricity_Export_" . $startDate . "_to_" . $endDate . ".csv";
    
    // กำหนด Header ให้เบราว์เซอร์ดาวน์โหลดไฟล์
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    
    // เปิดการเขียนไฟล์ CSV
    $output = fopen('php://output', 'w');

    // เขียนหัวข้อใน CSV
    fputcsv($output, array('รหัส', 'วันที่', 'หมายเลขห้อง', 'หมายเลขเครื่องมิเตอร์', 'ค่าไฟเพิ่มเติม'), ';');

    // เขียนข้อมูลจากฐานข้อมูล
    while ($row = $result->fetch_assoc()) {
        // ฟอร์แมตวันที่ให้เป็นรูปแบบที่ต้องการ
        $formattedDate = date('d/m/Y', strtotime($row['em_timestamp'])); // ฟอร์แมตวันที่
        fputcsv($output, array(
            $row['em_id'], 
            $formattedDate, 
            $row['em_roomNo'], 
            $row['em_meterID'], 
            $row['em_addNumber']
        ), ';');
    }

    // ปิดการเขียนไฟล์ CSV
    fclose($output);
} else {
    // ถ้าไม่มีข้อมูลในช่วงวันที่ที่เลือก
    die("ไม่พบข้อมูลที่ตรงกับวันที่ที่เลือก");
}

$conn->close();
?>


// คำสั่ง SQL สำหรับดึงข้อมูลตามช่วงวันที่เลือก
$sql = "SELECT em_id, em_timestamp, em_roomNo, em_meterID, em_addNumber FROM electricity WHERE em_timestamp BETWEEN '$startDate' AND '$endDate'";
$result = $conn->query($sql);

// สร้างไฟล์ CSV หากมีข้อมูล
if ($result->num_rows > 0) {
    // ตั้งชื่อไฟล์ CSV
    $filename = "Electricity_Export_" . $startDate . "_to_" . $endDate . ".csv";
    
    // กำหนด Header ให้เบราว์เซอร์ดาวน์โหลดไฟล์
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    
    // เปิดการเขียนไฟล์ CSV
    $output = fopen('php://output', 'w');

    // เขียนหัวข้อใน CSV
    fputcsv($output, array('รหัส', 'วันที่', 'หมายเลขห้อง', 'หมายเลขเครื่องมิเตอร์', 'ค่าไฟเพิ่มเติม'));

    // เขียนข้อมูลจากฐานข้อมูล
    while ($row = $result->fetch_assoc()) {
        $formattedDate = date('d/m/Y', strtotime($row['em_timestamp'])); // ฟอร์แมตวันที่
        fputcsv($output, array(
            $row['em_id'], 
            $formattedDate, 
            $row['em_roomNo'], 
            $row['em_meterID'], 
            $row['em_addNumber']
        ));
    }

    // ปิดการเขียนไฟล์ CSV
    fclose($output);
} else {
    echo "ไม่พบข้อมูลที่ตรงกับวันที่ที่เลือก";
}

$conn->close();
?>
