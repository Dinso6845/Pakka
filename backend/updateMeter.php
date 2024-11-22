<?php
include('connect.php');
$conn = dbconnect();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405); // Method Not Allowed
        echo json_encode(['status' => 'error', 'message' => 'Invalid HTTP method']);
        exit;
    }

    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['qrcode']) && isset($inputData['addNumber']) && isset($inputData['timestamp'])) {
        $qrcode = trim($inputData['qrcode']);
        $addNumber = trim($inputData['addNumber']);
        $timestamp = trim($inputData['timestamp']);

        if ($conn->connect_error) {
            http_response_code(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
            exit;
        }

        // SQL query to update data in the database
        $stmt = $conn->prepare("UPDATE electricity SET em_addNumber = ?, em_timestamp = ? WHERE em_sum = ?");
        $stmt->bind_param("sss", $addNumber, $timestamp, $qrcode); // bind the parameters

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Data updated successfully']);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['status' => 'error', 'message' => 'Failed to update data']);
        }

        $stmt->close();
    } else {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
    }

    $conn->close();
} catch (Exception $e) {
    http_response_code(500); // Internal Server Error
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
