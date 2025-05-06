<?php

function sendMessage($chat_id, $message) {
    $token = "7540390967:AAHe3SyuSz1WzqhbdspzMcRa5Ol7cCo6BVo"; // Ganti dengan token bot Anda
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];

    $options = [
        'http' => [
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data)
        ]
    ];

    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);

    return $result;
}

// Kirim pesan
$chat_id = "7821603325"; // Ganti dengan ID chat Anda
$message = "Halo! Ini pesan otomatis dari bot PHP.";
$response = sendMessage($chat_id, $message);

echo $response;
?>
