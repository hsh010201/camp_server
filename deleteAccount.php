<?php
session_start();
require 'db_connection.php'; // 데이터베이스 연결 파일

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "로그인하지 않았습니다."]);
    exit();
}

$user_id = $_SESSION['user_id'];

// 계정 삭제 쿼리
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);

if ($stmt->execute()) {
    session_destroy(); // 세션 종료
    echo json_encode(["success" => true, "message" => "계정이 삭제되었습니다."]);
} else {
    echo json_encode(["success" => false, "message" => "계정 삭제에 실패했습니다."]);
}

$stmt->close();
$conn->close();
?>
