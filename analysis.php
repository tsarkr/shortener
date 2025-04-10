<?php
session_start();
include 'database.php';

// 관리자 권한 확인
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI']; // 현재 페이지 URL 저장
    header("Location: login.php");
    exit;
}

// 페이지네이션 설정
$limit = 20; // 한 페이지당 20개씩 출력
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 단축 URL 총 개수 가져오기
$totalStmt = $pdo->query("SELECT COUNT(*) FROM urls");
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// 단축 URL 목록 가져오기 (페이징 처리)
$stmt = $pdo->prepare("SELECT id, short_code, original_url, click_count FROM urls ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$urls = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 순번 역순 계산
$index = $totalRows - $offset;

// 수정 및 삭제 요청 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_url_id'])) {
        // URL 수정 처리
        $urlId = $_POST['update_url_id'];
        $newUrl = $_POST['new_original_url'];
        $stmt = $pdo->prepare("UPDATE urls SET original_url = :new_url WHERE id = :id");
        $stmt->execute(['new_url' => $newUrl, 'id' => $urlId]);
        header("Location: analysis.php?page=$page");
        exit;
    }

    if (isset($_POST['delete_url_id'])) {
        // URL 삭제 처리
        $urlId = $_POST['delete_url_id'];
        $stmt = $pdo->prepare("DELETE FROM urls WHERE id = :id");
        $stmt->execute(['id' => $urlId]);
        header("Location: analysis.php?page=$page");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>단축 URL 분석</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .copy-btn { margin-left: 10px; }
        .copy-notice { color: green; font-weight: bold; display: none; }
        .edit-btn, .delete-btn { margin-left: 10px; }
        .save-btn { display: none; margin-left: 10px; }
        .pagination { justify-content: center; }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between mb-3">
            <h2>단축 URL 분석</h2>
            <a href="logout.php" class="btn btn-danger">로그아웃</a>
        </div>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>순번</th>
                    <th>단축 URL</th>
                    <th>원본 URL</th>
                    <th>클릭 수</th>
                    <th>작업</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($urls as $url): ?>
                    <tr>
                        <td><?= $index-- ?></td>
                        <td>
                            <input type="text" class="form-control" id="shortUrl-<?= htmlspecialchars($url['short_code']) ?>" value="http://11e.kr/<?= htmlspecialchars($url['short_code']) ?>" readonly>
                        </td>
                        <td>
                            <input type="text" class="form-control" id="originalUrl-<?= $url['id'] ?>" value="<?= htmlspecialchars($url['original_url']) ?>" readonly>
                        </td>
                        <td><?= htmlspecialchars($url['click_count']) ?></td>
                        <td>
                            <button class="btn btn-primary copy-btn" onclick="copyToClipboard('shortUrl-<?= htmlspecialchars($url['short_code']) ?>')">복사</button>
                            <button class="btn btn-warning edit-btn" onclick="enableEditing(<?= $url['id'] ?>)">수정</button>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="update_url_id" value="<?= $url['id'] ?>">
                                <input type="hidden" id="hiddenOriginalUrl-<?= $url['id'] ?>" name="new_original_url" value="<?= htmlspecialchars($url['original_url']) ?>">
                                <button type="submit" class="btn btn-success save-btn" id="saveBtn-<?= $url['id'] ?>">저장</button>
                            </form>
                            <form method="POST" style="display:inline;" onsubmit="return confirm('정말로 이 URL을 삭제하시겠습니까?');">
                                <input type="hidden" name="delete_url_id" value="<?= $url['id'] ?>">
                                <button type="submit" class="btn btn-danger delete-btn">삭제</button>
                            </form>
                            <a href="url_statistics.php?short_code=<?= htmlspecialchars($url['short_code']) ?>" class="btn btn-info">통계 보기</a>
                            <span id="notice-<?= htmlspecialchars($url['short_code']) ?>" class="copy-notice">복사되었습니다!</span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- 페이지네이션 -->
        <?php
        $maxVisiblePages = 5;
        $startPage = max(1, $page - floor($maxVisiblePages / 2));
        $endPage = min($totalPages, $startPage + $maxVisiblePages - 1);
        if ($endPage - $startPage + 1 < $maxVisiblePages) {
            $startPage = max(1, $endPage - $maxVisiblePages + 1);
        }
        ?>

        <nav>
            <ul class="pagination">
                <?php if ($startPage > 1): ?>
                    <li class="page-item"><a class="page-link" href="analysis.php?page=1">1</a></li>
                    <?php if ($startPage > 2): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                <?php endif; ?>

                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="analysis.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                    <?php endif; ?>
                    <li class="page-item"><a class="page-link" href="analysis.php?page=<?= $totalPages ?>"><?= $totalPages ?></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <script>
        function copyToClipboard(elementId) {
            var copyText = document.getElementById(elementId);
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // 클립보드에 복사
            navigator.clipboard.writeText(copyText.value).then(function() {
                var notice = document.getElementById('notice-' + elementId.split('-')[1]);
                notice.style.display = 'inline';
                setTimeout(function() {
                    notice.style.display = 'none';
                }, 2000);
            }).catch(function(err) {
                alert('복사에 실패했습니다: ' + err);
            });
        }

        function enableEditing(id) {
            var originalUrlField = document.getElementById('originalUrl-' + id);
            var saveBtn = document.getElementById('saveBtn-' + id);
            var hiddenInput = document.getElementById('hiddenOriginalUrl-' + id);

            originalUrlField.readOnly = false;  // 필드 수정 가능하게 변경
            saveBtn.style.display = 'inline';  // 저장 버튼 표시

            // URL 변경 시 hidden input의 값도 변경
            originalUrlField.addEventListener('input', function() {
                hiddenInput.value = originalUrlField.value;
            });
        }
    </script>
</body>
</html>