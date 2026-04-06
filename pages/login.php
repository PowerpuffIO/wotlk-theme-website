<?php
if (current_user()) {
    redirect(base_url('profile'));
}
$ret = $_GET['return'] ?? '';
?>
<div class="page-wrap form-page">
  <h1><?= h(__t('login_title')) ?></h1>
  <form method="post" action="<?= h(base_url('login')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="return" value="<?= h(is_string($ret) ? $ret : '') ?>">
    <label><?= h(__t('form_username')) ?></label>
    <input type="text" name="username" required autocomplete="username">
    <label><?= h(__t('form_password')) ?></label>
    <input type="password" name="password" required autocomplete="current-password">
    <?php captcha_render_form_fields(); ?>
    <button type="submit"><?= h(__t('nav_login')) ?></button>
  </form>
  <div class="form-page-links">
    <a href="<?= h(base_url('forgot-password')) ?>"><?= h(__t('auth_forgot_password')) ?></a>
    <a href="<?= h(base_url('register')) ?>"><?= h(__t('auth_register')) ?></a>
  </div>
</div>
