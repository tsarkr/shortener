<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL 단축 결과 | URL 단축기</title>
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
            align-items: flex-start; /* 위쪽 여백을 줄이기 위해 flex-start로 설정 */
            padding-top: 20px; /* 위쪽 여백을 20px로 설정 */
            margin-bottom: 0;
        }
        .shortener-box {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            border-radius: 10px;
            background-color: #f8f9fa;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            position: relative; /* 콘텐츠가 광고 위로 올라오도록 설정 */
            z-index: 2;
        }
        .result-box {
            font-size: 16px;
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
        /* 광고 배너를 하단에 고정하고 콘텐츠 아래로 보이도록 설정 */
        .ad-container {
            width: 100%;
            text-align: center;
            position: fixed;
            bottom: 0;
            left: 0;
            background-color: #fff;
            padding: 10px 0;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1; /* 광고가 콘텐츠 아래에 있도록 설정 */
        }
        ins.kakao_ad_area {
            display: block;
            width: 100%;
            height: 250px; /* 고정된 높이 */
        }
        /* 반응형 미디어 쿼리 */
        @media (max-width: 576px) {
            .shortener-box {
                padding: 15px;
            }
            .result-box {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <div class="container shortener-container">
        <div class="shortener-box">
            <h1 class="mb-4 text-center">URL 단축 결과</h1>
            <p class="text-center">아래에 생성된 단축 URL을 확인하세요.</p>
            <div class="result-box">
                <p><strong>단축된 URL:</strong> <a href="shortened_url">shortened_url</a></p>
                <button class="btn btn-secondary" onclick="copyToClipboard('shortened_url')">클립보드에 복사</button>
            </div>
            <!-- 하단 링크 추가 -->
            <div class="footer-links">
                <a href="index.php">메인 페이지로 돌아가기</a>
            </div>
        </div>
    </div>

    <!-- 광고 배너가 페이지 하단에 고정되며 콘텐츠 아래에 깔림 -->
    <div class="ad-container">
        <ins class="kakao_ad_area" style="display:block;"
        data-ad-unit="DAN-Y0ZNLuIjfBEOujr3"
        data-ad-width="300"
        data-ad-height="250"></ins>
        <script type="text/javascript" src="//t1.daumcdn.net/kas/static/ba.min.js" async></script>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(text) {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            alert("클립보드에 복사되었습니다: " + text);
        }
    </script>
</body>
</html>