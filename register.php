<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ✅ تنظيف البيانات
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password_raw = $_POST['password'];

    // ✅ تحقق
    if (empty($name) || empty($email) || empty($phone) || empty($password_raw)) {
        $error = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {

        // ✅ تشفير
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // ✅ تحقق email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {

            $error = "Email already exists.";

        } else {

            // ✅ insert
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $phone, $password);

            if ($stmt->execute()) {
                header("Location: login.php?success=1");
                exit();
            } else {
                $error = "Registration failed.";
            }

            $stmt->close();
        }

        $check->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Gardener Service</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container" style="max-width:400px;">
    <div class="card">

        <h2>Customer Registration</h2>

        <!-- ✅ Error -->
        <?php if(isset($error)): ?>
            <p style="color:red;"><?php echo clean($error); ?></p>
        <?php endif; ?>

        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="password" name="password" placeholder="Create Password" required>

            <button type="submit">Create Account</button>
        </form>

        <p style="text-align:center; margin-top:10px;">
            <a href="login.php">Already have an account? Login</a>
        </p>

    </div>
</div>

</body>
</html>