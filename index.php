<!DOCTYPE html>
<html>
<head>
    <title>صفحة الأزرار</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            direction: rtl;
            margin-top: 50px;
        }

        .container {
            display: grid;
            grid-template-columns: repeat(1, 0.5fr);
            gap: 20px;
            justify-content: center;
            align-items: center;
            height: 70vh;
        }

        .button {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            font-size: 22px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <a class="button" href="departments.php">اضافة شعبة</a>
        <a class="button" href="levels.php">اضافة فرقة</a>
        <a class="button" href="faculty_members.php">اضافة عضو هيئة تدريس</a>
        <a class="button" href="sessions.php">الفترات</a>
        <a class="button" href="classrooms.php">اضافة قاعة</a>
        <a class="button" href="sections.php">اضافة سكشن</a>
        <a class="button" href="subjects/subjects.php">اضافة مادة</a>
        <a class="button" href="membercourses.php">توزيع المواد على الأعضاء</a>
        <a class="button" href="timetable.php">تسكين المواد</a>
    </div>
</body>
</html>
