<!-- navbar.php -->
<div class="navbar">
    <a class="logo" href="index.php">الصفحة الرئيسية</a>
    <div class="menu-toggle">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div class="nav-links">
        <a href="departments.php">اضافة شعبة</a>
        <a href="levels.php">اضافة فرقة</a>
        <a href="faculty_members.php">اضافة عضو هيئة تدريس</a>
        <a href="sessions.php">الفترات</a>
        <a href="classrooms.php">اضافة قاعة</a>
        <a href="sections.php">اضافة سكشن</a>
        <a href="subjects/subjects.php">اضافة مادة</a>
        <a href="membercourses.php">توزيع مواد الاعضاء</a>
        <a href="timetable.php">تسكين المواد</a>
        <a href="table_query.php">عرض الجدول</a>
        <!-- يمكنك إضافة المزيد من الروابط هنا -->
    </div>
</div>


<!-- جافاسكريبت لتحقيق عملية الطي والتوسيع لقائمة التنقل -->
<script>
    const menuToggle = document.querySelector('.menu-toggle');
    const navLinks = document.querySelector('.nav-links');

    menuToggle.addEventListener('click', () => {
        // إضافة/إزالة الفئة "active" لتفعيل/تعطيل العنصر النقر عليه
        menuToggle.classList.toggle('active');
        // إضافة/إزالة الفئة "active" لإظهار/إخفاء قائمة التنقل
        navLinks.classList.toggle('active');
    });
</script>