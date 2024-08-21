<?php
$servername = 'localhost';
$username = 'root';
$password = '';
$dbname = 'timetable2';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// ضبط الترميز بعد الاتصال
$conn->set_charset('utf8mb4');
?>
