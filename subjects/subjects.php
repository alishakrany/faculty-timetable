<?php
require_once("../db_config.php");

$tableName = "subjects";

$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    subject_id INT AUTO_INCREMENT PRIMARY KEY,
    subject_name VARCHAR(255) NOT NULL,
    department_id INT NOT NULL,
    level_id INT NOT NULL,
    hours INT NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(department_id),
    FOREIGN KEY (level_id) REFERENCES levels(level_id)
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    echo "<p>تم إنشاء جدول المواد بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        // إضافة المادة
        $subject_name = $_POST['subject_name'];
        $department_id = $_POST['department_id'];
        $level_id = $_POST['level_id'];
        $hours = $_POST['hours'];

        $insertQuery = "
        INSERT INTO $tableName (subject_name, department_id, level_id, hours)
        VALUES ('$subject_name', $department_id, $level_id, $hours)
        ";
        
        $insertResult = mysqli_query($conn, $insertQuery);
        if ($insertResult) {
            echo "<p>تمت إضافة المادة بنجاح!</p>";
        } else {
            echo "<p>حدث خطأ أثناء إضافة المادة: " . mysqli_error($conn) . "</p>";
        }
    } elseif (isset($_POST['delete'])) {
        // حذف الجدول بالكامل
        $deleteQuery = "DROP TABLE $tableName";
        $deleteResult = mysqli_query($conn, $deleteQuery);
        if ($deleteResult) {
            echo "<p>تم حذف الجدول بنجاح!</p>";
        } else {
            echo "<p>حدث خطأ أثناء حذف الجدول: " . mysqli_error($conn) . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>نموذج البيانات</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
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

        select{
            padding: 10px;
            background-color: #f2f2f2;
            /* color: white; */
            cursor: pointer;
            border-collapse: collapse;
        }

        .delete{
            background-color: #CE2029;
        }

        .delete:hover{
            background-color: #B0171F;
        }

    </style>
</head>
<body>
    <h1>إدارة المواد</h1>
    <form method="post">
        <label for="subject_name">اسم المادة:</label>
        <input type="text" name="subject_name" required><br><br>

        <label for="department_id"> القسم:</label>

        <select name="department_id">
            <?php
            $departmentQuery = "SELECT * FROM departments";
            $departmentResult = mysqli_query($conn, $departmentQuery);
            if ($departmentResult && mysqli_num_rows($departmentResult) > 0) {
                while ($row = mysqli_fetch_assoc($departmentResult)) {
                    $department_id = $row['department_id'];
                    $department_name = $row['department_name'];
                    echo "<option value='$department_id'>$department_name</option>";
                }
            }
            ?>
        </select>

        <label for="level_id"> المستوى:</label>
        <select name="level_id">
            <?php
            $levelQuery = "SELECT * FROM levels";
            $levelResult = mysqli_query($conn, $levelQuery);
            if ($levelResult && mysqli_num_rows($levelResult) > 0) {
                while ($row = mysqli_fetch_assoc($levelResult)) {
                    $level_id = $row['level_id'];
                    $level_name = $row['level_name'];
                    echo "<option value='$level_id'>$level_name</option>";
                }
            }
            ?>
        </select>

        <label for="hours">عدد الساعات:</label>
        <input type="number" name="hours" required><br><br>

        <button type="submit" name="add">إضافة المادة</button>
    </form>
    <!-- <form method="post">
        <button type="submit" class="delete" name="delete">حذف الجدول </button>
    </form> -->

    <table>
        <thead>
            <tr>
                <th>رقم المادة</th>
                <th>اسم المادة</th>
                <th> رقم القسم</th>
                <th> القسم</th>
                <th> رقم الفرقة</th>
                <th> الفرقة</th>
                <th>عدد الساعات</th>
                <th>تعديل</th>
                <th>حذف</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // اختر الجدول إذا كان موجودًا
                $selectTableQuery = "SHOW TABLES LIKE '$tableName'";
                $selectTableResult = mysqli_query($conn, $selectTableQuery);
                if (mysqli_num_rows($selectTableResult) > 0) {
                    // الجدول موجود، قم بتنفيذ الاستعلام
                    $selectQuery = "SELECT subjects.subject_id, subjects.subject_name, subjects.hours, departments.department_id, departments.department_name, levels.level_id, levels.level_name
                    FROM $tableName
                    JOIN departments ON subjects.department_id = departments.department_id
                    JOIN levels ON subjects.level_id = levels.level_id";

                    $selectResult = mysqli_query($conn, $selectQuery);
                    if ($selectResult) {
                        while ($row = mysqli_fetch_assoc($selectResult)) {
                            echo "<tr>";
                            echo "<td>{$row['subject_id']}</td>";
                            echo "<td>{$row['subject_name']}</td>";
                            echo "<td>{$row['department_id']}</td>";
                            echo "<td>{$row['department_name']}</td>";
                            echo "<td>{$row['level_id']}</td>";
                            echo "<td>{$row['level_name']}</td>";
                            echo "<td>{$row['hours']}</td>";
                            echo "<td><a href='edit_subject.php?subject_id={$row['subject_id']}'>تعديل</a></td>";
                            echo "<td><a href='delete_subject.php?subject_id={$row['subject_id']}'>حذف</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<p>حدث خطأ أثناء استعلام قاعدة البيانات: " . mysqli_error($conn) . "</p>";
                    }
                } else {
                    // الجدول غير موجود
                    echo "<p>الجدول غير موجود!</p>";
                }
            ?>
        </tbody>
    </table>
</body>
</html>
