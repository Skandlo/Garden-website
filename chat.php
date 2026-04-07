<?php 
include 'config.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Access Denied");
}

// ✅ جلب الحجز
$stmt = $conn->prepare("SELECT * FROM bookings WHERE chat_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$booking = $stmt->get_result()->fetch_assoc();

// ❗ تحقق مهم
if (!$booking) {
    die("Invalid booking");
}

// ✅ حماية
if ($booking['user_id'] && (!is_logged_in() || $_SESSION['user_id'] != $booking['user_id']) && !is_admin()) {
    die("Unauthorized Access");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Chat - <?php echo clean($booking['service']); ?></title>

    <!-- ✅ Tailwind الصحيح -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        .chat-container { height: calc(100vh - 200px); overflow-y: auto; }
        .bubble-user { background: #059669; color: white; border-radius: 18px 18px 2px 18px; margin-left: auto; }
        .bubble-admin { background: #e5e7eb; color: #1f2937; border-radius: 18px 18px 18px 2px; }
    </style>
</head>

<body class="bg-stone-50 h-screen flex flex-col">

<header class="p-4 bg-white border-b shadow-sm flex justify-between items-center">
    <div>
        <h2 class="font-bold text-lg text-emerald-800">
            <?php echo clean($booking['service']); ?>
        </h2>
        <p class="text-xs text-gray-400">
            Token: <?php echo clean($token); ?>
        </p>
    </div>

    <a href="my_bookings.php" class="text-gray-500 hover:text-emerald-600">
        &larr; Back
    </a>
</header>

<!-- ✅ Chat -->
<div id="chat-box" class="chat-container p-4 space-y-4"></div>

<!-- ✅ Send -->
<form id="msgForm" class="p-4 bg-white border-t flex gap-2">
    <input type="hidden" name="token" value="<?php echo clean($token); ?>">

    <input type="text" id="message" name="message"
        placeholder="Type your message..."
        class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500"
        required>

    <button type="submit"
        class="bg-emerald-600 text-white p-2 rounded-full w-10 h-10 flex items-center justify-center hover:bg-emerald-700">
        ➤
    </button>
</form>

<script>
const chatBox = document.getElementById('chat-box');
const msgForm = document.getElementById('msgForm');

// ✅ جلب الرسائل
function fetchMessages() {
    fetch(`fetch_messages.php?token=<?php echo $token; ?>`)
        .then(res => res.text())
        .then(html => {
            const wasAtBottom = chatBox.scrollHeight - chatBox.scrollTop <= chatBox.clientHeight + 1;
            chatBox.innerHTML = html;
            if (wasAtBottom) chatBox.scrollTop = chatBox.scrollHeight;
        });
}
let typing = false;

document.getElementById("message").addEventListener("input", () => {
    typing = true;

    fetch("typing.php", {
        method: "POST",
        body: new URLSearchParams({
            token: "<?php echo $token; ?>",
            typing: 1
        })
    });

    setTimeout(() => typing = false, 2000);
});
// ✅ إرسال رسالة
msgForm.onsubmit = (e) => {
    e.preventDefault();

    const formData = new FormData(msgForm);

    fetch('send_message.php', {
        method: 'POST',
        body: formData
    })
    .then(() => {
        msgForm.reset();
        fetchMessages();
    });
};

// ✅ تحديث تلقائي
setInterval(fetchMessages, 3000);
fetchMessages();
</script>

</body>
</html>