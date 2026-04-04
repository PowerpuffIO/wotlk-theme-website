<?php
require_once dirname(__DIR__) . '/configs/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/srp6.php';
require_once __DIR__ . '/i18n.php';
require_once __DIR__ . '/captcha.php';

session_name(SESSION_NAME);
session_start();

if (!empty($_GET['lang']) && in_array($_GET['lang'], ['ru', 'en'], true)) {
    set_lang($_GET['lang']);
    $q = $_GET;
    unset($q['lang']);
    $rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $base = rtrim(BASE_PATH, '/');
    if ($base !== '' && str_starts_with($rawPath, $base)) {
        $rawPath = substr($rawPath, strlen($base)) ?: '/';
    }
    $route = trim($rawPath, '/') ?: 'home';
    $qs = http_build_query($q);
    redirect(base_url($route === 'home' ? '' : $route) . ($qs ? '?' . $qs : ''));
}

if (empty($_SESSION['lang'])) {
    $_SESSION['lang'] = detect_lang();
}

if (!empty($_SESSION['uid'])) {
    try {
        $st = site_pdo()->prepare('SELECT id, username, email, role, balance, created_at FROM users WHERE id = ?');
        $st->execute([(int)$_SESSION['uid']]);
        $row = $st->fetch();
        if ($row) {
            $_SESSION['user'] = $row;
        } else {
            unset($_SESSION['uid'], $_SESSION['user']);
        }
    } catch (Throwable $e) {
        unset($_SESSION['uid'], $_SESSION['user']);
    }
}
