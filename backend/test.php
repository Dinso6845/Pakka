<?php
include('connect.php');
$conn = dbconnect();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); 

// ตรวจสอบว่าเป็น POST request หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $inputData = json_decode(file_get_contents('php://input'), true);

        if (isset($inputData['qrcode']) && !empty($inputData['qrcode'])) {
            $qrcode = trim($inputData['qrcode']);

            // เชื่อมต่อกับฐานข้อมูล
            if ($conn->connect_error) {
                echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
                exit;
            }

            // ถ้าเป็นการดึงข้อมูล (SELECT)
            if (isset($inputData['fetchData']) && $inputData['fetchData'] === true) {
                $stmt = $conn->prepare("SELECT em_roomNo, em_meterID FROM electricity WHERE em_sum = ?");
                $stmt->bind_param("s", $qrcode);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo json_encode([ 
                        'status' => 'success',
                        'data' => [
                            'em_roomNo' => $row['em_roomNo'] ?? '',
                            'em_meterID' => $row['em_meterID'] ?? ''
                        ]
                    ]);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No data found']);
                }
                $stmt->close();
            } 
            // ถ้าเป็นการอัปเดตข้อมูล (UPDATE)
            elseif (isset($inputData['addNumber']) && isset($inputData['timestamp'])) {
                $addNumber = $inputData['addNumber'];
                $timestamp = $inputData['timestamp'];

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                    // คำสั่ง SQL สำหรับการอัปเดต
                    $sql = "UPDATE `electricity` SET `em_addNumber` = :addNumber, `em_timestamp` = :timestamp WHERE `em_sum` = :qrcode";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':addNumber', $addNumber);
                    $stmt->bindParam(':timestamp', $timestamp);
                    $stmt->bindParam(':qrcode', $qrcode);

                    // เรียกใช้คำสั่ง SQL
                    if ($stmt->execute()) {
                        echo json_encode(['status' => 'success']);
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถบันทึกข้อมูลได้']);
                    }
                } catch (PDOException $e) {
                    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid data for update']);
            }

            $conn->close();
        } else {
            echo json_encode(['status' => 'error', 'message' => 'QR code is required']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid HTTP method']);
}
?>
