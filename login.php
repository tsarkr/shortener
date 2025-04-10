<?php
session_start();
include 'database.php';

// 오류 메시지 출력 활성화 (디버깅용)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // 데이터베이스에서 사용자 검색
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username LIMIT 1");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['is_approved']) {
                // 비밀번호 검증 성공 및 승인된 사용자 -> 세션 설정
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['role'];

                // 로그인 성공 후 리디렉션
                $redirectUrl = $_SESSION['redirect_after_login'] ?? 'admin_dashboard.php'; // 저장된 URL 또는 기본값
                unset($_SESSION['redirect_after_login']); // 세션에서 URL 제거
                header("Location: $redirectUrl");
                exit;
            } else {
                // 계정이 승인되지 않음
                $error_message = "계정이 아직 승인되지 않았습니다. 관리자의 승인을 기다려주세요.";
            }
        } else {
            // 사용자명 또는 비밀번호가 잘못된 경우
            $error_message = "사용자명 또는 비밀번호가 잘못되었습니다.";
        }
    } catch (PDOException $e) {
        die("데이터베이스 오류: " . $e->getMessage());
    }
} else {
    $error_message = "사용자명 또는 비밀번호를 입력하세요.";
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container {
            max-width: 400px;
            margin-top: 100px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">로그인</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="username">사용자명</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">로그인</button>
        </form>
        <p class="text-center mt-3">
            계정이 없으신가요? <a href="register.php">회원가입</a>
        </p>
    </div>
</body>
</html>