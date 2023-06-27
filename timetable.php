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
    $duplicateQuery = "SELECT * FROM timetable WHERE classroom_id = $classroom_id AND session_id = $session_id";
    $duplicateResult = $conn->query($duplicateQuery);

    if ($duplicateResult->num_rows > 0) {
        echo "
        <p style='color: red;'>
        خطأ: تم اختيار الفترة والقاعة مسبقًا
        </p>";
    } else {
        $insertQuery = "INSERT INTO timetable (member_course_id, classroom_id, session_id) VALUES ($member_course_id, $classroom_id, $session_id)";

        if ($conn->query($insertQuery) === TRUE) {
            echo "تمت إضافة البيانات بنجاح";
        } else {
            echo "حدث خطأ أثناء إضافة البيانات: " . $conn->error;
        }
    }
}

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
    </style>

</head>
<body>
    <h2>إدخال بيانات الجدول الزمني</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="member_course_id">المادة:</label>
        <select name="member_course_id" id="member_course_id">
            <?php
            if ($subjectsResult->num_rows > 0) {
                while ($row = $subjectsResult->fetch_assoc()) {
                    echo "<option value='" . $row["subject_id"] . "'>" . $row["subject_name"] . "</option>";
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


    <!-- <?php
// استعلامات إضافية لاسترداد معلومات المعيد والسكشن
$facultyMemberQuery = "SELECT member_id FROM member_courses WHERE member_course_id = $member_course_id";
$facultyMemberResult = $conn->query($facultyMemberQuery);
$facultyMemberId = ($facultyMemberResult->num_rows > 0) ? $facultyMemberResult->fetch_assoc()['member_id'] : '';

$sectionQuery = "SELECT section_name FROM sections WHERE section_id = $section_id";
$sectionResult = $conn->query($sectionQuery);
$sectionName = ($sectionResult->num_rows > 0) ? $sectionResult->fetch_assoc()['section_name'] : '';

$facultyMemberNameQuery = "SELECT member_name FROM faculty_members WHERE member_id = $facultyMemberId";
$facultyMemberNameResult = $conn->query($facultyMemberNameQuery);
$facultyMemberName = ($facultyMemberNameResult->num_rows > 0) ? $facultyMemberNameResult->fetch_assoc()['member_name'] : '';

echo "<tr>";
echo "<td>" . $subjectName . "</td>";
echo "<td>" . $classroomName . "</td>";
echo "<td>" . $sessionName . "</td>";
echo "<td>" . $facultyMemberName . "</td>";
echo "<td>" . $sectionName . "</td>";
echo "</tr>";
?> -->

    
</body>
</html>
