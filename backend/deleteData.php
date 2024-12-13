<?php
include('connect.php');
$conn = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // รับ ID จาก URL
    parse_str(file_get_contents("php://input"), $data);
    $SN = $data['SN'];

    // ลบข้อมูล
    $sql = "DELETE FROM electricity WHERE SN = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $SN);

    if ($stmt->execute()) {
        echo "ลบข้อมูลสำเร็จ";
    } else {
        echo "เกิดข้อผิดพลาดในการลบข้อมูล";
    }

    $stmt->close();
    $conn->close();
}
?>
