<?php
session_start(); // 세션 시작

$servername = "3.38.98.83"; // MySQL 서버 IP 주소
$username = "newuser"; // MySQL 사용자 이름
$password = "your_password"; // MySQL 비밀번호
$dbname = "camping_site_db"; // 데이터베이스 이름

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "DB 연결 실패: " . $conn->connect_error]));
}

// 로그인한 사용자의 ID 가져오기
if (!isset($_SESSION['user_id'])) {
    die(json_encode(["success" => false, "message" => "로그인하지 않았습니다."]));
}

$user_id = $_SESSION['user_id'];

// 클라이언트에서 전송된 캠핑장 정보 가져오기 (JSON 방식)
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || empty($data['id']) || empty($data['name']) || empty($data['location']) || empty($data['priceRange'])) {
    die(json_encode(["success" => false, "message" => "필수 데이터가 누락되었습니다."]));
}

$site_id = $data['id']; // JSON에서 'id'를 가져옴
$site_name = $data['name'];
$location = $data['location'];
$price_range = $data['priceRange'];



// 예약 정보를 데이터베이스에 삽입
$sql = "INSERT INTO reservations (user_id, site_id, site_name, location, price_range) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisss", $user_id, $site_id, $site_name, $location, $price_range);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "예약이 완료되었습니다!"]);
} else {
    echo json_encode(["success" => false, "message" => "예약에 실패했습니다: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
