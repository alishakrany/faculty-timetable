<?php
// تأكد من إضافة كود الاتصال بقاعدة البيانات هنا
require_once("db_config.php");

$createTableQuery = "
CREATE TABLE IF NOT EXISTS timetable (
    timetable_id INT AUTO_INCREMENT PRIMARY KEY,
    member_course_id INT,
    classroom_id INT,
    session_id INT,
    FOREIGN KEY (member_course_id) REFERENCES member_courses(member_course_id),
    FOREIGN KEY (classroom_id) REFERENCES classrooms(classroom_id),
    FOREIGN KEY (session_id) REFERENCES sessions(session_id)
)";

$createTableResult = mysqli_query($conn, $createTableQuery);

if ($createTableResult) {
    echo "<p>تم إنشاء جدول الجدول الزمني بنجاح!</p>";
} else {
    echo "<p>حدث خطأ أثناء إنشاء الجدول: " . mysqli_error($conn) . "</p>";
}


// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}

// استعلام للحصول على معلومات المستخدم المسجل
$memberId = $_SESSION['member_id'];

// استعلام لاسترداد البيانات المرتبطة من الجداول الأخرى
$departmentsQuery = "SELECT * FROM departments";
$departmentsResult = $conn->query($departmentsQuery);

$levelsQuery = "SELECT * FROM levels";
$levelsResult = $conn->query($levelsQuery);

$facultyMembersQuery = "SELECT * FROM faculty_members";
$facultyMembersResult = $conn->query($facultyMembersQuery);

$subjectsQuery = "SELECT * FROM subjects";
$subjectsResult = $conn->query($subjectsQuery);

$sectionsQuery = "SELECT * FROM sections";
$sectionsResult = $conn->query($sectionsQuery);

$classroomsQuery = "SELECT * FROM classrooms";
$classroomsResult = $conn->query($classroomsQuery);

$sessionsQuery = "SELECT * FROM sessions";
$sessionsResult = $conn->query($sessionsQuery);

// إدخال البيانات في جدول "timetable" عند تقديم النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $member_course_id = $_POST["member_course_id"];
    $classroom_id = $_POST["classroom_id"];
    $session_id = $_POST["session_id"];

// التحقق من عدم تكرار قيم الفترة والقاعة معًا في الجدول
$duplicateQuery = "SELECT *
                FROM timetable t
                INNER JOIN member_courses mc ON t.member_course_id = mc.member_course_id
                INNER JOIN subjects s ON mc.subject_id = s.subject_id
                INNER JOIN sections sec ON mc.section_id = sec.section_id
                WHERE (t.classroom_id = $classroom_id AND t.session_id = $session_id)
                OR (t.session_id = $session_id AND s.department_id = (SELECT subject.department_id FROM subjects subject WHERE subject.subject_id = mc.subject_id) AND s.level_id = (SELECT subject.level_id FROM subjects subject WHERE subject.subject_id = mc.subject_id))";
$duplicateResult = $conn->query($duplicateQuery);
    
    if ($duplicateResult->num_rows > 0) {
        echo "<p style='color: red;'>خطأ: تم اختيار الفترة والفرقة أو الفترة والقسم والمستوى مسبقًا</p>";
    } else {
        $insertQuery = "INSERT INTO timetable (member_course_id, classroom_id, session_id) VALUES ($member_course_id, $classroom_id, $session_id)";
    
        if ($conn->query($insertQuery) === TRUE) {
            echo "تمت إضافة البيانات بنجاح";
        } else {
            echo "حدث خطأ أثناء إضافة البيانات: " . $conn->error;
        }
    }
    }



// استعلام لجلب البيانات المرتبطة من الجداول الأخرى
$recordsQuery = "
    SELECT fm.member_name, s.subject_name, sec.section_name, se.session_name, c.classroom_name
    FROM timetable t
    INNER JOIN member_courses mc ON t.member_course_id = mc.member_course_id
    INNER JOIN faculty_members fm ON mc.member_id = fm.member_id
    INNER JOIN subjects s ON mc.subject_id = s.subject_id
    INNER JOIN sections sec ON mc.section_id = sec.section_id
    INNER JOIN sessions se ON t.session_id = se.session_id
    INNER JOIN classrooms c ON t.classroom_id = c.classroom_id
";

$recordsResult = $conn->query($recordsQuery);
// echo "<table border='1' cellpadding='10'>";
// if ($recordsResult->num_rows > 0) {
//     while ($row = $recordsResult->fetch_assoc()) {
//         echo "<tr>";
//         echo "<td>" . $row['member_name'] . "</td>";
//         echo "<td>" . $row['subject_name'] . "</td>";
//         echo "<td>" . $row['section_name'] . "</td>";
//         echo "<td>" . $row['session_name'] . "</td>";
//         echo "<td>" . $row['classroom_name'] . "</td>";
//         echo "</tr>";
//     }
// } else {
//     echo "<tr><td colspan='5'>لا توجد سجلات محفوظة</td></tr>";
// }

// echo "</table>";


?>

<!DOCTYPE html>
<html>
<head>
    <title>إدخال بيانات الجدول الزمني</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        form {
            display: inline-block;
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select {
            width: 200px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        table th, table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
        }
    </style>

</head>
<body>
    <h2>إدخال بيانات الجدول الزمني</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="member_course_id">المادة:</label>
        <select name="member_course_id" id="member_course_id">
            <?php
            $facultyMemberId = $_SESSION['member_id']; // استرداد معرف عضو هيئة التدريس المسجل

            $memberCoursesQuery = "SELECT mc.member_course_id, s.subject_name 
                                   FROM member_courses mc
                                   INNER JOIN subjects s ON mc.subject_id = s.subject_id
                                   WHERE mc.member_id = $facultyMemberId";

            $memberCoursesResult = $conn->query($memberCoursesQuery);

            if ($memberCoursesResult->num_rows > 0) {
                while ($row = $memberCoursesResult->fetch_assoc()) {
                    echo "<option value='" . $row["member_course_id"] . "'>" . $row["subject_name"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="classroom_id">القاعة:</label>
        <select name="classroom_id" id="classroom_id">
            <?php
            if ($classroomsResult->num_rows > 0) {
                while ($row = $classroomsResult->fetch_assoc()) {
                    echo "<option value='" . $row["classroom_id"] . "'>" . $row["classroom_name"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="session_id">الفترة:</label>
        <select name="session_id" id="session_id">
            <?php
            if ($sessionsResult->num_rows > 0) {
                while ($row = $sessionsResult->fetch_assoc()) {
                    echo "<option value='" . $row["session_id"] . "'>" . $row["session_name"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <input type="submit" value="إضافة">
    </form>

    <a href="logout.php">تسجيل الخروج</a>

    <table border='1' cellpadding='10'>
        <tr>
            <th>اسم العضو</th>
            <th>اسم المادة</th>
            <th>اسم القسم</th>
            <th>اسم الفترة</th>
            <th>اسم القاعة</th>
        </tr>
        <?php
        if ($recordsResult->num_rows > 0) {
            while ($row = $recordsResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row['member_name'] . "</td>";
                echo "<td>" . $row['subject_name'] . "</td>";
                echo "<td>" . $row['section_name'] . "</td>";
                echo "<td>" . $row['session_name'] . "</td>";
                echo "<td>" . $row['classroom_name'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5'>لا توجد سجلات محفوظة</td></tr>";
        }
        ?>
    </table>

</body>
</html>
