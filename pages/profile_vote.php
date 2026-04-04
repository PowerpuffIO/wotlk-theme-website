<?php
$api = setting_get('mmorating_api_key');
$voteUrl = setting_get('mmorating_vote_url', 'https://mmorating.top');
$navActive = 'vote';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
?>
  <h1><?= h(__t('vote_title')) ?></h1>
  <div class="vote-page">
    <div class="vote-page-card">
      <h2 class="vote-page-title"><?= h(SERVER_NAME) ?></h2>
      <p class="vote-page-sub"><?= h(REALM_NAME) ?> · <?= h(REALM_RATE) ?></p>
      <p class="vote-page-desc"><?= h(__t('vote_on_mmorating')) ?></p>
      <a class="vote-portal-btn" href="<?= h($voteUrl) ?>" target="_blank" rel="noopener"><?= h(__t('vote_go_portal')) ?></a>
      <form method="post" action="<?= h(base_url('profile/vote/claim')) ?>" class="vote-claim-block">
        <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
        <button type="submit" class="vote-claim-btn"><?= h(__t('vote_check')) ?></button>
      </form>
    </div>
  </div>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
