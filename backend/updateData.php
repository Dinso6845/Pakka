<?php
include('connect.php');
$conn = dbconnect();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $id = $data['em_id'];
    $roomNo = $data['em_roomNo'];
    $meterID = $data['em_meterID'];
    $addNumber = $data['em_addNumber'];
    
    $sum = $roomNo . '-' . $meterID;

    $sql = "UPDATE electricity 
            SET em_roomNo = ?, em_meterID = ?, em_addNumber = ?, em_sum = ?
            WHERE em_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $roomNo, $meterID, $addNumber, $sum, $id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'อัพเดทข้อมูลสำเร็จ']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการอัพเดทข้อมูล']);
    }

    $stmt->close();
    $conn->close();
    exit();
}
?>