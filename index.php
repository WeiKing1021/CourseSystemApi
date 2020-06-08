<?php
    // 初始化
    require_once $_SERVER['CONTEXT_DOCUMENT_ROOT'] . '/_initialize.php';

    // 取得網址
    $_tmp_url = urldecode($_SERVER["REQUEST_URI"]);

    $_get_pos = strrpos($_tmp_url, '?');
    if ($_get_pos != null) {
        $_tmp_url = substr($_tmp_url, 0, $_get_pos);
    }

    // 迴圈處理網址列
    $_tmp_url_count = 0;
    foreach (explode('/', $_tmp_url) as $num => $value) {

        if ($value == '') {

            continue;
        }

        $_URL[$_tmp_url_count] = $value;
        $_tmp_url_count++;
    }

    if (!isset($_URL)) {

        return;
    }

    $require_file = DOCUMENT_ROOT . '/api/' . join('/', $_URL) . '.php';

    if (!file_exists($require_file)) {

        return;
    }

    $sql = createQuery();

    if (!$sql->connect()) {

        return;
    }

    require_once $require_file;
?>