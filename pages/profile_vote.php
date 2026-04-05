<?php
$u = current_user();
$voteUrl = setting_get('mmorating_vote_url', 'https://mmorating.top');
$bonus = max(0, (int)setting_get('vote_bonus', '10'));
$navActive = 'vote';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
?>
  <h1><?= h(__t('vote_title')) ?></h1>
  <p class="profile-balance vote-page-balance"><strong><?= h(__t('vote_balance')) ?>:</strong> <span class="profile-balance-value"><?= (int)($u['balance'] ?? 0) ?></span></p>
  <div class="vote-ratings">
    <table class="vote-ratings-table">
      <thead>
        <tr>
          <th scope="col" class="vote-col-name"><?= h(__t('vote_th_rating')) ?></th>
          <th scope="col" class="vote-col-reward"><?= h(__t('vote_th_reward')) ?></th>
          <th scope="col" class="vote-col-btn"><?= h(__t('vote_th_vote')) ?></th>
          <th scope="col" class="vote-col-btn"><?= h(__t('vote_th_claim')) ?></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td class="vote-col-name">
            <strong class="vote-rating-title"><?= h(__t('vote_rating_mmorating')) ?></strong>
            <span class="vote-rating-sub"><?= h(SERVER_NAME) ?></span>
          </td>
          <td class="vote-col-reward">
            <span class="vote-rating-reward">+<?= (int)$bonus ?></span>
          </td>
          <td class="vote-col-btn">
            <a class="site-btn vote-action-btn" href="<?= h($voteUrl) ?>" target="_blank" rel="noopener"><?= h(__t('vote_btn_vote')) ?></a>
          </td>
          <td class="vote-col-btn">
            <form method="post" action="<?= h(base_url('profile/vote/claim')) ?>" class="vote-claim-form">
              <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
              <button type="submit" class="site-btn vote-action-btn"><?= h(__t('vote_check')) ?></button>
            </form>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <p class="vote-hint"><?= h(__t('vote_on_mmorating')) ?></p>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
