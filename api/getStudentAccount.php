<?php
$student_id = @$_GET['student_id'];

$sql_result = $sql->run("
    SELECT * FROM `student_data` WHERE `student_id` = '$student_id';
");

if ($sql_result == null || $sql_result->num_rows == 0) {

    return null;
}

$result_data = array();

$row = $sql_result->fetch_array();

$result_data['uid'] = $row['student_id'];
$result_data['name'] = $row['student_name'];
$result_data['classId'] = $row['class_id'];

echo json_encode($result_data);
?>