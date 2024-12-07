<!-- <?php
include('connect.php');
$conn = dbconnect();

$query = "SELECT DISTINCT MONTH(em_timestamp) AS mm 
          FROM masterelectricity 
          WHERE em_timestamp IS NOT NULL AND MONTH(em_timestamp) = MONTH(NOW()) 
          LIMIT 1";
$result = $conn->query($query);

$data = [];
$currentMonth = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $currentMonth = $row['mm'];
}

// ส่งข้อมูลออกไปในรูป JSON
echo json_encode(['currentMonth' => $currentMonth]);

$conn->close();
?> -->
