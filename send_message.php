<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';
    $message = trim($_POST['message'] ?? '');

    if (empty($token) || empty($message)) {
        echo json_encode(['status' => 'error']);
        exit();
    }

    // ✅ جلب الحجز للتأكد من الصلاحيات
    $stmt = $conn->prepare("SELECT user_id FROM bookings WHERE chat_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();

    if (!$booking) {
        echo json_encode(['status' => 'invalid_token']);
        exit();
    }

    // ✅ SECURITY CHECK
    if ($booking['user_id'] !== null) {
        if (!is_logged_in() || $_SESSION['user_id'] != $booking['user_id']) {
            if (!is_admin()) {
                echo json_encode(['status' => 'unauthorized']);
                exit();
            }
        }
    }

    // ✅ تنظيف الرسالة (حماية XSS)
    $message = strip_tags($message);

    // ✅ تحديد المرسل
    if (is_admin()) {
        $sender_type = 'admin';
        $sender_id = $_SESSION['user_id'] ?? null;
        $status = 'Replied';
    } elseif (is_logged_in()) {
        $sender_type = 'user';
        $sender_id = $_SESSION['user_id'];
        $status = 'Pending';
    } else {
        $sender_type = 'guest';
        $sender_id = null;
        $status = 'Pending';
    }

    // ✅ إدخال الرسالة
    $stmt = $conn->prepare("INSERT INTO messages (chat_token, sender_id, sender_type, message) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $token, $sender_id, $sender_type, $message);
    $stmt->execute();

    // ✅ تحديث booking
    $update = $conn->prepare("UPDATE bookings SET last_message = ?, status = ?, last_message_time = NOW() WHERE chat_token = ?");
    $update->bind_param("sss", $message, $status, $token);
    $update->execute();

    // ✅ response لـ AJAX
    header("Location: admin_chat.php?token=" . $token);
exit();
}
?>