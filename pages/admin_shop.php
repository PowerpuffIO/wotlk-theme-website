<?php
$navActive = 'admin';
$adminTab = 'shop';
require_once dirname(__DIR__) . '/includes/shop.php';
$lang = active_lang();
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$subs = shop_subcategories_flat();
$list = [];
try {
    $list = site_pdo()->query('SELECT si.id, si.item_entry, si.price, si.quantity, si.enabled, c.name_ru AS sub_ru, c.name_en AS sub_en, p.name_ru AS cat_ru, p.name_en AS cat_en FROM shop_items si INNER JOIN shop_categories c ON c.id = si.subcategory_id INNER JOIN shop_categories p ON p.id = c.parent_id ORDER BY si.id DESC')->fetchAll();
} catch (Throwable $e) {
    $list = [];
}
?>
  <h1><?= h(__t('admin_shop')) ?></h1>
  <form method="post" action="<?= h(base_url('profile/adminpanel/shop-save')) ?>" class="site-form admin-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label><input type="checkbox" name="shop_enabled" value="1" <?= setting_get('shop_enabled', '0') === '1' ? ' checked' : '' ?>> <?= h(__t('shop_admin_enable')) ?></label>
    <p class="admin-hint"><?= h(__t('shop_admin_soap_hint')) ?></p>
    <button type="submit"><?= h(__t('save')) ?></button>
  </form>
  <h2 class="shop-admin-h2"><?= h(__t('shop_admin_add')) ?></h2>
  <form method="post" action="<?= h(base_url('profile/adminpanel/shop-item-add')) ?>" class="site-form admin-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label><?= h(__t('shop_admin_subcat')) ?></label>
    <select name="subcategory_id" required>
      <option value=""><?= h(__t('shop_admin_subcat')) ?></option>
      <?php foreach ($subs as $s): ?>
      <?php
        $pl = shop_category_display_name(['name_ru' => $s['parent_name_ru'], 'name_en' => $s['parent_name_en']], $lang);
        $sl = shop_category_display_name(['name_ru' => $s['name_ru'], 'name_en' => $s['name_en']], $lang);
      ?>
      <option value="<?= (int)$s['id'] ?>"><?= h($pl) ?> → <?= h($sl) ?></option>
      <?php endforeach; ?>
    </select>
    <label><?= h(__t('shop_admin_entry')) ?></label>
    <input type="number" name="item_entry" min="1" required>
    <label><?= h(__t('shop_admin_price')) ?></label>
    <input type="number" name="price" min="0" value="0" required>
    <label><?= h(__t('shop_admin_qty')) ?></label>
    <input type="number" name="quantity" min="1" value="1" required>
    <button type="submit"><?= h(__t('shop_admin_add_btn')) ?></button>
  </form>
  <h2 class="shop-admin-h2"><?= h(__t('shop_admin_list')) ?></h2>
  <ul class="admin-list shop-admin-list">
    <?php foreach ($list as $row): ?>
    <li>
      <span class="admin-list-news-title">#<?= (int)$row['item_entry'] ?> — <?= (int)$row['price'] ?> — <?= h($lang === 'ru' ? $row['cat_ru'] : $row['cat_en']) ?> / <?= h($lang === 'ru' ? $row['sub_ru'] : $row['sub_en']) ?></span>
      <div class="admin-list-actions">
        <form method="post" action="<?= h(base_url('profile/adminpanel/shop-item-del')) ?>" class="inline-form" onsubmit="return confirm('ok');">
          <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
          <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
          <button type="submit" class="admin-row-btn admin-row-btn-del"><?= h(__t('admin_delete')) ?></button>
        </form>
      </div>
    </li>
    <?php endforeach; ?>
    <?php if (empty($list)): ?>
    <li><span class="admin-list-news-title"><?= h(__t('shop_no_items')) ?></span></li>
    <?php endif; ?>
  </ul>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
