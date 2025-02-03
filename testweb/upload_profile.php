<?php
require_once('config.php');
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// POST 요청 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $updateSuccess = true;
    $message = '';

    // 프로필 이미지 업로드 처리
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] != UPLOAD_ERR_NO_FILE) {
        $file = $_FILES['profile_image'];
        
        // 파일 유효성 검사
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $updateSuccess = false;
            $message .= "JPG, PNG, GIF 형식의 이미지만 업로드 가능합니다.\n";
        } elseif ($file['size'] > $maxSize) {
            $updateSuccess = false;
            $message .= "파일 크기는 5MB를 초과할 수 없습니다.\n";
        } else {
            // 업로드 디렉토리 생성
            $uploadDir = 'uploads/profiles/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            // 파일명 생성
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = $userId . '_' . time() . '.' . $extension;
            $targetPath = $uploadDir . $newFileName;
            
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
                $stmt->bind_param("si", $targetPath, $userId);
                
                if ($stmt->execute()) {
                    $_SESSION['profile_image'] = $targetPath;
                    $message .= "프로필 이미지가 업데이트되었습니다.\n";
                } else {
                    $updateSuccess = false;
                    $message .= "이미지 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
                }
            } else {
                $updateSuccess = false;
                $message .= "파일 업로드 실패: " . error_get_last()['message'] . "\n";
            }
        }
    }

    // 닉네임 업데이트
    if (isset($_POST['nickname']) && !empty($_POST['nickname'])) {
        $nickname = trim($_POST['nickname']);
        
        // 닉네임 중복 검사
        $stmt = $conn->prepare("SELECT id FROM users WHERE nickname = ? AND id != ?");
        $stmt->bind_param("si", $nickname, $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $updateSuccess = false;
            $message .= "이미 사용 중인 닉네임입니다.\n";
        } else {
            $stmt = $conn->prepare("UPDATE users SET nickname = ? WHERE id = ?");
            $stmt->bind_param("si", $nickname, $userId);
            
            if ($stmt->execute()) {
                $_SESSION['nickname'] = $nickname;
                $message .= "닉네임이 업데이트되었습니다.\n";
            } else {
                $updateSuccess = false;
                $message .= "닉네임 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
            }
        }
    }

    // 소개글 업데이트
    if (isset($_POST['bio'])) {
        $bio = trim($_POST['bio']);
        $stmt = $conn->prepare("UPDATE users SET bio = ? WHERE id = ?");
        $stmt->bind_param("si", $bio, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['bio'] = $bio;
            $message .= "소개글이 업데이트되었습니다.\n";
        } else {
            $updateSuccess = false;
            $message .= "소개글 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
        }
    }

    // 이메일 업데이트
    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = trim($_POST['email']);
        
        // 이메일 형식 검증
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $updateSuccess = false;
            $message .= "올바른 이메일 형식이 아닙니다.\n";
        } else {
            // 이메일 중복 검사
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $updateSuccess = false;
                $message .= "이미 사용 중인 이메일입니다.\n";
            } else {
                $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
                $stmt->bind_param("si", $email, $userId);
                
                if ($stmt->execute()) {
                    $_SESSION['email'] = $email;
                    $message .= "이메일이 업데이트되었습니다.\n";
                } else {
                    $updateSuccess = false;
                    $message .= "이메일 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
                }
            }
        }
    }

    // 이름 업데이트
    if (isset($_POST['name']) && !empty($_POST['name'])) {
        $name = trim($_POST['name']);
        $stmt = $conn->prepare("UPDATE users SET name = ? WHERE id = ?");
        $stmt->bind_param("si", $name, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['name'] = $name;
            $message .= "이름이 업데이트되었습니다.\n";
        } else {
            $updateSuccess = false;
            $message .= "이름 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
        }
    }

    // 생년월일 업데이트
    if (isset($_POST['birthdate']) && !empty($_POST['birthdate'])) {
        $birthdate = trim($_POST['birthdate']);
        $stmt = $conn->prepare("UPDATE users SET birthdate = ? WHERE id = ?");
        $stmt->bind_param("si", $birthdate, $userId);
        
        if ($stmt->execute()) {
            $_SESSION['birthdate'] = $birthdate;
            $message .= "생년월일이 업데이트되었습니다.\n";
        } else {
            $updateSuccess = false;
            $message .= "생년월일 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
        }
    }

    // 전화번호 업데이트
    if (isset($_POST['phone']) && !empty($_POST['phone'])) {
        $phone = trim($_POST['phone']);
        if (!preg_match("/^[0-9]{3}-[0-9]{4}-[0-9]{4}$/", $phone)) {
            $updateSuccess = false;
            $message .= "올바른 전화번호 형식이 아닙니다.\n";
        } else {
            $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
            $stmt->bind_param("si", $phone, $userId);
            
            if ($stmt->execute()) {
                $_SESSION['phone'] = $phone;
                $message .= "전화번호가 업데이트되었습니다.\n";
            } else {
                $updateSuccess = false;
                $message .= "전화번호 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
            }
        }
    }

    // 성별 업데이트
    if (isset($_POST['gender']) && !empty($_POST['gender'])) {
        $gender = trim($_POST['gender']);
        if ($gender !== 'M' && $gender !== 'F') {
            $updateSuccess = false;
            $message .= "올바른 성별 값이 아닙니다.\n";
        } else {
            $stmt = $conn->prepare("UPDATE users SET gender = ? WHERE id = ?");
            $stmt->bind_param("si", $gender, $userId);
            
            if ($stmt->execute()) {
                $_SESSION['gender'] = $gender;
                $message .= "성별이 업데이트되었습니다.\n";
            } else {
                $updateSuccess = false;
                $message .= "성별 업데이트 중 오류가 발생했습니다: " . $conn->error . "\n";
            }
        }
    }

    // 세션 정보 새로고침
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $_SESSION['nickname'] = $user['nickname'];
        $_SESSION['profile_image'] = $user['profile_image'];
        $_SESSION['bio'] = $user['bio'];
    }

    if ($updateSuccess) {
        // 세션 정보 새로고침
        $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($user = $result->fetch_assoc()) {
            $_SESSION['nickname'] = $user['nickname'];
            $_SESSION['profile_image'] = $user['profile_image'];
            $_SESSION['bio'] = $user['bio'];
            
            // AJAX 응답인 경우
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                echo json_encode([
                    'success' => true,
                    'message' => '프로필이 업데이트되었습니다.',
                    'data' => [
                        'nickname' => $user['nickname'],
                        'profile_image' => $user['profile_image'],
                        'bio' => $user['bio']
                    ]
                ]);
                exit;
            } else {
                // 일반 폼 제출인 경우
                $_SESSION['update_message'] = "프로필이 성공적으로 업데이트되었습니다.";
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    } else {
        $_SESSION['update_message'] = "프로필 업데이트 중 오류가 발생했습니다.\n" . $message;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>프로필 수정</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 20px;
        }
        .profile-edit-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .profile-image-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 20px;
            display: block;
            object-fit: cover;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .gender-group {
            display: flex;
            gap: 20px;
        }
        .gender-option {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .gender-option input[type="radio"] {
            width: auto;
            margin: 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
            resize: vertical;
        }
        .submit-btn {
            background-color: #17191d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .submit-btn:hover {
            background-color: #0e1013;
        }
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        
        .cancel-btn {
            background-color: #6c757d;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        
        .cancel-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="profile-edit-container">
        <h2>프로필 수정</h2>
        <?php if (isset($_SESSION['update_message'])): ?>
            <div class="alert <?php echo strpos($_SESSION['update_message'], '오류') !== false ? 'alert-error' : 'alert-success'; ?>" style="margin-bottom: 20px; padding: 10px; border-radius: 4px; background-color: <?php echo strpos($_SESSION['update_message'], '오류') !== false ? '#ffe6e6' : '#e6ffe6'; ?>; border: 1px solid <?php echo strpos($_SESSION['update_message'], '오류') !== false ? '#ffcccc' : '#ccffcc'; ?>;">
                <?php 
                echo htmlspecialchars($_SESSION['update_message']); 
                unset($_SESSION['update_message']); 
                ?>
            </div>
        <?php endif; ?>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <form id="profileForm" action="upload_profile.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'images/default_profile.png'); ?>" 
                     alt="프로필 미리보기" 
                     class="profile-image-preview" 
                     id="profile-preview">
                <label for="profile_image">프로필 이미지</label>
                <input type="file" 
                       id="profile_image" 
                       name="profile_image" 
                       accept="image/*" 
                       onchange="previewImage(this)">
            </div>
            
            <div class="form-group">
                <label for="nickname">닉네임</label>
                <input type="text" 
                       id="nickname" 
                       name="nickname" 
                       value="<?php echo htmlspecialchars($_SESSION['nickname'] ?? ''); ?>" 
                       placeholder="닉네임을 입력하세요">
                <span id="nickname-error" style="color: red;"></span>
            </div>

            <div class="form-group">
                <label for="name">이름</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?>" 
                       placeholder="이름을 입력하세요">
            </div>

            <div class="form-group">
                <label for="birthdate">생년월일</label>
                <input type="date" 
                       id="birthdate" 
                       name="birthdate" 
                       value="<?php echo htmlspecialchars($_SESSION['birthdate'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="phone">전화번호</label>
                <input type="tel" 
                       id="phone" 
                       name="phone" 
                       pattern="[0-9]{3}-[0-9]{4}-[0-9]{4}"
                       placeholder="010-0000-0000"
                       value="<?php echo htmlspecialchars($_SESSION['phone'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>성별</label>
                <div class="gender-group">
                    <div class="gender-option">
                        <input type="radio" 
                               id="gender-m" 
                               name="gender" 
                               value="M" 
                               <?php echo (isset($_SESSION['gender']) && $_SESSION['gender'] === 'M') ? 'checked' : ''; ?>>
                        <label for="gender-m">남성</label>
                    </div>
                    <div class="gender-option">
                        <input type="radio" 
                               id="gender-f" 
                               name="gender" 
                               value="F" 
                               <?php echo (isset($_SESSION['gender']) && $_SESSION['gender'] === 'F') ? 'checked' : ''; ?>>
                        <label for="gender-f">여성</label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">이메일</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" 
                       placeholder="이메일을 입력하세요">
                <span id="email-error" style="color: red;"></span>
            </div>
            
            <div class="form-group">
                <label for="bio">소개글</label>
                <textarea id="bio" 
                          name="bio" 
                          placeholder="자기소개를 입력하세요"><?php echo htmlspecialchars($_SESSION['bio'] ?? ''); ?></textarea>
            </div>
            
            <div class="button-group">
                <a href="index.php" class="cancel-btn">취소</a>
                <button type="submit" class="submit-btn">프로필 업데이트</button>
            </div>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-preview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function() {
            let nicknameTimeout;
            let emailTimeout;
            
            $('#nickname').on('input', function() {
                clearTimeout(nicknameTimeout);
                const nickname = $(this).val();
                const originalNickname = '<?php echo htmlspecialchars($_SESSION['nickname'] ?? ''); ?>';
                
                if (nickname === originalNickname) {
                    $('#nickname-error').text('');
                    return;
                }
                
                nicknameTimeout = setTimeout(function() {
                    $.ajax({
                        url: 'check_duplicate.php',
                        method: 'POST',
                        data: { 
                            type: 'nickname',
                            value: nickname 
                        },
                        success: function(response) {
                            if (response.exists) {
                                $('#nickname-error').text('이미 사용 중인 닉네임입니다.');
                            } else {
                                $('#nickname-error').text('');
                            }
                        }
                    });
                }, 500);
            });

            $('#email').on('input', function() {
                clearTimeout(emailTimeout);
                const email = $(this).val();
                const originalEmail = '<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>';
                
                if (email === originalEmail) {
                    $('#email-error').text('');
                    return;
                }
                
                emailTimeout = setTimeout(function() {
                    if (!isValidEmail(email)) {
                        $('#email-error').text('올바른 이메일 형식이 아닙니다.');
                        return;
                    }
                    
                    $.ajax({
                        url: 'check_duplicate.php',
                        method: 'POST',
                        data: { 
                            type: 'email',
                            value: email 
                        },
                        success: function(response) {
                            if (response.exists) {
                                $('#email-error').text('이미 사용 중인 이메일입니다.');
                            } else {
                                $('#email-error').text('');
                            }
                        }
                    });
                }, 500);
            });

            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            $('#profileForm').on('submit', function(e) {
                if ($('#nickname-error').text() || $('#email-error').text()) {
                    e.preventDefault();
                    alert('중복된 닉네임이나 이메일이 있습니다. 다시 확인해주세요.');
                }
            });
        });
    </script>
</body>
</html> 