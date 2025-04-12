<?php
$config_file = __DIR__ . '/config.php';

if (!file_exists($config_file)) {
    die('설정 파일이 없습니다. config.php 파일을 생성해주세요.');
}

$config = require $config_file;  // require_once에서 require로 변경

if (!is_array($config) || !isset($config['database'])) {
    die('올바른 데이터베이스 설정이 없습니다.');
}

$db = $config['database'];

// 필수 설정값 확인
$required = ['host', 'dbname', 'username', 'password'];
foreach ($required as $field) {
    if (!isset($db[$field]) || empty($db[$field])) {
        die("필수 데이터베이스 설정 '{$field}'가 없거나 비어있습니다.");
    }
}

try {
    $dsn = sprintf(
        "mysql:host=%s;dbname=%s;charset=%s",
        $db['host'],
        $db['dbname'],
        $db['charset'] ?? 'utf8mb4'
    );

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ];

    $pdo = new PDO($dsn, $db['username'], $db['password'], $options);
} catch (PDOException $e) {
    die('데이터베이스 연결 실패: ' . $e->getMessage());
}
?>
