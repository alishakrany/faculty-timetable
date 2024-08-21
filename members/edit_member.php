<?php
require_once("../db_config.php");
session_start();

// التحقق من وجود معرف الجلسة للمستخدم المسجل
if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

// التحقق من وجود معرف العضو في العنوان
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$member_id = $_GET['id'];

// جلب بيانات العضو من قاعدة البيانات
$query = "SELECT fm.*, u.username, u.password FROM faculty_members fm LEFT JOIN users u ON fm.member_id = u.member_id WHERE fm.member_id = '$member_id'";
$result = mysqli_query($conn, $query);

if (mysqli_num_rows($result) == 1) {
    $row = mysqli_fetch_assoc($result);
    $member_name = $row['member_name'];
    $academic_degree = $row['academic_degree'];
    $join_date = $row['join_date'];
    $ranking = $row['ranking'];
    $role = $row['role'];
    $username = $row['username'];
    $hashed_password = $row['password'];
} else {
    header("Location: index.php");
    exit();
}

// التحقق من إرسال النموذج وتحديث البيانات في قاعدة البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_name = mysqli_real_escape_string($conn, $_POST['member_name']);
    $academic_degree = mysqli_real_escape_string($conn, $_POST['academic_degree']);
    $join_date = mysqli_real_escape_string($conn, $_POST['join_date']);
    $ranking = mysqli_real_escape_string($conn, $_POST['ranking']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $hashed_password;

    // تحديث بيانات العضو في جدول faculty_members
    $updateQuery = "
    UPDATE faculty_members SET
    member_name = '$member_name',
    academic_degree = '$academic_degree',
    join_date = '$join_date',
    ranking = '$ranking',
    role = '$role'
    WHERE member_id = '$member_id'
    ";

    $updateResult = mysqli_query($conn, $updateQuery);

    // تحديث بيانات المستخدم في جدول users
    $updateUserQuery = "
    UPDATE users SET
    username = '$username',
    password = '$password'
    WHERE member_id = '$member_id'
    ";

    $updateUserResult = mysqli_query($conn, $updateUserQuery);

    if ($updateResult && $updateUserResult) {
        $_SESSION['message'] = "تم تحديث بيانات العضو بنجاح!";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "حدث خطأ أثناء تحديث البيانات: " . mysqli_error($conn);
        $_SESSION['message_type'] = "error";
    }

    header("Location: faculty_members.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>تعديل بيانات عضو هيئة التدريس</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>

    <?php include 'navbar.php'; ?>

    <h1>تعديل بيانات عضو هيئة التدريس</h1>
    <form method="POST" action="" class="form-container">
        <div class="form-group">
            <label for="member_name">اسم العضو:</label>
            <input type="text" name="member_name" id="member_name" class="form-control" value="<?php echo $member_name; ?>" required>
        </div>
        <div class="form-group">
            <label for="academic_degree">الدرجة العلمية:</label>
            <select name="academic_degree" id="academic_degree" class="form-control" required>
                <option value="استاذ" <?php if ($academic_degree == "استاذ") echo "selected"; ?>>استاذ</option>
                <option value="استاذ مساعد" <?php if ($academic_degree == "استاذ مساعد") echo "selected"; ?>>استاذ مساعد</option>
                <option value="مدرس" <?php if ($academic_degree == "مدرس") echo "selected"; ?>>مدرس</option>
                <option value="معيد" <?php if ($academic_degree == "معيد") echo "selected"; ?>>معيد</option>
            </select>
        </div>
        <div class="form-group">
            <label for="join_date">تاريخ الانضمام:</label>
            <input type="date" name="join_date" id="join_date" class="form-control" value="<?php echo $join_date; ?>">
        </div>
        <div class="form-group">
            <label for="ranking">الترتيب حسب الأقدمية:</label>
            <input type="number" name="ranking" id="ranking" class="form-control" value="<?php echo $ranking; ?>">
        </div>
        <div class="form-group">
            <label for="role">الدور:</label>
            <select name="role" id="role" class="form-control">
                <option value="استاذ المادة" <?php if ($role == "استاذ المادة") echo "selected"; ?>>استاذ المادة</option>
                <option value="معاون" <?php if ($role == "معاون") echo "selected"; ?>>معاون</option>
            </select>
        </div>
        <div class="form-group">
            <label for="username">اسم المستخدم:</label>
            <input type="text" name="username" id="username" class="form-control" value="<?php echo $username; ?>" required>
        </div>
        <div class="form-group">
            <label for="password">كلمة المرور (اتركها فارغة إذا كنت لا تريد تغييرها):</label>
            <input type="password" name="password" id="password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">تحديث</button>
    </form>

    <?php if ($message): ?>
    <div class="alert mt-3 <?php echo $message_type === 'error' ? 'alert-danger' : 'alert-success'; ?>">
        <?php echo $message; ?>
    </div>
    <?php endif; ?>

</body>
</html>
