<?php
$discordSrc = discord_widget_iframe_src();
if ($discordSrc === '') {
    return;
}
?>
<div class="sidebar-discord-embed">
  <div class="sidebar-discord-inner">
    <iframe class="sidebar-discord-iframe" title="Discord" src="<?= h($discordSrc) ?>" allowtransparency="true" loading="lazy" sandbox="allow-popups allow-popups-to-escape-sandbox allow-same-origin allow-scripts"></iframe>
  </div>
</div>
