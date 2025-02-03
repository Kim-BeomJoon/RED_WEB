<?php
require_once('../config.php');

// 게시물 ID와 타입 가져오기
$post_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$board_type = isset($_GET['type']) ? $_GET['type'] : 'community';

// 조회수 증가
$updateViews = "UPDATE posts SET views = views + 1 WHERE post_id = ?";
$stmt = $conn->prepare($updateViews);
$stmt->bind_param("i", $post_id);
$stmt->execute();

// 게시물 조회
$query = "SELECT * FROM posts WHERE post_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// 현재 사용자 정보 가져오기 (예: 세션에서)
$current_user_id = $_SESSION['user_id']; // 현재 로그인한 사용자 ID

// 게시물 작성자 ID 가져오기
$post_author_id = $post['author_id']; // 게시물의 작성자 ID

// 게시물이 없는 경우
if (!$post) {
    echo "<script>
        alert('존재하지 않는 게시물입니다.');
        window.location.href = 'games.php?type=" . $board_type . "';
    </script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="board" class="view-page">
        <header>
            <h2>게시물 보기</h2>
        </header>

        <div class="post-container">
            <div class="post-header">
                <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                <div class="post-info">
                    <span>작성자: <?php echo htmlspecialchars($post['author']); ?></span>
                    <span>작성일: <?php echo $post['created_at']; ?></span>
                    <span>조회수: <?php echo $post['views']; ?></span>
                </div>
            </div>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <?php if (!empty($post['file_name'])): ?>
                <div class="post-file">
                    <h4>첨부 파일:</h4>
                    <a href="../uploads/images/<?php echo htmlspecialchars($post['file_name']); ?>" target="_blank">
                        <?php echo htmlspecialchars($post['file_name']); ?>
                    </a>
                </div>
            <?php else: ?>
                <p>첨부된 파일이 없습니다.</p>
            <?php endif; ?>

            <div class="button-container">
                <a href="games.php?type=<?php echo $board_type; ?>" class="btn">목록</a>
                <a href="edit.php?edit_id=<?php echo $post_id; ?>" class="btn">수정</a>
                <?php if ($current_user_id && $current_user_id === $post_author_id): ?> <!-- 올린 사용자만 삭제 버튼 표시 -->
                    <a href="javascript:void(0)" onclick="confirmDelete(<?php echo $post_id; ?>, '<?php echo $board_type; ?>')" class="btn">삭제</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(postId, boardType) {
        if(confirm('정말 이 게시물을 삭제하시겠습니까?')) {
            window.location.href = 'games.php?type=' + boardType + '&delete_id=' + postId;
        }
    }
    </script>
</body>
</html>