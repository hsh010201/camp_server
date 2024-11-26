<?php
session_start();
require 'db_connection.php';

// 디버깅용 오류 표시 활성화
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 로그인 여부 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "로그인이 필요합니다."]);
    exit();
}

// 요청 데이터 가져오기
$raw_data = file_get_contents('php://input');
error_log("수신된 RAW 데이터: " . $raw_data); // 디버깅용 로그
$data = json_decode($raw_data, true);

if (!$data) {
    error_log("JSON 디코딩 실패: " . json_last_error_msg());
    echo json_encode(["success" => false, "message" => "유효하지 않은 JSON 데이터입니다."]);
    exit();
}

if (!isset($data['campsite']) || !isset($data['rating']) || !isset($data['review_text'])) {
    error_log("필수 데이터 누락: " . json_encode($data));
    echo json_encode(["success" => false, "message" => "유효하지 않은 데이터입니다."]);
    exit();
}

// 변수 설정
$user_id = $_SESSION['user_id'];
$campsite = htmlspecialchars($data['campsite']);
$rating = (int) $data['rating'];
$review_text = htmlspecialchars($data['review_text']);

// 데이터베이스에 리뷰 삽입
$sql = "INSERT INTO reviews (user_id, campsite, rating, review_text) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isis", $user_id, $campsite, $rating, $review_text);

if ($stmt->execute()) {
    error_log("리뷰 저장 성공: " . json_encode($data)); // 디버깅
    echo json_encode(["success" => true, "message" => "리뷰가 성공적으로 저장되었습니다."]);
} else {
    error_log("SQL 오류: " . $stmt->error); // 디버깅
    echo json_encode(["success" => false, "message" => "리뷰 저장 실패: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
