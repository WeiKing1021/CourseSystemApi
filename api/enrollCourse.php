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
    $action_result['resultMessage'] = '無法連接到資料庫1';

    echo json_encode($action_result);

    return;
}

if ($sql_result->num_rows != 0) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '已經在該課程中';

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
    $action_result['resultMessage'] = '無法連接到資料庫2';

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
  Check course time against
*/
$sql_result = $sql->run("
    SELECT DISTINCT `A`.`course_id` FROM (
        SELECT `course_time`.`course_id`, `course_time`.`week`, `course_time`.`session` FROM `student_course`
        LEFT JOIN `course_data`
        ON `student_course`.`course_id` = `course_data`.`course_id`
        LEFT JOIN `course_time`
        ON `student_course`.`course_id` = `course_time`.`course_id`
        WHERE `student_id` = '$student_id'
    )
    AS `A`
    INNER JOIN (
        SELECT * FROM `course_time` WHERE `course_id` = $course_id
    )
    AS `B`
    ON `A`.`week` = `B`.`week` AND `A`.`session` = `B`.`session`;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫3';

    echo json_encode($action_result);

    return;
}

if ($sql_result->num_rows != 0) {

    $row = $sql_result->fetch_array();

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '與課程(' . $row['course_id'] . ')之時間衝突';

    echo json_encode($action_result);

    return;
}

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
    WHERE `_result`.`total_credits` + $enroll_credits > 25;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫4';

    echo json_encode($action_result);

    return;
}

$row = $sql_result->fetch_array();

if (@$row['student_id'] != null && @$row['total_credits'] != null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '超出最大學分，快找帥氣助教幫忙!';

    echo json_encode($action_result);

    return;
}

/*
  Check full
*/
$sql_result = $sql->run("
    SELECT DISTINCT `course_data`.`course_id` FROM `course_data`
    LEFT JOIN (
        SELECT `student_course`.`course_id`, COUNT(DISTINCT `student_course`.`student_id`) AS `students` FROM `student_course`
        GROUP BY `course_id`
    )
    AS `_tmp`
    ON `course_data`.`course_id` = `_tmp`.`course_id`
    WHERE `course_data`.`course_id` = $course_id AND `_tmp`.`students` >= `course_data`.`max_students`;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫5';

    echo json_encode($action_result);

    return;
}

if ($sql_result->num_rows != 0) {

    $row = $sql_result->fetch_array();

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '課程人數已滿!';

    echo json_encode($action_result);

    return;
}


/*
  Insert course enroll data
*/
$sql_result = $sql->run("
    INSERT INTO `student_course` SELECT '$student_id', $course_id
    WHERE NOT EXISTS (
        SELECT * FROM `student_course`
        WHERE `student_course`.`student_id` = '$student_id' AND `student_course`.`course_id` = $course_id
    );
");

if ($sql_result == false) {
    
    $action_result['ok'] = false;
    $action_result['resultMessage'] = '加選失敗QAQ';

    echo json_encode($action_result);

    return;
}

$action_result['ok'] = true;
$action_result['resultMessage'] = '加選成功!';

echo json_encode($action_result);
?>