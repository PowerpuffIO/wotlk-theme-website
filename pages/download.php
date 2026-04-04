<?php
$cu = current_user();
$t = setting_get('download_torrent');
$d = setting_get('download_direct');
$r = setting_get('realmlist');
?>
<div class="page-wrap download-page">
  <h1><?= h(__t('download_title')) ?></h1>
  <div class="download-grid">
    <div class="download-block">
      <?php if ($cu): ?>
      <p><?= h(__t('download_registered')) ?></p>
      <a class="site-btn" href="<?= h(base_url('profile')) ?>"><?= h(__t('nav_profile')) ?></a>
      <?php else: ?>
      <p><?= h(__t('download_block1')) ?></p>
      <a class="site-btn" href="<?= h(base_url('register')) ?>"><?= h(__t('nav_register')) ?></a>
      <?php endif; ?>
    </div>
    <div class="download-block">
      <p><?= h(__t('download_block2')) ?></p>
      <div class="download-btn-row">
      <?php if ($t !== ''): ?><a class="site-btn" href="<?= h($t) ?>" target="_blank" rel="noopener"><?= h(__t('download_torrent')) ?></a><?php endif; ?>
      <?php if ($d !== ''): ?><a class="site-btn" href="<?= h($d) ?>" target="_blank" rel="noopener"><?= h(__t('download_direct')) ?></a><?php endif; ?>
      </div>
    </div>
    <div class="download-block">
      <label><?= h(__t('download_realmlist')) ?></label>
      <div class="realmlist-row">
        <input type="text" readonly id="realmlist-field" value="<?= h($r) ?>">
        <button type="button" class="site-btn" id="copy-realmlist"><?= h(__t('copy')) ?></button>
      </div>
    </div>
  </div>
</div>
<script>
document.getElementById('copy-realmlist').addEventListener('click',function(){
  var i=document.getElementById('realmlist-field');
  i.select();
  document.execCommand('copy');
});
</script>
