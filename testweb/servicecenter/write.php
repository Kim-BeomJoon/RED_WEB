<?php
require_once('../config.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: center.php');
    exit;
}

$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;
$board_type = isset($_GET['type']) ? $_GET['type'] : 'inquiry';

// 권한 체크
if (($board_type === 'notice' && !$is_admin) || 
    ($board_type === 'inquiry' && $is_admin)) {
    header('Location: center.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    if (!empty($title) && !empty($content)) {
        $sql = "INSERT INTO service_board (user_id, title, content, type, ip_address, user_agent) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssss", $user_id, $title, $content, $board_type, $ip_address, $user_agent);
        
        if ($stmt->execute()) {
            header('Location: center.php?type=' . $board_type);
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
    <title>글 작성 - 고객센터</title>
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
                <h1><?php echo $board_type === 'notice' ? '공지사항 작성' : '1:1 문의하기'; ?></h1>
            </div>
            
            <form class="write-form" method="POST">
                <input type="text" name="title" placeholder="제목을 입력하세요" required>
                <textarea name="content" placeholder="내용을 입력하세요" required></textarea>
                
                <div class="button-group">
                    <button type="submit" class="submit-btn">등록</button>
                    <button type="button" class="cancel-btn" onclick="history.back()">취소</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 