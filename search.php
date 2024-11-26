<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>캠핑장 검색</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1>캠핑장 검색</h1>
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
        <form id="search-form">
            <label for="location">지역:</label>
            <select id="location" name="location">
                <option value="전국">전국</option>
                <option value="서울특별시">서울특별시</option>
                <option value="부산광역시">부산광역시</option>
                <option value="대구광역시">대구광역시</option>
                <option value="인천광역시">인천광역시</option>
                <option value="광주광역시">광주광역시</option>
                <option value="대전광역시">대전광역시</option>
                <option value="울산광역시">울산광역시</option>
                <option value="세종특별자치시">세종특별자치시</option>
                <option value="경기도">경기도</option>
                <option value="강원도">강원도</option>
                <option value="충청북도">충청북도</option>
                <option value="충청남도">충청남도</option>
                <option value="전라북도">전라북도</option>
                <option value="전라남도">전라남도</option>
                <option value="경상북도">경상북도</option>
                <option value="경상남도">경상남도</option>
                <option value="제주특별자치도">제주특별자치도</option>
            </select>

            <label for="min-price">최소 가격:</label>
            <input type="number" id="min-price" name="min-price" placeholder="최소 가격을 입력하세요">

            <label for="max-price">최대 가격:</label>
            <input type="number" id="max-price" name="max-price" placeholder="최대 가격을 입력하세요">

            <button type="button" onclick="searchCampingSites()">검색하기</button>
        </form>
        <div id="search-results" class="grid-container"></div>
    </main>

    <script>
        const isLoggedIn = <?php echo json_encode(isset($_SESSION['user_id'])); ?>; // 로그인 상태를 JS로 전달

        async function searchCampingSites() {
            try {
                const response = await fetch('campingSites.json');
                const campingSites = await response.json();

                const location = document.getElementById('location').value;
                const minPrice = parseInt(document.getElementById('min-price').value) || 0;
                const maxPrice = parseInt(document.getElementById('max-price').value) || Infinity;

                const filteredSites = campingSites.filter(site => {
                    const [minSitePrice, maxSitePrice] = site.priceRange.split('-').map(Number);
                    return (location === '전국' || site.location === location) &&
                           minSitePrice >= minPrice &&
                           maxSitePrice <= maxPrice;
                });

                const resultsDiv = document.getElementById('search-results');
                resultsDiv.innerHTML = '';

                if (filteredSites.length > 0) {
                    filteredSites.forEach(site => {
                        const siteElement = document.createElement('div');
                        siteElement.classList.add('camping-site');
                        siteElement.innerHTML = `
                            <img src="${site.image}" alt="${site.name}" class="camping-image">
                            <h3>${site.name}</h3>
                            <p>위치: ${site.location}</p>
                            <p>가격 범위: ${site.priceRange}원</p>
                        `;
                        siteElement.addEventListener('click', () => {
                            if (isLoggedIn) {
                                // ID를 URL에 포함하여 details.php로 이동
                                window.location.href = `details.php?site=${site.id}`;
                            } else {
                                alert('로그인 후 이용해주세요.');
                            }
                        });
                        resultsDiv.appendChild(siteElement);
                    });
                } else {
                    resultsDiv.innerHTML = '<p>검색 결과가 없습니다.</p>';
                }
            } catch (error) {
                console.error('캠핑장 데이터를 불러오는 중 오류 발생:', error);
                document.getElementById('search-results').innerHTML = '<p>데이터를 불러오는 중 오류가 발생했습니다.</p>';
            }
        }
    </script>
</body>
</html>
