<?php
include('connect.php');
$conn = dbconnect();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // $id = $data['em_id'];
    $Roomno = $data['Roomno'];
    $SN = $data['SN'];
    $Month = $data['em_month'];
    
    $sum = $Roomno . '-' . $Month;

    $sql = "UPDATE electricity 
            SET Roomno = ?, SN = ?, em_month = ?, em_sum = ?
            WHERE SN = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $Roomno, $SN, $Month, $SN, $sum);

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