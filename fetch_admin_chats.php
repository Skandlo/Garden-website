<?php
include 'config.php';

if(!is_admin()) exit();

// جلب حسب آخر رسالة
$res = $conn->query("
    SELECT * FROM bookings
    ORDER BY last_message_time DESC
");

while($row = $res->fetch_assoc()) {

    $isUnread = ($row['status'] === 'Pending');

    // ✅ online check
    $isOnline = isset($row['last_seen']) && (strtotime($row['last_seen']) > time() - 60);

    echo '<div class="chat-item border-b hover:bg-gray-50 cursor-pointer ' . ($isUnread ? 'bg-emerald-50' : '') . '"
            data-last="' . $row['last_message_time'] . '"
            onclick="window.location.href=\'admin_chat.php?token=' . clean($row['chat_token']) . '\'">';

        echo '<div class="flex justify-between p-4">';

            echo '<div>';
                echo '<h2 class="font-semibold">' . clean($row['name']) . '</h2>';

                // 🟢 Online / Offline
                if($isOnline) {
                    echo '<span style="color:green; font-size:12px;">● Online</span>';
                } else {
                    echo '<span style="color:gray; font-size:12px;">● Offline</span>';
                }

                echo '<p class="text-sm text-gray-500">'
                     . clean($row['last_message'] ?? 'No messages yet') .
                     '</p>';
            echo '</div>';

            echo '<div class="text-right">';

                if($row['last_message_time']) {
                    echo '<p class="text-xs text-gray-400">'
                         . date('H:i', strtotime($row['last_message_time'])) .
                         '</p>';
                }

                if($isUnread) {
                    echo '<span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                            New
                          </span>';
                }

            echo '</div>';

        echo '</div>';

    echo '</div>';
}
?>