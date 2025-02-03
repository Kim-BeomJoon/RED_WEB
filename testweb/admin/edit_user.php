<?php
// 데이터베이스 연결
$host = 'localhost'; // 데이터베이스 호스트
$dbname = 'last'; // 데이터베이스 이름
$username = 'test'; // MySQL 사용자 이름
$password = 'test1234'; // MySQL 비밀번호

try {
    // 데이터베이스 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 사용자 ID 가져오기
    $userId = $_GET['username'] ?? null; // null 체크 추가
    if ($userId === null) {
        die("사용자 ID가 없습니다.");
    }

    // 특정 사용자 정보 가져오기
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(1, $userId);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("사용자를 찾을 수 없습니다.");
    }

} catch (PDOException $e) {
    echo "데이터베이스 연결 실패: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>사용자 수정</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="head-ber">
        <a href="../index.php">
            <img src="../images/home_name.png" alt="Good Game Maker" style="max-height: 100px;">
        </a>
    </div>
    <div class="main-editcontent">
        <h2>사용자 수정</h2>
        <form method="POST" action="update_user.php">
            <input type="hidden" name="username" value="<?php echo htmlspecialchars($user['username']); ?>">
            <label for="full_name">이름:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['name']); ?>">
            <br>
            <label for="password">비밀번호:</label>
            <input type="password" name="password" value="<?php echo htmlspecialchars($user['password']); ?>" required style="background-color: #f0f0f0; pointer-events: none;">
            <br>
            <label for="gender">성별:</label>
            <input type="text" name="gender" value="<?php echo htmlspecialchars($user['gender']); ?>">
            <br>
            <label for="birth_date">생년월일:</label>
            <input type="date" name="birth_date" value="<?php echo htmlspecialchars($user['birth_date']); ?>">
            <br>
            <label for="phone_number">전화번호:</label>
            <input type="text" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number']); ?>">
            <br>
            <label for="email">이메일:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
            <br>
            <button type="submit">수정하기</button>
        </form>
    </div>

</body>
</html> 