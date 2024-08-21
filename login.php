<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 사용자 조회
    $stmt = $pdo->prepare("SELECT id, password, role, is_approved FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // 비밀번호 및 승인 상태 확인
    if ($user && password_verify($password, $user['password'])) {
        if ($user['is_approved']) {
            // 세션 설정
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];

            // 로그인 성공 후 역할에 따라 리디렉션
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");  // 일반 사용자용 페이지로 리디렉션
            }
            exit;
        } else {
            echo '계정이 아직 승인되지 않았습니다.';
        }
    } else {
        echo '사용자명 또는 비밀번호가 잘못되었습니다.';
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>로그인</h2>
        <form method="POST">
            <div class="form-group">
                <label for="username">사용자명</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">로그인</button>
        </form>
    </div>
</body>
</html>