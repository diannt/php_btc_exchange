-- --------------------------------------------------------
-- Хост:                         127.0.0.1
-- Версия сервера:               5.5.25 - MySQL Community Server (GPL)
-- ОС Сервера:                   Win32
-- HeidiSQL Версия:              8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Дамп структуры базы данных emonex
CREATE DATABASE IF NOT EXISTS `emonex` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `emonex`;


-- Дамп структуры для таблица emonex.at_btc
CREATE TABLE IF NOT EXISTS `at_btc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `address` text NOT NULL,
  `type` smallint(1) DEFAULT '0' COMMENT '0 - in, 1 - out',
  `done` smallint(1) DEFAULT '0' COMMENT '1 - done',
  `transaction_hash` text,
  `value` double DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UID` (`UID`),
  CONSTRAINT `UID` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Дамп данных таблицы emonex.at_btc: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `at_btc` DISABLE KEYS */;
/*!40000 ALTER TABLE `at_btc` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.at_egop
CREATE TABLE IF NOT EXISTS `at_egop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `our_wallet_id` int(11) DEFAULT NULL,
  `client_account` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `currency_id` int(11) NOT NULL,
  `amount` double NOT NULL,
  `type` smallint(1) NOT NULL,
  `status` smallint(1) NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `transaction_id` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_at_egop_user_id` (`UID`),
  KEY `FK_at_egop_currency_id` (`currency_id`),
  CONSTRAINT `FK_at_egop_currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`),
  CONSTRAINT `FK_at_egop_user_id` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.at_egop: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `at_egop` DISABLE KEYS */;
/*!40000 ALTER TABLE `at_egop` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.at_ltc
CREATE TABLE IF NOT EXISTS `at_ltc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `address` text COLLATE utf8_bin NOT NULL,
  `type` smallint(1) NOT NULL DEFAULT '0' COMMENT '0 - in, 1 - out',
  `done` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - done',
  `transaction_hash` text COLLATE utf8_bin,
  `value` double DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UIDEgop` (`UID`),
  CONSTRAINT `UIDEgop` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.at_ltc: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `at_ltc` DISABLE KEYS */;
/*!40000 ALTER TABLE `at_ltc` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.at_okp
CREATE TABLE IF NOT EXISTS `at_okp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `payee_email` char(24) COLLATE utf8_bin NOT NULL,
  `currency` varchar(3) COLLATE utf8_bin NOT NULL,
  `type` smallint(1) NOT NULL DEFAULT '0' COMMENT 'type of transaction: 0 - in, 1 - out',
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - done',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `payer_email` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `transaction_id` char(19) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UID_okp` (`UID`),
  CONSTRAINT `UID_okp` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.at_okp: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `at_okp` DISABLE KEYS */;
/*!40000 ALTER TABLE `at_okp` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.at_pm
CREATE TABLE IF NOT EXISTS `at_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `payee_account` varchar(10) COLLATE utf8_bin NOT NULL,
  `amount` double NOT NULL,
  `units` varchar(3) COLLATE utf8_bin NOT NULL COMMENT 'USD, EUR, OAU',
  `type` smallint(6) NOT NULL DEFAULT '0' COMMENT '0 - in, 1 - out',
  `timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `payer_account` varchar(10) COLLATE utf8_bin DEFAULT NULL,
  `batch_num` int(11) DEFAULT '0',
  `status` smallint(1) NOT NULL DEFAULT '0' COMMENT '1 - Done',
  PRIMARY KEY (`id`),
  KEY `UID_pm` (`UID`),
  CONSTRAINT `UID_pm` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.at_pm: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `at_pm` DISABLE KEYS */;
/*!40000 ALTER TABLE `at_pm` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.at_ym
CREATE TABLE IF NOT EXISTS `at_ym` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `wallet` varchar(255) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `UID_ym` (`UID`),
  CONSTRAINT `UID_ym` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.at_ym: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `at_ym` DISABLE KEYS */;
/*!40000 ALTER TABLE `at_ym` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.currency
CREATE TABLE IF NOT EXISTS `currency` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` varchar(50) NOT NULL,
  `tradeName` varchar(255) NOT NULL,
  `min_order_amount` double NOT NULL DEFAULT '0.01',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.currency: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `currency` DISABLE KEYS */;
INSERT INTO `currency` (`id`, `Name`, `tradeName`, `min_order_amount`) VALUES
	(1, 'BTC', 'Bitcoin', 0.01),
	(2, 'USD', 'United States dollar', 0.01),
	(3, 'EUR', 'Euro', 0.01),
	(4, 'RUB', 'Russian Ruble', 0.01),
	(5, 'LTC', 'Litecoin', 0.01);
/*!40000 ALTER TABLE `currency` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.currency_payment_system
CREATE TABLE IF NOT EXISTS `currency_payment_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cur_id` int(11) NOT NULL,
  `system_id` int(11) NOT NULL,
  `system_fee` double NOT NULL,
  `input_fee` double NOT NULL DEFAULT '0',
  `input_min` double NOT NULL DEFAULT '0.01',
  `input_max` double DEFAULT NULL,
  `output_fee` double NOT NULL DEFAULT '0',
  `output_min` double NOT NULL DEFAULT '0.01',
  `output_max` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_currency_payment_system_currency_id` (`cur_id`),
  KEY `FK_currency_payment_system_payment_system_id` (`system_id`),
  CONSTRAINT `FK_currency_payment_system_payment_system_id` FOREIGN KEY (`system_id`) REFERENCES `payment_system` (`id`),
  CONSTRAINT `FK_currency_payment_system_currency_id` FOREIGN KEY (`cur_id`) REFERENCES `currency` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.currency_payment_system: ~9 rows (приблизительно)
/*!40000 ALTER TABLE `currency_payment_system` DISABLE KEYS */;
INSERT INTO `currency_payment_system` (`id`, `cur_id`, `system_id`, `system_fee`, `input_fee`, `input_min`, `input_max`, `output_fee`, `output_min`, `output_max`) VALUES
	(1, 1, 1, 0, 0, 0.01, NULL, 0, 0.01, NULL),
	(2, 2, 2, 0.0199, 0, 0.01, NULL, 0.02, 0.01, NULL),
	(3, 3, 2, 0.0199, 0, 0.01, NULL, 0.02, 0.01, NULL),
	(4, 4, 3, 0.005, 0, 0.01, NULL, 0.01, 0.01, NULL),
	(5, 5, 4, 0, 0, 0.01, NULL, 0, 0.01, NULL),
	(6, 2, 5, 0, 0, 0.01, NULL, 0, 0.01, NULL),
	(7, 3, 5, 0, 0, 0.01, NULL, 0, 0.01, NULL),
	(10, 2, 7, 0, 0, 0.01, NULL, 0, 0.01, NULL),
	(11, 3, 7, 0, 0, 0.01, NULL, 0, 0.01, NULL);
/*!40000 ALTER TABLE `currency_payment_system` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.deal
CREATE TABLE IF NOT EXISTS `deal` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `OrderId` int(11) NOT NULL,
  `Price` double NOT NULL,
  `Type` int(1) NOT NULL,
  `RateId` int(11) NOT NULL,
  `Volume` double NOT NULL,
  `UID` int(11) NOT NULL,
  `Date` datetime NOT NULL,
  `Done` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_Deal_Order_id` (`OrderId`),
  KEY `FK_Deal_User_id` (`UID`),
  KEY `FK_Deal_Rate_id` (`RateId`),
  CONSTRAINT `FK_Deal_Order_id` FOREIGN KEY (`OrderId`) REFERENCES `order` (`id`),
  CONSTRAINT `FK_Deal_Rate_id` FOREIGN KEY (`RateId`) REFERENCES `rate` (`id`),
  CONSTRAINT `FK_Deal_User_id` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=171 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.deal: ~15 rows (приблизительно)
/*!40000 ALTER TABLE `deal` DISABLE KEYS */;
INSERT INTO `deal` (`id`, `OrderId`, `Price`, `Type`, `RateId`, `Volume`, `UID`, `Date`, `Done`) VALUES
	(149, 112, 816.5, 1, 1, 1, 2, '2014-01-28 02:13:19', 1),
	(150, 113, 816.5, 0, 1, 1, 2, '2014-01-28 02:13:49', 1),
	(151, 113, 816.5, 0, 1, 1, 2, '2014-01-28 02:13:19', 1),
	(152, 114, 816.5, 1, 1, 1, 2, '2014-01-28 02:13:49', 1),
	(154, 116, 820.5, 0, 1, 1, 2, '2014-01-28 02:15:22', 1),
	(155, 115, 820.5, 1, 1, 1, 2, '2014-01-28 02:15:22', 1),
	(156, 117, 0.02734, 1, 11, 5, 2, '2014-02-20 11:28:48', 0),
	(163, 124, 0.02734, 1, 11, 5, 2, '2014-02-21 10:21:30', 0),
	(164, 125, 0.02734, 1, 11, 5, 2, '2014-02-21 10:21:32', 0),
	(165, 126, 0.02734, 1, 11, 5, 2, '2014-02-21 10:21:34', 0),
	(166, 127, 0.02734, 1, 11, 4, 2, '2014-02-21 10:21:36', 0),
	(167, 128, 0.02734, 1, 11, 6, 2, '2014-02-21 10:21:38', 0),
	(168, 129, 0.02734, 1, 11, 9, 2, '2014-02-21 10:21:40', 0),
	(169, 130, 0.02734, 1, 11, 5, 2, '2014-02-21 10:21:41', 0),
	(170, 131, 0.02734, 1, 11, 100, 2, '2014-02-21 10:23:02', 0);
/*!40000 ALTER TABLE `deal` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.default_widgets
CREATE TABLE IF NOT EXISTS `default_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Country` char(2) NOT NULL COMMENT 'two letter country code',
  `WidgetId` int(11) NOT NULL,
  `Priority` int(11) NOT NULL,
  `RateId` int(11) NOT NULL,
  `Page` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.default_widgets: ~9 rows (приблизительно)
/*!40000 ALTER TABLE `default_widgets` DISABLE KEYS */;
INSERT INTO `default_widgets` (`id`, `Country`, `WidgetId`, `Priority`, `RateId`, `Page`) VALUES
	(1, 'RU', 1, 2, 1, 0),
	(2, 'RU', 2, 3, 1, 0),
	(3, 'US', 1, 2, 2, 0),
	(4, 'US', 2, 3, 2, 0),
	(5, 'CN', 1, 2, 3, 0),
	(6, 'CN', 2, 3, 3, 0),
	(7, 'US', 5, 1, 2, 0),
	(8, 'RU', 5, 1, 1, 0),
	(9, 'CN', 5, 1, 3, 0);
/*!40000 ALTER TABLE `default_widgets` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.feedback
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` int(11) NOT NULL,
  `UID` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `closed` int(1) NOT NULL DEFAULT '0',
  `type` enum('breaking','bug','info','') NOT NULL DEFAULT 'info',
  KEY `UID_feedback` (`UID`),
  CONSTRAINT `UID_feedback` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.feedback: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `feedback` DISABLE KEYS */;
/*!40000 ALTER TABLE `feedback` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.localization
CREATE TABLE IF NOT EXISTS `localization` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `EN` text NOT NULL,
  `RU` text NOT NULL,
  `ES` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=96 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.localization: ~93 rows (приблизительно)
/*!40000 ALTER TABLE `localization` DISABLE KEYS */;
INSERT INTO `localization` (`id`, `EN`, `RU`, `ES`) VALUES
	(1, 'Dashboard', 'Dashboard', 'Dashboard'),
	(2, 'Welcome', '%D0%94%D0%BE%D0%B1%D1%80%D0%BE%20%D0%BF%D0%BE%D0%B6%D0%B0%D0%BB%D0%BE%D0%B2%D0%B0%D1%82%D1%8C', 'Bienvenido'),
	(3, 'Logout', '%D0%92%D1%8B%D0%B9%D1%82%D0%B8', 'Salir'),
	(4, 'My page', '%D0%9C%D0%BE%D1%8F%20%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B8%D1%86%D0%B0', 'Mi%20p%C3%A1gina'),
	(5, 'Please', '%D0%9F%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0', 'Por%20favor'),
	(6, 'Register', '%D0%97%D0%B0%D1%80%D0%B5%D0%B3%D0%B8%D1%81%D1%82%D1%80%D0%B8%D1%80%D1%83%D0%B9%D1%82%D0%B5%D1%81%D1%8C', 'Registrar'),
	(7, 'or', '%D0%B8%D0%BB%D0%B8', 'o'),
	(8, 'Sign in', '%D0%92%D0%BE%D0%B9%D0%B4%D0%B8%D1%82%D0%B5', 'Registrarse'),
	(9, 'Login', '%D0%9B%D0%BE%D0%B3%D0%B8%D0%BD', 'Login'),
	(10, 'Password', '%D0%9F%D0%B0%D1%80%D0%BE%D0%BB%D1%8C', 'Contrase%C3%B1a'),
	(11, 'Finances', '%D0%A4%D0%B8%D0%BD%D0%B0%D0%BD%D1%81%D1%8B', 'Finanzas'),
	(12, 'Deals history', '%D0%98%D1%81%D1%82%D0%BE%D1%80%D0%B8%D1%8F%20%D1%81%D0%B4%D0%B5%D0%BB%D0%BE%D0%BA', 'Ofertas%20de%20historia'),
	(13, 'Success', '%D0%A3%D1%81%D0%BF%D0%B5%D1%85%0A', '%C3%89xito'),
	(14, 'Payment is done', '%D0%9E%D0%BF%D0%BB%D0%B0%D1%82%D0%B0%20%D0%BF%D1%80%D0%BE%D0%B8%D0%B7%D0%BE%D1%88%D0%BB%D0%B0', 'El%20pago%20se%20realiza'),
	(15, 'Your wallets', '%D0%92%D0%B0%D1%88%D0%B8%20%D0%BA%D0%BE%D1%88%D0%B5%D0%BB%D1%8C%D0%BA%D0%B8', 'Sus%20carteras'),
	(16, 'Error', '%D0%9E%D1%88%D0%B8%D0%B1%D0%BA%D0%B0', 'Error'),
	(17, 'Wrong user action', '%D0%9D%D0%B5%D0%B2%D0%B5%D1%80%D0%BD%D0%BE%D0%B5%20%D0%B4%D0%B5%D0%B9%D1%81%D1%82%D0%B2%D0%B8%D0%B5%20%D0%BF%D0%BE%D0%BB%D1%8C%D0%B7%D0%BE%D0%B2%D0%B0%D1%82%D0%B5%D0%BB%D1%8F', 'Acci%C3%B3n%20de%20usuario%20incorrecto'),
	(18, 'Wrong money value', '%D0%9D%D0%B5%D0%B2%D0%B5%D1%80%D0%BD%D0%BE%D0%B5%20%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B4%D0%B5%D0%BD%D0%B5%D0%B3', 'Valor%20monetario%20incorrecto'),
	(19, 'Server error', '%D0%9E%D1%88%D0%B8%D0%B1%D0%BA%D0%B0%20%D1%81%D0%B5%D1%80%D0%B2%D0%B5%D1%80%D0%B0', 'Error%20del%20servidor'),
	(20, 'Please try later', '%D0%9F%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0%2C%20%D0%BF%D0%BE%D0%BF%D1%80%D0%BE%D0%B1%D1%83%D0%B9%D1%82%D0%B5%20%D0%BF%D0%BE%D0%B7%D0%B4%D0%BD%D0%B5%D0%B5', 'Por%20favor%2C%20int%C3%A9ntelo%20m%C3%A1s%20tarde'),
	(21, 'Your account now associated with you purse. Please try again to pay.', '%D0%A1%D0%B5%D0%B9%D1%87%D0%B0%D1%81%20%D0%B2%D0%B0%D1%88%20%D0%B0%D0%BA%D0%BA%D0%B0%D1%83%D0%BD%D1%82%20%D1%81%D0%B2%D1%8F%D0%B7%D0%B0%D0%BD%20%D1%81%20%D0%B2%D0%B0%D1%88%D0%B8%D0%BC%20%D0%BA%D0%BE%D1%88%D0%B5%D0%BB%D1%8C%D0%BA%D0%BE%D0%BC.%20%D0%9F%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0%2C%20%D0%BF%D0%BE%D0%BF%D1%80%D0%BE%D0%B1%D1%83%D0%B9%D1%82%D0%B5%20%D0%BE%D0%BF%D0%BB%D0%B0%D1%82%D0%B8%D1%82%D1%8C%20%D0%BF%D0%BE%D0%B7%D0%B4%D0%BD%D0%B5%D0%B5.', 'Tu%20cuenta%20ahora%20asociado%20con%20usted%20bolso.%20Por%20favor%2C%20int%C3%A9ntalo%20de%20nuevo%20a%20pagar.'),
	(22, 'Value', '%D0%9A%D0%BE%D0%BB%D0%B8%D1%87%D0%B5%D1%81%D1%82%D0%B2%D0%BE', 'Valor'),
	(23, 'Deals history', '%D0%98%D1%81%D1%82%D0%BE%D1%80%D0%B8%D1%8F%20%D1%81%D0%B4%D0%B5%D0%BB%D0%BE%D0%BA', 'Ofertas%20de%20historia'),
	(24, 'History of deals', '%D0%98%D1%81%D1%82%D0%BE%D1%80%D0%B8%D1%8F%20%D1%81%D0%B4%D0%B5%D0%BB%D0%BE%D0%BA', 'Historia%20de%20las%20ofertas'),
	(25, 'ID', 'ID', 'ID'),
	(26, 'Pair', '%D0%9F%D0%B0%D1%80%D0%B0', 'Par'),
	(27, 'Type', '%D0%A2%D0%B8%D0%BF', 'Tipo'),
	(28, 'Price', '%D0%A6%D0%B5%D0%BD%D0%B0', 'Precio'),
	(29, 'Volume', '%D0%9A%D0%BE%D0%BB%D0%B8%D1%87%D0%B5%D1%81%D1%82%D0%B2%D0%BE', 'Volumen'),
	(30, 'Date', '%D0%94%D0%B0%D1%82%D0%B0', 'Fecha'),
	(31, 'Done', '%D0%92%D1%8B%D0%BF%D0%BE%D0%BB%D0%BD%D0%B5%D0%BD%D0%BE', 'Hecho'),
	(32, 'Buy', '%D0%9A%D1%83%D0%BF%D0%B8%D1%82%D1%8C', 'Comprar'),
	(33, 'Sell', '%D0%9F%D1%80%D0%BE%D0%B4%D0%B0%D1%82%D1%8C', 'Vender'),
	(34, 'No', '%D0%9D%D0%B5%D1%82', 'No'),
	(35, 'Yes', '%D0%94%D0%B0', 'S%C3%AD'),
	(36, 'Your balance', '%D0%92%D0%B0%D1%88%20%D0%B1%D0%B0%D0%BB%D0%B0%D0%BD%D1%81', 'El%20saldo%20de%20su'),
	(37, 'Available balance', '%D0%94%D0%BE%D1%81%D1%82%D1%83%D0%BF%D0%BD%D0%BE', 'Saldo%20disponible'),
	(38, 'with', '%D0%B7%D0%B0', 'con'),
	(39, 'Your money', '%D0%92%D0%B0%D1%88%D0%B8%20%D1%81%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B2%D0%B0', 'Su%20dinero'),
	(40, 'Min price', '%D0%9C%D0%B8%D0%BD%20%D1%86%D0%B5%D0%BD%D0%B0', 'Mejor%20oferta'),
	(41, 'Price for', '%D0%A6%D0%B5%D0%BD%D0%B0%20%D0%B7%D0%B0', 'Precio%20de'),
	(42, 'Total', '%D0%92%D1%81%D0%B5%D0%B3%D0%BE', 'Total'),
	(43, 'Fee', '%D0%9A%D0%BE%D0%BC%D0%B8%D1%81%D1%81%D0%B8%D1%8F', 'Cuota'),
	(44, 'Press', '%D0%9D%D0%B0%D0%B6%D0%BC%D0%B8%D1%82%D0%B5', 'Prensa'),
	(45, 'count', '%D0%BF%D0%BE%D0%B4%D1%81%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C', 'contar'),
	(46, 'to know your orders price', '%D1%87%D1%82%D0%BE%D0%B1%D1%8B%20%D1%80%D0%B0%D1%81%D1%81%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C%20%D1%81%D1%83%D0%BC%D0%BC%D1%83%20%D0%B2%20%D1%81%D0%BE%D0%BE%D1%82%D0%B2%D0%B5%D1%82%D1%81%D1%82%D0%B2%D0%B8%D0%B8%20%D1%81%20%D0%BE%D1%80%D0%B4%D0%B5%D1%80%D0%B0%D0%BC%D0%B8', 'saber%20su%20precio%20pedidos'),
	(47, 'Count', '%D0%9F%D0%BE%D0%B4%D1%81%D1%87%D0%B8%D1%82%D0%B0%D1%82%D1%8C', 'Contar'),
	(48, 'Sell orders', '%D0%9E%D1%80%D0%B4%D0%B5%D1%80%D0%B0%20%D0%BD%D0%B0%20%D0%BF%D1%80%D0%BE%D0%B4%D0%B0%D0%B6%D1%83', 'Las%20%C3%B3rdenes%20de%20venta'),
	(49, 'price', '%D1%86%D0%B5%D0%BD%D0%B0', 'precio'),
	(50, 'Max price', '%D0%9C%D0%B0%D0%BA%D1%81%20%D1%86%D0%B5%D0%BD%D0%B0', 'Precio%20m%C3%A1ximo'),
	(51, 'Buy orders', '%D0%9E%D1%80%D0%B4%D0%B5%D1%80%D0%B0%20%D0%BD%D0%B0%20%D0%BF%D0%BE%D0%BA%D1%83%D0%BF%D0%BA%D1%83', 'Comprar%20pedidos'),
	(52, 'Please, sign in', '%D0%9F%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0%2C%20%D0%B2%D0%BE%D0%B9%D0%B4%D0%B8%D1%82%D0%B5', 'Por%20favor%2C%20inicia%20sesi%C3%B3n'),
	(53, 'Change Language', '%D0%A1%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C%20%D1%8F%D0%B7%D1%8B%D0%BA', 'Cambiar%20Idioma'),
	(54, 'Your current active orders', '%D0%92%D0%B0%D1%88%D0%B8+%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D1%8B%D0%B5+%D0%BE%D1%80%D0%B4%D0%B5%D1%80%D1%8B', 'Sus+actuales+%C3%B3rdenes+activas'),
	(57, 'Action', '%D0%94%D0%B5%D0%B9%D1%81%D1%82%D0%B2%D0%B8%D1%8F', 'Acci%C3%B3n'),
	(58, 'Amount', '%D0%9A%D0%BE%D0%BB-%D0%B2%D0%BE', 'Cantidad'),
	(59, 'No active orders at the moment', '%D0%9D%D0%B5%D1%82+%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D1%8B%D1%85+%D0%BE%D1%80%D0%B4%D0%B5%D1%80%D0%BE%D0%B2+%D0%B2+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%B9+%D0%BC%D0%BE%D0%BC%D0%B5%D0%BD%D1%82', 'No hay pedidos activas en este momento'),
	(60, 'Trade history', '%D0%98%D1%81%D1%82%D0%BE%D1%80%D0%B8%D1%8F+%D1%81%D0%B4%D0%B5%D0%BB%D0%BE%D0%BA', 'Historia%20del%20Comercio'),
	(61, 'No trades', '%D0%9D%D0%B5%D1%82+%D1%81%D0%B4%D0%B5%D0%BB%D0%BE%D0%BA', 'No%20hay%20oficios'),
	(62, 'Cancel', '%D0%9E%D1%82%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C', 'Cancelar'),
	(63, 'for', '%D0%B7%D0%B0', 'para'),
	(64, 'Sign In', '%D0%92%D0%BE%D0%B9%D1%82%D0%B8', 'Ingresar'),
	(65, '/public/img/flag/eng.png', '%2Fpublic%2Fimg%2Fflag%2Frus.png', '%2Fpublic%2Fimg%2Fflag%2Fspan.png'),
	(66, 'English', '%D0%A0%D1%83%D1%81%D1%81%D0%BA%D0%B8%D0%B9', 'Ingl%C3%A9s'),
	(67, 'Your funds', '%D0%92%D0%B0%D1%88%D0%B8+%D1%81%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B2%D0%B0', 'Sus%20fondos'),
	(68, 'Price for one', '%D0%A6%D0%B5%D0%BD%D0%B0+%D0%B7%D0%B0+%D0%B5%D0%B4%D0%B8%D0%BD%D0%B8%D1%86%D1%83', 'Precio%20para%20una'),
	(69, 'password', '%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8C', 'Contrase%C3%B1a'),
	(70, 'Repeat', '%D0%9F%D0%BE%D0%B2%D1%82%D0%BE%D1%80%D0%B8%D1%82%D0%B5', 'Repetici%C3%B3n'),
	(71, 'Password repeat is not correct', '%D0%9D%D0%B5%D0%B2%D0%B5%D1%80%D0%BD%D1%8B%D0%B9+%D0%BF%D0%BE%D0%B2%D1%82%D0%BE%D1%80+%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8F', 'Repetir%20contrase%C3%B1a%20no%20es%20correcta'),
	(72, 'Confirmation link has been sent, please enter in your email', '%D0%A1%D1%81%D1%8B%D0%BB%D0%BA%D0%B0+%D0%B4%D0%BB%D1%8F+%D0%BF%D0%BE%D0%B4%D1%82%D0%B2%D0%B5%D1%80%D0%B6%D0%B4%D0%B5%D0%BD%D0%B8%D1%8F+%D1%80%D0%B5%D0%B3%D0%B8%D1%81%D1%82%D1%80%D0%B0%D1%86%D0%B8%D0%B8+%D0%B2%D1%8B%D1%81%D0%BB%D0%B0%D0%BD%D0%B0%2C+%D0%BF%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0+%D0%BF%D0%B5%D1%80%D0%B5%D0%B9%D0%B4%D0%B8%D1%82%D0%B5+%D0%B2+%D0%92%D0%B0%D1%88+%D0%B5%D0%BC%D1%8D%D0%B9%D0%BB', 'Enlace+de+confirmaci%C3%B3n+se+ha+enviado%2C+por+favor+ingrese+en+su+correo+electr%C3%B3nico+'),
	(73, 'You wrote something wrong', '%D0%92%D1%8B+%D0%B2%D0%B2%D0%B5%D0%BB%D0%B8+%D1%87%D1%82%D0%BE-%D1%82%D0%BE+%D0%BD%D0%B5+%D0%B2%D0%B5%D1%80%D0%BD%D0%BE', 'Usted%20escribi%C3%B3%20algo%20mal'),
	(74, 'Email', '%D0%95%D0%BC%D1%8D%D0%B9%D0%BB', 'Email'),
	(75, 'Localization', '%D0%9B%D0%BE%D0%BA%D0%B0%D0%BB%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D1%8F', 'Localizaci%C3%B3n'),
	(76, 'Add', '%D0%94%D0%BE%D0%B1%D0%B0%D0%B2%D0%B8%D1%82%D1%8C', 'A%C3%B1adir'),
	(77, 'Glass', '%D0%A3%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%BA%D0%B0+%D0%B7%D0%B0%D1%8F%D0%B2%D0%BE%D0%BA', 'Instalaci%C3%B3n+de+aplicaciones'),
	(78, 'Orders', '%D0%9E%D1%80%D0%B4%D0%B5%D1%80%D0%B0', 'Ordenes'),
	(79, 'Read more', '%D0%A7%D0%B8%D1%82%D0%B0%D1%82%D1%8C+%D0%B4%D0%B0%D0%BB%D0%B5%D0%B5', 'Leer+m%C3%A1s'),
	(80, 'News', '%D0%9D%D0%BE%D0%B2%D0%BE%D1%81%D1%82%D0%B8', 'Noticias'),
	(81, 'Graph', '%D0%93%D1%80%D0%B0%D1%84%D0%B8%D0%BA', 'Gr%C3%A1fico'),
	(82, 'Terms', '%D0%A3%D1%81%D0%BB%D0%BE%D0%B2%D0%B8%D1%8F', 'Condiciones'),
	(83, 'Privacy', '%D0%9A%D0%BE%D0%BD%D1%84%D0%B8%D0%B4%D0%B5%D0%BD%D1%86%D0%B8%D0%B0%D0%BB%D1%8C%D0%BD%D0%BE%D1%81%D1%82%D1%8C', 'Intimidad'),
	(84, 'Unsubscribe', '%D0%9E%D1%82%D0%BF%D0%B8%D1%81%D0%B0%D1%82%D1%8C%D1%81%D1%8F', 'Cancelar+la+suscripci%C3%B3n'),
	(85, 'Contact Info', '%D0%9A%D0%BE%D0%BD%D1%82%D0%B0%D0%BA%D1%82%D1%8B', 'Contacto'),
	(86, 'Site', '%D0%A1%D0%B0%D0%B9%D1%82', 'Sitio'),
	(87, 'Email', '%D0%95%D0%BC%D1%8D%D0%B9%D0%BB', 'Email'),
	(88, 'Connect With Us', '%D0%9F%D1%80%D0%B8%D1%81%D0%BE%D0%B5%D0%B4%D0%B8%D0%BD%D0%B8%D1%82%D1%8C%D1%81%D1%8F', 'Conecte+con+Nosotros'),
	(89, 'To confirm your registration, please click on this link', '%D0%A7%D1%82%D0%BE%D0%B1%D1%8B+%D0%BF%D0%BE%D0%B4%D1%82%D0%B2%D0%B5%D1%80%D0%B4%D0%B8%D1%82%D1%8C+%D1%80%D0%B5%D0%B3%D0%B8%D1%81%D1%82%D1%80%D0%B0%D1%86%D0%B8%D1%8E%2C+%D0%BD%D0%B0%D0%B6%D0%BC%D0%B8%D1%82%D0%B5+%D0%BF%D0%BE+%D1%81%D1%81%D1%8B%D0%BB%D0%BA%D0%B5', 'Para+confirmar+su+inscripci%C3%B3n%2C+por+favor+haga+clic+en+este+enlace'),
	(90, 'Activate', '%D0%90%D0%BA%D1%82%D0%B8%D0%B2%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D1%82%D1%8C', 'Activar'),
	(91, 'Your login is', '%D0%92%D0%B0%D1%88+%D0%BB%D0%BE%D0%B3%D0%B8%D0%BD', 'Su+nombre+de+usuario+es+'),
	(92, 'Your password is', '%D0%92%D0%B0%D1%88+%D0%BF%D0%B0%D1%80%D0%BE%D0%BB%D1%8C', 'Su+contrase%C3%B1a+es+'),
	(93, 'you have registered on the Bitmonex website', '%D0%B2%D1%8B+%D0%B7%D0%B0%D1%80%D0%B5%D0%B3%D0%B8%D1%81%D1%82%D1%80%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BD%D1%8B+%D0%BD%D0%B0+%D0%B1%D0%B8%D1%80%D0%B6%D0%B5+Bitmonex++++++++++++++++++++++++++++++++', '++++++++++++++++++++++++++++++++++++usted+se+ha+registrado+en+el+sitio+web+Bitmonex++++++++++++++++++++++++++++++++'),
	(94, 'Hello', '%D0%97%D0%B4%D1%80%D0%B0%D0%B2%D1%81%D1%82%D0%B2%D1%83%D0%B9%D1%82%D0%B5', '%C2%A1Hola+'),
	(95, 'You have successfully registered! Please, enter into your email to confirm', '%D0%92%D1%8B+%D1%83%D1%81%D0%BF%D0%B5%D1%88%D0%BD%D0%BE+%D0%B7%D0%B0%D1%80%D0%B5%D0%B3%D0%B8%D1%81%D1%82%D1%80%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D0%BB%D0%B8%D1%81%D1%8C%21+%D0%9F%D0%BE%D0%B6%D0%B0%D0%BB%D1%83%D0%B9%D1%81%D1%82%D0%B0%2C+%D0%BF%D0%BE%D0%B4%D1%82%D0%B2%D0%B5%D1%80%D0%B4%D0%B8%D1%82%D0%B5+%D1%80%D0%B5%D0%B3%D0%B8%D1%81%D1%82%D1%80%D0%B0%D1%86%D0%B8%D1%8E+%D1%81+%D0%BF%D0%BE%D0%BC%D0%BE%D1%89%D1%8C%D1%8E+email', 'Has+registrado+correctamente%21+Por+favor%2C+entra+en+tu+correo+electr%C3%B3nico+para+confirmar');
/*!40000 ALTER TABLE `localization` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.news
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `news_id` int(11) NOT NULL,
  `lang` varchar(20) NOT NULL,
  `title` varchar(255) NOT NULL,
  `full` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.news: ~2 rows (приблизительно)
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` (`id`, `news_id`, `lang`, `title`, `full`, `date`) VALUES
	(1, 1, 'RU', '1004+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%B0+%D1%83%D0%BD%D0%B8%D1%87%D1%82%D0%BE%D0%B6%D0%B5%D0%BD%D1%8B+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D0%B0%D0%BC%D0%B8.\n', '%D0%A6%D0%B8%D1%84%D1%80%D1%8B%2C+%D0%BE%D0%BF%D1%83%D0%B1%D0%BB%D0%B8%D0%BA%D0%BE%D0%B2%D0%B0%D0%BD%D0%BD%D1%8B%D0%B5+%D0%BF%D1%80%D0%B0%D0%B2%D0%B8%D1%82%D0%B5%D0%BB%D1%8C%D1%81%D1%82%D0%B2%D0%BE%D0%BC+%D0%AE%D0%90%D0%A0+%D0%B2+%D0%BF%D1%8F%D1%82%D0%BD%D0%B8%D1%86%D1%83%2C+17+%D1%8F%D0%BD%D0%B2%D0%B0%D1%80%D1%8F%2C+%D0%BF%D1%80%D0%B5%D0%B2%D1%8B%D1%88%D0%B0%D1%8E%D1%82+%D0%B2%D1%81%D0%B5+%D1%85%D1%83%D0%B4%D1%88%D0%B8%D0%B5+%D0%BE%D0%BF%D0%B0%D1%81%D0%B5%D0%BD%D0%B8%D1%8F%3A+1004+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%B0+%D0%BF%D0%BE%D0%B3%D0%B8%D0%B1%D0%BB%D0%B8+%D0%BE%D1%82+%D1%80%D1%83%D0%BA+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D0%BE%D0%B2+%D0%B2+%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B5+%D0%B2+%D0%BF%D1%80%D0%BE%D1%88%D0%BB%D0%BE%D0%BC+%D0%B3%D0%BE%D0%B4%D1%83.+%D0%92+%D1%81%D1%80%D0%B5%D0%B4%D0%BD%D0%B5%D0%BC%2C+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D1%8B+%D1%83%D0%B1%D0%B8%D0%B2%D0%B0%D0%BB%D0%B8+%D0%B5%D0%B6%D0%B5%D0%B4%D0%BD%D0%B5%D0%B2%D0%BD%D0%BE+%D0%BF%D0%BE+%D1%82%D1%80%D0%B8+%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D1%85.%0A%0A%D0%92+2012+%D0%B3%D0%BE%D0%B4%D1%83+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D0%B0%D0%BC%D0%B8+%D0%B1%D1%8B%D0%BB%D0%B8+%D1%83%D0%B1%D0%B8%D1%82%D1%8B+668+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%BE%D0%B2.+%D0%A7%D0%B8%D1%81%D0%BB%D0%B5%D0%BD%D0%BD%D0%BE%D1%81%D1%82%D1%8C++%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D1%85+%D0%BF%D1%80%D0%B8%D0%B1%D0%BB%D0%B8%D0%B6%D0%B0%D0%B5%D1%82%D1%81%D1%8F+%D0%BA+%D0%BA%D1%80%D0%B8%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%BC%D1%83+%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D1%8E%2C+%D1%82%D0%B0%D0%BA+%D0%BA%D0%B0%D0%BA+%D1%87%D0%B8%D1%81%D0%BB%D0%BE+%D1%83%D0%B1%D0%B8%D1%82%D1%8B%D1%85+%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D1%85+%D0%B1%D0%BE%D0%BB%D1%8C%D1%88%D0%B5%2C+%D1%87%D0%B5%D0%BC+%D0%BD%D0%BE%D0%B2%D0%BE%D1%80%D0%BE%D0%B6%D0%B4%D1%91%D0%BD%D0%BD%D1%8B%D1%85.++%0A%0A%C2%AB%D0%92+2014+%D0%B3%D0%BE%D0%B4%D1%83%2C+%D0%BD%D0%B0%D0%BA%D0%BE%D0%BD%D0%B5%D1%86%2C+%D1%81%D0%B8%D1%82%D1%83%D0%B0%D1%86%D0%B8%D1%8F+%D0%B4%D0%BE%D0%BB%D0%B6%D0%BD%D0%B0+%D0%B8%D0%B7%D0%BC%D0%B5%D0%BD%D0%B8%D1%82%D1%8C%D1%81%D1%8F%C2%BB%2C+%E2%80%94+%D0%B7%D0%B0%D1%8F%D0%B2%D0%B8%D0%BB+%D0%A2%D0%BE%D0%BC+%D0%9C%D0%B8%D0%BB%D0%BB%D0%B8%D0%BA%D0%B5%D0%BD%2C+%D0%BF%D1%80%D0%B5%D0%B4%D1%81%D1%82%D0%B0%D0%B2%D0%B8%D1%82%D0%B5%D0%BB%D1%8C+%D0%BF%D1%80%D0%B8%D1%80%D0%BE%D0%B4%D0%BE%D0%BE%D1%85%D1%80%D0%B0%D0%BD%D0%BD%D0%BE%D0%B9+%D0%BE%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8+Traffic.+%D0%AE%D0%90%D0%A0+%D0%B8+%D1%81%D0%BE%D1%81%D0%B5%D0%B4%D0%BD%D1%8F%D1%8F+%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B0+%D0%9C%D0%BE%D0%B7%D0%B0%D0%BC%D0%B1%D0%B8%D0%BA%2C+%D0%BA%D0%BE%D1%82%D0%BE%D1%80%D1%8B%D0%B5+%D1%81%D0%BB%D1%83%D0%B6%D0%B0%D1%82+%D0%B1%D0%B0%D0%B7%D0%BE%D0%B9+%D0%B4%D0%BB%D1%8F+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D0%BE%D0%B2%2C+%D0%B4%D0%BE%D0%BB%D0%B6%D0%BD%D1%8B+%D0%B0%D0%BA%D1%82%D0%B8%D0%B2%D0%B8%D0%B7%D0%B8%D1%80%D0%BE%D0%B2%D0%B0%D1%82%D1%8C+%D1%81%D0%B2%D0%BE%D0%B8+%D1%83%D1%81%D0%B8%D0%BB%D0%B8%D1%8F+%D0%B8+%D0%BE%D1%81%D1%82%D0%B0%D0%BD%D0%BE%D0%B2%D0%B8%D1%82%D1%8C+%D1%80%D0%BE%D1%81%D1%82+%D0%BF%D1%80%D0%B5%D1%81%D1%82%D1%83%D0%BF%D0%BB%D0%B5%D0%BD%D0%B8%D0%B9.%0A%0A%D0%91%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D1%8B+%D0%BF%D0%BE%D0%BB%D1%83%D1%87%D0%B0%D1%8E%D1%82+%D0%BE%D0%B3%D1%80%D0%BE%D0%BC%D0%BD%D1%83%D1%8E+%D0%BF%D1%80%D0%B8%D0%B1%D1%8B%D0%BB%D1%8C+%D0%BE%D1%82+%D0%BF%D1%80%D0%BE%D0%B4%D0%B0%D0%B6%D0%B8+%D1%80%D0%BE%D0%B3%D0%B0+%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D1%85.+%D0%A5%D0%BE%D1%80%D0%BE%D1%88%D0%BE+%D0%BE%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%BE%D0%B2%D0%B0%D0%BD%D0%BD%D1%8B%D0%B5+%D0%B1%D0%B0%D0%BD%D0%B4%D1%8B+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D0%BE%D0%B2+%D0%B7%D0%B0%D0%BA%D0%BB%D1%8E%D1%87%D0%B0%D1%8E%D1%82+%D1%81%D0%B4%D0%B5%D0%BB%D0%BA%D0%B8+%D0%BD%D0%B0+%D1%81%D1%83%D0%BC%D0%BC%D1%8B+%D0%B2+%D0%BD%D0%B5%D1%81%D0%BA%D0%BE%D0%BB%D1%8C%D0%BA%D0%BE+%D1%81%D0%BE%D1%82%D0%B5%D0%BD+%D0%BC%D0%B8%D0%BB%D0%BB%D0%B8%D0%BE%D0%BD%D0%BE%D0%B2+%D0%B5%D0%B2%D1%80%D0%BE%2C+%D1%81%D0%BE%D0%BE%D0%B1%D1%89%D0%B0%D0%B5%D1%82+%D0%94%D0%B6%D0%B5%D0%B9%D1%81%D0%BE%D0%BD+%D0%91%D0%B5%D0%BB%D0%BB%2C+%D0%B4%D0%B8%D1%80%D0%B5%D0%BA%D1%82%D0%BE%D1%80+%D1%8E%D0%B6%D0%BD%D0%BE%D0%B0%D1%84%D1%80%D0%B8%D0%BA%D0%B0%D0%BD%D1%81%D0%BA%D0%BE%D0%B9+%D0%BF%D1%80%D0%B8%D1%80%D0%BE%D0%B4%D0%BE%D0%BE%D1%85%D1%80%D0%B0%D0%BD%D0%BD%D0%BE%D0%B9+%D0%BE%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8+IFAW.%0A%0A%D0%9F%D0%BE+%D0%B4%D0%B0%D0%BD%D0%BD%D1%8B%D0%BC+%D0%B2%D1%8C%D0%B5%D1%82%D0%BD%D0%B0%D0%BC%D1%81%D0%BA%D0%BE%D0%B3%D0%BE+%D0%BE%D1%82%D0%B4%D0%B5%D0%BB%D0%B5%D0%BD%D0%B8%D1%8F+%D0%92%D1%81%D0%B5%D0%BC%D0%B8%D1%80%D0%BD%D0%BE%D0%B3%D0%BE+%D1%84%D0%BE%D0%BD%D0%B4%D0%B0+%D0%B4%D0%B8%D0%BA%D0%BE%D0%B9+%D0%BF%D1%80%D0%B8%D1%80%D0%BE%D0%B4%D1%8B+WWF%2C+%D1%81%D0%BF%D1%80%D0%BE%D1%81+%D0%BD%D0%B0+%D1%80%D0%BE%D0%B3+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%B0+%D0%B2%D1%81%D1%91+%D0%B5%D1%89%D1%91+%D0%BD%D0%B0%D0%BC%D0%BD%D0%BE%D0%B3%D0%BE+%D0%BF%D1%80%D0%B5%D0%B2%D1%8B%D1%88%D0%B0%D0%B5%D1%82+%D0%BF%D1%80%D0%B5%D0%B4%D0%BB%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%2C+%D0%BD%D0%B5%D1%81%D0%BC%D0%BE%D1%82%D1%80%D1%8F+%D0%BD%D0%B0+%D0%B2%D1%8B%D1%81%D0%BE%D0%BA%D1%83%D1%8E+%D1%86%D0%B5%D0%BD%D1%83+%D0%B2+50+000+%D0%B5%D0%B2%D1%80%D0%BE+%D0%B7%D0%B0+%D0%BA%D0%B8%D0%BB%D0%BE%D0%B3%D1%80%D0%B0%D0%BC%D0%BC+%D0%BE%D0%B1%D1%80%D0%B0%D0%B1%D0%BE%D1%82%D0%B0%D0%BD%D0%BD%D0%BE%D0%B3%D0%BE+%D1%81%D1%8B%D1%80%D1%8C%D1%8F.+%D0%9E%D1%81%D0%BD%D0%BE%D0%B2%D0%BD%D1%8B%D0%BC%D0%B8+%D0%BF%D0%BE%D0%BA%D1%83%D0%BF%D0%B0%D1%82%D0%B5%D0%BB%D1%8F%D0%BC%D0%B8+%D1%80%D0%BE%D0%B3%D0%B0+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%B0+%D1%8F%D0%B2%D0%BB%D1%8F%D1%8E%D1%82%D1%81%D1%8F+%D1%82%D0%B0%D0%BA%D0%B8%D0%B5+%D1%81%D1%82%D1%80%D0%B0%D0%BD%D1%8B%2C+%D0%BA%D0%B0%D0%BA+%D0%9A%D0%B8%D1%82%D0%B0%D0%B9%2C+%D0%92%D1%8C%D0%B5%D1%82%D0%BD%D0%B0%D0%BC%2C+%D0%9B%D0%B0%D0%BE%D1%81+%D0%B8+%D0%A2%D0%B0%D0%B8%D0%BB%D0%B0%D0%BD%D0%B4.+%D0%98%D0%BC%D0%B5%D0%BD%D0%BD%D0%BE+%D1%8D%D1%82%D0%B8+%D0%B3%D0%BE%D1%81%D1%83%D0%B4%D0%B0%D1%80%D1%81%D1%82%D0%B2%D0%B0+%D1%81%D0%BB%D0%B8%D1%88%D0%BA%D0%BE%D0%BC+%D0%BF%D0%B0%D1%81%D1%81%D0%B8%D0%B2%D0%BD%D1%8B+%D0%B2+%D0%B1%D0%BE%D1%80%D1%8C%D0%B1%D0%B5+%D1%81+%D0%BD%D0%B5%D0%B7%D0%B0%D0%BA%D0%BE%D0%BD%D0%BD%D0%BE%D0%B9+%D1%82%D0%BE%D1%80%D0%B3%D0%BE%D0%B2%D0%BB%D0%B5%D0%B9%2C+%D1%81%D1%87%D0%B8%D1%82%D0%B0%D0%B5%D1%82+%D1%8D%D0%BA%D0%BE%D0%BB%D0%BE%D0%B3+%D0%9C%D0%B8%D0%BB%D0%BB%D0%B8%D0%BA%D0%B5%D0%BD.+%D0%A1%D0%BB%D0%BE%D0%BD%D0%BE%D0%B2%D0%B0%D1%8F+%D0%BA%D0%BE%D1%81%D1%82%D1%8C%2C+%D1%80%D0%BE%D0%B3+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%B0+%D0%B8%D0%BB%D0%B8+%D0%BA%D0%BE%D1%81%D1%82%D0%B8+%D0%BB%D1%8C%D0%B2%D0%B0+%D1%86%D0%B5%D0%BD%D1%8F%D1%82%D1%81%D1%8F+%D0%B2+%D0%90%D0%B7%D0%B8%D0%B8+%D0%BA%D0%B0%D0%BA+%D0%B8%D0%BD%D0%B3%D1%80%D0%B5%D0%B4%D0%B8%D0%B5%D0%BD%D1%82%D1%8B+%D0%B4%D0%BB%D1%8F+%D0%BB%D0%B5%D0%BA%D0%B0%D1%80%D1%81%D1%82%D0%B2.+%D0%A1%D1%82%D1%80%D0%BE%D0%B3%D0%B8%D0%B5+%D1%82%D1%80%D0%B5%D0%B1%D0%BE%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F+%D0%A1%D0%98%D0%A2%D0%95%D0%A1+-+%D0%BA%D0%BE%D0%BD%D0%B2%D0%B5%D0%BD%D1%86%D0%B8%D0%B8+%D0%BE+%D0%BC%D0%B5%D0%B6%D0%B4%D1%83%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%BE%D0%B9+%D1%82%D0%BE%D1%80%D0%B3%D0%BE%D0%B2%D0%BB%D0%B5+%D0%B2%D0%B8%D0%B4%D0%B0%D0%BC%D0%B8+%D0%B4%D0%B8%D0%BA%D0%BE%D0%B9+%D1%84%D0%B0%D1%83%D0%BD%D1%8B+%D0%B8+%D1%84%D0%BB%D0%BE%D1%80%D1%8B%2C+%D0%BD%D0%B0%D1%85%D0%BE%D0%B4%D1%8F%D1%89%D0%B8%D1%85%D1%81%D1%8F+%D0%BF%D0%BE%D0%B4+%D1%83%D0%B3%D1%80%D0%BE%D0%B7%D0%BE%D0%B9+%D1%83%D0%BD%D0%B8%D1%87%D1%82%D0%BE%D0%B6%D0%B5%D0%BD%D0%B8%D1%8F%2C+%D0%BD%D0%B5+%D1%81%D0%BE%D0%B1%D0%BB%D1%8E%D0%B4%D0%B0%D1%8E%D1%82%D1%81%D1%8F+%D0%B2+%D1%8D%D1%82%D0%B8%D1%85+%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B0%D1%85.%0A%0A%D0%91%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D1%81%D1%82%D0%B2%D0%BE+%D1%8F%D0%B2%D0%BB%D1%8F%D0%B5%D1%82%D1%81%D1%8F+%D1%83%D0%B3%D1%80%D0%BE%D0%B7%D0%BE%D0%B9+%D0%B2+%D0%B4%D0%BE%D0%BB%D0%B3%D0%BE%D1%81%D1%80%D0%BE%D1%87%D0%BD%D0%BE%D0%B9+%D0%BF%D0%B5%D1%80%D1%81%D0%BF%D0%B5%D0%BA%D1%82%D0%B8%D0%B2%D0%B5+%D0%B8+%D1%81%D0%B5%D1%80%D1%8C%D1%91%D0%B7%D0%BD%D0%BE+%D0%BF%D0%BE%D0%B4%D1%80%D1%8B%D0%B2%D0%B0%D0%B5%D1%82+%D0%BF%D0%BE%D0%BF%D1%83%D0%BB%D1%8F%D1%86%D0%B8%D0%B8+%D0%B8%D0%B7+450+%D1%82%D1%8B%D1%81%D1%8F%D1%87+%D1%81%D0%BB%D0%BE%D0%BD%D0%BE%D0%B2+%D0%B8+25+000+%D0%BD%D0%BE%D1%81%D0%BE%D1%80%D0%BE%D0%B3%D0%BE%D0%B2+%D0%B2+%D0%90%D1%84%D1%80%D0%B8%D0%BA%D0%B5.++%0A%0A%D0%91%D0%BE%D1%80%D1%86%D1%8B+%D0%B7%D0%B0+%D0%BF%D1%80%D0%B0%D0%B2%D0%B0+%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D1%85+%D0%B8%D0%B7+%D0%BE%D1%80%D0%B3%D0%B0%D0%BD%D0%B8%D0%B7%D0%B0%D1%86%D0%B8%D0%B8+Traffic+%D0%BD%D0%B0%D0%B4%D0%B5%D1%8E%D1%82%D1%81%D1%8F%2C+%D1%87%D1%82%D0%BE+%D0%BD%D0%B0+%D0%9C%D0%B5%D0%B6%D0%B4%D1%83%D0%BD%D0%B0%D1%80%D0%BE%D0%B4%D0%BD%D0%BE%D0%B9+%D0%BA%D0%BE%D0%BD%D1%84%D0%B5%D1%80%D0%B5%D0%BD%D1%86%D0%B8%D0%B8+%D0%BF%D0%BE+%D1%82%D0%BE%D1%80%D0%B3%D0%BE%D0%B2%D0%BB%D0%B5+%D0%B4%D0%B8%D0%BA%D0%B8%D0%BC%D0%B8+%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D0%BC%D0%B8%2C+%D0%BA%D0%BE%D1%82%D0%BE%D1%80%D0%B0%D1%8F+%D0%BD%D0%B0%D1%87%D0%BD%D1%91%D1%82%D1%81%D1%8F+14+%D1%84%D0%B5%D0%B2%D1%80%D0%B0%D0%BB%D1%8F+%D0%B2+%D0%9B%D0%BE%D0%BD%D0%B4%D0%BE%D0%BD%D0%B5%2C+%D0%BC%D0%BE%D0%B6%D0%BD%D0%BE+%D0%B1%D1%83%D0%B4%D0%B5%D1%82+%D1%80%D0%B5%D1%88%D0%B8%D1%82%D1%8C+%D0%B2%D0%BE%D0%BF%D1%80%D0%BE%D1%81%D1%8B+%D1%81+%D0%B1%D1%80%D0%B0%D0%BA%D0%BE%D0%BD%D1%8C%D0%B5%D1%80%D1%81%D1%82%D0%B2%D0%BE%D0%BC+%D0%B2+%D0%90%D1%84%D1%80%D0%B8%D0%BA%D0%B5.+%D0%9E%D0%B6%D0%B8%D0%B4%D0%B0%D0%B5%D1%82%D1%81%D1%8F%2C+%D1%87%D1%82%D0%BE+%D0%B2+%D1%84%D0%BE%D1%80%D1%83%D0%BC%D0%B5+%D0%BF%D1%80%D0%B8%D0%BC%D1%83%D1%82+%D1%83%D1%87%D0%B0%D1%81%D1%82%D0%B8%D0%B5+%D0%B3%D0%BB%D0%B0%D0%B2%D1%8B+%D0%B3%D0%BE%D1%81%D1%83%D0%B4%D0%B0%D1%80%D1%81%D1%82%D0%B2+%D0%B8+%D0%BC%D0%B8%D0%BD%D0%B8%D1%81%D1%82%D1%80%D1%8B+%D0%B8%D0%BD%D0%BE%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%BD%D1%8B%D1%85+%D0%B4%D0%B5%D0%BB+%D0%B8%D0%B7+50+%D1%81%D1%82%D1%80%D0%B0%D0%BD%2C+%D0%B2+%D1%82%D0%BE%D0%BC+%D1%87%D0%B8%D1%81%D0%BB%D0%B5+%D0%B8%D0%B7+%D1%82%D0%B5%D1%85%2C+%D0%BA%D0%BE%D1%82%D0%BE%D1%80%D1%8B%D0%B5+%D0%BE%D1%82%D0%B2%D0%B5%D1%87%D0%B0%D1%8E%D1%82+%D0%B7%D0%B0+%D0%BD%D0%B5%D0%B7%D0%B0%D0%BA%D0%BE%D0%BD%D0%BD%D1%83%D1%8E+%D1%82%D0%BE%D1%80%D0%B3%D0%BE%D0%B2%D0%BB%D1%8E+%D0%B8+%D1%83%D0%B1%D0%B8%D0%B9%D1%81%D1%82%D0%B2%D0%B0+%D0%B6%D0%B8%D0%B2%D0%BE%D1%82%D0%BD%D1%8B%D1%85.\n', '2014-02-02 00:00:00'),
	(2, 1, 'EN', '1004 rhino killed by poachers.', 'Figures released by the Government of South Africa on Friday, January 17 , to exceed all the worst fears : 1004 rhino killed by poachers in the country last year. On average , poachers killed every three animals.\n\nIn 2012, poachers killed 668 rhinos . The number of animals approaches the critical value , as the number of animals killed more than newborns.\n\n" In 2014 , finally , the situation should change ," - said Tom Milliken , a spokesman for the environmental organization Traffic. South Africa and neighboring countries of Mozambique , which serve as the basis for poachers should strengthen its efforts to stop the growth of crime.\n\nPoachers reap huge profits from the sale of animal horns . Well- organized gangs of poachers are making deals worth several hundred million euros , according to Jason Bell , director of the South African environmental organization IFAW.\n\nAccording to the Vietnamese branch of the World Wildlife Fund, WWF, the demand for rhino horn is still far exceeds supply , despite the high price of 50 000 euros per kilo of processed raw materials. The main buyers of rhino horns are countries such as China, Vietnam, Laos and Thailand . That these states are too passive in the fight against illegal trade, according to environmental Milliken . Ivory, rhino horn or bone lion prized in Asia as ingredients for medicines. Stringent requirements of CITES - Convention on International Trade in Endangered Species of Wild Fauna and Flora , endangered , not observed in these countries.\n\nPoaching is a threat in the long run and seriously undermines population of 450,000 elephants and 25,000 rhinos in Africa.\n\nAnimal rights activists from the organization Traffic hope that the International Conference on wildlife trade , which will begin on February 14 in London, it will be possible to resolve issues with poaching in Africa. It is expected that the forum will be attended by heads of state and foreign ministers from 50 countries , including from those who are responsible for trafficking and killing of animals .', '2014-02-02 07:00:00'),
	(3, 1, 'ES', '1004+rinocerontes+muertos+por+los+cazadores+furtivos.', 'Las+cifras+publicadas+por+el+Gobierno+de+Sud%C3%A1frica+el+viernes+%2C+17+de+enero+exceder+los+peores+temores+%3A+1.004+rinocerontes+asesinados+por+cazadores+furtivos+en+el+pa%C3%ADs+el+a%C3%B1o+pasado.+En+promedio+%2C+los+cazadores+furtivos+mataron+cada+tres+animales.%0A%0AEn+2012+%2C+los+cazadores+furtivos+mataron+a+668+rinocerontes.+El+n%C3%BAmero+de+animales+se+acerca+al+valor+cr%C3%ADtico+%2C+como+el+n%C3%BAmero+de+animales+mat%C3%B3+a+m%C3%A1s+de+los+reci%C3%A9n+nacidos+.%0A%0A%22En+2014+%2C+finalmente+%2C+la+situaci%C3%B3n+debe+cambiar+%2C%22+-+dijo+Tom+Milliken+%2C+un+portavoz+de+la+organizaci%C3%B3n+ecologista+Traffic+.+Sud%C3%A1frica+y+los+pa%C3%ADses+vecinos+de+Mozambique+%2C+que+sirven+como+base+para+los+cazadores+furtivos+deben+fortalecer+sus+esfuerzos+para+detener+el+crecimiento+de+la+delincuencia+.%0A%0ALos+cazadores+furtivos+obtienen+enormes+ganancias+de+la+venta+de+cuernos+de+animales+.+Bandas+bien+organizadas+de+cazadores+furtivos+est%C3%A1n+haciendo+ofertas+por+valor+de+varios+cientos+de+millones+de+euros%2C+seg%C3%BAn+Jason+Bell%2C+director+de+la+organizaci%C3%B3n+ambientalista+sudafricana+IFAW+.%0A%0ADe+acuerdo+con+la+rama+vietnamita+del+Fondo+Mundial+para+la+Naturaleza+%2C+WWF+%2C+la+demanda+de+cuerno+de+rinoceronte+est%C3%A1+a%C3%BAn+lejos+supera+a+la+oferta+%2C+a+pesar+del+alto+precio+de+50+000+euros+por+kilo+de+materia+prima+procesada+.+Los+principales+compradores+de+cuernos+de+rinoceronte+son+pa%C3%ADses+como+China+%2C+Vietnam+%2C+Laos+y+Tailandia.+Que+estos+estados+son+demasiado+pasiva+en+la+lucha+contra+el+comercio+ilegal%2C+seg%C3%BAn+Milliken+ambiental.+Marfil+%2C+cuernos+de+rinoceronte+o+hueso+le%C3%B3n+apreciado+en+Asia+como+ingredientes+para+medicamentos.+Estrictos+requisitos+de+la+CITES+-+Convenci%C3%B3n+sobre+el+Comercio+Internacional+de+Especies+Amenazadas+de+Fauna+y+Flora+Silvestres+%2C+en+peligro+de+extinci%C3%B3n+%2C+no+se+ha+observado+en+estos+pa%C3%ADses.%0A%0ALa+caza+furtiva+es+una+amenaza+a+largo+plazo+y+socava+seriamente+la+poblaci%C3%B3n+de+450.000+elefantes+y+rinocerontes+en+%C3%81frica+25.000+.%0A%0ADefensores+de+los+animales+de+la+organizaci%C3%B3n+de+tr%C3%A1fico+esperan+que+la+Conferencia+Internacional+sobre+el+comercio+de+vida+silvestre+%2C+que+se+iniciar%C3%A1+el+14+de+febrero+en+Londres+%2C+ser%C3%A1+posible+resolver+los+problemas+con+la+caza+furtiva+en+%C3%81frica.+Se+espera+que+el+foro+contar%C3%A1+con+la+presencia+de+los+jefes+de+Estado+y+cancilleres+de+50+pa%C3%ADses%2C+incluyendo+a+aquellos+que+son+responsables+de+la+trata+y+la+matanza+de+animales+.', '2014-02-02 09:14:09');
/*!40000 ALTER TABLE `news` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.order
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `RateId` int(11) NOT NULL,
  `Type` tinyint(1) NOT NULL COMMENT '0 - buy, 1 - sell',
  `Price` double NOT NULL,
  `Volume` double NOT NULL,
  `Date` datetime NOT NULL,
  `Part` double NOT NULL COMMENT 'from 0 to 1',
  `Status` tinyint(1) NOT NULL COMMENT '0 - active, 1 - done, 2 - partially done, 3 - cancelled',
  PRIMARY KEY (`id`),
  KEY `FK_order_user_id` (`UID`),
  KEY `FK_order_rate_id` (`RateId`),
  CONSTRAINT `FK_Order_Rate_id` FOREIGN KEY (`RateId`) REFERENCES `rate` (`id`),
  CONSTRAINT `FK_Order_User_id` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.order: ~20 rows (приблизительно)
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` (`id`, `UID`, `RateId`, `Type`, `Price`, `Volume`, `Date`, `Part`, `Status`) VALUES
	(112, 2, 1, 1, 816.5, 1, '2014-01-28 02:13:19', 1, 1),
	(113, 2, 1, 0, 816.5, 2, '2014-01-28 02:13:49', 1, 1),
	(114, 2, 1, 1, 816.5, 1, '2014-01-28 02:13:49', 1, 1),
	(115, 2, 1, 1, 820.5, 2, '2014-01-30 00:37:24', 0.5, 2),
	(116, 2, 1, 0, 820.5, 1, '2014-01-28 02:15:22', 1, 1),
	(117, 2, 11, 1, 0.02734, 5, '2014-02-20 11:28:48', 0, 0),
	(118, 2, 11, 1, 0.02734, 5, '2014-02-21 10:20:46', 0, 3),
	(119, 2, 11, 1, 0.02734, 5, '2014-02-21 10:20:46', 0, 3),
	(120, 2, 11, 1, 0.02734, 3, '2014-02-21 10:20:45', 0, 3),
	(121, 2, 11, 1, 0.02734, 2, '2014-02-21 10:20:44', 0, 3),
	(122, 2, 11, 1, 0.02734, 1, '2014-02-21 10:20:43', 0, 3),
	(123, 2, 11, 1, 0.02734, 0.05, '2014-02-21 10:20:40', 0, 3),
	(124, 2, 11, 1, 0.02734, 5, '2014-02-21 10:21:30', 0, 0),
	(125, 2, 11, 1, 0.02734, 5, '2014-02-21 10:21:32', 0, 0),
	(126, 2, 11, 1, 0.02734, 5, '2014-02-21 10:21:34', 0, 0),
	(127, 2, 11, 1, 0.02734, 4, '2014-02-21 10:21:36', 0, 0),
	(128, 2, 11, 1, 0.02734, 6, '2014-02-21 10:21:38', 0, 0),
	(129, 2, 11, 1, 0.02734, 9, '2014-02-21 10:21:40', 0, 0),
	(130, 2, 11, 1, 0.02734, 5, '2014-02-21 10:21:41', 0, 0),
	(131, 2, 11, 1, 0.02734, 100, '2014-02-21 10:23:02', 0, 0);
/*!40000 ALTER TABLE `order` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.order_priority
CREATE TABLE IF NOT EXISTS `order_priority` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `priority` int(11) NOT NULL,
  `from` int(11) NOT NULL,
  `to` int(11) NOT NULL,
  `color` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.order_priority: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `order_priority` DISABLE KEYS */;
INSERT INTO `order_priority` (`id`, `priority`, `from`, `to`, `color`) VALUES
	(1, 1, 0, 25, '#f0f0f0'),
	(2, 2, 25, 50, '#dddddd'),
	(3, 3, 50, 75, '#aaaaaa'),
	(4, 4, 75, 100, '#666666');
/*!40000 ALTER TABLE `order_priority` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.payment_system
CREATE TABLE IF NOT EXISTS `payment_system` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `trade_name` varchar(50) COLLATE utf8_bin NOT NULL,
  `URL` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.payment_system: ~6 rows (приблизительно)
/*!40000 ALTER TABLE `payment_system` DISABLE KEYS */;
INSERT INTO `payment_system` (`id`, `name`, `trade_name`, `URL`) VALUES
	(1, 'BTC', 'Bitcoin', '/money/BTC_transaction'),
	(2, 'PM', 'Perfect Money', '/money/PM_transaction'),
	(3, 'YM', 'Yandex Money', '/money/YM_transaction'),
	(4, 'LTC', 'Litecoin', '/money/LTC_transaction'),
	(5, 'OKP', 'OKPay', '/money/OKP_transaction'),
	(7, 'EGOP', 'EGOPay', '/money/EGOP_transaction');
/*!40000 ALTER TABLE `payment_system` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.purse
CREATE TABLE IF NOT EXISTS `purse` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `CurId` int(11) NOT NULL,
  `Value` double NOT NULL,
  `UID` int(11) NOT NULL,
  `Additional_ID` text,
  PRIMARY KEY (`id`),
  KEY `FK_Purse_Currency_id` (`CurId`),
  KEY `FK_Purse_User_id` (`UID`),
  CONSTRAINT `FK_Purse_Currency_id` FOREIGN KEY (`CurId`) REFERENCES `currency` (`id`),
  CONSTRAINT `FK_Purse_User_id` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.purse: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `purse` DISABLE KEYS */;
INSERT INTO `purse` (`id`, `CurId`, `Value`, `UID`, `Additional_ID`) VALUES
	(1, 1, 854, 2, NULL),
	(2, 2, 11, 2, NULL),
	(3, 3, 0, 2, NULL),
	(4, 4, 0, 2, NULL),
	(5, 5, 0, 2, NULL);
/*!40000 ALTER TABLE `purse` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.rate
CREATE TABLE IF NOT EXISTS `rate` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `FirstId` int(11) NOT NULL,
  `SecondId` int(11) NOT NULL,
  `Bid` double NOT NULL,
  `Ask` double NOT NULL,
  `Fee` double NOT NULL,
  `MinPriceDifference` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_Rate_Currency_id` (`FirstId`),
  KEY `FK_Rate_Currency_id1` (`SecondId`),
  CONSTRAINT `FK_Rate_Currency_id` FOREIGN KEY (`FirstId`) REFERENCES `currency` (`id`),
  CONSTRAINT `FK_Rate_Currency_id1` FOREIGN KEY (`SecondId`) REFERENCES `currency` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.rate: ~4 rows (приблизительно)
/*!40000 ALTER TABLE `rate` DISABLE KEYS */;
INSERT INTO `rate` (`id`, `FirstId`, `SecondId`, `Bid`, `Ask`, `Fee`, `MinPriceDifference`) VALUES
	(1, 1, 2, 816.5, 820.5, 0, 0),
	(2, 1, 3, 615.3, 619, 0, 0),
	(3, 1, 4, 28449, 28500, 0, 0),
	(11, 1, 5, 0.02734, 0.02734, 0.001, 0);
/*!40000 ALTER TABLE `rate` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.session
CREATE TABLE IF NOT EXISTS `session` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` text NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `UID_session` (`user_id`),
  CONSTRAINT `UID_session` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=150 DEFAULT CHARSET=utf8;


-- Дамп структуры для таблица emonex.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `Login` varchar(100) NOT NULL,
  `PassHash` varchar(255) NOT NULL,
  `Email` varchar(50) NOT NULL,
  `Date` datetime DEFAULT NULL,
  `Activation` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Login` (`Login`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


-- Дамп структуры для таблица emonex.user_widgets
CREATE TABLE IF NOT EXISTS `user_widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `UID` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `page` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `UID_user_widgets` (`UID`),
  KEY `WIDGETS_id` (`widget_id`),
  CONSTRAINT `WIDGETS_id` FOREIGN KEY (`widget_id`) REFERENCES `widgets` (`id`),
  CONSTRAINT `UID_user_widgets` FOREIGN KEY (`UID`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.user_widgets: ~20 rows (приблизительно)
/*!40000 ALTER TABLE `user_widgets` DISABLE KEYS */;
INSERT INTO `user_widgets` (`id`, `UID`, `widget_id`, `priority`, `rate`, `page`) VALUES
	(48, 2, 1, 2, 1, 0),
	(49, 2, 2, 3, 1, 0),
	(62, 2, 1, 2, 11, 1),
	(63, 2, 2, 3, 11, 1),
	(64, 2, 3, 4, 11, 1),
	(65, 2, 4, 5, 11, 1),
	(72, 2, 5, 1, 1, 0),
	(74, 2, 5, 1, 11, 1),
	(75, 2, 1, 2, 2, 2),
	(76, 2, 2, 3, 2, 2),
	(77, 2, 3, 4, 2, 2),
	(78, 2, 5, 1, 2, 2),
	(79, 2, 4, 5, 2, 2),
	(80, 2, 5, 1, 3, 3),
	(81, 2, 1, 2, 3, 3),
	(82, 2, 2, 3, 3, 3),
	(83, 2, 3, 4, 3, 3),
	(84, 2, 4, 5, 3, 3),
	(85, 2, 3, 4, 1, 0),
	(86, 2, 4, 5, 1, 0);
/*!40000 ALTER TABLE `user_widgets` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.wallets_btc
CREATE TABLE IF NOT EXISTS `wallets_btc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) COLLATE utf8_bin NOT NULL,
  `value` double NOT NULL,
  `share` double NOT NULL,
  `profit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.wallets_btc: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `wallets_btc` DISABLE KEYS */;
INSERT INTO `wallets_btc` (`id`, `account`, `value`, `share`, `profit`) VALUES
	(1, 'romashka', 0, 0.05, 0);
/*!40000 ALTER TABLE `wallets_btc` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.wallets_egop
CREATE TABLE IF NOT EXISTS `wallets_egop` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_id` int(11) NOT NULL,
  `email` varchar(50) COLLATE utf8_bin NOT NULL,
  `api_id` varchar(255) COLLATE utf8_bin NOT NULL,
  `api_password` varchar(255) COLLATE utf8_bin NOT NULL,
  `store_id` varchar(255) COLLATE utf8_bin NOT NULL,
  `store_password` varchar(255) COLLATE utf8_bin NOT NULL,
  `checksum_key` varchar(255) COLLATE utf8_bin NOT NULL,
  `value` double NOT NULL,
  `share` double NOT NULL,
  `profit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_wallets_egop_currency_id` (`currency_id`),
  CONSTRAINT `FK_wallets_egop_currency_id` FOREIGN KEY (`currency_id`) REFERENCES `currency` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп структуры для таблица emonex.wallets_ltc
CREATE TABLE IF NOT EXISTS `wallets_ltc` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account` varchar(50) COLLATE utf8_bin NOT NULL,
  `value` double NOT NULL,
  `share` double NOT NULL,
  `profit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- Дамп данных таблицы emonex.wallets_ltc: ~1 rows (приблизительно)
/*!40000 ALTER TABLE `wallets_ltc` DISABLE KEYS */;
INSERT INTO `wallets_ltc` (`id`, `account`, `value`, `share`, `profit`) VALUES
	(1, 'bitmonex_1', 0, 0.005, 0);
/*!40000 ALTER TABLE `wallets_ltc` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.wallets_okp
CREATE TABLE IF NOT EXISTS `wallets_okp` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `wallet_id` char(24) COLLATE utf8_bin NOT NULL,
  `api_password` varchar(255) COLLATE utf8_bin NOT NULL,
  `currency` char(3) COLLATE utf8_bin NOT NULL,
  `value` double NOT NULL,
  `share` double NOT NULL,
  `profit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Дамп структуры для таблица emonex.wallets_pm
CREATE TABLE IF NOT EXISTS `wallets_pm` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `pass_phrase` varchar(50) COLLATE utf8_bin NOT NULL,
  `alternate_pass_phrase` varchar(50) COLLATE utf8_bin DEFAULT NULL,
  `units` char(3) COLLATE utf8_bin NOT NULL,
  `account` varchar(10) COLLATE utf8_bin NOT NULL,
  `value` double NOT NULL,
  `share` double NOT NULL,
  `profit` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


-- Дамп структуры для таблица emonex.wallets_ym
CREATE TABLE IF NOT EXISTS `wallets_ym` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `number` varchar(255) NOT NULL,
  `client_id` varchar(255) NOT NULL,
  `secret_id` varchar(255) NOT NULL,
  `token` text NOT NULL,
  `value` double unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.wallets_ym: ~0 rows (приблизительно)
/*!40000 ALTER TABLE `wallets_ym` DISABLE KEYS */;
/*!40000 ALTER TABLE `wallets_ym` ENABLE KEYS */;


-- Дамп структуры для таблица emonex.widgets
CREATE TABLE IF NOT EXISTS `widgets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `widget_name` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `official_name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `picture` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Дамп данных таблицы emonex.widgets: ~5 rows (приблизительно)
/*!40000 ALTER TABLE `widgets` DISABLE KEYS */;
INSERT INTO `widgets` (`id`, `widget_name`, `category`, `official_name`, `description`, `picture`) VALUES
	(1, 'graph', 'main', 'Graph', 'Watch the graph!', ''),
	(2, 'glass', 'main', 'Glass', 'Two-part widget. Buy or sell the currencies as you wish!', ''),
	(3, 'orders', 'main', 'Orders', 'Do you have many active orders? See them all!', ''),
	(4, 'tradehistory', 'main', 'Trade history', 'Watch to all emonex trades!', ''),
	(5, 'news', 'main', 'News', 'News', '');
/*!40000 ALTER TABLE `widgets` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
