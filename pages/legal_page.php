<?php
$lang = active_lang();
$slug = $legalSlug ?? 'rules';
$body = setting_get('legal_' . $slug . '_' . $lang, '');
$titleKey = 'page_' . $slug . '_title';
$pageTitle = __t($titleKey);
?>
<div class="page-wrap legal-page">
  <h1><?= h($pageTitle) ?></h1>
  <div class="legal-body"><?= $body !== '' ? nl2br(h($body)) : '<p class="legal-empty">' . h(__t('legal_empty_hint')) . '</p>' ?></div>
</div>
