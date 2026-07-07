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
    
    $body = array(
        "api_key" => $wa_token,
        "receiver" => $target,
        "data" => array("message" => $message)
    );

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $wa_api_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($body),
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Accept: */*"
        ),
    ));
    $res = curl_exec($curl);
    curl_close($curl);
    
    return $res;
}
?>
