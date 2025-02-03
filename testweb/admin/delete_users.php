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
    if (isset($_POST['userCheckbox']) && !empty($_POST['userCheckbox'])) { // 체크된 사용자 확인
        $usernames = $_POST['userCheckbox'];

        // 참조된 데이터 삭제 (service_comments 테이블에서 해당 사용자 삭제)
        $deleteCommentsQuery = "DELETE FROM service_comments WHERE post_id IN (SELECT id FROM service_board WHERE user_id IN (SELECT id FROM users WHERE username IN (" . implode(',', array_fill(0, count($usernames), '?')) . ")))";
        $stmt = $pdo->prepare($deleteCommentsQuery);
        $stmt->execute($usernames);

        // 참조된 데이터 삭제 (service_board 테이블에서 해당 사용자 삭제)
        $deleteBoardQuery = "DELETE FROM service_board WHERE user_id IN (SELECT id FROM users WHERE username IN (" . implode(',', array_fill(0, count($usernames), '?')) . "))";
        $stmt = $pdo->prepare($deleteBoardQuery);
        $stmt->execute($usernames);

        // 사용자 삭제 쿼리
        $query = "DELETE FROM users WHERE username IN (" . implode(',', array_fill(0, count($usernames), '?')) . ")";
        $stmt = $pdo->prepare($query);
        if ($stmt->execute($usernames)) { // 삭제 성공 여부 확인
            echo "<script>alert('선택한 사용자가 삭제되었습니다.');</script>"; // 삭제 완료 메시지
        } else {
            echo "<script>alert('사용자 삭제 중 오류가 발생했습니다.');</script>"; // 오류 메시지
        }
    } else {
        echo "<script>alert('삭제할 사용자를 선택하세요.');</script>"; // 체크된 사용자가 없을 경우 경고
    }

    // 삭제 완료 후 리다이렉트
    header("Location: admin.php");
    exit();

} catch (PDOException $e) {
    echo "데이터베이스 연결 실패: " . $e->getMessage();
    exit();
}
?> 