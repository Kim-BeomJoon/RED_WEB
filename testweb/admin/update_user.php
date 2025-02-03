<?php
// 데이터베이스 연결 설정
$host = 'localhost'; // 데이터베이스 호스트
$dbname = 'last'; // 데이터베이스 이름
$username = 'test'; // MySQL 사용자 이름
$password = 'test1234'; // MySQL 비밀번호

try {
    // 데이터베이스 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // POST 데이터 가져오기
    $userId = $_POST['username'];
    $password = $_POST['password'];
    $fullName = $_POST['name'];
    $gender = $_POST['gender'];
    $birthDate = $_POST['birth_date'];
    $phoneNumber = $_POST['phone_number'];
    $email = $_POST['email'];

    // 사용자 정보 업데이트 쿼리
    $updateQuery = "UPDATE users SET password=?, name=?, gender=?, birth_date=?, phone_number=?, email=? WHERE username=?";
    $stmt = $pdo->prepare($updateQuery);
    $stmt->execute([$password, $fullName, $gender, $birthDate, $phoneNumber, $email, $userId]);

    // 수정 완료 후 리다이렉트
    header("Location: admin.php");
    exit();

} catch (PDOException $e) {
    echo "데이터베이스 연결 실패: " . $e->getMessage();
    exit();
}
?> 