<?php 
require_once('config.php');
session_start();

// 로그인 상태 확인
$loggedIn = isset($_SESSION['user_id']);
$username = isset($_SESSION['username']) ? $_SESSION['username'] : '';
$nickname = isset($_SESSION['nickname']) ? $_SESSION['nickname'] : $username;  // 닉네임 추가

// 사용자 bio 가져오기
$bio = '';
if ($loggedIn) {
    $stmt = $conn->prepare("SELECT bio FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $bio = $row['bio'];
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="즐거운 게임을 한 곳에서 만나보세요." />
    <meta property="og:title" content="Last Game Site" />
    <meta property="og:description" content="즐거운 게임을 한 곳에서 만나보세요." />
    <title>GGM</title>
    <link rel="stylesheet" href="index_css/style.css">
</head>
<body>
    <header>
        <img src="images/home_name.png" alt="Good Game Maker" style="max-height: 100px;">
    </header>

    <nav>
        <a href="#">게임</a>
        <a href="dashboard/games.php">게시판</a>
        <a href="servicecenter/center.php">고객센터</a>
        <a href="admin/admin.php" id="admin-link" style="display: none;">관리자 페이지</a>
    </nav>

    <div class="main-content">
        <section class="featured-games">
            <h2>전체 게임</h2>
            <p>최고의 게임을 만나보세요!</p>
            
            <div class="game-grid">
                <!-- 게임 카드 예시 -->
                <div class="game-card">
                    <a href="https://store.steampowered.com/app/289070/Sid_Meiers_Civilization_VI/" target="_blank">
                        <img src="images/Sid_Meier's_Civilization_VI.jpg" alt="게임 1">
                        <div class="game-info">
                            <h3>Sid Meier's Civilization VI</h3>
                            <p>오랜 기간 동안 쌓아온 역사를 조작하고 확장하세요.</p>
                        </div>
                    </a>
                </div>
                <div class="game-card">
                    <a href="https://www.googleadservices.com/pagead/aclk?sa=L&ai=DChcSEwj1hpr06oWLAxVOCXsHHQRtFb8YABAAGgJ0bQ&ae=2&aspm=1&co=1&ase=5&gclid=Cj0KCQiAhbi8BhDIARIsAJLOlueGxF4bNBDVVlXvAYEbq9r7kD0-rEICam8bVC2DlVMfjJnTNowEfS4aAoTVEALw_wcB&ohost=www.google.com&cid=CAESVuD2SxVK13nGXfnxxj2GaHYwM1jC5FbrAdJrvqG3OeLNz9QfdFUmcr2BrQnfGg_I5bQU6M0udCX7CFQJyiWiA0yS3ZtsGeF4mY2QacD90c-c9RPAoSF6&sig=AOD64_3SxSxtXI6Iu6L0vga1Wcvt2xPoJg&q&adurl&ved=2ahUKEwj4-ZP06oWLAxUQ1zQHHXJxDnUQ0Qx6BAgLEAE" target="_blank">
                        <img src="images/meta_Dungeon.jpg" alt="게임 2">
                        <div class="game-info">
                            <h3>던전앤파이터</h3>
                            <p>액션쾌감!!! 던전앤파이터! 벨트스크롤 액션 게임</p>
                        </div>
                    </a>
                </div>
                <div class="game-card">
                    <a href="https://www.stardewvalley.net/" target="_blank">
                        <img src="images/Stardew_Valley.jpg" alt="게임 3">
                        <div class="game-info">
                            <h3>Stardew Valley</h3>
                            <p>농장을 운영하고 동네 사람들과 친구를 만들어보세요.</p>
                        </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://page.onstove.com/indie/global/view/10561674" target="_blank">  
                    <img src="images/Our_Portfolio.jpg" alt="게임 4">
                    <div class="game-info">
                        <h3>Our Portfolio</h3>
                        <p>최고의 게임을 만나보세요!</p>
                    </div>
                 </a>    
                </div>
                <div class="game-card">
                <a href="https://www.leagueoflegends.com/ko-kr/" target="_blank">
                    <img src="images/leagueof.jpg" alt="게임 5">
                    <div class="game-info">
                        <h3>League of Legends</h3>
                        <p>이 시대 최고의 전략 게임</p>
                    </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://www.roblox.com/ko" target="_blank">
                    <img src="images/roblox.png" alt="게임 6">
                    <div class="game-info">
                        <h3>ROBLOX</h3>
                        <p>초통령 게임!</p>
                    </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://namu.wiki/w/%ED%81%AC%EB%A0%88%EC%9D%B4%EC%A7%80%EB%A0%88%EC%9D%B4%EC%8B%B1%20%EC%B9%B4%ED%8A%B8%EB%9D%BC%EC%9D%B4%EB%8D%94" target="_blank">
                    <img src="images/KARTRIDER.jpg" alt="게임 4">
                    <div class="game-info">
                        <h3>KARTRIDER</h3>
                        <p>카트를 타고 길을 막아라!</p>
                    </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://tr.game.onstove.com/" target="_blank">
                    <img src="images/Tales_Runner.jpg" alt="게임 4">
                    <div class="game-info">
                        <h3>Tales Runner</h3>
                        <p>또 다른 모험을 시작해보세요!</p>
                    </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://www.cyberpunk.net/us/ko/" target="_blank">
                    <img src="images/syber.jpg" alt="게임 4">
                    <div class="game-info">
                        <h3>사이버펑크 2077</h3>
                        <p>2077년이 어떤지 궁금하세요? 이제 시작해보세요!</p>
                    </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://www.escapefromtarkov.com/" target="_blank">
                    <img src="images/Escape_from_Tarkov.jpg" alt="게임 4">
                    <div class="game-info">
                        <h3>Escape from Tarkov</h3>
                        <p>현실을 벗어나세요!</p>
                    </div>
                    </a>
                </div>
                <div class="game-card">
                <a href="https://www.minecraft.net/ko-kr" target="_blank">
                    <img src="images/Mincraft.jpg" alt="게임 4">
                    <div class="game-info">
                        <h3>Mincraft</h3>
                        <p>미래를 조작하세요!</p>
                    </div>
                </a>
                </div>
                <div class="game-card">
                <a href="https://pubg.game.daum.net/pubg/index.daum" target="_blank">
                    <img src="images/meta_tag_pubg.png" alt="게임 4">
                    <div class="game-info">
                        <h3>배틀그라운드</h3>
                        <p>치킨을 먹으세요!</p>
                    </div>
                </a>
                </div>
                <!-- 필요한 만큼 게임 카드 추가 -->
            </div>
        </section>

        <div class="login-section <?php echo $loggedIn ? 'logged-in' : ''; ?>">
            <?php if ($loggedIn): ?>
                <div class="user-info">
                    <div class="profile-image">
                        <?php
                        // 프로필 이미지 경로 확인
                        $profileImage = isset($_SESSION['profile_image']) && !empty($_SESSION['profile_image']) 
                            ? $_SESSION['profile_image'] 
                            : 'images/default_profile.png';
                        
                        // 파일 존재 여부 확인
                        if (!file_exists($profileImage) && $profileImage != 'images/default_profile.png') {
                            $profileImage = 'images/default_profile.png';
                        }
                        ?>
                        <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="프로필 이미지" id="profile-image">
                    </div>
                    <h3 id="profile-nickname"><?php echo htmlspecialchars($nickname); ?>님 환영합니다</h3>
                    <div class="bio-info">
                        <p><span><?php echo htmlspecialchars($bio); ?></span></p>
                    </div>
                    <div class="user-buttons">
                        <a href="upload_profile.php" class="login-button">프로필 수정</a>
                        <a href="logout.php" class="login-button">로그아웃</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="login-options">
                    <a href="login.php" class="login-button">ID 로그인</a>
                </div>
                <div class="find-account">
                    <a href="#">아이디 찾기</a>
                    <a href="#">비밀번호 찾기</a>
                    <a href="regster.html">회원가입</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <footer>
        <div class="footer-content">
            <div class="footer-links">
                <a href="#">이용약관</a>
                <a href="#">개인정보처리방침</a>
                <a href="#">청소년보호정책</a>
                <a href="#">고객센터</a>
            </div>
            <p>&copy; 2024 Good Game Maker. All rights reserved.</p>
        </div>
    </footer>

    <script src="web_user_check.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const gameCards = document.querySelectorAll('.game-card');
            gameCards.forEach((card, index) => {
                setTimeout(() => {
                    card.classList.add('show');
                }, index * 100);
            });
        });
    </script>
</body>
</html> 