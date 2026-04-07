<?php
include 'config.php';

// حماية
if (!is_admin()) {
    header("Location: login.php");
    exit();
}

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Error: No chat token provided.");
}

// تحديث الحالة
$update = $conn->prepare("UPDATE bookings SET status = 'Completed' WHERE chat_token = ?");
$update->bind_param("s", $token);
$update->execute();

// جلب الحجز
$stmt = $conn->prepare("SELECT * FROM bookings WHERE chat_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

if (!$booking) {
    die("Booking not found.");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Chat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div id="typingBox" style="font-size:12px; color:gray;"></div>

<div class="container">

    <h2>Chat with <?php echo clean($booking['name']); ?></h2>
    <a href="admin.php">← Back</a>

    <div class="card">

        <!-- معلومات -->
        <div style="background:#f9f9f9; padding:15px; margin-bottom:15px;">
            <strong>Service:</strong> <?php echo clean($booking['service']); ?><br>
            <strong>Date:</strong> <?php echo $booking['booking_date']; ?><br>
            <strong>Note:</strong> <?php echo clean($booking['message']); ?>
        </div>

        <!-- الرسائل -->
        <div class="chat-box" id="chatbox" style="height:300px; overflow-y:auto;"></div>

        <!-- إرسال -->
        <form id="msgForm">
            <input type="hidden" name="token" value="<?php echo clean($token); ?>">
            <input type="text" name="message" id="message" placeholder="Type message..." required>
            <button type="submit">Send</button>
        </form>

    </div>
</div>

<script>
const chatBox = document.getElementById("chatbox");
const form = document.getElementById("msgForm");

// 🔄 تحميل الرسائل
function fetchMessages() {
    fetch("fetch_messages.php?token=<?php echo $token; ?>")
        .then(res => res.text())
        .then(data => {
            const isAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 10;
            chatBox.innerHTML = data;
            if (isAtBottom) chatBox.scrollTop = chatBox.scrollHeight;
        });
}

// 📤 إرسال
form.addEventListener("submit", function(e) {
    e.preventDefault();

    let formData = new FormData(form);

    fetch("send_message.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.text())
    .then(data => {
        document.getElementById("message").value = "";
        fetchMessages();
    });
});

// 🔥 typing check (تم التصحيح)
function checkTyping() {
    fetch("check_typing.php?token=<?php echo $token; ?>")
        .then(res => res.text())
        .then(data => {
            document.getElementById("typingBox").innerHTML = data;
        });
}

// ⏱ تحديث
setInterval(fetchMessages, 2000);
setInterval(checkTyping, 1000);

// تحميل أولي
fetchMessages();
</script>

</body>
</html>