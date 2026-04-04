<?php
$navActive = 'admin';
$adminTab = 'news';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$list = site_pdo()->query('SELECT * FROM news ORDER BY created_at DESC')->fetchAll();
$edit = null;
if (isset($_GET['edit'])) {
    $eid = (int)$_GET['edit'];
    if ($eid > 0) {
        $st = site_pdo()->prepare('SELECT * FROM news WHERE id = ?');
        $st->execute([$eid]);
        $edit = $st->fetch();
    }
}
?>
  <h1><?= h(__t('admin_news')) ?></h1>
  <form method="post" enctype="multipart/form-data" action="<?= h(base_url('profile/adminpanel/news-save')) ?>" class="site-form admin-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <input type="hidden" name="id" value="<?= $edit ? (int)$edit['id'] : 0 ?>">
    <input type="hidden" name="image_existing" value="<?= h($edit['image'] ?? '') ?>">
    <label>title_ru</label>
    <input type="text" name="title_ru" value="<?= h($edit['title_ru'] ?? '') ?>" required>
    <label>title_en</label>
    <input type="text" name="title_en" value="<?= h($edit['title_en'] ?? '') ?>" required>
    <label>excerpt_ru</label>
    <textarea name="excerpt_ru" rows="3" required><?= h($edit['excerpt_ru'] ?? '') ?></textarea>
    <label>excerpt_en</label>
    <textarea name="excerpt_en" rows="3" required><?= h($edit['excerpt_en'] ?? '') ?></textarea>
    <label>body_ru</label>
    <textarea name="body_ru" rows="8" required><?= h($edit['body_ru'] ?? '') ?></textarea>
    <label>body_en</label>
    <textarea name="body_en" rows="8" required><?= h($edit['body_en'] ?? '') ?></textarea>
    <label><?= h(__t('admin_news_image')) ?></label>
    <input type="file" name="news_image" accept="image/jpeg,image/png,image/gif,image/webp">
    <?php if (!empty($edit['image'])): ?>
    <p class="admin-news-preview"><img src="<?= h(news_image_url($edit['image'])) ?>" alt=""></p>
    <?php endif; ?>
    <button type="submit"><?= h(__t('save')) ?></button>
  </form>
  <ul class="admin-list">
    <?php foreach ($list as $row): ?>
    <li>
      <?= h($row['title_ru']) ?>
      <a href="<?= h(base_url('profile/adminpanel/news?edit=' . (int)$row['id'])) ?>">edit</a>
      <form method="post" action="<?= h(base_url('profile/adminpanel/news-del')) ?>" class="inline-form" onsubmit="return confirm('ok');">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
        <button type="submit">del</button>
      </form>
    </li>
    <?php endforeach; ?>
  </ul>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
