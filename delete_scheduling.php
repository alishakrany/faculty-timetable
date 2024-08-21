<?php
require_once("db_config.php");

session_start();

if (!isset($_SESSION['member_id'])) {
    header("Location: login.php");
    exit();
}

$timetableId = $_GET['id'];

$deleteQuery = "DELETE FROM timetable WHERE timetable_id = $timetableId";

if ($conn->query($deleteQuery) === TRUE) {
    header("Location: scheduling.php");
} else {
    echo "حدث خطأ أثناء حذف البيانات: " . $conn->error;
}
?>
