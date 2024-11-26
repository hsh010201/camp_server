<?php
session_start();
session_unset(); // 모든 세션 변수 해제
session_destroy(); // 세션 파괴
header("Location: index.php"); // 홈 페이지로 리다이렉트
exit();
?>
