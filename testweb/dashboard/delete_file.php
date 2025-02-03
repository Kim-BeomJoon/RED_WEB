<?php
require_once('../config.php');
session_start();

if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];

    // 게시물의 파일 경로 가져오기
    $result = $conn->query("SELECT file_name FROM posts WHERE post_id = $postId");
    $post = $result->fetch_assoc();

    if ($post && !empty($post['file_name'])) {
        // 파일 경로 설정
        $filePath = '../uploads/images/' . $post['file_name']; // 파일 경로 수정

        // 파일 삭제
        if (unlink($filePath)) {
            // 데이터베이스에서 파일 경로 삭제
            $stmt = $conn->prepare("UPDATE posts SET file_name = NULL WHERE post_id = ?");
            $stmt->bind_param("i", $postId);
            $stmt->execute();

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?> 