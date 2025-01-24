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
    $sum = $Roomno . '-' . $SN;

    // เริ่มการทำงานของคำสั่ง SQL
    $conn->begin_transaction();

    try {
        // อัปเดตข้อมูลในตาราง electricity
        $sql1 = "UPDATE electricity 
                 SET Roomno = ?, SN = ?, em_month = ?
                 WHERE SN = ?";
        $stmt1 = $conn->prepare($sql1);
        if ($stmt1 === false) {
            throw new Exception('ไม่สามารถเตรียมคำสั่ง SQL ได้');
        }
        $stmt1->bind_param("ssss", $Roomno, $SN, $Month, $SN);
        if (!$stmt1->execute()) {
            throw new Exception('เกิดข้อผิดพลาดในการอัปเดตข้อมูลในตาราง electricity');
        }
        
        // อัปเดตข้อมูลในตาราง ewgreport
        $sql2 = "UPDATE ewgreport 
                 SET Roomno = ?, SN = ?
                 WHERE SN = ?";
        $stmt2 = $conn->prepare($sql2);
        if ($stmt2 === false) {
            throw new Exception('ไม่สามารถเตรียมคำสั่ง SQL ได้');
        }
        $stmt2->bind_param("sss", $Roomno, $SN, $SN);
        if (!$stmt2->execute()) {
            throw new Exception('เกิดข้อผิดพลาดในการอัปเดตข้อมูลในตาราง ewgreport');
        }

        // ถ้าทุกอย่างสำเร็จ ให้ commit การทำงาน
        $conn->commit();
        
        echo json_encode(['status' => 'success', 'message' => 'อัพเดทข้อมูลสำเร็จ']);
    } catch (Exception $e) {
        // ถ้ามีข้อผิดพลาดใดๆ เกิดขึ้น ให้ rollback การทำงานทั้งหมด
        $conn->rollback();
        
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // ปิดการเชื่อมต่อ
    $stmt1->close();
    $stmt2->close();
    $conn->close();
    exit();
}
?>
