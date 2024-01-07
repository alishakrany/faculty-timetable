<?php
require_once("db_config.php");
// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}


$tableName = "sections";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    section_id INT AUTO_INCREMENT PRIMARY KEY,
    section_name NVARCHAR(255) NOT NULL,
    department_id INT NOT NULL,
    level_id INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    FOREIGN KEY (level_id) REFERENCES levels(level_id)
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    // echo "<p>تم إنشاء جدول السكاشن بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}

// Check if the form is submitted and insert data if so
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $section_name = mysqli_real_escape_string($conn, $_POST['section_name']);
    $department_id = mysqli_real_escape_string($conn, $_POST['department_id']);
    $level_id = mysqli_real_escape_string($conn, $_POST['level_id']);

    // Execute INSERT INTO query to insert data into the table
    $insertQuery = "
    INSERT INTO $tableName (section_name, department_id, level_id)
    VALUES ('$section_name', '$department_id', '$level_id')
    ";

    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo "<p>تم إدخال السكشن بنجاح!</p>";
    } else {
        echo "<p>حدث خطأ أثناء إدخال السكشن: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>إضافة سكشن جديد</title>
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
    
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

    <?php include 'navbar.php'; ?>

    <h1>إضافة سكشن جديد</h1>
    <form method="POST" action="">
        <div>
            <label for="section_name">اسم السكشن:</label>
            <input type="text" name="section_name" id="section_name" required>
        </div>

        <div>
    <label for="department_id">القسم:</label>
    <select name="department_id" id="department_id" required>
        <?php
        $departmentQuery = "SELECT department_id, department_name FROM departments";
        $departmentResult = mysqli_query($conn, $departmentQuery);
        if (mysqli_num_rows($departmentResult) > 0) {
            while ($departmentRow = mysqli_fetch_assoc($departmentResult)) {
                $department_id = $departmentRow['department_id'];
                $department_name = $departmentRow['department_name'];
                echo "<option value='$department_id'>$department_name</option>";
            }
        }
        ?>
    </select>
</div>

<div>
    <label for="level_id">المستوى:</label>
    <select name="level_id" id="level_id" required>
        <?php
        $levelQuery = "SELECT level_id, level_name FROM levels";
        $levelResult = mysqli_query($conn, $levelQuery);
        if (mysqli_num_rows($levelResult) > 0) {
            while ($levelRow = mysqli_fetch_assoc($levelResult)) {
                $level_id = $levelRow['level_id'];
                $level_name = $levelRow['level_name'];
                echo "<option value='$level_id'>$level_name</option>";
            }
        }
        ?>
    </select>
</div>

        <button type="submit">إضافة السكشن</button>
    </form>
</body>
</html>
