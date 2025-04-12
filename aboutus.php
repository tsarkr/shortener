<?php
$config = require_once __DIR__ . '/config.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>서비스 설명 | URL 단축기</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            color: #333;
            margin: 0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 36px;
        }
        .content {
            margin-top: 30px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0px 4px 12px rgba(0, 0, 0, 0.1);
            max-width: 850px;
            margin-left: auto;
            margin-right: auto;
        }
        .content h2 {
            margin-top: 20px;
            font-size: 24px;
            color: #007bff;
        }
        .content p {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .content ul, .content ol {
            margin-bottom: 20px;
        }
        .highlight {
            background-color: #e7f1ff;
            padding: 15px;
            border-left: 5px solid #007bff;
            margin-bottom: 20px;
            font-size: 18px;
            color: #007bff;
        }
        .qr-highlight {
            background-color: #ffeb3b;
            padding: 15px;
            border-left: 5px solid #ff9800;
            margin-bottom: 20px;
            font-size: 18px;
            color: #ff5722;
            font-weight: bold;
            text-align: center;
        }
        .back-button {
            margin-top: 30px;
            text-align: center;
        }
        .ad-container {
            width: 100%;
            background-color: #f1f1f1;
            padding: 10px 0;
            text-align: center;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <div class="top-nav">
        <a href="index.php" class="btn btn-outline-primary">메인으로 돌아가기</a>
    </div>

    <!-- 상단 광고 영역 -->
    <div class="ad-container">
        <ins class="kakao_ad_area" style="display:block;"
        data-ad-unit = "<?php echo htmlspecialchars($config['ads']['kakao']['about']['unit']); ?>"
        data-ad-width = "<?php echo $config['ads']['kakao']['about']['width']; ?>"
        data-ad-height = "<?php echo $config['ads']['kakao']['about']['height']; ?>"></ins>
        <script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
    </div>

    <!-- Header Section -->
    <div class="header">
        <h1>쉽고 빠른 URL 단축기</h1>
        <p>긴 URL을 짧고 공유 가능한 링크로 변환하세요!</p>
    </div>

    <!-- Content Section -->
    <div class="container content mt-5">
        <!-- 한글 섹션 -->
        <div class="language-section">
            <h2>서비스 개요</h2>
            <p>우리의 URL 단축 서비스는 긴 URL을 짧고 간결한 링크로 변환해줍니다. 더 짧아진 URL은 쉽게 공유할 수 있으며, 클릭 데이터를 분석하여 마케팅 전략을 세우는 데 유용한 정보를 제공합니다.</p>

            <h2>주요 기능</h2>
            <ul>
                <li><strong>URL 단축:</strong> 긴 링크를 짧은 링크로 변환하여 공유성을 높입니다.</li>
                <li><strong>클릭 통계 제공:</strong> 단축된 URL의 클릭 수와 트래픽 소스를 분석합니다.</li>
                <li><strong>브라우저 호환성:</strong> 모든 주요 브라우저에서 사용 가능합니다.</li>
                <li><strong>QR 코드 생성:</strong> 모든 단축 URL에 대해 자동으로 QR 코드를 생성합니다.</li>
                <li><strong>통계 분석:</strong> URL 끝에 * 를 추가하여 상세 통계를 확인할 수 있습니다.</li>
            </ul>

            <div class="highlight">
                <p>사용 방법</p>
                <ol>
                    <li>메인 페이지에서 단축하고 싶은 URL을 입력합니다.</li>
                    <li>자동으로 생성된 단축 URL과 QR 코드를 받습니다.</li>
                    <li>단축 URL 끝에 *를 추가하여 클릭 통계를 확인할 수 있습니다.</li>
                </ol>
            </div>

            <div class="qr-highlight">
                <p>생성된 단축 URL과 함께 QR 코드도 무상으로 제공됩니다!</p>
            </div>
        </div>

        <div class="language-divider"></div>

        <!-- English Section -->
        <div class="language-section">
            <h2>Service Overview</h2>
            <p>Our URL shortening service converts long URLs into short, concise links. The shortened URLs are easy to share and provide useful analytics data for your marketing strategy.</p>

            <h2>Key Features</h2>
            <ul>
                <li><strong>URL Shortening:</strong> Convert long links into short, shareable URLs.</li>
                <li><strong>Click Analytics:</strong> Track clicks and analyze traffic sources.</li>
                <li><strong>Browser Compatibility:</strong> Works across all major browsers.</li>
                <li><strong>QR Code Generation:</strong> Automatically generates QR codes for all shortened URLs.</li>
                <li><strong>Statistics Analysis:</strong> Add * at the end of URL to view detailed statistics.</li>
            </ul>

            <div class="highlight">
                <p>How to Use</p>
                <ol>
                    <li>Enter the URL you want to shorten on the main page.</li>
                    <li>Get your automatically generated short URL and QR code.</li>
                    <li>Add * at the end of the shortened URL to view click statistics.</li>
                </ol>
            </div>

            <div class="qr-highlight">
                <p>Free QR code provided with every shortened URL!</p>
            </div>
        </div>

        <!-- Back to Main Page Button -->
        <div class="back-button">
            <a href="index.php" class="btn btn-primary">메인 페이지로 돌아가기</a>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>