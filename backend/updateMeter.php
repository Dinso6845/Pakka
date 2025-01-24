<?php
include('connect.php');
$conn = dbconnect();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    date_default_timezone_set('Asia/Bangkok');

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); 
        echo json_encode(['status' => 'error', 'message' => 'Invalid HTTP method']);
        exit;
    }

    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['qrcode']) && isset($inputData['month'])) {
        $qrcode = trim($inputData['qrcode']);
        $month = trim($inputData['month']);

        if ($conn->connect_error) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
            exit;
        } 

        // เช็คว่า SN มีอยู่ในฐานข้อมูลหรือไม่
        $stmt = $conn->prepare("SELECT * FROM electricity WHERE SN = ?");
        $stmt->bind_param("s", $qrcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // ถ้า SN มีอยู่ในฐานข้อมูล ให้ทำการอัปเดต
            $stmt = $conn->prepare("UPDATE electricity SET em_month = ?, DatePoint = now() WHERE SN = ?");
            $stmt->bind_param("ss", $month, $qrcode);

            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
            }
        } else {
            // ถ้า SN ไม่มีในฐานข้อมูล
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'SN not found in database']);
        }

        $stmt->close();
    } else {
        http_response_code(400); 
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
    }

    $conn->close();
} catch (Exception $e) {
    http_response_code(500); 
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
