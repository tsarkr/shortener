<?php
$config = require_once __DIR__ . '/config.php';

$db = $config['database'];
$dsn = "mysql:host={$db['host']};dbname={$db['dbname']};charset={$db['charset']}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $db['username'], $db['password'], $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}
?>
