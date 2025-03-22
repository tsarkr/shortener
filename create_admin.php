<?php
include 'database.php';

// 관리자 계정 정보
$username = '******';
$password = '********';
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

try {
    // 관리자 계정이 이미 존재하는지 확인
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $userExists = $stmt->fetchColumn();

    if ($userExists) {
        echo "이미 동일한 사용자명이 존재합니다.";
    } else {
        // 관리자 계정 추가 쿼리 실행
        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, is_approved, created_at) VALUES (:username, :password, 'admin', TRUE, NOW())");
        $stmt->execute(['username' => $username, 'password' => $hashedPassword]);

        echo "관리자 계정이 생성되었습니다.";
    }
} catch (PDOException $e) {
    echo "계정 생성 중 오류 발생: " . $e->getMessage();
}
?>