<?php
require_once("db_config.php");

session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$timetableId = $_GET['id'];
$userId = $_SESSION['member_id'];

$query = "SELECT * FROM timetable WHERE timetable_id = $timetableId";
$result = $conn->query($query);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "السجل غير موجود";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $member_course_id = $_POST["member_course_id"];
    $classroom_id = $_POST["classroom_id"];
    $session_id = $_POST["session_id"];

    $duplicateQuery = "SELECT *
                FROM timetable t
                INNER JOIN member_courses mc ON t.member_course_id = mc.member_course_id
                INNER JOIN subjects s ON mc.subject_id = s.subject_id
                INNER JOIN sections sec ON mc.section_id = sec.section_id
                WHERE (t.classroom_id = $classroom_id AND t.session_id = $session_id AND t.timetable_id != $timetableId)
                OR (t.session_id = $session_id AND s.department_id = (SELECT subject.department_id FROM subjects subject WHERE subject.subject_id = mc.subject_id) AND s.level_id = (SELECT subject.level_id FROM subjects subject WHERE subject.subject_id = mc.subject_id) AND t.timetable_id != $timetableId)";
    $duplicateResult = $conn->query($duplicateQuery);

    if ($duplicateResult->num_rows > 0) {
        echo "<p style='color: red;'>خطأ: تم اختيار الفترة والفرقة أو الفترة والقسم والمستوى مسبقًا</p>";
    } else {
        $updateQuery = "UPDATE timetable SET member_course_id = $member_course_id, classroom_id = $classroom_id, session_id = $session_id WHERE timetable_id = $timetableId";

        if ($conn->query($updateQuery) === TRUE) {
            header("Location: scheduling.php");
        } else {
            echo "حدث خطأ أثناء تحديث البيانات: " . $conn->error;
        }
    }
}

$memberCoursesQuery = "SELECT mc.member_course_id, s.subject_name 
                       FROM member_courses mc
                       INNER JOIN subjects s ON mc.subject_id = s.subject_id
                       WHERE mc.member_id = $userId";

$memberCoursesResult = $conn->query($memberCoursesQuery);

$classroomsQuery = "SELECT * FROM classrooms";
$classroomsResult = $conn->query($classroomsQuery);

$sessionsQuery = "SELECT * FROM sessions";
$sessionsResult = $conn->query($sessionsQuery);
?>

<!DOCTYPE html>
<html>
<head>
    <title>تعديل بيانات الجدول الزمني</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <h2>تعديل بيانات الجدول الزمني</h2>
    <form method="post" action="">
        <label for="member_course_id">المادة:</label>
        <select name="member_course_id" id="member_course_id">
            <?php
            if ($memberCoursesResult->num_rows > 0) {
                while ($courseRow = $memberCoursesResult->fetch_assoc()) {
                    $selected = ($courseRow["member_course_id"] == $row["member_course_id"]) ? "selected" : "";
                    echo "<option value='" . $courseRow["member_course_id"] . "' $selected>" . $courseRow["subject_name"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="classroom_id">القاعة:</label>
        <select name="classroom_id" id="classroom_id">
            <?php
            if ($classroomsResult->num_rows > 0) {
                while ($classroomRow = $classroomsResult->fetch_assoc()) {
                    $selected = ($classroomRow["classroom_id"] == $row["classroom_id"]) ? "selected" : "";
                    echo "<option value='" . $classroomRow["classroom_id"] . "' $selected>" . $classroomRow["classroom_name"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <label for="session_id">الفترة:</label>
        <select name="session_id" id="session_id">
            <?php
            if ($sessionsResult->num_rows > 0) {
                while ($sessionRow = $sessionsResult->fetch_assoc()) {
                    $selected = ($sessionRow["session_id"] == $row["session_id"]) ? "selected" : "";
                    echo "<option value='" . $sessionRow["session_id"] . "' $selected>" . $sessionRow["session_name"] . "</option>";
                }
            }
            ?>
        </select><br><br>

        <input type="submit" value="تعديل" name="submit">
    </form>

    <a style="background-color: red; color:white; font-size:20px" href="scheduling.php">العودة إلى الجدول الزمني</a>

</body>
</html>
