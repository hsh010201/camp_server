<?php
require 'db_connection.php'; // 데이터베이스 연결 파일

// POST 요청 처리 (회원가입 처리)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password']; // 비밀번호 확인 필드 추가
    $error = '';

    // 입력값 검증
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "모든 필드를 채워주세요.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "유효한 이메일을 입력하세요.";
    } elseif ($password !== $confirm_password) {
        $error = "비밀번호가 일치하지 않습니다.";
    } else {
        // 비밀번호 해싱
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // 중복 이메일 확인
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "이미 사용 중인 이메일입니다.";
        } else {
            // 사용자 삽입
            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                header("Location: login.php?success=1"); // 성공 시 로그인 페이지로 리디렉션
                exit();
            } else {
                $error = "회원가입 중 오류가 발생했습니다.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>회원가입</title>
    <link rel="stylesheet" href="style.css"> <!-- 공통 CSS 파일 -->
</head>
<body>
    <header>
        <h1>회원가입</h1>
        <nav>
            <ul>
                <li><a href="index.php">홈</a></li>
                <li><a href="register.php">회원가입</a></li>
                <li><a href="search.php">캠핑장 검색</a></li>
                <li><a href="mypage.php">마이페이지</a></li>
                <li><a href="reservation.php">예약 확인</a></li>
                <li><a href="review.php">리뷰 작성</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <form id="register-form" action="register.php" method="POST">
            <label for="username">사용자 이름:</label>
            <input type="text" id="username" name="username" required placeholder="사용자 이름을 입력하세요">
            
            <label for="email">이메일:</label>
            <input type="email" id="email" name="email" required placeholder="이메일을 입력하세요">
            
            <label for="password">비밀번호:</label>
            <input type="password" id="password" name="password" required placeholder="비밀번호를 입력하세요">
            
            <label for="confirm_password">비밀번호 확인:</label>
            <input type="password" id="confirm_password" name="confirm_password" required placeholder="비밀번호를 다시 입력하세요">
            
            <button type="submit">가입하기</button>
            
            <!-- 오류 메시지 출력 -->
            <?php if (!empty($error)): ?>
                <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </form>
    </main>
</body>
</html>
