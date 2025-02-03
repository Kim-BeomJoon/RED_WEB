<?php
require_once('../config.php');
session_start();

if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];

    // 게시물 정보 가져오기
    $stmt = $conn->prepare("SELECT author_id FROM posts WHERE post_id = ?");
    $stmt->bind_param("i", $deleteID);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();

    // 작성자 확인
    if ($post) {
        // 현재 로그인한 사용자 ID
        $current_user_id = $_SESSION['user_id'];

        // 현재 사용자의 관리자 여부 확인
        $stmt = $conn->prepare("SELECT Admin_set FROM users WHERE user_id = ?");
        $stmt->bind_param("i", $current_user_id);
        $stmt->execute();
        $adminResult = $stmt->get_result();
        $adminRow = $adminResult->fetch_assoc();
        $isAdmin = $adminRow['Admin_set'] === '001'; // 관리자 여부 확인

        // 작성자 ID와 현재 사용자 ID 비교
        if ($post['author_id'] === $current_user_id || $isAdmin) {
            // 게시물 삭제
            $stmt = $conn->prepare("DELETE FROM posts WHERE post_id = ?");
            $stmt->bind_param("i", $deleteID);
            if ($stmt->execute()) {
                header("Location: games.php?type=community"); // 삭제 후 리다이렉트
                exit();
            } else {
                echo "게시물 삭제에 실패했습니다. 에러: " . $conn->error;
            }
        } else {
            echo "<script>alert('삭제 권한이 없습니다.'); window.location.href='games.php?type=community';</script>";
        }
    } else {
        header("Location: games.php?type=community");
        exit();
    }
} else {
    header("Location: games.php?type=community");
    exit();
}
?>