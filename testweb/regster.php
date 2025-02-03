<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $username = trim($_POST['username']);
    $name = trim($_POST['name']);
    $birth_date = trim($_POST['birthdate']);
    $phone_number = trim($_POST['phone']);
    $gender = trim($_POST['gender']);
    $nickname = trim($_POST['nickname']);
    $password = trim($_POST['password']);
    $email = trim($_POST['email']);

    try {
        // 아이디 글자 제한 확인
        if (strlen($username) < 8 || strlen($username) > 16) {
            throw new Exception('아이디는 최소 8자, 최대 16자여야 합니다.');
        }

        // 중복 체크 (prepared statement 사용)
        $checkDuplicate = "SELECT username, email FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($checkDuplicate);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if ($row['username'] === $username) {
                throw new Exception('이미 존재하는 아이디입니다. 다른 아이디를 사용해주세요.');
            }
            if ($row['email'] === $email) {
                throw new Exception('이미 존재하는 E-mail입니다. 다른 E-mail을 사용해주세요.');
            }
        }

        // 비밀번호 해시 생성
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // 새 사용자 등록 (기본값이 있는 필드는 제외)
        $sql = "INSERT INTO users (username, password, email, nickname, name, gender, birth_date, phone_number) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $username, $hashedPassword, $email, $nickname, $name, $gender, $birth_date, $phone_number);

        if ($stmt->execute()) {
            echo "<script>
                    alert('회원가입이 성공적으로 완료되었습니다.');
                    window.location.href = 'login.php';
                  </script>";
        } else {
            throw new Exception('회원가입 처리 중 오류가 발생했습니다. 다시 시도해주세요.');
        }

    } catch (Exception $e) {
        echo "<script>
                alert('" . addslashes($e->getMessage()) . "');
                history.back();
              </script>";
    }
}

$conn->close();
?>

