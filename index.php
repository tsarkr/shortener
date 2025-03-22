<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>11e.kr | URL 단축기 - 빠르고 쉬운 URL 단축 서비스</title>
    
    <!-- Meta Description for Naver -->
    <meta name="naver-site-verification" content="5bbdc3d224a84d88022aa9a34c8db8671916589c" />

    <!-- Meta Description for SEO -->
    <meta name="description" content="11e.kr에서 긴 URL을 짧고 간결한 링크로 변환하세요. 무료로 QR 코드 생성과 클릭 통계 서비스를 제공하여 더욱 쉽게 링크를 관리하세요.">
    
    <!-- Open Graph for Social Sharing -->
    <meta property="og:title" content="11e.kr | URL 단축기">
    <meta property="og:description" content="긴 URL을 짧게 만들고 QR 코드와 클릭 통계를 확인하세요.">
    <meta property="og:image" content="https://11e.kr/images/og-image.png"> <!-- 대표 이미지 경로 설정 -->
    <meta property="og:image:alt" content="11e.kr 로고와 함께 간단한 URL 단축 서비스">
    <meta property="og:url" content="https://11e.kr">
    
    <!-- Twitter Card for Social Sharing -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="11e.kr | URL 단축기">
    <meta name="twitter:description" content="긴 URL을 짧게 만들고 QR 코드와 클릭 통계를 확인하세요.">
    <meta name="twitter:image" content="https://11e.kr/images/twitter-image.png"> <!-- 대표 이미지 경로 설정 -->
    <meta name="twitter:image:alt" content="11e.kr 로고와 함께 간단한 URL 단축 서비스">

    <!-- Structured Data for SEO -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebSite",
      "name": "11e.kr",
      "url": "https://11e.kr",
      "potentialAction": {
        "@type": "SearchAction",
        "target": "https://11e.kr/?q={search_term_string}",
        "query-input": "required name=search_term_string"
      }
    }
    </script>

    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body, html {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }
        .shortener-container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding-top: 20px;
            margin-bottom: 0;
        }
        .shortener-box {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        .instructions {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 20px;
        }
        .highlight {
            background-color: #e7f1ff;
            padding: 15px;
            border-left: 5px solid #007bff;
            font-size: 16px;
            color: #007bff;
            margin-bottom: 20px;
        }
        .footer-links {
            margin-top: 20px;
            text-align: center;
        }
        .footer-links a {
            color: #007bff;
            text-decoration: none;
        }
        .footer-links a:hover {
            text-decoration: underline;
        }
        .ad-container {
            width: 100%;
            text-align: center;
            background-color: #fff;
            padding: 10px 0;
            margin-top: 20px;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
        }
        ins.kakao_ad_area {
            display: block;
            width: 320px;
            height: 100px;
            margin: 0 auto;
        }
        @media (max-width: 576px) {
            .shortener-box {
                padding: 15px;
            }
            .instructions {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div class="container shortener-container">
        <div class="shortener-box">
            <h1 class="mb-4 text-center">URL 단축기</h1>
            <p class="text-center">아래에 긴 URL을 입력하고 단축 버튼을 누르세요.</p>
            <div class="instructions">
                <ul>
                    <li>URL을 입력할 때 <code>http://</code> 또는 <code>https://</code>는 자동으로 처리됩니다.</li>
                    <li>입력된 URL이 올바른 형식인지 확인하세요.</li>
                    <li>단축된 URL은 더 짧고 공유하기 쉬운 링크로 제공됩니다.</li>
                    <li><strong>추가 혜택:</strong> 생성된 단축 URL과 함께 QR 코드도 무상으로 제공됩니다!</li>
                </ul>
            </div>

            <h2 class="highlight-title">통계 서비스 설명 추가</h2>
            <div class="highlight">
                <p>생성된 단축 URL의 통계 데이터를 확인할 수 있습니다. 단축 URL 뒤에 <code>*</code>를 추가하여 클릭 수, 유입 경로, 브라우저 정보 등의 통계 데이터를 실시간으로 확인하세요. 예시: <code>https://11e.kr/abc123*</code></p>
            </div>

            <?php
            session_start();
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            ?>
            <form method="POST" action="shorten.php">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                <input type="text" name="original_url" class="form-control" placeholder="URL을 입력하세요" required>
                <button type="submit">단축</button>
            </form>

            <div class="footer-links">
                <a href="aboutus.html">서비스 설명</a>
            </div>

        </div>
    </div>

    <!-- Asynchronous loading of Bootstrap -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js" async></script>
</body>
</html>
