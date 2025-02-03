<?php
require_once('../config.php');
session_start();

if (isset($_GET['edit_id'])) {
    $editID = $_GET['edit_id'];
    $result = $conn->query("SELECT * FROM posts WHERE post_id = $editID");
    $post = $result->fetch_assoc();

    // 작성자 확인
    if ($post['author'] !== $_SESSION['username']) {
        echo "<script>alert('수정 권한이 없습니다.'); window.location.href='games.php?type=community';</script>";
        exit();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_SESSION['username'];
    $board_type = $_POST['board_type'];

    // 파일 업로드 처리
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['upload_file']['tmp_name'];
        $fileName = $_FILES['upload_file']['name'];
        $fileSize = $_FILES['upload_file']['size'];
        $fileType = $_FILES['upload_file']['type'];
        
        // 파일 저장 경로 설정
        $uploadFileDir = '../uploads/images/';
        $dest_path = $uploadFileDir . $fileName;

        // 파일 크기 제한 확인 (5MB)
        if ($fileSize > 5 * 1024 * 1024) {
            echo "파일 크기가 5MB를 초과합니다.";
            exit();
        }

        // 허용된 파일 형식 확인
        $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        if (!in_array($fileType, $allowedFileTypes)) {
            echo "허용되지 않는 파일 형식입니다.";
            exit();
        }

        // 파일 이동
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            // 파일이 성공적으로 업로드된 경우
            $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, author = ?, board_type = ?, file_name = ? WHERE post_id = ?");
            $stmt->bind_param("sssssi", $title, $content, $author, $board_type, $fileName, $editID);
        } else {
            echo "파일 업로드에 실패했습니다.";
        }
    } else {
        // 파일이 업로드되지 않은 경우
        $stmt = $conn->prepare("UPDATE posts SET title = ?, content = ?, author = ?, board_type = ? WHERE post_id = ?");
        $stmt->bind_param("ssssi", $title, $content, $author, $board_type, $editID);
    }

    if ($stmt->execute()) {
        header("Location: games.php?type=" . $board_type);
        exit();
    } else {
        echo "게시물 수정에 실패했습니다. 에러: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <title>게시물 수정</title>
</head>
<body>
    <h2>게시물 수정</h2>
    <form method="post" action="edit.php?edit_id=<?php echo $editID; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">제목:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        </div>

        <div class="form-group">
            <label for="content">내용:</label>
            <textarea id="content" name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        </div>

        <div class="form-group">
            <label for="author">작성자:</label>
            <input type="text" id="author" name="author" value="<?php echo htmlspecialchars($post['author']); ?>" readonly>
        </div>

        <div class="form-group">
            <label for="board_type">게시판 선택:</label>
            <select id="board_type" name="board_type">
                <option value="community" <?php echo $post['board_type'] == 'community' ? 'selected' : ''; ?>>커뮤니티</option>
                <?php if ($_SESSION['user_type'] === 'admin'): ?>
                    <option value="notice" <?php echo $post['board_type'] == 'notice' ? 'selected' : ''; ?>>공지사항</option>
                <?php endif; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="upload_file">현재 첨부 파일:</label>
            <?php if (!empty($post['file_name'])): ?>
                <div class="post-file">
                    <h4>첨부 파일:</h4>
                    <a href="../uploads/images/<?php echo htmlspecialchars($post['file_name']); ?>" target="_blank">
                        <?php echo htmlspecialchars($post['file_name']); ?>
                    </a>
                    <button type="button" onclick="deleteFile(<?php echo $editID; ?>)">삭제</button>
                </div>
            <?php else: ?>
                <p>첨부된 파일이 없습니다.</p>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="upload_file">새 파일 첨부:</label>
            <input type="file" id="upload_file" name="upload_file" onchange="validateFile(this)">
            <small>* 허용 파일: jpg, png, gif, pdf, doc, docx (최대 5MB)</small>
        </div>

        <div class="button-container">
            <a href="games.php?type=<?php echo $post['board_type']; ?>" class="btn">목록으로</a>
            <button type="submit">수정하기</button>
        </div>
    </form>

    <script>
        function deleteFile(postId) {
            if (confirm('첨부 파일을 삭제하시겠습니까?')) {
                // AJAX 요청을 통해 파일 삭제 처리
                fetch('delete_file.php?post_id=' + postId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('파일이 삭제되었습니다.');
                            location.reload(); // 페이지 새로고침
                        } else {
                            alert('파일 삭제에 실패했습니다.');
                        }
                    });
            }
        }
    </script>
</body>
</html> 