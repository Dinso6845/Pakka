<?php
include('connect.php');
$conn = dbconnect();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = $data['em_id'];
    
    $fields = ['em_roomNo', 'em_meterID', 'em_addNumber', 'em_addNumber1', 'em_addNumber2', 'em_addNumber3'];
    $params = [];
    $types = "";
    $sql = "UPDATE masterelectricity SET ";

    foreach ($fields as $field) {
        if (isset($data[$field])) {
            $sql .= "$field = ?, ";
            $params[] = $data[$field];
            $types .= "s";
        }
    }

    // คำนวณค่า em_sum ถ้าจำเป็น
    if (empty($data['em_sum']) && isset($data['em_roomNo'], $data['em_meterID'])) {
        $sql .= "em_sum = ?, ";
        $params[] = $data['em_roomNo'] . '-' . $data['em_meterID'];
        $types .= "s";
    }

    $sql = rtrim($sql, ", ") . " WHERE em_id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    echo json_encode($stmt->execute() ? ['status' => 'success', 'message' => 'อัพเดทข้อมูลสำเร็จ'] : ['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล']);
    
    $stmt->close();
    $conn->close();
}
?>
