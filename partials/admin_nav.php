<?php
$at = $adminTab ?? 'dash';
?>
<div class="admin-nav">
  <a href="<?= h(base_url('profile/adminpanel')) ?>" class="<?= $at === 'dash' ? 'active' : '' ?>"><?= h(__t('admin_dashboard')) ?></a>
  <a href="<?= h(base_url('profile/adminpanel/news')) ?>" class="<?= $at === 'news' ? 'active' : '' ?>"><?= h(__t('admin_news')) ?></a>
  <a href="<?= h(base_url('profile/adminpanel/vote')) ?>" class="<?= $at === 'vote' ? 'active' : '' ?>"><?= h(__t('admin_vote')) ?></a>
  <a href="<?= h(base_url('profile/adminpanel/download')) ?>" class="<?= $at === 'download' ? 'active' : '' ?>"><?= h(__t('admin_download')) ?></a>
  <a href="<?= h(base_url('profile/adminpanel/messages')) ?>" class="<?= $at === 'messages' ? 'active' : '' ?>"><?= h(__t('admin_messages')) ?></a>
  <a href="<?= h(base_url('profile/adminpanel/social')) ?>" class="<?= $at === 'social' ? 'active' : '' ?>"><?= h(__t('admin_social')) ?></a>
</div>
