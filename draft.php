<?php
// استعلام للتحقق من اختيار المستخدم الفترات بنجاح
$checkSelectionQuery = "SELECT COUNT(*) as count FROM member_courses WHERE member_id = '$memberId' AND selected_session IS NOT NULL";
$checkSelectionResult = mysqli_query($conn, $checkSelectionQuery);
$checkSelectionRow = mysqli_fetch_assoc($checkSelectionResult);
$hasSelectedPeriods = $checkSelectionRow['count'] > 0;

if ($hasSelectedPeriods) {
    // استعلام لاستعراض السجلات المرتبطة بالمستخدم للسماح بتحديد الفترات والقاعة
    $memberCoursesQuery = "SELECT * FROM member_courses WHERE member_id = '$memberId'";
    $memberCoursesResult = mysqli_query($conn, $memberCoursesQuery);
    
    // عرض نموذج اختيار الفترات والقاعة
    while ($courseRow = mysqli_fetch_assoc($memberCoursesResult)) {
        $courseId = $courseRow['member_course_id'];
        $subjectId = $courseRow['subject_id'];
        $sectionId = $courseRow['section_id'];
        
        // استعلام لجلب بيانات المادة
        $subjectQuery = "SELECT * FROM subjects WHERE subject_id = '$subjectId'";
        $subjectResult = mysqli_query($conn, $subjectQuery);
        $subjectRow = mysqli_fetch_assoc($subjectResult);
        $subjectName = $subjectRow['subject_name'];
        
        // استعلام لجلب بيانات السكشن
        $sectionQuery = "SELECT * FROM sections WHERE section_id = '$sectionId'";
        $sectionResult = mysqli_query($conn, $sectionQuery);
        $sectionRow = mysqli_fetch_assoc($sectionResult);
        $sectionName = $sectionRow['section_name'];
        
        // عرض نموذج اختيار الفترات والقاعة لكل مادة
        echo "
            <h3>اختيار الفترات والقاعة للمادة: $subjectName - السكشن: $sectionName</h3>
            <form method='POST' action='timetable.php'>
                <input type='hidden' name='course_id' value='$courseId'>
                <label for='selected_session'>اختيار الفترة:</label>
                <select name='selected_session'>
                    <option value='Morning'>الصباحية</option>
                    <option value='Evening'>المسائية</option>
                </select>
                <label for='selected_classroom'>اختيار القاعة:</label>
                <input type='text' name='selected_classroom'>
                <input type='submit' value='حفظ'>
            </form>
            <hr>
        ";
    }
} else {
    echo "لا يمكنك اختيار الفترات والقاعة حتى تختار الفترات الخاصة بك.";
}








//////////////////////////////
عرض فقط المتاح

// استعلام لجلب الفترات والقاعات التي تم اختيارها بالفعل
$selectedPeriodsQuery = "SELECT selected_session, selected_classroom FROM timetable WHERE member_course_id IN (SELECT member_course_id FROM member_courses WHERE member_id = '$memberId')";
$selectedPeriodsResult = mysqli_query($conn, $selectedPeriodsQuery);
$selectedPeriods = array();

// تخزين الفترات والقاعات المختارة في مصفوفة
while ($row = mysqli_fetch_assoc($selectedPeriodsResult)) {
    $selectedPeriods[] = array(
        'session' => $row['selected_session'],
        'classroom' => $row['selected_classroom']
    );
}

// استعلام لجلب المواد المتاحة للاختيار
$availableCoursesQuery = "SELECT mc.member_course_id, mc.subject_id, mc.section_id, s.subject_name, se.section_name
                          FROM member_courses mc
                          INNER JOIN subjects s ON mc.subject_id = s.subject_id
                          INNER JOIN sections se ON mc.section_id = se.section_id
                          WHERE mc.member_id = '$memberId'";
$availableCoursesResult = mysqli_query($conn, $availableCoursesQuery);

// عرض نموذج اختيار الفترات والقاعة للمواد المتاحة
while ($courseRow = mysqli_fetch_assoc($availableCoursesResult)) {
    $courseId = $courseRow['member_course_id'];
    $subjectId = $courseRow['subject_id'];
    $sectionId = $courseRow['section_id'];

    // التحقق مما إذا كانت الفترة والقاعة تم اختيارها بالفعل
    $isAlreadySelected = false;
    foreach ($selectedPeriods as $period) {
        if ($period['course_id'] == $courseId) {
            $isAlreadySelected = true;
            break;
        }
    }

    if (!$isAlreadySelected) {
        $subjectName = $courseRow['subject_name'];
        $sectionName = $courseRow['section_name'];

        // عرض نموذج اختيار الفترات والقاعة للمادة
        echo "
            <h3>اختيار الفترات والقاعة للمادة: $subjectName - السكشن: $sectionName</h3>
            <form method='POST' action='timetable.php'>
                <input type='hidden' name='course_id' value='$courseId'>
                <label for='selected_session'>اختيار الفترة:</label>
                <select name='selected_session'>
                    <option value='Morning'>الصباحية</option>
                    <option value='Evening'>المسائية</option>
                </select>
                <label for='selected_classroom'>اختيار القاعة:</label>
                <input type='text' name='selected_classroom'>
                <input type='submit' value='حفظ'>
            </form>
            <hr>
        ";
    }
}





$createTableQuery = "
CREATE TABLE IF NOT EXISTS timetable (
    timetable_id INT AUTO_INCREMENT PRIMARY KEY,
    member_course_id INT,
    classroom_id INT,
    section_id INT,
    UNIQUE KEY unique_member_course (member_course_id, classroom_id),
    FOREIGN KEY (member_course_id) REFERENCES member_courses(member_course_id),
    FOREIGN KEY (classroom_id) REFERENCES classrooms(classroom_id),
    FOREIGN KEY (section_id) REFERENCES sections(section_id)
)";


?>
