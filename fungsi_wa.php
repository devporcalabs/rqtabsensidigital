<?php
/**
 * Helper function to send WhatsApp messages via the custom WA Gateway API.
 *
 * @param string $target The receiver's WhatsApp number.
 * @param string $message The message content to send.
 * @param string $wa_token The API key / token.
 * @param string $wa_api_url The endpoint URL of the custom WA Gateway.
 * @return string|bool Response from the cURL request or false if params are empty.
 */
function sendWa($target, $message, $wa_token, $wa_api_url) {
    if (empty($target) || empty($wa_token) || empty($wa_api_url)) {
        return false;
    }

    $is_sidobe = (strpos($wa_api_url, 'sidobe.com') !== false);
    $is_fonnte = (strpos($wa_api_url, 'fonnte.com') !== false);

    if ($is_sidobe) {
        // Format nomor target ke format E.164 (+62...)
        $phone = preg_replace('/[^0-9]/', '', $target);
        if (substr($phone, 0, 2) === '62') {
            $phone = '+' . $phone;
        } elseif (substr($phone, 0, 1) === '0') {
            $phone = '+62' . substr($phone, 1);
        } elseif (substr($phone, 0, 1) !== '+') {
            $phone = '+' . $phone;
        }

        $payload = json_encode([
            "phone"    => $phone,
            "message"  => $message,
            "is_async" => true
        ]);
        $headers = [
            'Content-Type: application/json',
            'X-Secret-Key: ' . $wa_token
        ];
    } elseif ($is_fonnte) {
        $payload = http_build_query([
            'target'      => $target,
            'message'     => $message,
            'countryCode' => '62',
        ]);
        $headers = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: ' . $wa_token
        ];
    } else {
        // Fallback untuk Custom / Generic WA Gateway
        $payload = json_encode([
            "api_key"  => $wa_token,
            "receiver" => $target,
            "data"     => ["message" => $message]
        ]);
        $headers = [
            "Content-Type: application/json",
            "Accept: */*",
            "Authorization: " . $wa_token
        ];
    }

    // 1. Coba gunakan cURL jika ekstensi terpasang dan fungsi tersedia
    if (function_exists('curl_init')) {
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $wa_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ]);

        $res = curl_exec($curl);
        curl_close($curl);

        if ($res !== false) {
            return $res;
        }
    }

    // 2. Fallback native PHP Stream (file_get_contents) jika cURL tidak terpasang / dinonaktifkan
    $header_str = implode("\r\n", $headers) . "\r\n";
    $options = [
        'http' => [
            'method'        => 'POST',
            'header'        => $header_str,
            'content'       => $payload,
            'timeout'       => 30,
            'ignore_errors' => true // Tangkap pesan response meskipun HTTP status 4xx / 5xx
        ],
        'ssl' => [
            'verify_peer'      => false,
            'verify_peer_name' => false
        ]
    ];

    $context = stream_context_create($options);
    $res = @file_get_contents($wa_api_url, false, $context);

    if ($res === false) {
        return json_encode([
            'status'     => false,
            'is_success' => false,
            'reason'     => 'Gagal menghubungi server WA Gateway (Network / Stream Error)'
        ]);
    }

    return $res;
}
?>
