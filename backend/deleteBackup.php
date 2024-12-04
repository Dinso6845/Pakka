<?php
include('connect.php');
$conn = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // รับ ID จาก URL
    parse_str(file_get_contents("php://input"), $data);
    $id = $data['id'];

    // ลบข้อมูล
    $sql = "DELETE FROM masterelectricity WHERE em_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "ลบข้อมูลสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูล";
    }

    $stmt->close();
    $conn->close();
}
?>
