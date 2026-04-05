<?php
$pdo = site_pdo();
$list = $pdo->query('SELECT t.*, u.username FROM tickets t JOIN users u ON u.id = t.user_id ORDER BY t.updated_at DESC')->fetchAll();
$tid = isset($_GET['ticket']) ? (int)$_GET['ticket'] : 0;
$messages = [];
$ticketRow = null;
if ($tid > 0) {
    $st = $pdo->prepare('SELECT t.*, u.username, u.email FROM tickets t JOIN users u ON u.id = t.user_id WHERE t.id = ?');
    $st->execute([$tid]);
    $ticketRow = $st->fetch();
    if ($ticketRow) {
        $st2 = $pdo->prepare('SELECT m.*, u.username FROM ticket_messages m LEFT JOIN users u ON u.id = m.user_id WHERE m.ticket_id = ? ORDER BY m.created_at ASC');
        $st2->execute([$tid]);
        $messages = $st2->fetchAll();
    } else {
        $tid = 0;
    }
}
$navActive = 'admin';
$adminTab = 'messages';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
include dirname(__DIR__) . '/partials/admin_nav.php';
?>
  <h1><?= h(__t('admin_messages')) ?></h1>
  <ul class="admin-ticket-list">
    <?php foreach ($list as $t): ?>
    <li><a href="<?= h(base_url('profile/adminpanel/messages?ticket=' . (int)$t['id'])) ?>"><?= h($t['username']) ?> — <?= h($t['subject']) ?> (<?= h($t['status']) ?>)</a></li>
    <?php endforeach; ?>
  </ul>
  <?php if ($tid > 0 && $ticketRow): ?>
  <div class="admin-ticket-detail">
    <p><?= h($ticketRow['username']) ?> &lt;<?= h($ticketRow['email']) ?>&gt;</p>
    <?php foreach ($messages as $m): ?>
    <div class="ticket-msg<?= !empty($m['is_staff']) ? ' staff' : '' ?>">
      <div class="ticket-msg-meta"><?= h($m['created_at']) ?> <?= h($m['username'] ?? '') ?></div>
      <div class="ticket-msg-body"><?= nl2br(h($m['body'])) ?></div>
    </div>
    <?php endforeach; ?>
    <?php if ($ticketRow['status'] === 'open'): ?>
    <form method="post" action="<?= h(base_url('profile/adminpanel/ticket-reply')) ?>" class="site-form">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="ticket_id" value="<?= (int)$tid ?>">
      <textarea name="body" rows="4" required></textarea>
      <button type="submit"><?= h(__t('btn_submit')) ?></button>
    </form>
    <form method="post" action="<?= h(base_url('profile/adminpanel/ticket-close')) ?>" class="inline-form">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="ticket_id" value="<?= (int)$tid ?>">
      <button type="submit" class="admin-inline-secondary"><?= h(__t('admin_close')) ?></button>
    </form>
    <?php endif; ?>
    <form method="post" action="<?= h(base_url('profile/adminpanel/ticket-del')) ?>" class="inline-form" onsubmit="return confirm('ok');">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="ticket_id" value="<?= (int)$tid ?>">
      <button type="submit" class="admin-row-btn admin-row-btn-del"><?= h(__t('admin_delete')) ?></button>
    </form>
  </div>
  <?php endif; ?>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
