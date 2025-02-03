<?php
session_start();
require_once('../config.php');

// 게시판 타입 설정
$board_type = isset($_GET['type']) ? $_GET['type'] : 'community';

// 페이지네이션 설정
$limit = 5; // 한 페이지에 보여줄 게시물 수
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// 검색어 처리
$search = isset($_GET['search']) ? $_GET['search'] : '';

// 게시물 삭제 처리
if (isset($_GET['delete_id'])) {
    $deleteID = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM posts WHERE post_id = ?";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bind_param("i", $deleteID);
    
    echo "<script>
        if(confirm('정말 이 게시물을 삭제하시겠습니까?')) {";
    
    if ($stmt->execute()) {
        echo "
            alert('게시물이 성공적으로 삭제되었습니다.');
            window.location.href = 'games.php?type=" . $board_type . "';";
    } else {
        echo "
            alert('게시물 삭제에 실패했습니다. 에러: " . $conn->error . "');
            window.location.href = 'games.php?type=" . $board_type . "';";
    }
    

    
    echo "
        } else {
            window.location.href = 'games.php?type=" . $board_type . "';
        }
    </script>";
    exit();
}

// 게시물 조회
$totalQuery = "SELECT COUNT(*) as total FROM posts WHERE board_type = ? AND (title LIKE ? OR content LIKE ?)";
$searchTerm = '%' . $search . '%';
$stmt = $conn->prepare($totalQuery);
$stmt->bind_param("sss", $board_type, $searchTerm, $searchTerm);
$stmt->execute();
$totalResult = $stmt->get_result();
$totalRow = $totalResult->fetch_assoc();
$totalPosts = $totalRow['total'];
$totalPages = ceil($totalPosts / $limit);

$query = "SELECT p.*, u.nickname FROM posts p JOIN users u ON p.author = u.username WHERE p.board_type = ? AND (p.title LIKE ? OR p.content LIKE ?) ORDER BY p.created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssiii", $board_type, $searchTerm, $searchTerm, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// 게시물 작성 여부를 확인하는 쿠키가 존재하는지 검사
if (isset($_COOKIE['post_created']) && $_COOKIE['post_created'] === 'true') {
    echo "<p style='color: green;'>게시물이 성공적으로 작성되었습니다.</p>";
    // 쿠키를 삭제하여 다음 요청에 영향을 미치지 않도록 함
    setcookie('post_created', '', time() - 3600, '/');
}

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시판</title>
    <link rel="stylesheet" href="styles.css"> <!-- 스타일 시트 링크 -->
    <script>
    function confirmDelete(postId, boardType) {
        if(confirm('정말 이 게시물을 삭제하시겠습니까?')) {
            window.location.href = 'games.php?type=' + boardType + '&delete_id=' + postId;
        }
    }
    </script>
</head>
<body>

<div id="board">
    <header>
    <img src="../images/home_name.png" alt="Good Game Maker" style="max-height: 100px;">

        <!-- 게시판 선택 버튼 추가 -->
        <div class="board-type-buttons">
            <a href="games.php?type=notice" class="board-button <?php echo $board_type == 'notice' ? 'active' : ''; ?>">공지사항</a>
            <a href="games.php?type=community" class="board-button <?php echo $board_type == 'community' ? 'active' : ''; ?>">커뮤니티</a>
        </div>
        <form method="get" action="games.php" class="search-form">
            <input type="hidden" name="type" value="<?php echo $board_type; ?>">
            <input type="text" name="search" placeholder="검색어를 입력하세요" value="<?php echo htmlspecialchars($search); ?>" required>
            <button type="submit">검색</button>
        </form>
    </header>

    <table>
        <thead>
            <tr>
                <th>번호</th>
                <th>제목</th>
                <th>아이디</th>
                <th>작성일</th>
                <th>조회수</th>
                <th>작업</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['post_id']}</td>";
                    echo "<td><a href='view.php?id={$row['post_id']}&type={$board_type}'>{$row['title']}</a></td>";
                    echo "<td>{$row['nickname']}</td>";
                    echo "<td>{$row['created_at']}</td>";
                    echo "<td>{$row['views']}</td>";
                    echo "<td>";
                    if ($row['author'] === $_SESSION['username']):
                        echo "<a href='javascript:void(0)' onclick='confirmDelete({$row['post_id']}, \"$board_type\")'>삭제</a>";
                    else:
                        echo "<span>삭제 불가</span>";
                    endif;
                    echo " | <a href='edit.php?edit_id={$row['post_id']}'>수정</a></td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6'>게시물이 없습니다.</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <div class="navigation-container">
        <div class="button-container">
            <button onclick="window.location.href='upload.php?type=<?php echo $board_type; ?>'">게시물 작성</button>
            <a href="../index.php" class="ggm-button">GGM</a>
        </div>
        
        <div class="pagination">
            <a href="games.php?type=<?php echo $board_type; ?>&page=<?php echo max(1, $page - 1); ?>">이전</a>
            <span><?php echo $page; ?> / <?php echo $totalPages; ?></span>
            <a href="games.php?type=<?php echo $board_type; ?>&page=<?php echo min($totalPages, $page + 1); ?>">다음</a>
        </div>
    </div>
</div>

</body>
</html>
