<?php
include('connect.php');
$conn = dbconnect();

$startDate = $_GET['start_date'];
$endDate = $_GET['end_date'];

// ตรวจสอบว่ามีข้อมูลในช่วงวันที่ที่เลือก
$sqlCheck = "SELECT COUNT(*) AS count FROM electricity WHERE DatePoint BETWEEN '$startDate' AND '$endDate'";
$resultCheck = $conn->query($sqlCheck);
$rowCheck = $resultCheck->fetch_assoc();

if ($rowCheck['count'] == 0) {
    die("ไม่พบข้อมูลในช่วงวันที่ ที่เลือก");
}

// ดึงข้อมูล
$sql = "SELECT DISTINCT 
            e.DatePoint, 
            e.Roomno, 
            e.SN, 
            e.em_month, 
            m.Meter09, 
            m.Meter10, 
            m.Meter11, 
            m.Meter12,
            (e.em_month - m.Meter12) AS จำนวนหน่วย
        FROM electricity e
        JOIN ewgreport m ON e.SN = m.SN
        WHERE e.DatePoint BETWEEN '$startDate' AND '$endDate'
        ORDER BY e.SN ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    $outputFileName = 'Electricity_Export_' . date('d-m-Y H:i:s', strtotime($startDate)) . "_to_" . date('d-m-Y', strtotime($endDate)) . '.xls';
    header("Content-Disposition: attachment; filename=\"$outputFileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    echo "\xEF\xBB\xBF";

    // Generate month names dynamically
    $monthNames = [
        '1' => 'มกราคม',
        '2' => 'กุมภาพันธ์',
        '3' => 'มีนาคม',
        '4' => 'เมษายน',
        '5' => 'พฤษภาคม',
        '6' => 'มิถุนายน',
        '7' => 'กรกฎาคม',
        '8' => 'สิงหาคม',
        '9' => 'กันยายน',
        '10' => 'ตุลาคม',
        '11' => 'พฤศจิกายน',
        '12' => 'ธันวาคม'
    ];

    $currentMonth = date('n');
    $months = [
        $monthNames[($currentMonth - 3 + 12) % 12],
        $monthNames[($currentMonth - 2 + 12) % 12],
        $monthNames[($currentMonth - 1 + 12) % 12],
        $monthNames[$currentMonth]
    ];

    // สร้างตาราง HTML
    echo "<table border='1'>";
    echo "<tr>
            <th>วันที่</th>
            <th>หมายเลขห้อง</th>
            <th>หมายเลขเครื่องมิเตอร์</th>
            <th>{$months[0]}</th>
            <th>{$months[1]}</th>
            <th>{$months[2]}</th>
            <th>{$months[3]}</th>
            <th>จำนวนหน่วย</th>
          </tr>";
    while ($row = $result->fetch_assoc()) {
        if ($row['DatePoint'] == '0000-00-00 00:00:00') {
            $formattedDate = '00/00/0000 00:00';
        } else {
            $formattedDate = date('d-m-', strtotime($row['DatePoint'])) . (date('Y', strtotime($row['DatePoint'])) + 543) . ' ' . date('H:i:s', strtotime($row['DatePoint']));
        }
        
        echo "<tr>
                <td>{$formattedDate}</td>
                <td>{$row['Roomno']}</td>
                <td>{$row['SN']}</td>
                <td>{$row['Meter09']}</td>
                <td>{$row['Meter10']}</td>
                <td>{$row['Meter11']}</td>
                <td>{$row['Meter12']}</td>
                <td>{$row['จำนวนหน่วย']}</td>
              </tr>";
    }
    echo "</table>";
    exit;
} else {
    die("ไม่พบข้อมูล");
}

$conn->close();
?>
