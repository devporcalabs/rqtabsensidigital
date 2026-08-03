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
    
    $curl = curl_init();

    // Pengecekan jika menggunakan Fonnte WA Gateway (URL default: https://api.fonnte.com/send)
    if (strpos($wa_api_url, 'fonnte.com') !== false) {
        curl_setopt_array($curl, array(
            CURLOPT_URL => $wa_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array(
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: ' . $wa_token
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ));
    } else {
        // Fallback untuk Custom / Generic WA Gateway
        $body = array(
            "api_key"  => $wa_token,
            "receiver" => $target,
            "data"     => array("message" => $message)
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => $wa_api_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Accept: */*",
                "Authorization: " . $wa_token
            ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
        ));
    }

    $res = curl_exec($curl);
    $curl_error = curl_error($curl);
    curl_close($curl);
    
    if ($res === false && !empty($curl_error)) {
        return json_encode([
            'status' => false,
            'reason' => 'cURL Error: ' . $curl_error
        ]);
    }
    
    return $res;
}
?>
