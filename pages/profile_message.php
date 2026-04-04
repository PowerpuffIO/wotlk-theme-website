<?php
$u = current_user();
$pdo = site_pdo();
$st0 = $pdo->prepare('SELECT * FROM tickets WHERE user_id = ? ORDER BY updated_at DESC');
$st0->execute([(int)$u['id']]);
$tickets = $st0->fetchAll();
$tid = isset($_GET['ticket']) ? (int)$_GET['ticket'] : 0;
$messages = [];
if ($tid > 0) {
    $st = $pdo->prepare('SELECT * FROM tickets WHERE id = ? AND user_id = ?');
    $st->execute([$tid, $u['id']]);
    $ticketRow = $st->fetch();
    if ($ticketRow) {
        $st2 = $pdo->prepare('SELECT * FROM ticket_messages WHERE ticket_id = ? ORDER BY created_at ASC');
        $st2->execute([$tid]);
        $messages = $st2->fetchAll();
    } else {
        $tid = 0;
    }
}
$navActive = 'main';
include dirname(__DIR__) . '/partials/profile_sidebar.php';
?>
  <h1><?= h(__t('messages_title')) ?></h1>
  <h2><?= h(__t('new_ticket')) ?></h2>
  <form method="post" action="<?= h(base_url('profile/message/new')) ?>" class="site-form">
    <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
    <label><?= h(__t('ticket_subject')) ?></label>
    <input type="text" name="subject" required maxlength="255">
    <label><?= h(__t('ticket_body')) ?></label>
    <textarea name="body" rows="5" required></textarea>
    <button type="submit"><?= h(__t('btn_submit')) ?></button>
  </form>
  <ul class="ticket-list">
    <?php foreach ($tickets as $t): ?>
    <li><a href="<?= h(base_url('profile/message?ticket=' . (int)$t['id'])) ?>"><?= h($t['subject']) ?> — <?= h($t['status']) ?></a></li>
    <?php endforeach; ?>
  </ul>
  <?php if ($tid > 0 && !empty($messages)): ?>
  <div class="ticket-thread">
    <?php foreach ($messages as $m): ?>
    <div class="ticket-msg<?= !empty($m['is_staff']) ? ' staff' : '' ?>">
      <div class="ticket-msg-meta"><?= h($m['created_at']) ?></div>
      <div class="ticket-msg-body"><?= nl2br(h($m['body'])) ?></div>
    </div>
    <?php endforeach; ?>
    <?php
    $st = $pdo->prepare('SELECT status FROM tickets WHERE id = ? AND user_id = ?');
    $st->execute([$tid, $u['id']]);
    $tr = $st->fetch();
    if ($tr && $tr['status'] === 'open'):
    ?>
    <form method="post" action="<?= h(base_url('profile/message/reply')) ?>" class="site-form">
      <input type="hidden" name="csrf" value="<?= h(csrf_token()) ?>">
      <input type="hidden" name="ticket_id" value="<?= (int)$tid ?>">
      <textarea name="body" rows="3" required></textarea>
      <button type="submit"><?= h(__t('btn_submit')) ?></button>
    </form>
    <?php endif; ?>
  </div>
  <?php endif; ?>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
