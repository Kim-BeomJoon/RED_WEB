<?php
session_start();

// 오류를 JSON 형식으로 출력하도록 설정
ini_set('display_errors', 1);
error_reporting(E_ALL);

// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'last';
$dbUsername = 'test';
$dbPassword = 'test1234';

$isAdmin = false;

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['username'])) {
        $stmt = $pdo->prepare("SELECT Admin_set FROM users WHERE username = ?");
        $stmt->execute([$_SESSION['username']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $isAdmin = ($user && $user['Admin_set'] === '001');
    }
} catch (PDOException $e) {
    error_log("데이터베이스 오류: " . $e->getMessage());
}

// 관리자 여부를 JSON 형식으로 반환
header('Content-Type: application/json');
echo json_encode(['isAdmin' => $isAdmin]);

?>