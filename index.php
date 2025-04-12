<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>11e.kr | URL 단축기</title>
    
    <!-- SEO Meta Tags -->
    <meta name="description" content="11e.kr에서 긴 URL을 짧고 간결한 링크로 변환하세요.">
    <meta property="og:title" content="11e.kr | URL 단축기">
    <meta property="og:description" content="긴 URL을 짧게 만들고 QR 코드와 클릭 통계를 확인하세요.">
    
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }
        .shortener-container {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 30px;
        }
        .shortener-box {
            max-width: 600px;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .language-block {
            background: #fff;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            border: 1px solid #eee;
        }
        .language-title {
            color: #2c3e50;
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
        }
        .language-content {
            color: #34495e;
            line-height: 1.6;
        }
        .language-divider {
            margin: 15px 0;
            border-top: 1px solid #eee;
        }
        code {
            background: #f8f9fa;
            color: #e83e8c;
            padding: 2px 6px;
            border-radius: 4px;
        }
        form .form-control {
            height: 50px;
            border-radius: 25px;
        }
        form button {
            width: 100%;
            height: 50px;
            border-radius: 25px;
            background: #007bff;
            color: white;
            border: none;
            font-weight: 600;
        }
        @media (max-width: 768px) {
            .shortener-box {
                margin: 15px;
            }
        }
        .ad-container {
            width: 100%;
            max-width: 320px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
        }
    </style>
</head>
<body>
    <?php
    // 파일 상단에 설정 파일 로드
    $config = require_once __DIR__ . '/config.php';

    session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    ?>
    <div class="container shortener-container">
        <div class="shortener-box">
            <h1 class="text-center mb-4">URL 단축기</h1>
            
            <!-- URL 입력 폼을 맨 위로 이동 -->
            <form method="POST" action="shorten.php" class="mb-4">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="text" name="original_url" class="form-control mb-3" placeholder="URL을 입력하세요" required>
                <button type="submit">단축</button>
            </form>

            <!-- 서비스 설명 블록 -->
            <div class="language-block">
                <h3 class="language-title">URL 단축 서비스</h3>
                <div class="language-content">
                    <ul>
                        <li>URL 입력 시 http(s):// 자동 처리</li>
                        <li>단축된 URL과 함께 QR 코드 제공</li>
                    </ul>
                </div>
                
                <div class="language-divider"></div>
                
                <div class="language-content">
                    <ul>
                        <li>URLs are automatically processed with http(s)://</li>
                        <li>QR code provided with shortened URL</li>
                    </ul>
                </div>
            </div>

            <!-- 통계 설명 블록 -->
            <div class="language-block">
                <h3 class="language-title">통계 확인 / Statistics</h3>
                <div class="language-content">
                    <p>단축 URL 끝에 <code>*</code> 추가로 통계 확인<br>
                    Add <code>*</code> at the end of URL to view statistics</p>
                    <p>예시/Example: <code>11e.kr/abc123*</code></p>
                </div>
            </div>

            <!-- 광고 섹션 -->
            <div class="ad-container mt-4 mb-4">
                <div class="text-center">
                    <ins class="kakao_ad_area" style="display:none;"
                        data-ad-unit = "<?php echo htmlspecialchars($config['ads']['kakao']['main']['unit']); ?>"
                        data-ad-width = "<?php echo $config['ads']['kakao']['main']['width']; ?>"
                        data-ad-height = "<?php echo $config['ads']['kakao']['main']['height']; ?>"></ins>
                    <script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
                </div>
            </div>

            <!-- 서비스 설명 링크 -->
            <div class="text-center mt-4">
                <a href="aboutus.php" class="text-decoration-none">
                    <small class="text-muted">서비스 설명 <i class="fas fa-chevron-right"></i></small>
                </a>
            </div>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" async></script>
</body>
</html>
