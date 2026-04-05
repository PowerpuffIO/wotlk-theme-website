<?php

require_once __DIR__ . '/soap.php';

function shop_is_enabled() {
    return setting_get('shop_enabled', '0') === '1';
}

function shop_mail_subject() {
    return defined('SHOP_MAIL_SUBJECT') ? (string)SHOP_MAIL_SUBJECT : 'Shop';
}

function shop_mail_body() {
    return defined('SHOP_MAIL_BODY') ? (string)SHOP_MAIL_BODY : '';
}

function shop_wowhead_item_url($entry, $lang) {
    $e = (int)$entry;
    if ($lang === 'ru') {
        return 'https://www.wowhead.com/wotlk/ru/item=' . $e;
    }
    return 'https://www.wowhead.com/wotlk/item=' . $e;
}

function shop_category_display_name(array $row, $lang) {
    return ($lang === 'ru') ? (string)$row['name_ru'] : (string)$row['name_en'];
}

function shop_categories_filter_tree($lang) {
    try {
        $rows = site_pdo()->query('SELECT id, parent_id, name_ru, name_en, sort_order FROM shop_categories ORDER BY parent_id, sort_order, id')->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
    $isRu = ($lang === 'ru');
    $parents = [];
    $byPid = [];
    foreach ($rows as $r) {
        if ((int)$r['parent_id'] !== 0) {
            continue;
        }
        $id = (int)$r['id'];
        $parents[] = [
            'id' => $id,
            'name' => $isRu ? $r['name_ru'] : $r['name_en'],
            'subs' => [],
        ];
        $byPid[$id] = count($parents) - 1;
    }
    foreach ($rows as $r) {
        $pid = (int)$r['parent_id'];
        if ($pid < 1 || !isset($byPid[$pid])) {
            continue;
        }
        $parents[$byPid[$pid]]['subs'][] = [
            'id' => (int)$r['id'],
            'name' => $isRu ? $r['name_ru'] : $r['name_en'],
        ];
    }
    return $parents;
}

function shop_categories_tree() {
    $pdo = site_pdo();
    $rows = $pdo->query('SELECT id, parent_id, name_ru, name_en, sort_order FROM shop_categories ORDER BY parent_id, sort_order, id')->fetchAll();
    $parents = [];
    $children = [];
    foreach ($rows as $r) {
        if ((int)$r['parent_id'] === 0) {
            $parents[] = $r;
        } else {
            $pid = (int)$r['parent_id'];
            if (!isset($children[$pid])) {
                $children[$pid] = [];
            }
            $children[$pid][] = $r;
        }
    }
    return ['parents' => $parents, 'children' => $children];
}

function shop_subcategories_flat() {
    try {
        return site_pdo()->query('SELECT c.id, c.parent_id, c.name_ru, c.name_en, p.name_ru AS parent_name_ru, p.name_en AS parent_name_en FROM shop_categories c INNER JOIN shop_categories p ON p.id = c.parent_id WHERE c.parent_id > 0 ORDER BY p.sort_order, c.sort_order')->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function shop_items_for_display() {
    try {
        $sql = 'SELECT si.id, si.item_entry, si.price, si.quantity, si.sort_order,
            c.id AS subcategory_id, c.name_ru AS sub_name_ru, c.name_en AS sub_name_en,
            p.id AS category_id, p.name_ru AS cat_name_ru, p.name_en AS cat_name_en
            FROM shop_items si
            INNER JOIN shop_categories c ON c.id = si.subcategory_id
            INNER JOIN shop_categories p ON p.id = c.parent_id
            WHERE si.enabled = 1 AND c.parent_id > 0
            ORDER BY p.sort_order, c.sort_order, si.sort_order, si.id';
        return site_pdo()->query($sql)->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function shop_user_characters($authAccountId) {
    $aid = (int)$authAccountId;
    if ($aid < 1) {
        return [];
    }
    try {
        $st = characters_pdo()->prepare('SELECT name, level, guid FROM characters WHERE account = ? ORDER BY level DESC, name ASC');
        $st->execute([$aid]);
        return $st->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function shop_get_item_row($id) {
    try {
        $pdo = site_pdo();
        $st = $pdo->prepare('SELECT si.*, c.parent_id FROM shop_items si INNER JOIN shop_categories c ON c.id = si.subcategory_id WHERE si.id = ? AND si.enabled = 1');
        $st->execute([(int)$id]);
        $row = $st->fetch();
        if (!$row || (int)$row['parent_id'] < 1) {
            return null;
        }
        return $row;
    } catch (Throwable $e) {
        return null;
    }
}

function shop_verify_character_name($authAccountId, $name) {
    $name = trim((string)$name);
    if ($name === '' || strlen($name) > 12) {
        return null;
    }
    try {
        $st = characters_pdo()->prepare('SELECT name, guid FROM characters WHERE account = ? AND name = ? LIMIT 1');
        $st->execute([(int)$authAccountId, $name]);
        return $st->fetch() ?: null;
    } catch (Throwable $e) {
        return null;
    }
}

function shop_handle_buy() {
    header('Content-Type: application/json; charset=utf-8');
    if (empty($_SESSION['uid'])) {
        echo json_encode(['ok' => false, 'code' => 'auth']);
        exit;
    }
    if (!csrf_verify($_POST['csrf'] ?? '')) {
        echo json_encode(['ok' => false, 'code' => 'csrf', 'message' => __t('error_generic')]);
        exit;
    }
    if (!shop_is_enabled()) {
        echo json_encode(['ok' => false, 'code' => 'disabled', 'message' => __t('shop_toast_disabled')]);
        exit;
    }
    if (!class_exists('SoapClient')) {
        echo json_encode(['ok' => false, 'code' => 'soap', 'message' => __t('shop_toast_soap')]);
        exit;
    }
    if (acore_soap_options_from_config() === null) {
        echo json_encode(['ok' => false, 'code' => 'soap', 'message' => __t('shop_toast_soap')]);
        exit;
    }
    $itemId = (int)($_POST['item_id'] ?? 0);
    $charName = trim((string)($_POST['character_name'] ?? ''));
    if ($itemId < 1 || $charName === '') {
        echo json_encode(['ok' => false, 'code' => 'bad', 'message' => __t('error_generic')]);
        exit;
    }
    $u = current_user();
    if (!$u) {
        echo json_encode(['ok' => false, 'code' => 'auth']);
        exit;
    }
    $uid = (int)$u['id'];
    $authId = (int)$u['auth_account_id'];
    $item = shop_get_item_row($itemId);
    if (!$item) {
        echo json_encode(['ok' => false, 'code' => 'item', 'message' => __t('error_generic')]);
        exit;
    }
    $char = shop_verify_character_name($authId, $charName);
    if (!$char) {
        echo json_encode(['ok' => false, 'code' => 'char', 'message' => __t('shop_char_invalid')]);
        exit;
    }
    $price = (int)$item['price'];
    $qty = max(1, (int)$item['quantity']);
    $entry = (int)$item['item_entry'];
    $pdo = site_pdo();
    $pdo->beginTransaction();
    try {
        $st = $pdo->prepare('SELECT id, balance FROM users WHERE id = ? FOR UPDATE');
        $st->execute([$uid]);
        $row = $st->fetch();
        if (!$row || (int)$row['balance'] < $price) {
            $pdo->rollBack();
            echo json_encode(['ok' => false, 'code' => 'funds', 'message' => __t('shop_toast_funds')]);
            exit;
        }
        $pdo->prepare('UPDATE users SET balance = balance - ? WHERE id = ?')->execute([$price, $uid]);
        $pdo->commit();
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        echo json_encode(['ok' => false, 'code' => 'error', 'message' => __t('error_generic')]);
        exit;
    }
    $subject = shop_mail_subject();
    $body = shop_mail_body();
    $cmd = '.send items ' . $char['name'] . ' "' . str_replace(['"', '\\'], '', $subject) . '" "' . str_replace(['"', '\\'], '', $body) . '" ' . $entry . '[:' . $qty . ']';
    $soap = acore_soap_execute_command($cmd);
    if (!$soap['ok']) {
        try {
            $pdo->beginTransaction();
            $pdo->prepare('UPDATE users SET balance = balance + ? WHERE id = ?')->execute([$price, $uid]);
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
        }
        echo json_encode(['ok' => false, 'code' => 'soap', 'message' => __t('shop_toast_soap')]);
        exit;
    }
    $st = $pdo->prepare('SELECT balance FROM users WHERE id = ?');
    $st->execute([$uid]);
    $br = $st->fetch();
    if ($br && isset($_SESSION['user'])) {
        $_SESSION['user']['balance'] = (int)$br['balance'];
    }
    echo json_encode(['ok' => true, 'code' => 'ok', 'message' => __t('shop_toast_ok'), 'balance' => (int)($br['balance'] ?? 0)]);
    exit;
}
