<?php
// การเชื่อมต่อฐานข้อมูล
include('connect.php');
$conn = dbconnect();

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ตรวจสอบว่าฟอร์มมีข้อมูลครบถ้วน
    if (isset($_POST['em_roomNo'], $_POST['em_meterID'])) {
        $em_roomNos = $_POST['em_roomNo'];
        $em_meterIDs = $_POST['em_meterID'];

        if (count($em_roomNos) == count($em_meterIDs)) {
            // ใช้ prepared statement เพื่อเพิ่มข้อมูล
            $stmt = $conn->prepare("INSERT INTO electricity (em_roomNo, em_meterID, em_sum) VALUES (?, ?, ?)");

            for ($i = 0; $i < count($em_roomNos); $i++) {
                $em_roomNo = $em_roomNos[$i];
                $em_meterID = $em_meterIDs[$i];
                $em_sum = $em_roomNo . '-' . $em_meterID;

                // Binding ข้อมูลและ execute คำสั่ง SQL
                $stmt->bind_param("sss", $em_roomNo, $em_meterID, $em_sum);
                if (!$stmt->execute()) {
                    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล!');</script>";
                    exit();
                }
            }

            // ปิดการเชื่อมต่อฐานข้อมูล
            $stmt->close();
            $conn->close();

            // แสดงข้อความสำเร็จและเปลี่ยนเส้นทาง
            echo "<script>
                    alert('เพิ่มข้อมูลสำเร็จ!');
                    window.location.href = '/Electricity/frontend/data.html';
                  </script>";
            exit();
        } else {
            echo "<script>
                    alert('ข้อมูลไม่ครบถ้วน! โปรดตรวจสอบข้อมูลอีกครั้ง');
                    window.history.back(); // กลับไปยังหน้าก่อนหน้า
                  </script>";
            exit();
        }
    } else {
        echo "<script>
                alert('ไม่มีข้อมูลที่ส่งมา!');
                window.history.back();
              </script>";
        exit();
    }
}

// ปิดการเชื่อมต่อหากไม่ได้ใช้งาน
$conn->close();
?>
