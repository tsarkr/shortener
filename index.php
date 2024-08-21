<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL 단축기</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-N88DTJJQ');</script>
    <!-- End Google Tag Manager -->
    
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
            position: fixed;
            bottom: 0;
            left: 0;
            background-color: #fff;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1;
        }
        ins.kakao_ad_area {
            display: block;
            width: 100%;
            height: 250px;
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
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-N88DTJJQ"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
    
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

            <!-- 통계 서비스 설명 추가 -->
            <div class="highlight">
                <p>생성된 단축 URL의 통계 데이터를 확인할 수 있습니다. 단축 URL 뒤에 <code>*</code>를 추가하여 클릭 수, 유입 경로, 브라우저 정보 등의 통계 데이터를 실시간으로 확인하세요. 예시: <code>https://11e.kr/abc123*</code></p>
            </div>

            <form method="POST" action="shorten.php">
                <div class="form-group">
                    <input type="text" name="original_url" class="form-control" placeholder="URL을 입력하세요" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">단축</button>
            </form>

            <div class="footer-links">
                <a href="aboutus.html">서비스 설명</a>
            </div>
        </div>
    </div>

    <div class="ad-container">
        <ins class="kakao_ad_area" style="display:block;"
        data-ad-unit="DAN-Y0ZNLuIjfBEOujr3"
        data-ad-width="300"
        data-ad-height="250"></ins>
        <script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>