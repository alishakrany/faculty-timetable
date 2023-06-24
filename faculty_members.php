<?php
require_once("db_config.php");

$tableName = "faculty_members";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    member_id INT AUTO_INCREMENT PRIMARY KEY,
    member_name VARCHAR(255) NOT NULL,
    academic_degree VARCHAR(255) NOT NULL,
    join_date DATE,
    ranking INT,
    role VARCHAR(255)
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    echo "<p>تم إنشاء جدول أعضاء هيئة التدريس بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}

// Check if the form is submitted and insert data if so
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_name = mysqli_real_escape_string($conn, $_POST['member_name']);
    $academic_degree = mysqli_real_escape_string($conn, $_POST['academic_degree']);
    $join_date = mysqli_real_escape_string($conn, $_POST['join_date']);
    $ranking = mysqli_real_escape_string($conn, $_POST['ranking']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    // Execute INSERT INTO query to insert data into the table
    $insertQuery = "
    INSERT INTO $tableName (member_name, academic_degree, join_date, ranking, role)
    VALUES ('$member_name', '$academic_degree', '$join_date', '$ranking', '$role')
    ";

    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo "<p>تم إدخال البيانات بنجاح!</p>";
    } else {
        echo "<p>حدث خطأ أثناء إدخال البيانات: " . mysqli_error($conn) . "</p>";
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
    <h1>إضافة أعضاء هيئة التدريس</h1>
    <form method="POST" action="">
        <div>
            <label for="member_name">اسم العضو:</label>
            <input type="text" name="member_name" id="member_name" required>
        </div>

        <div>
            <label for="academic_degree">الدرجة العلمية:</label>
            <select name="academic_degree" id="academic_degree" required>
                <option value="استاذ">استاذ</option>
                <option value="استاذ مساعد">استاذ مساعد</option>
                <option value="مدرس">مدرس</option>
                <option value="معيد">معيد</option>
            </select>
        </div>

        <div>
            <label for="join_date">تاريخ الانضمام:</label>
            <input type="date" name="join_date" id="join_date">
        </div>

        <div>
            <label for="ranking">الترتيب حسب الأقدمية:</label>
            <input type="number" name="ranking" id="ranking">
        </div>

        <div>
            <label for="role">الدور:</label>
            <select name="role" id="role">
                <option value="استاذ المادة">استاذ المادة</option>
                <option value="معاون">معاون</option>
            </select>
        </div>

        <button type="submit">إرسال</button>
    </form>
</body>
</html>
