-- Adminer 4.8.1 MySQL 8.0.46-0ubuntu0.22.04.4 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `batch_purchase_charge_invoice`;
CREATE TABLE `batch_purchase_charge_invoice` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_purchase_id` bigint unsigned NOT NULL,
  `supplier_purchase_invoice_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bp_chg_inv_unique` (`batch_purchase_id`,`supplier_purchase_invoice_id`),
  KEY `bp_chg_inv_spi_fk` (`supplier_purchase_invoice_id`),
  CONSTRAINT `bp_chg_inv_batch_fk` FOREIGN KEY (`batch_purchase_id`) REFERENCES `batch_purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bp_chg_inv_spi_fk` FOREIGN KEY (`supplier_purchase_invoice_id`) REFERENCES `supplier_purchase_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `batch_purchase_charge_invoice` (`id`, `batch_purchase_id`, `supplier_purchase_invoice_id`) VALUES
(1,	1,	3),
(2,	1,	4);

DROP TABLE IF EXISTS `batch_purchase_items`;
CREATE TABLE `batch_purchase_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_purchase_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint unsigned DEFAULT NULL,
  `category_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qty` int unsigned NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `additional_charges` decimal(15,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(15,2) NOT NULL,
  `final_unit` decimal(15,4) NOT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `batch_purchase_items_batch_purchase_id_foreign` (`batch_purchase_id`),
  CONSTRAINT `batch_purchase_items_batch_purchase_id_foreign` FOREIGN KEY (`batch_purchase_id`) REFERENCES `batch_purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `batch_purchase_items` (`id`, `batch_purchase_id`, `product_id`, `product_name`, `sku`, `category_id`, `category_name`, `qty`, `unit_price`, `amount`, `additional_charges`, `grand_total`, `final_unit`, `is_taxable`, `tax_rate`, `tax_amount`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_002',	'FUJIFILM DIGITAL CAMERA X100VI',	'DIG-CAM-FUJIFILM-X100VI-01',	1,	'Electronics',	10,	16750.00,	167500.00,	13500.00,	181000.00,	18100.0000,	1,	15.00,	25125.00,	'2026-10-20 22:44:29',	'2026-10-20 22:44:29'),
(2,	1,	'P_006',	'GIGABYTE 27 inch QHD OLED Computer Monitor',	'MON-OLED-GIGABYTE-27-2K-1440-01',	3,	'Computer Systems',	20,	18500.00,	370000.00,	30250.00,	400250.00,	20012.5000,	1,	15.00,	55500.00,	'2026-10-20 22:44:29',	'2026-10-20 22:44:29'),
(3,	1,	'P_005',	'KOOFORWAY Mini PC Ryzen 7 8745HS 16GB',	'MINI-PC-KOOFORWAY-16GB-R7-01',	3,	'Computer Systems',	20,	65250.00,	1305000.00,	106250.00,	1411250.00,	70562.5000,	1,	15.00,	195750.00,	'2026-10-20 22:44:29',	'2026-10-20 22:44:29');

DROP TABLE IF EXISTS `batch_purchase_product_invoice`;
CREATE TABLE `batch_purchase_product_invoice` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `batch_purchase_id` bigint unsigned NOT NULL,
  `supplier_purchase_invoice_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `bp_prod_inv_unique` (`batch_purchase_id`,`supplier_purchase_invoice_id`),
  KEY `bp_prod_inv_spi_fk` (`supplier_purchase_invoice_id`),
  CONSTRAINT `bp_prod_inv_batch_fk` FOREIGN KEY (`batch_purchase_id`) REFERENCES `batch_purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `bp_prod_inv_spi_fk` FOREIGN KEY (`supplier_purchase_invoice_id`) REFERENCES `supplier_purchase_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `batch_purchase_product_invoice` (`id`, `batch_purchase_id`, `supplier_purchase_invoice_id`) VALUES
(1,	1,	2);

DROP TABLE IF EXISTS `batch_purchases`;
CREATE TABLE `batch_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint unsigned NOT NULL,
  `batch_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_date` date NOT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `charge_invoices_subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `allocated_additional_charges` decimal(15,2) NOT NULL DEFAULT '0.00',
  `draft_reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `batch_purchases_batch_number_unique` (`batch_number`),
  KEY `batch_purchases_store_id_foreign` (`store_id`),
  CONSTRAINT `batch_purchases_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `batch_purchases` (`id`, `store_id`, `batch_number`, `batch_date`, `status`, `tax_exempt`, `subtotal`, `tax_amount`, `total`, `charge_invoices_subtotal`, `allocated_additional_charges`, `draft_reference`, `notes`, `created_at`, `updated_at`) VALUES
(1,	1,	'BP-BR000001',	'2026-10-21',	'confirmed',	0,	1842500.00,	276375.00,	2118875.00,	150000.00,	150000.00,	NULL,	NULL,	'2026-10-20 22:44:29',	'2026-10-20 22:44:29');

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Electronics',	'electronics',	1,	'2026-08-08 00:42:23',	'2026-08-08 00:57:52'),
(2,	'Appliances',	'appliances',	1,	'2026-08-08 00:42:41',	'2026-08-08 00:46:01'),
(3,	'Computer Systems',	'computer-systems',	1,	'2026-08-08 00:43:15',	'2026-08-08 00:46:17'),
(4,	'Intangibles',	'intangibles',	1,	'2026-08-08 03:44:43',	'2026-08-08 03:44:43');

DROP TABLE IF EXISTS `customer_ledger_entries`;
CREATE TABLE `customer_ledger_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned DEFAULT NULL,
  `payment_id` bigint unsigned DEFAULT NULL,
  `entry_type` enum('invoice','payment','adjustment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entry_date` timestamp NULL DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` double NOT NULL DEFAULT '0',
  `credit` double NOT NULL DEFAULT '0',
  `balance_after` double NOT NULL DEFAULT '0',
  `meta` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_ledger_entries_customer_id_foreign` (`customer_id`),
  KEY `customer_ledger_entries_order_id_foreign` (`order_id`),
  KEY `customer_ledger_entries_payment_id_foreign` (`payment_id`),
  CONSTRAINT `customer_ledger_entries_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_ledger_entries_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `customer_ledger_entries_payment_id_foreign` FOREIGN KEY (`payment_id`) REFERENCES `customer_payments` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `customer_payments`;
CREATE TABLE `customer_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  `amount` double NOT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `reference_no` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_by` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `customer_payments_customer_id_foreign` (`customer_id`),
  KEY `customer_payments_order_id_foreign` (`order_id`),
  CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `customer_payments_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `credit_limit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` tinyint unsigned DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` (`id`, `customer_name`, `email`, `phone`, `country`, `city`, `address`, `description`, `credit_limit`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Amazong Primus Ltd',	'whydoineed@email.org',	'8761234567',	'Jamaica',	'Seriously',	'15 Is This Really Street',	NULL,	0.00,	1,	'2026-10-20 22:53:07',	'2026-10-20 22:53:07'),
(2,	'Treadstone Operations',	'secure@tops.com',	'8763334456',	'Oz',	'Not Kansas',	'Somewhere Over The Rainbow',	NULL,	0.00,	1,	'2026-10-20 22:57:05',	'2026-10-20 22:57:05');

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `gl_accounts`;
CREATE TABLE `gl_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `gl_accounts_code_unique` (`code`),
  KEY `gl_accounts_parent_id_foreign` (`parent_id`),
  CONSTRAINT `gl_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `gl_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `gl_accounts` (`id`, `code`, `name`, `type`, `parent_id`, `is_active`, `description`, `created_at`, `updated_at`) VALUES
(1,	'1000',	'Cash on Hand',	'asset',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-20 04:35:49'),
(2,	'1100',	'Accounts Receivable',	'asset',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(3,	'1200',	'Inventory',	'asset',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(4,	'1300',	'Prepaid Expenses',	'asset',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(5,	'2000',	'Accounts Payable',	'liability',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(6,	'2100',	'Accrued Liabilities',	'liability',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(7,	'3000',	'Owner\'s Equity',	'equity',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(8,	'3100',	'Retained Earnings',	'equity',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(9,	'4000',	'Sales Revenue',	'revenue',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(10,	'5000',	'Cost of Goods Sold',	'expense',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(11,	'5100',	'Operating Expenses',	'expense',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(12,	'5200',	'Other Expenses',	'expense',	NULL,	1,	NULL,	'2026-05-14 06:58:04',	'2026-05-14 06:58:04'),
(13,	'4010',	'Sales Revenue Store A',	'revenue',	9,	1,	NULL,	'2026-06-12 00:26:04',	'2026-06-12 00:26:04'),
(14,	'2200',	'Taxation Payable',	'liability',	NULL,	1,	NULL,	'2026-06-12 01:18:31',	'2026-06-12 01:18:31'),
(15,	'5010',	'Import Duty',	'expense',	10,	1,	NULL,	'2026-07-08 06:23:48',	'2026-07-08 06:25:32'),
(16,	'5020',	'Brokerage',	'expense',	10,	1,	NULL,	'2026-07-08 06:26:20',	'2026-07-08 06:26:20'),
(17,	'5030',	'Truckage',	'expense',	10,	1,	NULL,	'2026-07-08 06:26:46',	'2026-07-08 06:26:46');

DROP TABLE IF EXISTS `goods_receipt_note_items`;
CREATE TABLE `goods_receipt_note_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `goods_receipt_note_id` bigint unsigned NOT NULL,
  `product_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty_received` int unsigned NOT NULL DEFAULT '1',
  `unit_cost` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_receipt_note_items_goods_receipt_note_id_foreign` (`goods_receipt_note_id`),
  CONSTRAINT `goods_receipt_note_items_goods_receipt_note_id_foreign` FOREIGN KEY (`goods_receipt_note_id`) REFERENCES `goods_receipt_notes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `goods_receipt_note_items` (`id`, `goods_receipt_note_id`, `product_id`, `product_name`, `qty_received`, `unit_cost`, `line_total`, `is_taxable`, `tax_rate`, `tax_amount`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_001',	'JSW 42\" LED TV',	8,	25000.00,	200000.00,	1,	15.00,	30000.00,	'2026-10-16 04:08:29',	'2026-10-16 04:08:29'),
(2,	1,	'P_002',	'FUJIFILM DIGITAL CAMERA X100VI',	15,	15500.00,	232500.00,	1,	15.00,	34875.00,	'2026-10-16 04:08:29',	'2026-10-16 04:08:29');

DROP TABLE IF EXISTS `goods_receipt_notes`;
CREATE TABLE `goods_receipt_notes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned DEFAULT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `supplier_purchase_invoice_id` bigint unsigned DEFAULT NULL,
  `grn_number` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `grn_date` date NOT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'received',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_receipt_notes_grn_number_unique` (`grn_number`),
  KEY `goods_receipt_notes_supplier_id_foreign` (`supplier_id`),
  KEY `goods_receipt_notes_store_id_foreign` (`store_id`),
  KEY `goods_receipt_notes_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `goods_receipt_notes_supplier_purchase_invoice_id_foreign` (`supplier_purchase_invoice_id`),
  CONSTRAINT `goods_receipt_notes_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_receipt_notes_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_receipt_notes_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `goods_receipt_notes_supplier_purchase_invoice_id_foreign` FOREIGN KEY (`supplier_purchase_invoice_id`) REFERENCES `supplier_purchase_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `goods_receipt_notes` (`id`, `supplier_id`, `store_id`, `purchase_order_id`, `supplier_purchase_invoice_id`, `grn_number`, `grn_date`, `status`, `subtotal`, `tax_amount`, `total`, `tax_exempt`, `notes`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	1,	1,	'GRN-BR000001',	'2026-10-16',	'received',	432500.00,	64875.00,	497375.00,	0,	NULL,	'2026-10-16 04:08:29',	'2026-10-16 04:08:29');

DROP TABLE IF EXISTS `histories`;
CREATE TABLE `histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned DEFAULT NULL,
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `event` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `auditable_id` bigint unsigned NOT NULL,
  `requested_data` json DEFAULT NULL,
  `new_values` json DEFAULT NULL,
  `old_values` json DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `histories_user_id_foreign` (`user_id`),
  CONSTRAINT `histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `histories` (`id`, `user_id`, `key`, `event`, `auditable_type`, `auditable_id`, `requested_data`, `new_values`, `old_values`, `ip_address`, `user_agent`, `url`, `created_at`, `updated_at`) VALUES
(1,	1,	'store_form',	'created',	'store',	1,	'{\"city\": \"Spanish Town\", \"name\": \"Burke Road\", \"email\": \"burke.road@testdata.com\", \"phone\": \"18761236587\", \"state\": \"St Catherine\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"status\": \"1\", \"address\": \"123 Burke Road\", \"zipcode\": \"00000\", \"grn_prefix\": \"GRN-BR\", \"invoice_prefix\": \"INV-BR\", \"receipt_prefix\": \"RCPT-BR\", \"grn_start_number\": \"1\", \"debit_note_prefix\": \"DN-BR\", \"credit_note_prefix\": \"CN-BR\", \"receipt_start_number\": \"1\", \"batch_purchase_prefix\": \"BP-BR\", \"purchase_order_prefix\": \"POBR-BR\", \"purchase_return_prefix\": \"PR-BR\", \"debit_note_start_number\": \"1\", \"purchase_invoice_prefix\": \"PINV-BR\", \"credit_note_start_number\": \"1\", \"sale_invoice_start_number\": \"1\", \"batch_purchase_start_number\": \"1\", \"purchase_order_start_number\": \"1\", \"purchase_return_start_number\": \"1\", \"purchase_invoice_start_number\": \"1\"}',	'{\"id\": 1, \"city\": \"Spanish Town\", \"name\": \"Burke Road\", \"email\": \"burke.road@testdata.com\", \"phone\": \"18761236587\", \"state\": \"St Catherine\", \"status\": 1, \"address\": \"123 Burke Road\", \"zipcode\": \"00000\", \"created_at\": \"2026-08-08T00:23:56.000000Z\", \"grn_prefix\": \"GRN-BR\", \"updated_at\": \"2026-08-08T00:23:56.000000Z\", \"invoice_prefix\": \"INV-BR\", \"receipt_prefix\": \"RCPT-BR\", \"next_grn_number\": 1, \"debit_note_prefix\": \"DN-BR\", \"credit_note_prefix\": \"CN-BR\", \"next_receipt_number\": 1, \"batch_purchase_prefix\": \"BP-BR\", \"purchase_order_prefix\": \"POBR-BR\", \"next_debit_note_number\": 1, \"purchase_return_prefix\": \"PR-BR\", \"next_credit_note_number\": 1, \"purchase_invoice_prefix\": \"PINV-BR\", \"next_sale_invoice_number\": 1, \"next_batch_purchase_number\": 1, \"next_purchase_order_number\": 1, \"next_purchase_return_number\": 1, \"next_purchase_invoice_number\": 1, \"current_sale_invoice_sequence\": \"######\"}',	'[]',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/store/store',	'2026-08-08 00:23:56',	'2026-08-08 00:23:56'),
(2,	1,	'store_form',	'created',	'store',	2,	'{\"city\": \"Kingston\", \"name\": \"Harbour View\", \"email\": \"harbour.view@testdata.com\", \"phone\": \"18767426589\", \"state\": \"Kingston\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"status\": \"1\", \"address\": \"77 Harbour View Drive\", \"zipcode\": \"00000\", \"grn_prefix\": \"GRN-HV\", \"invoice_prefix\": \"INV-HV\", \"receipt_prefix\": \"RCPT-HV\", \"grn_start_number\": \"1\", \"debit_note_prefix\": \"DN-HV\", \"credit_note_prefix\": \"CN-HV\", \"receipt_start_number\": \"1\", \"batch_purchase_prefix\": \"BP-HV\", \"purchase_order_prefix\": \"PO-HV\", \"purchase_return_prefix\": \"PR-HV\", \"debit_note_start_number\": \"1\", \"purchase_invoice_prefix\": \"PINV-HV\", \"credit_note_start_number\": \"1\", \"sale_invoice_start_number\": \"1\", \"batch_purchase_start_number\": \"1\", \"purchase_order_start_number\": \"1\", \"purchase_return_start_number\": \"1\", \"purchase_invoice_start_number\": \"1\"}',	'{\"id\": 2, \"city\": \"Kingston\", \"name\": \"Harbour View\", \"email\": \"harbour.view@testdata.com\", \"phone\": \"18767426589\", \"state\": \"Kingston\", \"status\": 1, \"address\": \"77 Harbour View Drive\", \"zipcode\": \"00000\", \"created_at\": \"2026-08-08T00:25:50.000000Z\", \"grn_prefix\": \"GRN-HV\", \"updated_at\": \"2026-08-08T00:25:50.000000Z\", \"invoice_prefix\": \"INV-HV\", \"receipt_prefix\": \"RCPT-HV\", \"next_grn_number\": 1, \"debit_note_prefix\": \"DN-HV\", \"credit_note_prefix\": \"CN-HV\", \"next_receipt_number\": 1, \"batch_purchase_prefix\": \"BP-HV\", \"purchase_order_prefix\": \"PO-HV\", \"next_debit_note_number\": 1, \"purchase_return_prefix\": \"PR-HV\", \"next_credit_note_number\": 1, \"purchase_invoice_prefix\": \"PINV-HV\", \"next_sale_invoice_number\": 1, \"next_batch_purchase_number\": 1, \"next_purchase_order_number\": 1, \"next_purchase_return_number\": 1, \"next_purchase_invoice_number\": 1, \"current_sale_invoice_sequence\": \"######\"}',	'[]',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/store/store',	'2026-08-08 00:25:50',	'2026-08-08 00:25:50'),
(3,	1,	'store_form',	'created',	'store',	3,	'{\"city\": \"May Pen\", \"name\": \"May Pen\", \"email\": \"may.pen@testdata.com\", \"phone\": \"18768954789\", \"state\": \"Clarendon\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"status\": \"1\", \"address\": \"39 May Pen Street\", \"zipcode\": \"00000\", \"grn_prefix\": \"GRN-MP\", \"invoice_prefix\": \"INV-MP\", \"receipt_prefix\": \"RCPT-MP\", \"grn_start_number\": \"1\", \"debit_note_prefix\": \"DN-MP\", \"credit_note_prefix\": \"CN-MP\", \"receipt_start_number\": \"1\", \"batch_purchase_prefix\": \"BP-MP\", \"purchase_order_prefix\": \"PO-MP\", \"purchase_return_prefix\": \"PR-MP\", \"debit_note_start_number\": \"1\", \"purchase_invoice_prefix\": \"PINV-MP\", \"credit_note_start_number\": \"1\", \"sale_invoice_start_number\": \"1\", \"batch_purchase_start_number\": \"1\", \"purchase_order_start_number\": \"1\", \"purchase_return_start_number\": \"1\", \"purchase_invoice_start_number\": \"1\"}',	'{\"id\": 3, \"city\": \"May Pen\", \"name\": \"May Pen\", \"email\": \"may.pen@testdata.com\", \"phone\": \"18768954789\", \"state\": \"Clarendon\", \"status\": 1, \"address\": \"39 May Pen Street\", \"zipcode\": \"00000\", \"created_at\": \"2026-08-08T00:30:09.000000Z\", \"grn_prefix\": \"GRN-MP\", \"updated_at\": \"2026-08-08T00:30:09.000000Z\", \"invoice_prefix\": \"INV-MP\", \"receipt_prefix\": \"RCPT-MP\", \"next_grn_number\": 1, \"debit_note_prefix\": \"DN-MP\", \"credit_note_prefix\": \"CN-MP\", \"next_receipt_number\": 1, \"batch_purchase_prefix\": \"BP-MP\", \"purchase_order_prefix\": \"PO-MP\", \"next_debit_note_number\": 1, \"purchase_return_prefix\": \"PR-MP\", \"next_credit_note_number\": 1, \"purchase_invoice_prefix\": \"PINV-MP\", \"next_sale_invoice_number\": 1, \"next_batch_purchase_number\": 1, \"next_purchase_order_number\": 1, \"next_purchase_return_number\": 1, \"next_purchase_invoice_number\": 1, \"current_sale_invoice_sequence\": \"######\"}',	'[]',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/store/store',	'2026-08-08 00:30:09',	'2026-08-08 00:30:09'),
(4,	1,	'category_form',	'created',	'category',	1,	'{\"name\": \"Electronics\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\"}',	'{\"id\": 1, \"name\": \"Electronics\", \"slug\": \"electronics\", \"status\": 0, \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:42:23.000000Z\"}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories',	'2026-08-08 00:42:23',	'2026-08-08 00:42:23'),
(5,	1,	'category_form',	'created',	'category',	2,	'{\"name\": \"Appliances\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\"}',	'{\"id\": 2, \"name\": \"Appliances\", \"slug\": \"appliances\", \"status\": 0, \"created_at\": \"2026-08-08T00:42:41.000000Z\", \"updated_at\": \"2026-08-08T00:42:41.000000Z\"}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories',	'2026-08-08 00:42:41',	'2026-08-08 00:42:41'),
(6,	1,	'category_form',	'created',	'category',	3,	'{\"name\": \"Computer Systems\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\"}',	'{\"id\": 3, \"name\": \"Computer Systems\", \"slug\": \"computer-systems\", \"status\": 0, \"created_at\": \"2026-08-08T00:43:15.000000Z\", \"updated_at\": \"2026-08-08T00:43:15.000000Z\"}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories',	'2026-08-08 00:43:16',	'2026-08-08 00:43:16'),
(7,	1,	'category_form',	'updated',	'category',	2,	'{\"name\": \"Appliances\", \"status\": \"1\"}',	'{\"id\": 2, \"name\": \"Appliances\", \"slug\": \"appliances\", \"status\": \"1\", \"created_at\": \"2026-08-08T00:42:41.000000Z\", \"updated_at\": \"2026-08-08T00:46:01.000000Z\"}',	'{\"id\": 2, \"name\": \"Appliances\", \"slug\": \"appliances\", \"status\": 0, \"created_at\": \"2026-08-08T00:42:41.000000Z\", \"updated_at\": \"2026-08-08T00:42:41.000000Z\"}',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories/2',	'2026-08-08 00:46:01',	'2026-08-08 00:46:01'),
(8,	1,	'category_form',	'updated',	'category',	3,	'{\"name\": \"Computer Systems\", \"status\": \"1\"}',	'{\"id\": 3, \"name\": \"Computer Systems\", \"slug\": \"computer-systems\", \"status\": \"1\", \"created_at\": \"2026-08-08T00:43:15.000000Z\", \"updated_at\": \"2026-08-08T00:46:17.000000Z\"}',	'{\"id\": 3, \"name\": \"Computer Systems\", \"slug\": \"computer-systems\", \"status\": 0, \"created_at\": \"2026-08-08T00:43:15.000000Z\", \"updated_at\": \"2026-08-08T00:43:15.000000Z\"}',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories/3',	'2026-08-08 00:46:17',	'2026-08-08 00:46:17'),
(9,	1,	'category_form',	'updated',	'category',	1,	'{\"name\": \"Electronics\", \"status\": \"1\"}',	'{\"id\": 1, \"name\": \"Electronics\", \"slug\": \"electronics\", \"status\": \"1\", \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:50:22.000000Z\"}',	'{\"id\": 1, \"name\": \"Electronics\", \"slug\": \"electronics\", \"status\": 0, \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:42:23.000000Z\"}',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories/1',	'2026-08-08 00:50:22',	'2026-08-08 00:50:22'),
(10,	1,	'category_form',	'updated',	'category',	1,	'{\"name\": \"Toots\", \"status\": \"1\"}',	'{\"id\": 1, \"name\": \"Toots\", \"slug\": \"toots\", \"status\": \"1\", \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:57:32.000000Z\"}',	'{\"id\": 1, \"name\": \"Electronics\", \"slug\": \"electronics\", \"status\": 1, \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:50:22.000000Z\"}',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories/1',	'2026-08-08 00:57:32',	'2026-08-08 00:57:32'),
(11,	1,	'category_form',	'updated',	'category',	1,	'{\"name\": \"Electronics\", \"status\": \"1\"}',	'{\"id\": 1, \"name\": \"Electronics\", \"slug\": \"electronics\", \"status\": \"1\", \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:57:52.000000Z\"}',	'{\"id\": 1, \"name\": \"Toots\", \"slug\": \"toots\", \"status\": 1, \"created_at\": \"2026-08-08T00:42:23.000000Z\", \"updated_at\": \"2026-08-08T00:57:32.000000Z\"}',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories/1',	'2026-08-08 00:57:52',	'2026-08-08 00:57:52'),
(12,	1,	'stock_form',	'created',	'stock',	1,	'{\"sku\": \"TV-LED-JSW-42-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_001\", \"category_id\": \"1\", \"description\": \"JSW 42\\\" LED TV\", \"product_name\": \"42\\\" JSW LED TV\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"75\", \"2\": \"75\", \"3\": \"75\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 1, \"sku\": \"TV-LED-JSW-42-01\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T01:05:29.000000Z\", \"is_taxable\": true, \"product_id\": \"P_001\", \"updated_at\": \"2026-08-08T01:05:29.000000Z\", \"category_id\": \"1\", \"description\": \"JSW 42\\\" LED TV\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"42\\\" JSW LED TV\", \"product_type\": \"inventory_stock\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 01:05:32',	'2026-08-08 01:05:32'),
(13,	1,	'stock_form',	'created',	'stock',	2,	'{\"sku\": \"DIG-CAM-FUJIFILM-X100VI-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_002\", \"category_id\": \"1\", \"description\": \"FUJIFILM X100VI Digital Camera (Black)\", \"product_name\": \"FUJIFILM DIGITAL CAMERA X100VI\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"60\", \"2\": \"60\", \"3\": \"60\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 2, \"sku\": \"DIG-CAM-FUJIFILM-X100VI-01\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T01:10:18.000000Z\", \"is_taxable\": true, \"product_id\": \"P_002\", \"updated_at\": \"2026-08-08T01:10:18.000000Z\", \"category_id\": \"1\", \"description\": \"FUJIFILM X100VI Digital Camera (Black)\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"FUJIFILM DIGITAL CAMERA X100VI\", \"product_type\": \"inventory_stock\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 01:10:19',	'2026-08-08 01:10:19'),
(14,	1,	'stock_form',	'created',	'stock',	3,	'{\"sku\": \"MW-INV-PANASONIC-NN-SN74PWH-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_003\", \"category_id\": \"2\", \"description\": \"Panasonic NN-SN74PWH Microwave with Cyclonic Inverter 1.6 Cu. Ft. (White)\", \"product_name\": \"Panasonic NN-SN74PWH Microwave\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"65\", \"2\": \"65\", \"3\": \"65\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 3, \"sku\": \"MW-INV-PANASONIC-NN-SN74PWH-01\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T01:17:15.000000Z\", \"is_taxable\": true, \"product_id\": \"P_003\", \"updated_at\": \"2026-08-08T01:17:15.000000Z\", \"category_id\": \"2\", \"description\": \"Panasonic NN-SN74PWH Microwave with Cyclonic Inverter 1.6 Cu. Ft. (White)\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"Panasonic NN-SN74PWH Microwave\", \"product_type\": \"inventory_stock\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 01:17:16',	'2026-08-08 01:17:16'),
(15,	1,	'stock_form',	'updated',	'stock',	1,	'{\"sku\": \"TV-LED-JSW-42-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"_method\": \"PUT\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15.00\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_001\", \"category_id\": \"1\", \"description\": \"JSW 42\\\" LED TV\", \"product_name\": \"JSW 42\\\" LED TV\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"75\", \"2\": \"75\", \"3\": \"75\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"tax\": 0, \"unit_id\": \"2\", \"discount\": 0, \"category_id\": \"1\", \"product_name\": \"JSW 42\\\" LED TV\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\"}',	'{\"id\": 1, \"tax\": null, \"cost\": \"0.00\", \"status\": 1, \"unit_id\": 2, \"discount\": null, \"quantity\": 0, \"created_at\": \"2026-08-08T01:05:29.000000Z\", \"updated_at\": \"2026-08-08T01:05:32.000000Z\", \"category_id\": 1, \"total_stock\": 0, \"average_cost\": \"0.00\", \"product_name\": \"42\\\" JSW LED TV\", \"selling_price\": \"0.00\", \"debit_account_id\": 3, \"credit_account_id\": 10, \"last_purchase_price\": \"0.00\"}',	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/update/1',	'2026-08-08 02:51:58',	'2026-08-08 02:51:58'),
(16,	1,	'stock_form',	'created',	'stock',	4,	'{\"sku\": \"RVAC-EUFY-C28-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_004\", \"category_id\": \"2\", \"description\": \"eufy C28 Robot Vacuum and Mop Combo, HydroJet Self-Cleaning Roller Mop and Floor Cleaner\", \"product_name\": \"eufy C28 Robot Vacuum and Mop Combo\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"55\", \"2\": \"55\", \"3\": \"55\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 4, \"sku\": \"RVAC-EUFY-C28-01\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T02:57:40.000000Z\", \"is_taxable\": true, \"product_id\": \"P_004\", \"updated_at\": \"2026-08-08T02:57:40.000000Z\", \"category_id\": \"2\", \"description\": \"eufy C28 Robot Vacuum and Mop Combo, HydroJet Self-Cleaning Roller Mop and Floor Cleaner\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"eufy C28 Robot Vacuum and Mop Combo\", \"product_type\": \"inventory_stock\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 02:57:43',	'2026-08-08 02:57:43'),
(17,	1,	'stock_form',	'created',	'stock',	5,	'{\"sku\": \"MINI-PC-KOOFORWAY-16GB-R7-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_005\", \"category_id\": \"3\", \"description\": \"KOOFORWAY Mini PC Ryzen 7 8745HS (Up to 4.9GHz), 16GB DDR5 RAM 1TB SSD, Mini Desktop Computer with LED Screen, Triple 4K Display, Dual 2.5G LAN, WiFi 6E, BT5.3, Win 11 Pro for Business (Silver Gray)\", \"product_name\": \"KOOFORWAY Mini PC Ryzen 7 8745HS 16GB\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"65\", \"2\": \"65\", \"3\": \"65\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 5, \"sku\": \"MINI-PC-KOOFORWAY-16GB-R7-01\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T03:08:01.000000Z\", \"is_taxable\": true, \"product_id\": \"P_005\", \"updated_at\": \"2026-08-08T03:08:01.000000Z\", \"category_id\": \"3\", \"description\": \"KOOFORWAY Mini PC Ryzen 7 8745HS (Up to 4.9GHz), 16GB DDR5 RAM 1TB SSD, Mini Desktop Computer with LED Screen, Triple 4K Display, Dual 2.5G LAN, WiFi 6E, BT5.3, Win 11 Pro for Business (Silver Gray)\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"KOOFORWAY Mini PC Ryzen 7 8745HS 16GB\", \"product_type\": \"inventory_stock\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 03:08:03',	'2026-08-08 03:08:03'),
(18,	1,	'stock_form',	'created',	'stock',	6,	'{\"sku\": \"MON-OLED-GIGABYTE-27-2K-1440-01\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_006\", \"category_id\": \"3\", \"description\": \"GIGABYTE 27 inch QHD 2K 1440P 240Hz OLED Gaming Computer Monitor GO27Q24A\", \"product_name\": \"GIGABYTE 27 inch QHD OLED Computer Monitor\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"80\", \"2\": \"80\", \"3\": \"80\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 6, \"sku\": \"MON-OLED-GIGABYTE-27-2K-1440-01\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T03:15:38.000000Z\", \"is_taxable\": true, \"product_id\": \"P_006\", \"updated_at\": \"2026-08-08T03:15:38.000000Z\", \"category_id\": \"3\", \"description\": \"GIGABYTE 27 inch QHD 2K 1440P 240Hz OLED Gaming Computer Monitor GO27Q24A\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"GIGABYTE 27 inch QHD OLED Computer Monitor\", \"product_type\": \"inventory_stock\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 03:15:40',	'2026-08-08 03:15:40'),
(19,	1,	'category_form',	'created',	'category',	4,	'{\"name\": \"Intangibles\", \"status\": \"1\"}',	'{\"id\": 4, \"name\": \"Intangibles\", \"slug\": \"intangibles\", \"status\": \"1\", \"created_at\": \"2026-08-08T03:44:43.000000Z\", \"updated_at\": \"2026-08-08T03:44:43.000000Z\"}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/categories',	'2026-08-08 03:44:43',	'2026-08-08 03:44:43'),
(20,	1,	'stock_form',	'created',	'stock',	7,	'{\"sku\": \"FEE-BROK\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": null, \"expiry_on\": null, \"is_taxable\": \"0\", \"product_id\": \"P_007\", \"category_id\": \"4\", \"description\": \"Brokerage Fee\", \"product_name\": \"Brokerage Fee\", \"product_type\": \"service\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"0\", \"2\": \"0\", \"3\": \"0\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 7, \"sku\": \"FEE-BROK\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": null, \"created_at\": \"2026-08-08T03:50:29.000000Z\", \"is_taxable\": false, \"product_id\": \"P_007\", \"updated_at\": \"2026-08-08T03:50:29.000000Z\", \"category_id\": \"4\", \"description\": \"Brokerage Fee\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"Brokerage Fee\", \"product_type\": \"service\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 03:50:30',	'2026-08-08 03:50:30'),
(21,	1,	'stock_form',	'created',	'stock',	8,	'{\"sku\": \"FEE-TRANSP-IN\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_008\", \"category_id\": \"4\", \"description\": \"Transportation Fee Inward\", \"product_name\": \"Transportation Fee Inward\", \"product_type\": \"service\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"0\", \"2\": \"0\", \"3\": \"0\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 8, \"sku\": \"FEE-TRANSP-IN\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": \"15.00\", \"created_at\": \"2026-08-08T03:52:45.000000Z\", \"is_taxable\": true, \"product_id\": \"P_008\", \"updated_at\": \"2026-08-08T03:52:45.000000Z\", \"category_id\": \"4\", \"description\": \"Transportation Fee Inward\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"Transportation Fee Inward\", \"product_type\": \"service\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 03:52:46',	'2026-08-08 03:52:46'),
(22,	1,	'stock_form',	'created',	'stock',	9,	'{\"sku\": \"DUTY-CUST-10\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": null, \"expiry_on\": null, \"is_taxable\": \"0\", \"product_id\": \"P_009\", \"category_id\": \"4\", \"description\": \"Customs Duty 10%\", \"product_name\": \"Customs Duty 10%\", \"product_type\": \"service\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"0\", \"2\": \"0\", \"3\": \"0\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 9, \"sku\": \"DUTY-CUST-10\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": null, \"created_at\": \"2026-08-08T03:56:25.000000Z\", \"is_taxable\": false, \"product_id\": \"P_009\", \"updated_at\": \"2026-08-08T03:56:25.000000Z\", \"category_id\": \"4\", \"description\": \"Customs Duty 10%\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"Customs Duty 10%\", \"product_type\": \"service\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 03:56:26',	'2026-08-08 03:56:26'),
(23,	1,	'stock_form',	'created',	'stock',	10,	'{\"sku\": \"DUTY-CUST-20\", \"price\": \"0.00\", \"_token\": \"N3XvMRhYmUnfmIHgAcIRi8MtP8K0Tj5XeSvkkBgg\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": null, \"expiry_on\": null, \"is_taxable\": \"0\", \"product_id\": \"P_010\", \"category_id\": \"4\", \"description\": \"Customs Duty 20%\", \"product_name\": \"Customs Duty 20%\", \"product_type\": \"service\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"0\", \"2\": \"0\", \"3\": \"0\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"id\": 10, \"sku\": \"DUTY-CUST-20\", \"tax\": null, \"cost\": 0, \"price\": 0, \"images\": null, \"unit_id\": \"2\", \"discount\": null, \"quantity\": 0, \"store_id\": 1, \"tax_rate\": null, \"created_at\": \"2026-08-08T03:58:06.000000Z\", \"is_taxable\": false, \"product_id\": \"P_010\", \"updated_at\": \"2026-08-08T03:58:06.000000Z\", \"category_id\": \"4\", \"description\": \"Customs Duty 20%\", \"expiry_date\": null, \"average_cost\": 0, \"product_name\": \"Customs Duty 20%\", \"product_type\": \"service\", \"selling_price\": 0, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	NULL,	'69.79.197.183',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:153.0) Gecko/20100101 Firefox/153.0',	'http://eps.infoicon.co.in:201/admin/stock/store',	'2026-08-08 03:58:09',	'2026-08-08 03:58:09'),
(24,	1,	'stock_form',	'updated',	'stock',	1,	'{\"sku\": \"TV-LED-JSW-42-01\", \"price\": \"25000.00\", \"_token\": \"CICimFG2KnTaxmCwxjiqg4KRTO6Pu7HciHCAuuhL\", \"_method\": \"PUT\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15.00\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_001\", \"category_id\": \"1\", \"description\": \"JSW 42\\\" LED TV\", \"product_name\": \"JSW 42\\\" LED TV\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"80\", \"2\": \"70\", \"3\": \"75\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"tax\": 0, \"price\": 45000, \"unit_id\": \"2\", \"discount\": 0, \"category_id\": \"1\", \"purchase_date\": null, \"selling_price\": 45000, \"supplier_name\": null, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\"}',	'{\"id\": 1, \"tax\": 0, \"cost\": \"25000.00\", \"price\": 0, \"status\": 1, \"unit_id\": 2, \"discount\": 0, \"quantity\": 8, \"created_at\": \"2026-08-08T01:05:29.000000Z\", \"updated_at\": \"2026-10-16T04:08:30.000000Z\", \"category_id\": 1, \"total_stock\": 8, \"average_cost\": \"25000.00\", \"purchase_date\": \"2026-10-16\", \"selling_price\": \"0.00\", \"supplier_name\": \"Alpha Distributors\", \"debit_account_id\": 3, \"credit_account_id\": 10, \"last_purchase_price\": \"25000.00\"}',	'208.163.32.142',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36',	'http://eps.infoicon.co.in:201/admin/stock/update/1',	'2026-10-16 04:11:19',	'2026-10-16 04:11:19'),
(25,	1,	'stock_form',	'updated',	'stock',	2,	'{\"sku\": \"DIG-CAM-FUJIFILM-X100VI-01\", \"price\": \"15500.00\", \"_token\": \"CICimFG2KnTaxmCwxjiqg4KRTO6Pu7HciHCAuuhL\", \"_method\": \"PUT\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15.00\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_002\", \"category_id\": \"1\", \"description\": \"FUJIFILM X100VI Digital Camera (Black)\", \"product_name\": \"FUJIFILM DIGITAL CAMERA X100VI\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"60\", \"2\": \"60\", \"3\": \"60\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"tax\": 0, \"price\": 24800, \"unit_id\": \"2\", \"discount\": 0, \"category_id\": \"1\", \"purchase_date\": null, \"selling_price\": 24800, \"supplier_name\": null, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\"}',	'{\"id\": 2, \"tax\": null, \"cost\": \"15500.00\", \"price\": 0, \"status\": 1, \"unit_id\": 2, \"discount\": null, \"quantity\": 15, \"created_at\": \"2026-08-08T01:10:18.000000Z\", \"updated_at\": \"2026-10-16T04:08:30.000000Z\", \"category_id\": 1, \"total_stock\": 15, \"average_cost\": \"15500.00\", \"purchase_date\": \"2026-10-16\", \"selling_price\": \"0.00\", \"supplier_name\": \"Alpha Distributors\", \"debit_account_id\": 3, \"credit_account_id\": 10, \"last_purchase_price\": \"15500.00\"}',	'208.163.32.142',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/155.0.0.0 Safari/537.36',	'http://eps.infoicon.co.in:201/admin/stock/update/2',	'2026-10-16 04:13:19',	'2026-10-16 04:13:19'),
(26,	1,	'stock_form',	'updated',	'stock',	2,	'{\"sku\": \"DIG-CAM-FUJIFILM-X100VI-01\", \"price\": \"16540.00\", \"_token\": \"4LywzGwAMvuWzgZDd9i6c26Qpy79WLaUYihtn3TE\", \"_method\": \"PUT\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15.00\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_002\", \"category_id\": \"1\", \"description\": \"FUJIFILM X100VI Digital Camera (Black)\", \"product_name\": \"FUJIFILM DIGITAL CAMERA X100VI\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"60\", \"2\": \"60\", \"3\": \"60\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"tax\": 0, \"price\": 26464, \"unit_id\": \"2\", \"discount\": 0, \"category_id\": \"1\", \"purchase_date\": null, \"selling_price\": 26464, \"supplier_name\": null, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\"}',	'{\"id\": 2, \"tax\": 0, \"cost\": \"16540.00\", \"price\": 24800, \"status\": 1, \"unit_id\": 2, \"discount\": 0, \"quantity\": 25, \"created_at\": \"2026-08-08T01:10:18.000000Z\", \"updated_at\": \"2026-10-20T22:44:29.000000Z\", \"category_id\": 1, \"total_stock\": 25, \"average_cost\": \"16540.00\", \"purchase_date\": \"2026-10-21\", \"selling_price\": \"24800.00\", \"supplier_name\": \"Batch BP-BR000001\", \"debit_account_id\": 3, \"credit_account_id\": 10, \"last_purchase_price\": \"18100.00\"}',	'72.252.227.19',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',	'http://eps.infoicon.co.in:201/admin/stock/update/2',	'2026-10-20 22:47:02',	'2026-10-20 22:47:02'),
(27,	1,	'stock_form',	'updated',	'stock',	5,	'{\"sku\": \"MINI-PC-KOOFORWAY-16GB-R7-01\", \"price\": \"70562.50\", \"_token\": \"4LywzGwAMvuWzgZDd9i6c26Qpy79WLaUYihtn3TE\", \"_method\": \"PUT\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15.00\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_005\", \"category_id\": \"3\", \"description\": \"KOOFORWAY Mini PC Ryzen 7 8745HS (Up to 4.9GHz), 16GB DDR5 RAM 1TB SSD, Mini Desktop Computer with LED Screen, Triple 4K Display, Dual 2.5G LAN, WiFi 6E, BT5.3, Win 11 Pro for Business (Silver Gray)\", \"product_name\": \"KOOFORWAY Mini PC Ryzen 7 8745HS 16GB\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"65\", \"2\": \"65\", \"3\": \"65\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"tax\": 0, \"price\": 116428.13, \"unit_id\": \"2\", \"discount\": 0, \"category_id\": \"3\", \"purchase_date\": null, \"selling_price\": 116428.13, \"supplier_name\": null, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\"}',	'{\"id\": 5, \"tax\": null, \"cost\": \"70562.50\", \"price\": 0, \"status\": 1, \"unit_id\": 2, \"discount\": null, \"quantity\": 20, \"created_at\": \"2026-08-08T03:08:01.000000Z\", \"updated_at\": \"2026-10-20T22:44:30.000000Z\", \"category_id\": 3, \"total_stock\": 20, \"average_cost\": \"70562.50\", \"purchase_date\": \"2026-10-21\", \"selling_price\": \"0.00\", \"supplier_name\": \"Batch BP-BR000001\", \"debit_account_id\": 3, \"credit_account_id\": 10, \"last_purchase_price\": \"70562.50\"}',	'72.252.227.19',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',	'http://eps.infoicon.co.in:201/admin/stock/update/5',	'2026-10-20 22:47:32',	'2026-10-20 22:47:32'),
(28,	1,	'stock_form',	'updated',	'stock',	6,	'{\"sku\": \"MON-OLED-GIGABYTE-27-2K-1440-01\", \"price\": \"20012.50\", \"_token\": \"4LywzGwAMvuWzgZDd9i6c26Qpy79WLaUYihtn3TE\", \"_method\": \"PUT\", \"unit_id\": \"2\", \"store_id\": 1, \"tax_rate\": \"15.00\", \"expiry_on\": null, \"is_taxable\": \"1\", \"product_id\": \"P_006\", \"category_id\": \"3\", \"description\": \"GIGABYTE 27 inch QHD 2K 1440P 240Hz OLED Gaming Computer Monitor GO27Q24A\", \"product_name\": \"GIGABYTE 27 inch QHD OLED Computer Monitor\", \"product_type\": \"inventory_stock\", \"remove_image\": \"0\", \"store_markups\": {\"1\": \"80\", \"2\": \"80\", \"3\": \"80\"}, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\", \"manufactured_date\": null}',	'{\"tax\": 0, \"price\": 36022.5, \"unit_id\": \"2\", \"discount\": 0, \"category_id\": \"3\", \"purchase_date\": null, \"selling_price\": 36022.5, \"supplier_name\": null, \"debit_account_id\": \"3\", \"credit_account_id\": \"10\"}',	'{\"id\": 6, \"tax\": null, \"cost\": \"20012.50\", \"price\": 0, \"status\": 1, \"unit_id\": 2, \"discount\": null, \"quantity\": 20, \"created_at\": \"2026-08-08T03:15:38.000000Z\", \"updated_at\": \"2026-10-20T22:44:30.000000Z\", \"category_id\": 3, \"total_stock\": 20, \"average_cost\": \"20012.50\", \"purchase_date\": \"2026-10-21\", \"selling_price\": \"0.00\", \"supplier_name\": \"Batch BP-BR000001\", \"debit_account_id\": 3, \"credit_account_id\": 10, \"last_purchase_price\": \"20012.50\"}',	'72.252.227.19',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',	'http://eps.infoicon.co.in:201/admin/stock/update/6',	'2026-10-20 22:47:56',	'2026-10-20 22:47:56'),
(29,	1,	'customer_form',	'created',	'customer',	1,	'{\"city\": \"Seriously\", \"email\": \"whydoineed@email.org\", \"phone\": \"8761234567\", \"_token\": \"4LywzGwAMvuWzgZDd9i6c26Qpy79WLaUYihtn3TE\", \"address\": \"15 Is This Really Street\", \"country\": \"Jamaica\", \"description\": null, \"credit_limit\": \"0\", \"customer_name\": \"Amazong Primus Ltd\"}',	'{\"id\": 1, \"city\": \"Seriously\", \"email\": \"whydoineed@email.org\", \"phone\": \"8761234567\", \"address\": \"15 Is This Really Street\", \"country\": \"Jamaica\", \"created_at\": \"2026-10-20T22:53:07.000000Z\", \"updated_at\": \"2026-10-20T22:53:07.000000Z\", \"description\": null, \"credit_limit\": 0, \"customer_name\": \"Amazong Primus Ltd\"}',	'[]',	'72.252.227.19',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',	'http://eps.infoicon.co.in:201/admin/create-customer',	'2026-10-20 22:53:07',	'2026-10-20 22:53:07'),
(30,	1,	'customer_form',	'created',	'customer',	2,	'{\"city\": \"Not Kansas\", \"email\": \"secure@tops.com\", \"phone\": \"8763334456\", \"_token\": \"4LywzGwAMvuWzgZDd9i6c26Qpy79WLaUYihtn3TE\", \"address\": \"Somewhere Over The Rainbow\", \"country\": \"Oz\", \"description\": null, \"credit_limit\": \"0\", \"customer_name\": \"Treadstone Operations\"}',	'{\"id\": 2, \"city\": \"Not Kansas\", \"email\": \"secure@tops.com\", \"phone\": \"8763334456\", \"address\": \"Somewhere Over The Rainbow\", \"country\": \"Oz\", \"created_at\": \"2026-10-20T22:57:05.000000Z\", \"updated_at\": \"2026-10-20T22:57:05.000000Z\", \"description\": null, \"credit_limit\": 0, \"customer_name\": \"Treadstone Operations\"}',	'[]',	'72.252.227.19',	'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:158.0) Gecko/20100101 Firefox/158.0',	'http://eps.infoicon.co.in:201/admin/create-customer',	'2026-10-20 22:57:05',	'2026-10-20 22:57:05');

DROP TABLE IF EXISTS `invoice_options`;
CREATE TABLE `invoice_options` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_options_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `journal_entries`;
CREATE TABLE `journal_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `entry_date` date NOT NULL,
  `reference` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(16) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `user_id` bigint unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `store_id` bigint unsigned DEFAULT NULL,
  `source_type` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source_id` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entries_user_id_foreign` (`user_id`),
  KEY `journal_entries_store_id_foreign` (`store_id`),
  KEY `journal_entries_status_entry_date_index` (`status`,`entry_date`),
  KEY `journal_entries_source_type_source_id_index` (`source_type`,`source_id`),
  CONSTRAINT `journal_entries_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `journal_entries` (`id`, `entry_date`, `reference`, `description`, `status`, `user_id`, `posted_at`, `store_id`, `source_type`, `source_id`, `created_at`, `updated_at`) VALUES
(1,	'2026-10-16',	'POBR-BR000001',	'Purchase order — supplier commitment',	'posted',	1,	'2026-10-16 03:47:57',	1,	'purchase_order',	1,	'2026-10-16 03:47:57',	'2026-10-16 03:47:57'),
(2,	'2026-10-16',	'PINV-BR000001',	'Supplier purchase invoice',	'posted',	1,	'2026-10-16 03:57:29',	1,	'supplier_purchase_invoice',	1,	'2026-10-16 03:57:29',	'2026-10-16 03:57:29'),
(3,	'2026-10-21',	'PINV-BR000002',	'Supplier purchase invoice',	'posted',	1,	'2026-10-20 22:30:31',	1,	'supplier_purchase_invoice',	2,	'2026-10-20 22:30:31',	'2026-10-20 22:30:31'),
(4,	'2026-10-21',	'PINV-BR000003',	'Supplier purchase invoice',	'posted',	1,	'2026-10-20 22:33:01',	1,	'supplier_purchase_invoice',	3,	'2026-10-20 22:33:01',	'2026-10-20 22:33:01'),
(5,	'2026-10-21',	'PINV-BR000004',	'Supplier purchase invoice',	'posted',	1,	'2026-10-20 22:35:00',	1,	'supplier_purchase_invoice',	4,	'2026-10-20 22:35:00',	'2026-10-20 22:35:00');

DROP TABLE IF EXISTS `journal_lines`;
CREATE TABLE `journal_lines` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint unsigned NOT NULL,
  `gl_account_id` bigint unsigned NOT NULL,
  `line_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `debit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `credit` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_lines_journal_entry_id_foreign` (`journal_entry_id`),
  KEY `journal_lines_gl_account_id_index` (`gl_account_id`),
  CONSTRAINT `journal_lines_gl_account_id_foreign` FOREIGN KEY (`gl_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `journal_lines_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `journal_lines` (`id`, `journal_entry_id`, `gl_account_id`, `line_description`, `debit`, `credit`, `created_at`, `updated_at`) VALUES
(1,	1,	3,	'Inventory received — JSW 42\" LED TV',	482500.00,	0.00,	'2026-10-16 03:47:57',	'2026-10-16 03:47:57'),
(2,	1,	3,	'Inventory received adjustment',	72375.00,	0.00,	'2026-10-16 03:47:57',	'2026-10-16 03:47:57'),
(3,	1,	5,	'Supplier payable',	0.00,	554875.00,	'2026-10-16 03:47:57',	'2026-10-16 03:47:57'),
(4,	2,	3,	'Inventory received — JSW 42\" LED TV',	432500.00,	0.00,	'2026-10-16 03:57:29',	'2026-10-16 03:57:29'),
(5,	2,	14,	'Input tax (taxation payable)',	64875.00,	0.00,	'2026-10-16 03:57:29',	'2026-10-16 03:57:29'),
(6,	2,	5,	'Supplier payable',	0.00,	497375.00,	'2026-10-16 03:57:29',	'2026-10-16 03:57:29'),
(7,	3,	3,	'Inventory received — FUJIFILM DIGITAL CAMERA X100VI',	1842500.00,	0.00,	'2026-10-20 22:30:32',	'2026-10-20 22:30:32'),
(8,	3,	14,	'Input tax (taxation payable)',	276375.00,	0.00,	'2026-10-20 22:30:32',	'2026-10-20 22:30:32'),
(9,	3,	5,	'Supplier payable',	0.00,	2118875.00,	'2026-10-20 22:30:32',	'2026-10-20 22:30:32'),
(10,	4,	3,	'Inventory received — Brokerage Fee',	25000.00,	0.00,	'2026-10-20 22:33:01',	'2026-10-20 22:33:01'),
(11,	4,	5,	'Supplier payable',	0.00,	25000.00,	'2026-10-20 22:33:01',	'2026-10-20 22:33:01'),
(12,	5,	3,	'Inventory received — Customs Duty 10%',	125000.00,	0.00,	'2026-10-20 22:35:00',	'2026-10-20 22:35:00'),
(13,	5,	5,	'Supplier payable',	0.00,	125000.00,	'2026-10-20 22:35:00',	'2026-10-20 22:35:00');

DROP TABLE IF EXISTS `ledger_entries`;
CREATE TABLE `ledger_entries` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `ledger_id` bigint unsigned NOT NULL,
  `entry_date` date NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `messages_sender_id_foreign` (`sender_id`),
  KEY `messages_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
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
(20,	'2025_04_22_065607_create_notifications_table',	1),
(21,	'2025_06_30_060241_create_settings_table',	2),
(22,	'2025_07_01_075648_create_histories_table',	2),
(23,	'2025_07_22_093637_create_permissions_table',	3),
(24,	'2025_07_22_093704_create_user_permissions_table',	3),
(25,	'2025_07_23_121038_create_store_employee_permissions_table',	3),
(26,	'2025_08_08_165158_create_sidebar_links_table',	3),
(27,	'2026_06_03_140000_add_invoice_status_to_supplier_purchase_invoices_table',	4),
(28,	'2026_06_03_150000_add_draft_reference_and_credit_limit_flags',	5),
(29,	'2026_06_03_150100_add_credit_limit_override_users_setting',	6),
(30,	'2026_06_05_160000_add_purchase_doc_sequences_to_stores_table',	7),
(31,	'2026_06_05_170000_add_internal_invoice_number_to_supplier_purchase_invoices',	8),
(32,	'2026_06_05_171000_create_transaction_account_mappings_table',	9),
(33,	'2026_06_05_180000_add_per_line_tax_fields',	10),
(34,	'2026_06_10_120000_add_per_line_tax_to_purchase_invoices',	11),
(35,	'2026_06_10_130000_add_per_line_tax_to_purchase_documents',	12),
(36,	'2026_06_11_120000_add_taxation_payable_gl_account',	13),
(37,	'2026_06_11_100000_link_purchase_orders_invoices_grn',	14),
(38,	'2026_06_12_100000_create_batch_purchases_tables',	15),
(39,	'2026_06_13_100000_add_inventory_valuation_settings',	16),
(40,	'2026_06_13_120000_add_product_cost_and_store_markups',	17);

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `message` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_user_id_foreign` (`user_id`),
  CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `notifications` (`id`, `user_id`, `message`, `read_at`, `created_at`, `updated_at`) VALUES
(1,	1,	'Store (Burke Road) has been successfully created.',	NULL,	'2026-08-08 00:23:57',	'2026-08-08 00:23:57'),
(2,	1,	'Store (Harbour View) has been successfully created.',	NULL,	'2026-08-08 00:25:50',	'2026-08-08 00:25:50'),
(3,	1,	'Store (May Pen) has been successfully created.',	NULL,	'2026-08-08 00:30:09',	'2026-08-08 00:30:09'),
(4,	1,	'New category (Electronics) has been added.',	NULL,	'2026-08-08 00:42:23',	'2026-08-08 00:42:23'),
(5,	1,	'New category (Appliances) has been added.',	NULL,	'2026-08-08 00:42:42',	'2026-08-08 00:42:42'),
(6,	1,	'New category (Computer Systems) has been added.',	NULL,	'2026-08-08 00:43:16',	'2026-08-08 00:43:16'),
(7,	1,	'New Unit (Unit) has been created.',	NULL,	'2026-08-08 00:44:09',	'2026-08-08 00:44:09'),
(8,	1,	'New Unit (each) has been created.',	NULL,	'2026-08-08 00:44:30',	'2026-08-08 00:44:30'),
(9,	1,	'New Unit (pcs) has been created.',	NULL,	'2026-08-08 00:44:52',	'2026-08-08 00:44:52'),
(10,	1,	'Category (Appliances) has been updated.',	NULL,	'2026-08-08 00:46:01',	'2026-08-08 00:46:01'),
(11,	1,	'Category (Computer Systems) has been updated.',	NULL,	'2026-08-08 00:46:17',	'2026-08-08 00:46:17'),
(12,	1,	'Category (Electronics) has been updated.',	NULL,	'2026-08-08 00:50:22',	'2026-08-08 00:50:22'),
(13,	1,	'Unit (unit) has been updated.',	NULL,	'2026-08-08 00:51:07',	'2026-08-08 00:51:07'),
(14,	1,	'Unit (each) has been updated.',	NULL,	'2026-08-08 00:51:36',	'2026-08-08 00:51:36'),
(15,	1,	'Unit (pcs) has been updated.',	NULL,	'2026-08-08 00:51:50',	'2026-08-08 00:51:50'),
(16,	1,	'Category (Toots) has been updated.',	NULL,	'2026-08-08 00:57:32',	'2026-08-08 00:57:32'),
(17,	1,	'Category (Electronics) has been updated.',	NULL,	'2026-08-08 00:57:52',	'2026-08-08 00:57:52'),
(18,	1,	'Unit (boo) has been updated.',	NULL,	'2026-08-08 00:59:15',	'2026-08-08 00:59:15'),
(19,	1,	'Unit (unit) has been updated.',	NULL,	'2026-08-08 01:00:28',	'2026-08-08 01:00:28'),
(20,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 01:05:32',	'2026-08-08 01:05:32'),
(21,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 01:10:19',	'2026-08-08 01:10:19'),
(22,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 01:17:16',	'2026-08-08 01:17:16'),
(23,	1,	'Stock (JSW 42\" LED TV) has been updated in store (Burke Road)',	NULL,	'2026-08-08 02:52:00',	'2026-08-08 02:52:00'),
(24,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 02:57:44',	'2026-08-08 02:57:44'),
(25,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 03:08:04',	'2026-08-08 03:08:04'),
(26,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 03:15:40',	'2026-08-08 03:15:40'),
(27,	1,	'New category (Intangibles) has been added.',	NULL,	'2026-08-08 03:44:43',	'2026-08-08 03:44:43'),
(28,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 03:50:30',	'2026-08-08 03:50:30'),
(29,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 03:52:46',	'2026-08-08 03:52:46'),
(30,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 03:56:27',	'2026-08-08 03:56:27'),
(31,	1,	'New Stock has been added to store (Burke Road)',	NULL,	'2026-08-08 03:58:09',	'2026-08-08 03:58:09'),
(32,	1,	'Stock (JSW 42\" LED TV) has been updated in store (Burke Road)',	NULL,	'2026-10-16 04:11:20',	'2026-10-16 04:11:20'),
(33,	1,	'Stock (FUJIFILM DIGITAL CAMERA X100VI) has been updated in store (Burke Road)',	NULL,	'2026-10-16 04:13:19',	'2026-10-16 04:13:19'),
(34,	1,	'Stock (FUJIFILM DIGITAL CAMERA X100VI) has been updated in store (Burke Road)',	NULL,	'2026-10-20 22:47:02',	'2026-10-20 22:47:02'),
(35,	1,	'Stock (KOOFORWAY Mini PC Ryzen 7 8745HS 16GB) has been updated in store (Burke Road)',	NULL,	'2026-10-20 22:47:33',	'2026-10-20 22:47:33'),
(36,	1,	'Stock (GIGABYTE 27 inch QHD OLED Computer Monitor) has been updated in store (Burke Road)',	NULL,	'2026-10-20 22:47:57',	'2026-10-20 22:47:57'),
(37,	1,	'New Customer has been Added.',	NULL,	'2026-10-20 22:53:07',	'2026-10-20 22:53:07'),
(38,	1,	'New Customer has been Added.',	NULL,	'2026-10-20 22:57:05',	'2026-10-20 22:57:05');

DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `order_id` bigint unsigned NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `category_id` bigint DEFAULT NULL,
  `price` double DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_items_order_id_foreign` (`order_id`),
  CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint unsigned DEFAULT NULL,
  `customer_id` bigint NOT NULL,
  `customer_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_price` double NOT NULL,
  `paid_amount` double NOT NULL DEFAULT '0',
  `due_amount` double NOT NULL DEFAULT '0',
  `last_payment_at` timestamp NULL DEFAULT NULL,
  `status` int NOT NULL DEFAULT '1',
  `subtotal_price` double NOT NULL,
  `tax` double DEFAULT NULL,
  `tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `discount` int DEFAULT NULL,
  `shipping` int DEFAULT NULL,
  `payment_method` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_status` enum('Draft','Confirmed','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Draft',
  `invoice_number` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `draft_reference` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `due_date` date DEFAULT NULL,
  `payment_terms_days` int unsigned DEFAULT NULL,
  `type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `organization_infos`;
CREATE TABLE `organization_infos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `website` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `organization_infos` (`id`, `name`, `phone`, `email`, `address`, `website`, `logo_path`, `created_at`, `updated_at`) VALUES
(1,	'CSL POS & Inventory Management System.',	'9874123654',	'superadmin@gmail.com',	'E-40, Noida, USA',	'http://comfortsystemsja.ddns.net/',	'organization_logos/CbNKjogXeF6tTJZX6r8dQveJUTh3yuOYBdeMCFfs.jpg',	'2026-03-26 05:55:51',	'2026-03-26 06:10:51');

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('admin@gmail.com',	'$2y$12$Gzjw3LVaiqYW.Qx9Js8yG.Qx4OFOd7NpVP/iywjkkIf.4AqpBFTkW',	'2025-05-28 08:43:00'),
('storemanager@gmail.com',	'$2y$12$hcVR3FUnmWHqRm2DF88EFepZvvOfOL87ylC/PajPid.5AGa4vn5OS',	'2025-06-05 08:44:10');

DROP TABLE IF EXISTS `payment_terms`;
CREATE TABLE `payment_terms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `due_days` int unsigned DEFAULT NULL,
  `due_months` int unsigned NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `permissions`;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `module` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `permissions` (`id`, `module`, `action`, `created_at`, `updated_at`) VALUES
(1,	'stock',	'create',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(2,	'stock',	'update',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(3,	'stock',	'delete',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(4,	'order',	'create',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(5,	'order',	'update',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(7,	'user',	'create',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(8,	'user',	'update',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(9,	'user',	'delete',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(10,	'permission',	'create',	'2025-07-22 04:10:40',	'2025-07-22 04:10:40'),
(11,	'store',	'create',	NULL,	NULL),
(12,	'store',	'update',	NULL,	NULL),
(13,	'store',	'delete',	NULL,	NULL),
(15,	'stock_transfer',	'create',	NULL,	NULL),
(16,	'stock_transfer',	'update',	NULL,	NULL),
(17,	'stock_transfer',	'delete',	NULL,	NULL),
(18,	'stock_adjustment',	'create',	NULL,	NULL),
(19,	'stock_adjustment',	'update',	NULL,	NULL),
(20,	'stock_adjustment',	'delete',	NULL,	NULL),
(21,	'user_status',	'update',	NULL,	NULL);

DROP TABLE IF EXISTS `product_store_markups`;
CREATE TABLE `product_store_markups` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `product_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `markup_percentage` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_store_markups_unique` (`product_id`,`store_id`),
  KEY `product_store_markups_store_id_foreign` (`store_id`),
  CONSTRAINT `product_store_markups_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `product_store_markups` (`id`, `product_id`, `store_id`, `markup_percentage`, `created_at`, `updated_at`) VALUES
(1,	'P_001',	1,	80.00,	'2026-08-08 01:05:30',	'2026-10-16 04:11:20'),
(2,	'P_001',	2,	70.00,	'2026-08-08 01:05:32',	'2026-10-16 04:11:20'),
(3,	'P_001',	3,	75.00,	'2026-08-08 01:05:32',	'2026-08-08 01:05:32'),
(4,	'P_002',	1,	60.00,	'2026-08-08 01:10:18',	'2026-08-08 01:10:18'),
(5,	'P_002',	2,	60.00,	'2026-08-08 01:10:19',	'2026-08-08 01:10:19'),
(6,	'P_002',	3,	60.00,	'2026-08-08 01:10:19',	'2026-08-08 01:10:19'),
(7,	'P_003',	1,	65.00,	'2026-08-08 01:17:16',	'2026-08-08 01:17:16'),
(8,	'P_003',	2,	65.00,	'2026-08-08 01:17:16',	'2026-08-08 01:17:16'),
(9,	'P_003',	3,	65.00,	'2026-08-08 01:17:16',	'2026-08-08 01:17:16'),
(10,	'P_004',	1,	55.00,	'2026-08-08 02:57:41',	'2026-08-08 02:57:41'),
(11,	'P_004',	2,	55.00,	'2026-08-08 02:57:43',	'2026-08-08 02:57:43'),
(12,	'P_004',	3,	55.00,	'2026-08-08 02:57:43',	'2026-08-08 02:57:43'),
(13,	'P_005',	1,	65.00,	'2026-08-08 03:08:02',	'2026-08-08 03:08:02'),
(14,	'P_005',	2,	65.00,	'2026-08-08 03:08:03',	'2026-08-08 03:08:03'),
(15,	'P_005',	3,	65.00,	'2026-08-08 03:08:03',	'2026-08-08 03:08:03'),
(16,	'P_006',	1,	80.00,	'2026-08-08 03:15:38',	'2026-08-08 03:15:38'),
(17,	'P_006',	2,	80.00,	'2026-08-08 03:15:39',	'2026-08-08 03:15:39'),
(18,	'P_006',	3,	80.00,	'2026-08-08 03:15:40',	'2026-08-08 03:15:40'),
(19,	'P_007',	1,	0.00,	'2026-08-08 03:50:29',	'2026-08-08 03:50:29'),
(20,	'P_007',	2,	0.00,	'2026-08-08 03:50:30',	'2026-08-08 03:50:30'),
(21,	'P_007',	3,	0.00,	'2026-08-08 03:50:30',	'2026-08-08 03:50:30'),
(22,	'P_008',	1,	0.00,	'2026-08-08 03:52:45',	'2026-08-08 03:52:45'),
(23,	'P_008',	2,	0.00,	'2026-08-08 03:52:46',	'2026-08-08 03:52:46'),
(24,	'P_008',	3,	0.00,	'2026-08-08 03:52:46',	'2026-08-08 03:52:46'),
(25,	'P_009',	1,	0.00,	'2026-08-08 03:56:26',	'2026-08-08 03:56:26'),
(26,	'P_009',	2,	0.00,	'2026-08-08 03:56:26',	'2026-08-08 03:56:26'),
(27,	'P_009',	3,	0.00,	'2026-08-08 03:56:26',	'2026-08-08 03:56:26'),
(28,	'P_010',	1,	0.00,	'2026-08-08 03:58:07',	'2026-08-08 03:58:07'),
(29,	'P_010',	2,	0.00,	'2026-08-08 03:58:09',	'2026-08-08 03:58:09'),
(30,	'P_010',	3,	0.00,	'2026-08-08 03:58:09',	'2026-08-08 03:58:09');

DROP TABLE IF EXISTS `purchase_order_items`;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `product_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `product_id`, `product_name`, `qty`, `unit_price`, `line_total`, `is_taxable`, `tax_rate`, `tax_amount`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_001',	'JSW 42\" LED TV',	10,	25000.00,	250000.00,	1,	15.00,	37500.00,	'2026-10-16 03:47:55',	'2026-10-16 03:47:55'),
(2,	1,	'P_002',	'FUJIFILM DIGITAL CAMERA X100VI',	15,	15500.00,	232500.00,	1,	15.00,	34875.00,	'2026-10-16 03:47:56',	'2026-10-16 03:47:56');

DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned DEFAULT NULL,
  `po_number` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order_date` date NOT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_orders_store_id_foreign` (`store_id`),
  CONSTRAINT `purchase_orders_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchase_orders` (`id`, `supplier_id`, `store_id`, `po_number`, `order_date`, `status`, `subtotal`, `tax_percent`, `tax_amount`, `tax_exempt`, `total`, `notes`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	'POBR-BR000001',	'2026-10-16',	'received',	482500.00,	0.00,	72375.00,	0,	554875.00,	NULL,	'2026-10-16 03:47:55',	'2026-10-16 04:08:30');

DROP TABLE IF EXISTS `purchase_return_items`;
CREATE TABLE `purchase_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_return_id` bigint unsigned NOT NULL,
  `product_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_return_items_purchase_return_id_foreign` (`purchase_return_id`),
  CONSTRAINT `purchase_return_items_purchase_return_id_foreign` FOREIGN KEY (`purchase_return_id`) REFERENCES `purchase_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `purchase_returns`;
CREATE TABLE `purchase_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned DEFAULT NULL,
  `supplier_purchase_invoice_id` bigint unsigned DEFAULT NULL,
  `return_number` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `return_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `total_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_returns_return_number_unique` (`return_number`),
  KEY `purchase_returns_supplier_id_foreign` (`supplier_id`),
  KEY `purchase_returns_store_id_foreign` (`store_id`),
  KEY `purchase_returns_supplier_purchase_invoice_id_foreign` (`supplier_purchase_invoice_id`),
  CONSTRAINT `purchase_returns_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchase_returns_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_returns_supplier_purchase_invoice_id_foreign` FOREIGN KEY (`supplier_purchase_invoice_id`) REFERENCES `supplier_purchase_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
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
(5,	'Clerk',	'clerk',	'The employees working at a store',	'2025-04-29 00:30:27',	'2025-04-29 00:30:27'),
(6,	'Staff',	'staff',	'All other employees working at a store',	'2025-08-08 09:49:54',	'2025-08-08 09:49:54');

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('jTBli4e5JBbwiAG4urDrSCqXkUNg8w9tnVCbvV1v',	NULL,	'49.36.190.182',	'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoicnBkUmJ2c081cE0zcnk2dEtiQ1FYc0MzWFN3NXdPdURWYmdiZHJSMSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly9lcHMuaW5mb2ljb24uY28uaW46MjAxIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',	1770378759);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `placeholder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `label` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` (`id`, `key_name`, `value`, `placeholder`, `label`, `created_at`, `updated_at`) VALUES
(1,	'qty_alert',	'5',	'Enter Qty',	'Qty Alert (Low Stocks)',	NULL,	'2025-07-09 18:53:28'),
(2,	'credit_limit_override_user_ids',	'1',	'Select users who may override credit limits',	'Users Allowed To Override Credit Limits',	'2026-06-03 12:09:20',	'2026-06-08 10:08:54'),
(3,	'inventory_valuation_method',	'fifo',	'avco, fifo, or lifo',	'Inventory Valuation Method',	'2026-06-23 12:06:38',	'2026-07-08 07:13:59'),
(4,	'accounting_year_start_month',	'1',	'1-12 (January = 1)',	'Accounting year start month',	'2026-06-23 12:06:38',	'2026-07-08 07:14:09'),
(5,	'inventory_valuation_method_set_at',	'2026-07-08 12:43:59',	'Set automatically when valuation method is saved',	'Inventory valuation method last set at',	'2026-06-23 12:06:38',	'2026-07-08 07:13:59'),
(6,	'date_format',	'd-M-y',	'Display format for dates across lists, documents, and date pickers',	'Date format',	'2026-07-10 07:09:01',	'2026-07-23 16:23:36');

DROP TABLE IF EXISTS `sidebar_links`;
CREATE TABLE `sidebar_links` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `clerk_id` bigint unsigned NOT NULL,
  `link_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sidebar_links_clerk_id_foreign` (`clerk_id`),
  CONSTRAINT `sidebar_links_clerk_id_foreign` FOREIGN KEY (`clerk_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sidebar_links` (`id`, `clerk_id`, `link_key`, `description`, `created_at`, `updated_at`) VALUES
(23,	18,	'customer_interaction',	NULL,	'2025-09-09 12:10:47',	'2025-09-09 12:10:47'),
(24,	18,	'all_stock',	NULL,	'2025-09-09 12:10:47',	'2025-09-09 12:10:47'),
(25,	18,	'add_stock',	NULL,	'2025-09-09 12:10:47',	'2025-09-09 12:10:47'),
(48,	17,	'dashboard_inventory_summary',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(49,	17,	'dashboard_report_section',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(50,	17,	'dashboard_customer_insight',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(51,	17,	'sales_transaction',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(52,	17,	'sales_report',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(53,	17,	'customer_interaction',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(54,	17,	'all_stock',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(55,	17,	'add_stock',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(56,	17,	'stock_transfer',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(57,	17,	'stock_adjustment',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(58,	17,	'incoming_stock',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(59,	17,	'outgoing_stock',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(60,	17,	'real_time_updates',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(61,	17,	'configure_pos_terminal',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(62,	17,	'invoice_management',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(63,	17,	'transaction_management',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(64,	17,	'sales_insight',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(65,	17,	'all_store',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(66,	17,	'communication',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(67,	17,	'daily_operation_overview',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25'),
(68,	17,	'balance_sheet',	NULL,	'2026-04-03 07:04:25',	'2026-04-03 07:04:25');

DROP TABLE IF EXISTS `stock_adjustments`;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjust_quantity` int NOT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `adjust_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `stock_transfer_items`;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference_no` bigint NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int NOT NULL,
  `category_id` bigint DEFAULT NULL,
  `status` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `stock_transfers`;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_from` bigint NOT NULL,
  `store_to` bigint NOT NULL,
  `rperson_from` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rperson_to` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_no` bigint NOT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `arrival_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_reference_no_unique` (`reference_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `stocks`;
CREATE TABLE `stocks` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint NOT NULL,
  `product_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_type` enum('service','inventory_stock','non_inventory_stock') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inventory_stock',
  `sku` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `category_id` bigint NOT NULL,
  `unit_id` bigint DEFAULT NULL,
  `debit_account_id` bigint unsigned DEFAULT NULL,
  `credit_account_id` bigint unsigned DEFAULT NULL,
  `supplier_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `quantity` int DEFAULT NULL,
  `price` double DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount` double DEFAULT NULL,
  `tax` double DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) DEFAULT NULL,
  `manufactured_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `images` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `selling_price` decimal(10,2) DEFAULT '0.00',
  `average_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `last_purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_stock` int NOT NULL DEFAULT '0',
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stocks_product_id_store_id_unique` (`product_id`,`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stocks` (`id`, `store_id`, `product_id`, `product_name`, `product_type`, `sku`, `category_id`, `unit_id`, `debit_account_id`, `credit_account_id`, `supplier_name`, `purchase_date`, `description`, `quantity`, `price`, `cost`, `discount`, `tax`, `is_taxable`, `tax_rate`, `manufactured_date`, `expiry_date`, `images`, `selling_price`, `average_cost`, `last_purchase_price`, `total_stock`, `status`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_001',	'JSW 42\" LED TV',	'inventory_stock',	'TV-LED-JSW-42-01',	1,	2,	3,	10,	NULL,	NULL,	'JSW 42\" LED TV',	8,	45000,	25000.00,	0,	0,	1,	15.00,	NULL,	NULL,	NULL,	45000.00,	25000.00,	25000.00,	8,	1,	'2026-08-08 01:05:29',	'2026-10-16 04:11:20'),
(2,	1,	'P_002',	'FUJIFILM DIGITAL CAMERA X100VI',	'inventory_stock',	'DIG-CAM-FUJIFILM-X100VI-01',	1,	2,	3,	10,	NULL,	NULL,	'FUJIFILM X100VI Digital Camera (Black)',	25,	26464,	16540.00,	0,	0,	1,	15.00,	NULL,	NULL,	NULL,	26464.00,	16540.00,	18100.00,	25,	1,	'2026-08-08 01:10:18',	'2026-10-20 22:47:02'),
(3,	1,	'P_003',	'Panasonic NN-SN74PWH Microwave',	'inventory_stock',	'MW-INV-PANASONIC-NN-SN74PWH-01',	2,	2,	3,	10,	NULL,	NULL,	'Panasonic NN-SN74PWH Microwave with Cyclonic Inverter 1.6 Cu. Ft. (White)',	0,	0,	0.00,	NULL,	NULL,	1,	15.00,	NULL,	NULL,	NULL,	0.00,	0.00,	0.00,	0,	1,	'2026-08-08 01:17:15',	'2026-08-08 01:17:16'),
(4,	1,	'P_004',	'eufy C28 Robot Vacuum and Mop Combo',	'inventory_stock',	'RVAC-EUFY-C28-01',	2,	2,	3,	10,	NULL,	NULL,	'eufy C28 Robot Vacuum and Mop Combo, HydroJet Self-Cleaning Roller Mop and Floor Cleaner',	0,	0,	0.00,	NULL,	NULL,	1,	15.00,	NULL,	NULL,	NULL,	0.00,	0.00,	0.00,	0,	1,	'2026-08-08 02:57:40',	'2026-08-08 02:57:43'),
(5,	1,	'P_005',	'KOOFORWAY Mini PC Ryzen 7 8745HS 16GB',	'inventory_stock',	'MINI-PC-KOOFORWAY-16GB-R7-01',	3,	2,	3,	10,	NULL,	NULL,	'KOOFORWAY Mini PC Ryzen 7 8745HS (Up to 4.9GHz), 16GB DDR5 RAM 1TB SSD, Mini Desktop Computer with LED Screen, Triple 4K Display, Dual 2.5G LAN, WiFi 6E, BT5.3, Win 11 Pro for Business (Silver Gray)',	20,	116428.13,	70562.50,	0,	0,	1,	15.00,	NULL,	NULL,	NULL,	116428.13,	70562.50,	70562.50,	20,	1,	'2026-08-08 03:08:01',	'2026-10-20 22:47:33'),
(6,	1,	'P_006',	'GIGABYTE 27 inch QHD OLED Computer Monitor',	'inventory_stock',	'MON-OLED-GIGABYTE-27-2K-1440-01',	3,	2,	3,	10,	NULL,	NULL,	'GIGABYTE 27 inch QHD 2K 1440P 240Hz OLED Gaming Computer Monitor GO27Q24A',	20,	36022.5,	20012.50,	0,	0,	1,	15.00,	NULL,	NULL,	NULL,	36022.50,	20012.50,	20012.50,	20,	1,	'2026-08-08 03:15:38',	'2026-10-20 22:47:57'),
(7,	1,	'P_007',	'Brokerage Fee',	'service',	'FEE-BROK',	4,	2,	3,	10,	NULL,	NULL,	'Brokerage Fee',	0,	0,	0.00,	NULL,	NULL,	0,	NULL,	NULL,	NULL,	NULL,	0.00,	0.00,	0.00,	0,	1,	'2026-08-08 03:50:29',	'2026-08-08 03:50:30'),
(8,	1,	'P_008',	'Transportation Fee Inward',	'service',	'FEE-TRANSP-IN',	4,	2,	3,	10,	NULL,	NULL,	'Transportation Fee Inward',	0,	0,	0.00,	NULL,	NULL,	1,	15.00,	NULL,	NULL,	NULL,	0.00,	0.00,	0.00,	0,	1,	'2026-08-08 03:52:45',	'2026-08-08 03:52:46'),
(9,	1,	'P_009',	'Customs Duty 10%',	'service',	'DUTY-CUST-10',	4,	2,	3,	10,	NULL,	NULL,	'Customs Duty 10%',	0,	0,	0.00,	NULL,	NULL,	0,	NULL,	NULL,	NULL,	NULL,	0.00,	0.00,	0.00,	0,	1,	'2026-08-08 03:56:25',	'2026-08-08 03:56:26'),
(10,	1,	'P_010',	'Customs Duty 20%',	'service',	'DUTY-CUST-20',	4,	2,	3,	10,	NULL,	NULL,	'Customs Duty 20%',	0,	0,	0.00,	NULL,	NULL,	0,	NULL,	NULL,	NULL,	NULL,	0.00,	0.00,	0.00,	0,	1,	'2026-08-08 03:58:06',	'2026-08-08 03:58:09');

DROP TABLE IF EXISTS `store_employee_permissions`;
CREATE TABLE `store_employee_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_employee_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `store_employee_permissions_store_employee_id_foreign` (`store_employee_id`),
  KEY `store_employee_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `store_employee_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `store_employee_permissions_store_employee_id_foreign` FOREIGN KEY (`store_employee_id`) REFERENCES `store_employees` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `store_employees`;
CREATE TABLE `store_employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` bigint NOT NULL,
  `store_id` bigint NOT NULL,
  `profile_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_employees_username_unique` (`username`),
  UNIQUE KEY `store_employees_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `store_purchase_items`;
CREATE TABLE `store_purchase_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_purchase_id` bigint unsigned NOT NULL,
  `product_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_purchase_items_store_purchase_id_foreign` (`store_purchase_id`),
  CONSTRAINT `store_purchase_items_store_purchase_id_foreign` FOREIGN KEY (`store_purchase_id`) REFERENCES `store_purchases` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `store_purchases`;
CREATE TABLE `store_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned DEFAULT NULL,
  `purchase_number` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_date` date NOT NULL,
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_purchases_purchase_number_unique` (`purchase_number`),
  KEY `store_purchases_supplier_id_foreign` (`supplier_id`),
  KEY `store_purchases_store_id_foreign` (`store_id`),
  CONSTRAINT `store_purchases_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `store_purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `stores`;
CREATE TABLE `stores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `invoice_prefix` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INV/',
  `current_sale_invoice_sequence` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'INV/',
  `next_sale_invoice_number` int NOT NULL DEFAULT '1',
  `purchase_invoice_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PINV',
  `next_purchase_invoice_number` int unsigned NOT NULL DEFAULT '1',
  `purchase_order_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PO',
  `next_purchase_order_number` int unsigned NOT NULL DEFAULT '1',
  `purchase_return_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PR',
  `next_purchase_return_number` int unsigned NOT NULL DEFAULT '1',
  `grn_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'GRN',
  `next_grn_number` int unsigned NOT NULL DEFAULT '1',
  `credit_note_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CN',
  `next_credit_note_number` int unsigned NOT NULL DEFAULT '1',
  `debit_note_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'DN',
  `next_debit_note_number` int unsigned NOT NULL DEFAULT '1',
  `receipt_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'RCPT',
  `next_receipt_number` int unsigned NOT NULL DEFAULT '1',
  `batch_purchase_prefix` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BP',
  `next_batch_purchase_number` int unsigned NOT NULL DEFAULT '1',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `zipcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stores` (`id`, `name`, `invoice_prefix`, `current_sale_invoice_sequence`, `next_sale_invoice_number`, `purchase_invoice_prefix`, `next_purchase_invoice_number`, `purchase_order_prefix`, `next_purchase_order_number`, `purchase_return_prefix`, `next_purchase_return_number`, `grn_prefix`, `next_grn_number`, `credit_note_prefix`, `next_credit_note_number`, `debit_note_prefix`, `next_debit_note_number`, `receipt_prefix`, `next_receipt_number`, `batch_purchase_prefix`, `next_batch_purchase_number`, `address`, `city`, `state`, `zipcode`, `phone`, `email`, `status`, `created_at`, `updated_at`) VALUES
(1,	'Burke Road',	'INV-BR',	'######',	1,	'PINV-BR',	5,	'POBR-BR',	2,	'PR-BR',	1,	'GRN-BR',	2,	'CN-BR',	1,	'DN-BR',	1,	'RCPT-BR',	1,	'BP-BR',	2,	'123 Burke Road',	'Spanish Town',	'St Catherine',	'00000',	'18761236587',	'burke.road@testdata.com',	1,	'2026-08-08 00:23:56',	'2026-10-20 22:44:29'),
(2,	'Harbour View',	'INV-HV',	'######',	1,	'PINV-HV',	1,	'PO-HV',	1,	'PR-HV',	1,	'GRN-HV',	1,	'CN-HV',	1,	'DN-HV',	1,	'RCPT-HV',	1,	'BP-HV',	1,	'77 Harbour View Drive',	'Kingston',	'Kingston',	'00000',	'18767426589',	'harbour.view@testdata.com',	1,	'2026-08-08 00:25:50',	'2026-08-08 00:25:50'),
(3,	'May Pen',	'INV-MP',	'######',	1,	'PINV-MP',	1,	'PO-MP',	1,	'PR-MP',	1,	'GRN-MP',	1,	'CN-MP',	1,	'DN-MP',	1,	'RCPT-MP',	1,	'BP-MP',	1,	'39 May Pen Street',	'May Pen',	'Clarendon',	'00000',	'18768954789',	'may.pen@testdata.com',	1,	'2026-08-08 00:30:09',	'2026-08-08 00:30:09');

DROP TABLE IF EXISTS `supplier_payments`;
CREATE TABLE `supplier_payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `supplier_purchase_invoice_id` bigint unsigned DEFAULT NULL,
  `payment_date` date NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `payment_method` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_no` varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `supplier_payments_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_payments_supplier_purchase_invoice_id_foreign` (`supplier_purchase_invoice_id`),
  CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE,
  CONSTRAINT `supplier_payments_supplier_purchase_invoice_id_foreign` FOREIGN KEY (`supplier_purchase_invoice_id`) REFERENCES `supplier_purchase_invoices` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `supplier_purchase_invoice_items`;
CREATE TABLE `supplier_purchase_invoice_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_purchase_invoice_id` bigint unsigned NOT NULL,
  `product_id` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `product_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `qty` int unsigned NOT NULL DEFAULT '1',
  `unit_price` decimal(15,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `is_taxable` tinyint(1) NOT NULL DEFAULT '1',
  `tax_rate` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_spi_items_invoice_id` (`supplier_purchase_invoice_id`),
  CONSTRAINT `fk_spi_items_invoice_id` FOREIGN KEY (`supplier_purchase_invoice_id`) REFERENCES `supplier_purchase_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `supplier_purchase_invoice_items` (`id`, `supplier_purchase_invoice_id`, `product_id`, `product_name`, `qty`, `unit_price`, `line_total`, `is_taxable`, `tax_rate`, `tax_amount`, `created_at`, `updated_at`) VALUES
(1,	1,	'P_001',	'JSW 42\" LED TV',	8,	25000.00,	200000.00,	1,	15.00,	30000.00,	'2026-10-16 03:57:28',	'2026-10-16 03:57:28'),
(2,	1,	'P_002',	'FUJIFILM DIGITAL CAMERA X100VI',	15,	15500.00,	232500.00,	1,	15.00,	34875.00,	'2026-10-16 03:57:28',	'2026-10-16 03:57:28'),
(3,	2,	'P_002',	'FUJIFILM DIGITAL CAMERA X100VI',	10,	16750.00,	167500.00,	1,	15.00,	25125.00,	'2026-10-20 22:30:31',	'2026-10-20 22:30:31'),
(4,	2,	'P_006',	'GIGABYTE 27 inch QHD OLED Computer Monitor',	20,	18500.00,	370000.00,	1,	15.00,	55500.00,	'2026-10-20 22:30:31',	'2026-10-20 22:30:31'),
(5,	2,	'P_005',	'KOOFORWAY Mini PC Ryzen 7 8745HS 16GB',	20,	65250.00,	1305000.00,	1,	15.00,	195750.00,	'2026-10-20 22:30:31',	'2026-10-20 22:30:31'),
(6,	3,	'P_007',	'Brokerage Fee',	1,	25000.00,	25000.00,	0,	0.00,	0.00,	'2026-10-20 22:33:01',	'2026-10-20 22:33:01'),
(7,	4,	'P_009',	'Customs Duty 10%',	1,	125000.00,	125000.00,	0,	0.00,	0.00,	'2026-10-20 22:34:59',	'2026-10-20 22:34:59');

DROP TABLE IF EXISTS `supplier_purchase_invoices`;
CREATE TABLE `supplier_purchase_invoices` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `supplier_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned DEFAULT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `internal_invoice_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `invoice_number` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `draft_reference` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_limit_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_percent` decimal(8,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `tax_exempt` tinyint(1) NOT NULL DEFAULT '0',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `paid_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `due_amount` decimal(15,2) NOT NULL DEFAULT '0.00',
  `status` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'unpaid',
  `invoice_status` enum('Draft','Confirmed','Cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Confirmed',
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `supplier_purchase_invoices_internal_invoice_number_unique` (`internal_invoice_number`),
  KEY `supplier_purchase_invoices_supplier_id_foreign` (`supplier_id`),
  KEY `supplier_purchase_invoices_store_id_foreign` (`store_id`),
  KEY `supplier_purchase_invoices_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `supplier_purchase_invoices_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_purchase_invoices_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE SET NULL,
  CONSTRAINT `supplier_purchase_invoices_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `supplier_purchase_invoices` (`id`, `supplier_id`, `store_id`, `purchase_order_id`, `internal_invoice_number`, `invoice_number`, `draft_reference`, `credit_limit_blocked`, `invoice_date`, `due_date`, `subtotal`, `tax_percent`, `tax_amount`, `tax_exempt`, `total`, `paid_amount`, `due_amount`, `status`, `invoice_status`, `notes`, `created_at`, `updated_at`) VALUES
(1,	1,	1,	1,	'PINV-BR000001',	'236587',	NULL,	0,	'2026-10-16',	NULL,	432500.00,	0.00,	64875.00,	0,	497375.00,	0.00,	497375.00,	'unpaid',	'Confirmed',	NULL,	'2026-10-16 03:57:28',	'2026-10-16 03:57:28'),
(2,	2,	1,	NULL,	'PINV-BR000002',	NULL,	NULL,	0,	'2026-10-21',	NULL,	1842500.00,	0.00,	276375.00,	0,	2118875.00,	0.00,	2118875.00,	'unpaid',	'Confirmed',	NULL,	'2026-10-20 22:30:31',	'2026-10-20 22:30:31'),
(3,	4,	1,	NULL,	'PINV-BR000003',	'3698',	NULL,	0,	'2026-10-21',	NULL,	25000.00,	0.00,	0.00,	0,	25000.00,	0.00,	25000.00,	'unpaid',	'Confirmed',	NULL,	'2026-10-20 22:33:01',	'2026-10-20 22:33:01'),
(4,	5,	1,	NULL,	'PINV-BR000004',	'CA56987',	NULL,	0,	'2026-10-21',	NULL,	125000.00,	0.00,	0.00,	0,	125000.00,	0.00,	125000.00,	'unpaid',	'Confirmed',	NULL,	'2026-10-20 22:34:59',	'2026-10-20 22:34:59');

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `company_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tax_number` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `status` tinyint unsigned DEFAULT '1',
  `credit_limit` decimal(12,2) unsigned DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `suppliers_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `suppliers` (`id`, `code`, `company_name`, `contact_name`, `email`, `phone`, `address`, `city`, `country`, `tax_number`, `notes`, `status`, `credit_limit`, `created_at`, `updated_at`) VALUES
(1,	'SUP-00001',	'Alpha Distributors',	'Neo Matryx',	'info@alphadistributors.com',	'1-876-650-9994',	'48 Turtles Boulevard',	'Mandeville',	'Jamaica',	NULL,	NULL,	1,	0.00,	'2026-08-08 03:21:15',	'2026-08-08 03:21:15'),
(2,	'SUP-00002',	'Secondus Enterprise Ltd',	'Bob Dyelong',	'sales@secenterpriseltd.com',	'1-876-958-1537',	'82 Main Street',	'Ocho Rios',	'Jamaica',	NULL,	NULL,	1,	0.00,	'2026-08-08 03:25:04',	'2026-08-08 03:25:04'),
(3,	'SUP-00003',	'Travellers & Rollers Ltd',	'Patt Boon',	'sales@travellersrollers.com',	'1-876-922-1044',	'36 Stukonewe Way',	'Kingston',	'Jamaica',	NULL,	NULL,	1,	0.00,	'2026-08-08 03:31:50',	'2026-08-08 03:31:50'),
(4,	'SUP-00004',	'Add Libz Custom Brokers',	'Kitty Liscombe',	'info@addlibzcb.com',	'1-876-845-6622',	'63 Newport Southwest Blvd',	'Kingston',	'Jamaica',	NULL,	NULL,	1,	0.00,	'2026-08-08 03:34:52',	'2026-08-08 03:34:52'),
(5,	'SUP-00005',	'Jamaica Customz Agency',	'Beare Foote',	'support@jacustomz.gov.jm',	'1-876-902-1700',	'99 Main Street',	'Kingston',	'Jamaica',	NULL,	NULL,	1,	0.00,	'2026-08-08 03:39:33',	'2026-08-08 03:39:33');

DROP TABLE IF EXISTS `transaction_account_mappings`;
CREATE TABLE `transaction_account_mappings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `source_type` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `debit_account_id` bigint unsigned NOT NULL,
  `credit_account_id` bigint unsigned NOT NULL,
  `label` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_account_mappings_source_type_unique` (`source_type`),
  KEY `transaction_account_mappings_debit_account_id_foreign` (`debit_account_id`),
  KEY `transaction_account_mappings_credit_account_id_foreign` (`credit_account_id`),
  CONSTRAINT `transaction_account_mappings_credit_account_id_foreign` FOREIGN KEY (`credit_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `transaction_account_mappings_debit_account_id_foreign` FOREIGN KEY (`debit_account_id`) REFERENCES `gl_accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `transaction_account_mappings` (`id`, `source_type`, `debit_account_id`, `credit_account_id`, `label`, `created_at`, `updated_at`) VALUES
(1,	'sale_invoice',	2,	9,	'Sale invoice (revenue)',	'2026-06-09 05:49:14',	'2026-06-09 05:49:14'),
(2,	'purchase_invoice',	3,	5,	'Purchase invoice',	'2026-06-09 05:49:14',	'2026-06-09 05:49:14'),
(3,	'credit_note',	9,	2,	'Credit note',	'2026-06-09 05:49:14',	'2026-06-09 05:49:14'),
(4,	'debit_note',	5,	3,	'Debit note',	'2026-06-09 05:49:14',	'2026-06-09 05:49:14'),
(5,	'customer_payment',	1,	2,	'Customer receipt',	'2026-06-09 05:49:14',	'2026-06-09 05:49:14'),
(6,	'supplier_payment',	5,	1,	'Supplier payment',	'2026-06-09 05:49:14',	'2026-06-09 05:49:14');

DROP TABLE IF EXISTS `units`;
CREATE TABLE `units` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `units` (`id`, `name`, `slug`, `status`, `created_at`, `updated_at`) VALUES
(1,	'unit',	'unit',	1,	'2026-08-08 00:44:09',	'2026-08-08 01:00:27'),
(2,	'each',	'each',	1,	'2026-08-08 00:44:29',	'2026-08-08 00:51:36'),
(3,	'pcs',	'pcs',	1,	'2026-08-08 00:44:52',	'2026-08-08 00:51:49');

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `permission_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_permissions_user_id_foreign` (`user_id`),
  KEY `user_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`) VALUES
(1,	2,	1),
(2,	2,	2),
(3,	2,	3),
(4,	2,	4),
(5,	2,	5),
(7,	2,	7),
(8,	2,	8),
(9,	2,	9),
(10,	2,	10),
(11,	2,	11),
(12,	2,	12),
(13,	2,	13),
(15,	2,	15),
(16,	2,	16),
(17,	2,	17),
(18,	2,	18),
(19,	2,	19),
(20,	2,	20),
(21,	3,	1),
(22,	3,	2),
(23,	3,	3),
(27,	3,	7),
(28,	3,	8),
(29,	3,	9),
(30,	3,	10),
(31,	3,	11),
(32,	3,	12),
(33,	3,	13),
(35,	3,	15),
(36,	3,	16),
(37,	3,	17),
(38,	3,	18),
(39,	3,	19),
(40,	3,	20),
(41,	17,	1),
(42,	17,	2),
(55,	17,	15),
(56,	17,	16),
(58,	17,	18),
(59,	17,	19),
(61,	14,	21),
(62,	2,	21),
(63,	3,	21),
(64,	4,	1),
(65,	4,	2),
(66,	4,	3),
(67,	4,	4),
(68,	4,	5),
(69,	4,	7),
(70,	4,	8),
(71,	4,	9),
(72,	4,	10),
(73,	4,	11),
(74,	4,	12),
(75,	4,	13),
(76,	4,	15),
(77,	4,	16),
(78,	4,	17),
(79,	4,	18),
(80,	4,	19),
(81,	4,	20),
(82,	4,	21),
(83,	17,	4),
(84,	17,	5),
(85,	3,	4),
(86,	3,	5),
(87,	1,	1),
(88,	1,	2),
(89,	1,	3),
(90,	1,	4),
(91,	1,	5),
(92,	1,	7),
(93,	1,	8),
(94,	1,	9),
(95,	1,	10),
(96,	1,	11),
(97,	1,	12),
(98,	1,	13),
(99,	1,	15),
(100,	1,	16),
(101,	1,	17),
(102,	1,	18),
(103,	1,	19),
(104,	1,	20),
(105,	1,	21);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emp_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dob` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nationality` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `joining_date` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_no2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `zipcode` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_store` int DEFAULT NULL,
  `status` tinyint unsigned NOT NULL DEFAULT '1',
  `selected_store_id` bigint unsigned DEFAULT NULL,
  `max_discount` int DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `first_name`, `last_name`, `username`, `email`, `email_verified_at`, `role_id`, `password`, `profile_image`, `phone`, `emp_code`, `dob`, `gender`, `nationality`, `joining_date`, `emergency_no1`, `emergency_no2`, `address`, `city`, `state`, `country`, `zipcode`, `assigned_store`, `status`, `selected_store_id`, `max_discount`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Super Admin',	'Super',	'Admin',	'SuperAdmin',	'superadmin@gmail.com',	'2025-04-29 00:30:27',	1,	'$2y$12$pg3CBWG.3WfjJBibnK4ZLO8Ox3TxI6uBeioZVuyRhC.U6ODQv/u4u',	'uploads/EcxWx70q4c83C6v6ri0yjH6ICjib71hSdk9ASvje.jpg',	'12345678910',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'12345678',	NULL,	1,	1,	NULL,	'TmEy1Onettag5RSBKphbNpEUhN9dI7O4zX470Eiz2HIS8hiwxUEmWqAQ5mFS',	'2025-04-29 00:30:27',	'2026-10-16 03:43:12'),
(2,	'Admin Admin',	'Admin',	'Admin',	'Admin',	'admin@gmail.com',	'2025-04-29 00:30:27',	2,	'$2y$12$7Og4sNUcxK.kcmY4/77yCua2uHWbsZCvxOs088E0TN9jVeFaJGV5.',	'uploads/GvNbSiFc7StVLsK6eDSkjGyHINNZorg2yDxLQPtZ.png',	'8888888880',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'jhjhjkhk',	NULL,	NULL,	NULL,	NULL,	NULL,	1,	9,	NULL,	'iUQunTEcfE15kKJjKa0mPMq8T0LdcvLjyeJA6PIkPnJddoKpkqWGz1SHgrgi',	'2025-04-29 00:30:28',	'2026-05-07 00:05:44'),
(3,	'Store Manager A',	'Store',	'Manager A',	'StoreManager',	'storemanager@gmail.com',	'2025-04-29 00:30:28',	3,	'$2y$12$gQuIU1KLJMt1mElqvZSRtONwHTpN2bx0R8J44J354fojzj6bSraSm',	'uploads/XOYxiHvNZPeSQlnFCmFfEwUoljar6D0HN69c6IJ4.png',	'1122334455',	'zxc',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	1,	1,	NULL,	'bGbGhyWg62jtynGjCBwe3s44IqWEVpakAMuhPX7mUIRjPjvX4KcWr3zUE1Ct',	'2025-04-29 00:30:28',	'2025-08-08 19:32:45'),
(4,	'StoreManager B',	'StoreManager',	'B',	'bb1',	'storemanagerB@gmail.com',	NULL,	3,	'$2y$12$GHq0QMPgrPM0wEI0IPkLBupOHPpffg4Tok8xQDNoxmiwlhMkI/yo6',	'uploads/e0aceBMx9E3QbVNaDrMkzQurZ2qL08bhaeDkRCzB.jpg',	'12345678910',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	2,	1,	2,	NULL,	NULL,	'2025-04-29 01:15:16',	'2025-06-05 07:04:34'),
(5,	'StoreManager C',	'StoreManager',	'C',	'bw11',	'storemanagerC@gmail.com',	NULL,	3,	'$2y$12$167ERsc6J/3gzC/uxJ7uWuailJhlxCFNHXIZwGYyQxbm/Njg9XnU2',	NULL,	'12345678',	'6',	'02-04-2025',	'Male',	'India',	'30-04-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	3,	1,	3,	NULL,	NULL,	'2025-04-30 14:40:53',	'2025-05-28 10:31:50'),
(6,	'Test Store Manager',	'Test Store',	'Manager',	'teststoremanager@gmail.com',	'teststoremanager@gmail.com',	NULL,	3,	'$2y$12$BRu2.sqW4Fn9PXzyzwR00erv/.oxUwCwd4XgAhNbvJqWN1DqaIaMW',	'uploads/RFf0FUAeRSttFSTYxGU3OC4SXKsoWDnrLqQIWdKj.png',	'6777556776',	'03',	'01-06-2004',	'Male',	'UK',	'01-05-2025',	'6777556776',	'6777556776',	'noida sector 8',	'Noida',	NULL,	'UK',	'201301',	7,	1,	7,	NULL,	NULL,	'2025-05-01 05:25:55',	'2025-05-01 05:30:08'),
(8,	'Store manager test',	'Store',	'manager test',	'storemanagertest@gmail.com',	'storemanagertest@gmail.com',	NULL,	3,	'$2y$12$aTyEsRuPcxxhhDut/V9Xi.PNEFefX0VQKzZMOgePUgaDPv8V7hxXK',	NULL,	'7890252508',	'08',	'01-06-2003',	'Male',	'India',	'05-05-2025',	'6777556776',	'6777556776',	'e32',	'California city',	'California',	'USA',	'201301',	7,	1,	7,	NULL,	NULL,	'2025-05-05 06:05:46',	'2025-05-05 06:06:35'),
(9,	'Sunit Gupta',	'Sunit',	'Gupta',	'StoreManager12',	'sun1608it@gmail.com',	NULL,	3,	'$2y$12$O7Eie8VCBuxxKIwrCzC.1uC4COZw/vJNBWcF7X.izsgAvl5aYqXK6',	'uploads/O2HsyXPmTi23EmTxMkUgO7KrQ3xCtBbRWp4kC75R.jpg',	'09879879878',	'176',	NULL,	NULL,	NULL,	'08-02-2026',	NULL,	NULL,	'abcd 1234',	'new jersey',	'Delhi',	'India',	'118899',	7,	1,	7,	0,	NULL,	'2025-05-28 10:37:48',	'2026-02-09 07:36:52'),
(11,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'sdgadfg',	'ZSFs@gmail',	NULL,	1,	'$2y$12$i8m1hzgEJ6PYprQlvKYVy.iAMEZuqug5WhitkzlMdYHSA8jS9ZiZ2',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	NULL,	NULL,	'fghj',	NULL,	1,	NULL,	NULL,	NULL,	'2025-06-10 11:30:27',	'2025-06-10 11:30:27'),
(14,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'zx',	'xcbgzcf@gmai',	NULL,	2,	'$2y$12$GWj/dLr1dz3HLLPtZTQPIOZRruOwYNfV5p69v0wkyU49ETfNfovdS',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	'sfgjsfjgsjg',	'India',	'fghj',	NULL,	1,	NULL,	NULL,	NULL,	'2025-06-10 11:50:29',	'2025-06-10 11:50:29'),
(16,	'xsfdh dfhj',	'xsfdh',	'dfhj',	'SuZXDFCD',	'asfASFASDF@GM',	NULL,	1,	'$2y$12$cpWwXF5s5DkgwxGbINML3.pkd2olfYCQwjNpAhUEvtv88MrogEs7y',	'uploads/wrdoYZsHBlfvwqMH2sbMM8zncRa5WjQvn3ETm4P6.jpg',	'123456789111',	'dshgzdfa',	'12-06-2025',	'Male',	NULL,	'11-06-2025',	NULL,	NULL,	'zsdx',	'Groningen, Nethefgrlands',	NULL,	NULL,	'fghj',	NULL,	1,	NULL,	NULL,	NULL,	'2025-06-12 07:13:17',	'2025-06-12 07:13:57'),
(17,	'Clerk 1',	'Clerk',	'1',	'Clerkabc',	'clerk@gmail.com',	NULL,	5,	'$2y$12$1cPCLMPzdKs.RSOk.X2Oge4A5NtWNLyr5zHOTCYJhWeQCvXS0U5vW',	NULL,	'1234567891023',	'0123',	'08-08-2025',	'Male',	'India',	'08-08-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	9,	1,	9,	60,	NULL,	'2025-08-08 19:33:54',	'2026-03-30 21:44:51'),
(18,	'Clerk 2',	'Clerk',	'2',	'clerk2',	'clerk2@gmail.com',	NULL,	5,	'$2y$12$n.GO9TFfHDqqNq0A4aE.LeNCq5cT0LCOqK.SQZQagFCUF4fiqkUrq',	NULL,	'1234567812',	'235',	NULL,	NULL,	NULL,	'10-08-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	2,	1,	2,	50,	NULL,	'2025-08-11 17:35:22',	'2025-08-11 17:36:02'),
(19,	'Joe Kerr',	'Joe',	'Kerr',	'joker',	'muiride@vadgroupjm.com',	NULL,	1,	'$2y$12$SoFYchkFZxRR3tbJBarHZ.m3hEmcUJaynnC1WmVMkjuUp7aCZLBei',	NULL,	'8761234567',	'JK008',	NULL,	NULL,	NULL,	'21-10-2025',	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	NULL,	1,	NULL,	0,	NULL,	'2025-10-22 04:16:08',	'2026-04-07 19:55:28');

-- 2026-10-23 05:17:37
