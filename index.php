<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>캠핑장 홈</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* 사용자 정보 섹션 스타일 */
        .user-info {
            padding: 20px;
            margin: 20px auto;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            border-radius: 10px;
            max-width: 800px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .user-info h3 {
            margin-bottom: 10px;
            font-size: 1.5em;
            color: #333;
        }

        .user-info p {
            font-size: 1.2em;
            color: #555;
        }

        /* 추천 캠핑장 섹션 스타일 */
        .featured-section {
            margin: 40px auto;
            padding: 20px;
            text-align: center;
            max-width: 1200px;
        }

        .featured-section h2 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #FFD700;
        }

        .grid-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .camping-site {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 16px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .camping-site:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .camping-site img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .camping-site h3 {
            font-size: 1.4em;
            color: #222;
            margin-bottom: 8px;
        }

        .camping-site p {
            font-size: 1em;
            color: #444;
        }
    </style>
</head>
<body>
    <header>
        <h1>캠핑장 예약</h1>
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
        <!-- 사용자 정보 섹션 -->
        <section class="user-info">
            <?php if (isset($_SESSION['username'])): ?>
                <h3>환영합니다, <?php echo htmlspecialchars($_SESSION['username']); ?>님!</h3>
                <p>좋은 캠핑 되세요!</p>
            <?php else: ?>
                <h3>로그인되지 않았습니다</h3>
                <p>로그인 후 캠핑장 예약 시스템의 모든 기능을 이용할 수 있습니다.</p>
            <?php endif; ?>
        </section>

        <!-- 추천 캠핑장 섹션 -->
        <section class="featured-section">
            <h2>추천 캠핑장</h2>
            <div id="featured-campsites" class="grid-container"></div>
        </section>
    </main>

    <script>
       async function loadFeaturedCampsites() {
    try {
        const response = await fetch('campingSites.json');
        if (!response.ok) {
            throw new Error('캠핑장 데이터를 불러오는 데 실패했습니다.');
        }
        const campingSites = await response.json();
        if (!campingSites || campingSites.length === 0) {
            throw new Error('캠핑장 데이터가 없습니다.');
        }

        const featuredCampsites = campingSites.slice(0, 6);
        const container = document.getElementById('featured-campsites');
        container.innerHTML = ''; // 기존 데이터를 초기화

        featuredCampsites.forEach(site => {
            const siteElement = document.createElement('div');
            siteElement.classList.add('camping-site');
            siteElement.innerHTML = `
                <a href="details.php?site=${encodeURIComponent(site.id)}">
                    <img src="${site.image}" alt="${site.name}" class="camping-image">
                    <h3>${site.name}</h3>
                    <p>위치: ${site.location}</p>
                    <p>가격 범위: ${site.priceRange}원</p>
                </a>
            `;
            container.appendChild(siteElement);
        });
    } catch (error) {
        console.error(error.message);
        const container = document.getElementById('featured-campsites');
        container.innerHTML = '<p>캠핑장 데이터를 불러오는 데 문제가 발생했습니다. 나중에 다시 시도해 주세요.</p>';
    }
}

        loadFeaturedCampsites();
    </script>
</body>
</html>
