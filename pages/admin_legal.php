<?php
$navActive = 'admin';
$adminTab = 'legal';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$slugs = ['rules', 'terms', 'privacy'];
?>
  <h1><?= h(__t('admin_legal')) ?></h1>
  <p class="admin-hint"><?= h(__t('admin_legal_hint')) ?></p>
  <form method="post" action="<?= h(base_url('profile/adminpanel/settings-save')) ?>" class="site-form admin-form admin-legal-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="tab" value="legal">
    <?php foreach ($slugs as $slug): ?>
    <h2 class="admin-legal-section"><?= h(__t('admin_legal_' . $slug)) ?></h2>
    <label for="legal_<?= h($slug) ?>_ru"><?= h(__t('admin_legal_' . $slug . '_ru')) ?></label>
    <textarea id="legal_<?= h($slug) ?>_ru" name="legal_<?= h($slug) ?>_ru" rows="10"><?= h(setting_get('legal_' . $slug . '_ru', '')) ?></textarea>
    <label for="legal_<?= h($slug) ?>_en"><?= h(__t('admin_legal_' . $slug . '_en')) ?></label>
    <textarea id="legal_<?= h($slug) ?>_en" name="legal_<?= h($slug) ?>_en" rows="10"><?= h(setting_get('legal_' . $slug . '_en', '')) ?></textarea>
    <?php endforeach; ?>
    <button type="submit"><?= h(__t('save')) ?></button>
  </form>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
