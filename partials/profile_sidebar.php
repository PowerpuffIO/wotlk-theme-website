<?php
$cu = current_user();
$isAd = $cu && (int)$cu['role'] === 1;
$na = $navActive ?? 'main';
?>
<div class="profile-layout">
<aside class="profile-sidebar">
  <a class="profile-nav<?= $na === 'main' ? ' active' : '' ?>" href="<?= h(base_url('profile')) ?>"><?= h(__t('menu_main')) ?></a>
  <a class="profile-nav<?= $na === 'vote' ? ' active' : '' ?>" href="<?= h(base_url('profile/vote')) ?>"><?= h(__t('menu_vote')) ?></a>
  <a class="profile-nav<?= $na === 'settings' ? ' active' : '' ?>" href="<?= h(base_url('profile/settings')) ?>"><?= h(__t('menu_settings')) ?></a>
  <?php if ($isAd): ?>
  <a class="profile-nav<?= $na === 'admin' ? ' active' : '' ?>" href="<?= h(base_url('profile/adminpanel')) ?>"><?= h(__t('menu_admin')) ?></a>
  <?php endif; ?>
</aside>
<div class="profile-main">
