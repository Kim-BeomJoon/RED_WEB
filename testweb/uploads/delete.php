
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
            <a href="games.php?type=notice" class="board-button ">공지사항</a>
            <a href="games.php?type=community" class="board-button active">커뮤니티</a>
        </div>
        <form method="get" action="games.php" class="search-form">
            <input type="hidden" name="type" value="community">
            <input type="text" name="search" placeholder="검색어를 입력하세요" value="" required>
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
            <tr><td>38</td><td><a href='view.php?id=38&type=community'>1234</a></td><td>admin</td><td>2025-01-23 13:05:20</td><td>5</td><td><span>삭제 불가</span> | <a href='edit.php?edit_id=38'>수정</a></td></tr><tr><td>25</td><td><a href='view.php?id=25&type=community'>1빠</a></td><td>jinyeong</td><td>2025-01-23 08:41:31</td><td>8</td><td><span>삭제 불가</span> | <a href='edit.php?edit_id=25'>수정</a></td></tr>        </tbody>
    </table>

    <div class="navigation-container">
        <div class="button-container">
            <button onclick="window.location.href='write.php?type=community'">게시물 작성</button>
            <a href="../index.php" class="ggm-button">GGM</a>
        </div>
        
        <div class="pagination">
            <a href="games.php?type=community&page=1">이전</a>
            <span>1 / 1</span>
            <a href="games.php?type=community&page=1">다음</a>
        </div>
    </div>
</div>

</body>
</html>
