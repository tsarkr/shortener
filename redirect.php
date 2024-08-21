<?php
include 'database.php';

function incrementClickCount($shortCode, $referer) {
    global $pdo;

    try {
        // 클릭 수 증가
        $stmt = $pdo->prepare("UPDATE urls SET click_count = click_count + 1 WHERE short_code = :code");
        $stmt->execute(['code' => $shortCode]);

        // 클릭 발생 시간 및 referer 기록
        $stmt = $pdo->prepare("INSERT INTO url_clicks (url_id, referer) VALUES ((SELECT id FROM urls WHERE short_code = :code), :referer)");
        $stmt->execute(['code' => $shortCode, 'referer' => $referer]);
    } catch (PDOException $e) {
        echo "Query failed: " . $e->getMessage();
        exit;
    }
}

function getOriginalUrl($shortCode) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT original_url FROM urls WHERE short_code = :code");
        $stmt->execute(['code' => $shortCode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['original_url'] : null;
    } catch (PDOException $e) {
        echo "Query failed: " . $e->getMessage();
        exit;
    }
}

if (isset($_GET['code'])) {
    $shortCode = $_GET['code'];
    $originalUrl = getOriginalUrl($shortCode);

    if ($originalUrl) {
        // 참조 URL 가져오기
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        // 클릭 수 증가 및 참조 URL 기록
        incrementClickCount($shortCode, $referer);

        // 원래 URL로 리디렉션
        header("Location: " . $originalUrl);
        exit;
    } else {
        echo "URL not found!";
    }
}
?>