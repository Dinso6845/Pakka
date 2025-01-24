<?php
include('connect.php');
$conn = dbconnect();

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql_update = "
    UPDATE electricity e
    JOIN ewgreport m ON e.SN = m.SN
    SET e.unit = (e.em_month - m.Meter12)
";
$conn->query($sql_update);

// ถ้ามีคำค้นหา
if (!empty($search)) {
    $sql = "SELECT DISTINCT
        e.DatePoint,
        e.Roomno,
        e.SN,
        m.Meter09, 
        m.Meter10, 
        m.Meter11, 
        m.Meter12, 
        e.unit, 
        e.em_month AS MonthElectricity,
        (
            (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) - 
            (
                (
                    (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) + 
                    (COALESCE(m.Meter12, 0) - COALESCE(m.Meter11, 0)) + 
                    (COALESCE(m.Meter11, 0) - COALESCE(m.Meter10, 0)) + 
                    (COALESCE(m.Meter10, 0) - COALESCE(m.Meter09, 0))
                ) / 4
            )
        ) / 
        (
            (
                (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) + 
                (COALESCE(m.Meter12, 0) - COALESCE(m.Meter11, 0)) + 
                (COALESCE(m.Meter11, 0) - COALESCE(m.Meter10, 0)) + 
                (COALESCE(m.Meter10, 0) - COALESCE(m.Meter09, 0))
            ) / 4
        ) * 100 AS percentage_change
    FROM ewgreport m
    LEFT JOIN electricity e ON m.SN = e.SN AND m.Roomno = e.Roomno
    WHERE e.Roomno LIKE ? OR e.SN LIKE ? OR m.Meter09 LIKE ? OR m.Meter10 LIKE ? OR m.Meter11 LIKE ? OR m.Meter12 LIKE ? OR e.em_month LIKE ?
    ";
    
    if ($stmt = $conn->prepare($sql)) {
        $searchParam = '%' . $search . '%';

        // ใช้ bind_param เพื่อป้องกัน SQL Injection
        $stmt->bind_param("sssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);

        $stmt->execute();
        $result = $stmt->get_result();

        // ถ้ามีข้อมูล
        if ($result->num_rows > 0) {
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            echo json_encode(['error' => 'ไม่พบข้อมูลที่ค้นหา']);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'ไม่สามารถเตรียมคำสั่ง SQL ได้']);
    }
} else {
    $sql = "SELECT DISTINCT
        e.DatePoint,
        e.Roomno,
        e.SN,
        m.Meter09, 
        m.Meter10, 
        m.Meter11, 
        m.Meter12, 
        e.unit, 
        e.em_month AS MonthElectricity,
        (
            (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) - 
            (
                (
                    (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) + 
                    (COALESCE(m.Meter12, 0) - COALESCE(m.Meter11, 0)) + 
                    (COALESCE(m.Meter11, 0) - COALESCE(m.Meter10, 0)) + 
                    (COALESCE(m.Meter10, 0) - COALESCE(m.Meter09, 0))
                ) / 4
            )
        ) / 
        (
            (
                (COALESCE(e.em_month, 0) - COALESCE(m.Meter12, 0)) + 
                (COALESCE(m.Meter12, 0) - COALESCE(m.Meter11, 0)) + 
                (COALESCE(m.Meter11, 0) - COALESCE(m.Meter10, 0)) + 
                (COALESCE(m.Meter10, 0) - COALESCE(m.Meter09, 0))
            ) / 4
        ) * 100 AS percentage_change
    FROM ewgreport m
    LEFT JOIN electricity e ON m.SN = e.SN AND m.Roomno = e.Roomno
    WHERE e.Roomno LIKE ? OR e.SN LIKE ? OR m.Meter09 LIKE ? OR m.Meter10 LIKE ? OR m.Meter11 LIKE ? OR m.Meter12 LIKE ? OR e.em_month LIKE ?";

    if ($stmt = $conn->prepare($sql)) {
        $searchParam = '%'; // ถ้าไม่มีคำค้นหาให้ค้นหาทุกอย่าง

        // ใช้ bind_param เพื่อป้องกัน SQL Injection
        $stmt->bind_param("sssssss", $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam);

        $stmt->execute();
        $result = $stmt->get_result();

        // ถ้ามีข้อมูล
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                if (abs($row['percentage_change']) > 20) {
                    $row['warning'] = "เตือน: อาจมีข้อผิดพลาด";
                } else {
                    $row['warning'] = "ปกติ";
                }
                $data[] = $row;
            }
            echo json_encode($data);
        } else {
            echo json_encode([]);
        }

        $stmt->close();
    } else {
        echo json_encode(['error' => 'ไม่สามารถเตรียมคำสั่ง SQL ได้']);
    }
}

$conn->close();
?>
