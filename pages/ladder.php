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
  <div class="ladder-tabs">
    <input type="radio" name="ladder-tab" id="ladder-tab-pt" class="ladder-tab-input" checked>
    <input type="radio" name="ladder-tab" id="ladder-tab-hk" class="ladder-tab-input">
    <input type="radio" name="ladder-tab" id="ladder-tab-a2" class="ladder-tab-input">
    <input type="radio" name="ladder-tab" id="ladder-tab-a3" class="ladder-tab-input">
    <input type="radio" name="ladder-tab" id="ladder-tab-a5" class="ladder-tab-input">

    <div class="ladder-tab-nav">
      <label class="ladder-tab-btn" for="ladder-tab-pt"><?= h(__t('ladder_top_playtime')) ?></label>
      <label class="ladder-tab-btn" for="ladder-tab-hk"><?= h(__t('ladder_top_hk')) ?></label>
      <label class="ladder-tab-btn" for="ladder-tab-a2"><?= h(__t('ladder_arena_2')) ?></label>
      <label class="ladder-tab-btn" for="ladder-tab-a3"><?= h(__t('ladder_arena_3')) ?></label>
      <label class="ladder-tab-btn" for="ladder-tab-a5"><?= h(__t('ladder_arena_5')) ?></label>
    </div>

    <div class="ladder-tab-panel" id="ladder-panel-pt">
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
    </div>

    <div class="ladder-tab-panel" id="ladder-panel-hk">
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
    </div>

    <div class="ladder-tab-panel" id="ladder-panel-a2">
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
    </div>

    <div class="ladder-tab-panel" id="ladder-panel-a3">
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
    </div>

    <div class="ladder-tab-panel" id="ladder-panel-a5">
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
    </div>
  </div>
</div>
