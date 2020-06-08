<?php
$student_id = @$_GET['student_id'];

$sql_result = $sql->run("
    SELECT `student_track`.`student_id`, `course_data`.*, `week`, `session`, `_tmp`.`students` FROM `student_track`
    LEFT JOIN `course_data`
    ON `student_track`.`course_id` = `course_data`.`course_id`
    LEFT JOIN `course_time`
    ON `student_track`.`course_id` = `course_time`.`course_id`
    LEFT JOIN (
        SELECT `student_course`.`course_id`, COUNT(DISTINCT `student_course`.`student_id`) AS `students` FROM `student_course`
        GROUP BY `course_id`
    )
    AS `_tmp`
    ON `course_data`.`course_id` = `_tmp`.`course_id`
    WHERE `student_id` = '$student_id';
");

if ($sql_result == null || $sql_result->num_rows == 0) {

    return null;
}

$result_data = array();

for ($row_no = $sql_result->num_rows - 1; $row_no >= 0; $row_no--) {
    
    $row = $sql_result->fetch_array();

    // Course single info
    $course = @$result_data[$row['course_id']];

    if ($course == null) {

        $course = array();
        $course['id'] = $row['course_id'];
        $course['name'] = $row['course_name'];
        $course['credits'] = $row['credits'];
        $course['required'] = $row['required'];
        $course['maxStudents'] = $row['max_students'];
        $course['teacherId'] = $row['teacher_id'];
        $course['currentStudents'] = @$row['students'] != null ? $row['students'] : 0;
    }

    // Course combine class info
    $given_classes = @$course['classId'];

    if ($given_classes == null) {

        $given_classes = array();
    }

    if (!in_array($row['given_class_id'], $given_classes)) {

        array_push($given_classes, $row['given_class_id']);
    }

    $course['classId'] = $given_classes;

    // Course combine time info
    $course_times = @$course['time'];

    if ($course_times == null) {

        $course_times = array();
    }

    $course_time['week'] = $row['week'];
    $course_time['session'] = $row['session'];

    if (!in_array($course_time, $course_times)) {
        
        array_push($course_times, $course_time);
    }

    $course['time'] = $course_times;

    // Write back to result data
    $result_data[$row['course_id']] = $course;
}

echo json_encode(array_values($result_data));
?>