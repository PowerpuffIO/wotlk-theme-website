<?php
$navActive = 'admin';
$adminTab = 'social';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$guildId = setting_get('discord_guild_id');
$widgetTheme = setting_get('discord_widget_theme', 'dark');
if (!in_array($widgetTheme, ['dark', 'light'], true)) {
    $widgetTheme = 'dark';
}
?>
  <h1><?= h(__t('admin_social')) ?></h1>
  <p class="admin-hint"><?= h(__t('admin_social_discord_hint')) ?></p>
  <form method="post" action="<?= h(base_url('profile/adminpanel/settings-save')) ?>" class="site-form admin-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="tab" value="social">
    <label for="discord_guild_id"><?= h(__t('admin_social_guild_id')) ?></label>
    <input type="text" id="discord_guild_id" name="discord_guild_id" value="<?= h($guildId) ?>" inputmode="numeric" autocomplete="off" placeholder="123456789012345678">
    <label for="discord_widget_theme"><?= h(__t('admin_social_widget_theme')) ?></label>
    <select id="discord_widget_theme" name="discord_widget_theme">
      <option value="dark" <?= $widgetTheme === 'dark' ? ' selected' : '' ?>><?= h(__t('admin_social_theme_dark')) ?></option>
      <option value="light" <?= $widgetTheme === 'light' ? ' selected' : '' ?>><?= h(__t('admin_social_theme_light')) ?></option>
    </select>
    <button type="submit"><?= h(__t('save')) ?></button>
  </form>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
