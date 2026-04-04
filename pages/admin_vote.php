<?php
$navActive = 'admin';
$adminTab = 'vote';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$key = setting_get('mmorating_api_key');
$bonus = setting_get('vote_bonus', '10');
$url = setting_get('mmorating_vote_url', 'https://mmorating.top');
?>
  <h1><?= h(__t('admin_vote')) ?></h1>
  <form method="post" action="<?= h(base_url('profile/adminpanel/settings-save')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="tab" value="vote">
    <label>API key</label>
    <input type="text" name="mmorating_api_key" value="<?= h($key) ?>">
    <label>bonus</label>
    <input type="number" name="vote_bonus" value="<?= h($bonus) ?>" min="0">
    <label>vote page URL</label>
    <input type="text" name="mmorating_vote_url" value="<?= h($url) ?>">
    <button type="submit"><?= h(__t('save')) ?></button>
  </form>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
