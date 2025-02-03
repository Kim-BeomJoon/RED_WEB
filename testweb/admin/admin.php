<?php
// 세션 시작
session_start();

// 데이터베이스 연결 설정
$host = 'localhost'; // 데이터베이스 호스트
$dbname = 'last'; // 데이터베이스 이름
$dbUsername = 'test'; // MySQL 사용자 이름
$dbPassword = 'test1234'; // MySQL 비밀번호

// 로그인 상태 확인
$loggedIn = isset($_SESSION['user_id']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';




try {
    // 데이터베이스 연결
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Admin_set 값 확인
    if ($loggedIn) {
        $stmt = $pdo->prepare("SELECT Admin_set FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user || $user['Admin_set'] !== '001') {
            echo "<script>alert('관리자 권한이 없습니다.'); window.location.href='../index.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('로그인이 필요합니다.'); window.location.href='../login.php';</script>";
        exit();
    }

    // 사용자 삭제 처리
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_users']) && isset($_POST['userCheckbox'])) {
        $usersToDelete = $_POST['userCheckbox'];
        $placeholders = rtrim(str_repeat('?,', count($usersToDelete)), ',');
        $stmt = $pdo->prepare("DELETE FROM users WHERE username IN ($placeholders)");
        if ($stmt->execute($usersToDelete)) {
            echo "<script>alert('선택한 사용자가 삭제되었습니다.');</script>";
        } else {
            echo "<script>alert('사용자 삭제 중 오류가 발생했습니다.');</script>";
        }
    }

    // 비밀번호 변경 처리
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password']) && isset($_POST['username']) && isset($_POST['new_password'])) {
        $usernameToChange = $_POST['username'];
        $newPassword = $_POST['new_password'];

        // 비밀번호 유효성 검사
        if (strlen($newPassword) < 8) {
            echo "<script>alert('비밀번호는 최소 8자 이상이어야 합니다.');</script>";
        } else {
            // 비밀번호를 해시하여 저장
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            // 데이터베이스 업데이트
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = ?");
            if ($stmt->execute([$hashedPassword, $usernameToChange])) {
                echo "<script>alert('비밀번호가 성공적으로 변경되었습니다.');</script>";
            } else {
                echo "<script>alert('비밀번호 변경 중 오류가 발생했습니다.');</script>";
            }
        }
    }

    // 사용자 데이터 조회
    $stmt = $pdo->query("SELECT username, password, name, gender, birth_date, phone_number, email, created_at FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <title>Administrator Page</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const visitCounterElement = document.getElementById('visitCounter');
        const today = new Date().toISOString().split('T')[0];

        // 오늘의 방문자 수 관리
        const visitorCountKey = 'visitorCount';
        const todayKey = 'lastVisitDate';
        let visitorCount = localStorage.getItem(visitorCountKey) || 0;

        // 오늘 처음 방문한 경우
        if (localStorage.getItem(todayKey) !== today) {
            visitorCount = parseInt(visitorCount) + 1;
            localStorage.setItem(visitorCountKey, visitorCount);
            localStorage.setItem(todayKey, today);
        }

        // 방문자 수 표시
        if (visitCounterElement) {
            visitCounterElement.textContent = `오늘의 방문자 수: ${visitorCount}`;
        }
    });

    function toggleCheckboxes(source) {
        const checkboxes = document.querySelectorAll('input[name="userCheckbox[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    function confirmDelete() {
        const checkboxes = document.querySelectorAll('input[name="userCheckbox[]"]:checked');
        if (checkboxes.length === 0) {
            alert('삭제할 사용자를 선택하세요.');
            return false;
        }
        return confirm('삭제하시겠습니까?');
    }

    function confirmChangePassword() {
        return confirm('비밀번호를 변경하시겠습니까?');
    }
    </script>
</head>
<body>
    <div class="header">
        <a href="../index.php">
            <img src="../images/home_name.png" alt="Good Game Maker" style="max-height: 100px;">
        </a>
        <div id="visitCounter" style="float: right; margin-top: 20px; font-size: 16px; color: #333;"></div>
    </div>

    <div class="main-content">
        <h2>사용자 목록</h2>

        <!-- 사용자 삭제 폼 -->
        <form method="POST" onsubmit="return confirmDelete();">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleCheckboxes(this)"> 모두 체크</th>
                        <th>계정명</th>
                        <th>비밀번호</th>
                        <th>이름</th>
                        <th>성별</th>
                        <th>생년월일</th>
                        <th>전화번호</th>
                        <th>이메일</th>
                        <th>계정 생성일</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><input type="checkbox" name="userCheckbox[]" value="<?php echo htmlspecialchars($user['username']); ?>"></td>
                            <td><a href="edit_user.php?username=<?php echo urlencode($user['username']); ?>"><?php echo htmlspecialchars($user['username']); ?></a></td>
                            <td><?php echo htmlspecialchars($user['password']); ?></td>
                            <td><?php echo htmlspecialchars($user['name'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['gender'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['birth_date'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['phone_number'] ?: '-'); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['created_at'] ?: '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <!-- 삭제 버튼 -->
            <button type="submit" name="delete_users">선택한 사용자 삭제</button>
        </form>
    </div>
</body>
</html>
