<?php
// 출력 버퍼링 시작
ob_start();

include 'database.php';

function incrementClickCount($shortCode, $referer, $userAgent, $ipAddress) {
    global $pdo;

    try {
        // 클릭 수 증가
        $stmt = $pdo->prepare("UPDATE urls SET click_count = click_count + 1 WHERE short_code = :code");
        $stmt->execute(['code' => $shortCode]);

        // referer, user_agent, ip_address UTF-8 인코딩 처리
        $referer = $referer ? mb_convert_encoding($referer, 'UTF-8', 'auto') : null;
        $userAgent = $userAgent ? mb_convert_encoding($userAgent, 'UTF-8', 'auto') : null;

        // 클릭 발생 시간 및 referer, user_agent, ip_address 기록
        $stmt = $pdo->prepare("
            INSERT INTO url_clicks (url_id, referer, user_agent, ip_address) 
            VALUES ((SELECT id FROM urls WHERE short_code = :code), :referer, :user_agent, :ip_address)
        ");
        $stmt->execute([
            'code' => $shortCode,
            'referer' => $referer,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
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
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        // 클릭 수 증가 및 참조 URL 기록
        incrementClickCount($shortCode, $referer, $userAgent, $ipAddress);
        
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
