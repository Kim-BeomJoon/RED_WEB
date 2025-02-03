
<!DOCTYPE html>
<html lang="kr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Page</title>
    <link rel="stylesheet" href="styles.css">
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const visitCounterElement = document.getElementById('visitCounter');
        const today = new Date().toISOString().split('T')[0];

        // 오늘의 방문자 수 관리
        const visitorCountKey = 'visitorCount';
        const todayKey = 'lastVisitDate';
        let visitorCount = localStorage.getItem(visitorCountKey) || 0;

        // 오늘 처음 방문한 경우
        if (localStorage.getItem(todayKey) !== today) {
            visitorCount = parseInt(visitorCount) + 1;
            localStorage.setItem(visitorCountKey, visitorCount);
            localStorage.setItem(todayKey, today);
        }

        // 방문자 수 표시
        if (visitCounterElement) {
            visitCounterElement.textContent = `오늘의 방문자 수: ${visitorCount}`;
        }
    });

    function toggleCheckboxes(source) {
        const checkboxes = document.querySelectorAll('input[name="userCheckbox[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = source.checked;
        });
    }

    function confirmDelete() {
        const checkboxes = document.querySelectorAll('input[name="userCheckbox[]"]:checked');
        if (checkboxes.length === 0) {
            alert('삭제할 사용자를 선택하세요.');
            return false;
        }
        return confirm('삭제하시겠습니까?');
    }

    function confirmChangePassword() {
        return confirm('비밀번호를 변경하시겠습니까?');
    }
    </script>
</head>
<body>
    <div class="header">
        <a href="../index.php">
            <img src="../images/home_name.png" alt="Good Game Maker" style="max-height: 100px;">
        </a>
        <div id="visitCounter" style="float: right; margin-top: 20px; font-size: 16px; color: #333;"></div>
    </div>

    <div class="main-content">
        <h2>사용자 목록</h2>

        <!-- 사용자 삭제 폼 -->
        <form method="POST" onsubmit="return confirmDelete();">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="toggleCheckboxes(this)"> 모두 체크</th>
                        <th>계정명</th>
                        <th>비밀번호</th>
                        <th>이름</th>
                        <th>성별</th>
                        <th>생년월일</th>
                        <th>전화번호</th>
                        <th>이메일</th>
                        <th>계정 생성일</th>
                        <th>비밀번호 변경</th>
                    </tr>
                </thead>
                <tbody>
                                            <tr>
                            <td><input type="checkbox" name="userCheckbox[]" value="adsupermin"></td>
                            <td>adsupermin</td>
                            <td>$2y$10$q8pqLlXnzq2A2u07WnMIXuhQymk3I2gxifiIZDu5mlIB7ZuD75.OC</td>
                            <td>-</td>
                            <td>-</td>
                            <td>0000-00-00</td>
                            <td>-</td>
                            <td></td>
                            <td>2025-01-22 13:47:17</td>
                            <td>
                                <!-- 비밀번호 변경 폼 -->
                                <form method="POST" style="display:inline;" onsubmit="return confirmChangePassword();">
                                    <input type="hidden" name="username" value="adsupermin">
                                    <input type="password" name="new_password" placeholder="새 비밀번호" required>
                                    <button type="submit" name="change_password">변경</button>
                                </form>
                            </td>
                        </tr>
                                            <tr>
                            <td><input type="checkbox" name="userCheckbox[]" value="jinyeong"></td>
                            <td>jinyeong</td>
                            <td>$2y$10$4LwzDKWmxV8K1pGySFg2iOYA4u9U.mQdMjTAV6IzgKx2YHcuJfpN.</td>
                            <td>장진영</td>
                            <td>M</td>
                            <td>1997-06-19</td>
                            <td>010-1234-5678</td>
                            <td>qwer1234@ggm.com</td>
                            <td>2025-01-23 08:39:50</td>
                            <td>
                                <!-- 비밀번호 변경 폼 -->
                                <form method="POST" style="display:inline;" onsubmit="return confirmChangePassword();">
                                    <input type="hidden" name="username" value="jinyeong">
                                    <input type="password" name="new_password" placeholder="새 비밀번호" required>
                                    <button type="submit" name="change_password">변경</button>
                                </form>
                            </td>
                        </tr>
                                            <tr>
                            <td><input type="checkbox" name="userCheckbox[]" value="testtets"></td>
                            <td>testtets</td>
                            <td>$2y$10$rbFffVtdaaJ9QkhStR2x0epDfwrpfOhF0fOIz1NXtTxzPyqx/plne</td>
                            <td>test</td>
                            <td>M</td>
                            <td>0000-00-00</td>
                            <td>999-9999-9999</td>
                            <td>asdf@asdf.com</td>
                            <td>2025-01-23 11:17:15</td>
                            <td>
                                <!-- 비밀번호 변경 폼 -->
                                <form method="POST" style="display:inline;" onsubmit="return confirmChangePassword();">
                                    <input type="hidden" name="username" value="testtets">
                                    <input type="password" name="new_password" placeholder="새 비밀번호" required>
                                    <button type="submit" name="change_password">변경</button>
                                </form>
                            </td>
                        </tr>
                                    </tbody>
            </table>
        </form>

        <!-- 삭제 버튼 -->
        <form method="POST" onsubmit="return confirmDelete();">
            <button type="submit" name="delete_users">선택한 사용자 삭제</button>
        </form>
    </div>
</body>
</html>
