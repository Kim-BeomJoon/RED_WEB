<?php
require_once('../config.php');
session_start(); // 세션 시작

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 데이터 처리
    $title = $_POST['title'];
    $content = $_POST['content'];
    $author = $_SESSION['username'];
    $board_type = $_POST['board_type'];

    // 게시판 유형에 따라 사용자 권한 확인
    if ($board_type === 'notice') {
        $stmt = $conn->prepare("SELECT Admin_set FROM users WHERE username = ?");
        $stmt->bind_param("s", $author);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user['Admin_set'] !== '001') {
            echo "<script>alert('공지사항 게시판에는 관리자만 게시물을 작성할 수 있습니다.'); window.location.href='upload.php';</script>";
            exit; // 권한이 없으면 종료
        }
    }

    // 파일 업로드 처리
    // URL을 통한 파일 다운로드 부분
if (isset($_POST['file_url'])) {
    $file_url = $_POST['file_url'];
    $fileName = basename($file_url);  // URL에서 파일명 추출
    $uploadFileDir = '../uploads/images/';
    
    // URL에서 파일을 가져오기
    $fileContent = file_get_contents($file_url);  // URL에서 파일 콘텐츠 가져오기

    // 파일이 정상적으로 다운로드되었는지 확인
    if ($fileContent === false) {
        echo "<script>alert('업로드를 할 URL이 없습니다.');</script>";
    } else {
        // 파일을 서버에 저장
        $filePath = $uploadFileDir . $fileName;
        file_put_contents($filePath, $fileContent);  // 파일을 저장

        echo "<script>alert('파일이 성공적으로 업로드되었습니다.');</script>";
    }
}
    $fileName = null; // 파일 이름 초기화
    if (isset($_FILES['upload_file']) && $_FILES['upload_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['upload_file']['tmp_name'];
        $fileName = $_FILES['upload_file']['name'];
        $fileSize = $_FILES['upload_file']['size'];
        $fileType = $_FILES['upload_file']['type'];
        
        $maxSize = 5 * 1024 * 1024; // 5MB
        $allowedTypes = [ 'image/jpg', 'image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 
                         'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        // 파일 크기 및 형식 확인
        if ($fileSize > $maxSize) {
            echo "<script>alert('파일 크기 초과: 최대 5MB입니다.');</script>";
        } elseif (!in_array($fileType, $allowedTypes)) {
            echo "<script>alert('허용되지 않는 파일 형식입니다.');</script>";
        } else {
            $uploadFileDir = '../uploads/images/';
            $dest_path = $uploadFileDir . $fileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // 데이터베이스에 게시물 저장
                $stmt = $conn->prepare("INSERT INTO posts (title, content, author, board_type, file_name, created_at, views) VALUES (?, ?, ?, ?, ?, NOW(), 0)");
                $stmt->bind_param("sssss", $title, $content, $author, $board_type, $fileName);
                if ($stmt->execute()) {
                    echo "<script>alert('게시물이 작성되었습니다.'); window.location.href='games.php?type=$board_type';</script>";
                } else {
                    echo "<script>alert('게시물 작성에 실패했습니다.');</script>";
                }
            } else {
                echo "<script>alert('파일 업로드에 실패했습니다.');</script>";
            }
        }
    } else {
        // 파일이 첨부되지 않은 경우에도 게시물 저장
        $stmt = $conn->prepare("INSERT INTO posts (title, content, author, board_type, file_name, created_at, views) VALUES (?, ?, ?, ?, NULL, NOW(), 0)");
        $stmt->bind_param("ssss", $title, $content, $author, $board_type);
        if ($stmt->execute()) {
            echo "<script>alert('게시물이 작성되었습니다.'); window.location.href='games.php?type=$board_type';</script>";
        } else {
            echo "<script>alert('게시물 작성에 실패했습니다.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>게시물 작성</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    function validateFile(input) {
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 
                            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        
        if (input.files[0]) {
            if (input.files[0].size > maxSize) {
                alert('파일 크기는 5MB를 초과할 수 없습니다.');
                input.value = '';
                return false;
            }
            
            if (!allowedTypes.includes(input.files[0].type)) {
                alert('허용되지 않는 파일 형식입니다. (jpg, png, gif, pdf, doc, docx만 가능)');
                input.value = '';
                return false;
            }
        }
        return true;
    }
    </script>
</head>
<body>
    <div id="board" class="edit-page">
        <header>
            <h2>게시물 작성</h2>
        </header>

        <form method="post" action="" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">제목:</label>
                <input type="text" id="title" name="title" maxlength="30" required>
            </div>

            <div class="form-group">
                <label for="content">내용:</label>
                <textarea id="content" name="content" required></textarea>
            </div>

            <div class="form-group">
                <label for="author">작성자:</label>
                <input type="text" id="author" name="author" value="<?php echo $_SESSION['username']; ?>" readonly>
            </div>

            <div class="form-group">
                <label for="board_type">게시판 선택:</label>
                <select id="board_type" name="board_type" required>
                    <option value="">-- 목록 --</option>
                    <option value="community">커뮤니티</option>
                    <option value="notice">공지사항</option>
                </select>
            </div>

            <div class="form-group">
                <label for="upload_file">파일 첨부:</label>
                <input type="file" id="upload_file" name="upload_file" onchange="validateFile(this)">
                <small>* 허용 파일: jpg, png, gif, pdf, doc, docx (최대 5MB)</small>
            </div>

            <div class="form-group">
                <label for="file_url">파일 URL:</label>
                <input type="url" id="file_url" name="file_url" placeholder="http://example.com/file.png">
                <small>* URL에서 직접 파일을 가져옵니다.</small>
            </div>

            <div class="button-container">
                <a href="games.php?type=community" class="btn">목록으로</a>
                <button type="submit">작성하기</button>
            </div>
        </form>
    </div>
</body>
</html>
