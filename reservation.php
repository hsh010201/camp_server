<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('로그인하지 않으셨습니다. 로그인 페이지로 이동합니다.'); window.location.href = 'login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 예약 정보 가져오기
$sql = "SELECT site_name, location, price_range, reservation_date FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$reservations = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>예약 확인</title>
    <link rel="stylesheet" href="style.css"> <!-- 공통 CSS 파일 -->
</head>
<body>
    <header>
        <h1>캠핑장 예약 시스템 - 예약 확인</h1>
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
        <div class="user-info">
            <h3><?php echo htmlspecialchars($username); ?>님의 예약 내역</h3>
            <p>예약하신 캠핑장 내역을 확인하세요.</p>
        </div>
        
        <section id="reservation-list">
            <?php if (count($reservations) > 0): ?>
                <?php foreach ($reservations as $reservation): ?>
                    <div class="reservation-item">
                        <h2><?php echo htmlspecialchars($reservation['site_name']); ?></h2>
                        <p><strong>위치:</strong> <?php echo htmlspecialchars($reservation['location']); ?></p>
                        <p><strong>가격대:</strong> <?php echo htmlspecialchars($reservation['price_range']); ?></p>
                        <p><strong>예약 날짜:</strong> <?php echo htmlspecialchars($reservation['reservation_date']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>예약된 내역이 없습니다.</p>
            <?php endif; ?>
        </section>
    </main>

</body>
</html>