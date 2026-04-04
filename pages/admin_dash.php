<?php
require_once dirname(__DIR__) . '/includes/realm.php';
$navActive = 'admin';
$adminTab = 'dash';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$uc = 0;
$tb = 0;
try {
    $rw = site_pdo()->query('SELECT COUNT(*) AS c FROM users')->fetch();
    $uc = $rw ? (int)$rw['c'] : 0;
} catch (Throwable $e) {
}
try {
    $bs = site_pdo()->query('SELECT COALESCE(SUM(balance),0) AS s FROM users')->fetch();
    $tb = $bs ? (int)$bs['s'] : 0;
} catch (Throwable $e) {
}
$tc = stats_total_characters();
$td = stats_registered_today_site();
?>
  <h1><?= h(__t('admin_dashboard')) ?></h1>
  <div class="admin-stats">
    <div class="admin-stat-box"><span><?= h(__t('registered_users')) ?></span><strong><?= (int)$uc ?></strong></div>
    <div class="admin-stat-box"><span><?= h(__t('total_chars')) ?></span><strong><?= (int)$tc ?></strong></div>
    <div class="admin-stat-box"><span><?= h(__t('registered_today')) ?></span><strong><?= (int)$td ?></strong></div>
    <div class="admin-stat-box"><span><?= h(__t('admin_bonus_total')) ?></span><strong><?= (int)$tb ?></strong></div>
  </div>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
