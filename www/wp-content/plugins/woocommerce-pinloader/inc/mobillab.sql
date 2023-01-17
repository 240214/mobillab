
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `mobillab`
--


--
-- Структура таблицы `mod_diapasons`
--

CREATE TABLE IF NOT EXISTS `mod_diapasons` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_supplier` int(10) UNSIGNED NOT NULL,
  `price_low` int(10) UNSIGNED NOT NULL,
  `price_high` int(10) UNSIGNED NOT NULL,
  `coefficient` float UNSIGNED NOT NULL,
  `delivery` int(10) UNSIGNED NOT NULL,
  `floor` tinyint(3) UNSIGNED NOT NULL,
  `minus` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `mod_monitoring_data`
--

CREATE TABLE IF NOT EXISTS `mod_monitoring_data` (
  `products_id` int(10) UNSIGNED NOT NULL,
  `color` enum('1','0') NOT NULL DEFAULT '0',
  `minprice` mediumint(9) NOT NULL,
  `firm` varchar(3) NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`products_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `mod_products_monitor_price`
-- (См. Ниже фактическое представление)
--
CREATE TABLE IF NOT EXISTS `mod_products_monitor_price` (
`products_id` int(10) unsigned
,`products_name` varchar(255)
,`products_name_new` varchar(255)
,`id_supplier` tinyint(10) unsigned
,`name_supplier` varchar(255)
,`products_model` varchar(255)
,`products_price` decimal(12,0)
,`new_price` mediumint(8)
,`nacenka` decimal(13,0)
,`coefficient` decimal(15,3)
,`date` varchar(8)
,`color` varchar(1)
,`minprice` mediumint(9)
,`firm` varchar(3)
,`raznica` decimal(13,0)
);

-- --------------------------------------------------------

--
-- Структура таблицы `mod_products_new`
--

CREATE TABLE IF NOT EXISTS `mod_products_new` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `mod_products_price`
--

CREATE TABLE IF NOT EXISTS `mod_products_price` (
  `products_id` int(10) UNSIGNED NOT NULL,
  `price` mediumint(8) NOT NULL,
  `new_price` mediumint(8) NOT NULL,
  PRIMARY KEY (`products_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `mod_products_price_change`
-- (См. Ниже фактическое представление)
--
CREATE TABLE IF NOT EXISTS `mod_products_price_change` (
`products_id` int(10) unsigned
,`products_name` varchar(255)
,`products_name_new` varchar(255)
,`id_supplier` tinyint(10) unsigned
,`name_supplier` varchar(255)
,`products_model` varchar(255)
,`products_price` decimal(12,0)
,`price` mediumint(8)
,`new_price` mediumint(8)
,`pricediff` int(9)
,`percentdiff` decimal(13,1)
,`nacenka` decimal(13,0)
);

-- --------------------------------------------------------

--
-- Дублирующая структура для представления `mod_products_to_switch_on`
-- (См. Ниже фактическое представление)
--
CREATE TABLE IF NOT EXISTS `mod_products_to_switch_on` (
`products_id` int(10) unsigned
,`ignore` enum('yes','no')
,`products_name` varchar(255)
,`products_name_new` varchar(255)
,`id_supplier` tinyint(10) unsigned
,`name_supplier` varchar(255)
,`products_model` varchar(255)
,`products_price` decimal(12,0)
,`price` mediumint(8)
,`new_price` mediumint(8)
,`pricediff` int(9)
,`percentdiff` decimal(13,1)
,`nacenka` decimal(13,0)
);

-- --------------------------------------------------------

--
-- Структура таблицы `mod_soho_current`
--

CREATE TABLE IF NOT EXISTS `mod_soho_current` (
  `products_id` int(10) UNSIGNED NOT NULL,
  `id_supplier` tinyint(3) UNSIGNED NOT NULL,
  `ostatok` tinyint(3) UNSIGNED NOT NULL,
  `roznica` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`products_id`,`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `mod_soho_prev`
--

CREATE TABLE IF NOT EXISTS `mod_soho_prev` (
  `products_id` int(10) UNSIGNED NOT NULL,
  `id_supplier` tinyint(3) UNSIGNED NOT NULL,
  `ostatok` tinyint(3) UNSIGNED NOT NULL,
  `roznica` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`products_id`,`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Структура таблицы `mod_soho_temp`
--

CREATE TABLE IF NOT EXISTS `mod_soho_temp` (
  `id_supplier` tinyint(3) UNSIGNED NOT NULL,
  `products_model` varchar(255) NOT NULL,
  `ostatok` tinyint(3) UNSIGNED NOT NULL,
  `roznica` tinyint(3) UNSIGNED NOT NULL,
  PRIMARY KEY (`id_supplier`,`products_model`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `mod_suppliers`
--

CREATE TABLE IF NOT EXISTS `mod_suppliers` (
  `id_supplier` tinyint(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_supplier` varchar(255) NOT NULL,
  `code` tinyint(3) UNSIGNED NOT NULL,
  `name` tinyint(3) UNSIGNED NOT NULL,
  `price` tinyint(3) UNSIGNED NOT NULL,
  `coefficient` float UNSIGNED NOT NULL,
  `diapason` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `mod_temp`
--

CREATE TABLE IF NOT EXISTS `mod_temp` (
  `sid` char(32) NOT NULL,
  `type` varchar(32) NOT NULL,
  `data` mediumtext NOT NULL,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  PRIMARY KEY (`sid`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------


--
-- Структура для представления `mod_products_monitor_price`
--
DROP TABLE IF EXISTS `mod_products_monitor_price`;

CREATE ALGORITHM=UNDEFINED DEFINER=`mobillab`@`localhost` SQL SECURITY DEFINER VIEW `mod_products_monitor_price`  AS  select `pr`.`products_id` AS `products_id`,`d`.`products_name` AS `products_name`,`n`.`products_name` AS `products_name_new`,`s`.`id_supplier` AS `id_supplier`,`s`.`name_supplier` AS `name_supplier`,`p`.`products_model` AS `products_model`,round(`p`.`products_price`,0) AS `products_price`,`pr`.`new_price` AS `new_price`,round(`p`.`products_price` - `pr`.`new_price`,0) AS `nacenka`,abs(round(`p`.`products_price` / `pr`.`new_price`,3)) AS `coefficient`,if(`mp`.`date`,date_format(`mp`.`date`,'%d.%m.%y'),NULL) AS `date`,if(`mp`.`color`,`mp`.`color`,NULL) AS `color`,if(`mp`.`minprice`,`mp`.`minprice`,NULL) AS `minprice`,`mp`.`firm` AS `firm`,if(`mp`.`minprice`,round(`mp`.`minprice` - `p`.`products_price`,0),NULL) AS `raznica` from ((((`mod_products_price` `pr` join (`products` `p` left join `mod_monitoring_data` `mp` on(`p`.`products_id` = `mp`.`products_id`))) join `products_description` `d`) join `mod_products_new` `n`) join `mod_suppliers` `s`) where `p`.`products_status` <> 0 and `pr`.`products_id` = `p`.`products_id` and `p`.`products_id` = `d`.`products_id` and `p`.`products_id` = `n`.`products_id` and `p`.`id_supplier` = `s`.`id_supplier` ;

-- --------------------------------------------------------

--
-- Структура для представления `mod_products_price_change`
--
DROP TABLE IF EXISTS `mod_products_price_change`;

CREATE ALGORITHM=UNDEFINED DEFINER=`mobillab`@`localhost` SQL SECURITY DEFINER VIEW `mod_products_price_change`  AS  select `pr`.`products_id` AS `products_id`,`d`.`products_name` AS `products_name`,`n`.`products_name` AS `products_name_new`,`s`.`id_supplier` AS `id_supplier`,`s`.`name_supplier` AS `name_supplier`,`p`.`products_model` AS `products_model`,round(`p`.`products_price`,0) AS `products_price`,`pr`.`price` AS `price`,`pr`.`new_price` AS `new_price`,`pr`.`new_price` - `pr`.`price` AS `pricediff`,abs(round((`pr`.`new_price` - `pr`.`price`) / `pr`.`price` * 100,1)) AS `percentdiff`,round(`p`.`products_price` - `pr`.`new_price`,0) AS `nacenka` from ((((`mod_products_price` `pr` join `products` `p`) join `products_description` `d`) join `mod_products_new` `n`) join `mod_suppliers` `s`) where `pr`.`new_price` - `pr`.`price` <> 0 and `p`.`products_status` <> 0 and `pr`.`products_id` = `p`.`products_id` and `p`.`products_id` = `d`.`products_id` and `p`.`products_id` = `n`.`products_id` and `p`.`id_supplier` = `s`.`id_supplier` and `n`.`ignore` = 'no' and `n`.`current_list` = 'on' order by round(`p`.`products_price` - `pr`.`new_price`,0),abs(round((`pr`.`new_price` - `pr`.`price`) / `pr`.`price` * 100,1)) desc,abs(`pricediff`) desc ;

-- --------------------------------------------------------

--
-- Структура для представления `mod_products_to_switch_on`
--
DROP TABLE IF EXISTS `mod_products_to_switch_on`;

CREATE ALGORITHM=UNDEFINED DEFINER=`mobillab`@`localhost` SQL SECURITY DEFINER VIEW `mod_products_to_switch_on`  AS  select `pr`.`products_id` AS `products_id`,`n`.`ignore` AS `ignore`,`d`.`products_name` AS `products_name`,`n`.`products_name` AS `products_name_new`,`s`.`id_supplier` AS `id_supplier`,`s`.`name_supplier` AS `name_supplier`,`p`.`products_model` AS `products_model`,round(`p`.`products_price`,0) AS `products_price`,`pr`.`price` AS `price`,`pr`.`new_price` AS `new_price`,`pr`.`new_price` - `pr`.`price` AS `pricediff`,abs(round((`pr`.`new_price` - `pr`.`price`) / `pr`.`price` * 100,1)) AS `percentdiff`,round(`p`.`products_price` - `pr`.`new_price`,0) AS `nacenka` from ((((`mod_products_price` `pr` join `products` `p`) join `products_description` `d`) join `mod_products_new` `n`) join `mod_suppliers` `s`) where `p`.`products_status` = 0 and `pr`.`products_id` = `p`.`products_id` and `p`.`products_id` = `d`.`products_id` and `p`.`products_id` = `n`.`products_id` and `p`.`id_supplier` = `s`.`id_supplier` and `n`.`ignore` = 'no' and `n`.`current_list` = 'on' order by round(`p`.`products_price` - `pr`.`new_price`,0),abs(round((`pr`.`new_price` - `pr`.`price`) / `pr`.`price` * 100,1)) desc,abs(`pricediff`) desc ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
