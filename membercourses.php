<?php
// إنشاء اتصال بقاعدة البيانات
require_once("db_config.php");


// إنشاء جدول الاختيارات إذا لم يكن موجوداً
$tableName = "member_courses";


$createTableQuery = "
CREATE TABLE IF NOT EXISTS $tableName (
    member_course_id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT NOT NULL,
    subject_id INT NOT NULL,
    section_id INT NOT NULL,
    FOREIGN KEY (member_id) REFERENCES faculty_members(member_id),
    FOREIGN KEY (subject_id) REFERENCES subjects(subject_id),
    FOREIGN KEY (section_id) REFERENCES sections(section_id)
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    echo "<p>تم إنشاء جدول مواد أعضاء هيئة التدريس بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}



// التحقق من إرسال النموذج وحفظ البيانات إذا تم الإرسال
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $subject_id = $_POST['subject_id'];
    $section_id = $_POST['section_id'];

    // إدخال البيانات في جدول الاختيارات
    $insertQuery = "
    INSERT INTO $tableName (member_id, subject_id, section_id)
    VALUES ('$member_id', '$subject_id', '$section_id')
    ";

    if ($conn->query($insertQuery) === TRUE) {
        echo "<p>تم حفظ الاختيارات بنجاح!</p>";
    } else {
        echo "<p>حدث خطأ أثناء حفظ الاختيارات: " . $conn->error . "</p>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>اختيار المواد والسكاشن</title>
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

        select {
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

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
    
    <link rel="stylesheet" type="text/css" href="style.css">

</head>
<body>

    <?php include 'navbar.php'; ?>

    <h1>اختيار المواد والسكاشن</h1>

    <form method="POST" action="">
        <div>
            <label for="member_id">اختر عضو هيئة التدريس:</label>
            <select name="member_id" id="member_id" required>
                <?php
                // استعلام لاسترداد قائمة أعضاء هيئة التدريس
                $membersQuery = "SELECT * FROM faculty_members";
                $membersResult = $conn->query($membersQuery);

                if ($membersResult->num_rows > 0) {
                    while ($row = $membersResult->fetch_assoc()) {
                        echo "<option value='" . $row['member_id'] . "'>" . $row['member_name'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div>
            <label for="subject_id">اختر المادة:</label>
            <select name="subject_id" id="subject_id" required>
                <?php
                // استعلام لاسترداد قائمة المواد
                $subjectsQuery = "SELECT * FROM subjects";
                $subjectsResult = $conn->query($subjectsQuery);

                if ($subjectsResult->num_rows > 0) {
                    while ($row = $subjectsResult->fetch_assoc()) {
                        echo "<option value='" . $row['subject_id'] . "'>" . $row['subject_name'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <div>
            <label for="section_id">اختر السكاشن:</label>
            <select name="section_id" id="section_id" required>
                <?php
                // استعلام لاسترداد قائمة السكاشن
                $sectionsQuery = "SELECT * FROM sections";
                $sectionsResult = $conn->query($sectionsQuery);

                if ($sectionsResult->num_rows > 0) {
                    while ($row = $sectionsResult->fetch_assoc()) {
                        echo "<option value='" . $row['section_id'] . "'>" . $row['section_name'] . "</option>";
                    }
                }
                ?>
            </select>
        </div>

        <button type="submit">حفظ الاختيارات</button>
    </form>

    <h2>السجلات المحفوظة:</h2>
    <table>
        <tr>
            <th>اسم العضو</th>
            <th>اسم المادة</th>
            <th>اسم السكاشن</th>
        </tr>
        <?php
        // استعلام لاسترداد السجلات المحفوظة
        $recordsQuery = "SELECT faculty_members.member_name, subjects.subject_name, sections.section_name FROM member_courses
                        INNER JOIN faculty_members ON member_courses.member_id = faculty_members.member_id
                        INNER JOIN subjects ON member_courses.subject_id = subjects.subject_id
                        INNER JOIN sections ON member_courses.section_id = sections.section_id";
        $recordsResult = $conn->query($recordsQuery);

        if ($recordsResult->num_rows > 0) {
            while ($row = $recordsResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['member_name'] . "</td>";
                echo "<td>" . $row['subject_name'] . "</td>";
                echo "<td>" . $row['section_name'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>لا توجد سجلات محفوظة</td></tr>";
        }
        ?>
    </table>
</body>
</html>
