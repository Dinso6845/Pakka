<!-- <?php
include('connect.php'); 
$conn = dbconnect(); 

$query = "SELECT e.em_addNumber, e.em_timestamp, m.em_addNumber1, m.em_addNumber2, m.em_addNumber3, m.em_id
          FROM electricity e
          INNER JOIN masterelectricity m ON m.em_id = e.em_id";

$stmt = $conn->prepare($query);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($em_addNumber, $em_timestamp, $em_addNumber1, $em_addNumber2, $em_addNumber3, $em_id);

while ($stmt->fetch()) {
    
    $updateQuery = "UPDATE masterelectricity SET 
        em_addNumber3 = COALESCE(em_addNumber2, em_addNumber1, em_addNumber),
        em_addNumber2 = COALESCE(em_addNumber1, em_addNumber),
        em_addNumber1 = COALESCE(em_addNumber, em_addNumber),
        em_addNumber = ?,
        em_timestamp = ?
        WHERE em_id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("isi", $em_addNumber, $em_timestamp, $em_id);
    $updateStmt->execute();
}

// แสดงผลลัพธ์
echo json_encode(["status" => "success", "message" => "ข้อมูลถูกสำรองเรียบร้อยแล้ว"]);

$conn->close();
?> -->
