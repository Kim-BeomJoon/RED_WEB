<?php
require_once('../config.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['post_id']) || !isset($_POST['content'])) {
    header('Location: center.php');
    exit;
}

$post_id = (int)$_POST['post_id'];
$user_id = $_SESSION['user_id'];
$content = trim($_POST['content']);

// 게시글 존재 여부 및 권한 확인
$sql = "SELECT type, user_id FROM service_board WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// 권한 체크 (관리자이거나 본인 글인 경우만 댓글 작성 가능)
if (!$post || 
    (!$is_admin && $post['user_id'] != $_SESSION['user_id'])) {
    echo "<script>
            alert('댓글을 작성할 권한이 없습니다.');
            history.back();
          </script>";
    exit;
}

if (!empty($content)) {
    $sql = "INSERT INTO service_comments (post_id, user_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $post_id, $user_id, $content);
    
    if ($stmt->execute()) {
        header('Location: view.php?id=' . $post_id . '&type=' . $post['type']);
        exit;
    }
}

echo "<script>
        alert('댓글 작성에 실패했습니다.');
        history.back();
      </script>"; 