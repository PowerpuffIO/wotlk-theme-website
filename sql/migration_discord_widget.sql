INSERT INTO `site_settings` (`skey`, `svalue`) VALUES
('discord_guild_id', ''),
('discord_widget_theme', 'dark')
ON DUPLICATE KEY UPDATE `svalue` = `svalue`;
