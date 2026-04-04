<?php
function mmorating_check_flexible($apiKey, $email) {
    if ($apiKey === '' || $email === '') {
        return ['ok' => false, 'error' => 'config'];
    }
    $url = MMORATING_API_BASE . '/vote/check-flexible';
    $data = json_encode(['api_key' => $apiKey, 'email' => $email]);
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
        CURLOPT_TIMEOUT => 10,
    ]);
    $response = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === false || $code !== 200) {
        return ['ok' => false, 'error' => 'http'];
    }
    $j = json_decode($response, true);
    if (!is_array($j) || empty($j['success'])) {
        return ['ok' => false, 'error' => $j['error'] ?? 'api'];
    }
    return ['ok' => true, 'has_voted' => !empty($j['has_voted'])];
}
