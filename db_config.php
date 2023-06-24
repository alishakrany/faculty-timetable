<?php
// معلومات اتصال قاعدة البيانات
$host = "localhost";
$username = "ali";
$password = "12345";
$database = "sb_timetable";

// إنشاء اتصال بقاعدة البيانات
$conn = mysqli_connect($host, $username, $password, $database);

// التحقق من نجاح الاتصال
if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}
?>
