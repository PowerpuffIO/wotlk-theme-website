<?php

function captcha_is_active() {
    $t = (int)CAPTCHA_TYPE;
    if ($t === 0) {
        return false;
    }
    if ($t === 1) {
        return CAPTCHA_GOOGLE_SITE_KEY !== '' && CAPTCHA_GOOGLE_SECRET !== '';
    }
    if ($t === 2) {
        return CAPTCHA_TURNSTILE_SITE_KEY !== '' && CAPTCHA_TURNSTILE_SECRET !== '';
    }
    return false;
}

function captcha_render_form_fields() {
    if (!captcha_is_active()) {
        return;
    }
    $t = (int)CAPTCHA_TYPE;
    if ($t === 1) {
        echo '<script src="https://www.google.com/recaptcha/api.js" async defer></script>';
        echo '<div class="g-recaptcha" data-sitekey="' . h(CAPTCHA_GOOGLE_SITE_KEY) . '"></div>';
        return;
    }
    if ($t === 2) {
        echo '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>';
        echo '<div class="cf-turnstile" data-sitekey="' . h(CAPTCHA_TURNSTILE_SITE_KEY) . '" data-theme="dark"></div>';
    }
}

function captcha_http_post($url, $fields) {
    $body = http_build_query($fields);
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 10,
        ]);
        $out = curl_exec($ch);
        curl_close($ch);
        return $out !== false ? $out : '';
    }
    $ctx = stream_context_create([
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $body,
            'timeout' => 10,
        ],
    ]);
    $out = @file_get_contents($url, false, $ctx);
    return is_string($out) ? $out : '';
}

function captcha_verify_request() {
    if (!captcha_is_active()) {
        return true;
    }
    $t = (int)CAPTCHA_TYPE;
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($t === 1) {
        $response = $_POST['g-recaptcha-response'] ?? '';
        if ($response === '') {
            return false;
        }
        $raw = captcha_http_post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => CAPTCHA_GOOGLE_SECRET,
            'response' => $response,
            'remoteip' => $ip,
        ]);
        $json = json_decode($raw, true);
        return is_array($json) && !empty($json['success']);
    }
    if ($t === 2) {
        $response = $_POST['cf-turnstile-response'] ?? '';
        if ($response === '') {
            return false;
        }
        $raw = captcha_http_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
            'secret' => CAPTCHA_TURNSTILE_SECRET,
            'response' => $response,
            'remoteip' => $ip,
        ]);
        $json = json_decode($raw, true);
        return is_array($json) && !empty($json['success']);
    }
    return true;
}
