<?php
$navActive = 'admin';
$adminTab = 'download';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$t = setting_get('download_torrent');
$d = setting_get('download_direct');
$r = setting_get('realmlist');
?>
  <h1><?= h(__t('admin_download')) ?></h1>
  <form method="post" action="<?= h(base_url('profile/adminpanel/settings-save')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="tab" value="download">
    <label>torrent</label>
    <input type="text" name="download_torrent" value="<?= h($t) ?>">
    <label>direct</label>
    <input type="text" name="download_direct" value="<?= h($d) ?>">
    <label>realmlist</label>
    <textarea name="realmlist" rows="3"><?= h($r) ?></textarea>
    <button type="submit"><?= h(__t('save')) ?></button>
  </form>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
