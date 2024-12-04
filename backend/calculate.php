<?php
include('connect.php');
$conn = dbconnect();
// SQL query ที่คำนวณค่าเฉลี่ยและเปอร์เซ็นต์การเปลี่ยนแปลง
$sql = "SELECT e.`em_id`, 
               e.`em_timestamp`, 
               e.`em_roomNo`, 
               e.`em_meterID`, 
               e.`em_sum`, 
               e.`em_addNumber` AS `electricity_em_addNumber`, 
               m.`em_addNumber` AS `master_em_addNumber`,
               m.`em_addNumber1` AS `master_em_addNumber1`,
               m.`em_addNumber2` AS `master_em_addNumber2`,
               m.`em_addNumber3` AS `master_em_addNumber3`,
               (
                   e.`em_addNumber` + 
                   COALESCE(m.`em_addNumber`, 0) + 
                   COALESCE(m.`em_addNumber1`, 0) + 
                   COALESCE(m.`em_addNumber2`, 0) + 
                   COALESCE(m.`em_addNumber3`, 0)
               ) / 4 AS `average_electricity_usage`,
               (
                   (e.`em_addNumber` - 
                   (
                       e.`em_addNumber` + 
                       COALESCE(m.`em_addNumber`, 0) + 
                       COALESCE(m.`em_addNumber1`, 0) + 
                       COALESCE(m.`em_addNumber2`, 0) + 
                       COALESCE(m.`em_addNumber3`, 0)
                   ) / 4)
                   / 
                   (
                       e.`em_addNumber` + 
                       COALESCE(m.`em_addNumber`, 0) + 
                       COALESCE(m.`em_addNumber1`, 0) + 
                       COALESCE(m.`em_addNumber2`, 0) + 
                       COALESCE(m.`em_addNumber3`, 0)
                   ) / 4
               ) * 100 AS `percentage_change`,

               (e.`em_addNumber` - COALESCE(m.`em_addNumber`, 0)) AS `difference`

        FROM `electricity` e
        LEFT JOIN `masterelectricity` m ON e.`em_id` = m.`em_id`
        ORDER BY e.`em_timestamp` ASC";

// Execute the query
$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์
if ($result->num_rows > 0) {
    // เริ่มแสดงผลข้อมูล
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["em_id"] . "<br>";
        echo "Timestamp: " . $row["em_timestamp"] . "<br>";
        echo "Room No: " . $row["em_roomNo"] . "<br>";
        echo "Meter ID: " . $row["em_meterID"] . "<br>";
        echo "Sum: " . $row["em_sum"] . "<br>";
        echo "Electricity Add Number: " . $row["electricity_em_addNumber"] . "<br>";
        echo "Master Add Number: " . $row["master_em_addNumber"] . "<br>";
        echo "Master Add Number 1: " . $row["master_em_addNumber1"] . "<br>";
        echo "Master Add Number 2: " . $row["master_em_addNumber2"] . "<br>";
        echo "Master Add Number 3: " . $row["master_em_addNumber3"] . "<br>";
        echo "Average Electricity Usage: " . $row["average_electricity_usage"] . "<br>";
        echo "Percentage Change: " . $row["percentage_change"] . "%<br><br>";
        echo "Difference (Electricity vs Master): " . $row["difference"] . "<br><br>";
    }
} else {
    echo "No results found.";
}

$result = $conn->query($sql);

// ตรวจสอบผลลัพธ์
if ($result->num_rows > 0) {
    $data = [];
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    // ส่งข้อมูลในรูปแบบ JSON
    echo json_encode($data);
} else {
    echo json_encode(['error' => 'No results found.']);
}

$conn->close();
?>
