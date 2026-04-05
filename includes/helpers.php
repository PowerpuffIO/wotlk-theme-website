<?php
if (!function_exists('str_starts_with')) {
    function str_starts_with($haystack, $needle) {
        if ($needle === '') {
            return true;
        }
        return strncmp($haystack, $needle, strlen($needle)) === 0;
    }
}
function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function redirect($url, $code = 302) {
    header('Location: ' . $url, true, $code);
    exit;
}

function csrf_token() {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_verify($token) {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], (string)$token);
}

function base_url($path = '') {
    $b = rtrim(BASE_PATH, '/');
    $p = ltrim($path, '/');
    if ($b === '') {
        return '/' . $p;
    }
    return $b . '/' . $p;
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!current_user()) {
        redirect(base_url('login?return=' . urlencode($_SERVER['REQUEST_URI'] ?? '/')));
    }
}

function require_admin() {
    require_login();
    $u = current_user();
    if (!$u || (int)($u['role'] ?? 0) !== 1) {
        http_response_code(403);
        exit('Forbidden');
    }
}

function detect_lang() {
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['ru', 'en'], true)) {
        return $_COOKIE['lang'];
    }
    $al = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '';
    if (preg_match('/^([a-z]{2})/i', $al, $m)) {
        $c = strtolower($m[1]);
        if ($c === 'ru') {
            return 'ru';
        }
    }
    return DEFAULT_LANG === 'ru' ? 'ru' : 'en';
}

function set_lang($lang) {
    if (!in_array($lang, ['ru', 'en'], true)) {
        return;
    }
    $_SESSION['lang'] = $lang;
    setcookie('lang', $lang, [
        'expires' => time() + 365 * 86400,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
}

function active_lang() {
    if (!empty($_COOKIE['lang']) && in_array($_COOKIE['lang'], ['ru', 'en'], true)) {
        if (($_SESSION['lang'] ?? '') !== $_COOKIE['lang']) {
            $_SESSION['lang'] = $_COOKIE['lang'];
        }
        return $_COOKIE['lang'];
    }
    return $_SESSION['lang'] ?? detect_lang();
}

function news_image_url($stored) {
    $s = trim((string)$stored);
    if ($s === '') {
        return base_url('Assets/images/hero.jpg');
    }
    if (preg_match('#^https?://#i', $s)) {
        return $s;
    }
    return base_url(ltrim($s, '/'));
}

function discord_widget_iframe_src() {
    $id = preg_replace('/\D/', '', setting_get('discord_guild_id', ''));
    if ($id === '') {
        return '';
    }
    $theme = setting_get('discord_widget_theme', 'dark');
    if (!in_array($theme, ['dark', 'light'], true)) {
        $theme = 'dark';
    }
    return 'https://discord.com/widget?id=' . rawurlencode($id) . '&theme=' . rawurlencode($theme);
}
