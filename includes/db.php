<?php
function site_pdo() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . SITE_DB_HOST . ';dbname=' . SITE_DB_NAME . ';charset=' . SITE_DB_CHARSET;
        $pdo = new PDO($dsn, SITE_DB_USER, SITE_DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function auth_pdo() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . AUTH_DB_HOST . ';dbname=' . AUTH_DB_NAME . ';charset=' . AUTH_DB_CHARSET;
        $pdo = new PDO($dsn, AUTH_DB_USER, AUTH_DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function characters_pdo() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . CHARACTERS_DB_HOST . ';dbname=' . CHARACTERS_DB_NAME . ';charset=' . CHARACTERS_DB_CHARSET;
        $pdo = new PDO($dsn, CHARACTERS_DB_USER, CHARACTERS_DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}

function setting_get($key, $default = '') {
    $st = site_pdo()->prepare('SELECT svalue FROM site_settings WHERE skey = ?');
    $st->execute([$key]);
    $row = $st->fetch();
    return $row ? (string)$row['svalue'] : $default;
}

function setting_set($key, $value) {
    $pdo = site_pdo();
    $st = $pdo->prepare('INSERT INTO site_settings (skey, svalue) VALUES (?, ?) ON DUPLICATE KEY UPDATE svalue = VALUES(svalue)');
    $st->execute([$key, $value]);
}
