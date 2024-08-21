<?php
session_start();
include 'database.php';

if (isset($_SESSION['admin_id'])) {
    // 로그아웃 시간 기록
    $stmt = $pdo->prepare("UPDATE login_logs SET logout_time = NOW() WHERE admin_id = :admin_id ORDER BY login_time DESC LIMIT 1");
    $stmt->execute(['admin_id' => $_SESSION['admin_id']]);
}

session_destroy();
header('Location: login.php');
exit;
?>