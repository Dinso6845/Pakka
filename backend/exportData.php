<?php
include('connect.php');
$conn = dbconnect();

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];


// ตรวจสอบว่ามีข้อมูลในช่วงวันที่ที่เลือก
$sqlCheck = "SELECT COUNT(*) AS count FROM electricity WHERE em_timestamp BETWEEN '$startDate' AND '$endDate'";
$resultCheck = $conn->query($sqlCheck);
$rowCheck = $resultCheck->fetch_assoc();

if ($rowCheck['count'] == 0) {
    die("ไม่พบข้อมูลในช่วงวันที่ ที่เลือก");
}

// ดึงข้อมูล
$sql = "SELECT em_id, em_timestamp, em_roomNo, em_meterID, em_addNumber 
        FROM electricity 
        WHERE em_timestamp BETWEEN '$startDate' AND '$endDate' 
        ORDER BY em_id ASC"; // เปลี่ยนจาก DESC เป็น ASC

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header("Content-Type: application/vnd.ms-excel");
    $outputFileName = 'Electricity_Export_' . date('d-m-Y H:i:s', strtotime($startDate)) . "_to_" . date('d-m-Y', strtotime($endDate)) . '.xls';
    header("Content-Disposition: attachment; filename=\"$outputFileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // สร้างตาราง HTML
    echo "<table border='1'>";
    echo "<tr>
            <th>ลำดับ</th>
            <th>วันที่</th>
            <th>หมายเลขห้อง</th>
            <th>หมายเลขเครื่องมิเตอร์</th>
            <th>เลขมิเตอร์</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        $formattedDate = date('d-m-Y H:i:s', strtotime($row['em_timestamp'])); // ฟอร์แมตวันที่ให้เป็น d-m-Y H:i:s
        echo "<tr>
                <td>{$row['em_id']}</td>
                <td>{$formattedDate}</td>
                <td>{$row['em_roomNo']}</td>
                <td>{$row['em_meterID']}</td>
                <td>{$row['em_addNumber']}</td>
              </tr>";
    }
    echo "</table>";
    exit;
} else {
    die("ไม่พบข้อมูล");
}


$conn->close();
?>
