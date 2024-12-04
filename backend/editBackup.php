<?php
include('connect.php');
$conn = dbconnect();

// รับคำค้นหาจาก URL (ผ่าน GET method)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// ถ้ามีคำค้นหา
if (!empty($search)) {
    $sql = "SELECT m.em_id, m.em_timestamp, m.em_roomNo, m.em_meterID, m.em_addNumber, m.em_addNumber1, m.em_addNumber2, m.em_addNumber3, e.em_addNumber AS em_addNumberElectricity, e.em_addNumber AS em_addNumberElectricity,
    (COALESCE(e.`em_addNumber`, 0) - COALESCE(m.`em_addNumber`, 0)) AS `difference`, (
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
               ) * 100 AS `percentage_change`
            FROM masterelectricity m
            LEFT JOIN electricity e ON m.em_id = e.em_id
            WHERE m.em_roomNo LIKE ? OR m.em_meterID LIKE ? OR m.em_addNumber LIKE ? OR m.em_addNumber1 LIKE ? OR m.em_addNumber2 LIKE ? OR m.em_addNumber3 LIKE ? OR e.em_addNumber LIKE ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $searchParam = '%' . $search . '%';
        
        // ใช้ bind_param เพื่อป้องกัน SQL Injection
        $stmt->bind_param("sssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);

        $stmt->execute();
        $result = $stmt->get_result();

        // ถ้ามีข้อมูล
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'ไม่พบข้อมูลที่ค้นหา']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'ไม่สามารถเตรียมคำสั่ง SQL ได้']);
    }
} else {
    // หากไม่มีคำค้นหา ให้ดึงข้อมูลทั้งหมด
    $sql = "SELECT m.em_id, m.em_timestamp, m.em_roomNo, m.em_meterID, m.em_addNumber, m.em_addNumber1, m.em_addNumber2, m.em_addNumber3, e.em_addNumber AS em_addNumberElectricity, e.em_addNumber AS em_addNumberElectricity,
    (COALESCE(e.`em_addNumber`, 0) - COALESCE(m.`em_addNumber`, 0)) AS `difference`,(
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
               ) * 100 AS `percentage_change`
        FROM masterelectricity m
        LEFT JOIN electricity e ON m.em_id = e.em_id";

    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // เช็คค่า percentage_change ถ้ามันน้อยกว่า 5% หรือมากกว่า 5%
            if (abs($row['percentage_change']) > 5) {
                $row['warning'] = "เตือน: อาจมีข้อผิดพลาด";
            }else {
                $row['warning'] = "ปกติ";
            }
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
}

$conn->close();
?>
