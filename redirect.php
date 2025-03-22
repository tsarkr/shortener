<?php
// 출력 버퍼링 시작
ob_start();

include 'database.php';

/**
 * 클릭 수 증가 및 클릭 정보 기록 함수
 */
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
            'code'       => $shortCode,
            'referer'    => $referer,
            'user_agent' => $userAgent,
            'ip_address' => $ipAddress
        ]);
    } catch (PDOException $e) {
        // 실제 운영 환경에서는 상세 에러 출력 대신 로깅하는 것이 좋습니다.
        error_log("Query failed: " . $e->getMessage());
        exit("처리 중 문제가 발생했습니다.");
    }
}

/**
 * 단축 코드에 해당하는 원래 URL을 반환하는 함수
 */
function getOriginalUrl($shortCode) {
    global $pdo;

    try {
        $stmt = $pdo->prepare("SELECT original_url FROM urls WHERE short_code = :code");
        $stmt->execute(['code' => $shortCode]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $row['original_url'] : null;
    } catch (PDOException $e) {
        error_log("Query failed: " . $e->getMessage());
        exit("처리 중 문제가 발생했습니다.");
    }
}

// GET 파라미터 'code'가 있는지와 예상 형식(6자리 영문/숫자, 선택적으로 '*'가 끝에 올 수 있음)인지 검증
if (isset($_GET['code']) && preg_match('/^[A-Za-z0-9]{6}\*?$/', $_GET['code'])) {
    $shortCode = $_GET['code'];

    // 단축 URL 뒤에 '*'이 있는지 확인 (통계 페이지 요청)
    if (substr($shortCode, -1) === '*') {
        // '*' 제거 후 통계 페이지로 리디렉션
        $shortCode = rtrim($shortCode, '*');
        $redirectUrl = "url_statistics.php?short_code=" . urlencode($shortCode);
        error_log("Redirecting to: " . $redirectUrl);
        header("Location: " . $redirectUrl);
        ob_end_flush();
        exit;
    }

    // 원래 URL 가져오기
    $originalUrl = getOriginalUrl($shortCode);

    if ($originalUrl) {
        // HTTP_REFERER, User-Agent, IP 주소 가져오기
        $referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        // 클릭 수 증가 및 클릭 기록 저장
        incrementClickCount($shortCode, $referer, $userAgent, $ipAddress);

        // 원래 URL로 안전하게 리디렉션
        header("Location: " . filter_var($originalUrl, FILTER_SANITIZE_URL));
        ob_end_flush();
        exit;
    } else {
        echo "URL not found!";
    }
} else {
    // 코드 파라미터가 없거나 유효하지 않을 때
    echo "잘못된 요청입니다.";
}

ob_end_flush();
?>