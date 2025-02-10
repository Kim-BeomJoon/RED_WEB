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

    // 체크된 사용자 삭제
    if (isset($_POST['userCheckbox']) && !empty($_POST['userCheckbox'])) {
        $usernames = $_POST['userCheckbox'];
        
        // 트랜잭션 시작
        $pdo->beginTransaction();

        try {
            // 1. 먼저 service_comments 테이블의 데이터 삭제
            $deleteCommentsQuery = "DELETE FROM service_comments WHERE post_id IN (SELECT id FROM service_board WHERE user_id IN (SELECT id FROM users WHERE username IN (" . implode(',', array_fill(0, count($usernames), '?')) . ")))";
            $stmt = $pdo->prepare($deleteCommentsQuery);
            $stmt->execute($usernames);

            // 2. service_board 테이블의 데이터 삭제
            $deleteBoardQuery = "DELETE FROM service_board WHERE user_id IN (SELECT id FROM users WHERE username IN (" . implode(',', array_fill(0, count($usernames), '?')) . "))";
            $stmt = $pdo->prepare($deleteBoardQuery);
            $stmt->execute($usernames);

            // 3. 마지막으로 users 테이블에서 사용자 삭제
            $deleteUsersQuery = "DELETE FROM users WHERE username IN (" . implode(',', array_fill(0, count($usernames), '?')) . ")";
            $stmt = $pdo->prepare($deleteUsersQuery);
            $stmt->execute($usernames);

            // 트랜잭션 커밋
            $pdo->commit();
            
            echo "<script>alert('선택한 사용자와 관련 데이터가 모두 삭제되었습니다.');</script>";
        } catch (Exception $e) {
            // 오류 발생 시 롤백
            $pdo->rollBack();
            throw $e;
        }
    } else {
        echo "<script>alert('삭제할 사용자를 선택하세요.');</script>";
    }

    // 리다이렉트
    header("Location: admin.php");
    exit();

} catch (PDOException $e) {
    echo "데이터베이스 오류: " . $e->getMessage();
    exit();
}
?> 