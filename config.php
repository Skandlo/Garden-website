<?php
// 🔒 إعدادات Session آمنة
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
// ini_set('session.cookie_secure', 1); // فعلها فقط مع HTTPS
ini_set('session.cookie_samesite', 'Lax');

// ✅ بدء session مرة واحدة فقط
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔐 حماية من Session Fixation
function secure_login_start() {
    session_regenerate_id(true);
}

// 🛢️ Database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "gardener_db";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed.");
}

$conn->set_charset("utf8mb4");

// 🧼 تنظيف output (XSS)
function clean($data) {
    return htmlspecialchars($data ?? '', ENT_QUOTES, 'UTF-8');
}

// 🔐 Auth helpers
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

function is_admin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// 🟢 تحديث last_seen (بشكل آمن)
if (is_logged_in()) {
    $stmt = $conn->prepare("UPDATE users SET last_seen = NOW() WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}
?>