<?php
$student_id = @$_GET['student_id'];

$sql_result = $sql->run("
    SELECT `student_course`.`student_id`, SUM(`_course`.`credits`) AS `credits` FROM `student_course`
    LEFT JOIN (
        SELECT DISTINCT `course_data`.`course_id`, `course_data`.`credits` FROM `course_data`
    )
    AS `_course`
    ON `student_course`.`course_id` = `_course`.`course_id`
    WHERE `student_course`.`student_id` = '$student_id';
");

if ($sql_result == null || $sql_result->num_rows == 0) {

    return null;
}
    
$row = $sql_result->fetch_array();

echo json_encode($row['credits']);
?>