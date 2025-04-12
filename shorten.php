<?php
session_start();
$config = require_once __DIR__ . '/config.php';  // 설정 파일 로드
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
        die('잘못된 접근입니다 (CSRF).');
    }
}

// 오류 메시지 출력 활성화 (디버깅용)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'vendor/autoload.php'; // Composer로 설치한 경우

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;

include 'database.php'; // 데이터베이스 연결 파일 포함

// 차단된 도메인 목록 예시
$blocked_domains = [
    "pornhub.com",
    "another-bad-site.com",
    // 추가 도메인...
];

// URL이 차단된 도메인에 해당하는지 확인하는 함수
function isBlockedDomain($url, $blocked_domains) {
    $parsed_url = parse_url($url);
    if (isset($parsed_url['host'])) {
        $host = $parsed_url['host'];
        foreach ($blocked_domains as $domain) {
            if (strpos($host, $domain) !== false) {
                return true;
            }
        }
    }
    return false;
}

function generateUniqueShortCode($url, $pdo) {
    // 먼저 동일한 URL이 있는지 확인
    $stmt = $pdo->prepare("SELECT short_code FROM urls WHERE original_url = :url LIMIT 1");
    $stmt->execute(['url' => $url]);
    $existingCode = $stmt->fetchColumn();
    
    if ($existingCode) {
        return ['code' => $existingCode, 'isNew' => false];
    }
    
    // 새로운 URL인 경우 새 코드 생성
    do {
        $shortCode = substr(md5($url . microtime()), 0, 6);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM urls WHERE short_code = :code");
        $stmt->execute(['code' => $shortCode]);
        $exists = $stmt->fetchColumn();
    } while ($exists > 0);
    
    return ['code' => $shortCode, 'isNew' => true];
}

// URL이 http:// 또는 https://로 시작하지 않으면 자동으로 추가
function addHttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}

$blocked_message = false; // 차단된 URL 여부 확인용 변수

// POST 처리 부분 수정
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['original_url'])) {
    $original_url = trim($_POST['original_url']);
    $original_url = addHttp($original_url);

    if (!filter_var($original_url, FILTER_VALIDATE_URL)) {
        $shortened_url = "유효한 URL을 입력하세요.";
    } else if (isBlockedDomain($original_url, $blocked_domains)) {
        $shortened_url = "해당 URL은 차단된 사이트입니다.";
        $blocked_message = true;
    } else if (!empty($original_url)) {
        // 단축 URL 생성 또는 조회
        $result = generateUniqueShortCode($original_url, $pdo);
        $shortened_url_code = $result['code'];
        $shortened_url = "https://11e.kr/" . $shortened_url_code;

        // 새로운 URL인 경우에만 데이터베이스에 저장
        if ($result['isNew']) {
            try {
                $stmt = $pdo->prepare("INSERT INTO urls (original_url, short_code) VALUES (:original_url, :short_code)");
                $stmt->execute(['original_url' => $original_url, 'short_code' => $shortened_url_code]);
            } catch (PDOException $e) {
                die("데이터베이스 저장 중 오류 발생: " . $e->getMessage());
            }
        }

        // QR 코드 파일 경로 확인
        $qr_file = 'qrcodes/' . $shortened_url_code . '.png';
        
        // QR 코드 파일이 없는 경우에만 생성
        if (!file_exists($qr_file)) {
            if (!is_dir('qrcodes')) {
                mkdir('qrcodes', 0755, true);
            }
            
            $result = Builder::create()
                ->writer(new PngWriter())
                ->data($shortened_url)
                ->encoding(new Encoding('UTF-8'))
                ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
                ->size(150)
                ->margin(10)
                ->build();
            
            $result->saveToFile($qr_file);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL 단축 결과</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #f8f9fa;
            flex-direction: column;
        }
        .result-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 600px;
            width: 100%;
            margin-bottom: 20px;
        }
        .btn-copy, .btn-download, .btn-back {
            margin-top: 20px;
        }
        .back-link {
            margin-top: 20px;
            display: block;
            color: #007bff;
        }
        .highlight {
            background-color: #e7f1ff;
            padding: 15px;
            border-left: 5px solid #007bff;
            font-size: 16px;
            color: #007bff;
            margin-top: 20px;
        }
        .qr-ad-container {
            display: flex;
            justify-content: space-around;
            align-items: center;
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .qr-code {
            margin-right: 20px;
        }
        .ad-container {
            width: 300px;
            height: 250px;
            background-color: #fff;
            text-align: center;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        ins.kakao_ad_area {
            display: block;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <div class="result-container">
        <?php if ($blocked_message): ?>
            <h1>차단된 URL</h1>
            <p>입력하신 URL은 차단된 사이트로 연결됩니다.</p>
            <p>이 URL은 단축할 수 없습니다.</p>
        <?php else: ?>
            <h1>URL 단축 결과</h1>
            <p>아래에 생성된 단축 URL을 확인하세요.</p>
            <p>단축된 URL: <a href="<?= htmlspecialchars($shortened_url) ?>" target="_blank"><?= htmlspecialchars($shortened_url) ?></a></p>
            <button class="btn btn-secondary btn-copy" onclick="copyToClipboard('<?= htmlspecialchars($shortened_url) ?>')">클립보드에 복사</button>
            <a href="index.php" class="back-link">메인 페이지로 돌아가기</a>

            <!-- 통계 서비스 설명 추가 -->
            <div class="highlight">
                <p>생성된 단축 URL의 통계 데이터를 확인할 수 있습니다. 단축 URL 뒤에 <code>*</code>를 추가하여 클릭 수, 유입 경로, 브라우저 정보 등의 통계 데이터를 실시간으로 확인하세요. 예시: <code>https://11e.kr/abc123*</code></p>
            </div>

            <!-- QR 코드 및 광고 수평 배치 -->
            <div class="qr-ad-container">
                <?php if (isset($qr_file)): ?>
                    <div class="qr-code">
                        <p>QR 코드:</p>
                        <img src="<?= htmlspecialchars($qr_file) ?>" alt="QR Code">
                        <br>
                        <a href="<?= htmlspecialchars($qr_file) ?>" download class="btn btn-primary btn-download">QR 코드 다운로드</a>
                    </div>
                <?php endif; ?>
                <!-- 하단 광고 -->
                <div class="ad-container">
                    <ins class="kakao_ad_area" style="display:none;"
                        data-ad-unit="<?php echo htmlspecialchars($config['ads']['kakao']['result']['unit']); ?>"
                        data-ad-width="<?php echo $config['ads']['kakao']['result']['width']; ?>"
                        data-ad-height="<?php echo $config['ads']['kakao']['result']['height']; ?>"></ins>
                    <script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
                </div>
            </div>
        <?php endif; ?>

        <!-- 처음으로 돌아가기 버튼 -->
        <a href="index.php" class="btn btn-secondary btn-back">처음으로 돌아가기</a>
    </div>

    <script>
        function copyToClipboard(text) {
            const textarea = document.createElement("textarea");
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand("copy");
            document.body.removeChild(textarea);
            alert("클립보드에 복사되었습니다.");
        }
    </script>
</body>
</html>