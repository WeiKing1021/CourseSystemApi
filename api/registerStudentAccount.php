<?php
$student_id = @$_GET['student_id'];
$student_name = @$_GET['student_name'];
$class_id = @$_GET['class_id'];

$sql_result = $sql->run("
    SELECT * FROM `student_data` WHERE `student_id` = '$student_id';
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '無法連線至資料庫';

    echo json_encode($action_result);
    return;
}

if ($sql_result->num_rows != 0) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '使用者已存在!!';

    echo json_encode($action_result);
    return;
}

$sql_result = $sql->run("
    INSERT INTO `student_data` VALUES ('$student_id', '$student_name', '$class_id');
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '又無法連線至資料庫';

    echo json_encode($action_result);
    return;
}

$sql_result = $sql->run("
    INSERT INTO `student_course`
    SELECT DISTINCT '$student_id', `course_id` AS `required_course_id`
    FROM `course_data` WHERE `given_class_id` = '$class_id' AND `required` = true;
");

if ($sql_result == null) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '最後還是無法連線至資料庫';

    echo json_encode($action_result);
    return;
}

if ($sql_result == false) {

    $action_result['ok'] = false;
    $action_result['resultMessage'] = '建立使用者失敗囉～';

    echo json_encode($action_result);
    return;
}

$action_result['ok'] = true;
$action_result['resultMessage'] = '建立成功, 有夠讚！';

echo json_encode($action_result);
?>