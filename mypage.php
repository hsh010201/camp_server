<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인하지 않으셨습니다. 로그인 페이지로 이동합니다.'); window.location.href = 'login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 사용자 정보와 예약 내역 가져오기
$sql_user = "SELECT * FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_info = $result_user->fetch_assoc();

$sql_reservations = "SELECT * FROM reservations WHERE user_id = ?";
$stmt_reservations = $conn->prepare($sql_reservations);
$stmt_reservations->bind_param("i", $user_id);
$stmt_reservations->execute();
$result_reservations = $stmt_reservations->get_result();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>마이 페이지</title>
    <link rel="stylesheet" href="style.css"> <!-- 공통 CSS 파일로 설정 -->

  
</head>
<body>
    <header>
        <h1>마이페이지</h1>
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
        <section id="user-info">
            <h2>내 정보</h2>
            <p>사용자 이름: <?php echo htmlspecialchars($user_info['username']); ?></p>
            <p>이메일: <?php echo htmlspecialchars($user_info['email']); ?></p>
            <button onclick="deleteAccount()">계정 삭제</button>
        </section>

        <section id="my-reservations">
            <h2>내 예약 내역</h2>
            <ul>
                <?php while ($reservation = $result_reservations->fetch_assoc()): ?>
                    <li>
                        <?php echo htmlspecialchars($reservation['site_name']); ?>
                        <button onclick="deleteReservation(<?php echo $reservation['id']; ?>)">삭제</button>
                    </li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>
    <script>
        function deleteAccount() {
            if (confirm("정말로 계정을 삭제하시겠습니까?")) {
                fetch('deleteAccount.php', {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        window.location.href = 'index.php'; // 홈으로 리다이렉트
                    }
                });
            }
        }

        function deleteReservation(id) {
            if (confirm("정말로 이 예약을 삭제하시겠습니까?")) {
                fetch('deleteReservation.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // 페이지 새로고침
                    }
                });
            }
        }
    </script>
</body>
</html>

<?php
$stmt_user->close();
$stmt_reservations->close();
$conn->close();
?>
