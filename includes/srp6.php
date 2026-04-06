<?php
function acore_N() {
    return gmp_init('0x894B645E89E1535BBDAD5B8B290650530801B18EBFBF5E8FAB3C82872A3E9BB7', 16);
}

function acore_username_upper($username) {
    if (function_exists('mb_strtoupper')) {
        return mb_strtoupper($username, 'UTF-8');
    }
    return strtoupper($username);
}

function acore_password_upper($password) {
    if (function_exists('mb_strtoupper')) {
        return mb_strtoupper((string)$password, 'UTF-8');
    }
    return strtoupper((string)$password);
}

function acore_calculate_verifier($username, $password, $salt) {
    $u = acore_username_upper($username);
    $p = acore_password_upper($password);
    $h1 = sha1(acore_username_upper($u . ':' . $p), true);
    $h2 = sha1($salt . $h1, true);
    $N = acore_N();
    $g = gmp_init(7);
    $x = gmp_import($h2, 1, GMP_LSW_FIRST);
    $v = gmp_powm($g, $x, $N);
    $b = gmp_export($v, 1, GMP_LSW_FIRST);
    return str_pad(substr($b, 0, 32), 32, "\0", STR_PAD_RIGHT);
}

function acore_make_registration_data($username, $password) {
    $salt = random_bytes(32);
    $verifier = acore_calculate_verifier($username, $password, $salt);
    return [$salt, $verifier];
}

function acore_verifier_match($username, $password, $salt, $storedVerifier) {
    $calc = acore_calculate_verifier($username, $password, $salt);
    return hash_equals($storedVerifier, $calc);
}
