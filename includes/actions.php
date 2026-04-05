<?php
require_once __DIR__ . '/mail.php';

function handle_post($route) {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return false;
    }
    $parts = explode('/', trim($route, '/'));
    if (($parts[0] ?? '') === 'profile' && ($parts[1] ?? '') === 'shop' && ($parts[2] ?? '') === 'buy') {
        require_once __DIR__ . '/shop.php';
        shop_handle_buy();
    }
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        $_SESSION['flash_err'] = 'CSRF';
        redirect($_SERVER['HTTP_REFERER'] ?? base_url());
        return true;
    }
    $a = $parts[0] ?? '';

    if ($a === 'register' && ($parts[1] ?? '') === '') {
        action_register();
        return true;
    }
    if ($a === 'login' && ($parts[1] ?? '') === '') {
        action_login();
        return true;
    }
    if ($a === 'profile' && ($parts[1] ?? '') === 'settings' && ($parts[2] ?? '') === 'password-request') {
        require_login();
        action_password_request();
        return true;
    }
    if ($a === 'profile' && ($parts[1] ?? '') === 'vote' && ($parts[2] ?? '') === 'claim') {
        require_login();
        action_vote_claim();
        return true;
    }
    if ($a === 'news-vote') {
        require_login();
        action_news_vote();
        return true;
    }
    if ($a === 'profile' && ($parts[1] ?? '') === 'message') {
        require_login();
        if (($parts[2] ?? '') === 'new') {
            action_ticket_new();
            return true;
        }
        if (($parts[2] ?? '') === 'reply') {
            action_ticket_reply();
            return true;
        }
    }
    if ($a === 'profile' && ($parts[1] ?? '') === 'adminpanel') {
        require_admin();
        $sub = $parts[2] ?? 'dash';
        if ($sub === 'news-save') {
            action_admin_news_save();
            return true;
        }
        if ($sub === 'news-del') {
            action_admin_news_del();
            return true;
        }
        if ($sub === 'settings-save') {
            action_admin_settings_save();
            return true;
        }
        if ($sub === 'ticket-reply') {
            action_admin_ticket_reply();
            return true;
        }
        if ($sub === 'ticket-close') {
            action_admin_ticket_close();
            return true;
        }
        if ($sub === 'ticket-del') {
            action_admin_ticket_del();
            return true;
        }
        if ($sub === 'shop-save') {
            action_admin_shop_save();
            return true;
        }
        if ($sub === 'shop-item-add') {
            action_admin_shop_item_add();
            return true;
        }
        if ($sub === 'shop-item-del') {
            action_admin_shop_item_del();
            return true;
        }
    }
    if ($a === 'reset-password' && ($parts[1] ?? '') === 'confirm') {
        action_reset_confirm();
        return true;
    }
    return false;
}

function action_register() {
    if (!extension_loaded('gmp')) {
        $_SESSION['flash_err'] = 'gmp';
        redirect(base_url('register'));
        return;
    }
    if (!captcha_verify_request()) {
        $_SESSION['flash_err'] = 'captcha';
        redirect(base_url('register'));
        return;
    }
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if (strlen($username) < 3 || strlen($username) > 32) {
        $_SESSION['flash_err'] = 'user';
        redirect(base_url('register'));
        return;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['flash_err'] = 'email';
        redirect(base_url('register'));
        return;
    }
    if ($pass !== $pass2 || strlen($pass) < 6) {
        $_SESSION['flash_err'] = 'pass';
        redirect(base_url('register'));
        return;
    }
    $uname = acore_username_upper($username);
    $pdoSite = site_pdo();
    $st = $pdoSite->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
    $st->execute([$uname, $email]);
    if ($st->fetch()) {
        $_SESSION['flash_err'] = 'exists';
        redirect(base_url('register'));
        return;
    }
    $auth = auth_pdo();
    $st = $auth->prepare('SELECT id FROM account WHERE username = ?');
    $st->execute([$uname]);
    if ($st->fetch()) {
        $_SESSION['flash_err'] = 'exists';
        redirect(base_url('register'));
        return;
    }
    [$salt, $verifier] = acore_make_registration_data($username, $pass);
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    try {
        $auth->beginTransaction();
        $ins = $auth->prepare('INSERT INTO account (username, salt, verifier, joindate, email, expansion) VALUES (?, ?, ?, NOW(), ?, 2)');
        $ins->execute([$uname, $salt, $verifier, $email]);
        $aid = (int)$auth->lastInsertId();
        $auth->commit();
    } catch (Throwable $e) {
        if ($auth->inTransaction()) {
            $auth->rollBack();
        }
        $_SESSION['flash_err'] = 'auth';
        redirect(base_url('register'));
        return;
    }
    try {
        $ins2 = $pdoSite->prepare('INSERT INTO users (username, email, password_hash, auth_account_id, role, balance, lang, created_at) VALUES (?, ?, ?, ?, 0, 0, ?, NOW())');
        $ins2->execute([$uname, $email, $hash, $aid, active_lang()]);
    } catch (Throwable $e) {
        try {
            $d = $auth->prepare('DELETE FROM account WHERE id = ?');
            $d->execute([$aid]);
        } catch (Throwable $e2) {
        }
        $_SESSION['flash_err'] = 'site';
        redirect(base_url('register'));
        return;
    }
    $_SESSION['flash_ok'] = 1;
    redirect(base_url('login'));
}

function action_login() {
    if (!captcha_verify_request()) {
        $_SESSION['flash_err'] = 'captcha';
        redirect(base_url('login'));
        return;
    }
    $username = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';
    $uname = acore_username_upper($username);
    $st = site_pdo()->prepare('SELECT * FROM users WHERE username = ?');
    $st->execute([$uname]);
    $u = $st->fetch();
    if (!$u || !password_verify($pass, $u['password_hash'])) {
        $_SESSION['flash_err'] = 'login';
        redirect(base_url('login'));
        return;
    }
    session_regenerate_id(true);
    $_SESSION['uid'] = (int)$u['id'];
    $_SESSION['user'] = $u;
    $ret = $_GET['return'] ?? $_POST['return'] ?? '';
    if (is_string($ret) && str_starts_with($ret, '/') && !str_starts_with($ret, '//')) {
        redirect($ret);
        return;
    }
    redirect(base_url('profile'));
}

function action_password_request() {
    $u = current_user();
    $token = bin2hex(random_bytes(32));
    $exp = date('Y-m-d H:i:s', time() + 3600);
    $st = site_pdo()->prepare('UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?');
    $st->execute([$token, $exp, $u['id']]);
    $link = rtrim(SITE_PUBLIC_URL, '/') . base_url('reset-password?token=' . $token);
    $html = '<p><a href="' . h($link) . '">' . h($link) . '</a></p>';
    send_mail_raw($u['email'], 'Password reset', $html);
    $_SESSION['flash_ok'] = 'mail';
    redirect(base_url('profile/settings'));
}

function action_reset_confirm() {
    $token = $_POST['token'] ?? '';
    $pass = $_POST['password'] ?? '';
    $pass2 = $_POST['password2'] ?? '';
    if ($pass !== $pass2 || strlen($pass) < 6) {
        $_SESSION['flash_err'] = 'pass';
        redirect(base_url('reset-password?token=' . urlencode($token)));
        return;
    }
    $st = site_pdo()->prepare('SELECT * FROM users WHERE reset_token = ? AND reset_expires > NOW()');
    $st->execute([$token]);
    $u = $st->fetch();
    if (!$u) {
        $_SESSION['flash_err'] = 'token';
        redirect(base_url('login'));
        return;
    }
    [$salt, $verifier] = acore_make_registration_data($u['username'], $pass);
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    try {
        $auth = auth_pdo();
        $auth->prepare('UPDATE account SET salt = ?, verifier = ? WHERE id = ?')->execute([$salt, $verifier, $u['auth_account_id']]);
        site_pdo()->prepare('UPDATE users SET password_hash = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?')->execute([$hash, $u['id']]);
    } catch (Throwable $e) {
        $_SESSION['flash_err'] = 'upd';
        redirect(base_url('reset-password?token=' . urlencode($token)));
        return;
    }
    $_SESSION['flash_ok'] = 'pwd';
    redirect(base_url('login'));
}

function action_vote_claim() {
    $u = current_user();
    $api = setting_get('mmorating_api_key');
    $bonus = (int)setting_get('vote_bonus', '10');
    $r = mmorating_check_flexible($api, $u['email']);
    if (!$r['ok'] || empty($r['has_voted'])) {
        $_SESSION['flash_err'] = 'vote';
        redirect(base_url('profile/vote'));
        return;
    }
    $today = date('Y-m-d');
    $pdo = site_pdo();
    try {
        $pdo->beginTransaction();
        $st = $pdo->prepare('INSERT INTO vote_log (user_id, claimed_at, amount) VALUES (?, ?, ?)');
        $st->execute([$u['id'], $today, $bonus]);
        $pdo->prepare('UPDATE users SET balance = balance + ? WHERE id = ?')->execute([$bonus, $u['id']]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['flash_err'] = 'claimed';
        redirect(base_url('profile/vote'));
        return;
    }
    $_SESSION['user']['balance'] = (int)$u['balance'] + $bonus;
    $_SESSION['flash_ok'] = 'vote';
    redirect(base_url('profile/vote'));
}

function action_news_vote() {
    $nid = (int)($_POST['news_id'] ?? 0);
    $val = (int)($_POST['value'] ?? 0);
    if ($nid < 1 || ($val !== 1 && $val !== -1)) {
        redirect(base_url());
        return;
    }
    $u = current_user();
    $pdo = site_pdo();
    $pdo->prepare('INSERT INTO news_votes (news_id, user_id, value) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE value = VALUES(value)')->execute([$nid, $u['id'], $val]);
    $ref = $_SERVER['HTTP_REFERER'] ?? base_url();
    redirect($ref);
}

function action_ticket_new() {
    $sub = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    if ($sub === '' || $body === '') {
        $_SESSION['flash_err'] = 'ticket';
        redirect(base_url('profile/message'));
        return;
    }
    $u = current_user();
    $pdo = site_pdo();
    $pdo->prepare('INSERT INTO tickets (user_id, subject, status, updated_at, created_at) VALUES (?, ?, \'open\', NOW(), NOW())')->execute([$u['id'], $sub]);
    $tid = (int)$pdo->lastInsertId();
    $pdo->prepare('INSERT INTO ticket_messages (ticket_id, user_id, is_staff, body, created_at) VALUES (?, ?, 0, ?, NOW())')->execute([$tid, $u['id'], $body]);
    redirect(base_url('profile/message?ticket=' . $tid));
}

function action_ticket_reply() {
    $tid = (int)($_POST['ticket_id'] ?? 0);
    $body = trim($_POST['body'] ?? '');
    if ($tid < 1 || $body === '') {
        redirect(base_url('profile/message'));
        return;
    }
    $u = current_user();
    $st = site_pdo()->prepare('SELECT id FROM tickets WHERE id = ? AND user_id = ? AND status = \'open\'');
    $st->execute([$tid, $u['id']]);
    if (!$st->fetch()) {
        redirect(base_url('profile/message'));
        return;
    }
    site_pdo()->prepare('INSERT INTO ticket_messages (ticket_id, user_id, is_staff, body, created_at) VALUES (?, ?, 0, ?, NOW())')->execute([$tid, $u['id'], $body]);
    site_pdo()->prepare('UPDATE tickets SET updated_at = NOW() WHERE id = ?')->execute([$tid]);
    redirect(base_url('profile/message?ticket=' . $tid));
}

function action_admin_news_save() {
    $id = (int)($_POST['id'] ?? 0);
    $pdo = site_pdo();
    $imagePath = trim((string)($_POST['image_existing'] ?? ''));
    $up = $_FILES['news_image'] ?? null;
    if (is_array($up) && isset($up['tmp_name'], $up['error']) && $up['error'] === UPLOAD_ERR_OK && $up['tmp_name'] !== '' && is_uploaded_file($up['tmp_name'])) {
        $f = $up;
        $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed, true)) {
            $_SESSION['flash_err'] = 'upload';
            redirect(base_url('profile/adminpanel/news' . ($id ? '?edit=' . $id : '')));
            return;
        }
        $dir = defined('NEWS_STORAGE_PATH') ? NEWS_STORAGE_PATH : (SITE_ROOT . '/storage/news');
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
                $_SESSION['flash_err'] = 'upload';
                redirect(base_url('profile/adminpanel/news' . ($id ? '?edit=' . $id : '')));
                return;
            }
        }
        $name = bin2hex(random_bytes(16)) . '.' . $ext;
        $dest = $dir . '/' . $name;
        if (!move_uploaded_file($f['tmp_name'], $dest)) {
            $_SESSION['flash_err'] = 'upload';
            redirect(base_url('profile/adminpanel/news' . ($id ? '?edit=' . $id : '')));
            return;
        }
        if ($imagePath !== '' && str_starts_with($imagePath, 'storage/news/')) {
            $old = SITE_ROOT . '/' . $imagePath;
            if (is_file($old)) {
                @unlink($old);
            }
        }
        $imagePath = 'storage/news/' . $name;
    }
    $fields = [
        trim($_POST['title_ru'] ?? ''),
        trim($_POST['title_en'] ?? ''),
        trim($_POST['excerpt_ru'] ?? ''),
        trim($_POST['excerpt_en'] ?? ''),
        trim($_POST['body_ru'] ?? ''),
        trim($_POST['body_en'] ?? ''),
        $imagePath,
    ];
    if ($id > 0) {
        $pdo->prepare('UPDATE news SET title_ru=?, title_en=?, excerpt_ru=?, excerpt_en=?, body_ru=?, body_en=?, image=? WHERE id=?')->execute(array_merge($fields, [$id]));
    } else {
        $pdo->prepare('INSERT INTO news (title_ru, title_en, excerpt_ru, excerpt_en, body_ru, body_en, image, created_at) VALUES (?,?,?,?,?,?,?,NOW())')->execute($fields);
    }
    redirect(base_url('profile/adminpanel/news'));
}

function action_admin_news_del() {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        $st = site_pdo()->prepare('SELECT image FROM news WHERE id = ?');
        $st->execute([$id]);
        $row = $st->fetch();
        if ($row && !empty($row['image']) && str_starts_with((string)$row['image'], 'storage/news/')) {
            $p = SITE_ROOT . '/' . $row['image'];
            if (is_file($p)) {
                @unlink($p);
            }
        }
        site_pdo()->prepare('DELETE FROM news WHERE id = ?')->execute([$id]);
    }
    redirect(base_url('profile/adminpanel/news'));
}

function action_admin_settings_save() {
    $tab = $_POST['tab'] ?? '';
    if ($tab === 'vote') {
        setting_set('mmorating_api_key', trim($_POST['mmorating_api_key'] ?? ''));
        setting_set('vote_bonus', (string)max(0, (int)($_POST['vote_bonus'] ?? 0)));
        setting_set('mmorating_vote_url', trim($_POST['mmorating_vote_url'] ?? ''));
    } elseif ($tab === 'download') {
        setting_set('download_torrent', trim($_POST['download_torrent'] ?? ''));
        setting_set('download_direct', trim($_POST['download_direct'] ?? ''));
        setting_set('realmlist', trim($_POST['realmlist'] ?? ''));
    } elseif ($tab === 'social') {
        $gid = preg_replace('/\D/', '', (string)($_POST['discord_guild_id'] ?? ''));
        setting_set('discord_guild_id', $gid);
        $th = (string)($_POST['discord_widget_theme'] ?? 'dark');
        setting_set('discord_widget_theme', in_array($th, ['dark', 'light'], true) ? $th : 'dark');
    }
    $redir = 'vote';
    if ($tab === 'download') {
        $redir = 'download';
    } elseif ($tab === 'social') {
        $redir = 'social';
    }
    redirect(base_url('profile/adminpanel/' . $redir));
}

function action_admin_ticket_reply() {
    $tid = (int)($_POST['ticket_id'] ?? 0);
    $body = trim($_POST['body'] ?? '');
    if ($tid < 1 || $body === '') {
        redirect(base_url('profile/adminpanel/messages'));
        return;
    }
    $pdo = site_pdo();
    $pdo->prepare('INSERT INTO ticket_messages (ticket_id, user_id, is_staff, body, created_at) VALUES (?, NULL, 1, ?, NOW())')->execute([$tid, $body]);
    $pdo->prepare('UPDATE tickets SET updated_at = NOW() WHERE id = ?')->execute([$tid]);
    redirect(base_url('profile/adminpanel/messages?ticket=' . $tid));
}

function action_admin_ticket_close() {
    $tid = (int)($_POST['ticket_id'] ?? 0);
    if ($tid > 0) {
        site_pdo()->prepare('UPDATE tickets SET status = \'closed\', updated_at = NOW() WHERE id = ?')->execute([$tid]);
    }
    redirect(base_url('profile/adminpanel/messages?ticket=' . $tid));
}

function action_admin_ticket_del() {
    $tid = (int)($_POST['ticket_id'] ?? 0);
    if ($tid > 0) {
        $pdo = site_pdo();
        $pdo->prepare('DELETE FROM ticket_messages WHERE ticket_id = ?')->execute([$tid]);
        $pdo->prepare('DELETE FROM tickets WHERE id = ?')->execute([$tid]);
    }
    redirect(base_url('profile/adminpanel/messages'));
}

function action_admin_shop_save() {
    setting_set('shop_enabled', isset($_POST['shop_enabled']) ? '1' : '0');
    redirect(base_url('profile/adminpanel/shop'));
}

function action_admin_shop_item_add() {
    $sub = (int)($_POST['subcategory_id'] ?? 0);
    $entry = (int)($_POST['item_entry'] ?? 0);
    $price = max(0, (int)($_POST['price'] ?? 0));
    $qty = max(1, (int)($_POST['quantity'] ?? 1));
    $pdo = site_pdo();
    $st = $pdo->prepare('SELECT id FROM shop_categories WHERE id = ? AND parent_id > 0');
    $st->execute([$sub]);
    if ($st->fetch() && $entry > 0 && $price >= 0) {
        $pdo->prepare('INSERT INTO shop_items (subcategory_id, item_entry, price, quantity, enabled, sort_order) VALUES (?, ?, ?, ?, 1, 0)')
            ->execute([$sub, $entry, $price, $qty]);
    }
    redirect(base_url('profile/adminpanel/shop'));
}

function action_admin_shop_item_del() {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        site_pdo()->prepare('DELETE FROM shop_items WHERE id = ?')->execute([$id]);
    }
    redirect(base_url('profile/adminpanel/shop'));
}
