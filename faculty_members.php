<?php
require_once("db_config.php");

// التحقق من تسجيل الدخول
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php"); // إعادة توجيه المستخدم إلى صفحة تسجيل الدخول إذا لم يكن مسجل الدخول
    exit();
}


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
    // echo "<p>تم إنشاء جدول أعضاء هيئة التدريس بنجاح!</p>";
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



    // بعد عملية الإدخال في جدول faculty_members
    //اضافة بيانات تسجيل الدخول الخاصة بعضو الهيئة في جدول  users
if ($insertResult) {
    // إنشاء اسم مستخدم عشوائي
    $username = 'user_' . uniqid();

    // إنشاء كلمة مرور عشوائية
    $password = bin2hex(random_bytes(8));

    // إدخال بيانات المستخدم في جدول users
    $insertUserQuery = "
    INSERT INTO users (username, password, member_id)
    VALUES ('$username', '$password', LAST_INSERT_ID())
    ";

    $insertUserResult = mysqli_query($conn, $insertUserQuery);

    if ($insertUserResult) {
        echo "<p>تم إنشاء حساب المستخدم بنجاح!</p>";
        echo "<p>اسم المستخدم: $username</p>";
        echo "<p>كلمة المرور: $password</p>";
    } else {
        echo "<p>حدث خطأ أثناء إنشاء حساب المستخدم: " . mysqli_error($conn) . "</p>";
    }
} else {
    echo "<p>حدث خطأ أثناء إدخال البيانات: " . mysqli_error($conn) . "</p>";
}




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
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

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



    <h1>عرض أعضاء هيئة التدريس</h1>
    
    <?php
// استعلام SELECT لاسترجاع الأعضاء الموجودين
$selectQuery = "SELECT * FROM $tableName";
$result = mysqli_query($conn, $selectQuery);

if (mysqli_num_rows($result) > 0) {
    echo "<h2>الأعضاء الموجودين:</h2>";
    echo "<table>";
    echo "<tr><th>رقم العضو</th><th>اسم العضو</th><th>الدرجة العلمية</th><th>تاريخ الانضمام</th><th>الترتيب حسب الأقدمية</th><th>الدور</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['member_id'] . "</td>";
        echo "<td>" . $row['member_name'] . "</td>";
        echo "<td>" . $row['academic_degree'] . "</td>";
        echo "<td>" . $row['join_date'] . "</td>";
        echo "<td>" . $row['ranking'] . "</td>";
        echo "<td>" . $row['role'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";
} else {
    echo "<p>لا يوجد أعضاء موجودين حاليًا.</p>";
}

// إغلاق اتصال قاعدة البيانات
mysqli_close($conn);
?>
    

</body>
</html>
