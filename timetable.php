<?php
// تأكد من إضافة كود الاتصال بقاعدة البيانات هنا
require_once("db_config.php");

// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}

// استعلام للحصول على معلومات المستخدم المسجل
$memberId = $_SESSION['member_id'];

// استعلام للتحقق من اختيار المستخدم الفترات بنجاح
// $checkSelectionQuery = "SELECT COUNT(*) as count FROM member_courses WHERE member_id = '$memberId' AND selected_periods IS NOT NULL";
// $checkSelectionResult = mysqli_query($conn, $checkSelectionQuery);
// $checkSelectionRow = mysqli_fetch_assoc($checkSelectionResult);
// $hasSelectedPeriods = $checkSelectionRow['count'] > 0;

$checkPreviousSelectionQuery = "SELECT COUNT(*) as count FROM member_courses WHERE member_id = (SELECT member_id FROM faculty_members ORDER BY ranking ASC LIMIT 1) AND selected_periods IS NULL";
$checkPreviousSelectionResult = mysqli_query($conn, $checkPreviousSelectionQuery);
$checkPreviousSelectionRow = mysqli_fetch_assoc($checkPreviousSelectionResult);
$previousMemberSelected = $checkPreviousSelectionRow['count'] === 0;








// جلب سجلات المستخدم المسجل من جدول member_courses
$userCoursesQuery = "SELECT * FROM member_courses WHERE member_id = '$memberId'";
$userCoursesResult = mysqli_query($conn, $userCoursesQuery);

// عملية إدخال البيانات في جدول timetable
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $memberCourseId = $_POST['member_course_id'];
    $classroomId = $_POST['classroom_id'];
    $sectionId = $_POST['section_id'];

    // تنفيذ استعلام إدخال البيانات في جدول timetable
    $insertQuery = "INSERT INTO timetable (member_course_id, classroom_id, section_id) VALUES ('$memberCourseId', '$classroomId', '$sectionId')";
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
        echo "<p>تم إدخال البيانات بنجاح في الجدول!</p>";
    } else {
        echo "<p>حدث خطأ أثناء إدخال البيانات في الجدول: " . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>تسجيل الفترات</title>
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
    <?php if (!$hasSelectedPeriods) : ?>
        <p>قم بتحديد الفترات الخاصة بك قبل إدخال البيانات في الجدول.</p>
    <?php endif; ?>

    <form method="POST" action="timetable.php">
        <label>اختر المادة:</label>
        <select name="member_course_id">
            <?php while ($row = mysqli_fetch_assoc($userCoursesResult)) : ?>
                <option value="<?php echo $row['member_course_id']; ?>"><?php echo $row['member_course_name']; ?></option>
            <?php endwhile; ?>
        </select>

        <br>

        <label>اختر القاعة:</label>
        <select name="classroom_id">
            <!-- اضافة خيارات القاعات من جدول classrooms -->
            <!-- تأكد من تنفيذ الاستعلام واستدعاء mysqli_fetch_assoc() في هذا المكان -->
        </select>

        <br>

        <label>اختر الفترة:</label>
        <select name="section_id">
            <!-- اضافة خيارات الفترات من جدول sections -->
            <!-- تأكد من تنفيذ الاستعلام واستدعاء mysqli_fetch_assoc() في هذا المكان -->
        </select>

        <br>

        <input type="submit" value="إرسال">
    </form>

    <br>

    <a href="logout.php">تسجيل الخروج</a>
</body>
</html>
