<?php
include('connect.php');
$conn = dbconnect();

// รับคำค้นหาจาก URL (ผ่าน GET method)
$search = isset($_GET['search']) ? $_GET['search'] : '';

// ถ้ามีคำค้นหา
if (!empty($search)) {
    // สร้างคำสั่ง SQL ค้นหาฟิลด์ที่ต้องการ (em_roomNo, em_meterID, em_addNumber, em_sum)
    $sql = "SELECT em_id, em_timestamp, em_roomNo, em_meterID, em_addNumber, em_sum 
            FROM electricity 
            WHERE em_roomNo LIKE ? OR em_meterID LIKE ? OR em_addNumber LIKE ? OR em_sum LIKE ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $searchParam = '%' . $search . '%';
        
        // ใช้ bind_param เพื่อป้องกัน SQL Injection
        $stmt->bind_param("ssss", $searchParam, $searchParam, $searchParam, $searchParam);

        $stmt->execute();
        $result = $stmt->get_result();

        // ถ้ามีข้อมูล
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);  // ส่งผลลัพธ์เป็น JSON
        } else {
            echo json_encode(['error' => 'ไม่พบข้อมูลที่ค้นหา']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'ไม่สามารถเตรียมคำสั่ง SQL ได้']);
    }
} else {
    // หากไม่มีคำค้นหา ให้ดึงข้อมูลทั้งหมด
    $sql = "SELECT em_id, em_timestamp, em_roomNo, em_meterID, em_addNumber, em_sum FROM electricity";
    $result = $conn->query($sql);

    $data = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        echo json_encode($data);
    } else {
        echo json_encode([]);
    }
}

$conn->close();
?>
