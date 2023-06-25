<?php
require_once("db_config.php");

// Path: login.php
$tableName = "users";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    member_id INT,
    FOREIGN KEY (member_id) REFERENCES faculty_members(member_id)
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    echo "<p>تم إنشاء جدول المستخدمين بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}



session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

// استقبال بيانات تسجيل الدخول من النموذج
$username = $_POST['username'];
$password = $_POST['password'];

// استعلام للتحقق من صحة معلومات تسجيل الدخول
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    // تسجيل الدخول ناجح

    // استرداد معرف العضو المقابل
    $row = mysqli_fetch_assoc($result);
    $memberId = $row['member_id'];

    // حفظ معرف العضو في الجلسة
    $_SESSION['member_id'] = $memberId;

    // توجيه المستخدم إلى صفحة اختيار الفترات المناسبة للمواد
    header("Location: timetable.php");
    exit();
} else {
    // تسجيل الدخول غير صحيح
    echo "اسم المستخدم أو كلمة المرور غير صحيحة.";
}
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>إضافة أعضاء هيئة التدريس</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
            direction: rtl;
        }

        form {
            display: inline-block;
            text-align: right;
            direction: rtl;
        }

        table {
            margin: 0 auto;
            border-collapse: collapse;
        }

        table td, table th {
            padding: 10px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input, select {
            width: 200px;
            padding: 5px;
            margin-bottom: 10px;
        }

        button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <h1>تسجيل الدخول</h1>


<form action="login.php" method="POST">
    <label for="username">اسم المستخدم:</label>
    <input type="text" name="username" id="username" required><br>

    <label for="password">كلمة المرور:</label>
    <input type="password" name="password" id="password" required><br>

    <input type="submit" value="تسجيل الدخول">
</form>


<!-- <a href="logout.php">تسجيل الخروج</a> -->

</body>
</html>

