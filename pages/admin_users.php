<?php
$navActive = 'admin';
$adminTab = 'users';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
$perPage = 25;
$page = max(1, (int)($_GET['p'] ?? 1));
$total = 0;
$rows = [];
try {
    $total = (int)site_pdo()->query('SELECT COUNT(*) FROM users')->fetchColumn();
} catch (Throwable $e) {
}
$totalPages = max(1, (int)ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;
$lim = (int)$perPage;
$off = (int)$offset;
try {
    $rows = site_pdo()->query('SELECT id, username, email, created_at, balance FROM users ORDER BY id ASC LIMIT ' . $lim . ' OFFSET ' . $off)->fetchAll();
} catch (Throwable $e) {
    $rows = [];
}
$usersBase = base_url('profile/adminpanel/users');
?>
  <h1><?= h(__t('admin_users')) ?></h1>
  <div class="shop-table-wrap admin-users-wrap">
    <table class="shop-table admin-users-table">
      <thead>
        <tr>
          <th><?= h(__t('admin_users_th_login')) ?></th>
          <th><?= h(__t('admin_users_th_email')) ?></th>
          <th><?= h(__t('admin_users_th_reg')) ?></th>
          <th class="shop-col-price"><?= h(__t('admin_users_th_balance')) ?></th>
          <th class="shop-col-act"><?= h(__t('admin_users_th_action')) ?></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $urow): ?>
        <tr>
          <td><?= h($urow['username']) ?></td>
          <td><?= h($urow['email']) ?></td>
          <td class="admin-users-date"><?= h(date('Y-m-d H:i', strtotime($urow['created_at']))) ?></td>
          <td class="shop-td-price"><?= (int)$urow['balance'] ?></td>
          <td class="shop-td-act">
            <button type="button" class="admin-user-bonus-open shop-buy-btn"
              data-user-id="<?= (int)$urow['id'] ?>"
              data-balance="<?= (int)$urow['balance'] ?>"
              data-return-page="<?= (int)$page ?>"
              data-username="<?= h($urow['username']) ?>"><?= h(__t('admin_users_grant_btn')) ?></button>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php if ($totalPages > 1): ?>
  <nav class="admin-pagination" aria-label="<?= h(__t('admin_users')) ?>">
    <?php if ($page > 1): ?>
    <a class="admin-page-link" href="<?= h($usersBase . '?p=' . ($page - 1)) ?>"><?= h(__t('admin_users_prev')) ?></a>
    <?php endif; ?>
    <span class="admin-page-info"><?= h(__t('admin_users_page')) ?> <?= (int)$page ?> / <?= (int)$totalPages ?></span>
    <?php if ($page < $totalPages): ?>
    <a class="admin-page-link" href="<?= h($usersBase . '?p=' . ($page + 1)) ?>"><?= h(__t('admin_users_next')) ?></a>
    <?php endif; ?>
  </nav>
  <?php endif; ?>

  <dialog class="admin-bonus-dialog" id="admin-bonus-dialog">
    <form method="post" action="<?= h(base_url('profile/adminpanel/users-bonus')) ?>" class="admin-bonus-form">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="user_id" value="">
      <input type="hidden" name="return_page" value="<?= (int)$page ?>">
      <h2 class="admin-bonus-title"><?= h(__t('admin_users_dialog_title')) ?></h2>
      <p class="admin-bonus-user"><span id="admin-bonus-username"></span></p>
      <p class="admin-bonus-line"><?= h(__t('admin_users_current_balance')) ?>: <strong id="admin-bonus-current">0</strong></p>
      <label class="admin-bonus-label" for="admin-bonus-amount"><?= h(__t('admin_users_add_amount')) ?></label>
      <input type="number" id="admin-bonus-amount" name="amount" class="admin-bonus-input" min="1" max="2000000000" value="" required inputmode="numeric">
      <p class="admin-bonus-line admin-bonus-result"><?= h(__t('admin_users_new_balance')) ?>: <strong id="admin-bonus-new">0</strong></p>
      <div class="admin-bonus-actions">
        <button type="submit" class="shop-buy-btn"><?= h(__t('admin_users_bonus_submit')) ?></button>
        <button type="button" class="admin-bonus-cancel" id="admin-bonus-cancel"><?= h(__t('admin_users_bonus_cancel')) ?></button>
      </div>
    </form>
  </dialog>
  <script>
(function(){
  var dlg = document.getElementById('admin-bonus-dialog');
  if (!dlg) return;
  var curEl = document.getElementById('admin-bonus-current');
  var newEl = document.getElementById('admin-bonus-new');
  var amt = document.getElementById('admin-bonus-amount');
  var userSpan = document.getElementById('admin-bonus-username');
  var form = dlg.querySelector('form');
  var curBal = 0;
  function sync() {
    var add = parseInt(amt.value, 10);
    if (isNaN(add) || add < 0) add = 0;
    newEl.textContent = curBal + add;
  }
  amt.addEventListener('input', sync);
  document.getElementById('admin-bonus-cancel').addEventListener('click', function() { dlg.close(); });
  document.querySelectorAll('.admin-user-bonus-open').forEach(function(btn) {
    btn.addEventListener('click', function() {
      curBal = parseInt(btn.getAttribute('data-balance'), 10) || 0;
      form.querySelector('[name=user_id]').value = btn.getAttribute('data-user-id');
      form.querySelector('[name=return_page]').value = btn.getAttribute('data-return-page');
      curEl.textContent = String(curBal);
      userSpan.textContent = btn.getAttribute('data-username') || '';
      amt.value = '';
      newEl.textContent = String(curBal);
      dlg.showModal();
      amt.focus();
    });
  });
})();
  </script>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
