<?php
// 오류 메시지 출력 활성화 (디버깅용)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 한국 시간대 설정
date_default_timezone_set('Asia/Seoul');

require 'vendor/autoload.php'; // Composer로 설치한 경우

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;

include 'database.php'; // 데이터베이스 연결 파일 포함

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

// 원형 통계값을 가져오는 함수 (예시로 하루 및 일주일의 클릭 수)
function getCircularStatistics($shortCode, $pdo) {
    try {
        // 일간 클릭 수
        $dailyClicksStmt = $pdo->prepare("
            SELECT COUNT(*) FROM url_clicks 
            JOIN urls ON url_clicks.url_id = urls.id 
            WHERE urls.short_code = :short_code AND DATE(url_clicks.click_time) = CURDATE()
        ");
        $dailyClicksStmt->execute(['short_code' => $shortCode]);
        $dailyClicks = $dailyClicksStmt->fetchColumn();

        // 주간 클릭 수
        $weeklyClicksStmt = $pdo->prepare("
            SELECT COUNT(*) FROM url_clicks 
            JOIN urls ON url_clicks.url_id = urls.id 
            WHERE urls.short_code = :short_code AND YEARWEEK(url_clicks.click_time, 1) = YEARWEEK(CURDATE(), 1)
        ");
        $weeklyClicksStmt->execute(['short_code' => $shortCode]);
        $weeklyClicks = $weeklyClicksStmt->fetchColumn();

        return ['daily' => $dailyClicks, 'weekly' => $weeklyClicks];
    } catch (PDOException $e) {
        echo "<p>Error: " . $e->getMessage() . "</p>";
        return false;
    }
}

// QR 코드 생성 함수
function generateQrCode($url, $shortCode) {
    $result = Builder::create()
        ->writer(new PngWriter())
        ->data($url)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelLow())
        ->size(150)  // QR 코드 크기 조정
        ->margin(10)
        ->build();

    // QR 코드 이미지를 저장할 디렉토리가 있는지 확인
    $qr_directory = 'qrcodes/';
    if (!is_dir($qr_directory)) {
        mkdir($qr_directory, 0755, true);  // 디렉토리가 없으면 생성
    }

    $qr_file = $qr_directory . $shortCode . '.png'; // QR 코드 파일 경로
    $result->saveToFile($qr_file); // 파일로 저장

    return $qr_file;
}

// 엑셀 다운로드 기능
if (isset($_GET['download']) && isset($_GET['short_code'])) {
    $shortCode = $_GET['short_code'];
    $statistics = getUrlStatistics($shortCode, $pdo);

    if ($statistics && count($statistics) > 0) {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment;filename="statistics_' . $shortCode . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['클릭 시간', '참조 URL', '브라우저 정보', 'IP 주소']);

        foreach ($statistics as $stat) {
            fputcsv($output, $stat);
        }
        
        fclose($output);
        exit;
    } else {
        echo "통계 데이터를 찾을 수 없습니다.";
        exit;
    }
}

// short_code 값이 전달되었는지 확인
if (isset($_GET['short_code']) && !empty($_GET['short_code'])) {
    $shortCode = $_GET['short_code'];
    $statistics = getUrlStatistics($shortCode, $pdo);
    $circularStats = getCircularStatistics($shortCode, $pdo);

    // 단축 URL을 생성하여 QR 코드 생성
    $shortened_url = "https://11e.kr/" . $shortCode;
    $qr_file = generateQrCode($shortened_url, $shortCode);

    // 현재 조회 일시
    $currentDateTime = date('Y-m-d H:i:s');

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
                    max-width: 900px;
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
                .description {
                    font-size: 16px;
                    margin-bottom: 10px;
                    text-align: center;
                }
                .current-time {
                    text-align: right;
                    font-size: 14px;
                    color: #666;
                    margin-bottom: 30px;
                }
                /* QR 코드 및 광고 배치 */
                .qr-ad-container {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 50px;
                }
                .qr-code {
                    margin-right: 20px;
                }
                /* 원형 통계 값 스타일 */
                .circular-stats {
                    display: flex;
                    justify-content: space-around;
                    margin-bottom: 30px;
                }
                .stat-circle {
                    width: 100px;
                    height: 100px;
                    border-radius: 50%;
                    background-color: #007bff;
                    color: white;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    font-size: 24px;
                    font-weight: bold;
                    text-align: center;
                }
                .left-ad {
                    width: 300px;
                    height: 250px;
                    background-color: #f1f1f1;
                    padding: 10px;
                    text-align: center;
                    box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                }
                .btn-container {
                    margin-bottom: 20px;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="container">
                <p class="description">이 통계는 11e.kr에서 제공하는 통계 서비스의 일환으로 제공됩니다.</p>
                <h2>단축 URL: <?= htmlspecialchars($shortCode) ?>의 통계</h2>
                <p class="current-time">조회 일시: <?= htmlspecialchars($currentDateTime) ?></p>

                <!-- QR 코드 및 광고 -->
                <div class="qr-ad-container">
                    <div class="qr-code">
                        <p>QR 코드:</p>
                        <img src="<?= htmlspecialchars($qr_file) ?>" alt="QR Code" class="qr-code">
                        <br>
                        <a href="<?= htmlspecialchars($qr_file) ?>" download class="btn btn-primary">QR 코드 다운로드</a>
                    </div>
                    <div class="left-ad">
                        <ins class="kakao_ad_area" style="display:none;"
                        data-ad-unit = "DAN-Y0ZNLuIjfBEOujr3"
                        data-ad-width = "300"
                        data-ad-height = "250"></ins>
                        <script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
                    </div>
                    </div>
            <!-- 원형 통계 값 -->
            <div class="circular-stats">
                <div class="stat-circle">일간<br><?= $circularStats['daily'] ?></div>
                <div class="stat-circle">주간<br><?= $circularStats['weekly'] ?></div>
            </div>
        <!-- 엑셀 다운로드 및 index.php 바로가기 -->
        <div class="btn-container">
            <a href="url_statistics.php?short_code=<?= htmlspecialchars($shortCode) ?>&download=true" class="btn btn-success">엑셀로 다운로드</a>
            <a href="index.php" class="btn btn-secondary">메인 페이지로 돌아가기</a>
        </div>

        <!-- 통계 데이터 테이블 -->
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
    echo "잘못된 접근입니다.";
}
?>