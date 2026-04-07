<?php
include 'config.php';

if(isset($_POST['submit_booking'])) {

    // ✅ تنظيف البيانات
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $service = $_POST['service'];
    $date = $_POST['date'];
    $msg = $_POST['message'] ?? '';

    // ✅ تحقق
    if (empty($name) || empty($phone)) {
        die("Please fill all required fields");
    }

    // ✅ user_id (إذا مسجل دخول)
    $user_id = $_SESSION['user_id'] ?? null;

    // ✅ إنشاء token
    $token = bin2hex(random_bytes(16));

    // ✅ INSERT
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, name, phone, service, booking_date, message, chat_token) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $user_id, $name, $phone, $service, $date, $msg, $token);

    if($stmt->execute()) {
        header("Location: chat.php?token=" . $token);
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>EverGreen Gardening</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- ✅ Navbar بسيطة -->
<div style="text-align:right; padding:10px;">
<?php if (is_logged_in()): ?>
    Welcome <?php echo clean($_SESSION['user_name']); ?> |
    <a href="logout.php">Logout</a>
<?php else: ?>
    <a href="login.php">Login</a> |
    <a href="register.php">Register</a>
<?php endif; ?>
</div>

<div class="container">
    <h1>EverGreen Gardener Service</h1>

    <!-- ⚠️ تنبيه للزائر -->
    <?php if (!is_logged_in()): ?>
        <p style="color:orange;">
            ⚠️ You are booking as guest. Login to save your bookings.
        </p>
    <?php endif; ?>

    <div class="card">
        <h2>Book a Service</h2>

        <form method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="text" name="phone" placeholder="Phone Number" required>

            <select name="service">
                <option>Lawn Mowing</option>
                <option>Hedge Trimming</option>
                <option>Garden Cleanup</option>
            </select>

            <input type="date" name="date" required>

            <textarea name="message" placeholder="Optional message"></textarea>

            <button type="submit" name="submit_booking">Book & Chat</button>
        </form>
    </div>
</div>

</body>
</html>