<?php
require_once("db_config.php");

// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}


$tableName = "classrooms";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    classroom_id INT AUTO_INCREMENT PRIMARY KEY,
    classroom_name NVARCHAR(255) NOT NULL
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    // echo "<p>تم إنشاء جدول القاعات الدراسية بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}

// التحقق من إرسال النموذج وإدخال البيانات في قاعدة البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $classroom_name = mysqli_real_escape_string($conn, $_POST['classroom_name']);

    // إجراء استعلام INSERT لإدخال البيانات في جدول القاعات الدراسية
    $insertQuery = "
    INSERT INTO $tableName (classroom_name)
    VALUES ('$classroom_name')
    ";

    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo "<p>تم إدخال القاعة الدراسية بنجاح!</p>";
    } else {
        echo "<p>حدث خطأ أثناء إدخال القاعة الدراسية: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>إضافة قاعة دراسية جديدة</title>
    <!-- <style>
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
     -->
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

    <?php include 'navbar.php'; ?>

    <h1>إضافة قاعة دراسية جديدة</h1>
    <form method="POST" action="">
        <div>
            <label for="classroom_name">اسم القاعة الدراسية:</label>
            <input type="text" name="classroom_name" id="classroom_name" required>
        </div>

        <button type="submit">إضافة القاعة الدراسية</button>
    </form>
</body>
</html>
