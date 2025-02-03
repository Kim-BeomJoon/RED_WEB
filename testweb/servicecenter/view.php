<?php
require_once('../config.php');
session_start();

if (!isset($_GET['id'])) {
    header('Location: center.php');
    exit;
}

$post_id = (int)$_GET['id'];
$board_type = isset($_GET['type']) ? $_GET['type'] : 'notice';
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// 게시글 조회
$sql = "SELECT sb.*, u.nickname, u.username, u.email 
        FROM service_board sb 
        LEFT JOIN users u ON sb.user_id = u.id 
        WHERE sb.id = ? AND sb.type = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $post_id, $board_type);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

// 권한 체크
if (!$post || 
    ($post['type'] === 'inquiry' && 
     !$is_admin && 
     (!isset($_SESSION['user_id']) || $_SESSION['user_id'] != $post['user_id']))) {
    echo "<script>
            alert('접근 권한이 없습니다.');
            location.href = 'center.php';
          </script>";
    exit;
}

// 조회수 증가
$sql = "UPDATE service_board SET views = views + 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();

// 댓글 목록 조회
$sql = "SELECT sc.*, u.nickname 
        FROM service_comments sc 
        LEFT JOIN users u ON sc.user_id = u.id 
        WHERE sc.post_id = ? 
        ORDER BY sc.created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$comments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - <?php echo $board_type === 'notice' ? '공지사항' : '1:1 문의'; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container <?php echo $is_admin ? 'with-info-panel' : ''; ?>">
        <div class="sidebar">
        <div class="site-logo">
                <a href="../index.php">
                    <img src="../images/home_name.png" alt="Good Game Maker" class="logo-image">
                </a>
            </div>
            
            <div class="sidebar-menu">
                <h2>고객센터</h2>
                <ul>
                    <li>
                        <a href="center.php?type=notice" class="<?php echo $board_type === 'notice' ? 'active' : ''; ?>">
                            공지사항
                        </a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li>
                        <a href="center.php?type=inquiry" class="<?php echo $board_type === 'inquiry' ? 'active' : ''; ?>">
                            전체 문의
                        </a>
                    </li>
                    <?php else: ?>
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
            <div class="post-header">
                <h1 class="post-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                <?php if ($board_type === 'inquiry'): ?>
                <div class="post-status">
                    <span class="status-badge <?php echo $post['status']; ?>">
                        <?php echo $post['status'] === 'pending' ? '답변대기' : '답변완료'; ?>
                    </span>
                    <?php if ($is_admin): ?>
                    <button onclick="toggleStatus(<?php echo $post['id']; ?>)" class="status-toggle-btn">
                        상태 변경
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <div class="post-info">
                    <span>작성자: <?php 
                        if ($board_type === 'notice') {
                            echo '관리자';
                        } else {
                            echo htmlspecialchars($post['nickname']); 
                        }
                    ?></span> |
                    <span>작성일: <?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?></span> |
                    <span>조회수: <?php echo $post['views']; ?></span>
                </div>
            </div>

            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post['content'])); ?>
            </div>

            <div class="post-actions">
                <!-- 버튼 그룹 -->
                <div class="button-group">
                    <a href="center.php?type=<?php echo $board_type; ?>" class="btn list-btn">목록</a>
                    <?php if (isset($_SESSION['user_id']) && 
                            (($board_type === 'notice' && $is_admin) || 
                             ($board_type === 'inquiry' && $_SESSION['user_id'] == $post['user_id']))): ?>
                        <a href="edit.php?id=<?php echo $post['id']; ?>&type=<?php echo $board_type; ?>" 
                           class="btn edit-btn">수정</a>
                        <a href="delete.php?id=<?php echo $post['id']; ?>&type=<?php echo $board_type; ?>" 
                           class="btn delete-btn" 
                           onclick="return confirm('정말 삭제하시겠습니까?')">삭제</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- 댓글 섹션 -->
            <div class="comments-section">
                <h3 class="comments-title">
                    댓글 <span class="comment-count"><?php echo $comments->num_rows; ?></span>
                </h3>

                <!-- 댓글 작성 폼을 위로 이동 -->
                <?php if ($is_admin || ($loggedIn && $_SESSION['user_id'] == $post['user_id'])): ?>
                <form class="comment-form" method="POST" action="add_comment.php">
                    <input type="hidden" name="post_id" value="<?php echo $post_id; ?>">
                    <textarea name="content" placeholder="댓글을 입력하세요" required></textarea>
                    <button type="submit" class="comment-submit-btn">댓글작성</button>
                </form>
                <?php endif; ?>

                <!-- 댓글 목록 -->
                <?php while ($comment = $comments->fetch_assoc()): ?>
                <div class="comment" id="comment-<?php echo $comment['id']; ?>">
                    <div class="comment-header">
                        <span class="comment-author"><?php echo htmlspecialchars($comment['nickname']); ?></span>
                        <div class="comment-meta">
                            <span class="comment-date"><?php echo date('Y-m-d H:i', strtotime($comment['created_at'])); ?></span>
                            <?php if ($is_admin || (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id'])): ?>
                            <div class="comment-actions">
                                <button onclick="editComment(<?php echo $comment['id']; ?>)" class="comment-btn edit">수정</button>
                                <button onclick="deleteComment(<?php echo $comment['id']; ?>)" class="comment-btn delete">삭제</button>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="comment-content" id="comment-content-<?php echo $comment['id']; ?>">
                        <?php echo nl2br(htmlspecialchars($comment['content'])); ?>
                    </div>
                    <div class="comment-edit-form" id="comment-edit-<?php echo $comment['id']; ?>" style="display: none;">
                        <textarea class="edit-textarea"><?php echo htmlspecialchars($comment['content']); ?></textarea>
                        <div class="edit-actions">
                            <button onclick="saveComment(<?php echo $comment['id']; ?>)" class="comment-btn save">저장</button>
                            <button onclick="cancelEdit(<?php echo $comment['id']; ?>)" class="comment-btn cancel">취소</button>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <?php if ($is_admin && $post['type'] === 'inquiry'): ?>
        <!-- 관리자용 정보 패널 -->
        <div class="info-panel">
            <h3>문의자 정보</h3>
            <div class="info-item">
                <label>회원 ID</label>
                <span><?php echo htmlspecialchars($post['username']); ?></span>
            </div>
            <div class="info-item">
                <label>IP 주소</label>
                <span><?php echo $post['ip_address']; ?></span>
            </div>
            <div class="info-item">
                <label>E-mail</label>
                <span><?php echo htmlspecialchars($post['email']); ?></span>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript 추가 -->
    <?php if ($is_admin): ?>
    <script>
    function toggleStatus(postId) {
        if (confirm('답변 상태를 변경하시겠습니까?')) {
            fetch('toggle_status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('상태 변경에 실패했습니다.');
                }
            });
        }
    }

    function editComment(commentId) {
        document.getElementById(`comment-content-${commentId}`).style.display = 'none';
        document.getElementById(`comment-edit-${commentId}`).style.display = 'block';
    }

    function cancelEdit(commentId) {
        document.getElementById(`comment-content-${commentId}`).style.display = 'block';
        document.getElementById(`comment-edit-${commentId}`).style.display = 'none';
    }

    function saveComment(commentId) {
        const content = document.querySelector(`#comment-edit-${commentId} .edit-textarea`).value;
        
        fetch('edit_comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `comment_id=${commentId}&content=${encodeURIComponent(content)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || '댓글 수정에 실패했습니다.');
            }
        });
    }

    function deleteComment(commentId) {
        if (confirm('정말 삭제하시겠습니까?')) {
            fetch('delete_comment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `comment_id=${commentId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || '댓글 삭제에 실패했습니다.');
                }
            });
        }
    }
    </script>
    <?php endif; ?>
</body>
</html> 