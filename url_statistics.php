<?php
session_start();

// 에러 보고 활성화 (디버깅 용도)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 데이터베이스 연결 파일 포함
include 'database.php';

// 관리자 권한 확인
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// 특정 URL의 통계 데이터를 가져오는 함수
function getUrlStatistics($shortCode, $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT url_clicks.click_time, url_clicks.referer, url_clicks.user_agent, url_clicks.ip_address 
            FROM url_clicks 
            JOIN urls ON url_clicks.url_id = urls.id 
            WHERE urls.short_code = :short_code
            ORDER BY url_clicks.click_time DESC
        ");
        $stmt->execute(['short_code' => $shortCode]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
        return false;
    }
}

// short_code 값이 전달되었는지 확인
if (isset($_GET['short_code']) && !empty($_GET['short_code'])) {
    $shortCode = $_GET['short_code'];
    $statistics = getUrlStatistics($shortCode, $pdo);

    // 통계 데이터가 있는지 확인
    if ($statistics && count($statistics) > 0) {
        ?>
        <!DOCTYPE html>
        <html lang="ko">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>URL 통계</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .container {
                    margin-top: 50px;
                }
                .table-striped {
                    background-color: #f9f9f9;
                    box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
                }
                .table-striped th, .table-striped td {
                    text-align: center;
                }
                h2 {
                    margin-bottom: 20px;
                    color: #007bff;
                }
                .no-data {
                    font-size: 18px;
                    color: #ff0000;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <h2>단축 URL: <?= htmlspecialchars($shortCode) ?>의 통계</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>클릭 시간</th>
                            <th>참조 URL</th>
                            <th>브라우저 정보</th>
                            <th>IP 주소</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($statistics as $stat): ?>
                            <tr>
                                <td><?= htmlspecialchars($stat['click_time']) ?></td>
                                <td><?= htmlspecialchars($stat['referer']) ?></td>
                                <td><?= htmlspecialchars($stat['user_agent']) ?></td>
                                <td><?= htmlspecialchars($stat['ip_address']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </body>
        </html>
        <?php
    } else {
        ?>
        <!DOCTYPE html>
        <html lang="ko">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>URL 통계</title>
            <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
            <style>
                .container {
                    margin-top: 50px;
                    text-align: center;
                }
                .no-data {
                    font-size: 24px;
                    color: #ff0000;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <p class="no-data">통계 데이터가 없습니다.</p>
            </div>
        </body>
        </html>
        <?php
    }
} else {
    echo "<p>잘못된 접근입니다.</p>";
}
?>