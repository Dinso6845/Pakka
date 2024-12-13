<?php
include('connect.php');
$conn = dbconnect();

// รับคำค้นหาจาก URL (ผ่าน GET method)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// ดึงข้อมูลจาก 2 ตาราง electricity และ ewgreport
$sql = "SELECT DISTINCT
        e.em_timestamp,
        e.Roomno,
        e.SN,
        m.Meter09, 
        m.Meter10, 
        m.Meter11, 
        m.Meter12, 
        e.unit, 
        e.em_month AS MonthElectricity,
        (
            (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) - 
            (
                (
                    (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) + 
                    (COALESCE(m.Meter12, 0) - COALESCE(m.Meter11, 0)) + 
                    (COALESCE(m.Meter11, 0) - COALESCE(m.Meter10, 0)) + 
                    (COALESCE(m.Meter10, 0) - COALESCE(m.Meter09, 0))
                ) / 4
            )
        ) / 
        (
            (
                (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) + 
                (COALESCE(m.Meter12, 0) - COALESCE(m.Meter11, 0)) + 
                (COALESCE(m.Meter11, 0) - COALESCE(m.Meter10, 0)) + 
                (COALESCE(m.Meter10, 0) - COALESCE(m.Meter09, 0))
            ) / 4
        ) * 100 AS percentage_change
    FROM ewgreport m
    LEFT JOIN electricity e ON m.SN = e.SN
    WHERE e.Roomno LIKE ? OR e.SN LIKE ? OR m.Meter09 LIKE ? OR m.Meter10 LIKE ? OR m.Meter11 LIKE ? OR m.Meter12 LIKE ? OR e.em_month LIKE ?";

if ($stmt = $conn->prepare($sql)) {
    $searchParam = '%' . $search . '%';
    $stmt->bind_param("sssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Content-Type: application/vnd.ms-excel");
        $outputFileName = 'Electricity_Export_' . date('d-m-Y H:i:s') . '.xls';
        header("Content-Disposition: attachment; filename=\"$outputFileName\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        $monthNames = [
            '12' => 'ธันวาคม',
            '11' => 'พฤศจิกายน',
            '10' => 'ตุลาคม',
            '9'  => 'กันยายน',
            '8'  => 'สิงหาคม',
            '7'  => 'กรกฎาคม',
            '6'  => 'มิถุนายน',
            '5'  => 'พฤษภาคม',
            '4'  => 'เมษายน',
            '3'  => 'มีนาคม',
            '2'  => 'กุมภาพันธ์',
            '1'  => 'มกราคม'
        ];

        $currentMonth = date('n');

        $months = [
            'เดือนล่าสุด' => $monthNames[$currentMonth],
            'เดือน'        => $monthNames[$currentMonth - 1] ?? '',
            'เดือน 1'      => $monthNames[$currentMonth - 2] ?? '',
            'เดือน 2'      => $monthNames[$currentMonth - 3] ?? '',
            'เดือน 3'      => $monthNames[$currentMonth - 4] ?? '',
        ];

        echo "<table border='1'>";
        echo "<tr>
                <th>วันที่</th>
                <th>หมายเลขห้อง</th>
                <th>หมายเลขเครื่องมิเตอร์</th>
                <th>{$months['เดือน 3']}</th>
                <th>{$months['เดือน 2']}</th>
                <th>{$months['เดือน 1']}</th>
                <th>{$months['เดือน']}</th>
                <th>{$months['เดือนล่าสุด']}</th>
                <th>ผลต่าง</th>
                <th>การเปลี่ยนแปลง (%)</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            if ($row['em_timestamp'] == '0000-00-00 00:00:00') {
                $formattedDate = '00/00/0000 00:00';
            } else {
                $formattedDate = date('d-m-', strtotime($row['em_timestamp'])) . (date('Y', strtotime($row['em_timestamp'])) + 543) . ' ' . date('H:i:s', strtotime($row['em_timestamp']));
            }

            $percentage_change = $row['percentage_change'];
            $percentage_change_display = isset($percentage_change) ? number_format($percentage_change, 2) : '0.00';

            echo "<tr>
                    <td>{$formattedDate}</td>
                    <td>{$row['Roomno']}</td>
                    <td>{$row['SN']}</td>
                    <td>{$row['Meter09']}</td>
                    <td>{$row['Meter10']}</td>
                    <td>{$row['Meter11']}</td>
                    <td>{$row['Meter12']}</td>
                    <td>{$row['MonthElectricity']}</td>
                    <td>{$row['unit']}</td>
                    <td>{$percentage_change_display}%</td>
                  </tr>";
        }
        echo "</table>";
        exit;
    } else {
        die("ไม่พบข้อมูล");
    }

    $stmt->close();
} else {
    die("ไม่สามารถเตรียมคำสั่ง SQL ได้");
}

$conn->close();
?>
