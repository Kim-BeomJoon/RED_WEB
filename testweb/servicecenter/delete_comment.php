<?php
require_once('../config.php');
session_start();

if (!isset($_SESSION['user_id']) || !isset($_POST['comment_id'])) {
    header('Location: center.php');
    exit;
}

$comment_id = (int)$_POST['comment_id'];
$user_id = $_SESSION['user_id'];

// 댓글 권한 확인
$sql = "SELECT sc.*, sb.type FROM service_comments sc 
        JOIN service_board sb ON sc.post_id = sb.id 
        WHERE sc.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comment_id);
$stmt->execute();
$result = $stmt->get_result();
$comment = $result->fetch_assoc();

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

if (!$comment || (!$is_admin && $comment['user_id'] != $user_id)) {
    echo json_encode(['success' => false, 'message' => '삭제 권한이 없습니다.']);
    exit;
}

$sql = "DELETE FROM service_comments WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $comment_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => '댓글 삭제에 실패했습니다.']);
} 