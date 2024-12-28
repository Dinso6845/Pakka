<?php
include('connect.php');
$conn = dbconnect();
$search = isset($_GET['search']) ? $_GET['search'] : '';


// ถ้ามีคำค้นหา
if (!empty($search)) {
    $sql = "SELECT Roomno, SN, em_month
            FROM electricity 
            WHERE Roomno LIKE ? OR SN LIKE ? OR em_month LIKE ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $searchParam = '%' . $search . '%';
        
        // ใช้ bind_param เพื่อป้องกัน SQL Injection
        $stmt->bind_param("sss", $searchParam, $searchParam, $searchParam);

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
    $sql = "SELECT  DatePoint, Roomno, SN, em_month FROM electricity";
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
