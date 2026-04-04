<?php
$lang = active_lang();
$st = site_pdo()->prepare('SELECT * FROM news WHERE id = ?');
$st->execute([$newsId]);
$n = $st->fetch();
if (!$n) {
    http_response_code(404);
    exit;
}
$title = $lang === 'ru' ? $n['title_ru'] : $n['title_en'];
$body = $lang === 'ru' ? $n['body_ru'] : $n['body_en'];
$img = news_image_url($n['image'] ?? '');
$pageTitle = $title;
?>
<div class="page-wrap news-single-page">
  <article class="news-single">
    <img src="<?= h($img) ?>" alt="">
    <h1><?= h($title) ?></h1>
    <div class="news-body"><?= nl2br(h($body)) ?></div>
    <a class="news-back-btn" href="<?= h(base_url()) ?>"><i class="ri-arrow-left-s-line"></i> <?= h(__t('news_back_btn')) ?></a>
    <?php if (current_user()): ?>
    <div class="news-single-votes">
      <form method="post" action="<?= h(base_url('news-vote')) ?>">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="news_id" value="<?= (int)$n['id'] ?>">
        <input type="hidden" name="value" value="1">
        <button type="submit" class="btn-vote"><i class="ri-thumb-up-line"></i></button>
      </form>
      <form method="post" action="<?= h(base_url('news-vote')) ?>">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        <input type="hidden" name="news_id" value="<?= (int)$n['id'] ?>">
        <input type="hidden" name="value" value="-1">
        <button type="submit" class="btn-vote"><i class="ri-thumb-down-line"></i></button>
      </form>
    </div>
    <?php endif; ?>
  </article>
</div>
