<?php
function realm_faction_online() {
    try {
        $pdo = characters_pdo();
        $sql = 'SELECT 
            COALESCE(SUM(CASE WHEN race IN (1,3,4,7,11) AND online > 0 THEN 1 ELSE 0 END),0) AS a,
            COALESCE(SUM(CASE WHEN race IN (2,5,6,8,10) AND online > 0 THEN 1 ELSE 0 END),0) AS h
            FROM characters';
        $row = $pdo->query($sql)->fetch();
        return ['ok' => true, 'alliance' => (int)$row['a'], 'horde' => (int)$row['h']];
    } catch (Throwable $e) {
        return ['ok' => false, 'alliance' => 0, 'horde' => 0];
    }
}

function realm_uptime_seconds() {
    try {
        $pdo = auth_pdo();
        $st = $pdo->prepare('SELECT uptime FROM uptime WHERE realmid = ? ORDER BY starttime DESC LIMIT 1');
        $st->execute([UPTIME_REALM_ID]);
        $row = $st->fetch();
        return $row ? (int)$row['uptime'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function format_uptime($sec) {
    $sec = max(0, (int)$sec);
    $h = intdiv($sec, 3600);
    $m = intdiv($sec % 3600, 60);
    $s = $sec % 60;
    return $h . 'h ' . $m . 'm ' . $s . 's';
}

function count_characters_for_account($authAccountId) {
    try {
        $st = characters_pdo()->prepare('SELECT COUNT(*) AS c FROM characters WHERE account = ?');
        $st->execute([(int)$authAccountId]);
        $row = $st->fetch();
        return $row ? (int)$row['c'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function stats_total_accounts_auth() {
    try {
        $row = auth_pdo()->query('SELECT COUNT(*) AS c FROM account')->fetch();
        return $row ? (int)$row['c'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function stats_total_characters() {
    try {
        $row = characters_pdo()->query('SELECT COUNT(*) AS c FROM characters')->fetch();
        return $row ? (int)$row['c'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function stats_registered_today_site() {
    try {
        $st = site_pdo()->query("SELECT COUNT(*) AS c FROM users WHERE DATE(created_at) = CURDATE()");
        $row = $st->fetch();
        return $row ? (int)$row['c'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}
