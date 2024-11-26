<?php
session_start();
require 'db_connection.php';

// 로그인 확인
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "로그인이 필요합니다."]);
    exit();
}

// 요청 데이터 가져오기
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['id'])) {
    echo json_encode(["success" => false, "message" => "유효하지 않은 데이터입니다."]);
    exit();
}

// 변수 설정
$review_id = (int) $data['id'];
$user_id = $_SESSION['user_id'];

// 리뷰 삭제 (로그인 사용자만 자신의 리뷰 삭제 가능)
$sql = "DELETE FROM reviews WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $review_id, $user_id);

if ($stmt->execute() && $stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "리뷰가 성공적으로 삭제되었습니다."]);
} else {
    echo json_encode(["success" => false, "message" => "작성자 id가 아닙니다."]);
}

$stmt->close();
$conn->close();
?>
