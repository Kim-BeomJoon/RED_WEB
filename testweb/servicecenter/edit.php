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
$sql = "SELECT * FROM service_board WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// 권한 체크
if (!$post || 
    ($post['type'] === 'notice' && !$is_admin) || 
    ($post['type'] === 'inquiry' && $post['user_id'] != $_SESSION['user_id'])) {
    echo "<script>
            alert('수정 권한이 없습니다.');
            location.href = 'center.php';
          </script>";
    exit;
}

// 수정 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);

    if (!empty($title) && !empty($content)) {
        $sql = "UPDATE service_board SET title = ?, content = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $title, $content, $post_id);
        
        if ($stmt->execute()) {
            echo "<script>
                    alert('게시글이 수정되었습니다.');
                    location.href = 'view.php?id=" . $post_id . "&type=" . $board_type . "';
                  </script>";
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>글 수정 - 고객센터</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <div class="site-logo">
                <a href="../index.php">GGM</a>
            </div>
            
            <div class="sidebar-menu">
                <h2>고객센터</h2>
                <ul>
                    <li>
                        <a href="center.php?type=notice" class="<?php echo $board_type === 'notice' ? 'active' : ''; ?>">
                            공지사항
                        </a>
                    </li>
                    <?php if (!$is_admin): ?>
                    <li>
                        <a href="center.php?type=inquiry" class="<?php echo $board_type === 'inquiry' ? 'active' : ''; ?>">
                            1:1 문의하기
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <div class="page-header">
                <h1><?php echo $board_type === 'notice' ? '공지사항 수정' : '문의글 수정'; ?></h1>
            </div>
            
            <form class="edit-form" method="POST">
                <input type="text" name="title" placeholder="제목을 입력하세요" 
                       value="<?php echo htmlspecialchars($post['title']); ?>" required>
                <textarea name="content" placeholder="내용을 입력하세요" 
                          required><?php echo htmlspecialchars($post['content']); ?></textarea>
                
                <div class="button-group">
                    <button type="submit" class="submit-btn">저장</button>
                    <button type="button" class="cancel-btn" onclick="history.back()">취소</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 