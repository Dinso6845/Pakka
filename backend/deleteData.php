<?php
include('connect.php');
$conn = dbconnect();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $Roomno = $data['Roomno'];
    $SN = $data['SN'];

    // ตรวจสอบว่ามีข้อมูลที่ต้องการลบในตาราง electricity
    $sql_check_electricity = "SELECT * FROM electricity WHERE Roomno = ? AND SN = ?";
    $stmt_check_electricity = $conn->prepare($sql_check_electricity);
    $stmt_check_electricity->bind_param("ss", $Roomno, $SN);
    $stmt_check_electricity->execute();
    $result_electricity = $stmt_check_electricity->get_result();

    // ตรวจสอบว่ามีข้อมูลที่ต้องการลบในตาราง ewgreport
    $sql_check_ewgreport = "SELECT * FROM ewgreport WHERE Roomno = ? AND SN = ?";
    $stmt_check_ewgreport = $conn->prepare($sql_check_ewgreport);
    $stmt_check_ewgreport->bind_param("ss", $Roomno, $SN);
    $stmt_check_ewgreport->execute();
    $result_ewgreport = $stmt_check_ewgreport->get_result();

    // ตรวจสอบว่ามีข้อมูลที่ตรงกับการลบในตารางทั้งสอง
    if ($result_electricity->num_rows > 0 || $result_ewgreport->num_rows > 0) {
        if ($result_ewgreport->num_rows > 0) {
            $sql_delete_ewgreport = "DELETE FROM ewgreport WHERE Roomno = ? AND SN = ?";
            $stmt_delete_ewgreport = $conn->prepare($sql_delete_ewgreport);
            $stmt_delete_ewgreport->bind_param("ss", $Roomno, $SN);
            $stmt_delete_ewgreport->execute();
            $stmt_delete_ewgreport->close();
        }

        // ลบข้อมูลจากตาราง electricity
        if ($result_electricity->num_rows > 0) {
            $sql_delete_electricity = "DELETE FROM electricity WHERE Roomno = ? AND SN = ?";
            $stmt_delete_electricity = $conn->prepare($sql_delete_electricity);
            $stmt_delete_electricity->bind_param("ss", $Roomno, $SN);
            if ($stmt_delete_electricity->execute()) {
                // echo "ลบข้อมูลจากทั้งสองตารางสำเร็จ";
            } else {
                echo "เกิดข้อผิดพลาดในการลบข้อมูลจากตาราง electricity";
            }
            $stmt_delete_electricity->close();
        }
    } else {
        echo "ไม่พบข้อมูลที่ต้องการลบในทั้งสองตาราง";
    }

    $stmt_check_electricity->close();
    $stmt_check_ewgreport->close();
    $conn->close();
}
?>
