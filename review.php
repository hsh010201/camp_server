<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>리뷰 작성</title>
    <link rel="stylesheet" href="style.css"> <!-- 공통 CSS 파일 -->

    <style>
        /* 별점 스타일 */
        .star-rating {
            display: flex;
            gap: 5px;
            direction: ltr; /* 왼쪽에서 오른쪽으로 설정 */
        }

        .star {
            font-size: 24px;
            cursor: pointer;
            color: #ccc;
            transition: color 0.3s;
        }

        .star.selected {
            color: gold;
        }

        /* 리뷰 항목 스타일 */
        .review-item {
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #ffffff; /* 리뷰 카드 배경색: 밝은 모드에서 가독성 증가 */
            color: #333; /* 텍스트 색상: 밝은 배경에 적합 */
            transition: background-color 0.3s, color 0.3s; /* 다크모드 전환 시 부드러운 전환 */
        }

        /* 다크모드 스타일 */
        @media (prefers-color-scheme: dark) {
            .review-item {
                background-color: #2c2c2c; /* 다크모드 배경색 */
                color: #f0f0f0; /* 다크모드 텍스트 색상 */
            }

            .delete-btn {
                background-color: #ff4d4d; /* 다크모드에서 삭제 버튼 배경색 */
                color: white;
            }

            .delete-btn:hover {
                background-color: darkred;
            }
        }

        .delete-btn {
            margin-top: 10px;
            padding: 5px 10px;
            border: none;
            background-color: red;
            color: white;
            cursor: pointer;
            border-radius: 3px;
            transition: background-color 0.3s;
        }

        .delete-btn:hover {
            background-color: darkred;
        }

        /* 리뷰 입력 칸 크기 */
        textarea {
            width: 100%;
            height: 150px; /* 높이를 크게 설정 */
            resize: vertical; /* 사용자가 크기를 조절할 수 있도록 설정 */
            font-size: 16px;
        }

        /* 텍스트 정렬 */
        body {
            text-align: left; /* 기본 텍스트 정렬 */
        }

        header h1, .review-section h2 {
            text-align: center; /* 타이틀은 가운데 정렬 유지 */
        }

        form, .review-section {
            text-align: left; /* 폼과 리뷰 섹션은 왼쪽 정렬 */
        }
    </style>
</head>
<body>
    <header>
        <h1>리뷰 작성</h1>
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
        <form id="review-form">
            <label for="campsite">캠핑장 선택:</label>
            <select id="campsite" name="campsite">
                <!-- 캠핑장 옵션은 JavaScript로 추가됩니다 -->
            </select>

            <label for="rating">평점:</label>
            <div class="star-rating">
                <span class="star" data-value="1">&#9733;</span>
                <span class="star" data-value="2">&#9733;</span>
                <span class="star" data-value="3">&#9733;</span>
                <span class="star" data-value="4">&#9733;</span>
                <span class="star" data-value="5">&#9733;</span>
            </div>

            <label for="review">리뷰 내용:</label>
            <textarea id="review" name="review" placeholder="리뷰 내용을 입력하세요"></textarea>
            <button type="submit">리뷰 작성</button>
        </form>

        <section class="review-section" id="review-section">
            <h2>리뷰 목록</h2>
            <!-- 리뷰는 JavaScript로 추가됩니다 -->
        </section>
    </main>

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const reviewForm = document.getElementById('review-form');
            const stars = document.querySelectorAll('.star');
            let selectedRating = 0;

            // 캠핑장 목록 불러오기
            fetch('campingSites.json')
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    const campsiteSelect = document.getElementById('campsite');
                    data.forEach(site => {
                        const option = document.createElement('option');
                        option.value = site.name;
                        option.textContent = site.name;
                        campsiteSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('캠핑장 로드 중 오류:', error);
                });

            // 별점 선택
            stars.forEach((star, index) => {
                star.addEventListener('click', () => {
                    selectedRating = index + 1; // 선택된 별의 인덱스를 기준으로 점수 계산
                    stars.forEach((s, i) => {
                        s.classList.toggle('selected', i < selectedRating);
                    });
                });
            });

            // 리뷰 제출
            reviewForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const campsite = document.getElementById('campsite').value;
                const reviewText = document.getElementById('review').value;

                // 입력값 검증
                if (!selectedRating) {
                    alert('평점을 선택해주세요!');
                    return;
                }
                if (!campsite) {
                    alert('캠핑장을 선택해주세요!');
                    return;
                }
                if (!reviewText.trim()) {
                    alert('리뷰 내용을 작성해주세요!');
                    return;
                }

                fetch('saveReview.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        campsite: campsite,
                        rating: selectedRating,
                        review_text: reviewText
                    })
                })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.json();
                })
                .then(result => {
                    if (result.success) {
                        alert('리뷰가 성공적으로 저장되었습니다!');
                        loadReviews(); // 새로운 리뷰 목록 로드
                        reviewForm.reset();
                        stars.forEach(star => star.classList.remove('selected'));
                        selectedRating = 0;
                    } else {
                        alert(result.message || '리뷰 저장에 실패했습니다.');
                    }
                })
                .catch(error => {
                    console.error('리뷰 저장 중 오류:', error);
                    alert('리뷰 저장 중 오류가 발생했습니다. 다시 시도해주세요.');
                });
            });

            // 리뷰 목록 불러오기
            const loadReviews = () => {
                fetch('getReviews.php')
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        const reviewSection = document.getElementById('review-section');
                        reviewSection.innerHTML = ''; // 초기화
                        if (data.reviews) {
                            data.reviews.forEach(review => {
                                const reviewElement = document.createElement('div');
                                reviewElement.classList.add('review-item');
                                reviewElement.innerHTML = `
                                    <h3>${review.campsite} - ${review.rating}점</h3>
                                    <p>${review.review_text}</p>
                                    <p><strong>작성자:</strong> ${review.username}</p>
                                    <button class="delete-btn" data-id="${review.id}">삭제</button>
                                `;
                                reviewSection.appendChild(reviewElement);
                            });

                            // 리뷰 삭제 기능
                            const deleteButtons = document.querySelectorAll('.delete-btn');
                            deleteButtons.forEach(button => {
                                button.addEventListener('click', (e) => {
                                    const reviewId = e.target.getAttribute('data-id');
                                    if (confirm('정말 이 리뷰를 삭제하시겠습니까?')) {
                                        fetch('deleteReview.php', {
                                            method: 'POST',
                                            headers: { 'Content-Type': 'application/json' },
                                            body: JSON.stringify({ id: reviewId })
                                        })
                                        .then(response => {
                                            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                                            return response.json();
                                        })
                                        .then(result => {
                                            if (result.success) {
                                                alert('리뷰가 삭제되었습니다.');
                                                loadReviews(); // 목록 새로고침
                                            } else {
                                                alert(result.message || '리뷰 삭제에 실패했습니다.');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('리뷰 삭제 중 오류:', error);
                                            alert('리뷰 삭제 중 오류가 발생했습니다.');
                                        });
                                    }
                                });
                            });
                        } else {
                            reviewSection.innerHTML = '<p>등록된 리뷰가 없습니다.</p>';
                        }
                    })
                    .catch(error => {
                        console.error('리뷰 로드 중 오류:', error);
                    });
            };
            loadReviews();
        });
    </script>
</body>
</html>
