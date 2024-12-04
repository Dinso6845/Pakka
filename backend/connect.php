<?php

function dbconnect()
{
    $conn = mysqli_connect("localhost", "root", "", "meter");
    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}
$conn = dbconnect();
?>
