<?php
include 'config.php';

if(!is_admin()) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<!-- 🔔 صوت الإشعار -->
<audio id="notifSound" src=""C:\xampp\htdocs\gardener_service\ress\notif.mp3""></audio>

<div class="max-w-4xl mx-auto mt-10">

    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-emerald-700">💬 Admin Chats</h1>
        <a href="logout.php" class="bg-red-500 text-white px-4 py-2 rounded-lg">
            Logout
        </a>
    </div>

    <div id="chatList" class="bg-white rounded-xl shadow overflow-hidden"></div>

</div>

<script>

// 🧠 نخزن آخر رسالة
let lastMessage = "";

// 🔄 تحميل الشاتات
function fetchChats() {
    fetch("fetch_admin_chats.php")
        .then(res => res.text())
        .then(data => {

            // 🧠 نجيب آخر رسالة من HTML
            let match = data.match(/data-last="(.*?)"/);

            if (match) {
                let newLast = match[1];

                // 🔔 تشغيل الصوت فقط إذا تغيرت الرسالة
                if (lastMessage && newLast !== lastMessage) {
                    const sound = document.getElementById("notifSound");
                    sound.play().catch(() => {});
                }

                lastMessage = newLast;
            }

            document.getElementById("chatList").innerHTML = data;
        })
        .catch(err => console.log(err));
}

// ⏱ تحديث كل 2 ثانية
setInterval(fetchChats, 2000);

// أول تحميل
fetchChats();

</script>

</body>
</html>