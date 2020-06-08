<?php
$student_id = @$_GET['student_id'];
$course_id = @$_GET['course_id'];

/*
  Check exist
*/
$sql_result = $sql->run("
    SELECT * FROM `student_course` WHERE `student_id` = '$student_id' AND `course_id` = $course_id;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫';

    echo json_encode($action_result);

    return;
}

if ($sql_result->num_rows == 0) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '不在該課程中';

    echo json_encode($action_result);

    return;
}

/*
  Find course data
*/
$sql_result = $sql->run("
    SELECT DISTINCT `course_data`.`course_id`, `course_data`.`credits`
    FROM `course_data`
    WHERE `course_data`.`course_id` = $course_id;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫';

    echo json_encode($action_result);

    return;
}

if ($sql_result->num_rows == 0) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '找不到該課程';

    echo json_encode($action_result);

    return;
}

$row = $sql_result->fetch_array();

$enroll_credits = $row['credits'];

/*
  Check reach max credits
*/
$sql_result = $sql->run("
    SELECT * FROM (
        SELECT `student_course`.`student_id`, SUM(`_course`.`credits`) AS `total_credits` FROM `student_course`
        LEFT JOIN (
            SELECT DISTINCT `course_data`.`course_id`, `course_data`.`credits` FROM `course_data`
        )
        AS `_course`
        ON `student_course`.`course_id` = `_course`.`course_id`
        WHERE `student_course`.`student_id` = '$student_id'
    )
    AS `_result`
    WHERE `_result`.`total_credits` - $enroll_credits < 9;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫';

    echo json_encode($action_result);

    return;
}

$row = $sql_result->fetch_array();

if (@$row['student_id'] != null && @$row['total_credits'] != null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '低於最小學分！悲慘的人生......';

    echo json_encode($action_result);

    return;
}

/*
  Insert course enroll data
*/
$sql_result = $sql->run("
    DELETE FROM `student_course` WHERE `student_id` = '$student_id' AND `course_id` = $course_id;
");

if ($sql_result == false) {
    
    $action_result['ok'] = false;
    $action_result['resultMessage'] = '退選失敗QAQ';

    echo json_encode($action_result);

    return;
}

$action_result['ok'] = true;
$action_result['resultMessage'] = '已退選! 恭喜!!!';

echo json_encode($action_result);
?>