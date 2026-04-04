<?php
require_once dirname(__DIR__) . '/includes/realm.php';
require_once dirname(__DIR__) . '/includes/ladder.php';
$siteUsersCount = stats_site_users_total();
$charsOnline = realm_online_character_count();
$rf = realm_faction_online();
$realmFactionOk = !empty($rf['ok']);
$alliance = (int)($rf['alliance'] ?? 0);
$horde = (int)($rf['horde'] ?? 0);
$total = max(1, $alliance + $horde);
$pa = round($alliance * 100 / $total);
$ph = round($horde * 100 / $total);
$up = format_uptime(realm_uptime_seconds());
$lang = active_lang();
$newsList = [];
try {
    $newsList = site_pdo()->query('SELECT * FROM news ORDER BY created_at DESC LIMIT 12')->fetchAll();
} catch (Throwable $e) {
}
?>
<section class="hero">
  <div class="hero-center">
    <div class="logo_text">
      <?= h(SERVER_NAME) ?>
      <div class="text-container">
        <span>P</span>
        <span>A</span>
        <span>T</span>
        <span>C</span>
        <span>H</span>
        <span>&nbsp;</span>
        <span>3.</span>
        <span>3.</span>
        <span>5</span>
      </div>
    </div>
  </div>
<div class="realm-status">
  <h3><?= h(__t('realm_status')) ?></h3><br>
      <p style="color:WHITE;">
      <?= h(REALM_NAME) ?> <a style="color:#ff5ffd;"><?= h(REALM_RATE) ?></a>
      <a style="color:#807931;"> <?= h($up) ?></a>
    </p>
  <div class="realm">
    <?php if (!$realmFactionOk): ?>
    <p class="realm-faction-placeholder"><?= h(__t('realm_no_connection')) ?></p>
    <?php else: ?>
    <div class="progress-bar">
      <div class="progress-track"> 
        <div class="progress-fill horde" style="width:<?= (int)$ph ?>%;"> Horde <?= (int)$ph ?>%</div>
        <div class="progress-fill alliance" style="width:<?= (int)$pa ?>%;"> Alliance <?= (int)$pa ?>%</div>
      </div>
    </div>
    <?php endif; ?>
  </div>
</div>
</section>

<main>
  <div class="content">
    <?php foreach ($newsList as $n): 
      $title = $lang === 'ru' ? $n['title_ru'] : $n['title_en'];
      $ex = $lang === 'ru' ? $n['excerpt_ru'] : $n['excerpt_en'];
      $img = news_image_url($n['image'] ?? '');
    ?>
    <div class="news">
      <img src="<?= h($img) ?>" alt="">
      <div class="news-text">
        <div class="news-text-title"><?= h($title) ?></div>
        <div class="news-text-content">
          <div class="news-excerpt"><?= nl2br(h($ex)) ?></div>
          <div class="news-actions-row">
            <a class="btn-readmore" href="<?= h(base_url('news/' . (int)$n['id'])) ?>"><?= h(__t('read_more')) ?></a>
            <?php if (current_user()): ?>
            <div class="news-vote-btns">
              <form method="post" action="<?= h(base_url('news-vote')) ?>" class="news-vote-form">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="news_id" value="<?= (int)$n['id'] ?>">
                <input type="hidden" name="value" value="1">
                <button type="submit" class="btn-vote" title="<?= h(__t('news_like')) ?>"><i class="ri-thumb-up-line"></i></button>
              </form>
              <form method="post" action="<?= h(base_url('news-vote')) ?>" class="news-vote-form">
                <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
                <input type="hidden" name="news_id" value="<?= (int)$n['id'] ?>">
                <input type="hidden" name="value" value="-1">
                <button type="submit" class="btn-vote" title="<?= h(__t('news_dislike')) ?>"><i class="ri-thumb-down-line"></i></button>
              </form>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($newsList)): ?>
    <div class="news"><div class="news-text"><div class="news-text-content"><p></p></div></div></div>
    <?php endif; ?>
  </div>

  <aside class="sidebar">
    <div class="sidebar-box">
      <div class="sidebar-title"><?= h(__t('sidebar_social')) ?></div>
      <div class="sidebar-content sidebar-social">
        <p class="social-stat"><?= h(__t('social_registered')) ?>: <strong><?= (int)$siteUsersCount ?></strong></p>
        <p class="social-stat"><?= h(__t('social_online')) ?>: <strong><?= (int)$charsOnline ?></strong></p>
        <?php include dirname(__DIR__) . '/partials/discord_embed_sidebar.php'; ?>
      </div>
    </div>
  </aside>
</main>
