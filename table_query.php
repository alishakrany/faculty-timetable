<!DOCTYPE html>
<html>
<head>
  <title>استعلام الجدول الزمني</title>
  <style>
    /* أنماط CSS للتنسيق */
    table {
      border-collapse: collapse;
    }
    th, td {
      border: 1px solid black;
      padding: 8px;
    }
  </style>
</head>
<body>
<h1>استعلام الجدول الزمني</h1>

<form method="post" action="">
  <label for="department">القسم :</label>
  <select id="department" name="department">
      <?php
      require_once("db_config.php");

          // استعلام SQL لاسترداد بيانات الاقسام من جدول departments
          $departmentQuery = "SELECT * FROM departments";
          // تنفيذ الاستعلام
          $departmentResult = mysqli_query($conn, $departmentQuery);
          // عرض خيارات القسم في القائمة المنسدلة
          while ($row = mysqli_fetch_assoc($departmentResult)) {
              echo '<option value="' . $row['department_id'] . '">' . $row['department_name'] . '</option>';
          }
      ?>
  </select>

  <label for="level">الفرقة:</label>
  <select id="level" name="level">
    <?php
      // استعلام SQL لاسترداد بيانات الفرق من جدول levels
      $levelQuery = "SELECT * FROM levels";

      // تنفيذ الاستعلام
      $levelResult = mysqli_query($conn, $levelQuery);

      // عرض خيارات الفرق في القائمة المنسدلة
      while ($row = mysqli_fetch_assoc($levelResult)) {
        echo '<option value="' . $row['level_id'] . '">' . $row['level_name'] . '</option>';
      }
    ?>
  </select>

  <input type="submit" value="عرض الجدول الزمني">
</form>

<?php
require_once("db_config.php");

// الاستعلام وعرض الجدول الزمني
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // استعلام SQL لاسترداد الجدول الزمني بناءً على الفرقة والشعبة المحددة
  $timetableQuery = "SELECT subjects.subject_name, faculty_members.member_name, classrooms.classroom_name, sessions.day, sessions.session_name
                     FROM timetable
                     INNER JOIN member_courses ON timetable.member_course_id = member_courses.member_course_id
                     INNER JOIN subjects ON member_courses.subject_id = subjects.subject_id
                     INNER JOIN faculty_members ON member_courses.member_id = faculty_members.member_id
                     INNER JOIN classrooms ON timetable.classroom_id = classrooms.classroom_id
                     INNER JOIN sessions ON timetable.session_id = sessions.session_id
                     WHERE subjects.department_id = " . $_POST['department'] . " AND subjects.level_id = " . $_POST['level'] . "
                     ORDER BY sessions.day, sessions.session_id"; // ترتيب الجدول الزمني حسب اليوم والفترة

  // تنفيذ الاستعلام والحصول على النتائج
  $timetableResult = mysqli_query($conn, $timetableQuery);

  // التحقق من وجود بيانات
  if (mysqli_num_rows($timetableResult) > 0) {
    // إنشاء جدول الجدول الزمني
    echo '<h2>جدول الفرقة والشعبة المحددة:</h2>';
    echo '<table>';
    echo '<tr><th>اليوم</th><th>الفترة</th><th>a1</th><th>a2</th><th>a3</th><th>a4</th><th>a5</th></tr>';

    // متغير لتتبع اليوم الحالي والفترة الحالية
    $currentDay = null;
    $currentSession = null;

    // عرض البيانات في الجدول
    while ($row = mysqli_fetch_assoc($timetableResult)) {
      // التحقق من تغيير اليوم
      if ($currentDay !== $row['day']) {
        $currentDay = $row['day'];
        $currentSession = null; // إعادة تعيين الفترة لليوم الجديد
      }

      // التحقق من تغيير الفترة
      if ($currentSession !== $row['session_name']) {
        $currentSession = $row['session_name'];
        echo '<tr><td>' . $row['day'] . '</td><td>' . $row['session_name'] . '</td><td>' . $row['subject_name'] . '/' . $row['member_name'] . '/' . $row['classroom_name'] . '</td></tr>';
      } else {
        echo '<tr><td></td><td></td><td>' . $row['subject_name'] . '/' . $row['member_name'] . '/' . $row['classroom_name'] . '</td></tr>';
      }
    }

    echo '</table>';
  } else {
    echo '<p>لا يوجد بيانات متاحة للفرقة والشعبة المحددة.</p>';
  }
}
?>

</body>
</html>
