-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Feb 21, 2026 at 08:36 PM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ledgera`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounting_periods`
--

CREATE TABLE `accounting_periods` (
  `id` int(1) NOT NULL,
  `company_id` int(1) NOT NULL,
  `period_month` tinyint(4) DEFAULT NULL,
  `period_year` smallint(6) DEFAULT NULL,
  `is_closed` tinyint(4) DEFAULT 0,
  `closed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accounting_periods`
--

INSERT INTO `accounting_periods` (`id`, `company_id`, `period_month`, `period_year`, `is_closed`, `closed_at`) VALUES
(1, 1, 1, 2026, 1, '2026-02-21 18:55:59'),
(2, 1, 2, 2026, 1, '2026-02-21 18:56:06'),
(3, 1, 3, 2026, 0, NULL),
(4, 1, 4, 2026, 0, NULL),
(5, 1, 5, 2026, 0, NULL),
(6, 1, 6, 2026, 0, NULL),
(7, 1, 7, 2026, 0, NULL),
(8, 1, 8, 2026, 0, NULL),
(9, 1, 9, 2026, 0, NULL),
(10, 1, 10, 2026, 0, NULL),
(11, 1, 11, 2026, 0, NULL),
(12, 1, 12, 2026, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `approval_flows`
--

CREATE TABLE `approval_flows` (
  `id` int(1) NOT NULL,
  `company_id` int(1) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `level` int(1) DEFAULT NULL,
  `role_name` varchar(50) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_logs`
--

CREATE TABLE `approval_logs` (
  `id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `step_order` int(1) NOT NULL,
  `role_id` int(1) NOT NULL,
  `approved_by` int(1) NOT NULL,
  `status` enum('pending','approved','rejected','') NOT NULL,
  `note` varchar(250) NOT NULL,
  `approved_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_rules`
--

CREATE TABLE `approval_rules` (
  `id` int(1) NOT NULL,
  `approval_flow_id` int(1) NOT NULL,
  `min_amount` decimal(18,2) NOT NULL,
  `max_amount` decimal(18,2) DEFAULT NULL,
  `auto_approve` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `approval_steps`
--

CREATE TABLE `approval_steps` (
  `id` int(11) NOT NULL,
  `approval_rule_id` int(1) NOT NULL,
  `step_order` int(1) NOT NULL,
  `role_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` int(1) NOT NULL,
  `company_id` int(1) DEFAULT NULL,
  `table_name` varchar(50) DEFAULT NULL,
  `record_id` int(1) DEFAULT NULL,
  `action` enum('insert','update','delete') DEFAULT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `user_id` int(1) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `branches`
--

CREATE TABLE `branches` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `branch_code` varchar(20) DEFAULT NULL,
  `branch_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `company_id`, `branch_code`, `branch_name`) VALUES
(1, 1, 'HQ', 'Head Office'),
(2, 1, 'BDG', 'Branch Bandung');

-- --------------------------------------------------------

--
-- Table structure for table `business_partners`
--

CREATE TABLE `business_partners` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `partner_type` enum('customer','vendor') DEFAULT NULL,
  `partner_code` varchar(30) DEFAULT NULL,
  `partner_name` varchar(100) DEFAULT NULL,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_partners`
--

INSERT INTO `business_partners` (`id`, `company_id`, `partner_type`, `partner_code`, `partner_name`, `deleted_at`) VALUES
(1, 1, 'customer', 'C001', 'PT Customer A', '0000-00-00 00:00:00'),
(2, 1, 'vendor', 'V001', 'PT Vendor B', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `coa`
--

CREATE TABLE `coa` (
  `id` int(1) NOT NULL,
  `company_id` int(1) NOT NULL,
  `account_code` varchar(20) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_type` enum('asset','liability','equity','revenue','expense','cogs') DEFAULT NULL,
  `parent_id` int(1) DEFAULT NULL,
  `cashflow_type` enum('operating','investing','financing') DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coa`
--

INSERT INTO `coa` (`id`, `company_id`, `account_code`, `account_name`, `account_type`, `parent_id`, `cashflow_type`, `is_active`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 1, '1101', 'Kas', 'asset', 14, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 1, '1201', 'Piutang Usaha', 'asset', 14, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(3, 1, '1301', 'Persediaan', 'asset', 14, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(4, 1, '2101', 'Utang Usaha', 'liability', 15, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(5, 1, '2201', 'Utang Pajak', 'liability', 15, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(6, 1, '3100', 'Modal Disetor', 'equity', 11, 'financing', 1, NULL, 0, NULL, NULL, NULL, NULL),
(7, 1, '3200', 'Laba Ditahan', 'equity', 11, 'financing', 1, NULL, 0, NULL, NULL, NULL, NULL),
(8, 1, '4101', 'Penjualan', 'revenue', 17, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(9, 1, '5101', 'Beban Gaji', 'expense', 18, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(10, 1, '5201', 'Beban Operasional', 'expense', 18, 'operating', 1, NULL, 0, NULL, NULL, NULL, NULL),
(11, 1, '3000', 'Ekuitas', 'equity', NULL, 'financing', 1, NULL, 0, NULL, NULL, NULL, NULL),
(12, 1, '3300', 'Prive', 'equity', 11, 'financing', 1, NULL, 0, NULL, NULL, NULL, NULL),
(14, 1, '1000', 'Aset', 'asset', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 1, '2000', 'Kewajiban', 'liability', NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 1, '4000', 'Pendapatan', 'revenue', NULL, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 1, '5000', 'Beban', 'expense', NULL, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 1, '6000', 'Harga Pokok Penjualan', 'cogs', NULL, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 1, '1102', 'Bank', 'asset', 14, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 1, '1103', 'Kas Kecil', 'asset', 14, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 1, '1401', 'PPN Masukan', 'asset', 14, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 1, '2102', 'Utang Gaji', 'liability', 15, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 1, '2202', 'PPN Keluaran', 'liability', 15, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 1, '3400', 'Saldo Laba Tahun Berjalan', 'equity', 11, 'financing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 1, '4102', 'Pendapatan Jasa', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 1, '4201', 'Pendapatan Lain-lain', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 1, '6101', 'HPP Penjualan', 'cogs', 19, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 1, '5202', 'Beban Listrik & Air', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 1, '5203', 'Beban Sewa', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 1, '5204', 'Beban Internet', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 1, '5205', 'Beban Transportasi', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 1, '1501', 'Aset Tetap', 'asset', 14, 'investing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 1, '1502', 'Akumulasi Penyusutan', 'asset', 14, 'investing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 1, '2301', 'Utang Bank', 'liability', 15, 'financing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 1, '1500', 'Kelompok Aset Tetap', 'asset', 14, 'investing', 1, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `coa_opening_balances`
--

CREATE TABLE `coa_opening_balances` (
  `id` int(1) NOT NULL,
  `company_id` int(1) NOT NULL,
  `coa_id` int(1) NOT NULL,
  `opening_balance` decimal(18,2) DEFAULT 0.00,
  `period_year` smallint(4) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `coa_opening_balances`
--

INSERT INTO `coa_opening_balances` (`id`, `company_id`, `coa_id`, `opening_balance`, `period_year`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 1, 150000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(2, 1, 20, 250000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(3, 1, 3, 80000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(4, 1, 22, 20000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(5, 1, 33, 500000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(6, 1, 4, 100000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(7, 1, 5, 50000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(8, 1, 35, 200000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(9, 1, 6, 500000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL),
(10, 1, 7, 150000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-02-21 17:52:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(1) NOT NULL,
  `company_code` varchar(20) DEFAULT NULL,
  `company_name` varchar(100) NOT NULL,
  `company_addr` varchar(250) DEFAULT NULL,
  `company_web` varchar(150) DEFAULT NULL,
  `company_logo` varchar(250) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(1) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `company_code`, `company_name`, `company_addr`, `company_web`, `company_logo`, `is_active`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 'COMP01', 'PT ERP Testing Indonesia', 'Jakarta Timur, DKI Jakarta', 'erptesting.com', NULL, 1, '2026-02-08 21:02:02', 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_years`
--

CREATE TABLE `fiscal_years` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `year_name` varchar(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fiscal_years`
--

INSERT INTO `fiscal_years` (`id`, `company_id`, `year_name`, `start_date`, `end_date`, `is_active`) VALUES
(1, 1, 'FY 2025', '2025-01-01', '2025-12-31', 1),
(2, 1, 'FY 2026', '2026-01-01', '2026-12-31', 1);

-- --------------------------------------------------------

--
-- Table structure for table `journal_approvals`
--

CREATE TABLE `journal_approvals` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `note` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_details`
--

CREATE TABLE `journal_details` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(18,2) DEFAULT 0.00,
  `credit` decimal(18,2) DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_details`
--

INSERT INTO `journal_details` (`id`, `journal_id`, `account_id`, `debit`, `credit`, `created_at`) VALUES
(31, 16, 1, 20000000.00, 0.00, '2026-02-21 18:31:48'),
(32, 16, 8, 0.00, 20000000.00, '2026-02-21 18:31:48'),
(33, 17, 9, 5000000.00, 0.00, '2026-02-21 18:32:08'),
(34, 17, 1, 0.00, 5000000.00, '2026-02-21 18:32:08'),
(35, 18, 29, 2000000.00, 0.00, '2026-02-21 18:32:49'),
(36, 18, 1, 0.00, 2000000.00, '2026-02-21 18:32:49'),
(39, 22, 1, 50000000.00, 0.00, '2026-02-21 19:19:40'),
(40, 22, 8, 0.00, 50000000.00, '2026-02-21 19:19:40');

-- --------------------------------------------------------

--
-- Table structure for table `journal_headers`
--

CREATE TABLE `journal_headers` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `fiscal_year_id` int(11) DEFAULT NULL,
  `journal_no` varchar(50) DEFAULT NULL,
  `journal_date` date DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `total_amount` decimal(18,2) DEFAULT 0.00,
  `period_month` tinyint(4) DEFAULT NULL,
  `period_year` smallint(6) DEFAULT NULL,
  `status` enum('draft','waiting','approved','posted','rejected') DEFAULT 'draft',
  `is_locked` tinyint(4) DEFAULT 0,
  `reversal_of` int(11) DEFAULT NULL,
  `reverse_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `journal_headers`
--

INSERT INTO `journal_headers` (`id`, `company_id`, `branch_id`, `fiscal_year_id`, `journal_no`, `journal_date`, `description`, `total_amount`, `period_month`, `period_year`, `status`, `is_locked`, `reversal_of`, `reverse_date`, `created_at`, `deleted_at`) VALUES
(16, 1, NULL, 2, 'AUTO-16', '2026-02-21', NULL, 0.00, 2, 2026, 'posted', 0, NULL, NULL, '2026-02-21 18:31:48', '0000-00-00 00:00:00'),
(17, 1, NULL, 2, 'AUTO-17', '2026-02-22', NULL, 0.00, 2, 2026, 'posted', 0, NULL, NULL, '2026-02-21 18:32:08', '0000-00-00 00:00:00'),
(18, 1, NULL, 2, 'AUTO-18', '2026-02-23', NULL, 0.00, 2, 2026, 'posted', 0, NULL, NULL, '2026-02-21 18:32:49', '0000-00-00 00:00:00'),
(19, 1, NULL, NULL, 'CLOSING-1-2026', '2026-01-31', NULL, 0.00, 1, 2026, 'posted', 1, NULL, NULL, '2026-02-21 18:55:59', '0000-00-00 00:00:00'),
(20, 1, NULL, NULL, 'CLOSING-2-2026', '2026-02-28', NULL, 0.00, 2, 2026, 'posted', 1, NULL, NULL, '2026-02-21 18:56:06', '0000-00-00 00:00:00'),
(22, 1, NULL, 2, 'AUTO-20', '2026-03-01', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-02-21 19:19:40', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `journal_taxes`
--

CREATE TABLE `journal_taxes` (
  `id` int(11) NOT NULL,
  `journal_id` int(11) DEFAULT NULL,
  `tax_id` int(11) DEFAULT NULL,
  `tax_base` decimal(18,2) DEFAULT NULL,
  `tax_amount` decimal(18,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int(1) NOT NULL,
  `module` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `module`, `action`, `code`) VALUES
(1, 'coa', 'view', 'coa.view'),
(2, 'coa', 'create', 'coa.create'),
(3, 'coa', 'edit', 'coa.edit'),
(4, 'journal', 'view', 'journal.view'),
(5, 'journal', 'create', 'journal.create'),
(6, 'journal', 'edit', 'journal.edit'),
(7, 'journal', 'approve', 'journal.approve'),
(8, 'sales', 'view', 'sales.view'),
(9, 'sales', 'create', 'sales.create'),
(10, 'sales', 'edit', 'sales.edit'),
(11, 'sales', 'approve', 'sales.approve'),
(12, 'report', 'view', 'report.view'),
(13, 'users', 'view', 'users.view'),
(14, 'users', 'create', 'users.create'),
(15, 'users', 'edit', 'users.edit'),
(16, 'users', 'delete', 'users.delete'),
(17, 'coa', 'delete', 'coa.delete');

-- --------------------------------------------------------

--
-- Table structure for table `refresh_tokens`
--

CREATE TABLE `refresh_tokens` (
  `id` int(1) NOT NULL,
  `user_id` int(1) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `retained_earnings`
--

CREATE TABLE `retained_earnings` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `period_month` tinyint(4) DEFAULT NULL,
  `period_year` smallint(6) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(1) NOT NULL,
  `company_id` int(1) DEFAULT NULL,
  `name` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `company_id`, `name`) VALUES
(1, 0, 'Super Admin'),
(2, 1, 'Company Owner');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--

CREATE TABLE `role_permissions` (
  `id` int(1) NOT NULL,
  `role_id` int(1) NOT NULL,
  `permission_id` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`) VALUES
(1, 1, 13),
(2, 1, 14),
(3, 1, 15),
(4, 1, 16),
(5, 2, 13),
(6, 2, 14),
(7, 2, 15),
(8, 2, 16),
(9, 1, 1),
(10, 1, 2),
(11, 1, 3),
(12, 1, 17),
(13, 2, 1),
(14, 2, 2),
(15, 2, 3),
(16, 2, 17);

-- --------------------------------------------------------

--
-- Table structure for table `sub_ledgers`
--

CREATE TABLE `sub_ledgers` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `partner_id` int(11) DEFAULT NULL,
  `journal_detail_id` int(11) DEFAULT NULL,
  `account_type` enum('AR','AP') DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tax_codes`
--

CREATE TABLE `tax_codes` (
  `id` int(11) NOT NULL,
  `tax_code` varchar(20) DEFAULT NULL,
  `tax_name` varchar(100) DEFAULT NULL,
  `tax_type` enum('ppn','withholding') DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `deleted_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tax_codes`
--

INSERT INTO `tax_codes` (`id`, `tax_code`, `tax_name`, `tax_type`, `tax_rate`, `is_active`, `deleted_at`) VALUES
(1, 'PPN11', 'PPN 11%', 'ppn', 11.00, 1, '0000-00-00 00:00:00'),
(2, 'PPh23', 'PPh 23', 'withholding', 2.00, 1, '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `branch_id` int(11) DEFAULT NULL,
  `trx_date` date DEFAULT NULL,
  `trx_type` varchar(30) DEFAULT NULL,
  `reference_no` varchar(50) DEFAULT NULL,
  `amount` decimal(18,2) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `journal_id` int(11) DEFAULT NULL,
  `debit_account_id` int(11) DEFAULT NULL,
  `credit_account_id` int(11) DEFAULT NULL,
  `status` enum('draft','submitted','approved','posted','rejected') DEFAULT 'draft',
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `company_id`, `branch_id`, `trx_date`, `trx_type`, `reference_no`, `amount`, `created_at`, `journal_id`, `debit_account_id`, `credit_account_id`, `status`, `updated_at`) VALUES
(16, 1, NULL, '2026-02-21', 'sales_cash', 'SC01', 20000000.00, '2026-02-21 18:31:48', 16, NULL, NULL, 'posted', '2026-02-21 18:31:48'),
(17, 1, NULL, '2026-02-22', 'expense_salary', 'SL01', 5000000.00, '2026-02-21 18:32:08', 17, NULL, NULL, 'posted', '2026-02-21 18:32:08'),
(18, 1, NULL, '2026-02-23', 'expense_electric', 'EL01', 2000000.00, '2026-02-21 18:32:49', 18, NULL, NULL, 'posted', '2026-02-21 18:32:49'),
(20, 1, NULL, '2026-03-01', 'sales_cash', 'SC03', 50000000.00, '2026-02-21 19:19:40', 22, NULL, NULL, 'posted', '2026-02-21 19:19:40');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_account_map`
--

CREATE TABLE `transaction_account_map` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `trx_type` varchar(30) NOT NULL,
  `debit_account_id` int(11) NOT NULL,
  `credit_account_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_account_map`
--

INSERT INTO `transaction_account_map` (`id`, `company_id`, `trx_type`, `debit_account_id`, `credit_account_id`, `created_at`) VALUES
(1, 1, 'sales_cash', 1, 8, '2026-02-21 18:21:20'),
(2, 1, 'expense_salary', 9, 1, '2026-02-21 18:21:27'),
(3, 1, 'expense_electric', 29, 1, '2026-02-21 18:21:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(1) NOT NULL,
  `company_id` int(1) NOT NULL,
  `branch_id` int(1) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(250) DEFAULT NULL,
  `photo` varchar(250) DEFAULT NULL,
  `is_active` enum('active','inactive') DEFAULT 'active',
  `last_login_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `created_by` int(1) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` int(1) DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `company_id`, `branch_id`, `name`, `email`, `phone`, `password`, `photo`, `is_active`, `last_login_at`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 0, 0, 'Mick Jagger', 'admin@admin.com', '0812', '$2y$10$TYZN8k0YxaB.jxCtqA4sl.JnllEeN3/UF9oGYK5.LTvbGlCe7HE82', NULL, 'active', '2026-02-12 12:11:18', '2026-01-18 12:25:53', 1, '2026-02-12 12:11:18', NULL, NULL, NULL),
(2, 1, 0, 'Arya Seftian', 'yerblues6@gmail.com', '895330907220', '$2y$10$relLlluCofLYvJKJDW65zuxFadTF4X4A.mCur9V2uEbiZVW8vGhaa', 'profile_2_1768811928.png', 'active', '2026-02-12 13:56:55', '2026-01-18 18:59:55', 1, '2026-02-12 13:56:55', NULL, NULL, NULL),
(3, 1, 0, 'Muhammad', 'muhammad@gmail.com', '99988776', '$2y$10$relLlluCofLYvJKJDW65zuxFadTF4X4A.mCur9V2uEbiZVW8vGhaa', 'profile_3_1768820480.png', 'active', '2026-02-21 16:09:08', '2026-01-19 10:53:08', 1, '2026-02-21 16:09:08', NULL, NULL, NULL),
(4, 1, 0, 'Muhammad', 'worker@gmail.com', '99988776', '$2y$10$relLlluCofLYvJKJDW65zuxFadTF4X4A.mCur9V2uEbiZVW8vGhaa', '1770800774_86ac11607620813dfeb3.png', 'active', NULL, '2026-01-19 10:53:08', 1, '2026-02-11 16:39:18', 2, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `id` int(1) NOT NULL,
  `user_id` int(1) NOT NULL,
  `role_id` int(1) NOT NULL,
  `branch_id` int(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `branch_id`) VALUES
(1, 1, 1, 0),
(2, 2, 2, 0);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_export_journal`
-- (See below for the actual view)
--
CREATE TABLE `vw_export_journal` (
`journal_no` varchar(50)
,`journal_date` date
,`account_code` varchar(20)
,`account_name` varchar(100)
,`debit` decimal(18,2)
,`credit` decimal(18,2)
,`description` varchar(255)
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_period` (`company_id`,`period_month`,`period_year`);

--
-- Indexes for table `approval_flows`
--
ALTER TABLE `approval_flows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `approval_logs`
--
ALTER TABLE `approval_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval_rules`
--
ALTER TABLE `approval_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `approval_steps`
--
ALTER TABLE `approval_steps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_approval_steps_role` (`role_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `branches`
--
ALTER TABLE `branches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `business_partners`
--
ALTER TABLE `business_partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `coa`
--
ALTER TABLE `coa`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `parent_id` (`parent_id`),
  ADD KEY `idx_coa_type` (`account_type`);

--
-- Indexes for table `coa_opening_balances`
--
ALTER TABLE `coa_opening_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_balance` (`company_id`,`coa_id`,`period_year`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `company_code` (`company_code`);

--
-- Indexes for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `journal_approvals`
--
ALTER TABLE `journal_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`);

--
-- Indexes for table `journal_details`
--
ALTER TABLE `journal_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`),
  ADD KEY `idx_journal_account` (`account_id`);

--
-- Indexes for table `journal_headers`
--
ALTER TABLE `journal_headers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `journal_no` (`journal_no`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `fiscal_year_id` (`fiscal_year_id`),
  ADD KEY `reversal_of` (`reversal_of`),
  ADD KEY `idx_journal_period` (`company_id`,`period_year`,`period_month`);

--
-- Indexes for table `journal_taxes`
--
ALTER TABLE `journal_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `journal_id` (`journal_id`),
  ADD KEY `tax_id` (`tax_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `retained_earnings`
--
ALTER TABLE `retained_earnings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role_permissions`
--
ALTER TABLE `role_permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_ledgers`
--
ALTER TABLE `sub_ledgers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `partner_id` (`partner_id`),
  ADD KEY `journal_detail_id` (`journal_detail_id`);

--
-- Indexes for table `tax_codes`
--
ALTER TABLE `tax_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `branch_id` (`branch_id`),
  ADD KEY `idx_trx_date` (`trx_date`),
  ADD KEY `idx_trx_type` (`trx_type`),
  ADD KEY `idx_company` (`company_id`),
  ADD KEY `idx_branch` (`branch_id`),
  ADD KEY `idx_journal` (`journal_id`);

--
-- Indexes for table `transaction_account_map`
--
ALTER TABLE `transaction_account_map`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `approval_flows`
--
ALTER TABLE `approval_flows`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_logs`
--
ALTER TABLE `approval_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_rules`
--
ALTER TABLE `approval_rules`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `approval_steps`
--
ALTER TABLE `approval_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `branches`
--
ALTER TABLE `branches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `business_partners`
--
ALTER TABLE `business_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coa`
--
ALTER TABLE `coa`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `coa_opening_balances`
--
ALTER TABLE `coa_opening_balances`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `journal_approvals`
--
ALTER TABLE `journal_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_details`
--
ALTER TABLE `journal_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `journal_headers`
--
ALTER TABLE `journal_headers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `journal_taxes`
--
ALTER TABLE `journal_taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `refresh_tokens`
--
ALTER TABLE `refresh_tokens`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `retained_earnings`
--
ALTER TABLE `retained_earnings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `role_permissions`
--
ALTER TABLE `role_permissions`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sub_ledgers`
--
ALTER TABLE `sub_ledgers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_codes`
--
ALTER TABLE `tax_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `transaction_account_map`
--
ALTER TABLE `transaction_account_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_roles`
--
ALTER TABLE `user_roles`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

-- --------------------------------------------------------

--
-- Structure for view `vw_export_journal`
--
DROP TABLE IF EXISTS `vw_export_journal`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_export_journal`  AS SELECT `jh`.`journal_no` AS `journal_no`, `jh`.`journal_date` AS `journal_date`, `coa`.`account_code` AS `account_code`, `coa`.`account_name` AS `account_name`, `jd`.`debit` AS `debit`, `jd`.`credit` AS `credit`, `jh`.`description` AS `description` FROM ((`journal_headers` `jh` join `journal_details` `jd` on(`jd`.`journal_id` = `jh`.`id`)) join `coa` on(`coa`.`id` = `jd`.`account_id`)) WHERE `jh`.`status` = 'posted' ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounting_periods`
--
ALTER TABLE `accounting_periods`
  ADD CONSTRAINT `accounting_periods_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `approval_flows`
--
ALTER TABLE `approval_flows`
  ADD CONSTRAINT `approval_flows_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `approval_steps`
--
ALTER TABLE `approval_steps`
  ADD CONSTRAINT `fk_approval_steps_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `branches`
--
ALTER TABLE `branches`
  ADD CONSTRAINT `branches_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `business_partners`
--
ALTER TABLE `business_partners`
  ADD CONSTRAINT `business_partners_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `coa`
--
ALTER TABLE `coa`
  ADD CONSTRAINT `coa_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `coa_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `coa` (`id`);

--
-- Constraints for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD CONSTRAINT `fiscal_years_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `journal_approvals`
--
ALTER TABLE `journal_approvals`
  ADD CONSTRAINT `journal_approvals_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_headers` (`id`);

--
-- Constraints for table `journal_details`
--
ALTER TABLE `journal_details`
  ADD CONSTRAINT `journal_details_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_headers` (`id`),
  ADD CONSTRAINT `journal_details_ibfk_2` FOREIGN KEY (`account_id`) REFERENCES `coa` (`id`);

--
-- Constraints for table `journal_headers`
--
ALTER TABLE `journal_headers`
  ADD CONSTRAINT `journal_headers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `journal_headers_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `journal_headers_ibfk_3` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`),
  ADD CONSTRAINT `journal_headers_ibfk_4` FOREIGN KEY (`reversal_of`) REFERENCES `journal_headers` (`id`);

--
-- Constraints for table `journal_taxes`
--
ALTER TABLE `journal_taxes`
  ADD CONSTRAINT `journal_taxes_ibfk_1` FOREIGN KEY (`journal_id`) REFERENCES `journal_headers` (`id`),
  ADD CONSTRAINT `journal_taxes_ibfk_2` FOREIGN KEY (`tax_id`) REFERENCES `tax_codes` (`id`);

--
-- Constraints for table `retained_earnings`
--
ALTER TABLE `retained_earnings`
  ADD CONSTRAINT `retained_earnings_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `sub_ledgers`
--
ALTER TABLE `sub_ledgers`
  ADD CONSTRAINT `sub_ledgers_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `sub_ledgers_ibfk_2` FOREIGN KEY (`partner_id`) REFERENCES `business_partners` (`id`),
  ADD CONSTRAINT `sub_ledgers_ibfk_3` FOREIGN KEY (`journal_detail_id`) REFERENCES `journal_details` (`id`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
