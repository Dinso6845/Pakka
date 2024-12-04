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

    if (isset($inputData['qrcode']) && isset($inputData['addNumber'])) {
        $qrcode = trim($inputData['qrcode']);
        $addNumber = trim($inputData['addNumber']);

        if ($conn->connect_error) {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
            exit;
        } 

        $stmt = $conn->prepare("UPDATE electricity SET em_addNumber = ?, em_timestamp = now() WHERE em_sum = ?");
        $stmt->bind_param("ss", $addNumber, $qrcode);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
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
