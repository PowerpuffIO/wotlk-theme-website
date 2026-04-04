<?php
require_once dirname(__DIR__) . '/includes/realm.php';
$u = current_user();
$chars = count_characters_for_account($u['auth_account_id']);
$dt = new DateTime($u['created_at']);
$df = $dt->format('d.m.Y H:i');
$navActive = 'main';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
?>
  <h1><?= h(__t('profile_title')) ?></h1>
  <div class="profile-info">
    <p><strong><?= h(__t('profile_chars')) ?>:</strong> <?= (int)$chars ?></p>
    <p><strong><?= h(__t('profile_regdate')) ?>:</strong> <?= h($df) ?></p>
  </div>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
