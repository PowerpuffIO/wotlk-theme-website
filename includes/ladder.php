<?php
if (!defined('ARENA_TYPE_2V2')) {
    define('ARENA_TYPE_2V2', 0);
}
if (!defined('ARENA_TYPE_3V3')) {
    define('ARENA_TYPE_3V3', 1);
}
if (!defined('ARENA_TYPE_5V5')) {
    define('ARENA_TYPE_5V5', 2);
}

function ladder_format_playtime($seconds) {
    $sec = max(0, (int)$seconds);
    $d = intdiv($sec, 86400);
    $h = intdiv($sec % 86400, 3600);
    $m = intdiv($sec % 3600, 60);
    if ($d > 0) {
        return $d . 'd ' . $h . 'h';
    }
    if ($h > 0) {
        return $h . 'h ' . $m . 'm';
    }
    return $m . 'm';
}

function ladder_top_playtime($limit = 10) {
    $lim = max(1, min(50, (int)$limit));
    try {
        $sql = 'SELECT name, totaltime AS pt, level, class, race FROM characters ORDER BY totaltime DESC LIMIT ' . $lim;
        return characters_pdo()->query($sql)->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function ladder_top_honorable_kills($limit = 10) {
    $lim = max(1, min(50, (int)$limit));
    try {
        $sql = 'SELECT name, totalKills AS hk, level, class, race FROM characters ORDER BY totalKills DESC LIMIT ' . $lim;
        return characters_pdo()->query($sql)->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function ladder_arena_by_type($arenaType, $limit = 10) {
    $lim = max(1, min(50, (int)$limit));
    try {
        $st = characters_pdo()->prepare('SELECT name, rating, seasonWins AS sw, seasonGames AS sg FROM arena_team WHERE type = ? ORDER BY rating DESC LIMIT ' . $lim);
        $st->execute([(int)$arenaType]);
        return $st->fetchAll();
    } catch (Throwable $e) {
        return [];
    }
}

function stats_site_users_total() {
    try {
        $r = site_pdo()->query('SELECT COUNT(*) AS c FROM users')->fetch();
        return $r ? (int)$r['c'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}

function realm_online_character_count() {
    try {
        $r = characters_pdo()->query('SELECT COUNT(*) AS c FROM characters WHERE online > 0')->fetch();
        return $r ? (int)$r['c'] : 0;
    } catch (Throwable $e) {
        return 0;
    }
}
