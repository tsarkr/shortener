<?php
if (!file_exists(__DIR__ . '/config.php')) {
    die('설정 파일이 없습니다. config.example.php를 참고하여 config.php 파일을 생성해주세요.');
}

$config = require_once __DIR__ . '/config.php';
$required_keys = ['database'];
$required_db_keys = ['host', 'dbname', 'username', 'password', 'charset'];

foreach ($required_keys as $key) {
    if (!isset($config[$key])) {
        die("필수 설정 '{$key}'가 없습니다.");
    }
}

foreach ($required_db_keys as $key) {
    if (!isset($config['database'][$key])) {
        die("필수 데이터베이스 설정 '{$key}'가 없습니다.");
    }
}

echo "설정 파일이 정상적으로 확인되었습니다.\n";