<?php
$servername = "3.38.98.83"; // MySQL 서버 IP 주소
$username = "newuser"; // MySQL 사용자 이름
$password = "your_password"; // MySQL 비밀번호
$dbname = "camping_site_db"; // 데이터베이스 이름

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("DB 연결 실패: " . $conn->connect_error);
}
?>
