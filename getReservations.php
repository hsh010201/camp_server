<?php
session_start(); // 세션 시작

$servername = "3.38.98.83";
$username = "user";
$password = "your_password";
$dbname = "camping_site_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB 연결 실패: " . $conn->connect_error]));
}

// 로그인 확인
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    die(json_encode(["success" => false, "message" => "로그인하지 않았습니다."]));
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// 예약 정보 가져오기
$sql = "SELECT site_name, location, price_range, reservation_date FROM reservations WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

echo json_encode(["success" => true, "username" => $username, "reservations" => $reservations]);

$stmt->close();
$conn->close();
?>
