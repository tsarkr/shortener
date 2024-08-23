<?php
include 'database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // 사용자 추가 (승인 대기 상태)
    $stmt = $pdo->prepare("INSERT INTO users (username, password, is_approved) VALUES (:username, :password, FALSE)");
    try {
        $stmt->execute(['username' => $username, 'password' => $hashedPassword]);
        $success = '회원가입이 완료되었습니다. 관리자의 승인을 기다려주세요.';
    } catch (PDOException $e) {
        $error = '회원가입에 실패했습니다: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
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
        <h2 class="text-center">회원가입</h2>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="username">사용자명</label>
                <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">비밀번호</label>
                <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">회원가입</button>
        </form>
        <p class="text-center mt-3">
            이미 계정이 있으신가요? <a href="login.php">로그인</a>
        </p>
    </div>
</body>
</html>