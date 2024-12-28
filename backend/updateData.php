<?php
include('connect.php');
$conn = dbconnect();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['Roomno'], $data['SN'], $data['em_month'])) {
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    $Roomno = $data['Roomno'];
    $SN = $data['SN'];
    $Month = $data['em_month'];
    $sum = $Roomno . '-' . $Month;

    $sql = "UPDATE electricity 
            SET Roomno = ?, SN = ?, em_month = ?, em_sum = ? 
            WHERE SN = ?";
    
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถเตรียมคำสั่ง SQL ได้']);
        exit();
    }
    $stmt->bind_param("sssss", $Roomno, $SN, $Month, $sum, $SN);

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