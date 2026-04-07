<?php
require_once 'config.php';

// إذا المستخدم مسجل دخول بالفعل
if (is_logged_in()) {
    if (is_admin()) {
        header("Location: admin.php");
    } else {
        header("Location: my_bookings.php");
    }
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // تنظيف البيانات
    $email = trim($_POST['email']);
    $pass = $_POST['password'];

    // جلب المستخدم
    $stmt = $conn->prepare("SELECT id, name, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    // التحقق
    if ($user && password_verify($pass, $user['password'])) {

        // تخزين session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        // توجيه حسب الدور
        if ($user['role'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: my_bookings.php");
        }
        exit();

    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Gardener Service</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width: 400px;">
<div class="card">

<h2>Customer Login</h2>

<!-- عرض الخطأ -->
<?php if(isset($error)): ?>
    <p style="color:red;"><?php echo clean($error); ?></p>
<?php endif; ?>

<!-- رسالة النجاح -->
<?php if(isset($_GET['success'])): ?>
    <p style="color:green;">Account created! Please login.</p>
<?php endif; ?>

<form method="POST">
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button type="submit">Login</button>
</form>

<p style="text-align:center">
    <a href="register.php">Need an account? Register</a><br>
    <a href="index.php">Back to Booking</a>
</p>

</div>
</div>

</body>
</html>