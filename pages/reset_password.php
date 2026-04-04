<?php
$token = $_GET['token'] ?? '';
if ($token === '') {
    redirect(base_url('login'));
}
?>
<div class="page-wrap form-page">
  <h1><?= h(__t('change_password')) ?></h1>
  <form method="post" action="<?= h(base_url('reset-password/confirm')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="token" value="<?= h($token) ?>">
    <label><?= h(__t('form_password')) ?></label>
    <input type="password" name="password" required autocomplete="new-password">
    <label><?= h(__t('form_confirm')) ?></label>
    <input type="password" name="password2" required autocomplete="new-password">
    <button type="submit"><?= h(__t('btn_submit')) ?></button>
  </form>
</div>
