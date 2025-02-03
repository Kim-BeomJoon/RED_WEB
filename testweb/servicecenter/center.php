<?php
require_once('../config.php');
session_start();

$loggedIn = isset($_SESSION['user_id']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : $username;
$is_admin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

// 게시판 타입 (공지사항 또는 1:1문의)
$board_type = isset($_GET['type']) ? $_GET['type'] : 'notice';

// 페이징 처리
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// 게시판 별 조회 조건
if ($board_type === 'notice') {
    $where_clause = "sb.type = 'notice'";
} else {
    if ($is_admin) {
        // 관리자는 모든 문의글을 볼 수 있음
        $where_clause = "sb.type = 'inquiry'";
    } else if ($loggedIn) {
        // 일반 회원은 자신의 문의글만 볼 수 있음
        $where_clause = "sb.type = 'inquiry' AND sb.user_id = " . $_SESSION['user_id'];
    } else {
        // 비로그인 상태
        $where_clause = "sb.type = 'inquiry' AND 0";  // 아무것도 보이지 않음
    }
}

// 전체 게시글 수 조회
$sql = "SELECT COUNT(*) as total FROM service_board sb WHERE " . $where_clause;
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$total_posts = $row['total'];
$total_pages = ceil($total_posts / $limit);

// 게시글 목록 조회
$sql = "SELECT sb.*, u.nickname 
        FROM service_board sb 
        LEFT JOIN users u ON sb.user_id = u.id 
        WHERE " . $where_clause . "
        ORDER BY sb.created_at DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>고객센터 - GGM</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+KR:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .logo-image {
            width: 200px;  /* 또는 원하는 크기로 조절 가능 */
            height: auto;  /* 비율 유지 */
        }
    </style>
</head>
<body>
    <div class="container">
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
                        <a href="?type=notice" class="<?php echo $board_type === 'notice' ? 'active' : ''; ?>">
                            공지사항
                        </a>
                    </li>
                    <?php if ($is_admin): ?>
                    <li>
                        <a href="?type=inquiry" class="<?php echo $board_type === 'inquiry' ? 'active' : ''; ?>">
                            전체 문의
                        </a>
                    </li>
                    <?php else: ?>
                    <li>
                        <a href="?type=inquiry" class="<?php echo $board_type === 'inquiry' ? 'active' : ''; ?>">
                            1:1 문의하기
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="main-content">
            <h1>
                <?php 
                if ($board_type === 'notice') {
                    echo '공지사항';
                } else {
                    echo $is_admin ? '전체 문의' : '1:1 문의하기';
                }
                ?>
            </h1>

            <table class="board-table">
                <thead>
                    <tr>
                        <th>번호</th>
                        <th>제목</th>
                        <?php if ($board_type === 'inquiry'): ?>
                        <th>상태</th>
                        <?php endif; ?>
                        <th>작성자</th>
                        <th>작성일</th>
                        <th>조회수</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $counter = $total_posts - ($page - 1) * $limit;
                    while ($row = $result->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?php echo $counter--; ?></td>
                            <td>
                                <a href="view.php?id=<?php echo $row['id']; ?>&type=<?php echo $board_type; ?>">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </a>
                            </td>
                            <?php if ($board_type === 'inquiry'): ?>
                            <td>
                                <span class="status-badge <?php echo $row['status']; ?>">
                                    <?php echo $row['status'] === 'pending' ? '답변대기' : '답변완료'; ?>
                                </span>
                            </td>
                            <?php endif; ?>
                            <td>
                                <?php 
                                if ($board_type === 'notice') {
                                    echo '관리자';
                                } else {
                                    echo htmlspecialchars($row['nickname']); 
                                }
                                ?>
                            </td>
                            <td><?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                            <td><?php echo $row['views']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <div class="board-footer">
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?type=<?php echo $board_type; ?>&page=<?php echo $i; ?>" 
                           class="<?php echo ($page == $i) ? 'current-page' : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>
                </div>

                <?php if (($board_type === 'notice' && $is_admin) || 
                         ($board_type === 'inquiry' && $loggedIn && !$is_admin)): ?>
                    <a href="write.php?type=<?php echo $board_type; ?>" class="write-btn">글쓰기</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- 로그인 안내 모달 추가 -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <h2>로그인 필요</h2>
            <p>고객센터를 이용하기 위해서는 로그인이 필요합니다.</p>
            <div class="modal-buttons">
                <a href="../login.php" class="modal-button login-button">로그인</a>
                <button class="modal-button cancel-button" onclick="closeModal()">취소</button>
            </div>
        </div>
    </div>

    <script>
        // 페이지 로드 시 로그인 체크
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!$loggedIn && $board_type === 'inquiry'): ?>
            showLoginModal();
            <?php endif; ?>
        });

        function showLoginModal() {
            const modal = document.getElementById('loginModal');
            modal.style.display = 'block';
            setTimeout(() => {
                modal.classList.add('show');
            }, 10);
        }

        function closeModal() {
            const modal = document.getElementById('loginModal');
            modal.classList.remove('show');
            setTimeout(() => {
                modal.style.display = 'none';
                window.location.href = 'center.php?type=notice';
            }, 300);
        }

        // 모달 외부 클릭 시 닫기
        window.onclick = function(event) {
            const modal = document.getElementById('loginModal');
            if (event.target == modal) {
                closeModal();
            }
        }

        // ESC 키 누르면 모달 닫기
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeModal();
            }
        });
    </script>

    <?php if (!$loggedIn): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 글쓰기 버튼 클릭 시 모달
            const writeBtn = document.querySelector('.write-btn');
            if (writeBtn) {
                writeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    showLoginModal();
                });
            }

            // 1:1 문의하기 메뉴 클릭 시 모달
            const inquiryLink = document.querySelector('a[href="?type=inquiry"]');
            if (inquiryLink) {
                inquiryLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    showLoginModal();
                });
            }
        });
    </script>
    <?php endif; ?>
</body>
</html> 