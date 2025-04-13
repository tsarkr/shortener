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

// PDO 에러 모드를 예외 처리로 설정
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 한 페이지에 보여줄 데이터 수
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 특정 URL의 통계 데이터를 가져오는 함수
function getUrlStatistics($shortCode, $pdo, $limit, $offset) {
    try {
        $stmt = $pdo->prepare("
            SELECT url_clicks.click_time, url_clicks.referer, url_clicks.user_agent, url_clicks.ip_address 
            FROM url_clicks 
            JOIN urls ON url_clicks.url_id = urls.id 
            WHERE urls.short_code = :short_code
            ORDER BY url_clicks.click_time DESC
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':short_code', $shortCode, PDO::PARAM_STR);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 에러 로그 출력
        error_log($e->getMessage());
        echo "<p>데이터베이스 쿼리 오류: " . htmlspecialchars($e->getMessage()) . "</p>";
        return false;
    }
}

// 총 데이터 수를 가져오는 함수
function getTotalStatisticsCount($shortCode, $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM url_clicks 
            JOIN urls ON url_clicks.url_id = urls.id 
            WHERE urls.short_code = :short_code
        ");
        $stmt->execute(['short_code' => $shortCode]);
        return $stmt->fetchColumn();
    } catch (PDOException $e) {
        // 에러 로그 출력
        error_log($e->getMessage());
        echo "<p>데이터베이스 쿼리 오류: " . htmlspecialchars($e->getMessage()) . "</p>";
        return 0;
    }
}

// 특정 URL의 총 클릭 수와 마지막 클릭 시간을 가져오는 함수
function getUrlSummary($shortCode, $pdo) {
    try {
        $stmt = $pdo->prepare("
            SELECT COUNT(url_clicks.id) AS click_count, MAX(url_clicks.click_time) AS last_click 
            FROM url_clicks
            JOIN urls ON url_clicks.url_id = urls.id
            WHERE urls.short_code = :short_code
        ");
        $stmt->bindValue(':short_code', $shortCode, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 에러 로그 출력
        error_log($e->getMessage());
        echo "<p>데이터베이스 쿼리 오류: " . htmlspecialchars($e->getMessage()) . "</p>";
        return false;
    }
}

// QR 코드 생성 함수
function generateQrCode($url, $shortCode) {
    try {
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
            // @ 연산자를 사용하여 디렉토리 생성 시 발생할 수 있는 경고 억제
            @mkdir($qr_directory, 0775, true);
        }

        $qr_file = $qr_directory . $shortCode . '.png'; // QR 코드 파일 경로
        // @ 연산자를 사용하여 파일 저장 시 발생할 수 있는 경고 억제
        @$result->saveToFile($qr_file); // 파일로 저장

        return $qr_file;
    } catch (Exception $e) {
        // 예외 처리 및 로그 출력
        error_log($e->getMessage());
        return null;
    }
}

// short_code 값이 전달되었는지 확인
if (isset($_GET['short_code']) && !empty($_GET['short_code'])) {
    $shortCode = $_GET['short_code'];
    $statistics = getUrlStatistics($shortCode, $pdo, $limit, $offset);
    $totalStatisticsCount = getTotalStatisticsCount($shortCode, $pdo);
    $totalPages = ceil($totalStatisticsCount / $limit);

    // 단축 URL 요약 정보 (클릭 수 및 마지막 클릭 시간) 가져오기
    $urlSummary = getUrlSummary($shortCode, $pdo);
    
    // 단축 URL을 생성하여 QR 코드 생성
    $shortened_url = "https://11e.kr/" . $shortCode;
    $qr_file = generateQrCode($shortened_url, $shortCode);

    // 현재 조회 일시
    $currentDateTime = date('Y-m-d H:i:s');

    ?>
    <!DOCTYPE html>
    <html lang="ko">
    <head>
        <?php if ($config['analytics']['google']['enabled']): ?>
        <!-- Google Analytics 4 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($config['analytics']['google']['id']); ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo htmlspecialchars($config['analytics']['google']['id']); ?>');
        </script>
        <?php endif; ?>

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
            /* 페이지네이션 스타일 */
            .pagination {
                justify-content: center;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <p class="description">이 통계는 11e.kr에서 제공하는 통계 서비스의 일환으로 제공됩니다.</p>
            <h2>단축 URL: <?= htmlspecialchars($shortCode) ?>의 통계</h2>
            <p>총 클릭 수: <?= htmlspecialchars($urlSummary['click_count']) ?>회</p>
            <p>마지막 클릭 시간: <?= $urlSummary['last_click'] ? htmlspecialchars($urlSummary['last_click']) : '클릭 기록 없음' ?></p>
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

            <!-- 엑셀 다운로드 및 index.php 바로가기 -->
            <div class="btn-container">
                <a href="url_statistics.php?short_code=<?= htmlspecialchars($shortCode) ?>&download=true" class="btn btn-success">엑셀로 다운로드</a>
                <a href="index.php" class="btn btn-secondary">메인 페이지로 돌아가기</a>
            </div>

            <?php if ($statistics && count($statistics) > 0): ?>
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

                <!-- 페이지네이션 -->
                <nav>
                    <ul class="pagination">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                <a class="page-link" href="url_statistics.php?short_code=<?= htmlspecialchars($shortCode) ?>&page=<?= $i ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </nav>
            <?php else: ?>
                <p class="no-data">통계 데이터가 없습니다.</p>
            <?php endif; ?>

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
        <title>잘못된 접근</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

        <style>
            .container {
                margin-top: 50px;
                text-align: center;
            }
            .no-data {
                font-size: 24px;
                color: #ff0000;
                margin-bottom: 20px;
            }
            .left-ad {
                width: 300px;
                height: 250px;
                background-color: #f1f1f1;
                padding: 10px;
                text-align: center;
                box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
                margin: 20px auto;
            }
            .btn-container {
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <p class="no-data">잘못된 접근입니다.</p>

            <!-- 메인 페이지로 돌아가기 버튼 -->
            <div class="btn-container">
                <a href="index.php" class="btn btn-secondary">메인 페이지로 돌아가기</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
