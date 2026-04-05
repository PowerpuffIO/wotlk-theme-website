<?php
require_once dirname(__DIR__) . '/includes/shop.php';
$u = current_user();
$lang = active_lang();
$items = shop_items_for_display();
$chars = shop_user_characters((int)$u['auth_account_id']);
$filterTree = shop_categories_filter_tree($lang);
$filterTreeJson = json_encode($filterTree, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS);
$navActive = 'shop';
$whLocale = $lang === 'ru' ? 8 : 0;
include dirname(__DIR__) . '/partials/profile_sidebar.php';
?>
  <h1><?= h(__t('menu_shop')) ?></h1>
  <p class="shop-balance-line"><strong><?= h(__t('vote_balance')) ?>:</strong> <span class="profile-balance-value" id="shop-balance-val"><?= (int)($u['balance'] ?? 0) ?></span></p>
  <?php if (empty($items)): ?>
  <p class="shop-empty"><?= h(__t('shop_no_items')) ?></p>
  <?php else: ?>
  <div class="shop-char-bar">
    <label class="shop-char-label" for="shop-char-select"><?= h(__t('shop_char_for_purchase')) ?></label>
    <select id="shop-char-select" class="theme-select shop-char-select shop-char-select-global">
      <option value=""><?= h(__t('shop_select_char')) ?></option>
      <?php foreach ($chars as $ch): ?>
      <option value="<?= h($ch['name']) ?>"><?= h($ch['name']) ?> (<?= (int)$ch['level'] ?>)</option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="shop-filter-bar" id="shop-filter-bar">
    <div class="shop-filter-field">
      <label class="shop-filter-label" for="shop-filter-cat"><?= h(__t('shop_filter_category')) ?></label>
      <select id="shop-filter-cat" class="theme-select shop-filter-select">
        <option value=""><?= h(__t('shop_filter_all')) ?></option>
        <?php foreach ($filterTree as $fc): ?>
        <option value="<?= (int)$fc['id'] ?>"><?= h($fc['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="shop-filter-field">
      <label class="shop-filter-label" for="shop-filter-sub"><?= h(__t('shop_filter_subcategory')) ?></label>
      <select id="shop-filter-sub" class="theme-select shop-filter-select">
        <option value=""><?= h(__t('shop_filter_all')) ?></option>
      </select>
    </div>
    <div class="shop-filter-field shop-filter-field-grow">
      <label class="shop-filter-label" for="shop-filter-q"><?= h(__t('shop_filter_search')) ?></label>
      <input type="search" id="shop-filter-q" class="shop-filter-search" placeholder="<?= h(__t('shop_filter_search_ph')) ?>" autocomplete="off">
    </div>
  </div>
  <p class="shop-filter-empty" id="shop-filter-empty" hidden><?= h(__t('shop_filter_no_results')) ?></p>
  <div class="shop-wrap shop-wrap-flat" id="shop-wrap">
    <div class="shop-table-wrap">
      <table class="shop-table">
        <thead>
          <tr>
            <th class="shop-col-entry"><?= h(__t('shop_col_entry')) ?></th>
            <th class="shop-col-name"><?= h(__t('shop_col_name')) ?></th>
            <th class="shop-col-price"><?= h(__t('shop_col_price')) ?></th>
            <th class="shop-col-act"><?= h(__t('shop_col_action')) ?></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $row): ?>
          <?php
            $entry = (int)$row['item_entry'];
            $catId = (int)$row['category_id'];
            $subId = (int)$row['subcategory_id'];
            $wh = shop_wowhead_item_url($entry, $lang);
            $dispName = __t('shop_item') . ' #' . $entry;
          ?>
          <tr class="shop-row" data-item-id="<?= (int)$row['id'] ?>" data-category-id="<?= $catId ?>" data-subcategory-id="<?= $subId ?>" data-item-entry="<?= $entry ?>">
            <td class="shop-td-entry"><?= $entry ?></td>
            <td class="shop-td-name">
              <a href="<?= h($wh) ?>" class="shop-item-link" target="_blank" rel="noopener"><?= h($dispName) ?></a>
            </td>
            <td class="shop-td-price"><?= (int)$row['price'] ?></td>
            <td class="shop-td-act"><button type="button" class="shop-buy-btn"><?= h(__t('shop_buy')) ?></button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
  <script>var whTooltips = { colorLinks: true, iconizeLinks: true, iconize: true, renameLinks: true, locale: <?= (int)$whLocale ?> };</script>
  <script src="https://wow.zamimg.com/widgets/power.js"></script>
  <script>window.SHOP_CAT_TREE = <?= $filterTreeJson ?>;</script>
  <?php endif; ?>
  <script>
(function(){
  var csrfMeta = document.querySelector('meta[name="csrf-token"]');
  var csrf = csrfMeta ? csrfMeta.getAttribute('content') : '';
  function toast(msg, kind) {
    if (typeof siteToast === 'function') siteToast(msg, kind);
    else alert(msg);
  }
  var charSel = document.getElementById('shop-char-select');
  document.querySelectorAll('.shop-row').forEach(function(row) {
    var itemId = row.getAttribute('data-item-id');
    var btn = row.querySelector('.shop-buy-btn');
    if (!btn || !itemId) return;
    btn.addEventListener('click', function() {
      var name = charSel ? (charSel.value || '').trim() : '';
      if (!name) { toast(<?= json_encode(__t('shop_select_char')) ?>, 'err'); return; }
      var fd = new FormData();
      fd.append('csrf', csrf);
      fd.append('item_id', itemId);
      fd.append('character_name', name);
      fetch(<?= json_encode(base_url('profile/shop/buy')) ?>, { method: 'POST', body: fd, credentials: 'same-origin' })
        .then(function(r) { return r.json(); })
        .then(function(j) {
          if (j.ok) {
            toast(j.message || <?= json_encode(__t('shop_toast_ok')) ?>, 'ok');
            var bal = document.getElementById('shop-balance-val');
            if (bal && typeof j.balance === 'number') bal.textContent = j.balance;
          } else {
            toast(j.message || <?= json_encode(__t('error_generic')) ?>, 'err');
          }
        })
        .catch(function() { toast(<?= json_encode(__t('error_generic')) ?>, 'err'); });
    });
  });

  var tree = window.SHOP_CAT_TREE || [];
  var catSel = document.getElementById('shop-filter-cat');
  var subSel = document.getElementById('shop-filter-sub');
  var qIn = document.getElementById('shop-filter-q');
  var emptyMsg = document.getElementById('shop-filter-empty');
  if (!catSel || !subSel || !qIn) return;

  function fillSubOptions(catId) {
    var allOpt = document.createElement('option');
    allOpt.value = '';
    allOpt.textContent = <?= json_encode(__t('shop_filter_all')) ?>;
    subSel.innerHTML = '';
    subSel.appendChild(allOpt);
    if (!catId) {
      tree.forEach(function(c) {
        (c.subs || []).forEach(function(s) {
          var o = document.createElement('option');
          o.value = String(s.id);
          o.textContent = s.name;
          o.setAttribute('data-parent-cat', String(c.id));
          subSel.appendChild(o);
        });
      });
      return;
    }
    var cat = tree.filter(function(c) { return String(c.id) === String(catId); })[0];
    if (!cat || !cat.subs) return;
    cat.subs.forEach(function(s) {
      var o = document.createElement('option');
      o.value = String(s.id);
      o.textContent = s.name;
      subSel.appendChild(o);
    });
  }

  function rowMatchesQuery(row, q) {
    if (!q) return true;
    var entry = (row.getAttribute('data-item-entry') || '').trim();
    var link = row.querySelector('.shop-item-link');
    var name = link ? (link.textContent || '').toLowerCase() : '';
    q = q.toLowerCase();
    if (/^\d+$/.test(q)) {
      return entry.indexOf(q) !== -1;
    }
    return name.indexOf(q) !== -1 || entry.indexOf(q) !== -1;
  }

  function hasActiveFilter() {
    return !!(catSel.value || subSel.value || (qIn.value || '').trim());
  }

  function applyFilters() {
    if (!hasActiveFilter()) {
      document.querySelectorAll('.shop-row').forEach(function(row) {
        row.style.display = '';
      });
      if (emptyMsg) emptyMsg.hidden = true;
      return;
    }
    var catId = catSel.value || '';
    var subId = subSel.value || '';
    var q = (qIn.value || '').trim();
    var anyVisible = false;
    document.querySelectorAll('.shop-row').forEach(function(row) {
      var rc = row.getAttribute('data-category-id') || '';
      var rs = row.getAttribute('data-subcategory-id') || '';
      var okCat = !catId || rc === catId;
      var okSub = !subId || rs === subId;
      var okQ = rowMatchesQuery(row, q);
      var show = okCat && okSub && okQ;
      row.style.display = show ? '' : 'none';
      if (show) anyVisible = true;
    });
    if (emptyMsg) {
      emptyMsg.hidden = anyVisible;
    }
  }

  fillSubOptions('');
  catSel.addEventListener('change', function() {
    fillSubOptions(catSel.value);
    subSel.value = '';
    applyFilters();
  });
  subSel.addEventListener('change', applyFilters);
  var tq = null;
  qIn.addEventListener('input', function() {
    clearTimeout(tq);
    tq = setTimeout(applyFilters, 200);
  });
  qIn.addEventListener('search', applyFilters);
  applyFilters();
})();
  </script>
<?php include dirname(__DIR__) . '/partials/profile_sidebar_end.php'; ?>
