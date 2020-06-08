<?php
// 網站網址
define('HTTP_HOST', $_SERVER['HTTP_HOST']);

// 路徑位置
define('DOCUMENT_ROOT', $_SERVER['CONTEXT_DOCUMENT_ROOT']); 

// 載入系統設定檔
include_once DOCUMENT_ROOT . '/_config.php';

// 開啟Session機制
session_start();

// 設定時區
date_default_timezone_set('Asia/Taipei');

// 載入資料庫元件
include_once DOCUMENT_ROOT . '/_mysqlQuery.php';

// 載入函式庫
include_once DOCUMENT_ROOT . '/_function.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');
?>