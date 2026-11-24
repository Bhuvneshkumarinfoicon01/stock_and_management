-- Adminer 4.8.1 MySQL 8.0.42-0ubuntu0.22.04.1 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Electronics',	'electronics',	1,	'2025-04-29 00:35:19',	'2025-04-30 05:00:04'),
(2,	'Clothing',	'clothing',	1,	'2025-04-29 00:35:28',	'2025-04-30 05:00:01'),
(3,	'Home',	'home',	1,	'2025-04-29 00:35:35',	'2025-04-30 04:59:55'),
(4,	'Travel',	'travel',	1,	'2025-04-30 04:59:50',	'2025-04-30 04:59:50'),
(6,	'Makeup',	'makeup',	1,	'2025-05-28 10:53:40',	'2025-06-11 06:55:09'),
(7,	'Furniture',	'furniture',	0,	'2025-06-11 12:34:57',	'2025-06-11 12:34:57');

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` (`id`, `customer_name`, `email`, `phone`, `country`, `city`, `address`, `description`, `created_at`, `updated_at`) VALUES
(1,	'Alice',	'Alice@gmail.com',	'abcdefg',	'india',	'sdcsxzcvxc',	'dl121xzvg',	'xcbzxcbcxvnvcxncvncbzxcvzczcvb',	'2025-04-29 04:32:56',	'2025-05-27 12:55:22'),
(2,	'john doe',	'johndoe@gmail.com',	'14413241431',	'India',	'admincity',	'abcdefgh',	NULL,	'2025-04-30 14:35:17',	'2025-04-30 14:35:17'),
(4,	'Ginny',	'ginny@gmail.com',	'abcdefg',	'ABC',	'abc',	'abc',	'zxfdhxfgjg',	'2025-05-27 12:57:34',	'2025-05-27 12:57:34'),
(5,	'xsfdh dfhj',	'owner@gmail.com',	'1512323556696',	'Netherlands',	'Groningen, Nethefgrlands',	'zsdx',	'sssssssssssssssssss',	'2025-06-05 07:07:21',	'2025-06-05 07:07:21'),
(6,	'xsfdh dfhj',	'abc@gmail.com',	'dshbfjsdbgkfjhbsdkjgflksdjg',	'Netherlands',	'Groningen, Nethefgrlands',	'zsdx',	NULL,	'2025-06-11 05:11:01',	'2025-06-11 05:11:01'),
(7,	'Ginny',	'ginny@gmail.com',	'dshbfjsdbgkfjhbsdkjgflksdjg',	'India',	'Groningen, Nethefgrlands',	'zsdx',	NULL,	'2025-06-11 05:12:09',	'2025-06-11 05:12:09'),
(8,	'Ginny',	'ginny@gmail.com',	'dshbfjsdbgkfjhbsdkjgflksdjg',	'India',	'Groningen, Nethefgrlands',	'zsdx',	NULL,	'2025-06-11 05:12:18',	'2025-06-11 05:12:18'),
(9,	'test customer',	'abc1@gmail.com',	'93',	'India',	'Wellsburry',	'ABC-apartment',	NULL,	'2025-06-11 05:13:47',	'2025-06-11 05:13:47');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `ledger_entries`;
CREATE TABLE `ledger_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ledger_id` bigint unsigned NOT NULL,
  `entry_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `debit` decimal(10,2) DEFAULT NULL,
  `credit` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ledger_entries_ledger_id_foreign` (`ledger_id`),
  CONSTRAINT `ledger_entries_ledger_id_foreign` FOREIGN KEY (`ledger_id`) REFERENCES `ledgers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `ledgers`;
CREATE TABLE `ledgers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `ledger_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `content`, `is_read`, `created_at`, `updated_at`) VALUES
(1,	4,	2,	'hi',	1,	'2025-04-29 03:21:07',	'2025-06-05 11:22:13'),
(2,	4,	2,	'hi',	1,	'2025-04-29 03:21:12',	'2025-06-05 11:22:13'),
(3,	1,	2,	'hi admin',	1,	'2025-04-29 03:41:09',	'2025-06-05 11:21:52'),
(4,	2,	1,	'hi super admin',	1,	'2025-04-29 03:41:19',	'2025-06-11 10:01:48'),
(5,	2,	1,	'hi',	1,	'2025-04-29 03:41:49',	'2025-06-11 10:01:48'),
(6,	2,	1,	'hi]',	1,	'2025-04-29 03:41:52',	'2025-06-11 10:01:48'),
(7,	2,	4,	'hi',	1,	'2025-04-29 03:42:01',	'2025-06-05 07:00:32'),
(8,	2,	3,	'hi',	1,	'2025-04-29 03:42:06',	'2025-06-12 12:16:41'),
(9,	1,	2,	'hi',	1,	'2025-04-29 03:42:39',	'2025-06-05 11:21:52'),
(10,	2,	1,	'hi\'',	1,	'2025-04-29 03:44:29',	'2025-06-11 10:01:48'),
(11,	2,	4,	'hi',	1,	'2025-04-29 04:54:23',	'2025-06-05 07:00:32'),
(12,	4,	2,	'hi',	1,	'2025-04-29 04:54:29',	'2025-06-05 11:22:13'),
(13,	4,	2,	'hi',	1,	'2025-04-29 04:56:55',	'2025-06-05 11:22:13'),
(14,	2,	4,	'hi]',	1,	'2025-04-29 04:57:02',	'2025-06-05 07:00:32'),
(15,	2,	4,	'sdcsdcsdc',	1,	'2025-04-29 04:57:07',	'2025-06-05 07:00:32'),
(16,	2,	3,	'hi',	1,	'2025-04-29 04:57:31',	'2025-06-12 12:16:41'),
(17,	3,	2,	'sdvsdcv',	1,	'2025-04-29 04:58:04',	'2025-06-12 12:15:28'),
(18,	3,	2,	'hi',	1,	'2025-04-29 05:03:43',	'2025-06-12 12:15:28'),
(19,	3,	2,	'hi',	1,	'2025-04-29 05:07:54',	'2025-06-12 12:15:28'),
(20,	3,	2,	'hi',	1,	'2025-04-29 05:10:12',	'2025-06-12 12:15:28'),
(21,	3,	2,	'hi',	1,	'2025-04-29 05:12:37',	'2025-06-12 12:15:28'),
(22,	2,	3,	'hi',	1,	'2025-04-29 05:12:51',	'2025-06-12 12:16:41'),
(23,	3,	2,	'hi',	1,	'2025-04-29 05:13:02',	'2025-06-12 12:15:28'),
(24,	3,	2,	'asasas',	1,	'2025-04-29 05:13:20',	'2025-06-12 12:15:28'),
(25,	2,	3,	'asas',	1,	'2025-04-29 05:13:26',	'2025-06-12 12:16:41'),
(26,	3,	2,	'qaacdc',	1,	'2025-04-29 05:15:09',	'2025-06-12 12:15:28'),
(27,	2,	3,	'sds',	1,	'2025-04-29 05:15:18',	'2025-06-12 12:16:41'),
(28,	2,	3,	'as',	1,	'2025-04-29 05:15:47',	'2025-06-12 12:16:41'),
(29,	3,	2,	'wscwdc',	1,	'2025-04-29 05:16:08',	'2025-06-12 12:15:28'),
(30,	2,	3,	'dcdc',	1,	'2025-04-29 05:16:14',	'2025-06-12 12:16:41'),
(31,	3,	2,	'wcwc',	1,	'2025-04-29 05:17:05',	'2025-06-12 12:15:28'),
(32,	2,	3,	'qq',	1,	'2025-04-29 05:17:12',	'2025-06-12 12:16:41'),
(33,	3,	2,	'sdc',	1,	'2025-04-29 05:17:30',	'2025-06-12 12:15:28'),
(34,	2,	3,	'sasxs',	1,	'2025-04-29 05:17:54',	'2025-06-12 12:16:41'),
(35,	2,	3,	'asas',	1,	'2025-04-29 05:19:51',	'2025-06-12 12:16:41'),
(36,	3,	2,	'sfsf',	1,	'2025-04-29 05:20:07',	'2025-06-12 12:15:28'),
(37,	3,	2,	'svsv',	1,	'2025-04-29 05:23:48',	'2025-06-12 12:15:28'),
(38,	2,	3,	'wdqww',	1,	'2025-04-29 05:25:26',	'2025-06-12 12:16:41'),
(39,	2,	3,	'qcwcc',	1,	'2025-04-29 05:25:47',	'2025-06-12 12:16:41'),
(40,	2,	3,	'wfwfwfwfwe',	1,	'2025-04-29 05:26:31',	'2025-06-12 12:16:41'),
(41,	3,	2,	'qdqqdq',	1,	'2025-04-29 05:26:38',	'2025-06-12 12:15:28'),
(42,	1,	2,	'ergege',	1,	'2025-04-30 01:22:06',	'2025-06-05 11:21:52'),
(43,	2,	1,	'df',	1,	'2025-04-30 01:22:15',	'2025-06-11 10:01:48'),
(44,	1,	2,	'asxasxcasxasx',	1,	'2025-04-30 01:25:32',	'2025-06-05 11:21:52'),
(45,	2,	1,	'wedwedcw',	1,	'2025-04-30 01:25:45',	'2025-06-11 10:01:48'),
(46,	1,	2,	'sdscsd',	1,	'2025-04-30 01:25:56',	'2025-06-05 11:21:52'),
(47,	2,	1,	'qs',	1,	'2025-04-30 01:26:04',	'2025-06-11 10:01:48'),
(48,	1,	2,	'asxasaxaxaax',	1,	'2025-04-30 01:28:56',	'2025-06-05 11:21:52'),
(49,	2,	1,	'sdcsdc',	1,	'2025-04-30 01:29:04',	'2025-06-11 10:01:48'),
(50,	1,	2,	'sdc',	1,	'2025-04-30 01:29:10',	'2025-06-05 11:21:52'),
(51,	1,	2,	'sdcscscscsdcsdsscsdcs',	1,	'2025-04-30 01:33:01',	'2025-06-05 11:21:52'),
(52,	2,	1,	'sadc',	1,	'2025-04-30 01:33:12',	'2025-06-11 10:01:48'),
(53,	2,	1,	'asdcac',	1,	'2025-04-30 04:13:53',	'2025-06-11 10:01:48'),
(54,	2,	1,	'saas',	1,	'2025-04-30 04:16:34',	'2025-06-11 10:01:48'),
(55,	2,	1,	'acca',	1,	'2025-04-30 04:16:51',	'2025-06-11 10:01:48'),
(56,	1,	2,	'aqsas',	1,	'2025-04-30 04:17:48',	'2025-06-05 11:21:52'),
(57,	2,	1,	'HI',	1,	'2025-05-01 05:10:21',	'2025-06-11 10:01:48'),
(58,	1,	2,	'HELLO',	1,	'2025-05-01 05:13:07',	'2025-06-05 11:21:52'),
(59,	2,	5,	'hi',	0,	'2025-05-01 13:58:33',	'2025-05-01 13:58:33'),
(60,	2,	6,	'Hi',	1,	'2025-05-06 12:27:53',	'2025-05-07 12:42:16'),
(61,	2,	8,	'Hi',	0,	'2025-05-06 12:28:13',	'2025-05-06 12:28:13'),
(62,	6,	3,	'hi',	1,	'2025-05-07 12:40:08',	'2025-05-27 06:11:59'),
(63,	3,	6,	'Hello',	1,	'2025-05-07 12:40:32',	'2025-05-07 12:40:52'),
(64,	6,	3,	'I will transfer some stock tomorrow',	1,	'2025-05-07 12:41:56',	'2025-05-27 06:11:59'),
(65,	6,	2,	'hi',	1,	'2025-05-07 12:42:23',	'2025-06-05 11:23:00'),
(66,	2,	6,	'hello',	0,	'2025-05-07 12:43:32',	'2025-05-07 12:43:32'),
(68,	1,	2,	'hi admin form vivek',	1,	'2025-05-07 12:50:59',	'2025-06-05 11:21:52'),
(69,	2,	1,	'hello super admin form vivek',	1,	'2025-05-07 12:51:32',	'2025-06-11 10:01:48'),
(70,	1,	2,	'sdfds',	1,	'2025-05-07 12:52:09',	'2025-06-05 11:21:52'),
(71,	2,	1,	'sdfdsf',	1,	'2025-05-07 12:52:31',	'2025-06-11 10:01:48'),
(72,	1,	2,	'hi admin',	1,	'2025-05-07 12:53:11',	'2025-06-05 11:21:52'),
(73,	2,	1,	'hellp super admmn',	1,	'2025-05-07 12:53:39',	'2025-06-11 10:01:48'),
(74,	1,	2,	'hi super admin 2',	1,	'2025-05-07 13:13:28',	'2025-06-05 11:21:52'),
(75,	2,	1,	'hello superadmin 2',	1,	'2025-05-07 13:14:37',	'2025-06-11 10:01:48'),
(76,	2,	1,	'hi vivek',	1,	'2025-05-07 13:25:26',	'2025-06-11 10:01:48'),
(77,	1,	2,	'hello',	1,	'2025-05-07 13:25:58',	'2025-06-05 11:21:52'),
(78,	1,	2,	'hello',	1,	'2025-05-07 13:37:33',	'2025-06-05 11:21:52'),
(79,	2,	1,	'hi',	1,	'2025-05-07 13:37:48',	'2025-06-11 10:01:48'),
(80,	1,	2,	'hhh',	1,	'2025-05-07 13:38:13',	'2025-06-05 11:21:52'),
(81,	1,	2,	'vivek1',	1,	'2025-05-07 13:39:36',	'2025-06-05 11:21:52'),
(82,	1,	2,	'vivek2',	1,	'2025-05-07 13:40:55',	'2025-06-05 11:21:52'),
(83,	1,	2,	'hisss',	1,	'2025-05-07 14:13:39',	'2025-06-05 11:21:52'),
(84,	1,	2,	'sss',	1,	'2025-05-07 14:13:57',	'2025-06-05 11:21:52'),
(85,	1,	2,	'dasdsadsad',	1,	'2025-05-07 14:15:03',	'2025-06-05 11:21:52'),
(86,	2,	1,	'asssdsd',	1,	'2025-05-07 14:15:15',	'2025-06-11 10:01:48'),
(87,	2,	1,	'hi',	1,	'2025-05-14 08:35:06',	'2025-06-11 10:01:48'),
(88,	1,	2,	'hi',	1,	'2025-05-14 08:35:20',	'2025-06-05 11:21:52'),
(89,	1,	2,	'hi',	1,	'2025-05-14 08:35:31',	'2025-06-05 11:21:52'),
(90,	2,	1,	'hi',	1,	'2025-05-14 08:40:03',	'2025-06-11 10:01:48'),
(91,	1,	2,	'hi',	1,	'2025-05-14 08:40:10',	'2025-06-05 11:21:52'),
(92,	2,	1,	'123',	1,	'2025-05-14 08:40:20',	'2025-06-11 10:01:48'),
(93,	2,	1,	'hi',	1,	'2025-05-23 04:54:19',	'2025-06-11 10:01:48'),
(94,	1,	2,	'hello',	1,	'2025-05-23 04:54:31',	'2025-06-05 11:21:52'),
(95,	1,	2,	'hi',	1,	'2025-05-23 04:56:24',	'2025-06-05 11:21:52'),
(96,	3,	2,	'hi admin',	1,	'2025-05-23 05:15:51',	'2025-06-12 12:15:28'),
(97,	3,	2,	'hello',	1,	'2025-05-23 05:16:54',	'2025-06-12 12:15:28'),
(98,	2,	3,	'hie',	1,	'2025-05-23 05:17:13',	'2025-06-12 12:16:41'),
(99,	1,	2,	'hi',	1,	'2025-05-23 05:23:11',	'2025-06-05 11:21:52'),
(100,	2,	1,	'hi',	1,	'2025-05-23 05:23:54',	'2025-06-11 10:01:48'),
(101,	1,	2,	'123',	1,	'2025-05-23 05:24:01',	'2025-06-05 11:21:52'),
(102,	2,	1,	'hi',	1,	'2025-05-23 05:24:47',	'2025-06-11 10:01:48'),
(103,	1,	2,	'hi',	1,	'2025-05-23 05:26:00',	'2025-06-05 11:21:52'),
(104,	2,	1,	'1',	1,	'2025-05-23 05:26:14',	'2025-06-11 10:01:48'),
(105,	1,	2,	'2',	1,	'2025-05-23 05:26:17',	'2025-06-05 11:21:52'),
(106,	1,	2,	'sdcsdc',	1,	'2025-05-23 06:49:55',	'2025-06-05 11:21:52'),
(107,	2,	1,	'asdasd',	1,	'2025-05-23 06:50:08',	'2025-06-11 10:01:48'),
(108,	2,	1,	'qweqw',	1,	'2025-05-23 06:50:17',	'2025-06-11 10:01:48'),
(109,	3,	2,	'11',	1,	'2025-05-23 07:35:34',	'2025-06-12 12:15:28'),
(110,	2,	3,	'22',	1,	'2025-05-23 07:35:49',	'2025-06-12 12:16:41'),
(111,	2,	3,	'hi',	1,	'2025-05-27 06:01:59',	'2025-06-12 12:16:41'),
(112,	3,	2,	'hello',	1,	'2025-05-27 06:04:27',	'2025-06-12 12:15:28'),
(113,	2,	3,	'hi',	1,	'2025-05-27 06:04:37',	'2025-06-12 12:16:41'),
(114,	2,	3,	'hi',	1,	'2025-05-27 06:21:18',	'2025-06-12 12:16:41'),
(115,	2,	3,	'hhjgh',	1,	'2025-05-27 06:56:44',	'2025-06-12 12:16:41'),
(116,	3,	2,	'ytguy7878',	1,	'2025-05-27 06:57:30',	'2025-06-12 12:15:28'),
(117,	3,	2,	'com 1',	1,	'2025-05-28 07:44:56',	'2025-06-12 12:15:28'),
(118,	2,	3,	'com 2',	1,	'2025-05-28 07:45:16',	'2025-06-12 12:16:41'),
(119,	3,	4,	'ytgkjdghkhgkkk',	1,	'2025-06-03 07:12:48',	'2025-06-05 07:00:28'),
(120,	3,	4,	'xhbddfhfgsjfg',	1,	'2025-06-03 07:14:08',	'2025-06-05 07:00:28'),
(121,	3,	4,	'xgfmjhgklhjlkhjkhgkjhgkjfghj',	1,	'2025-06-03 07:14:25',	'2025-06-05 07:00:28'),
(122,	4,	3,	'fxdnhjxgfkghkghkgh',	1,	'2025-06-03 07:14:32',	'2025-06-05 10:03:22'),
(123,	4,	3,	'xghkxghkhkhgj',	1,	'2025-06-03 07:14:40',	'2025-06-05 10:03:22'),
(124,	4,	3,	'xgfjghkjghkhkghkhgkxjfghzhfdfh',	1,	'2025-06-03 07:14:45',	'2025-06-05 10:03:22'),
(125,	3,	4,	'gfjghkghkhgkhgk',	1,	'2025-06-03 07:15:00',	'2025-06-05 07:00:28'),
(126,	4,	3,	'dfhfhsfshhsfdhsfh',	1,	'2025-06-03 07:15:05',	'2025-06-05 10:03:22'),
(127,	2,	3,	'sdgsdfhgftjhgkjhgkgh',	1,	'2025-06-05 11:21:59',	'2025-06-12 12:16:41'),
(128,	2,	4,	'sfdhfdshfdhfdhfdhshs',	0,	'2025-06-05 11:22:10',	'2025-06-05 11:22:10'),
(129,	2,	8,	'dfhfdhsfdhshghshggfhsfhfghfgh',	0,	'2025-06-05 11:22:52',	'2025-06-05 11:22:52');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'0001_01_01_000000_create_users_table',	1),
(2,	'0001_01_01_000001_create_cache_table',	1),
(3,	'0001_01_01_000002_create_jobs_table',	1),
(4,	'2025_03_11_121257_create_roles_table',	1),
(5,	'2025_03_17_125711_create_stores_table',	1),
(6,	'2025_03_17_125725_create_stocks_table',	1),
(7,	'2025_03_20_110100_create_categories_table',	1),
(8,	'2025_03_20_110100_create_units_table',	1),
(9,	'2025_03_26_134509_create_orders_table',	1),
(10,	'2025_03_27_100614_crearte_order_items_table',	1),
(11,	'2025_03_27_120208_create_stock_transfers_table',	1),
(12,	'2025_03_27_121043_create_stock_transfer_items_table',	1),
(13,	'2025_03_31_045217_create_stock_adjustments_table',	1),
(14,	'2025_04_01_122422_create_customers_table',	1),
(15,	'2025_04_03_123459_create_ledgers_table',	1),
(16,	'2025_04_03_133334_create_ledger_entries_table',	1),
(17,	'2025_04_07_060024_create_store_employees_table',	1),
(18,	'2025_04_07_064750_add_selected_store_id_to_users_table',	1),
(19,	'2025_04_21_051159_create_messages_table',	1),
(20,	'2025_04_22_065607_create_notifications_table',	1);

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notifications` (`id`, `user_id`, `message`, `read_at`, `created_at`, `updated_at`) VALUES
(18,	4,	'New Employee (employee 2) has been successfully added.',	NULL,	'2025-04-29 01:49:49',	'2025-04-29 01:49:49'),
(20,	4,	'Employee has been successfully deleted.',	NULL,	'2025-04-29 02:03:10',	'2025-04-29 02:03:10'),
(83,	6,	'New Customer has been Added.',	NULL,	'2025-05-01 05:43:35',	'2025-05-01 05:43:35'),
(93,	6,	'New Employee (Test staff staff) has been successfully added.',	NULL,	'2025-05-02 07:05:03',	'2025-05-02 07:05:03'),
(131,	4,	'New category (Makeup) has been added.',	NULL,	'2025-05-28 10:53:40',	'2025-05-28 10:53:40'),
(132,	4,	'New Stock has been added to store (Store A)',	NULL,	'2025-05-28 10:55:02',	'2025-05-28 10:55:02'),
(135,	4,	'Outgoing stock transfer to Store A has been initiated.',	NULL,	'2025-05-28 11:08:24',	'2025-05-28 11:08:24'),
(142,	4,	'Outgoing stock transfer to Store A has been initiated.',	NULL,	'2025-05-28 12:09:26',	'2025-05-28 12:09:26'),
(153,	4,	'New Employee (xsfdh dfhj) has been successfully added.',	NULL,	'2025-06-05 06:58:09',	'2025-06-05 06:58:09'),
(154,	3,	'New Customer has been Added.',	NULL,	'2025-06-05 07:07:21',	'2025-06-05 07:07:21'),
(156,	3,	'New Employee (dsdggg dfgdgdf) has been successfully added.',	NULL,	'2025-06-05 10:56:40',	'2025-06-05 10:56:40'),
(157,	3,	'Employee (a b) has been successfully updated.',	NULL,	'2025-06-05 11:00:27',	'2025-06-05 11:00:27'),
(158,	3,	'New Employee (XZCBXCB XZCBXCB) has been successfully added.',	NULL,	'2025-06-05 11:15:27',	'2025-06-05 11:15:27'),
(161,	4,	'Incoming stock transfer request from Store A',	NULL,	'2025-06-10 02:19:56',	'2025-06-10 02:19:56'),
(162,	3,	'Outgoing stock transfer to Store B has been initiated.',	NULL,	'2025-06-10 02:19:56',	'2025-06-10 02:19:56'),
(164,	4,	'Incoming stock transfer request from Store A',	NULL,	'2025-06-10 09:27:03',	'2025-06-10 09:27:03'),
(165,	3,	'Outgoing stock transfer to Store B has been initiated.',	NULL,	'2025-06-10 09:27:03',	'2025-06-10 09:27:03'),
(169,	3,	'Employee (a b) has been successfully updated.',	NULL,	'2025-06-10 10:56:06',	'2025-06-10 10:56:06'),
(170,	3,	'Employee (dsdggg dfgdgdf) has been successfully updated.',	NULL,	'2025-06-10 10:59:26',	'2025-06-10 10:59:26'),
(171,	3,	'New Employee (test test) has been successfully added.',	NULL,	'2025-06-10 11:11:09',	'2025-06-10 11:11:09'),
(172,	3,	'Employee (test test) has been successfully updated.',	NULL,	'2025-06-10 11:11:52',	'2025-06-10 11:11:52'),
(217,	4,	'Incoming stock transfer request from Store A',	NULL,	'2025-06-11 11:26:42',	'2025-06-11 11:26:42'),
(218,	3,	'Outgoing stock transfer to Store B has been initiated.',	NULL,	'2025-06-11 11:26:42',	'2025-06-11 11:26:42'),
(248,	3,	'Employee (employee 1) has been successfully updated.',	NULL,	'2025-06-12 10:57:21',	'2025-06-12 10:57:21'),
(249,	3,	'Employee (employee 1) has been successfully updated.',	NULL,	'2025-06-12 10:58:16',	'2025-06-12 10:58:16'),
(250,	3,	'Employee (employee 1) has been successfully updated.',	NULL,	'2025-06-12 10:58:26',	'2025-06-12 10:58:26'),
(261,	4,	'Incoming stock transfer request from Store A',	NULL,	'2025-06-16 13:53:22',	'2025-06-16 13:53:22'),
(262,	3,	'Outgoing stock transfer to Store B has been initiated.',	NULL,	'2025-06-16 13:53:22',	'2025-06-16 13:53:22'),
(264,	2,	'New Order has been created.',	NULL,	'2025-06-17 09:14:13',	'2025-06-17 09:14:13');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `category_id` bigint DEFAULT NULL,
  `price` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `sku`, `qty`, `category_id`, `price`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_001',	'Dell Xperia 360',	'100',	1,	1,	50000,	'2025-04-29 04:33:15',	'2025-04-29 04:33:15'),
(2,	2,	'P_002',	'Arrow Tshirt',	'101',	3,	2,	2000,	'2025-04-29 07:25:54',	'2025-04-29 07:25:54'),
(3,	3,	'P_003',	'Bed',	'102',	1,	3,	20000,	'2025-04-30 00:23:11',	'2025-04-30 00:23:11'),
(4,	4,	'P_004',	'Headphones',	'103',	1,	1,	10000,	'2025-04-30 00:23:52',	'2025-04-30 00:23:52'),
(6,	6,	'P_004',	'Headphones',	'103',	1,	1,	10000,	'2025-04-30 14:35:29',	'2025-04-30 14:35:29'),
(7,	6,	'P_006',	'wallpaper',	'104',	1,	3,	3500,	'2025-04-30 14:35:29',	'2025-04-30 14:35:29'),
(8,	7,	'P_007',	'Bag',	'105',	1,	4,	2500,	'2025-04-30 14:36:00',	'2025-04-30 14:36:00'),
(9,	8,	'P_013',	'NewProduct',	'NewProduct',	20,	1,	2000000,	'2025-05-05 07:19:46',	'2025-05-05 07:19:46'),
(10,	9,	'P_001',	'Dell Xperia 360',	'100',	4,	1,	50000,	'2025-05-06 12:24:11',	'2025-05-06 12:24:11'),
(11,	10,	'P_001',	'Dell Xperia 3601',	'100',	2,	1,	55000,	'2025-06-05 01:44:31',	'2025-06-05 01:44:31'),
(12,	10,	'P_020',	'Makeup blush',	'123s',	1,	6,	500,	'2025-06-05 01:44:31',	'2025-06-05 01:44:31'),
(13,	11,	'P_004',	'Headphones',	'103',	1,	1,	10000,	'2025-06-10 11:22:51',	'2025-06-10 11:22:51'),
(14,	11,	'P_014',	'Test EProduct',	'00Test',	1,	1,	20000,	'2025-06-10 11:22:51',	'2025-06-10 11:22:51'),
(15,	12,	'P_021',	'Blush',	'1b',	1,	6,	500,	'2025-06-11 05:07:42',	'2025-06-11 05:07:42'),
(16,	12,	'P_020',	'Makeup blush',	'123s',	1,	6,	500,	'2025-06-11 05:07:42',	'2025-06-11 05:07:42'),
(17,	12,	'P_023',	'dress',	'1002003232',	3,	2,	499,	'2025-06-11 05:07:42',	'2025-06-11 05:07:42'),
(18,	13,	'P_018',	'Makeup brush',	'123',	1,	3,	100,	'2025-06-11 05:27:57',	'2025-06-11 05:27:57'),
(19,	14,	'P_001',	'Dell Xperia 3601',	'100',	1,	1,	55000,	'2025-06-12 17:52:20',	'2025-06-12 17:52:20'),
(20,	14,	'P_004',	'Headphones',	'103',	1,	1,	10000,	'2025-06-12 17:52:20',	'2025-06-12 17:52:20'),
(21,	14,	'P_014',	'Test EProduct',	'00Test',	1,	1,	20000,	'2025-06-12 17:52:20',	'2025-06-12 17:52:20'),
(23,	5,	'P_003',	'Bed',	'102',	1,	3,	20000,	'2025-06-16 09:35:36',	'2025-06-16 09:35:36'),
(24,	5,	'P_021',	'Blush',	'1b',	1,	6,	500,	'2025-06-16 09:35:36',	'2025-06-16 09:35:36'),
(26,	15,	'P_021',	'Blush',	'1b',	1,	6,	500,	'2025-06-17 09:10:23',	'2025-06-17 09:10:23'),
(27,	16,	'P_024',	'dress',	'10022',	3,	2,	499,	'2025-06-17 09:14:13',	'2025-06-17 09:14:13');

DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` double NOT NULL,
  `status` int NOT NULL DEFAULT '1',
  `subtotal_price` double NOT NULL,
  `tax` double DEFAULT NULL,
  `discount` int DEFAULT NULL,
  `shipping` int DEFAULT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `orders` (`id`, `store_id`, `customer_id`, `customer_name`, `total_price`, `status`, `subtotal_price`, `tax`, `discount`, `shipping`, `payment_method`, `type`, `notes`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	'aice',	60000,	1,	50000,	25,	5,	0,	'cash',	'online',	NULL,	'2025-04-29 04:33:15',	'2025-04-29 04:33:15'),
(2,	2,	1,	'aice',	6930,	1,	6000,	25,	10,	30,	'debit card',	'online',	NULL,	'2025-04-29 07:25:54',	'2025-04-29 07:25:54'),
(3,	1,	1,	'aice',	22000,	1,	20000,	10,	10,	10,	'cash',	'online',	NULL,	'2025-04-30 00:23:11',	'2025-04-30 00:23:11'),
(4,	1,	1,	'aice',	11000,	1,	10000,	10,	10,	10,	'cash',	'online',	NULL,	'2025-04-30 00:23:52',	'2025-04-30 00:23:52'),
(5,	1,	1,	'aice',	22550,	1,	20500,	20,	10,	5,	'Card',	'offline',	NULL,	'2025-04-30 00:34:16',	'2025-06-16 09:35:36'),
(6,	3,	2,	'john doe',	15525,	1,	13500,	5,	5,	15,	'debit card',	'online',	NULL,	'2025-04-30 14:35:29',	'2025-04-30 14:35:29'),
(7,	3,	2,	'john doe',	3750,	1,	2500,	25,	0,	25,	'scan',	'online',	NULL,	'2025-04-30 14:36:00',	'2025-04-30 14:36:00'),
(8,	7,	3,	'testcx',	44000000,	1,	40000000,	5,	0,	5,	'debit card',	'online',	NULL,	'2025-05-05 07:19:46',	'2025-05-05 07:19:46'),
(9,	1,	3,	'testcx',	240000,	1,	200000,	10,	0,	10,	'debit card',	'online',	NULL,	'2025-05-06 12:24:11',	'2025-05-06 12:24:11'),
(10,	1,	1,	'Alice',	127075,	1,	110500,	15,	0,	0,	'cash',	'online',	NULL,	'2025-06-05 01:44:31',	'2025-06-05 01:44:31'),
(11,	1,	2,	'john doe',	33000,	1,	30000,	5,	0,	5,	'cash',	'online',	NULL,	'2025-06-10 11:22:51',	'2025-06-10 11:22:51'),
(12,	1,	1,	'Alice',	2871.55,	1,	2497,	10,	5,	10,	'cash',	'online',	NULL,	'2025-06-11 05:07:42',	'2025-06-11 05:07:42'),
(13,	1,	1,	'Alice',	100,	1,	100,	0,	0,	0,	'cash',	'online',	NULL,	'2025-06-11 05:27:57',	'2025-06-11 05:27:57'),
(14,	1,	1,	'Alice',	85000,	1,	85000,	0,	0,	0,	'debit card',	'online',	NULL,	'2025-06-12 17:52:20',	'2025-06-12 17:52:20'),
(15,	1,	1,	'Alice',	550,	1,	500,	20,	10,	0,	'Cash',	'offline',	NULL,	'2025-06-17 09:09:34',	'2025-06-17 09:10:23'),
(16,	1,	4,	'Ginny',	1646.7,	1,	1497,	15,	5,	0,	'Scan',	'offline',	NULL,	'2025-06-17 09:14:13',	'2025-06-17 09:14:13');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('admin@gmail.com',	'$2y$12$Gzjw3LVaiqYW.Qx9Js8yG.Qx4OFOd7NpVP/iywjkkIf.4AqpBFTkW',	'2025-05-28 08:43:00'),
('storemanager@gmail.com',	'$2y$12$hcVR3FUnmWHqRm2DF88EFepZvvOfOL87ylC/PajPid.5AGa4vn5OS',	'2025-06-05 08:44:10');

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  UNIQUE KEY `roles_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1,	'SuperAdmin',	'superadmin',	'manage administration privileges',	'2025-04-29 00:30:27',	'2025-04-29 00:30:27'),
(2,	'Admin',	'admin',	'To create and manage store ',	'2025-04-29 00:30:27',	'2025-04-29 00:30:27'),
(3,	'StoreManager',	'storemanager',	'To create and manage store ',	'2025-04-29 00:30:27',	'2025-04-29 00:30:27'),
(4,	'Anonymous',	'anonymous',	'The users who are not allowed for any access',	'2025-04-29 00:30:27',	'2025-04-29 00:30:27'),
(5,	'Staff',	'staff',	'The employees working at a store',	'2025-04-29 00:30:27',	'2025-04-29 00:30:27');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('0uzWqudgcxK18HW2kgnZSQ84f1y0xMLnJoKdgWKK',	NULL,	'64.62.156.192',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiV1dFTWpybzFJdm5CQk9jajJlaWVqdWNsMlpUV2xYb3ZNbElQOE1YZSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751242743),
('79MjFtriULabljyJGsDyCITddPr6F9xyi7Wcs87J',	NULL,	'167.94.146.61',	'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUmFVbDNQeHZRS2E3YjBCeUR2TWh1TWNVTE1sejRxQUF1RERQZE80MiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751225921),
('c33ZSs8tIroyxxG6qoelavLmTcTI3qahItZqjVvW',	NULL,	'138.197.15.182',	'Mozilla/5.0 zgrab/0.x',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUlA2VDlKd3JOcUxWSVh5dnR6bTJTNGppR3JzdTRIcEpxbEh1aUM0bCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751252258),
('igVDtl6MIaz80H7H5ui1QZz5IdHUspjKbJVUsgXf',	NULL,	'167.94.138.51',	'Mozilla/5.0 (compatible; CensysInspect/1.1; +https://about.censys.io/)',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiUldJM1RQOE1rak1nTURCcnE1dlZLMWZ2T1JsazY5SWtZbEcyd09FMiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751259114),
('jLNv0WEtRGAr1RPdXtxmwpACp1Ap7WZ7b4OZCn2M',	NULL,	'49.36.187.30',	'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaDl3Y1FmOGNwUDlFcE9SZDZDMnoxMWQzV0xUdXFKVDhtanE4OFNVTyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDE6Imh0dHBzOi8vYXVjdGlvbi5pdGV4cGVydGlzZWluZGlhLmNvbTo4MDgxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',	1751265482),
('LYVla9DlUrNLuTod6vejnd5e9OrvBOYqwJ78Yx1z',	NULL,	'64.62.156.197',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/108.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiQmI1bUh3NDNDUUR6OW1KdDZ0ZERuRWgxZW1BM2NiVkZyWXdJOEowdCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751242762),
('RHa7FHzwbPPTLVfBHJkmt0De4wbhAzMjz9DcmmH9',	NULL,	'118.194.251.246',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 Edg/120.0.0.0',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiRjcyMzZxRjROTEg0MTN0Y29JWlpQR2t1NE1ycnVIR09LbFRvdTAzYiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751223035),
('uKDBONWickPkXyyLv7ejGUbMbezRTDKGdzRnjr53',	NULL,	'184.105.139.68',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Safari/605.1.15',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoicVVESWM5amZVZkJWeU50QTBUSWxMUnRSbmFFd01PS0VpYjBKbEJaRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751225169),
('vf4YAg5KO1tjdjjBD329rNKRu88ENh4ohHvhv3T7',	NULL,	'205.210.31.229',	'Expanse, a Palo Alto Networks company, searches across the global IPv4 space multiple times per day to identify customers&#39; presences on the Internet. If you would like to be excluded from our scans, please send IP addresses/domains to: scaninfo@paloaltonetworks.com',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiR1V1aWI4Ynd3NTNlbGpiTFJRaDVkRXVHbENHZjNnMGhPMDdVb3JGWiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751256181),
('XB4Y0QE7FqXhJBcC7yjeTigMo950N9mRdTNE7pK6',	NULL,	'170.64.130.197',	'Mozilla/5.0 zgrab/0.x',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaDVFenBKRzBSZFVnaXhZeUl3aDFITXBUMkxmeTVCWlRYTk50VVh2aiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHBzOi8vMTY1LjIyNy45OC4xNTA6ODA4MSI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',	1751216700);

DROP TABLE IF EXISTS `stock_adjustments`;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjust_quantity` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjust_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_adjustments` (`id`, `store_id`, `product_id`, `adjust_quantity`, `reason`, `adjust_date`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_003',	1,	'Loss',	'2025-04-22',	'2025-04-29 06:07:57',	'2025-06-12 10:51:54'),
(2,	1,	'P_001',	1,	'Loss',	'2025-04-08',	'2025-04-29 06:08:20',	'2025-06-12 08:33:18'),
(3,	7,	'P_012',	1,	'Loss',	'2025-05-02',	'2025-05-01 07:40:32',	'2025-06-12 08:33:33'),
(6,	3,	'P_004',	1,	'Damage',	'2025-06-11',	'2025-06-11 12:28:17',	'2025-06-12 08:33:39'),
(7,	2,	'P_009',	1,	'Loss',	'2025-06-12',	'2025-06-12 11:33:23',	'2025-06-12 11:33:23'),
(8,	1,	'P_004',	1,	'Loss',	'2025-06-12',	'2025-06-12 11:33:43',	'2025-06-12 11:33:51'),
(9,	1,	'P_014',	1,	'Loss',	'2025-06-12',	'2025-06-12 11:34:07',	'2025-06-12 11:34:07');

DROP TABLE IF EXISTS `stock_transfer_items`;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` bigint NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `category_id` bigint DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_transfer_items` (`id`, `reference_no`, `product_id`, `product_name`, `sku`, `qty`, `category_id`, `status`, `created_at`, `updated_at`) VALUES
(1,	174593166170,	'P_004',	'Headphones',	'103',	4,	1,	1,	'2025-04-29 07:31:01',	'2025-04-29 07:31:31'),
(4,	174608376744,	'P_012',	'TestProduct',	'TestProduct',	5,	2,	1,	'2025-05-01 07:16:07',	'2025-06-11 10:58:28'),
(7,	174653123910,	'P_001',	'Dell Xperia 360',	'100',	9,	1,	1,	'2025-05-06 11:33:59',	'2025-05-06 12:08:31'),
(9,	174825573855,	'P_004',	'Headphones',	'103',	1,	1,	0,	'2025-05-26 10:37:22',	'2025-05-28 11:39:44'),
(10,	174643110988,	'P_014',	'Test EProduct',	'00Test',	5,	1,	1,	'2025-05-26 10:39:00',	'2025-05-28 11:14:36'),
(13,	174825621597,	'P_004',	'Headphones',	'103',	1,	1,	1,	'2025-05-26 10:43:35',	'2025-06-03 05:08:13'),
(15,	843050377818,	'P_019',	'Makeup',	'1234',	1,	1,	0,	'2025-05-28 11:43:35',	'2025-06-03 05:03:44'),
(17,	174608470273,	'P_012',	'TestProduct',	'TestProduct',	1,	2,	NULL,	'2025-05-28 11:47:44',	'2025-05-28 11:47:44'),
(18,	843416415024,	'P_021',	'Blush',	'1b',	1,	6,	1,	'2025-05-28 12:09:25',	'2025-06-03 06:39:37'),
(19,	952198755440,	'P_001',	'Dell Xperia 3601',	'100',	3,	1,	1,	'2025-06-10 02:19:56',	'2025-06-11 10:58:39'),
(20,	952198755440,	'P_003',	'Bed',	'102',	1,	3,	1,	'2025-06-10 02:19:56',	'2025-06-11 10:58:35'),
(21,	954762815713,	'P_023',	'dress',	'1002003232',	1,	2,	0,	'2025-06-10 09:27:03',	'2025-06-11 10:58:45'),
(27,	964120904185,	'P_023',	'dress',	'1002003232',	3,	2,	NULL,	'2025-06-12 12:20:20',	'2025-06-12 12:20:20'),
(28,	8200139865,	'P_001',	'Dell Xperia 3601',	'100',	1,	1,	1,	'2025-06-16 13:53:22',	'2025-06-16 13:54:01');

DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_from` bigint NOT NULL,
  `store_to` bigint NOT NULL,
  `rperson_from` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rperson_to` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_no` bigint NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `arrival_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_reference_no_unique` (`reference_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_transfers` (`id`, `store_from`, `store_to`, `rperson_from`, `rperson_to`, `reference_no`, `notes`, `arrival_date`, `created_at`, `updated_at`) VALUES
(1,	3,	1,	'alice',	'jophn',	174593166170,	NULL,	'2025-05-03',	'2025-04-29 07:31:01',	'2025-04-29 07:31:01'),
(4,	7,	2,	'test store manager',	'store manager',	174608376744,	NULL,	'2025-05-02',	'2025-05-01 07:16:07',	'2025-05-01 07:16:07'),
(5,	7,	3,	'Store manager',	'store manager',	174608470273,	NULL,	'2025-05-01',	'2025-05-01 07:31:42',	'2025-05-01 07:31:42'),
(6,	7,	1,	'Store manager',	'store manager',	174643110988,	NULL,	'2025-05-06',	'2025-05-05 07:45:09',	'2025-05-05 07:45:09'),
(7,	2,	7,	'Store manager',	'store manager',	174653123910,	'test',	'2025-05-07',	'2025-05-06 11:33:59',	'2025-05-06 11:33:59'),
(8,	3,	1,	'alice',	'john',	174825573855,	NULL,	'2025-05-31',	'2025-05-26 10:35:38',	'2025-05-26 10:35:38'),
(12,	1,	2,	'alice',	'john',	174825621597,	NULL,	'2025-05-30',	'2025-05-26 10:43:35',	'2025-05-26 10:43:35'),
(13,	2,	1,	'alice',	'john',	843050377818,	NULL,	'2025-05-30',	'2025-05-28 11:08:24',	'2025-05-28 11:08:24'),
(14,	2,	1,	'alice',	'border',	843416415024,	'abc',	'2025-05-29',	'2025-05-28 12:09:25',	'2025-05-28 12:09:25'),
(15,	1,	2,	'John Doe',	'Jane Doe',	952198755440,	NULL,	'2025-06-10',	'2025-06-10 02:19:56',	'2025-06-10 02:19:56'),
(16,	1,	2,	'John Doe',	'Jane Doe',	954762815713,	NULL,	'2025-06-10',	'2025-06-10 09:27:03',	'2025-06-10 09:27:03'),
(17,	1,	2,	'Store manager',	'Jane Doe',	964120904185,	NULL,	'2025-06-12',	'2025-06-11 11:26:42',	'2025-06-11 11:26:42'),
(18,	1,	2,	'alice',	'john',	8200139865,	NULL,	'2025-06-18',	'2025-06-16 13:53:22',	'2025-06-16 13:53:22');

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE `stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint NOT NULL,
  `unit_id` bigint DEFAULT NULL,
  `supplier_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `quantity` int DEFAULT NULL,
  `price` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `manufactured_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `images` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocks_product_id_store_id_unique` (`product_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `product_name`, `sku`, `category_id`, `unit_id`, `supplier_name`, `purchase_date`, `description`, `quantity`, `price`, `discount`, `tax`, `manufactured_date`, `expiry_date`, `images`, `status`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_001',	'Dell Xperia 3601',	'100',	1,	1,	'dell',	'2025-04-22',	NULL,	3,	55000,	0,	0,	'2025-04-08',	'2025-04-30',	'products/S5Hh8mBQPuV9lHdSuVqffPpK57loRJukRAIW0aXE.jpg',	1,	'2025-04-29 00:36:30',	'2025-06-16 13:54:01'),
(2,	2,	'P_002',	'Arrow Tshirt',	'101',	2,	1,	'arrow',	'2025-04-29',	NULL,	0,	2000,	0,	0,	'2025-04-08',	'2025-04-30',	'products/qvmkxiY90BGTiWkWbn5lh7jmzcAw0HvQT0XJB8xU.jpg',	1,	'2025-04-29 00:37:31',	'2025-06-11 12:27:27'),
(3,	1,	'P_003',	'Bed',	'102',	3,	1,	'Godrej',	'2025-04-29',	NULL,	2,	20000,	0,	0,	'2025-03-31',	'2025-05-10',	'products/9JZ5fXd9DFaAzDERahzPkc4GlnQlHn361G2SQt66.jpg',	1,	'2025-04-29 00:38:26',	'2025-06-16 09:35:36'),
(4,	3,	'P_004',	'Headphones',	'103',	1,	1,	'JBL',	'2025-04-08',	NULL,	4,	10000,	0,	0,	'2025-04-30',	'2025-05-02',	'products/wtNHuXSppbY5rxIyZhSB8q23qi4yJYMfLdYJwrd7.jpg',	1,	'2025-04-29 00:40:12',	'2025-06-11 12:28:17'),
(5,	1,	'P_004',	'Headphones',	'103',	1,	1,	'JBL',	'2025-04-08',	NULL,	0,	10000,	0,	0,	'2025-04-30',	'2025-05-03',	'products/ccRhXyf9zvuwrpn4jOfnLmplC555lQnhsRaQ0738.jpg',	1,	'2025-04-29 07:31:31',	'2025-06-12 17:52:20'),
(6,	3,	'P_006',	'wallpaper',	'104',	3,	1,	'Godrej',	'2025-04-22',	NULL,	11,	3500,	NULL,	NULL,	NULL,	NULL,	'products/iF3GH8HobsXSDfjfRvcLg69ynOOcD07hvzRLOawH.jpg',	1,	'2025-04-30 14:24:24',	'2025-04-30 14:35:29'),
(7,	3,	'P_007',	'Bag',	'105',	4,	1,	'Wildcraft',	'2025-04-30',	NULL,	6,	2500,	NULL,	NULL,	NULL,	NULL,	'products/1hnUiw2WHEIKnS7FMVDri5tanu7VP0aMMHUW83jd.jpg',	1,	'2025-04-30 14:26:00',	'2025-04-30 14:36:00'),
(8,	2,	'P_008',	'Smartphone',	'106',	1,	1,	'Apple',	'2025-04-30',	NULL,	3,	45000,	NULL,	NULL,	NULL,	NULL,	'products/G105O2NgmTBABBmNzogcns6XjJijdP6Z9vA28BH7.jpg',	1,	'2025-04-30 14:27:23',	'2025-04-30 14:27:23'),
(9,	2,	'P_009',	'Tent',	'107',	4,	1,	'Wildcraft',	'2025-04-23',	NULL,	4,	23000,	NULL,	NULL,	NULL,	NULL,	'products/UewhyyG2CSCQXi6N0qgG7ajM6Ch0yC0rex7h3fIz.jpg',	1,	'2025-04-30 14:28:58',	'2025-06-12 11:33:23'),
(10,	1,	'P_009',	'Tent',	'107',	4,	1,	'Wildcraft',	'2025-04-23',	NULL,	4,	23000,	NULL,	NULL,	NULL,	NULL,	'products/UewhyyG2CSCQXi6N0qgG7ajM6Ch0yC0rex7h3fIz.jpg',	1,	'2025-04-30 14:38:38',	'2025-04-30 14:38:38'),
(11,	2,	'P_001',	'Dell Xperia 360',	'100',	1,	1,	'dell',	'2025-04-22',	NULL,	15,	50000,	0,	0,	'2025-04-08',	'2025-04-30',	'products/S5Hh8mBQPuV9lHdSuVqffPpK57loRJukRAIW0aXE.jpg',	1,	'2025-04-30 14:41:29',	'2025-06-16 13:54:01'),
(12,	7,	'P_012',	'TestProduct',	'TestProduct',	2,	3,	'test',	'2025-05-01',	'test',	4,	3000,	NULL,	NULL,	'2025-05-01',	NULL,	'products/SeJi61BjzCoouNT2qlp81zXDbSlU4UZ8qg5Sq3h6.jpg',	1,	'2025-05-01 06:01:37',	'2025-06-11 10:58:28'),
(13,	7,	'P_013',	'NewProduct',	'NewProduct',	1,	1,	'test',	'2025-05-01',	'test',	10,	2000000,	0,	0,	'2025-04-01',	'2032-06-01',	'products/Z3qgPGFmMM7DexbRyO4fARrlucLZ4nD5KChnDVzd.jpg',	1,	'2025-05-01 06:30:47',	'2025-05-06 11:09:25'),
(14,	7,	'P_014',	'Test EProduct',	'00Test',	1,	3,	'test',	'2025-05-05',	'Test stock',	15,	20000,	NULL,	NULL,	'2025-05-05',	NULL,	NULL,	1,	'2025-05-05 07:41:08',	'2025-05-28 11:14:36'),
(15,	7,	'P_001',	'Dell Xperia 360',	'100',	1,	1,	'dell',	'2025-04-22',	NULL,	9,	50000,	0,	0,	'2025-04-08',	'2025-04-30',	'products/S5Hh8mBQPuV9lHdSuVqffPpK57loRJukRAIW0aXE.jpg',	1,	'2025-05-06 12:08:31',	'2025-05-06 12:08:31'),
(16,	7,	'P_016',	'Test product',	't009',	2,	1,	'test supplyer',	'2025-05-07',	'test stock',	50,	500000,	NULL,	NULL,	NULL,	NULL,	'products/NJdIMH1IrfygjfdGnvHybjOcFBmsns0zu8Aji6cF.jpg',	1,	'2025-05-07 12:21:11',	'2025-05-07 12:21:11'),
(17,	1,	'P_014',	'Test EProduct',	'00Test',	1,	3,	'test',	'2025-05-05',	'Test stock',	7,	20000,	0,	0,	'2025-05-05',	'2025-05-28',	'products/7OTmcTvBefpMRZwSIp79zK8YoEeWcEv2uX9dPk0O.jpg',	1,	'2025-05-27 04:51:27',	'2025-06-12 17:52:20'),
(18,	1,	'P_018',	'Makeup brush',	'123',	3,	1,	'Supplier A',	'2025-05-28',	'ZADFHGFJ',	0,	100,	0,	0,	'2025-05-28',	'2025-05-29',	'products/tHBJkMIvUF0oComYiwucrwla7rb34PlZT2FI0iOY.png',	1,	'2025-05-28 10:18:53',	'2025-06-11 05:27:57'),
(19,	2,	'P_019',	'Makeup',	'1234',	1,	3,	'Supplier b',	'2025-05-29',	'zSDgsdbg',	1,	100,	NULL,	NULL,	'2025-05-29',	'2025-05-30',	NULL,	1,	'2025-05-28 10:39:14',	'2025-05-28 10:39:14'),
(20,	1,	'P_020',	'Makeup blush',	'123s',	6,	1,	'Supplier A',	'2025-05-28',	'zgfzxgdxz',	20,	500,	0,	0,	'2025-05-28',	'2025-06-30',	'products/V5NWTXqSPmTNDWNiLdmKjfKWbsSwBI9fSamUon3U.png',	1,	'2025-05-28 10:55:02',	'2025-06-11 10:18:07'),
(21,	2,	'P_021',	'Blush',	'1b',	6,	1,	'Supplier b',	'2025-05-28',	'ghkjhkhjbk',	99,	500,	NULL,	NULL,	'2025-05-28',	'2026-01-30',	NULL,	1,	'2025-05-28 11:45:18',	'2025-06-03 06:39:37'),
(22,	2,	'P_004',	'Headphones',	'103',	1,	1,	'JBL',	'2025-04-08',	NULL,	1,	10000,	0,	0,	'2025-04-30',	'2025-05-03',	'products/ccRhXyf9zvuwrpn4jOfnLmplC555lQnhsRaQ0738.jpg',	1,	'2025-06-03 05:08:13',	'2025-06-03 05:08:13'),
(23,	1,	'P_023',	'dress',	'1002003232',	2,	1,	'store A',	'2025-06-03',	'hghvjhkj jkkjbgkj',	10,	499,	0,	0,	'2025-06-03',	'2027-03-06',	'products/3HloTu2uYca0XzyCd2edwZkGpVHiPJGmDe0JQVjc.jpg',	1,	'2025-06-03 05:35:51',	'2025-06-11 10:16:35'),
(24,	1,	'P_024',	'dress',	'10022',	2,	1,	'store A',	'2025-06-03',	'low stock testing',	6,	499,	0,	0,	'2025-06-03',	'2027-03-06',	'products/oZRLM0RwLJptx5kR4T0auhC6n7etHs1mRVrJcI5u.png',	1,	'2025-06-03 05:46:26',	'2025-06-17 09:14:13'),
(25,	1,	'P_021',	'Blush',	'1b',	6,	1,	'Supplier b',	'2025-05-28',	'ghkjhkhjbk',	13,	500,	0,	0,	'2025-05-28',	'2026-01-30',	'products/XgxUbDDVuVqFO9lJMH5OrSX0G3Opb06ixFng2QPC.png',	1,	'2025-06-03 06:39:37',	'2025-06-17 09:10:23'),
(26,	7,	'P_026',	'Makeup',	'1234s',	6,	1,	'Supplier C',	'2025-06-05',	NULL,	5,	5,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-05 13:00:35',	'2025-06-05 13:00:35'),
(27,	2,	'P_027',	'Makeup',	'123sssss',	3,	1,	'Supplier A',	'2025-06-11',	'sdgfsdgsd',	63,	459,	NULL,	NULL,	'2025-06-11',	'2027-07-10',	NULL,	1,	'2025-06-11 10:56:41',	'2025-06-11 10:56:41'),
(28,	2,	'P_012',	'TestProduct',	'TestProduct',	2,	3,	'test',	'2025-05-01',	'test',	5,	3000,	NULL,	NULL,	'2025-05-01',	NULL,	'products/SeJi61BjzCoouNT2qlp81zXDbSlU4UZ8qg5Sq3h6.jpg',	1,	'2025-06-11 10:58:28',	'2025-06-11 10:58:28'),
(29,	2,	'P_003',	'Bed',	'102',	3,	1,	'Godrej',	'2025-04-29',	NULL,	1,	20000,	0,	0,	'2025-03-31',	'2025-05-10',	'products/9JZ5fXd9DFaAzDERahzPkc4GlnQlHn361G2SQt66.jpg',	1,	'2025-06-11 10:58:35',	'2025-06-11 10:58:35'),
(30,	1,	'P_030',	'Makeup',	'saf15245',	1,	1,	NULL,	'2025-06-11',	'sdsgdg',	11,	1,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-11 13:16:24',	'2025-06-11 13:16:24'),
(31,	1,	'P_031',	'Makeup',	'123sdf',	2,	3,	NULL,	'2025-06-11',	NULL,	11111,	1,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-11 13:17:01',	'2025-06-11 13:17:01'),
(32,	1,	'P_032',	'dress',	'1234sl[kp[',	2,	3,	NULL,	'2025-06-18',	NULL,	1111,	1,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-11 13:20:45',	'2025-06-11 13:20:45'),
(33,	1,	'P_033',	'Makeup',	'123ljugv',	3,	3,	NULL,	'2025-06-11',	NULL,	1,	0,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-11 13:21:31',	'2025-06-11 13:21:31'),
(34,	1,	'P_034',	'laptop',	'11dell',	1,	1,	NULL,	'2025-06-05',	NULL,	999999999,	10000,	0,	0,	NULL,	NULL,	NULL,	1,	'2025-06-12 04:49:15',	'2025-06-12 04:55:07');

DROP TABLE IF EXISTS `store_employees`;
CREATE TABLE `store_employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint NOT NULL,
  `store_id` bigint NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_employees_username_unique` (`username`),
  UNIQUE KEY `store_employees_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `store_employees` (`id`, `name`, `first_name`, `last_name`, `username`, `email`, `role_id`, `store_id`, `profile_image`, `phone`, `emp_code`, `dob`, `gender`, `nationality`, `joining_date`, `emergency_no1`, `emergency_no2`, `address`, `city`, `state`, `country`, `zipcode`, `status`, `created_at`, `updated_at`) VALUES
(1,	'employee 1',	'employee',	'1',	'emp1',	'emp1@gmail',	5,	1,	'uploads/o3Qf3gbffgiUr0XmQYmphZWVujZ8dcHHtb5u6tf6.jpg',	'11221122651465',	'xcb',	NULL,	NULL,	NULL,	'12-06-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-04-29 01:38:33',	'2025-06-12 10:58:16'),
(2,	'employee 2',	'employee',	'2',	'emp2',	'emp2@gmail.com',	5,	2,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-04-29 01:49:49',	'2025-04-29 02:50:16'),
(4,	'employee 3',	'employee',	'3',	'emp3',	'emp3@gmail.com',	5,	1,	NULL,	NULL,	NULL,	NULL,	'select_gender',	'Select Nationality',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'Select Nationality',	NULL,	1,	'2025-04-29 02:03:56',	'2025-04-29 02:03:56'),
(5,	'employee 4',	'employee',	'4',	'emp4',	'emp4@gmail.com',	5,	2,	NULL,	NULL,	NULL,	NULL,	'select_gender',	'Select Nationality',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'Select Nationality',	NULL,	1,	'2025-04-29 02:08:20',	'2025-04-29 02:08:20'),
(11,	'employee 6',	'employee',	'6',	'emp6',	'emp6@gmail.com',	5,	2,	NULL,	NULL,	NULL,	NULL,	'select_gender',	'Select Nationality',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'Select Nationality',	NULL,	1,	'2025-04-29 03:35:00',	'2025-04-29 03:35:00'),
(12,	'Test staff staff',	'Test staff',	'staff',	'teststaff@gmail.com',	'teststaff@gmail.com',	5,	7,	NULL,	'6474784838',	'05',	'02-05-2025',	'Male',	'India',	'02-05-2025',	'7675657567',	'7675657567',	'e32',	'California city',	'California',	'USA',	'110090',	1,	'2025-05-02 07:05:03',	'2025-05-02 07:05:03'),
(14,	'a b',	'a',	'b',	'b',	'1ab@gmail',	5,	1,	'uploads/Pfwh5zevUkDFkThKY2VU3LbfY7e2rkJcjcpzYbfE.webp',	'9356780943',	'345bc',	NULL,	NULL,	NULL,	'12-06-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-03 07:29:32',	'2025-06-12 13:15:12'),
(16,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'bkjj',	'sadmin@sadmin',	5,	2,	NULL,	NULL,	'123',	'05-06-2025',	'select_gender',	'Select Nationality',	NULL,	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	NULL,	'Select Nationality',	'fghj',	1,	'2025-06-05 06:58:09',	'2025-06-05 06:58:09'),
(17,	'dsdggg dfgdgdf',	'dsdggg',	'dfgdgdf',	'dfgdfg',	'fdgdfgdfg@g',	5,	1,	'uploads/Y4gIKvabEMR3nDQBvDDSeXIZjA74kMAuUN8Odnwh.jpg',	'fdgdfg',	NULL,	'05-06-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-05 10:56:40',	'2025-06-10 10:59:26'),
(18,	'XZCBXCB XZCBXCB',	'XZCBXCB',	'XZCBXCB',	'XCB',	'ZXVXV@G',	5,	1,	'uploads/JBHqaDHqvnULEkmpGhBvQ0RPskTaQGyZr6PlrE1x.jpg',	'4353541211313',	'hjdk123',	NULL,	NULL,	NULL,	'12-06-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-05 11:15:27',	'2025-06-12 07:24:27'),
(19,	'test test',	'test',	'test',	'test11',	'test11@gma',	5,	1,	'uploads/d0jasLkdRL7n41FPYwWLedJUP67X1wJWr6j2OBnF.jpg',	'12345678',	'51',	'17-06-2025',	'Male',	'India',	'10-06-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	'2025-06-10 11:11:09',	'2025-06-10 11:11:52');

DROP TABLE IF EXISTS `stores`;
CREATE TABLE `stores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stores` (`id`, `name`, `address`, `city`, `state`, `zipcode`, `phone`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Store A',	'abcdefgh',	'admincity',	'delhi',	'110011',	'12345678',	'storeA@gmail.com',	1,	'2025-04-29 00:31:55',	'2025-06-11 09:36:21'),
(2,	'Store B',	'abcdefgh',	'sssc',	'Maharashtra',	'110011',	'1234556',	'StoreB@gmail.com',	1,	'2025-04-29 00:33:50',	'2025-04-29 00:33:50'),
(3,	'Store C',	'wdfwdf',	'wwewed',	'wew',	'234234',	'234232323432',	'stoec@gmail.com',	1,	'2025-04-29 00:39:08',	'2025-05-26 07:30:17'),
(7,	'Store D',	'Sector 11',	'Noida',	'utttar pradesh',	'201301',	'6756745657',	'ravi.kumar2@infoicon.in',	1,	'2025-05-01 05:17:20',	'2025-05-01 05:17:20');

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `units` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Pcs',	'pcs',	1,	'2025-04-29 00:35:46',	'2025-04-30 01:03:31'),
(3,	'Unit',	'unit',	1,	'2025-04-30 01:21:38',	'2025-06-12 05:25:15');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_store` int DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `selected_store_id` bigint unsigned DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `username`, `email`, `email_verified_at`, `role_id`, `password`, `profile_image`, `phone`, `emp_code`, `dob`, `gender`, `nationality`, `joining_date`, `emergency_no1`, `emergency_no2`, `address`, `city`, `state`, `country`, `zipcode`, `assigned_store`, `status`, `selected_store_id`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Super Admin',	'Super',	'Admin',	'SuperAdmin',	'superadmin@gmail.com',	'2025-04-29 00:30:27',	1,	'$2y$12$pg3CBWG.3WfjJBibnK4ZLO8Ox3TxI6uBeioZVuyRhC.U6ODQv/u4u',	'uploads/EcxWx70q4c83C6v6ri0yjH6ICjib71hSdk9ASvje.jpg',	'12345678910',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'12345678',	NULL,	1,	1,	'oGZzJctFEJAy9LuZR56NzcQbqDPZC8liFOmbbxTkhZgh9qnBKx3HicN3yBM5',	'2025-04-29 00:30:27',	'2025-06-23 12:48:13'),
(2,	'Admin Admin',	'Admin',	'Admin',	'Admin',	'admin@gmail.com',	'2025-04-29 00:30:27',	2,	'$2y$12$7Og4sNUcxK.kcmY4/77yCua2uHWbsZCvxOs088E0TN9jVeFaJGV5.',	'uploads/GvNbSiFc7StVLsK6eDSkjGyHINNZorg2yDxLQPtZ.png',	'8888888880',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'jhjhjkhk',	NULL,	NULL,	NULL,	NULL,	NULL,	1,	1,	'E9FxdgCUhkQfmKfTdiHbFghI2ZYqtsw4vtZWFkeLjlMLoua5ASjqXGlbeEO3',	'2025-04-29 00:30:28',	'2025-06-17 09:06:47'),
(3,	'Store Manager A',	'Store',	'Manager A',	'StoreManager',	'storemanager@gmail.com',	'2025-04-29 00:30:28',	3,	'$2y$12$gQuIU1KLJMt1mElqvZSRtONwHTpN2bx0R8J44J354fojzj6bSraSm',	'uploads/XOYxiHvNZPeSQlnFCmFfEwUoljar6D0HN69c6IJ4.png',	'1122334455',	'zxc',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	0,	1,	'wNOwVoMSv0sMVHwfXjDy84EHS4cUOwwyoLamQDrYLz4JGvjIGR7YmqO9b4Q7',	'2025-04-29 00:30:28',	'2025-06-12 13:14:07'),
(4,	'StoreManager B',	'StoreManager',	'B',	'bb1',	'storemanagerB@gmail.com',	NULL,	3,	'$2y$12$GHq0QMPgrPM0wEI0IPkLBupOHPpffg4Tok8xQDNoxmiwlhMkI/yo6',	'uploads/e0aceBMx9E3QbVNaDrMkzQurZ2qL08bhaeDkRCzB.jpg',	'12345678910',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	2,	1,	2,	NULL,	'2025-04-29 01:15:16',	'2025-06-05 07:04:34'),
(5,	'StoreManager C',	'StoreManager',	'C',	'bw11',	'storemanagerC@gmail.com',	NULL,	3,	'$2y$12$167ERsc6J/3gzC/uxJ7uWuailJhlxCFNHXIZwGYyQxbm/Njg9XnU2',	NULL,	'12345678',	'6',	'02-04-2025',	'Male',	'India',	'30-04-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	3,	1,	3,	NULL,	'2025-04-30 14:40:53',	'2025-05-28 10:31:50'),
(6,	'Test Store Manager',	'Test Store',	'Manager',	'teststoremanager@gmail.com',	'teststoremanager@gmail.com',	NULL,	3,	'$2y$12$BRu2.sqW4Fn9PXzyzwR00erv/.oxUwCwd4XgAhNbvJqWN1DqaIaMW',	'uploads/RFf0FUAeRSttFSTYxGU3OC4SXKsoWDnrLqQIWdKj.png',	'6777556776',	'03',	'01-06-2004',	'Male',	'UK',	'01-05-2025',	'6777556776',	'6777556776',	'noida sector 8',	'Noida',	NULL,	'UK',	'201301',	7,	1,	7,	NULL,	'2025-05-01 05:25:55',	'2025-05-01 05:30:08'),
(8,	'Store manager test',	'Store',	'manager test',	'storemanagertest@gmail.com',	'storemanagertest@gmail.com',	NULL,	3,	'$2y$12$aTyEsRuPcxxhhDut/V9Xi.PNEFefX0VQKzZMOgePUgaDPv8V7hxXK',	NULL,	'7890252508',	'08',	'01-06-2003',	'Male',	'India',	'05-05-2025',	'6777556776',	'6777556776',	'e32',	'California city',	'California',	'USA',	'201301',	7,	1,	7,	NULL,	'2025-05-05 06:05:46',	'2025-05-05 06:06:35'),
(9,	'StoreManager D',	'StoreManager',	'D',	'StoreManager12',	'storemanagerD@gmail.com',	NULL,	3,	'$2y$12$FxHCidEE3Gr2.3ErPsmY/Ogv//xJabLrEdZEmwZbr5ZLACWR9hbHW',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	7,	1,	7,	NULL,	'2025-05-28 10:37:48',	'2025-06-03 05:06:46'),
(11,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'sdgadfg',	'ZSFs@gmail',	NULL,	1,	'$2y$12$i8m1hzgEJ6PYprQlvKYVy.iAMEZuqug5WhitkzlMdYHSA8jS9ZiZ2',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	NULL,	NULL,	'fghj',	NULL,	1,	NULL,	NULL,	'2025-06-10 11:30:27',	'2025-06-10 11:30:27'),
(14,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'zx',	'xcbgzcf@gmai',	NULL,	2,	'$2y$12$GWj/dLr1dz3HLLPtZTQPIOZRruOwYNfV5p69v0wkyU49ETfNfovdS',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	'sfgjsfjgsjg',	'India',	'fghj',	NULL,	1,	NULL,	NULL,	'2025-06-10 11:50:29',	'2025-06-10 11:50:29'),
(16,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'SuZXDFCD',	'asfASFASDF@GM',	NULL,	1,	'$2y$12$cpWwXF5s5DkgwxGbINML3.pkd2olfYCQwjNpAhUEvtv88MrogEs7y',	'uploads/wrdoYZsHBlfvwqMH2sbMM8zncRa5WjQvn3ETm4P6.jpg',	'123456789111',	'dshgzdfa',	'12-06-2025',	'Male',	NULL,	'11-06-2025',	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	NULL,	NULL,	'fghj',	NULL,	1,	NULL,	NULL,	'2025-06-12 07:13:17',	'2025-06-12 07:13:57');

-- 2025-06-30 06:38:35
