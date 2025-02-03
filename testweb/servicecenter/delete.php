<?php
require_once('../config.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: center.php');
    exit;
}

$post_id = (int)$_GET['id'];
$board_type = isset($_GET['type']) ? $_GET['type'] : 'notice';
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// 게시글 조회
$sql = "SELECT user_id, type FROM service_board WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// 권한 체크
if (!$post || 
    ($post['type'] === 'notice' && !$is_admin) || 
    ($post['type'] === 'inquiry' && $post['user_id'] != $_SESSION['user_id'] && !$is_admin)) {
    echo "<script>
            alert('삭제 권한이 없습니다.');
            history.back();
          </script>";
    exit;
}

// 댓글 삭제 (외래 키 제약조건으로 자동 삭제되지만, 명시적으로 처리)
$sql = "DELETE FROM service_comments WHERE post_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();

// 게시글 삭제
$sql = "DELETE FROM service_board WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);

if ($stmt->execute()) {
    echo "<script>
            alert('게시글이 삭제되었습니다.');
            location.href = 'center.php?type=" . $board_type . "';
          </script>";
} else {
    echo "<script>
            alert('게시글 삭제에 실패했습니다.');
            history.back();
          </script>";
} 