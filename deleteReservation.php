<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "로그인하지 않았습니다."]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);
$reservation_id = $data['id'];

// 예약 삭제 쿼리
$sql = "DELETE FROM reservations WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $reservation_id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "예약이 취소되었습니다."]);
} else {
    echo json_encode(["success" => false, "message" => "예약 취소에 실패했습니다."]);
}

$stmt->close();
$conn->close();
?>
