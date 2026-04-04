<?php
require_once dirname(__DIR__) . '/includes/ladder.php';
$topTime = ladder_top_playtime(10);
$topHk = ladder_top_honorable_kills(10);
$a2 = ladder_arena_by_type(ARENA_TYPE_2V2, 10);
$a3 = ladder_arena_by_type(ARENA_TYPE_3V3, 10);
$a5 = ladder_arena_by_type(ARENA_TYPE_5V5, 10);
?>
<div class="page-wrap ladder-page">
  <h1><?= h(__t('ladder_title')) ?></h1>
  <div class="ladder-grid">
    <section class="ladder-section">
      <h2 class="ladder-heading"><?= h(__t('ladder_top_playtime')) ?></h2>
      <table class="ladder-table">
        <thead>
          <tr>
            <th>#</th>
            <th><?= h(__t('ladder_name')) ?></th>
            <th><?= h(__t('ladder_level')) ?></th>
            <th><?= h(__t('ladder_playtime')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($topTime as $i => $row): ?>
          <tr>
            <td><?= (int)($i + 1) ?></td>
            <td><?= h($row['name'] ?? '') ?></td>
            <td><?= (int)($row['level'] ?? 0) ?></td>
            <td><?= h(ladder_format_playtime((int)($row['pt'] ?? 0))) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($topTime)): ?>
          <tr><td colspan="4" class="ladder-empty"><?= h(__t('ladder_no_data')) ?></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
    <section class="ladder-section">
      <h2 class="ladder-heading"><?= h(__t('ladder_top_hk')) ?></h2>
      <table class="ladder-table">
        <thead>
          <tr>
            <th>#</th>
            <th><?= h(__t('ladder_name')) ?></th>
            <th><?= h(__t('ladder_level')) ?></th>
            <th><?= h(__t('ladder_hk')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($topHk as $i => $row): ?>
          <tr>
            <td><?= (int)($i + 1) ?></td>
            <td><?= h($row['name'] ?? '') ?></td>
            <td><?= (int)($row['level'] ?? 0) ?></td>
            <td><?= (int)($row['hk'] ?? 0) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($topHk)): ?>
          <tr><td colspan="4" class="ladder-empty"><?= h(__t('ladder_no_data')) ?></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
    <section class="ladder-section">
      <h2 class="ladder-heading"><?= h(__t('ladder_arena_2')) ?></h2>
      <table class="ladder-table">
        <thead>
          <tr>
            <th>#</th>
            <th><?= h(__t('ladder_team')) ?></th>
            <th><?= h(__t('ladder_rating')) ?></th>
            <th><?= h(__t('ladder_season_record')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($a2 as $i => $row): ?>
          <tr>
            <td><?= (int)($i + 1) ?></td>
            <td><?= h($row['name'] ?? '') ?></td>
            <td><?= (int)($row['rating'] ?? 0) ?></td>
            <td><?= (int)($row['sw'] ?? 0) ?> / <?= (int)($row['sg'] ?? 0) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($a2)): ?>
          <tr><td colspan="4" class="ladder-empty"><?= h(__t('ladder_no_data')) ?></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
    <section class="ladder-section">
      <h2 class="ladder-heading"><?= h(__t('ladder_arena_3')) ?></h2>
      <table class="ladder-table">
        <thead>
          <tr>
            <th>#</th>
            <th><?= h(__t('ladder_team')) ?></th>
            <th><?= h(__t('ladder_rating')) ?></th>
            <th><?= h(__t('ladder_season_record')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($a3 as $i => $row): ?>
          <tr>
            <td><?= (int)($i + 1) ?></td>
            <td><?= h($row['name'] ?? '') ?></td>
            <td><?= (int)($row['rating'] ?? 0) ?></td>
            <td><?= (int)($row['sw'] ?? 0) ?> / <?= (int)($row['sg'] ?? 0) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($a3)): ?>
          <tr><td colspan="4" class="ladder-empty"><?= h(__t('ladder_no_data')) ?></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
    <section class="ladder-section ladder-section-wide">
      <h2 class="ladder-heading"><?= h(__t('ladder_arena_5')) ?></h2>
      <table class="ladder-table">
        <thead>
          <tr>
            <th>#</th>
            <th><?= h(__t('ladder_team')) ?></th>
            <th><?= h(__t('ladder_rating')) ?></th>
            <th><?= h(__t('ladder_season_record')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($a5 as $i => $row): ?>
          <tr>
            <td><?= (int)($i + 1) ?></td>
            <td><?= h($row['name'] ?? '') ?></td>
            <td><?= (int)($row['rating'] ?? 0) ?></td>
            <td><?= (int)($row['sw'] ?? 0) ?> / <?= (int)($row['sg'] ?? 0) ?></td>
          </tr>
          <?php endforeach; ?>
          <?php if (empty($a5)): ?>
          <tr><td colspan="4" class="ladder-empty"><?= h(__t('ladder_no_data')) ?></td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </div>
</div>
