<?php
session_start();
include 'database.php';

// 관리자 권한 확인
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 사용자 목록 가져오기
$stmt = $pdo->query("SELECT id, username, role, is_approved, created_at FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 승인 처리 또는 사용자 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve_user_id'])) {
        $userId = $_POST['approve_user_id'];
        $stmt = $pdo->prepare("UPDATE users SET is_approved = TRUE WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        header("Location: admin_dashboard.php");
        exit;
    }

    if (isset($_POST['delete_user_id'])) {
        $userId = $_POST['delete_user_id'];
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        header("Location: admin_dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>관리자 대시보드</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>관리자 대시보드</h2>

        <!-- 분석 페이지로 이동하는 링크 추가 -->
        <a href="analysis.php" class="btn btn-primary mb-3">통계 분석 페이지로 이동</a>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>사용자명</th>
                    <th>역할</th>
                    <th>승인 상태</th>
                    <th>가입일자</th>
                    <th>작업</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['role']) ?></td>
                        <td><?= $user['is_approved'] ? '승인됨' : '승인 대기 중' ?></td>
                        <td><?= htmlspecialchars($user['created_at']) ?></td>
                        <td>
                            <?php if (!$user['is_approved']): ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="approve_user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-success btn-sm">승인</button>
                                </form>
                            <?php endif; ?>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('정말로 이 사용자를 삭제하시겠습니까?');">
                                <input type="hidden" name="delete_user_id" value="<?= $user['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm">삭제</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>