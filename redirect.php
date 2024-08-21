<?php
// 출력 버퍼링 시작
ob_start();

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

    // 단축 URL 뒤에 '*'이 있는지 확인
    if (substr($shortCode, -1) === '*') {
        // '*'을 제거하고 통계 페이지로 리디렉션
        $shortCode = rtrim($shortCode, '*');
        
        // 리디렉션 URL을 확인
        $redirectUrl = "url_statistics.php?short_code=" . $shortCode;
        error_log("Redirecting to: " . $redirectUrl);  // 서버 로그에 기록
        header("Location: " . $redirectUrl);
        
        // 출력 버퍼링 종료
        ob_end_flush();
        exit;
    }

    // 원래 URL 가져오기
    $originalUrl = getOriginalUrl($shortCode);

    if ($originalUrl) {
        // 참조 URL 가져오기
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;

        // 클릭 수 증가 및 참조 URL 기록
        incrementClickCount($shortCode, $referer);

        // 원래 URL로 리디렉션
        header("Location: " . $originalUrl);

        // 출력 버퍼링 종료
        ob_end_flush();
        exit;
    } else {
        echo "URL not found!";
    }
}

// 출력 버퍼링 종료
ob_end_flush();
?>