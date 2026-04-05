<?php
require_once dirname(__DIR__) . '/includes/shop.php';
$cu = current_user();
$isAd = $cu && (int)$cu['role'] === 1;
$na = $navActive ?? 'main';
$shopVisible = shop_is_enabled();
?>
<div class="profile-layout">
<aside class="profile-sidebar">
  <a class="profile-nav<?= $na === 'main' ? ' active' : '' ?>" href="<?= h(base_url('profile')) ?>"><?= h(__t('menu_main')) ?></a>
  <a class="profile-nav<?= $na === 'vote' ? ' active' : '' ?>" href="<?= h(base_url('profile/vote')) ?>"><?= h(__t('menu_vote')) ?></a>
  <?php if ($shopVisible): ?>
  <a class="profile-nav<?= $na === 'shop' ? ' active' : '' ?>" href="<?= h(base_url('profile/shop')) ?>"><?= h(__t('menu_shop')) ?></a>
  <?php endif; ?>
  <a class="profile-nav<?= $na === 'settings' ? ' active' : '' ?>" href="<?= h(base_url('profile/settings')) ?>"><?= h(__t('menu_settings')) ?></a>
  <?php if ($isAd): ?>
  <a class="profile-nav<?= $na === 'admin' ? ' active' : '' ?>" href="<?= h(base_url('profile/adminpanel')) ?>"><?= h(__t('menu_admin')) ?></a>
  <?php endif; ?>
</aside>
<div class="profile-main">
