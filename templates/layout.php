<?php
/*
Credits (English, in templates/layout.php):
Block site-credits with lang="en":
Design by Horuxia · Discord: Horuxia
Developed by Powerpuff · Discord: powerpuff_io
*/
$cu = current_user();
$isRu = active_lang() === 'ru';
$langOther = $isRu ? 'en' : 'ru';
$flashOk = $_SESSION['flash_ok'] ?? null;
$flashErr = $_SESSION['flash_err'] ?? null;
unset($_SESSION['flash_ok'], $_SESSION['flash_err']);
?><!DOCTYPE html>
<html lang="<?= active_lang() ?>">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h($pageTitle ?? '') ?></title>
  <link rel="stylesheet" href="<?= h(base_url('Assets/css/remixicon.css')) ?>">
  <link rel="stylesheet" href="<?= h(base_url('Assets/css/style.css')) ?>">
</head>
<body class="site-body <?= h($bodyClass ?? '') ?>">
<nav role="navigation">
  <input type="checkbox" id="menu-toggle">
  <label for="menu-toggle" class="menu-icon">☰ Navigation</label>
  <ul>
    <li><a href="<?= h(base_url()) ?>"><?= h(__t('nav_home')) ?></a></li>
    <li><a href="<?= h(base_url('download')) ?>"><?= h(__t('nav_download')) ?></a></li>
    <li><a href="<?= h(base_url('ladder')) ?>"><?= h(__t('nav_ladder')) ?></a></li>
    <li class="right nav-lang"><a href="?lang=<?= h($langOther) ?>"><?= $isRu ? 'EN' : 'RU' ?></a></li>
    <li class="right"><a href="#"><i class="ri-user-3-line"></i> <?= h(__t('nav_account')) ?></a>
      <ul>
        <li><a href="<?= h(base_url('profile')) ?>"><i class="ri-user-3-line"></i> <?= h(__t('nav_profile')) ?></a></li>
        <?php if ($cu): ?>
        <li><a href="<?= h(base_url('profile/message')) ?>"><i class="ri-mail-line"></i> <?= h(__t('nav_messages')) ?></a></li>
        <?php endif; ?>
        <li><a href="<?= h(base_url('profile/settings')) ?>"><i class="ri-settings-2-line"></i> <?= h(__t('nav_settings')) ?></a></li>
        <?php if ($cu): ?>
        <li><a onclick="closeMobileMenu()" href="<?= h(base_url('logout')) ?>"><i class="ri-logout-box-r-line"></i> <?= h(__t('nav_logout')) ?></a></li>
        <?php else: ?>
        <li><a onclick="closeMobileMenu()" href="<?= h(base_url('login')) ?>"><i class="ri-login-box-line"></i> <?= h(__t('nav_login')) ?></a></li>
        <li><a onclick="closeMobileMenu()" href="<?= h(base_url('register')) ?>"><i class="ri-user-add-line"></i> <?= h(__t('nav_register')) ?></a></li>
        <?php endif; ?>
        <li><a onclick="closeMobileMenu();location.reload();return false;" href="#" id="resetpwBtn"><i class="ri-reset-left-line"></i> <?= h(__t('nav_reset')) ?></a></li>
      </ul>
    </li>
  </ul>
</nav>
<script>
function closeMobileMenu() {
  var menuToggle = document.getElementById("menu-toggle");
  if(menuToggle){ menuToggle.checked = false; }
}
</script>
<div class="site-content-wrap">
<?php if ($flashOk !== null): ?><div class="site-flash site-flash-ok"><?= is_string($flashOk) ? h($flashOk) : h(__t('success')) ?></div><?php endif; ?>
<?php if ($flashErr !== null): ?><div class="site-flash site-flash-err"><?php
$fe = $flashErr;
if ($fe === 'captcha') {
    echo h(__t('error_captcha'));
} elseif (is_string($fe)) {
    echo h($fe);
} else {
    echo h(__t('error_generic'));
}
?></div><?php endif; ?>
<?= $content ?? '' ?>
</div>
<footer class="site-footer">
<p><?= h(SERVER_NAME) ?> &middot; <?= h(__t('footer_year')) ?></p>
</footer>
</body>
</html>
