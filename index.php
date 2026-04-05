<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/actions.php';

$rawPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$base = rtrim(BASE_PATH, '/');
if ($base !== '' && str_starts_with($rawPath, $base)) {
    $rawPath = substr($rawPath, strlen($base)) ?: '/';
}
$route = trim($rawPath, '/');
if ($route === '') {
    $route = 'home';
}

if (handle_post($route)) {
    exit;
}

if ($route === 'logout' || $route === 'logout.php') {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    redirect(base_url());
}

$parts = explode('/', $route);
$pageTitle = SERVER_NAME;
$bodyClass = '';

if ($parts[0] === 'news' && isset($parts[1]) && ctype_digit($parts[1])) {
    ob_start();
    $newsId = (int)$parts[1];
    include __DIR__ . '/pages/news_single.php';
    $content = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

if ($parts[0] === 'home' || $route === 'home') {
    ob_start();
    include __DIR__ . '/pages/home.php';
    $content = ob_get_clean();
    include __DIR__ . '/templates/layout.php';
    exit;
}

switch ($parts[0]) {
    case 'login':
        $pageTitle = __t('login_title');
        ob_start();
        include __DIR__ . '/pages/login.php';
        $content = ob_get_clean();
        break;
    case 'register':
        $pageTitle = __t('register_title');
        ob_start();
        include __DIR__ . '/pages/register.php';
        $content = ob_get_clean();
        break;
    case 'download':
        $pageTitle = __t('download_title');
        ob_start();
        include __DIR__ . '/pages/download.php';
        $content = ob_get_clean();
        break;
    case 'ladder':
        $pageTitle = __t('ladder_title');
        ob_start();
        include __DIR__ . '/pages/ladder.php';
        $content = ob_get_clean();
        break;
    case 'reset-password':
        $pageTitle = __t('change_password');
        ob_start();
        include __DIR__ . '/pages/reset_password.php';
        $content = ob_get_clean();
        break;
    case 'profile':
        require_login();
        $sub = $parts[1] ?? '';
        if ($sub === '') {
            $pageTitle = __t('profile_title');
            ob_start();
            include __DIR__ . '/pages/profile.php';
            $content = ob_get_clean();
        } elseif ($sub === 'settings') {
            $pageTitle = __t('settings_title');
            ob_start();
            include __DIR__ . '/pages/profile_settings.php';
            $content = ob_get_clean();
        } elseif ($sub === 'vote') {
            $pageTitle = __t('vote_title');
            ob_start();
            include __DIR__ . '/pages/profile_vote.php';
            $content = ob_get_clean();
        } elseif ($sub === 'shop') {
            require_once __DIR__ . '/includes/shop.php';
            if (!shop_is_enabled()) {
                http_response_code(404);
                exit;
            }
            $pageTitle = __t('menu_shop');
            ob_start();
            include __DIR__ . '/pages/profile_shop.php';
            $content = ob_get_clean();
        } elseif ($sub === 'message') {
            $pageTitle = __t('messages_title');
            ob_start();
            include __DIR__ . '/pages/profile_message.php';
            $content = ob_get_clean();
        } elseif ($sub === 'adminpanel') {
            require_admin();
            $tab = $parts[2] ?? 'dash';
            $pageTitle = __t('menu_admin');
            ob_start();
            if ($tab === 'dash' || $tab === '') {
                include __DIR__ . '/pages/admin_dash.php';
            } elseif ($tab === 'news') {
                include __DIR__ . '/pages/admin_news.php';
            } elseif ($tab === 'vote') {
                include __DIR__ . '/pages/admin_vote.php';
            } elseif ($tab === 'download') {
                include __DIR__ . '/pages/admin_download.php';
            } elseif ($tab === 'messages') {
                include __DIR__ . '/pages/admin_messages.php';
            } elseif ($tab === 'social') {
                include __DIR__ . '/pages/admin_social.php';
            } elseif ($tab === 'shop') {
                include __DIR__ . '/pages/admin_shop.php';
            } elseif ($tab === 'users') {
                include __DIR__ . '/pages/admin_users.php';
            } else {
                include __DIR__ . '/pages/admin_dash.php';
            }
            $content = ob_get_clean();
        } else {
            http_response_code(404);
            exit;
        }
        break;
    default:
        http_response_code(404);
        exit;
}

include __DIR__ . '/templates/layout.php';
