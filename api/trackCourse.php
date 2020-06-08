<?php
$student_id = @$_GET['student_id'];
$course_id = @$_GET['course_id'];

/*
  Check exist
*/
$sql_result = $sql->run("
    SELECT * FROM `student_track` WHERE `student_id` = '$student_id' AND `course_id` = $course_id;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連接到資料庫';

    echo json_encode($action_result);

    return;
}

if ($sql_result->num_rows != 0) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '已經追縱過該課程';

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

/*
  Insert course track data
*/
$sql_result = $sql->run("
    INSERT INTO `student_track` SELECT '$student_id', $course_id
    WHERE NOT EXISTS (
        SELECT * FROM `student_track`
        WHERE `student_track`.`student_id` = '$student_id' AND `student_track`.`course_id` = $course_id
    );
");

if ($sql_result == false) {
    
    $action_result['ok'] = false;
    $action_result['resultMessage'] = '追蹤失敗QAQ';

    echo json_encode($action_result);

    return;
}

$action_result['ok'] = true;
$action_result['resultMessage'] = '追蹤成功!';

echo json_encode($action_result);
?>