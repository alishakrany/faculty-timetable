<?php
require_once("db_config.php");

// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}


$tableName = "departments";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    department_id INT AUTO_INCREMENT PRIMARY KEY,
    department_name NVARCHAR(255) NOT NULL
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    // echo "<p>تم إنشاء جدول المواد بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}

// Check if the form is submitted and insert data if so
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $department_name = mysqli_real_escape_string($conn, $_POST['department_name']);
    
    // Execute INSERT INTO query to insert data into the table
    $insertQuery = "
    INSERT INTO $tableName (department_name)
    VALUES ('$department_name')
    ";

    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo "<p>تم إدخال بيانات القسم  بنجاح!</p>";
    } else {
        echo "<p>حدث خطأ أثناء إدخال بيانات القسم : " . mysqli_error($conn) . "</p>";
    }
}
?>

<!-- نموذج HTML لإدخال البيانات -->

<!DOCTYPE html>
<html>
<head>
    <title>إضافة قسم جديد</title>
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

    <h1>إضافة قسم جديد</h1>
    <form method="POST" action="">
        <div>
            <label for="department_name">اسم القسم:</label>
            <input type="text" name="department_name" id="department_name" required>
        </div>

        <button type="submit">إضافة القسم</button>
    </form>
</body>
</html>