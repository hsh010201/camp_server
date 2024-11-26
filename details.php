<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    // 로그인되지 않은 경우
    echo "<script>alert('로그인하지 않으셨습니다. 로그인 페이지로 이동합니다.'); window.location.href = 'login.php';</script>";
    exit();
}

// 요청된 캠핑장 ID 가져오기
$siteId = isset($_GET['site']) ? $_GET['site'] : null;

// 캠핑장 데이터 로드
$json = file_get_contents('campingSites.json');
$campingSites = json_decode($json, true);

$siteDetails = null;

// URL 매개변수로 전달된 ID를 검색
if ($siteId) {
    foreach ($campingSites as $site) {
        if (isset($site['id']) && $site['id'] == $siteId) { // ID가 존재하는지 확인
            $siteDetails = $site;
            break;
        }
    }
}

// 캠핑장 정보가 없으면 에러 처리
if (!$siteDetails) {
    echo "<script>alert('캠핑장 정보를 찾을 수 없습니다.'); window.location.href = 'index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>캠핑장 세부 정보</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($siteDetails['name']); ?></h1>
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
        <section>
            <h2>캠핑장 상세 정보</h2>
            <p>위치: <?php echo htmlspecialchars($siteDetails['location']); ?></p>
            <p>가격 범위: <?php echo htmlspecialchars($siteDetails['priceRange']); ?>원</p>
            <img src="<?php echo htmlspecialchars($siteDetails['image']); ?>" alt="<?php echo htmlspecialchars($siteDetails['name']); ?>" style="max-width:100%; height:auto;">
        </section>
        <section>
            <h2>예약하기</h2>
            <button onclick="makeReservation()">예약</button>
        </section>
    </main>
    <script>
        // PHP 데이터를 JavaScript로 전달
        const siteDetails = <?php echo json_encode($siteDetails); ?>;

        function makeReservation() {
            // siteDetails 객체에서 모든 데이터 가져오기
            fetch('makeReservation.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    id: siteDetails.id,         // 캠핑장 ID
                    name: siteDetails.name,     // 캠핑장 이름
                    location: siteDetails.location, // 캠핑장 위치
                    priceRange: siteDetails.priceRange // 캠핑장 가격 범위
                })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert('예약이 완료되었습니다.');
                    window.location.href = 'reservation.php';
                } else {
                    alert('예약에 실패했습니다. 다시 시도해주세요.');
                }
            })
            .catch(error => {
                console.error('예약 중 오류 발생:', error);
                alert('예약 중 오류가 발생했습니다.');
            });
        }
    </script>
</body>
</html>
