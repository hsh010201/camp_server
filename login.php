<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

// POST 요청이 있을 경우 로그인 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 데이터베이스에서 사용자 확인
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        // 로그인 성공
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: index.php"); // 로그인 후 리디렉션
        exit();
    } else {
        // 로그인 실패
        $error = "이메일 또는 비밀번호가 잘못되었습니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>로그인</title>
    <link rel="stylesheet" href="style.css"> <!-- 공통 CSS 파일 -->
</head>
<body>
    <header>
        <h1>로그인</h1>
        <nav>
            <ul>
            <li><a href="index.php">홈</a></li>
                <li><a href="register.php">회원가입</a></li>
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li><a href="login.php">로그인</a></li>
                <?php else: ?>
                    <li><a href="logout.php">로그아웃</a></li>
                <?php endif; ?>
                <li><a href="search.php">캠핑장 검색</a></li>
                <li><a href="mypage.php">마이페이지</a></li>
                <li><a href="reservation.php">예약 확인</a></li>
                <li><a href="review.php">리뷰 작성</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form id="login-form" action="login.php" method="POST" onsubmit="return validateForm()">
            <label for="email">이메일:</label>
            <input type="email" id="email" name="email" required placeholder="이메일을 입력하세요">
            
            <label for="password">비밀번호:</label>
            <input type="password" id="password" name="password" required placeholder="비밀번호를 입력하세요">
            
            <button type="submit">로그인</button>
            
            <!-- 오류 메시지 표시 -->
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 캠핑장 예약 시스템</p>
    </footer>

    <script>
        function validateForm() {
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();

            if (!email || !password) {
                alert('이메일과 비밀번호를 모두 입력해 주세요.');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
