<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

// 로그인 확인 (필요에 따라 사용하지 않아도 됨)
// if (!isset($_SESSION['user_id'])) {
//     echo json_encode(["success" => false, "message" => "로그인이 필요합니다."]);
//     exit();
// }

// 리뷰 목록 가져오기
$sql = "SELECT reviews.id, reviews.campsite, reviews.rating, reviews.review_text, reviews.created_at, users.username 
        FROM reviews 
        JOIN users ON reviews.user_id = users.id 
        ORDER BY reviews.created_at DESC";
$result = $conn->query($sql);

$reviews = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $reviews[] = $row;
    }
}

echo json_encode(["success" => true, "reviews" => $reviews]);

$conn->close();
?>
