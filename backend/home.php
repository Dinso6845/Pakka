<?php
include('connect.php');
$conn = dbconnect();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'message' => 'Invalid HTTP method']);
        exit;
    }

    $inputData = json_decode(file_get_contents('php://input'), true);

    if (isset($inputData['qrcode']) && !empty($inputData['qrcode'])) {
        $qrcode = trim($inputData['qrcode']);

        if ($conn->connect_error) {
            echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
            exit;
        }

        $stmt = $conn->prepare("SELECT Roomno, SN FROM electricity WHERE SN = ?");
        $stmt->bind_param("s", $qrcode);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode([
                'status' => 'success',
                'data' => [
                    'Roomno' => $row['Roomno'],
                    'SN' => $row['SN']
                ]
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'No data found']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'QR code is required']);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>
