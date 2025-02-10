<?php
// 데이터베이스 연결 정보
$servername = "localhost";
$username = "test";  // 이전에 설정한 MySQL 사용자 이름
$password = "test1234";  // 이전에 설정한 MySQL 비밀번호
$dbname = "last";  // 이전에 생성한 데이터베이스 이름

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
