<?php
require_once('../config.php');
session_start();

// 관리자 권한 체크
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    echo json_encode(['success' => false]);
    exit;
}

if (isset($_POST['post_id'])) {
    $post_id = (int)$_POST['post_id'];
    
    // 현재 상태 확인
    $sql = "SELECT status FROM service_board WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $post = $result->fetch_assoc();
    
    // 상태 토글
    $new_status = $post['status'] === 'pending' ? 'answered' : 'pending';
    
    // 상태 업데이트
    $sql = "UPDATE service_board SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $post_id);
    
    echo json_encode(['success' => $stmt->execute()]);
} else {
    echo json_encode(['success' => false]);
} 