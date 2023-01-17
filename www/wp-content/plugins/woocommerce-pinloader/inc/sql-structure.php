<?php
$tables = "
	CREATE TABLE mod_products (
		`products_id` bigint(20) UNSIGNED NOT NULL,
	    `products_name` longtext,
	    `products_status` varchar(20),
		`id_supplier` int(10) UNSIGNED NOT NULL,
	    `name_supplier` varchar(255),
		`products_model` longtext,
		`products_price` longtext,
		`products_filled` int(1) UNSIGNED NOT NULL DEFAULT 0,
		`thumb_id` int(15) UNSIGNED NOT NULL DEFAULT 0,
		`yoast_seo` int(1) UNSIGNED NOT NULL DEFAULT 0,
		`image_alt` int(1) UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY (`products_id`),
		UNIQUE KEY `products_id` (`products_id`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_diapasons (
		`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`id_supplier` int(10) UNSIGNED NOT NULL,
		`price_low` int(10) UNSIGNED NOT NULL,
		`price_high` int(10) UNSIGNED NOT NULL,
		`coefficient` float UNSIGNED NOT NULL,
		`delivery` int(10) UNSIGNED NOT NULL,
		`floor` tinyint(3) UNSIGNED NOT NULL,
		`minus` int(10) UNSIGNED NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_monitoring_data (
		`products_id` int(10) UNSIGNED NOT NULL,
		`color` enum('1','0') NOT NULL DEFAULT '0',
		`minprice` mediumint(9) NOT NULL,
		`firm` varchar(3) NOT NULL,
		`date` datetime NOT NULL,
		PRIMARY KEY (`products_id`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_products_monitor_price (
	    `products_id` int(10) unsigned,
	    `products_name` varchar(255),
	    `products_name_new` varchar(255),
	    `id_supplier` tinyint(10) unsigned,
	    `name_supplier` varchar(255),
	    `products_model` varchar(255),
	    `products_price` decimal(12,0),
	    `new_price` mediumint(8),
	    `nacenka` decimal(13,0),
	    `coefficient` decimal(15,3),
	    `date` varchar(8),
	    `color` varchar(1),
	    `minprice` mediumint(9),
	    `firm` varchar(3),
	    `raznica` decimal(13,0)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_products_new (
		`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`products_id` int(10) UNSIGNED NOT NULL,
		`id_supplier` tinyint(3) UNSIGNED NOT NULL,
		`products_model` varchar(255) NOT NULL,
		`products_name` varchar(255) NOT NULL,
		`ignore` enum('yes','no') NOT NULL DEFAULT 'no',
		`current_list` enum('on','off') NOT NULL DEFAULT 'off',
		`price` mediumint(8) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE KEY `id_supplier` (`id_supplier`,`products_model`),
		UNIQUE KEY `products_id` (`products_id`,`id_supplier`,`products_model`),
		KEY `id_supplier_2` (`id_supplier`),
		KEY `ignore` (`ignore`),
		KEY `current_list` (`current_list`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_products_price (
		`products_id` int(10) UNSIGNED NOT NULL,
		`price` mediumint(8) NOT NULL,
		`new_price` mediumint(8) NOT NULL,
		PRIMARY KEY (`products_id`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_products_price_change (
	    `products_id` int(10) unsigned,
	    `products_name` varchar(255),
	    `products_name_new` varchar(255),
	    `id_supplier` tinyint(10) unsigned,
	    `name_supplier` varchar(255),
	    `products_model` varchar(255),
	    `products_price` decimal(12,0),
	    `price` mediumint(8),
	    `new_price` mediumint(8),
	    `pricediff` int(9),
	    `percentdiff` decimal(13,1),
	    `nacenka` decimal(13,0)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_products_to_switch_on (
	    `products_id` int(10) unsigned,
	    `ignore` enum('yes','no'),
	    `products_name` varchar(255),
	    `products_name_new` varchar(255),
	    `id_supplier` tinyint(10) unsigned,
	    `name_supplier` varchar(255),
	    `products_model` varchar(255),
	    `products_price` decimal(12,0),
	    `price` mediumint(8),
	    `new_price` mediumint(8),
	    `pricediff` int(9),
	    `percentdiff` decimal(13,1),
	    `nacenka` decimal(13,0)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_soho_current (
		`products_id` int(10) UNSIGNED NOT NULL,
		`id_supplier` tinyint(3) UNSIGNED NOT NULL,
		`ostatok` tinyint(3) UNSIGNED NOT NULL,
		`roznica` tinyint(3) UNSIGNED NOT NULL,
		PRIMARY KEY (`products_id`,`id_supplier`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_soho_prev (
		`products_id` int(10) UNSIGNED NOT NULL,
		`id_supplier` tinyint(3) UNSIGNED NOT NULL,
		`ostatok` tinyint(3) UNSIGNED NOT NULL,
		`roznica` tinyint(3) UNSIGNED NOT NULL,
		PRIMARY KEY (`products_id`,`id_supplier`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_soho_temp (
		`id_supplier` tinyint(3) UNSIGNED NOT NULL,
		`products_model` varchar(255) NOT NULL,
		`ostatok` tinyint(3) UNSIGNED NOT NULL,
		`roznica` tinyint(3) UNSIGNED NOT NULL,
		PRIMARY KEY (`id_supplier`,`products_model`) USING BTREE
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_suppliers (
		`id_supplier` tinyint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`term_id` int(11) UNSIGNED NOT NULL DEFAULT 0,
		`name_supplier` varchar(255) NOT NULL,
		`sku_suffix` varchar(5) NOT NULL,
		`code` tinyint(3) UNSIGNED NOT NULL,
		`name` tinyint(3) UNSIGNED NOT NULL,
		`price` tinyint(3) UNSIGNED NOT NULL,
		`coefficient` float UNSIGNED NOT NULL,
		`diapason` enum('1','0') NOT NULL DEFAULT '0',
		PRIMARY KEY (`id_supplier`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_temp (
		`sid` char(32) NOT NULL,
		`type` varchar(32) NOT NULL,
		`data` mediumtext NOT NULL,
		`date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
		PRIMARY KEY (`sid`,`type`)
	) ENGINE = INNODB {charset_collate};
	CREATE TABLE mod_products_group (
		`id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`product_ids` varchar(255) NOT NULL,
		`color` varchar(25) NOT NULL,
		PRIMARY KEY (`id`)
	) ENGINE = INNODB {charset_collate};
";

$views = "
	DROP VIEW IF EXISTS `mod_products_monitor_price`;
	CREATE ALGORITHM=UNDEFINED DEFINER=`{db_name}`@`localhost` SQL SECURITY DEFINER VIEW `mod_products_monitor_price` AS SELECT pr.products_id AS products_id, p.products_name AS products_name, n.products_name AS products_name_new, p.id_supplier AS id_supplier, p.name_supplier AS name_supplier, p.products_model AS products_model, round(p.products_price, 0) AS products_price, pr.new_price AS new_price, round(p.products_price - pr.new_price, 0) AS nacenka, abs(round(p.products_price / pr.new_price, 3)) AS coefficient, if(mp.date, date_format(mp.date, '%d.%m.%y'), NULL) AS date, if(mp.color, mp.color, NULL) AS color, if(mp.minprice, mp.minprice, NULL) AS minprice, mp.firm AS firm, if(mp.minprice, round(mp.minprice - p.products_price, 0), NULL) AS raznica FROM ((mod_products_price pr JOIN (mod_products p LEFT JOIN mod_monitoring_data mp ON (p.products_id = mp.products_id))) JOIN mod_products_new n) WHERE (p.products_status = 'publish' OR p.products_status = 'pending') AND pr.products_id = p.products_id AND p.products_id = n.products_id;
	DROP VIEW IF EXISTS `mod_products_price_change`;
	CREATE ALGORITHM=UNDEFINED DEFINER=`{db_name}`@`localhost` SQL SECURITY DEFINER VIEW `mod_products_price_change` AS SELECT pr.products_id AS products_id, p.products_name AS products_name, n.products_name AS products_name_new, p.id_supplier AS id_supplier, p.name_supplier AS name_supplier, p.products_model AS products_model, round(p.products_price, 0) AS products_price, pr.price AS price, pr.new_price AS new_price, pr.new_price - pr.price AS pricediff, abs(round((pr.new_price - pr.price) / pr.price * 100, 1)) AS percentdiff, round(p.products_price - pr.new_price, 0) AS nacenka FROM ((mod_products_price pr JOIN mod_products p) JOIN mod_products_new n) WHERE pr.new_price - pr.price <> 0 AND (p.products_status = 'publish' OR p.products_status = 'pending') AND pr.products_id = p.products_id AND p.products_id = n.products_id AND n.ignore = 'no' AND n.current_list = 'on' ORDER BY nacenka, percentdiff DESC, abs(pricediff) DESC;
	DROP VIEW IF EXISTS `mod_products_to_switch_on`;
	CREATE ALGORITHM=UNDEFINED DEFINER=`{db_name}`@`localhost` SQL SECURITY DEFINER VIEW `mod_products_to_switch_on` AS SELECT pr.products_id AS products_id, n.ignore AS `ignore`, p.products_name AS products_name, n.products_name AS products_name_new, p.id_supplier AS id_supplier, p.name_supplier AS name_supplier, p.products_model AS products_model, round(p.products_price, 0) AS products_price, pr.price AS price, pr.new_price AS new_price, pr.new_price - pr.price AS pricediff, abs(round((pr.new_price - pr.price) / pr.price * 100, 1)) AS percentdiff, round(p.products_price - pr.new_price, 0) AS nacenka FROM mod_products_price pr JOIN mod_products p JOIN mod_products_new n WHERE p.products_status = 'draft' AND pr.products_id = p.products_id AND p.products_id = n.products_id AND n.ignore = 'no' AND n.current_list = 'on' ORDER BY nacenka, percentdiff DESC, abs(pricediff) DESC;
	DROP VIEW IF EXISTS `mod_products_vc`;
	CREATE ALGORITHM=UNDEFINED DEFINER=`{db_name}`@`localhost` SQL SECURITY DEFINER VIEW mod_products_vc AS SELECT p.ID AS products_id, p.post_title AS products_name, p.post_status AS product_status, s.id_supplier AS id_supplier, s.name_supplier AS name_supplier, pm.meta_value AS products_model, pm2.meta_value AS products_price, pm3.meta_value AS products_filled FROM ml_posts p LEFT JOIN ml_postmeta pm ON pm.post_id = p.ID LEFT JOIN ml_postmeta pm2 ON pm2.post_id = p.ID LEFT JOIN ml_postmeta pm3 ON pm3.post_id = p.ID AND pm3.meta_key = 'products_filled_via_ym' LEFT JOIN ml_term_relationships tr ON tr.object_id = p.ID LEFT JOIN ml_term_taxonomy tt ON tt.term_taxonomy_id = tr.term_taxonomy_id LEFT JOIN mod_suppliers s ON s.term_id = tt.term_id WHERE p.post_type = 'product' AND tt.taxonomy = 'suppliers' AND pm.meta_key = 'products_model' AND pm2.meta_key = '_price' GROUP BY p.ID;
";

return ['tables' => $tables, 'views' => $views];
