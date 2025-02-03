<?php
session_start();
if (isset($_SESSION['user_id'])) {
    // 로그인된 경우 세션 제거
    session_destroy();
    echo "<script>
            alert('로그아웃되었습니다.');
            window.location.href = 'index.php';
          </script>";
} else {
    header("Location: index.php");
}
?>
