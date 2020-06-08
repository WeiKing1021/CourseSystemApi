<?php
$course_id = @$_GET['id'];

$sql_result = $sql->run("
    SELECT `course_data`.*, `course_time`.`week`, `course_time`.`session`, `_tmp`.`students`
    FROM `course_data` LEFT JOIN `course_time`
    ON `course_data`.`course_id` = `course_time`.`course_id`
    LEFT JOIN (
        SELECT `student_course`.`course_id`, COUNT(DISTINCT `student_course`.`student_id`) AS `students` FROM `student_course`
        GROUP BY `course_id`
    )
    AS `_tmp`
    ON `course_data`.`course_id` = `_tmp`.`course_id`
    WHERE `course_data`.`course_id` = $course_id;
");

if ($sql_result == null || $sql_result->num_rows == 0) {

    return null;
}

$row = $sql_result->fetch_array();

$result_data = array();

$result_data['id'] = $row['course_id'];
$result_data['name'] = $row['course_name'];
$result_data['credits'] = $row['credits'];
$result_data['required'] = $row['required'];
$result_data['maxStudents'] = $row['max_students'];
$result_data['teacherId'] = $row['teacher_id'];
$result_data['currentStudents'] = @$row['students'] != null ? $row['students'] : 0;

$given_classes = array();
array_push($given_classes, $row['given_class_id']);
$result_data['classId'] = $given_classes;

$course_times = array();
$course_time['week'] = $row['week'];
$course_time['session'] = $row['session'];
array_push($course_times, $course_time);
$result_data['time'] = $course_times;

for ($row_no = $sql_result->num_rows - 1; $row_no > 0; $row_no--) {

    $row = $sql_result->fetch_array();

    // Course combine class info
    $given_classes = $result_data['classId'];

    if (!in_array($row['given_class_id'], $given_classes)) {

        array_push($given_classes, $row['given_class_id']);
    }

    $result_data['classId'] = $given_classes;

    // Course combine time info
    $course_times = $result_data['time'];

    $course_time['week'] = $row['week'];
    $course_time['session'] = $row['session'];

    if (!in_array($course_time, $course_times)) {
        
        array_push($course_times, $course_time);
    }

    $result_data['time'] = $course_times;
}

echo json_encode($result_data);
?>