<?php
// config.php 파일 수정

// 기존 DB 연결 설정 유지
$servername = "localhost";
$username = "test";
$password = "test1234";
$dbname = "last";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// 세션 보안 설정
ini_set('session.use_strict_mode', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.gc_maxlifetime', 600);  // 10분
session_set_cookie_params(600);          // 10분
session_start();

// 새로운 보안 세션 시작 함수
function initSecureSession($user_id, $username) {
    // 기존 세션 삭제
    session_unset();
    session_destroy();
    
    // 새 세션 시작
    session_start();
    
    // 새로운 세션 ID 생성
    session_regenerate_id(true);
    
    // 세션 데이터 설정
    $_SESSION['user_id'] = $user_id;
    $_SESSION['username'] = $username;
    $_SESSION['LAST_ACTIVITY'] = time();
    $_SESSION['IP_ADDRESS'] = $_SERVER['REMOTE_ADDR'];
}

// 세션 유효성 검사 함수
function checkSessionTimeout() {
    if (!isset($_SESSION['LAST_ACTIVITY'])) {
        $_SESSION['LAST_ACTIVITY'] = time();  // 세션 시작 시간 설정
        return true;  // 새로운 세션은 유효함
    }
    
    if (time() - $_SESSION['LAST_ACTIVITY'] > 600) {
        session_unset();
        session_destroy();
        session_start();
        header("Location: login.php?msg=session_expired");
        exit();
    }
    
    // IP 주소 검증
    if (isset($_SESSION['IP_ADDRESS']) && $_SESSION['IP_ADDRESS'] !== $_SERVER['REMOTE_ADDR']) {
        session_unset();
        session_destroy();
        header("Location: login.php?msg=invalid_session");
        exit();
    }
    
    $_SESSION['LAST_ACTIVITY'] = time();
    return true;
}

// 로그인 상태 확인이 필요한 모든 페이지에서 즉시 체크
$current_page = basename($_SERVER['PHP_SELF']);
if (isset($_SESSION['user_id']) && $current_page !== 'login.php') {
    checkSessionTimeout();  // 반환값 체크 제거
}
?>