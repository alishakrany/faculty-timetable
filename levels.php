<?php
require_once("db_config.php");

// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}


$tableName = "levels";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    level_id INT AUTO_INCREMENT PRIMARY KEY,
    level_name NVARCHAR(255) NOT NULL
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    // echo "<p>تم إنشاء جدول الفرق الدراسية بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}

// التحقق من إرسال النموذج وإدخال البيانات في قاعدة البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $level_name = mysqli_real_escape_string($conn, $_POST['level_name']);

    // إجراء استعلام INSERT لإدخال البيانات في جدول الفرق الدراسية
    $insertQuery = "
    INSERT INTO $tableName (level_name)
    VALUES ('$level_name')
    ";

    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo "<p>تم إدخال الفرق الدراسية بنجاح!</p>";
    } else {
        echo "<p>حدث خطأ أثناء إدخال الفرق الدراسية: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>إضافة فرق دراسية جديدة</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        form {
            display: inline-block;
            text-align: right;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input {
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
    
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

    <?php include 'navbar.php'; ?>

    <h1>إضافة فرق دراسية جديدة</h1>
    <form method="POST" action="">
        <div>
            <label for="level_name">اسم الفرقة الدراسية:</label>
            <input type="text" name="level_name" id="level_name" required>
        </div>

        <button type="submit">إضافة الفرقة الدراسية</button>
    </form>
</body>
</html>
