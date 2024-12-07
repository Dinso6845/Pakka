<?php
include('connect.php');
$conn = dbconnect();

// ดึงข้อมูลจาก 2 ตาราง electricity และ masterelectricity
$sql = "SELECT e.em_id, e.em_timestamp, e.em_roomNo, e.em_meterID, e.em_addNumber, 
               m.em_sum, m.em_addNumber AS master_addNumber, m.em_addNumber1, m.em_addNumber2, m.em_addNumber3, 
               (COALESCE(e.`em_addNumber`, 0) - COALESCE(m.`em_addNumber`, 0)) AS `difference`,(
    (
        (COALESCE(e.em_addNumber, 0) - COALESCE(m.em_addNumber, 0)) - 
        (
            (
                (COALESCE(e.em_addNumber, 0) - COALESCE(m.em_addNumber, 0)) + 
                (COALESCE(m.em_addNumber, 0) - COALESCE(m.em_addNumber1, 0)) + 
                (COALESCE(m.em_addNumber1, 0) - COALESCE(m.em_addNumber2, 0)) + 
                (COALESCE(m.em_addNumber2, 0) - COALESCE(m.em_addNumber3, 0))
            ) / 4
        )
    ) / 
    (
        (
            (COALESCE(e.em_addNumber, 0) - COALESCE(m.em_addNumber, 0)) + 
            (COALESCE(m.em_addNumber, 0) - COALESCE(m.em_addNumber1, 0)) + 
            (COALESCE(m.em_addNumber1, 0) - COALESCE(m.em_addNumber2, 0)) + 
            (COALESCE(m.em_addNumber2, 0) - COALESCE(m.em_addNumber3, 0))
        ) / 4
    )
) * 100 AS percentage_change
        FROM electricity e
        LEFT JOIN masterelectricity m ON e.em_id = m.em_id
        ORDER BY e.em_id ASC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // ตั้งค่าหัวข้อของไฟล์ Excel
    header("Content-Type: application/vnd.ms-excel");
    $outputFileName = 'Electricity_Export_' . date('d-m-Y H:i:s') . '.xls';
    header("Content-Disposition: attachment; filename=\"$outputFileName\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    // ชื่อเดือนในภาษาไทย
    $months = [
        'เดือนล่าสุด' => 'ธันวาคม',
        'เดือน'        => 'พฤศจิกายน',
        'เดือน 1'      => 'ตุลาคม',
        'เดือน 2'      => 'กันยายน',
        'เดือน 3'      => 'สิงหาคม',
    ];

    // สร้างตาราง HTML สำหรับไฟล์ Excel
    echo "<table border='1'>";
    echo "<tr>
            <th>ลำดับ</th>
            <th>วันที่</th>
            <th>หมายเลขห้อง</th>
            <th>หมายเลขเครื่องมิเตอร์</th>
            <th>ยอดรวม</th>
            <th>{$months['เดือน 3']}</th>
            <th>{$months['เดือน 2']}</th>
            <th>{$months['เดือน 1']}</th>
            <th>{$months['เดือน']}</th>
            <th>{$months['เดือนล่าสุด']}</th>
            <th>ผลต่าง</th>
            <th>การเปลี่ยนแปลง (%)</th>
          </tr>";

    while ($row = $result->fetch_assoc()) {
        $formattedDate = date('d-m-Y H:i:s', strtotime($row['em_timestamp'])); // ฟอร์แมตวันที่ให้เป็น d-m-Y H:i:s
        $difference = $row['difference'];
        $percentage_change = $row['percentage_change'];

        // ตรวจสอบว่า percentage_change มีค่าหรือไม่
        $percentage_change_display = isset($percentage_change) ? number_format($percentage_change, 2) : '0.00';

        echo "<tr>
                <td>{$row['em_id']}</td>
                <td>{$formattedDate}</td>
                <td>{$row['em_roomNo']}</td>
                <td>{$row['em_meterID']}</td>
                <td>{$row['em_sum']}</td>
                <td>{$row['em_addNumber3']}</td>
                <td>{$row['em_addNumber2']}</td>
                <td>{$row['em_addNumber1']}</td>
                <td>{$row['master_addNumber']}</td>
                <td>{$row['em_addNumber']}</td>
                <td>{$difference}</td>
                <td>{$percentage_change_display}%</td>
              </tr>";
    }
    echo "</table>";
    exit;
} else {
    die("ไม่พบข้อมูล");
}

$conn->close();
?>
