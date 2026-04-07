<?php
include 'config.php';

$token = $_GET['token'] ?? '';

if (!$token) exit();

// مثال: تحقق إذا المستخدم يكتب (تقدر تطور لاحقاً)
$res = $conn->prepare("SELECT is_typing FROM bookings WHERE chat_token = ?");
$res->bind_param("s", $token);
$res->execute();
$row = $res->get_result()->fetch_assoc();

if ($row && $row['is_typing'] == 1) {
    echo "Typing...";
} else {
    echo "";
}