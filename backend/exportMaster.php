<?php
include('connect.php');
$conn = dbconnect();

// รับคำค้นหาจาก URL (ผ่าน GET method)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// ดึงข้อมูลจาก 2 ตาราง electricity และ ewgreport
$sql = "SELECT
        e.DatePoint,
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
    WHERE e.Roomno LIKE ? OR e.SN LIKE ? OR m.Meter09 LIKE ? OR m.Meter10 LIKE ? OR m.Meter11 LIKE ? OR m.Meter12 LIKE ? OR e.em_month LIKE ?
    GROUP BY e.Roomno, e.SN";

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
            $monthNames[($currentMonth - 4 + 11) % 12 + 1],
            $monthNames[($currentMonth - 3 + 11) % 12 + 1],
            $monthNames[($currentMonth - 2 + 11) % 12 + 1],
            $monthNames[($currentMonth - 1 + 11) % 12 + 1],
            $monthNames[$currentMonth]
        ];

        echo "<table border='1'>";
        echo "<tr>
                <th>วันที่</th>
                <th>หมายเลขห้อง</th>
                <th>หมายเลขเครื่องมิเตอร์</th>
                <th>{$months[0]}</th>
                <th>{$months[1]}</th>
                <th>{$months[2]}</th>
                <th>{$months[3]}</th>
                <th>{$months[4]}</th>
                <th>จำนวนหน่วย</th>
                <th>การเปลี่ยนแปลง (%)</th>
              </tr>";

        while ($row = $result->fetch_assoc()) {
            if ($row['DatePoint'] == '0000-00-00 00:00:00') {
                $formattedDate = '00/00/0000 00:00';
            } else {
                $formattedDate = date('d-m-', strtotime($row['DatePoint'])) . (date('Y', strtotime($row['DatePoint'])) + 543) . ' ' . date('H:i:s', strtotime($row['DatePoint']));
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
