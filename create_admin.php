<?php
include 'database.php';

// 관리자 계정 정보
$username = 'id';
$password = 'password';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// 관리자 계정 추가 쿼리 실행
$stmt = $pdo->prepare("INSERT INTO users (username, password, role, is_approved, created_at) VALUES (:username, :password, 'admin', TRUE, NOW())");
$stmt->execute(['username' => $username, 'password' => $hashedPassword]);

//echo "관리자 계정이 생성되었습니다.";
?>