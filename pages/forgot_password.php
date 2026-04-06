<?php
if (current_user()) {
    redirect(base_url('profile'));
}
?>
<div class="page-wrap form-page">
  <h1><?= h(__t('forgot_password_title')) ?></h1>
  <p class="form-page-hint"><?= h(__t('forgot_password_hint')) ?></p>
  <form method="post" action="<?= h(base_url('forgot-password')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label for="forgot-email"><?= h(__t('form_email')) ?></label>
    <input type="email" id="forgot-email" name="email" required autocomplete="email" inputmode="email">
    <?php captcha_render_form_fields(); ?>
    <button type="submit"><?= h(__t('forgot_password_submit')) ?></button>
  </form>
  <div class="form-page-links">
    <a href="<?= h(base_url('login')) ?>"><?= h(__t('auth_back_to_login')) ?></a>
  </div>
</div>
