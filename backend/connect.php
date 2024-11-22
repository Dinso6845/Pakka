<?php

function dbconnect()
{
    $conn = mysqli_connect("localhost", "root", "", "meter");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }
    return $conn;
}

dbconnect();
?>