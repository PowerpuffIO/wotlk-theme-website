<?php
$navActive = 'settings';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
?>
  <h1><?= h(__t('settings_title')) ?></h1>
  <p><?= h(__t('settings_email_hint')) ?></p>
  <form method="post" action="<?= h(base_url('profile/settings/password-request')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <button type="submit"><?= h(__t('change_password')) ?></button>
  </form>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
