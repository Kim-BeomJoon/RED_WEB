<?php
header('Content-Type: application/json');

// 데이터베이스 연결 설정
$host = 'localhost';
$dbname = 'last';
$username = 'test'; // MySQL 사용자 이름
$password = 'test1234';     // MySQL 비밀번호

try {
    // 데이터베이스 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 사용자 데이터 조회
    $stmt = $pdo->query("SELECT username, password, nickname, email, Admin_set, last_login , name , gender , birth_date , phone_number, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // JSON 형태로 반환
    echo json_encode($users);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
