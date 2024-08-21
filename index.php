<?php
include 'database.php';

// URL 정리 함수 추가
function cleanUrl($url) {
    // 입력된 URL에서 http:// 또는 https://를 찾아 제거한 후 다시 추가
    $url = trim($url);
    if (stripos($url, 'http://') === 0) {
        $url = substr($url, 7);  // http:// 제거
    } elseif (stripos($url, 'https://') === 0) {
        $url = substr($url, 8);  // https:// 제거
    }
    // http:// 또는 https://가 없는 경우 기본적으로 http://를 추가
    return 'http://' . $url;
}

// URL 단축 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['original_url'])) {
    $original_url = cleanUrl($_POST['original_url']);  // URL 정리 함수 적용

    // 이후 기존의 URL 단축 처리 로직을 여기에 추가합니다.
    // 예: 데이터베이스에 저장, 단축 URL 생성 등.
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URL 단축기</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body, html {
            height: 100%;
        }
        .shortener-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .shortener-box {
            width: 100%;
            max-width: 500px;
            padding: 30px;
            border-radius: 10px;
            background-color: #f8f9fa;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        .instructions {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container shortener-container">
        <div class="shortener-box">
            <h1 class="mb-4 text-center">URL 단축기</h1>
            <p class="text-center">아래에 긴 URL을 입력하고 단축 버튼을 누르세요.</p>
            <!-- 사용 방법 설명 추가 -->
            <div class="instructions">
                <ul>
                    <li>URL을 입력할 때 <code>http://</code> 또는 <code>https://</code>는 자동으로 처리됩니다.</li>
                    <li>입력된 URL이 올바른 형식인지 확인하세요.</li>
                    <li>단축된 URL은 더 짧고 공유하기 쉬운 링크로 제공됩니다.</li>
                </ul>
            </div>
            <form method="POST" action="shorten.php">  <!-- 액션 추가 -->
                <div class="form-group">
                    <input type="text" name="original_url" class="form-control" placeholder="URL을 입력하세요" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block">단축</button>
            </form>
        </div>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>