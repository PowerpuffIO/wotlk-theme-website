<?php
if (current_user()) {
    redirect(base_url('profile'));
}
?>
<div class="page-wrap form-page">
  <h1><?= h(__t('register_title')) ?></h1>
  <form method="post" action="<?= h(base_url('register')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label><?= h(__t('form_username')) ?></label>
    <input type="text" name="username" required maxlength="32" autocomplete="username">
    <label><?= h(__t('form_email')) ?></label>
    <input type="email" name="email" required autocomplete="email">
    <label><?= h(__t('form_password')) ?></label>
    <input type="password" name="password" required autocomplete="new-password">
    <label><?= h(__t('form_confirm')) ?></label>
    <input type="password" name="password2" required autocomplete="new-password">
    <?php captcha_render_form_fields(); ?>
    <button type="submit"><?= h(__t('register_btn')) ?></button>
  </form>
</div>
