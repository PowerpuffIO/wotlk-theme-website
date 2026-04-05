CREATE TABLE IF NOT EXISTS `shop_categories` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int unsigned NOT NULL DEFAULT '0',
  `name_ru` varchar(128) NOT NULL,
  `name_en` varchar(128) NOT NULL,
  `sort_order` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `shop_categories` (`id`, `parent_id`, `name_ru`, `name_en`, `sort_order`) VALUES
(1, 0, 'Транспорт', 'Mounts', 1),
(2, 0, 'Экипировка', 'Equipment', 2),
(3, 0, 'Питомцы', 'Pets', 3),
(4, 0, 'Расходуемые', 'Consumables', 4),
(5, 0, 'Прочее', 'Miscellaneous', 5),
(11, 1, 'Наземные средства передвижения', 'Ground mounts', 1),
(12, 1, 'Летающие средства передвижения', 'Flying mounts', 2),
(60, 2, 'Одноручные топоры', 'One-Handed Axes', 10),
(61, 2, 'Двуручные топоры', 'Two-Handed Axes', 11),
(62, 2, 'Луки', 'Bows', 12),
(63, 2, 'Ружья', 'Guns', 13),
(64, 2, 'Одноручное дробящее', 'One-Handed Maces', 14),
(65, 2, 'Двуручное дробящее', 'Two-Handed Maces', 15),
(66, 2, 'Древковое оружие', 'Polearms', 16),
(67, 2, 'Одноручные мечи', 'One-Handed Swords', 17),
(68, 2, 'Двуручные мечи', 'Two-Handed Swords', 18),
(69, 2, 'Посохи', 'Staves', 19),
(70, 2, 'Кистевое оружие', 'Fist Weapons', 20),
(71, 2, 'Кинжалы', 'Daggers', 21),
(72, 2, 'Арбалеты', 'Crossbows', 22),
(73, 2, 'Жезлы', 'Wands', 23),
(74, 2, 'Метательное оружие', 'Thrown', 24),
(75, 2, 'Удочки', 'Fishing Poles', 25),
(76, 2, 'Ткань', 'Cloth', 30),
(77, 2, 'Кожа', 'Leather', 31),
(78, 2, 'Кольчуга', 'Mail', 32),
(79, 2, 'Латы', 'Plate', 33),
(80, 2, 'Щиты', 'Shields', 34),
(81, 2, 'Плащи', 'Cloaks', 35),
(82, 2, 'Аксессуары (тринкеты)', 'Trinkets', 36),
(83, 2, 'Кольца', 'Rings', 37),
(84, 2, 'Ожерелья', 'Necklaces', 38),
(85, 2, 'Реликвии (кодексы, идолы, тотемы, печати)', 'Relics (Librams, Idols, Totems, Sigils)', 39),
(100, 3, 'Спутники', 'Companions', 1),
(110, 4, 'Зелья и настои', 'Potions & flasks', 1),
(111, 4, 'Еда и напитки', 'Food & drink', 2),
(112, 4, 'Сумки и контейнеры', 'Bags & containers', 3),
(113, 4, 'Свитки', 'Scrolls', 4),
(114, 4, 'Бинты', 'Bandages', 5),
(115, 4, 'Прочие расходуемые', 'Other consumables', 6),
(120, 5, 'Хозяйственные товары', 'Trade goods', 1),
(121, 5, 'Рецепты', 'Recipes', 2),
(122, 5, 'Самоцветы', 'Gems', 3),
(123, 5, 'Реагенты', 'Reagents', 4),
(124, 5, 'Другое', 'Other', 5);

ALTER TABLE `shop_categories` AUTO_INCREMENT = 200;

CREATE TABLE IF NOT EXISTS `shop_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `subcategory_id` int unsigned NOT NULL,
  `item_entry` int unsigned NOT NULL,
  `price` int unsigned NOT NULL,
  `quantity` int unsigned NOT NULL DEFAULT '1',
  `enabled` tinyint unsigned NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `subcategory_id` (`subcategory_id`),
  KEY `enabled` (`enabled`),
  KEY `item_entry` (`item_entry`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `site_settings` (`skey`, `svalue`) VALUES
('shop_enabled', '0');
