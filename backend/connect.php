<?php

function dbconnect()
{
    // ตั้งค่าการเชื่อมต่อฐานข้อมูล
    $servername = "localhost"; 
    $username = "root";
    $password = "";          
    $dbname = "meter";       

    // เชื่อมต่อกับฐานข้อมูล
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // ตรวจสอบการเชื่อมต่อ
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    return $conn; // ส่งคืนการเชื่อมต่อ
}

// เรียกใช้ฟังก์ชัน dbconnect
$conn = dbconnect();
?>
