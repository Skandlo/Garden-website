<?php 
include 'config.php';

// ✅ حماية
if (!is_logged_in()) { 
    header("Location: login.php"); 
    exit; 
}

// ✅ جلب البيانات بطريقة آمنة
$u_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $u_id);
$stmt->execute();
$res = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings - EverGreen</title>
    <!-- ✅ FIXED -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-50 text-gray-800">

<nav class="bg-emerald-700 text-white p-4 shadow-lg">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
        <a href="index.php" class="font-bold text-xl">EverGreen</a>
        <div class="space-x-4">
            <a href="index.php">Book Now</a>
            <a href="my_bookings.php" class="underline">My Bookings</a>
            <a href="logout.php" class="bg-emerald-800 px-3 py-1 rounded">Logout</a>
        </div>
    </div>
</nav>

<main class="max-w-6xl mx-auto py-10 px-4">

    <h1 class="text-3xl font-bold mb-8 text-emerald-900">
        Your Gardening Projects
    </h1>

    <div class="grid gap-6">

        <?php if ($res->num_rows > 0): ?>

            <?php while($row = $res->fetch_assoc()): ?>
            
            <div class="bg-white rounded-xl shadow-sm border p-6 flex flex-col md:flex-row justify-between items-center hover:shadow-md transition">
                
                <div class="mb-4 md:mb-0">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600">
                        <?php echo clean($row['status'] ?? 'Pending'); ?>
                    </span>

                    <h3 class="text-xl font-bold">
                        <?php echo clean($row['service']); ?>
                    </h3>

                    <p class="text-gray-500 text-sm">
                        Booked for: <?php echo date('M d, Y', strtotime($row['booking_date'])); ?>
                    </p>
                </div>

                <div class="text-right flex items-center gap-4">

                    <div class="hidden md:block">
                        <p class="text-xs text-gray-400">Last update</p>
                        <p class="text-sm italic text-gray-600">
                            "<?php echo substr(clean($row['last_message'] ?? 'No messages yet'), 0, 30); ?>..."
                        </p>
                    </div>

                    <a href="chat.php?token=<?php echo $row['chat_token']; ?>"
                       class="bg-emerald-600 text-white px-6 py-2 rounded-full font-medium hover:bg-emerald-700 transition">
                        Open Chat
                    </a>

                </div>

            </div>

            <?php endwhile; ?>

        <?php else: ?>

            <!-- ✅ حالة فارغة -->
            <div class="text-center text-gray-500 mt-10">
                No bookings yet.
            </div>

        <?php endif; ?>

    </div>

</main>

</body>
</html>