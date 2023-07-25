<!DOCTYPE html>
<html>
<head>
  <title>استعلام الجدول الزمني</title>
  <style>
    body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select {
            width: 200px;
            margin-bottom: 10px;
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
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // استعلام SQL لاسترداد الجدول الزمني بناءً على الفرقة والشعبة المحددة
      $timetableQuery = "SELECT subjects.subject_name, faculty_members.member_name, classrooms.classroom_name, sessions.day, sessions.session_name
      FROM timetable
      INNER JOIN member_courses ON timetable.member_course_id = member_courses.member_course_id
      INNER JOIN subjects ON member_courses.subject_id = subjects.subject_id
      INNER JOIN faculty_members ON member_courses.member_id = faculty_members.member_id
      INNER JOIN classrooms ON timetable.classroom_id = classrooms.classroom_id
      INNER JOIN sessions ON timetable.session_id = sessions.session_id
      WHERE subjects.department_id = " . $_POST['department'] . " AND subjects.level_id = " . $_POST['level'];

      // تنفيذ الاستعلام
      $timetableResult = mysqli_query($conn, $timetableQuery);

      // التحقق من وجود بيانات
      if (mysqli_num_rows($timetableResult) > 0) {
        echo '<h2>جدول الفرقة والشعبة المحددة:</h2>';
        echo '<table>';
        echo '<tr><th>المادة</th><th>عضو هيئة التدريس</th><th>القاعة</th><th>اليوم</th><th>الجلسة</th></tr>';

        // عرض البيانات في الجدول
        while ($row = mysqli_fetch_assoc($timetableResult)) {
          echo '<tr>';
          echo '<td>' . $row['subject_name'] . '</td>';
          echo '<td>' . $row['member_name'] . '</td>';
          echo '<td>' . $row['classroom_name'] . '</td>';
          echo '<td>' . $row['day'] . '</td>';
          echo '<td>' . $row['session_name'] . '</td>';
          echo '</tr>';
        }

        echo '</table>';
      } else {
        echo '<p>لا يوجد بيانات متاحة للفرقة والشعبة المحددة.</p>';
      }

    }
  ?>

------------------------------
</body>
</html>

///// last working code
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
// استعلام SQL لاسترداد الجدول الزمني بناءً على الفرقة والشعبة المحددة
// استعلام SQL لاسترداد الجدول الزمني بناءً على الفرقة والشعبة المحددة
$timetableQuery = "SELECT subjects.subject_name, faculty_members.member_name, classrooms.classroom_name, sessions.day, sessions.session_name
                   FROM timetable
                   INNER JOIN member_courses ON timetable.member_course_id = member_courses.member_course_id
                   INNER JOIN subjects ON member_courses.subject_id = subjects.subject_id
                   INNER JOIN faculty_members ON member_courses.member_id = faculty_members.member_id
                   INNER JOIN classrooms ON timetable.classroom_id = classrooms.classroom_id
                   INNER JOIN sessions ON timetable.session_id = sessions.session_id
                   WHERE subjects.department_id = " . $_POST['department'] . " AND subjects.level_id = " . $_POST['level'];

      // تنفيذ الاستعلام والحصول على النتائج
      $timetableResult = mysqli_query($conn, $timetableQuery);

      // التحقق من وجود بيانات
      if (mysqli_num_rows($timetableResult) > 0) {
        // إنشاء جدول الجدول الزمني
        echo '<h2>جدول الفرقة والشعبة المحددة:</h2>';
        echo '<table>';
        echo '<tr><th>اليوم</th><th>الفترة</th><th>a1</th><th>a2</th><th>a3</th><th>a4</th><th>a5</th></tr>';

        // عرض البيانات في الجدول
        while ($row = mysqli_fetch_assoc($timetableResult)) {
          echo '<tr>';
          echo '<td rowspan="5">' . $row['day'] . '</td>';
          echo '<td>' . $row['session_name'] . '</td>';
          echo '<td>' . $row['subject_name'] . '/' . $row['member_name'] . '/' . $row['classroom_name'] . '</td>';
          echo '</tr>';
          for ($i = 2; $i <= 5; $i++) {
            echo '<tr>';
            echo '<td>' . $row['session_name'] . '</td>';
            echo '<td></td>'; // خلية فارغة
            echo '</tr>';
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

=============================================
last working quiry
=============================================

SELECT 
    s.day AS Day,
    CONCAT(s.start_time, ' - ', s.end_time) AS Period,
    IFNULL(CONCAT(sub.subject_name, ' / ', fm.member_name, ' / ', c.classroom_name), 'لا يوجد') AS Course_Details
FROM
    sessions s
    LEFT JOIN timetable t ON s.session_id = t.session_id
    LEFT JOIN member_courses mc ON t.member_course_id = mc.member_course_id
    LEFT JOIN subjects sub ON mc.subject_id = sub.subject_id
    LEFT JOIN faculty_members fm ON mc.member_id = fm.member_id
    LEFT JOIN classrooms c ON t.classroom_id = c.classroom_id
ORDER BY
    FIELD(s.day, 'الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس');
    
===================================================