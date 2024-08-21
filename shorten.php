<?php
include 'database.php';

// URL 정리 함수
function cleanUrl($url) {
    // 입력된 URL 앞뒤 공백 제거
    $url = trim($url);

    // http:// 또는 https://로 시작하지 않는 경우 기본적으로 http:// 추가
    if (!preg_match('#^https?://#', $url)) {
        $url = 'http://' . $url;
    }

    return $url;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['original_url'])) {
    $original_url = cleanUrl($_POST['original_url']);  // URL 정리 함수 적용

    // 기존 URL 확인
    $stmt = $pdo->prepare("SELECT short_code FROM urls WHERE original_url = :original_url");
    $stmt->execute(['original_url' => $original_url]);
    $existing_url = $stmt->fetch();

    if ($existing_url) {
        // 기존 단축 URL이 존재하는 경우 해당 URL 반환
        $short_code = $existing_url['short_code'];
    } else {
        // 새로운 단축 URL 생성
        $short_code = substr(md5(uniqid(rand(), true)), 0, 6);  // 예시로 6자리 코드 생성
        $stmt = $pdo->prepare("INSERT INTO urls (original_url, short_code) VALUES (:original_url, :short_code)");
        $stmt->execute(['original_url' => $original_url, 'short_code' => $short_code]);
    }

    // 단축된 URL 표시
    $shortened_url = "http://11e.kr/" . $short_code;
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
            background-color: #f7f9fc;
        }
        .result-container {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .result-box {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 6px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .short-url-input {
            font-size: 1.1rem;
            text-align: center;
            border-radius: 5px 0 0 5px;
        }
        .copy-btn {
            background-color: #007bff;
            color: white;
            border-radius: 0 5px 5px 0;
            transition: background-color 0.3s ease;
            height: 100%;
            padding: 10px 20px;
            border: none;
        }
        .copy-btn:hover {
            background-color: #0056b3;
        }
        #copySuccessMessage {
            margin-top: 15px;
            color: #28a745;
            font-size: 0.9rem;
        }
        .back-btn {
            margin-top: 20px;
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container result-container">
        <div class="result-box">
            <h3 class="mb-4">URL이 성공적으로 단축되었습니다!</h3>
            <p>아래 버튼을 클릭하여 단축된 URL을 클립보드에 복사하세요:</p>
            <div class="input-group mb-3">
                <input type="text" id="shortenedUrl" class="form-control short-url-input" value="<?= htmlspecialchars($shortened_url) ?>" readonly>
                <div class="input-group-append">
                    <button class="btn copy-btn" onclick="copyToClipboard()">복사</button>
                </div>
            </div>
            <div id="copySuccessMessage" style="display: none;">URL이 클립보드에 복사되었습니다!</div>
            <a href="index.php" class="back-btn">메인 페이지로 돌아가기</a>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            var copyText = document.getElementById("shortenedUrl");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // 모바일 대응
            document.execCommand("copy");

            // 성공 메시지 표시
            var successMessage = document.getElementById("copySuccessMessage");
            successMessage.style.display = "block";

            // 잠시 후 성공 메시지 숨기기
            setTimeout(function() {
                successMessage.style.display = "none";
            }, 2000);
        }
    </script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>