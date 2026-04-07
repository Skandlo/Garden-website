<?php
include 'config.php';

// ✅ التحقق من token
$token = $_GET['token'] ?? '';

if (!$token) {
    die("No token");
}

// ✅ التحقق من وجود booking
$stmt = $conn->prepare("SELECT user_id FROM bookings WHERE chat_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Invalid token");
}

// ✅ حماية
if ($booking['user_id'] && (!is_logged_in() || $_SESSION['user_id'] != $booking['user_id']) && !is_admin()) {
    die("Unauthorized");
}

// ✅ جلب الرسائل + اسم المستخدم
$stmt = $conn->prepare("
    SELECT m.*, u.name 
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.id
    WHERE m.chat_token = ?
    ORDER BY m.created_at ASC
");
$stmt->bind_param("s", $token);
$stmt->execute();
$res = $stmt->get_result();

// ✅ عرض الرسائل
while ($m = $res->fetch_assoc()) {

    $isAdmin = ($m['sender_type'] === 'admin');
    $class = $isAdmin ? 'bubble-admin' : 'bubble-user';

    echo '<div class="max-w-[80%] p-3 shadow-sm ' . $class . '">';

    // ✅ اسم المرسل
    if ($m['sender_type'] === 'admin') {
        echo '<strong>Admin:</strong><br>';
    } elseif ($m['sender_type'] === 'user') {
        echo '<strong>' . clean($m['name']) . ':</strong><br>';
    } else {
        echo '<strong>Guest:</strong><br>';
    }

    echo '<p class="text-sm">' . clean($m['message']) . '</p>';

    echo '<span class="text-[10px] opacity-70 block text-right mt-1">'
        . date('H:i', strtotime($m['created_at'])) .
    '</span>';

    echo '</div>';
}
?>