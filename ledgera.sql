-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 01, 2026 at 03:38 PM
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
  `branch_name` varchar(100) DEFAULT NULL,
  `branch_address` varchar(255) DEFAULT NULL,
  `branch_logo` varchar(255) DEFAULT NULL,
  `target` decimal(12,2) NOT NULL,
  `room_revenue` decimal(12,2) NOT NULL,
  `fb_revenue` decimal(12,2) NOT NULL,
  `tax_service` int(11) NOT NULL,
  `total_margin` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches`
--

INSERT INTO `branches` (`id`, `company_id`, `branch_code`, `branch_name`, `branch_address`, `branch_logo`, `target`, `room_revenue`, `fb_revenue`, `tax_service`, `total_margin`) VALUES
(2, 1, 'ME', 'MidEast', 'Jl. Sancang No.8B 16128 Bogor', 'assets/img/logo-mideast.jpg', 0.00, 0.00, 0.00, 0, 0.00),
(3, 1, 'SBH', 'Sahira Butik Hotel Paledang', 'Jl. Paledang No. 53 16122 Bogor West Java', 'assets/img/logo-sbh.png', 0.00, 0.00, 0.00, 0, 0.00),
(4, 1, 'HW', 'HeyWork', '', '', 0.00, 0.00, 0.00, 0, 0.00),
(5, 1, 'HM', 'HeyMeal', '', '', 0.00, 0.00, 0.00, 0, 0.00),
(6, 1, 'SS', 'Salam Supply', '', '', 0.00, 0.00, 0.00, 0, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `branches_target`
--

CREATE TABLE `branches_target` (
  `id` int(11) NOT NULL,
  `branch_id` int(11) NOT NULL,
  `target` int(11) NOT NULL,
  `room_revenue` int(11) NOT NULL,
  `fb_revenue` int(11) NOT NULL,
  `tax_service` int(11) NOT NULL,
  `total_margin` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branches_target`
--

INSERT INTO `branches_target` (`id`, `branch_id`, `target`, `room_revenue`, `fb_revenue`, `tax_service`, `total_margin`) VALUES
(2, 3, 350000000, 40, 60, 21, 79);

-- --------------------------------------------------------

--
-- Table structure for table `branch_items`
--

CREATE TABLE `branch_items` (
  `id` bigint(20) NOT NULL,
  `branch_id` int(20) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `variant_id` bigint(1) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `tax_type` enum('pb1','ppn','none') DEFAULT 'none',
  `stock` int(11) DEFAULT 0,
  `status` enum('available','out_of_stock') DEFAULT 'available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_items`
--

INSERT INTO `branch_items` (`id`, `branch_id`, `item_id`, `variant_id`, `price`, `tax_type`, `stock`, `status`) VALUES
(1, 3, 10, NULL, 0.00, 'pb1', 0, 'available'),
(2, 3, 11, NULL, 0.00, 'pb1', 0, 'available'),
(3, 3, 12, NULL, 0.00, 'ppn', 0, 'available'),
(5, 3, 14, NULL, 0.00, 'pb1', 0, 'available'),
(6, 3, 15, NULL, 0.00, 'pb1', 0, 'available'),
(7, 3, 16, NULL, 0.00, 'pb1', 0, 'available'),
(8, 3, 17, NULL, 0.00, 'pb1', 0, 'available'),
(9, 3, 18, NULL, 0.00, 'pb1', 0, 'available'),
(10, 3, 19, NULL, 0.00, 'none', 0, 'available'),
(11, 3, 20, NULL, 0.00, 'none', 0, 'available'),
(12, 3, 21, NULL, 0.00, 'none', 0, 'available'),
(13, 3, 22, NULL, 0.00, 'pb1', 0, 'available'),
(14, 3, 23, NULL, 0.00, 'pb1', 0, 'available'),
(15, 3, 24, NULL, 0.00, 'pb1', 0, 'available'),
(16, 3, 25, NULL, 0.00, 'pb1', 0, 'available'),
(17, 3, 26, NULL, 0.00, 'none', 0, 'available'),
(18, 3, 27, NULL, 0.00, 'ppn', 0, 'available'),
(19, 3, 28, NULL, 0.00, 'ppn', 0, 'available'),
(20, 3, 29, NULL, 0.00, 'ppn', 0, 'available'),
(21, 2, 30, NULL, 0.00, 'pb1', 0, 'available'),
(22, 2, 31, NULL, 0.00, 'pb1', 0, 'available'),
(23, 2, 32, NULL, 0.00, 'pb1', 0, 'available'),
(24, 2, 33, NULL, 0.00, 'pb1', 0, 'available'),
(25, 2, 34, NULL, 0.00, 'pb1', 0, 'available'),
(26, 2, 35, NULL, 0.00, 'pb1', 0, 'available');

-- --------------------------------------------------------

--
-- Table structure for table `branch_opening_hours`
--

CREATE TABLE `branch_opening_hours` (
  `id` bigint(20) NOT NULL,
  `branch_id` int(20) DEFAULT NULL,
  `day_of_week` enum('monday','tuesday','wednesday','thursday','friday','saturday','sunday') DEFAULT NULL,
  `open_time` time DEFAULT NULL,
  `close_time` time DEFAULT NULL,
  `is_closed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_opening_hours`
--

INSERT INTO `branch_opening_hours` (`id`, `branch_id`, `day_of_week`, `open_time`, `close_time`, `is_closed`) VALUES
(1, 2, 'monday', '09:00:00', '21:00:00', 0),
(2, 2, 'tuesday', '09:00:00', '21:00:00', 0),
(3, 2, 'wednesday', '09:00:00', '21:00:00', 0),
(4, 2, 'thursday', '09:00:00', '21:00:00', 0),
(5, 2, 'friday', '09:00:00', '21:00:00', 0),
(6, 2, 'saturday', '09:00:00', '21:00:00', 0),
(7, 2, 'sunday', '09:00:00', '21:00:00', 0),
(8, 2, 'monday', '10:00:00', '23:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `branch_referral_rules`
--

CREATE TABLE `branch_referral_rules` (
  `id` bigint(20) NOT NULL,
  `branch_id` int(20) NOT NULL,
  `referrer_reward_type` enum('points','wallet','voucher','free_drink') DEFAULT 'points',
  `referrer_reward_value` decimal(12,2) DEFAULT 0.00,
  `referee_reward_type` enum('points','wallet','voucher','free_drink') DEFAULT 'points',
  `referee_reward_value` decimal(12,2) DEFAULT 0.00,
  `min_order` decimal(12,2) DEFAULT 0.00,
  `max_referral_per_user` int(11) DEFAULT 10,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `branch_referral_rules`
--

INSERT INTO `branch_referral_rules` (`id`, `branch_id`, `referrer_reward_type`, `referrer_reward_value`, `referee_reward_type`, `referee_reward_value`, `min_order`, `max_referral_per_user`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 'points', 100.00, 'points', 50.00, 50000.00, 10, 'active', '2026-03-17 00:49:47', NULL);

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
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `branch_id` int(20) DEFAULT NULL,
  `status` enum('active','checked_out','expired') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `branch_id`, `status`, `created_at`) VALUES
(1, 9, 3, 'checked_out', '2026-03-27 03:23:55'),
(3, 2, 3, 'checked_out', '2026-03-27 05:46:16'),
(6, 3, 2, 'checked_out', '2026-03-27 12:07:30'),
(7, 3, 2, 'checked_out', '2026-03-27 12:07:46'),
(8, 3, 2, 'checked_out', '2026-03-27 12:08:01'),
(9, 3, 2, 'checked_out', '2026-03-27 12:08:16'),
(10, 3, 2, 'checked_out', '2026-03-27 12:08:31'),
(11, 3, 2, 'checked_out', '2026-03-27 12:08:46'),
(12, 9, 3, 'checked_out', '2026-03-27 16:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) NOT NULL,
  `cart_id` bigint(20) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `item_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 10, 1, 1000000.00, '2026-03-27 00:00:00'),
(3, 3, 10, 5, 1000000.00, '2026-03-27 00:00:00'),
(6, 6, 30, 1, 46171312.00, '2026-03-25 00:00:00'),
(7, 7, 31, 1, 17395000.00, '2026-03-25 00:00:00'),
(8, 8, 32, 1, 9980000.00, '2026-03-25 00:00:00'),
(9, 9, 33, 1, 5266000.00, '2026-03-25 00:00:00'),
(10, 10, 34, 1, 15591560.00, '2026-03-25 00:00:00'),
(11, 11, 35, 1, 41161137.00, '2026-03-25 00:00:00'),
(12, 12, 10, 20, 550000.00, '2026-03-27 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `icon`, `status`) VALUES
(1, 'FB Product', NULL, 'active'),
(2, 'FB Services', NULL, 'active'),
(3, 'House Keeping', NULL, 'active'),
(4, 'Front Office', NULL, 'active'),
(5, 'Accounting', NULL, 'active'),
(6, 'Human Resources', NULL, 'active'),
(7, 'Sales & Marketing', NULL, 'active'),
(8, 'Engineering & IT', NULL, 'active'),
(9, 'POMEC', NULL, 'active');

-- --------------------------------------------------------

--
-- Table structure for table `chats`
--

CREATE TABLE `chats` (
  `id` bigint(20) NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `branch_id` int(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chats`
--

INSERT INTO `chats` (`id`, `user_id`, `branch_id`, `created_at`) VALUES
(1, 1, 3, '2026-03-10 01:26:30'),
(2, 2, 3, '2026-03-15 16:19:55'),
(3, 3, 2, '2026-03-15 16:22:28'),
(4, 9, 3, '2026-03-27 17:53:08');

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint(20) NOT NULL,
  `chat_id` bigint(20) DEFAULT NULL,
  `sender_type` enum('user','admin') DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat_messages`
--

INSERT INTO `chat_messages` (`id`, `chat_id`, `sender_type`, `message`, `created_at`) VALUES
(1, 1, 'user', 'Hello', '2026-03-10 01:26:45'),
(2, 1, 'user', 'Hi', '2026-03-15 15:31:14'),
(3, 1, 'admin', 'Halo', '2026-03-15 15:35:36'),
(4, 1, 'admin', 'Hi', '2026-03-15 15:36:50'),
(5, 1, 'admin', 'Oke', '2026-03-15 15:37:32'),
(6, 2, 'admin', 'Halo', '2026-03-15 16:21:27'),
(7, 3, 'admin', 'Halo', '2026-03-15 16:22:32'),
(8, 3, 'admin', 'Tes pesan', '2026-03-15 16:39:09'),
(9, 4, 'admin', 'Hi', '2026-03-27 17:53:11');

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
(6, 1, '3100', 'Modal', 'equity', 11, 'financing', 1, NULL, 0, NULL, NULL, NULL, NULL),
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
(29, 1, '5202', 'Beban Listrik', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 1, '5203', 'Beban Sewa', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 1, '5204', 'Beban Internet', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 1, '5205', 'Beban Transportasi', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 1, '1501', 'Aset Tetap', 'asset', 14, 'investing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 1, '1502', 'Akumulasi Penyusutan', 'asset', 14, 'investing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 1, '2301', 'Utang Bank', 'liability', 15, 'financing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 1, '1500', 'Kelompok Aset Tetap', 'asset', 14, 'investing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 1, '2401', 'Utang PB1', 'liability', 15, 'financing', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 1, '5206', 'Beban Perlengkapan & Peralatan', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 1, '5207', 'Beban Air', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 1, '5208', 'Beban Gas', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 1, '4103', 'Pendapatan Kamar', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 1, '4104', 'Pendapatan Service', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 1, '4105', 'Pendapatan Food', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 1, '4106', 'Pendapatan Beverage', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 1, '4107', 'Pendapatan Shisha', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 1, '4108', 'Pendapatan Catering', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 1, '4109', 'Pendapatan Paket', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 1, '4110', 'Pendapatan Service Charge', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 1, '4111', 'Pendapatan Elqahua', 'revenue', 17, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 1, '6102', 'HPP Food', 'cogs', 19, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 1, '6103', 'HPP Beverage', 'cogs', 19, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 1, '6104', 'HPP Shisha', 'cogs', 19, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 1, '5209', 'Beban Kitchen', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 1, '5210', 'Beban Bar', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 1, '5211', 'Beban Shisha', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 1, '5212', 'Beban Catering', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 1, '5213', 'Beban Buffet', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 1, '6105', 'HPP Catering', 'cogs', 19, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 1, '6106', 'HPP Buffet', 'cogs', 19, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 1, '5214', 'Beban Fuel & Gas', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 1, '5216', 'Beban Photoshoot', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 1, '5217', 'Beban Pest Control', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 1, '5218', 'Beban Marketing', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 1, '5219', 'Beban Branding', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 1, '5220', 'Beban Maintenance', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 1, '5221', 'Beban Payroll', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 1, '5222', 'Beban HRD', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 1, '5223', 'Beban POMEC', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 1, '5224', 'Beban Lain-lain', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 1, '5225', 'Beban Cleaning Supply', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 1, '5226', 'Beban Entertainment', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 1, '5227', 'Beban Floor Operational', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 1, '5228', 'Beban Reimburse Housebank', 'expense', 18, 'operating', 1, NULL, NULL, NULL, NULL, NULL, NULL);

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
(1, 1, 1, 150000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(2, 1, 20, 250000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(3, 1, 3, 80000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(4, 1, 22, 20000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(5, 1, 33, 500000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(6, 1, 4, 100000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(7, 1, 5, 50000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(8, 1, 35, 200000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3),
(9, 1, 6, 650000000.00, 2026, '2026-02-21 17:52:35', 3, '2026-03-26 15:54:07', 3);

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
(1, 'COMP01', 'PT. Salam Mandiri Berkarya', 'Bogor Tengah, Kota Bogor', 'salamdjourney.com', NULL, 1, '2026-02-08 21:02:02', 0, NULL, NULL, NULL, NULL),
(3, 'COMP02', 'JHL', 'JHL', NULL, NULL, 1, '2026-03-27 18:00:42', 3, NULL, NULL, NULL, NULL),
(4, 'COMP03', 'BIGLAND', 'BIGLAND', NULL, NULL, 1, '2026-03-27 18:01:08', 3, NULL, NULL, NULL, NULL),
(5, 'COMP04', 'SAVERO', 'SAVERO', NULL, NULL, 1, '2026-03-27 18:01:26', 3, NULL, NULL, NULL, NULL),
(6, 'COMP05', 'WISPRIME', 'WISPRIME', NULL, NULL, 1, '2026-03-27 18:01:51', 3, NULL, NULL, NULL, NULL);

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
-- Table structure for table `form_pengajuan`
--

CREATE TABLE `form_pengajuan` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `divisi` varchar(100) NOT NULL,
  `jabatan` varchar(50) NOT NULL,
  `tanggal` varchar(10) NOT NULL,
  `status` enum('Pengajuan','Proses','Selesai') NOT NULL DEFAULT 'Pengajuan',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_pengajuan`
--

INSERT INTO `form_pengajuan` (`id`, `nama`, `divisi`, `jabatan`, `tanggal`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Muhammad', 'Supply', 'Admin', '27-03-2026', 'Pengajuan', '2026-03-27 12:44:46', '2026-03-27 12:44:46');

-- --------------------------------------------------------

--
-- Table structure for table `form_pengajuan_detail`
--

CREATE TABLE `form_pengajuan_detail` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `vendor_item_id` int(11) DEFAULT NULL,
  `sparepart` varchar(500) NOT NULL,
  `kondisi` varchar(20) NOT NULL,
  `qty` int(3) NOT NULL,
  `harga` double NOT NULL,
  `no_po` varchar(50) DEFAULT NULL,
  `is_bon` int(11) DEFAULT NULL,
  `is_delete` int(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_pengajuan_detail`
--

INSERT INTO `form_pengajuan_detail` (`id`, `pengajuan_id`, `vendor_item_id`, `sparepart`, `kondisi`, `qty`, `harga`, `no_po`, `is_bon`, `is_delete`, `created_at`) VALUES
(1, 1, 1, 'Daun Bawang', '', 100, 400000, 'SS/0326-0001', 0, 0, '2026-03-27 12:46:03');

-- --------------------------------------------------------

--
-- Table structure for table `form_purchasing`
--

CREATE TABLE `form_purchasing` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `nama_po` varchar(255) NOT NULL,
  `divisi_po` varchar(100) NOT NULL,
  `jabatan_po` varchar(50) NOT NULL,
  `tanggal_po` varchar(10) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_purchasing`
--

INSERT INTO `form_purchasing` (`id`, `pengajuan_id`, `nama_po`, `divisi_po`, `jabatan_po`, `tanggal_po`, `created_at`, `updated_at`) VALUES
(1, 1, 'Muhammad', 'Supply', 'Admin', '27-03-2026', '2026-03-27 12:47:02', '2026-03-27 12:47:02');

-- --------------------------------------------------------

--
-- Table structure for table `inventori`
--

CREATE TABLE `inventori` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `vendor_item_id` int(11) NOT NULL,
  `sparepart` varchar(500) NOT NULL,
  `kondisi` varchar(20) NOT NULL,
  `qty` int(3) NOT NULL,
  `is_used` int(11) NOT NULL,
  `is_delete` int(1) NOT NULL,
  `form_purchasing_id` int(11) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventori`
--

INSERT INTO `inventori` (`id`, `vendor_id`, `vendor_item_id`, `sparepart`, `kondisi`, `qty`, `is_used`, `is_delete`, `form_purchasing_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Daun Bawang', 'Baru', 900, 100, 0, 1, '2026-03-27 12:52:31', '2026-03-27 12:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `inventori_items`
--

CREATE TABLE `inventori_items` (
  `id` int(11) NOT NULL,
  `inventori_id` int(11) NOT NULL,
  `kondisi` varchar(20) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `no_pintu` varchar(5) NOT NULL,
  `qty` int(3) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) NOT NULL,
  `category_id` bigint(20) DEFAULT NULL,
  `name` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `status` enum('available','unavailable') DEFAULT 'available',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `category_id`, `name`, `description`, `image`, `price`, `status`, `created_at`) VALUES
(10, 4, 'FIT', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(11, 4, 'Hotel Package', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(12, 4, 'OTA', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(14, 4, 'Website', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(15, 4, 'MICE', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(16, 4, 'Wedding', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(17, 4, 'Extra Bed', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(18, 4, 'Other Room Revenue', NULL, NULL, 0.00, 'available', '2026-03-24 16:15:03'),
(19, 2, 'MICE', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(20, 2, 'Wedding', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(21, 2, 'Social Event', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(22, 2, 'Rahisa Resto', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(23, 2, 'Room Service', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(24, 2, 'Banquet', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(25, 2, 'Meeting Room Rental', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(26, 2, 'Other', NULL, NULL, 0.00, 'available', '2026-03-24 16:16:50'),
(27, 3, 'Laundry/Other', NULL, NULL, 0.00, 'available', '2026-03-24 16:17:44'),
(28, 3, 'Business Center Rev.', NULL, NULL, 0.00, 'available', '2026-03-24 16:17:44'),
(29, 3, 'Miscellaneous', NULL, NULL, 0.00, 'available', '2026-03-24 16:17:44'),
(30, 1, 'Food', NULL, NULL, NULL, 'available', '2026-03-27 06:45:58'),
(31, 1, 'Beverage', NULL, NULL, NULL, 'available', '2026-03-27 06:45:58'),
(32, 1, 'Shisha', NULL, NULL, NULL, 'available', '2026-03-27 06:45:58'),
(33, 1, 'Elqahua', NULL, NULL, NULL, 'available', '2026-03-27 06:45:58'),
(34, 2, 'Catering', NULL, NULL, NULL, 'available', '2026-03-27 06:45:58'),
(35, 2, 'Paket', NULL, NULL, NULL, 'available', '2026-03-27 06:45:58');

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
(1, 1, 1, 18225000.00, 0.00, '2026-03-25 16:03:02'),
(2, 1, 8, 0.00, 18225000.00, '2026-03-25 16:03:02'),
(3, 2, 1, 7388217.00, 0.00, '2026-03-25 16:03:27'),
(4, 2, 8, 0.00, 7388217.00, '2026-03-25 16:03:27'),
(5, 3, 1, 16062000.00, 0.00, '2026-03-25 16:04:34'),
(6, 3, 8, 0.00, 16062000.00, '2026-03-25 16:04:34'),
(7, 4, 1, 3500000.00, 0.00, '2026-03-25 16:05:44'),
(8, 4, 8, 0.00, 3500000.00, '2026-03-25 16:05:44'),
(9, 5, 1, 2800000.00, 0.00, '2026-03-25 16:06:19'),
(10, 5, 8, 0.00, 2800000.00, '2026-03-25 16:06:19'),
(11, 6, 1, 2420000.00, 0.00, '2026-03-25 16:07:14'),
(12, 6, 8, 0.00, 2420000.00, '2026-03-25 16:07:14'),
(13, 7, 1, 2760000.00, 0.00, '2026-03-25 16:07:28'),
(14, 7, 8, 0.00, 2760000.00, '2026-03-25 16:07:28'),
(15, 8, 1, 16560000.00, 0.00, '2026-03-25 16:07:45'),
(16, 8, 8, 0.00, 16560000.00, '2026-03-25 16:07:45'),
(17, 9, 1, 8520000.00, 0.00, '2026-03-25 16:08:00'),
(18, 9, 8, 0.00, 8520000.00, '2026-03-25 16:08:00'),
(19, 10, 1, 12600000.00, 0.00, '2026-03-25 16:08:40'),
(20, 10, 8, 0.00, 12600000.00, '2026-03-25 16:08:40'),
(21, 11, 1, 15480000.00, 0.00, '2026-03-25 16:08:56'),
(22, 11, 8, 0.00, 15480000.00, '2026-03-25 16:08:56'),
(23, 12, 1, 15760000.00, 0.00, '2026-03-25 16:09:18'),
(24, 12, 8, 0.00, 15760000.00, '2026-03-25 16:09:18'),
(25, 13, 1, 14950000.00, 0.00, '2026-03-25 16:09:38'),
(26, 13, 8, 0.00, 14950000.00, '2026-03-25 16:09:38'),
(27, 14, 1, 14070000.00, 0.00, '2026-03-25 16:10:09'),
(28, 14, 8, 0.00, 14070000.00, '2026-03-25 16:10:09'),
(29, 15, 1, 7980000.00, 0.00, '2026-03-25 16:10:31'),
(30, 15, 8, 0.00, 7980000.00, '2026-03-25 16:10:31'),
(31, 16, 1, 3000000.00, 0.00, '2026-03-25 16:10:45'),
(32, 16, 8, 0.00, 3000000.00, '2026-03-25 16:10:45'),
(33, 17, 1, 2850000.00, 0.00, '2026-03-25 16:11:01'),
(34, 17, 8, 0.00, 2850000.00, '2026-03-25 16:11:01'),
(35, 18, 1, 12540000.00, 0.00, '2026-03-25 16:11:18'),
(36, 18, 8, 0.00, 12540000.00, '2026-03-25 16:11:18'),
(37, 19, 1, 16230000.00, 0.00, '2026-03-25 16:11:34'),
(38, 19, 8, 0.00, 16230000.00, '2026-03-25 16:11:34'),
(39, 20, 1, 8850000.00, 0.00, '2026-03-25 16:11:49'),
(40, 20, 8, 0.00, 8850000.00, '2026-03-25 16:11:49'),
(41, 21, 1, 6450000.00, 0.00, '2026-03-25 16:12:05'),
(42, 21, 8, 0.00, 6450000.00, '2026-03-25 16:12:05'),
(43, 22, 1, 4200000.00, 0.00, '2026-03-25 16:12:23'),
(44, 22, 8, 0.00, 4200000.00, '2026-03-25 16:12:23'),
(45, 23, 1, 7500000.00, 0.00, '2026-03-25 16:12:41'),
(46, 23, 8, 0.00, 7500000.00, '2026-03-25 16:12:41'),
(47, 24, 1, 750000.00, 0.00, '2026-03-25 16:12:57'),
(48, 24, 8, 0.00, 750000.00, '2026-03-25 16:12:57'),
(49, 25, 1, 9750000.00, 0.00, '2026-03-25 16:14:02'),
(50, 25, 8, 0.00, 9750000.00, '2026-03-25 16:14:02'),
(51, 26, 1, 6750000.00, 0.00, '2026-03-25 16:14:48'),
(52, 26, 8, 0.00, 6750000.00, '2026-03-25 16:14:48'),
(53, 27, 1, 3620000.00, 0.00, '2026-03-25 16:15:44'),
(54, 27, 8, 0.00, 3620000.00, '2026-03-25 16:15:44'),
(55, 28, 1, 550000.00, 0.00, '2026-03-25 16:16:11'),
(56, 28, 8, 0.00, 550000.00, '2026-03-25 16:16:11'),
(61, 31, 1, 4211061.00, 0.00, '2026-03-25 16:19:06'),
(62, 31, 8, 0.00, 4211061.00, '2026-03-25 16:19:06'),
(63, 32, 1, 1428444.00, 0.00, '2026-03-25 16:19:37'),
(64, 32, 8, 0.00, 1428444.00, '2026-03-25 16:19:37'),
(65, 33, 1, 3200000.00, 0.00, '2026-03-25 16:20:13'),
(66, 33, 8, 0.00, 3200000.00, '2026-03-25 16:20:13'),
(67, 34, 1, 2295000.00, 0.00, '2026-03-25 16:20:36'),
(68, 34, 8, 0.00, 2295000.00, '2026-03-25 16:20:36'),
(69, 35, 1, 768976.00, 0.00, '2026-03-25 16:21:18'),
(70, 35, 8, 0.00, 768976.00, '2026-03-25 16:21:18'),
(71, 36, 1, 779152.00, 0.00, '2026-03-25 16:21:40'),
(72, 36, 8, 0.00, 779152.00, '2026-03-25 16:21:40'),
(73, 37, 1, 6100000.00, 0.00, '2026-03-25 16:23:41'),
(74, 37, 8, 0.00, 6100000.00, '2026-03-25 16:23:41'),
(75, 38, 1, 950000.00, 0.00, '2026-03-25 16:24:03'),
(76, 38, 8, 0.00, 950000.00, '2026-03-25 16:24:03'),
(77, 39, 1, 2190000.00, 0.00, '2026-03-25 16:24:25'),
(78, 39, 8, 0.00, 2190000.00, '2026-03-25 16:24:25'),
(79, 40, 1, 4095000.00, 0.00, '2026-03-25 16:24:37'),
(80, 40, 8, 0.00, 4095000.00, '2026-03-25 16:24:37'),
(81, 41, 1, 3112523.00, 0.00, '2026-03-25 16:26:00'),
(82, 41, 8, 0.00, 3112523.00, '2026-03-25 16:26:00'),
(83, 42, 1, 476148.00, 0.00, '2026-03-25 16:26:31'),
(84, 42, 8, 0.00, 476148.00, '2026-03-25 16:26:31'),
(85, 43, 1, 891358.00, 0.00, '2026-03-25 16:26:48'),
(86, 43, 8, 0.00, 891358.00, '2026-03-25 16:26:48'),
(87, 44, 1, 1700000.00, 0.00, '2026-03-25 16:27:22'),
(88, 44, 8, 0.00, 1700000.00, '2026-03-25 16:27:22'),
(89, 45, 1, 1700000.00, 0.00, '2026-03-25 16:27:41'),
(90, 45, 8, 0.00, 1700000.00, '2026-03-25 16:27:41'),
(91, 46, 1, 1200000.00, 0.00, '2026-03-25 16:27:54'),
(92, 46, 8, 0.00, 1200000.00, '2026-03-25 16:27:54'),
(93, 47, 1, 4766433.00, 0.00, '2026-03-25 16:28:26'),
(94, 47, 8, 0.00, 4766433.00, '2026-03-25 16:28:26'),
(95, 48, 1, 650000.00, 0.00, '2026-03-25 16:28:48'),
(96, 48, 8, 0.00, 650000.00, '2026-03-25 16:28:48'),
(97, 49, 1, 850000.00, 0.00, '2026-03-25 16:29:16'),
(98, 49, 8, 0.00, 850000.00, '2026-03-25 16:29:16'),
(99, 50, 1, 3075904.00, 0.00, '2026-03-25 16:30:27'),
(100, 50, 8, 0.00, 3075904.00, '2026-03-25 16:30:27'),
(101, 51, 1, 53260000.00, 0.00, '2026-03-25 16:31:35'),
(102, 51, 8, 0.00, 53260000.00, '2026-03-25 16:31:35'),
(105, 54, 10, 46960396.00, 0.00, '2026-03-25 17:54:50'),
(106, 54, 1, 0.00, 46960396.00, '2026-03-25 17:54:50'),
(107, 55, 10, 3876900.00, 0.00, '2026-03-25 17:55:09'),
(108, 55, 1, 0.00, 3876900.00, '2026-03-25 17:55:09'),
(109, 56, 10, 13239500.00, 0.00, '2026-03-25 17:55:32'),
(110, 56, 1, 0.00, 13239500.00, '2026-03-25 17:55:32'),
(111, 57, 10, 34317872.00, 0.00, '2026-03-25 17:55:55'),
(112, 57, 1, 0.00, 34317872.00, '2026-03-25 17:55:55'),
(113, 58, 10, 8810000.00, 0.00, '2026-03-25 17:56:12'),
(114, 58, 1, 0.00, 8810000.00, '2026-03-25 17:56:12'),
(115, 59, 10, 1321504.00, 0.00, '2026-03-25 17:56:54'),
(116, 59, 1, 0.00, 1321504.00, '2026-03-25 17:56:54'),
(117, 60, 10, 2838435.00, 0.00, '2026-03-25 17:57:11'),
(118, 60, 1, 0.00, 2838435.00, '2026-03-25 17:57:11'),
(119, 61, 10, 1318609.00, 0.00, '2026-03-25 17:57:27'),
(120, 61, 1, 0.00, 1318609.00, '2026-03-25 17:57:27'),
(121, 62, 10, 734850.00, 0.00, '2026-03-25 17:57:44'),
(122, 62, 1, 0.00, 734850.00, '2026-03-25 17:57:44'),
(123, 63, 10, 2220421.00, 0.00, '2026-03-25 17:58:00'),
(124, 63, 1, 0.00, 2220421.00, '2026-03-25 17:58:00'),
(129, 65, 1, 600000.00, 0.00, '2026-03-27 02:27:40'),
(130, 65, 41, 0.00, 491400.49, '2026-03-27 02:27:40'),
(131, 65, 42, 0.00, 49140.05, '2026-03-27 02:27:40'),
(132, 65, 37, 0.00, 59459.46, '2026-03-27 02:27:40'),
(156, 73, 1, 500000.00, 0.00, '2026-03-27 05:17:07'),
(157, 73, 2, 500000.00, 0.00, '2026-03-27 05:17:07'),
(158, 73, 41, 0.00, 819000.82, '2026-03-27 05:17:07'),
(159, 73, 42, 0.00, 81900.08, '2026-03-27 05:17:07'),
(160, 73, 37, 0.00, 99099.10, '2026-03-27 05:17:07'),
(161, 74, 1, 500000.00, 0.00, '2026-03-27 05:17:51'),
(162, 74, 2, 0.00, 500000.00, '2026-03-27 05:17:51'),
(163, 75, 1, 2500000.00, 0.00, '2026-03-27 05:46:57'),
(164, 75, 2, 2500000.00, 0.00, '2026-03-27 05:46:57'),
(165, 75, 41, 0.00, 4095004.10, '2026-03-27 05:46:57'),
(166, 75, 42, 0.00, 409500.41, '2026-03-27 05:46:57'),
(167, 75, 37, 0.00, 495495.49, '2026-03-27 05:46:57'),
(168, 76, 1, 2500000.00, 0.00, '2026-03-27 05:47:27'),
(169, 76, 2, 0.00, 2500000.00, '2026-03-27 05:47:27'),
(194, 85, 1, 46171312.00, 0.00, '2026-03-27 12:10:55'),
(195, 85, 50, 0.00, 37814342.34, '2026-03-27 12:10:55'),
(196, 85, 55, 0.00, 3781434.23, '2026-03-27 12:10:55'),
(197, 85, 37, 0.00, 4575535.43, '2026-03-27 12:10:55'),
(198, 86, 1, 17395000.00, 0.00, '2026-03-27 12:11:03'),
(199, 86, 50, 0.00, 14246519.25, '2026-03-27 12:11:03'),
(200, 86, 55, 0.00, 1424651.92, '2026-03-27 12:11:03'),
(201, 86, 37, 0.00, 1723828.83, '2026-03-27 12:11:03'),
(202, 87, 1, 9980000.00, 0.00, '2026-03-27 12:11:15'),
(203, 87, 50, 0.00, 8173628.17, '2026-03-27 12:11:15'),
(204, 87, 55, 0.00, 817362.82, '2026-03-27 12:11:15'),
(205, 87, 37, 0.00, 989009.01, '2026-03-27 12:11:15'),
(206, 88, 1, 5266000.00, 0.00, '2026-03-27 12:11:22'),
(207, 88, 50, 0.00, 4312858.31, '2026-03-27 12:11:22'),
(208, 88, 55, 0.00, 431285.83, '2026-03-27 12:11:22'),
(209, 88, 37, 0.00, 521855.86, '2026-03-27 12:11:22'),
(210, 89, 1, 15591560.00, 0.00, '2026-03-27 12:11:30'),
(211, 89, 51, 0.00, 12769500.41, '2026-03-27 12:11:30'),
(212, 89, 55, 0.00, 1276950.04, '2026-03-27 12:11:30'),
(213, 89, 37, 0.00, 1545109.55, '2026-03-27 12:11:30'),
(214, 90, 1, 41161137.00, 0.00, '2026-03-27 12:11:39'),
(215, 90, 51, 0.00, 33711004.91, '2026-03-27 12:11:39'),
(216, 90, 55, 0.00, 3371100.49, '2026-03-27 12:11:39'),
(217, 90, 37, 0.00, 4079031.60, '2026-03-27 12:11:39'),
(218, 91, 57, 15876500.00, 0.00, '2026-03-27 12:23:51'),
(219, 91, 1, 0.00, 15876500.00, '2026-03-27 12:23:51'),
(220, 92, 58, 2300600.00, 0.00, '2026-03-27 12:24:04'),
(221, 92, 1, 0.00, 2300600.00, '2026-03-27 12:24:04'),
(222, 93, 65, 5540900.00, 0.00, '2026-03-27 12:24:17'),
(223, 93, 1, 0.00, 5540900.00, '2026-03-27 12:24:17'),
(224, 94, 66, 7650700.00, 0.00, '2026-03-27 12:24:29'),
(225, 94, 1, 0.00, 7650700.00, '2026-03-27 12:24:29'),
(226, 95, 78, 500000.00, 0.00, '2026-03-27 12:30:43'),
(227, 95, 1, 0.00, 500000.00, '2026-03-27 12:30:43'),
(228, 96, 67, 2358750.00, 0.00, '2026-03-27 12:30:59'),
(229, 96, 1, 0.00, 2358750.00, '2026-03-27 12:30:59'),
(230, 97, 4, 6997000.00, 0.00, '2026-03-27 12:34:29'),
(231, 97, 1, 0.00, 6997000.00, '2026-03-27 12:34:29'),
(232, 98, 74, 38845833.00, 0.00, '2026-03-27 12:34:55'),
(233, 98, 1, 0.00, 38845833.00, '2026-03-27 12:34:55'),
(234, 99, 76, 10914012.00, 0.00, '2026-03-27 12:35:11'),
(235, 99, 1, 0.00, 10914012.00, '2026-03-27 12:35:11'),
(236, 100, 1, 11000000.00, 0.00, '2026-03-27 16:12:34'),
(237, 100, 53, 0.00, 9009009.01, '2026-03-27 16:12:34'),
(238, 100, 55, 0.00, 900900.90, '2026-03-27 16:12:34'),
(239, 100, 37, 0.00, 1090090.09, '2026-03-27 16:12:34'),
(240, 101, 1, 20000000.00, 0.00, '2026-03-27 17:05:05'),
(241, 101, 2, 0.00, 20000000.00, '2026-03-27 17:05:05');

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
(1, 1, 3, 2, 'AUTO-1', '2026-03-19', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:03:02', '0000-00-00 00:00:00'),
(2, 1, 3, 2, 'AUTO-2', '2026-03-19', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:03:27', '0000-00-00 00:00:00'),
(3, 1, 3, 2, 'AUTO-3', '2026-03-19', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:04:34', '0000-00-00 00:00:00'),
(4, 1, 3, 2, 'AUTO-4', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:05:44', '0000-00-00 00:00:00'),
(5, 1, 3, 2, 'AUTO-5', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:06:19', '0000-00-00 00:00:00'),
(6, 1, 3, 2, 'AUTO-6', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:07:14', '0000-00-00 00:00:00'),
(7, 1, 3, 2, 'AUTO-7', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:07:28', '0000-00-00 00:00:00'),
(8, 1, 3, 2, 'AUTO-8', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:07:45', '0000-00-00 00:00:00'),
(9, 1, 3, 2, 'AUTO-9', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:08:00', '0000-00-00 00:00:00'),
(10, 1, 3, 2, 'AUTO-10', '2026-03-05', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:08:40', '0000-00-00 00:00:00'),
(11, 1, 3, 2, 'AUTO-11', '2026-03-06', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:08:56', '0000-00-00 00:00:00'),
(12, 1, 3, 2, 'AUTO-12', '2026-03-07', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:09:18', '0000-00-00 00:00:00'),
(13, 1, 3, 2, 'AUTO-13', '2026-03-08', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:09:38', '0000-00-00 00:00:00'),
(14, 1, 3, 2, 'AUTO-14', '2026-03-09', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:10:09', '0000-00-00 00:00:00'),
(15, 1, 3, 2, 'AUTO-15', '2026-03-10', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:10:31', '0000-00-00 00:00:00'),
(16, 1, 3, 2, 'AUTO-16', '2026-03-11', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:10:45', '0000-00-00 00:00:00'),
(17, 1, 3, 2, 'AUTO-17', '2026-03-12', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:11:01', '0000-00-00 00:00:00'),
(18, 1, 3, 2, 'AUTO-18', '2026-03-13', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:11:18', '0000-00-00 00:00:00'),
(19, 1, 3, 2, 'AUTO-19', '2026-03-14', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:11:34', '0000-00-00 00:00:00'),
(20, 1, 3, 2, 'AUTO-20', '2026-03-15', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:11:49', '0000-00-00 00:00:00'),
(21, 1, 3, 2, 'AUTO-21', '2026-03-16', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:12:05', '0000-00-00 00:00:00'),
(22, 1, 3, 2, 'AUTO-22', '2026-03-17', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:12:23', '0000-00-00 00:00:00'),
(23, 1, 3, 2, 'AUTO-23', '2026-03-18', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:12:41', '0000-00-00 00:00:00'),
(24, 1, 3, 2, 'AUTO-24', '2026-03-19', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:12:57', '0000-00-00 00:00:00'),
(25, 1, 3, 2, 'AUTO-25', '2026-03-24', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:14:02', '0000-00-00 00:00:00'),
(26, 1, 3, 2, 'AUTO-26', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:14:48', '0000-00-00 00:00:00'),
(27, 1, 3, 2, 'AUTO-27', '2026-03-15', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:15:44', '0000-00-00 00:00:00'),
(28, 1, 3, 2, 'AUTO-28', '2026-03-20', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:16:11', '0000-00-00 00:00:00'),
(31, 1, 3, 2, 'AUTO-31', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:19:06', '0000-00-00 00:00:00'),
(32, 1, 3, 2, 'AUTO-32', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:19:37', '0000-00-00 00:00:00'),
(33, 1, 3, 2, 'AUTO-33', '2026-03-21', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:20:13', '0000-00-00 00:00:00'),
(34, 1, 3, 2, 'AUTO-34', '2026-03-21', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:20:36', '0000-00-00 00:00:00'),
(35, 1, 3, 2, 'AUTO-35', '2026-03-21', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:21:18', '0000-00-00 00:00:00'),
(36, 1, 3, 2, 'AUTO-36', '2026-03-21', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:21:40', '0000-00-00 00:00:00'),
(37, 1, 3, 2, 'AUTO-37', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:23:41', '0000-00-00 00:00:00'),
(38, 1, 3, 2, 'AUTO-38', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:24:03', '0000-00-00 00:00:00'),
(39, 1, 3, 2, 'AUTO-39', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:24:25', '0000-00-00 00:00:00'),
(40, 1, 3, 2, 'AUTO-40', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:24:37', '0000-00-00 00:00:00'),
(41, 1, 3, 2, 'AUTO-41', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:26:00', '0000-00-00 00:00:00'),
(42, 1, 3, 2, 'AUTO-42', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:26:31', '0000-00-00 00:00:00'),
(43, 1, 3, 2, 'AUTO-43', '2026-03-22', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:26:48', '0000-00-00 00:00:00'),
(44, 1, 3, 2, 'AUTO-44', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:27:22', '0000-00-00 00:00:00'),
(45, 1, 3, 2, 'AUTO-45', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:27:41', '0000-00-00 00:00:00'),
(46, 1, 3, 2, 'AUTO-46', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:27:54', '0000-00-00 00:00:00'),
(47, 1, 3, 2, 'AUTO-47', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:28:26', '0000-00-00 00:00:00'),
(48, 1, 3, 2, 'AUTO-48', '2026-03-23', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:28:48', '0000-00-00 00:00:00'),
(49, 1, 3, 2, 'AUTO-49', '2026-03-24', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:29:16', '0000-00-00 00:00:00'),
(50, 1, 3, 2, 'AUTO-50', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:30:27', '0000-00-00 00:00:00'),
(51, 1, 3, 2, 'AUTO-51', '2026-03-31', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 16:31:35', '0000-00-00 00:00:00'),
(54, 1, 3, 2, 'AUTO-53', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:54:50', '0000-00-00 00:00:00'),
(55, 1, 3, 2, 'AUTO-54', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:55:09', '0000-00-00 00:00:00'),
(56, 1, 3, 2, 'AUTO-55', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:55:32', '0000-00-00 00:00:00'),
(57, 1, 3, 2, 'AUTO-56', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:55:55', '0000-00-00 00:00:00'),
(58, 1, 3, 2, 'AUTO-57', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:56:12', '0000-00-00 00:00:00'),
(59, 1, 3, 2, 'AUTO-58', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:56:54', '0000-00-00 00:00:00'),
(60, 1, 3, 2, 'AUTO-59', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:57:11', '0000-00-00 00:00:00'),
(61, 1, 3, 2, 'AUTO-60', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:57:27', '0000-00-00 00:00:00'),
(62, 1, 3, 2, 'AUTO-61', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:57:44', '0000-00-00 00:00:00'),
(63, 1, 3, 2, 'AUTO-62', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-25 17:58:00', '0000-00-00 00:00:00'),
(65, 1, 3, 2, 'AUTO-64', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 02:27:40', '0000-00-00 00:00:00'),
(73, 1, 3, 2, 'AUTO-72', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 05:17:07', '0000-00-00 00:00:00'),
(74, 1, 3, 2, 'AUTO-73', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 05:17:51', '0000-00-00 00:00:00'),
(75, 1, 3, 2, 'AUTO-74', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 05:46:57', '0000-00-00 00:00:00'),
(76, 1, 3, 2, 'AUTO-75', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 05:47:27', '0000-00-00 00:00:00'),
(85, 1, 2, 2, 'AUTO-84', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:10:55', '0000-00-00 00:00:00'),
(86, 1, 2, 2, 'AUTO-85', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:11:03', '0000-00-00 00:00:00'),
(87, 1, 2, 2, 'AUTO-86', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:11:15', '0000-00-00 00:00:00'),
(88, 1, 2, 2, 'AUTO-87', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:11:22', '0000-00-00 00:00:00'),
(89, 1, 2, 2, 'AUTO-88', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:11:30', '0000-00-00 00:00:00'),
(90, 1, 2, 2, 'AUTO-89', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:11:39', '0000-00-00 00:00:00'),
(91, 1, 2, 2, 'AUTO-90', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:23:51', '0000-00-00 00:00:00'),
(92, 1, 2, 2, 'AUTO-91', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:24:04', '0000-00-00 00:00:00'),
(93, 1, 2, 2, 'AUTO-92', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:24:17', '0000-00-00 00:00:00'),
(94, 1, 2, 2, 'AUTO-93', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:24:29', '0000-00-00 00:00:00'),
(95, 1, 2, 2, 'AUTO-94', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:30:43', '0000-00-00 00:00:00'),
(96, 1, 2, 2, 'AUTO-95', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:30:59', '0000-00-00 00:00:00'),
(97, 1, 2, 2, 'AUTO-96', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:34:29', '0000-00-00 00:00:00'),
(98, 1, 2, 2, 'AUTO-97', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:34:55', '0000-00-00 00:00:00'),
(99, 1, 2, 2, 'AUTO-98', '2026-03-25', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 12:35:11', '0000-00-00 00:00:00'),
(100, 1, 3, 2, 'AUTO-99', '2026-03-27', NULL, 0.00, 3, 2026, 'posted', 0, NULL, NULL, '2026-03-27 16:12:34', '0000-00-00 00:00:00'),
(101, 1, 2, 2, 'AUTO-100', '2026-03-27', NULL, 0.00, 3, 2026, 'draft', 0, NULL, NULL, '2026-03-27 17:05:05', '0000-00-00 00:00:00');

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
-- Table structure for table `loyalty_tiers`
--

CREATE TABLE `loyalty_tiers` (
  `id` bigint(20) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `min_points` int(11) DEFAULT 0,
  `min_spending` decimal(12,2) DEFAULT 0.00,
  `cashback_percent` decimal(5,2) DEFAULT 0.00,
  `point_multiplier` decimal(5,2) DEFAULT 1.00,
  `free_drink_per_month` int(11) DEFAULT 0,
  `priority_support` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_tiers`
--

INSERT INTO `loyalty_tiers` (`id`, `name`, `min_points`, `min_spending`, `cashback_percent`, `point_multiplier`, `free_drink_per_month`, `priority_support`, `created_at`) VALUES
(3, 'Silver', 0, 0.00, 0.00, 1.00, 0, 0, '2026-03-26 12:20:36'),
(5, 'Gold', 0, 0.00, 0.00, 1.00, 0, 0, '2026-03-26 12:20:36');

-- --------------------------------------------------------

--
-- Table structure for table `maintenances`
--

CREATE TABLE `maintenances` (
  `id` int(11) NOT NULL,
  `tgl_order` varchar(10) NOT NULL,
  `tgl_selesai` varchar(10) NOT NULL,
  `jam_order` varchar(5) NOT NULL,
  `jam_selesai` varchar(5) NOT NULL,
  `type` varchar(100) NOT NULL,
  `requester` varchar(50) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `no_pintu` varchar(20) NOT NULL,
  `staff_gudang` varchar(50) NOT NULL,
  `security` varchar(50) NOT NULL,
  `driver_id` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenances`
--

INSERT INTO `maintenances` (`id`, `tgl_order`, `tgl_selesai`, `jam_order`, `jam_selesai`, `type`, `requester`, `vehicle_id`, `no_pintu`, `staff_gudang`, `security`, `driver_id`, `created_at`, `updated_at`) VALUES
(1, '27-03-2026', '27-03-2026', '08:00', '09:00', 'Pengiriman Sayuran', 'Muhammad', 102, '1', 'Gudang Default', 'Security Default', 3, '2026-03-27 12:50:13', '2026-03-27 12:57:26');

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_orders`
--

CREATE TABLE `maintenance_orders` (
  `id` int(11) NOT NULL,
  `maintenance_id` int(11) NOT NULL,
  `inventori_id` int(11) DEFAULT NULL,
  `permintaan_perbaikan` text NOT NULL,
  `sparepart` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `kondisi` varchar(20) NOT NULL,
  `posisi` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `no_seri` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `maintenance_orders`
--

INSERT INTO `maintenance_orders` (`id`, `maintenance_id`, `inventori_id`, `permintaan_perbaikan`, `sparepart`, `qty`, `kondisi`, `posisi`, `keterangan`, `no_seri`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'Sayuran', 'Daung Bawang', 100, 'Baru', '', 'DB01-0001', NULL, '2026-03-27 12:51:43', '2026-03-27 12:51:43');

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
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) NOT NULL,
  `order_number` varchar(50) DEFAULT NULL,
  `user_id` int(20) DEFAULT NULL,
  `branch_id` int(20) DEFAULT NULL,
  `cart_id` bigint(20) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  `discount` decimal(12,2) DEFAULT 0.00,
  `wallet_used` decimal(12,2) DEFAULT 0.00,
  `deposit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(12,2) DEFAULT 0.00,
  `status` enum('pending','paid','processing','ready','completed','cancelled') DEFAULT 'pending',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_number`, `user_id`, `branch_id`, `cart_id`, `subtotal`, `discount`, `wallet_used`, `deposit`, `total_amount`, `status`, `created_at`) VALUES
(1, 'ORD20260327032342E81330', 9, 3, 1, 1000000.00, 0.00, 0.00, 1000000.00, 1000000.00, 'paid', '2026-03-27 03:23:55'),
(3, 'ORD2026032705455755A360', 2, 3, 3, 5000000.00, 0.00, 0.00, 5000000.00, 5000000.00, 'paid', '2026-03-27 05:46:16'),
(6, 'ORD202603271206488106AA', 3, 2, 6, 46171312.00, 0.00, 0.00, 46171312.00, 46171312.00, 'paid', '2026-03-27 12:07:30'),
(7, 'ORD202603271207313DADAB', 3, 2, 7, 17395000.00, 0.00, 0.00, 17395000.00, 17395000.00, 'paid', '2026-03-27 12:07:46'),
(8, 'ORD2026032712074738E254', 3, 2, 8, 9980000.00, 0.00, 0.00, 9980000.00, 9980000.00, 'paid', '2026-03-27 12:08:01'),
(9, 'ORD20260327120802250176', 3, 2, 9, 5266000.00, 0.00, 0.00, 5266000.00, 5266000.00, 'paid', '2026-03-27 12:08:16'),
(10, 'ORD202603271208171BE415', 3, 2, 10, 15591560.00, 0.00, 0.00, 15591560.00, 15591560.00, 'paid', '2026-03-27 12:08:31'),
(11, 'ORD2026032712083206FB60', 3, 2, 11, 41161137.00, 0.00, 0.00, 41161137.00, 41161137.00, 'paid', '2026-03-27 12:08:46'),
(12, 'ORD20260327160946AEACC1', 9, 3, 12, 11000000.00, 0.00, 0.00, 1000000.00, 11000000.00, 'pending', '2026-03-27 16:11:45');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `price` decimal(12,2) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `item_id`, `quantity`, `price`, `created_at`) VALUES
(1, 1, 10, 1, 1000000.00, '2026-03-27 00:00:00'),
(3, 3, 10, 5, 1000000.00, '2026-03-27 00:00:00'),
(6, 6, 30, 1, 46171312.00, '2026-03-25 00:00:00'),
(7, 7, 31, 1, 17395000.00, '2026-03-25 00:00:00'),
(8, 8, 32, 1, 9980000.00, '2026-03-25 00:00:00'),
(9, 9, 33, 1, 5266000.00, '2026-03-25 00:00:00'),
(10, 10, 34, 1, 15591560.00, '2026-03-25 00:00:00'),
(11, 11, 35, 1, 41161137.00, '2026-03-25 00:00:00'),
(12, 12, 10, 20, 550000.00, '2026-03-27 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) NOT NULL,
  `order_id` bigint(20) DEFAULT NULL,
  `payment_method` enum('cash','qris','wallet','credit_card','debit_card') DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `status` enum('pending','paid','failed','processing') DEFAULT 'pending',
  `transaction_ref` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `order_id`, `payment_method`, `amount`, `status`, `transaction_ref`, `paid_at`) VALUES
(1, 1, 'cash', 1000000.00, 'paid', NULL, '2026-03-27 05:17:51'),
(3, 3, 'cash', 5000000.00, 'paid', NULL, '2026-03-27 05:47:27'),
(6, 6, 'cash', 46171312.00, 'paid', NULL, '2026-03-27 12:10:55'),
(7, 7, 'cash', 17395000.00, 'paid', NULL, '2026-03-27 12:11:03'),
(8, 8, 'cash', 9980000.00, 'paid', NULL, '2026-03-27 12:11:15'),
(9, 9, 'cash', 5266000.00, 'paid', NULL, '2026-03-27 12:11:22'),
(10, 10, 'cash', 15591560.00, 'paid', NULL, '2026-03-27 12:11:30'),
(11, 11, 'cash', 41161137.00, 'paid', NULL, '2026-03-27 17:05:05'),
(12, 12, 'cash', 11000000.00, 'pending', NULL, '2026-03-27 16:12:34');

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
-- Table structure for table `point_rules`
--

CREATE TABLE `point_rules` (
  `id` bigint(20) NOT NULL,
  `branch_id` int(1) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `spend_amount` int(11) NOT NULL,
  `point_amount` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `point_rules`
--

INSERT INTO `point_rules` (`id`, `branch_id`, `name`, `spend_amount`, `point_amount`, `status`, `created_at`) VALUES
(1, 2, 'Default Rule', 10000, 1, 'active', '2026-03-15 19:43:10');

-- --------------------------------------------------------

--
-- Table structure for table `point_transactions`
--

CREATE TABLE `point_transactions` (
  `id` bigint(20) NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `type` enum('earn','redeem') DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `point_transactions`
--

INSERT INTO `point_transactions` (`id`, `user_id`, `points`, `type`, `reference_type`, `reference_id`, `created_at`) VALUES
(1, 3, 1500, 'earn', 'order', 3, '2026-03-24 18:18:43'),
(2, 3, 1500, 'earn', 'order', 7, '2026-03-24 18:30:30'),
(3, 3, 2000, 'earn', 'order', 8, '2026-03-24 19:48:23'),
(4, 3, 500, 'earn', 'order', 11, '2026-03-24 19:58:58'),
(5, 3, 1400, 'earn', 'order', 12, '2026-03-24 20:22:58'),
(9, 3, 1500, 'earn', 'order', 13, '2026-03-24 20:26:45'),
(10, 3, 1000, 'earn', 'order', 1, '2026-03-25 15:04:18'),
(11, 3, 200, 'earn', 'order', 5, '2026-03-27 11:45:47'),
(12, 3, 4617, 'earn', 'order', 6, '2026-03-27 12:07:30'),
(13, 3, 1739, 'earn', 'order', 7, '2026-03-27 12:07:46'),
(14, 3, 998, 'earn', 'order', 8, '2026-03-27 12:08:01'),
(15, 3, 526, 'earn', 'order', 9, '2026-03-27 12:08:16'),
(16, 3, 1559, 'earn', 'order', 10, '2026-03-27 12:08:31'),
(17, 3, 4116, 'earn', 'order', 11, '2026-03-27 12:08:46');

-- --------------------------------------------------------

--
-- Table structure for table `ratio_spend`
--

CREATE TABLE `ratio_spend` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `department_category` varchar(255) DEFAULT NULL,
  `min_value` decimal(10,2) NOT NULL,
  `max_value` decimal(10,2) NOT NULL,
  `label` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratio_spend`
--

INSERT INTO `ratio_spend` (`id`, `hotel_id`, `department_category`, `min_value`, `max_value`, `label`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(12, 1, 'Front Office', 0.00, 1.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(15, 1, 'Housekeeping', 0.00, 5.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(18, 1, 'Food & Beverage Service', 0.00, 2.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(21, 1, 'Kitchen / Culinary', 0.00, 23.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(24, 1, 'Finance', 0.00, 2.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(27, 1, 'Human Resources', 0.00, 1.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(30, 1, 'Engineering', 0.00, 1.00, 'BAD', 1, 1, '2026-02-27 11:32:52', NULL),
(33, 1, 'Sales & Marketing', 0.00, 2.00, 'BAD', 1, 1, '2026-02-24 03:40:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `ratio_worker`
--

CREATE TABLE `ratio_worker` (
  `id` int(11) NOT NULL,
  `hotel_id` int(11) DEFAULT NULL,
  `department_category` varchar(255) DEFAULT NULL,
  `min_value` decimal(10,2) NOT NULL,
  `max_value` decimal(10,2) NOT NULL,
  `label` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratio_worker`
--

INSERT INTO `ratio_worker` (`id`, `hotel_id`, `department_category`, `min_value`, `max_value`, `label`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 0.00, 0.00, 'NO DATA', 1, 1, '2026-02-24 03:40:13', NULL),
(2, 1, NULL, 0.00, 1.00, 'GOOD', 1, 2, '2026-02-24 03:40:13', NULL),
(3, 1, NULL, 1.00, 4.00, 'AVERAGE', 1, 3, '2026-02-24 03:40:13', NULL),
(4, 1, NULL, 4.00, 6.00, 'OVER', 1, 4, '2026-02-24 03:40:13', NULL),
(5, 1, NULL, 6.00, 999.00, 'NOT OPTIMAL MAN POWER', 1, 5, '2026-02-24 03:40:13', NULL),
(12, 1, 'Front Office', 0.00, 2.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(13, 1, 'Front Office', 3.00, 4.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(14, 1, 'Front Office', 4.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(15, 1, 'Housekeeping', 0.00, 3.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(16, 1, 'Housekeeping', 3.00, 5.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(17, 1, 'Housekeeping', 5.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(18, 1, 'Food & Beverage Service', 0.00, 2.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(19, 1, 'Food & Beverage Service', 2.00, 4.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(20, 1, 'Food & Beverage Service', 4.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(21, 1, 'Kitchen / Culinary', 0.00, 3.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(22, 1, 'Kitchen / Culinary', 3.00, 6.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(23, 1, 'Kitchen / Culinary', 6.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(24, 1, 'Finance', 0.00, 1.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(25, 1, 'Finance', 1.00, 3.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(26, 1, 'Finance', 3.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(27, 1, 'Human Resources', 0.00, 1.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(28, 1, 'Human Resources', 1.00, 4.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(29, 1, 'Human Resources', 4.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(30, 1, 'Engineering', 0.00, 1.00, 'GOOD', 1, 1, '2026-02-27 11:32:52', NULL),
(31, 1, 'Engineering', 1.00, 3.00, 'AVERAGE', 1, 1, '2026-02-27 11:32:52', NULL),
(32, 1, 'Engineering', 3.00, 999.00, 'OVER', 1, 1, '2026-02-27 11:32:52', NULL),
(33, 1, 'Sales & Marketing', 0.00, 3.00, 'GOOD', 1, 1, '2026-02-24 03:40:13', NULL),
(34, 1, 'Sales & Marketing', 3.00, 6.00, 'AVERAGE', 1, 2, '2026-02-24 03:40:13', NULL),
(35, 1, 'Sales & Marketing', 6.00, 999.00, 'OVER', 1, 3, '2026-02-24 03:40:13', NULL);

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
  `tax_type` enum('ppn','withholding','pb1') DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `is_active` tinyint(4) DEFAULT 1,
  `deleted_at` datetime NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `tax_direction` enum('input','output','both') DEFAULT NULL,
  `coa_account_id` int(11) DEFAULT NULL,
  `is_included` tinyint(4) DEFAULT 0,
  `is_creditable` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tax_codes`
--

INSERT INTO `tax_codes` (`id`, `tax_code`, `tax_name`, `tax_type`, `tax_rate`, `is_active`, `deleted_at`, `company_id`, `tax_direction`, `coa_account_id`, `is_included`, `is_creditable`) VALUES
(1, 'PPN11', 'PPN 11%', 'ppn', 11.00, 1, '0000-00-00 00:00:00', 1, 'input', 22, 0, 1),
(2, 'PPh23', 'PPh 23', 'withholding', 2.00, 1, '0000-00-00 00:00:00', 1, NULL, 25, 0, 1),
(3, 'PPN11', 'PPN 11%', 'ppn', 11.00, 1, '0000-00-00 00:00:00', 1, 'output', 24, 0, 1),
(4, 'PPh21', 'PPh 21', 'withholding', 5.00, 1, '0000-00-00 00:00:00', 1, NULL, 25, 0, 0),
(5, 'PB1', 'PB1 11%', 'pb1', 11.00, 1, '0000-00-00 00:00:00', 1, 'input', 37, 0, 1),
(6, 'PB1', 'PB1 11%', 'pb1', 11.00, 1, '0000-00-00 00:00:00', 1, 'output', 37, 0, 1);

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
  `payment_account_id` int(11) NOT NULL,
  `status` enum('draft','submitted','approved','posted','rejected') DEFAULT 'draft',
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`id`, `company_id`, `branch_id`, `trx_date`, `trx_type`, `reference_no`, `amount`, `created_at`, `journal_id`, `debit_account_id`, `credit_account_id`, `payment_account_id`, `status`, `updated_at`) VALUES
(1, 1, 3, '2026-03-19', 'sales', 'FIT-ROOM-190326', 18225000.00, '2026-03-25 16:03:02', 1, NULL, NULL, 0, 'posted', '2026-03-25 16:03:02'),
(2, 1, 3, '2026-03-19', 'sales', 'OTA-ROOM-190326', 7388217.00, '2026-03-25 16:03:27', 2, NULL, NULL, 0, 'posted', '2026-03-25 16:03:27'),
(3, 1, 3, '2026-03-19', 'sales', 'FNB-190326', 16062000.00, '2026-03-25 16:04:34', 3, NULL, NULL, 0, 'posted', '2026-03-25 16:04:34'),
(4, 1, 3, '2026-03-11', 'sales', 'MICE-IPBDIR-110326', 3500000.00, '2026-03-25 16:05:44', 4, NULL, NULL, 0, 'posted', '2026-03-25 16:05:44'),
(5, 1, 3, '2026-03-16', 'sales', 'MICE-KEMENTAN-110326', 2800000.00, '2026-03-25 16:06:19', 5, NULL, NULL, 0, 'posted', '2026-03-25 16:06:19'),
(6, 1, 3, '2026-03-01', 'sales', 'IFTAR-010326', 2420000.00, '2026-03-25 16:07:14', 6, NULL, NULL, 0, 'posted', '2026-03-25 16:07:14'),
(7, 1, 3, '2026-03-02', 'sales', 'IFTAR-020326', 2760000.00, '2026-03-25 16:07:28', 7, NULL, NULL, 0, 'posted', '2026-03-25 16:07:28'),
(8, 1, 3, '2026-03-03', 'sales', 'IFTAR-030326', 16560000.00, '2026-03-25 16:07:45', 8, NULL, NULL, 0, 'posted', '2026-03-25 16:07:45'),
(9, 1, 3, '2026-03-04', 'sales', 'IFTAR-040326', 8520000.00, '2026-03-25 16:08:00', 9, NULL, NULL, 0, 'posted', '2026-03-25 16:08:00'),
(10, 1, 3, '2026-03-05', 'sales', 'IFTAR-050326', 12600000.00, '2026-03-25 16:08:40', 10, NULL, NULL, 0, 'posted', '2026-03-25 16:08:40'),
(11, 1, 3, '2026-03-06', 'sales', 'IFTAR-060326', 15480000.00, '2026-03-25 16:08:56', 11, NULL, NULL, 0, 'posted', '2026-03-25 16:08:56'),
(12, 1, 3, '2026-03-07', 'sales', 'IFTAR-070326', 15760000.00, '2026-03-25 16:09:18', 12, NULL, NULL, 0, 'posted', '2026-03-25 16:09:18'),
(13, 1, 3, '2026-03-08', 'sales', 'IFTAR-080326', 14950000.00, '2026-03-25 16:09:38', 13, NULL, NULL, 0, 'posted', '2026-03-25 16:09:38'),
(14, 1, 3, '2026-03-09', 'sales', 'IFTAR-090326', 14070000.00, '2026-03-25 16:10:09', 14, NULL, NULL, 0, 'posted', '2026-03-25 16:10:09'),
(15, 1, 3, '2026-03-10', 'sales', 'IFTAR-100326', 7980000.00, '2026-03-25 16:10:31', 15, NULL, NULL, 0, 'posted', '2026-03-25 16:10:31'),
(16, 1, 3, '2026-03-11', 'sales', 'IFTAR-110326', 3000000.00, '2026-03-25 16:10:44', 16, NULL, NULL, 0, 'posted', '2026-03-25 16:10:45'),
(17, 1, 3, '2026-03-12', 'sales', 'IFTAR-120326', 2850000.00, '2026-03-25 16:11:01', 17, NULL, NULL, 0, 'posted', '2026-03-25 16:11:01'),
(18, 1, 3, '2026-03-13', 'sales', 'IFTAR-130326', 12540000.00, '2026-03-25 16:11:18', 18, NULL, NULL, 0, 'posted', '2026-03-25 16:11:18'),
(19, 1, 3, '2026-03-14', 'sales', 'IFTAR-140326', 16230000.00, '2026-03-25 16:11:33', 19, NULL, NULL, 0, 'posted', '2026-03-25 16:11:34'),
(20, 1, 3, '2026-03-15', 'sales', 'IFTAR-150326', 8850000.00, '2026-03-25 16:11:49', 20, NULL, NULL, 0, 'posted', '2026-03-25 16:11:49'),
(21, 1, 3, '2026-03-16', 'sales', 'IFTAR-160326', 6450000.00, '2026-03-25 16:12:05', 21, NULL, NULL, 0, 'posted', '2026-03-25 16:12:05'),
(22, 1, 3, '2026-03-17', 'sales', 'IFTAR-170326', 4200000.00, '2026-03-25 16:12:23', 22, NULL, NULL, 0, 'posted', '2026-03-25 16:12:23'),
(23, 1, 3, '2026-03-18', 'sales', 'IFTAR-180326', 7500000.00, '2026-03-25 16:12:41', 23, NULL, NULL, 0, 'posted', '2026-03-25 16:12:41'),
(24, 1, 3, '2026-03-19', 'sales', 'IFTAR-190326', 750000.00, '2026-03-25 16:12:57', 24, NULL, NULL, 0, 'posted', '2026-03-25 16:12:57'),
(25, 1, 3, '2026-03-24', 'sales', 'FIT-RB-240326', 9750000.00, '2026-03-25 16:14:02', 25, NULL, NULL, 0, 'posted', '2026-03-25 16:14:02'),
(26, 1, 3, '2026-03-23', 'sales', 'FIT-RB-230326', 6750000.00, '2026-03-25 16:14:48', 26, NULL, NULL, 0, 'posted', '2026-03-25 16:14:48'),
(27, 1, 3, '2026-03-15', 'sales', 'EVN-KRH-150326', 3620000.00, '2026-03-25 16:15:44', 27, NULL, NULL, 0, 'posted', '2026-03-25 16:15:44'),
(28, 1, 3, '2026-03-20', 'sales', 'FIT-ROOM-200326', 550000.00, '2026-03-25 16:16:11', 28, NULL, NULL, 0, 'posted', '2026-03-25 16:16:11'),
(31, 1, 3, '2026-03-22', 'sales', 'OTA-TRAVELOKA-220326', 4211061.00, '2026-03-25 16:19:06', 31, NULL, NULL, 0, 'posted', '2026-03-25 16:19:06'),
(32, 1, 3, '2026-03-23', 'sales', 'OTA-CTRIP-230326', 1428444.00, '2026-03-25 16:19:37', 32, NULL, NULL, 0, 'posted', '2026-03-25 16:19:37'),
(33, 1, 3, '2026-03-21', 'sales', 'FIT-ROOM-210326', 3200000.00, '2026-03-25 16:20:13', 33, NULL, NULL, 0, 'posted', '2026-03-25 16:20:13'),
(34, 1, 3, '2026-03-21', 'sales', 'FIT-ROOM-210326B', 2295000.00, '2026-03-25 16:20:36', 34, NULL, NULL, 0, 'posted', '2026-03-25 16:20:36'),
(35, 1, 3, '2026-03-21', 'sales', 'OTA-TRAVELOKA-210326C', 768976.00, '2026-03-25 16:21:18', 35, NULL, NULL, 0, 'posted', '2026-03-25 16:21:18'),
(36, 1, 3, '2026-03-21', 'sales', 'OTA-CTRIP-210326D', 779152.00, '2026-03-25 16:21:40', 36, NULL, NULL, 0, 'posted', '2026-03-25 16:21:40'),
(37, 1, 3, '2026-03-25', 'sales', 'FIT-RB-250326', 6100000.00, '2026-03-25 16:23:41', 37, NULL, NULL, 0, 'posted', '2026-03-25 16:23:41'),
(38, 1, 3, '2026-03-22', 'sales', 'FIT-ROOM-220326', 950000.00, '2026-03-25 16:24:03', 38, NULL, NULL, 0, 'posted', '2026-03-25 16:24:03'),
(39, 1, 3, '2026-03-22', 'sales', 'FIT-ROOM-220326A', 2190000.00, '2026-03-25 16:24:25', 39, NULL, NULL, 0, 'posted', '2026-03-25 16:24:25'),
(40, 1, 3, '2026-03-22', 'sales', 'FIT-ROOM-220326B', 4095000.00, '2026-03-25 16:24:37', 40, NULL, NULL, 0, 'posted', '2026-03-25 16:24:37'),
(41, 1, 3, '2026-03-22', 'sales', 'OTA-TRAVELOKA-220326D', 3112523.00, '2026-03-25 16:26:00', 41, NULL, NULL, 0, 'posted', '2026-03-25 16:26:00'),
(42, 1, 3, '2026-03-22', 'sales', 'OTA-CTRIP-220326E', 476148.00, '2026-03-25 16:26:31', 42, NULL, NULL, 0, 'posted', '2026-03-25 16:26:31'),
(43, 1, 3, '2026-03-22', 'sales', 'OTA-AGODA-220326D', 891358.00, '2026-03-25 16:26:48', 43, NULL, NULL, 0, 'posted', '2026-03-25 16:26:48'),
(44, 1, 3, '2026-03-23', 'sales', 'MICE-RB-230326', 1700000.00, '2026-03-25 16:27:22', 44, NULL, NULL, 0, 'posted', '2026-03-25 16:27:22'),
(45, 1, 3, '2026-03-23', 'sales', 'FIT-ROOM-230326', 1700000.00, '2026-03-25 16:27:41', 45, NULL, NULL, 0, 'posted', '2026-03-25 16:27:41'),
(46, 1, 3, '2026-03-23', 'sales', 'FIT-ROOM-230326A', 1200000.00, '2026-03-25 16:27:54', 46, NULL, NULL, 0, 'posted', '2026-03-25 16:27:54'),
(47, 1, 3, '2026-03-23', 'sales', 'OTA-TRAVELOKA-230326B', 4766433.00, '2026-03-25 16:28:26', 47, NULL, NULL, 0, 'posted', '2026-03-25 16:28:26'),
(48, 1, 3, '2026-03-23', 'sales', 'OTA-TIKETCOM-230326C', 650000.00, '2026-03-25 16:28:48', 48, NULL, NULL, 0, 'posted', '2026-03-25 16:28:48'),
(49, 1, 3, '2026-03-24', 'sales', 'FIT-ROOM-240326', 850000.00, '2026-03-25 16:29:16', 49, NULL, NULL, 0, 'posted', '2026-03-25 16:29:16'),
(50, 1, 3, '2026-03-25', 'sales', 'OTA-TRAVELOKA-250326', 3075904.00, '2026-03-25 16:30:27', 50, NULL, NULL, 0, 'posted', '2026-03-25 16:30:27'),
(51, 1, 3, '2026-03-31', 'sales', 'FIT-RB-310326', 53260000.00, '2026-03-25 16:31:35', 51, NULL, NULL, 0, 'posted', '2026-03-25 16:31:35'),
(53, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-FNBP', 46960396.00, '2026-03-25 17:54:50', 54, NULL, NULL, 0, 'draft', '2026-03-25 17:54:50'),
(54, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-FNBS', 3876900.00, '2026-03-25 17:55:09', 55, NULL, NULL, 0, 'draft', '2026-03-25 17:55:09'),
(55, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-HK', 13239500.00, '2026-03-25 17:55:32', 56, NULL, NULL, 0, 'draft', '2026-03-25 17:55:32'),
(56, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-ENG-POMEC', 34317872.00, '2026-03-25 17:55:55', 57, NULL, NULL, 0, 'draft', '2026-03-25 17:55:55'),
(57, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-HRD', 8810000.00, '2026-03-25 17:56:12', 58, NULL, NULL, 0, 'draft', '2026-03-25 17:56:12'),
(58, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-ACCOUNTING', 1321504.00, '2026-03-25 17:56:54', 59, NULL, NULL, 0, 'draft', '2026-03-25 17:56:54'),
(59, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-SM', 2838435.00, '2026-03-25 17:57:11', 60, NULL, NULL, 0, 'draft', '2026-03-25 17:57:11'),
(60, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-FO', 1318609.00, '2026-03-25 17:57:27', 61, NULL, NULL, 0, 'draft', '2026-03-25 17:57:27'),
(61, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-ENG', 734850.00, '2026-03-25 17:57:44', 62, NULL, NULL, 0, 'draft', '2026-03-25 17:57:44'),
(62, 1, 3, '2026-03-25', 'expense_salary', 'EXPN-IT', 2220421.00, '2026-03-25 17:58:00', 63, NULL, NULL, 0, 'draft', '2026-03-25 17:58:00'),
(64, 1, 3, '2026-03-27', 'sales', 'ORD20260326180540492137', 491400.49, '2026-03-27 02:27:40', 65, NULL, NULL, 0, 'draft', '2026-03-27 02:27:40'),
(72, 1, 3, '2026-03-27', 'sales_partial', 'ORD20260327032342E81330', 1000000.00, '2026-03-27 05:17:07', 73, NULL, NULL, 0, 'draft', '2026-03-27 05:17:07'),
(73, 1, 3, '2026-03-27', 'receive_payment', 'ORD20260327032342E81330', 500000.00, '2026-03-27 05:17:51', 74, NULL, NULL, 0, 'draft', '2026-03-27 05:17:51'),
(74, 1, 3, '2026-03-27', 'sales_partial', 'ORD2026032705455755A360', 5000000.00, '2026-03-27 05:46:57', 75, NULL, NULL, 0, 'draft', '2026-03-27 05:46:57'),
(75, 1, 3, '2026-03-27', 'receive_payment', 'ORD2026032705455755A360', 2500000.00, '2026-03-27 05:47:27', 76, NULL, NULL, 0, 'draft', '2026-03-27 05:47:27'),
(84, 1, 2, '2026-03-27', 'sales_food', 'ORD202603271206488106AA', 37814342.34, '2026-03-27 12:10:55', 85, NULL, NULL, 0, 'draft', '2026-03-27 12:10:55'),
(85, 1, 2, '2026-03-27', 'sales_food', 'ORD202603271207313DADAB', 14246519.25, '2026-03-27 12:11:03', 86, NULL, NULL, 0, 'draft', '2026-03-27 12:11:03'),
(86, 1, 2, '2026-03-27', 'sales_food', 'ORD2026032712074738E254', 8173628.17, '2026-03-27 12:11:15', 87, NULL, NULL, 0, 'draft', '2026-03-27 12:11:15'),
(87, 1, 2, '2026-03-27', 'sales_food', 'ORD20260327120802250176', 4312858.31, '2026-03-27 12:11:22', 88, NULL, NULL, 0, 'draft', '2026-03-27 12:11:22'),
(88, 1, 2, '2026-03-27', 'sales_beverage', 'ORD202603271208171BE415', 12769500.41, '2026-03-27 12:11:30', 89, NULL, NULL, 0, 'draft', '2026-03-27 12:11:30'),
(89, 1, 2, '2026-03-27', 'sales_beverage', 'ORD2026032712083206FB60', 33711004.91, '2026-03-27 12:11:39', 90, NULL, NULL, 0, 'draft', '2026-03-27 12:11:39'),
(90, 1, 2, '2026-03-25', 'expense_kitchen', 'EXP-KIT', 15876500.00, '2026-03-27 12:23:51', 91, NULL, NULL, 0, 'draft', '2026-03-27 12:23:51'),
(91, 1, 2, '2026-03-25', 'expense_bar', 'EXP-BAR', 2300600.00, '2026-03-27 12:24:04', 92, NULL, NULL, 0, 'draft', '2026-03-27 12:24:04'),
(92, 1, 2, '2026-03-25', 'expense_catering', 'EXP-CATR', 5540900.00, '2026-03-27 12:24:17', 93, NULL, NULL, 0, 'draft', '2026-03-27 12:24:17'),
(93, 1, 2, '2026-03-25', 'expense_buffet', 'EXP-BUFF', 7650700.00, '2026-03-27 12:24:29', 94, NULL, NULL, 0, 'draft', '2026-03-27 12:24:29'),
(94, 1, 2, '2026-03-25', 'expense_entertainment', 'EXP-ENT', 500000.00, '2026-03-27 12:30:43', 95, NULL, NULL, 0, 'draft', '2026-03-27 12:30:43'),
(95, 1, 2, '2026-03-25', 'expense_fuel_gas', 'EXP-FG', 2358750.00, '2026-03-27 12:30:59', 96, NULL, NULL, 0, 'draft', '2026-03-27 12:30:59'),
(96, 1, 2, '2026-03-25', 'payable_payment', 'EXP-AP', 6997000.00, '2026-03-27 12:34:29', 97, NULL, NULL, 0, 'draft', '2026-03-27 12:34:29'),
(97, 1, 2, '2026-03-25', 'expense_payroll', 'EXP-PAYROLL', 38845833.00, '2026-03-27 12:34:55', 98, NULL, NULL, 0, 'draft', '2026-03-27 12:34:55'),
(98, 1, 2, '2026-03-25', 'expense_pomec', 'EXP-POMEC', 10914012.00, '2026-03-27 12:35:11', 99, NULL, NULL, 0, 'draft', '2026-03-27 12:35:11'),
(99, 1, 3, '2026-03-27', 'sales_catering', 'ORD20260327160946AEACC1', 9009009.01, '2026-03-27 16:12:34', 100, NULL, NULL, 0, 'draft', '2026-03-27 16:12:34'),
(100, 1, 2, '2026-03-27', 'receive_payment', 'ORD2026032712083206FB60', 20000000.00, '2026-03-27 17:05:05', 101, NULL, NULL, 0, 'draft', '2026-03-27 17:05:05');

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
  `service_account_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_account_map`
--

INSERT INTO `transaction_account_map` (`id`, `company_id`, `trx_type`, `debit_account_id`, `credit_account_id`, `service_account_id`, `created_at`) VALUES
(1, 1, 'sales', 1, 41, 42, '2026-02-21 18:21:20'),
(2, 1, 'expense_salary', 9, 1, NULL, '2026-02-21 18:21:27'),
(3, 1, 'expense_electric', 29, 1, NULL, '2026-02-21 18:21:33'),
(4, 1, 'expense_operational', 10, 1, NULL, '2026-02-21 18:21:33'),
(5, 1, 'capital_injection', 1, 6, NULL, '2026-02-21 23:38:13'),
(6, 1, 'capital_withdrawal', 1, 6, NULL, '2026-02-21 23:38:13'),
(7, 1, 'dividend_distribution', 1, 6, NULL, '2026-02-21 23:38:13'),
(8, 1, 'sales_partial', 2, 41, 42, '2026-03-27 02:41:08'),
(9, 1, 'receive_payment', 1, 2, NULL, '2026-03-27 02:50:54'),
(10, 1, 'sales_food', 1, 50, 55, '2026-03-27 11:32:31'),
(11, 1, 'sales_beverage', 1, 51, 55, '2026-03-27 11:32:31'),
(12, 1, 'sales_shisha', 1, 52, 55, '2026-03-27 11:32:31'),
(13, 1, 'sales_catering', 1, 53, 55, '2026-03-27 11:32:31'),
(14, 1, 'sales_package', 1, 54, 55, '2026-03-27 11:32:31'),
(15, 1, 'sales_food_partial', 2, 50, 55, '2026-03-27 11:32:40'),
(16, 1, 'sales_beverage_partial', 2, 51, 55, '2026-03-27 11:32:40'),
(18, 1, 'sales_shisha_partial', 2, 52, 55, '2026-03-27 11:55:44'),
(19, 1, 'sales_catering_partial', 2, 53, 55, '2026-03-27 11:55:44'),
(20, 1, 'sales_package_partial', 2, 54, 55, '2026-03-27 11:55:44'),
(21, 1, 'sales_elqahua', 1, 56, 55, '2026-03-27 12:20:48'),
(22, 1, 'sales_elqahua_partial', 2, 56, 55, '2026-03-27 12:20:48'),
(23, 1, 'sales_service', 1, 55, NULL, '2026-03-27 12:20:48'),
(24, 1, 'sales_service_partial', 2, 55, NULL, '2026-03-27 12:20:48'),
(25, 1, 'expense_kitchen', 57, 1, NULL, '2026-03-27 12:21:48'),
(26, 1, 'expense_bar', 58, 1, NULL, '2026-03-27 12:21:48'),
(27, 1, 'expense_shisha', 59, 1, NULL, '2026-03-27 12:21:48'),
(28, 1, 'expense_catering', 65, 1, NULL, '2026-03-27 12:21:48'),
(29, 1, 'expense_buffet', 66, 1, NULL, '2026-03-27 12:21:48'),
(30, 1, 'expense_entertainment', 78, 1, NULL, '2026-03-27 12:21:48'),
(31, 1, 'expense_fuel_gas', 67, 1, NULL, '2026-03-27 12:21:48'),
(33, 1, 'expense_payroll', 74, 1, NULL, '2026-03-27 12:21:48'),
(34, 1, 'expense_pomec', 76, 1, NULL, '2026-03-27 12:21:48'),
(35, 1, 'expense_cleaning', 78, 1, NULL, '2026-03-27 12:29:55'),
(36, 1, 'expense_floor', 80, 1, NULL, '2026-03-27 12:29:55'),
(37, 1, 'expense_reimburse', 81, 1, NULL, '2026-03-27 12:29:55'),
(38, 1, 'expense_photoshoot', 69, 1, NULL, '2026-03-27 12:29:55'),
(39, 1, 'expense_pest_control', 70, 1, NULL, '2026-03-27 12:29:55'),
(40, 1, 'expense_marketing', 71, 1, NULL, '2026-03-27 12:29:55'),
(41, 1, 'expense_branding', 72, 1, NULL, '2026-03-27 12:29:55'),
(42, 1, 'expense_maintenance', 73, 1, NULL, '2026-03-27 12:29:55'),
(43, 1, 'expense_hrd', 75, 1, NULL, '2026-03-27 12:29:55'),
(44, 1, 'expense_other', 77, 1, NULL, '2026-03-27 12:29:55'),
(45, 1, 'payable_payment', 4, 1, NULL, '2026-03-27 12:33:49');

-- --------------------------------------------------------

--
-- Table structure for table `transaction_taxes`
--

CREATE TABLE `transaction_taxes` (
  `id` int(11) NOT NULL,
  `transaction_id` int(11) NOT NULL,
  `tax_code_id` int(11) NOT NULL,
  `tax_base` decimal(18,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction_taxes`
--

INSERT INTO `transaction_taxes` (`id`, `transaction_id`, `tax_code_id`, `tax_base`, `tax_amount`, `created_at`) VALUES
(2, 64, 5, 540540.54, 59459.46, '2026-03-26 18:36:15'),
(6, 68, 3, 540540.54, 59459.46, '2026-03-26 19:13:59'),
(7, 69, 1, 600000.00, 66000.00, '2026-03-26 19:14:29'),
(8, 70, 3, 600000.00, 66000.00, '2026-03-26 19:15:00'),
(9, 71, 1, 540540.54, 59459.46, '2026-03-26 19:16:43'),
(10, 72, 5, 594594.59, 59459.46, '2026-03-26 19:17:27'),
(11, 73, 1, 540540.54, 59459.46, '2026-03-26 19:18:21'),
(12, 74, 1, 540540.54, 59459.46, '2026-03-26 19:23:17'),
(13, 75, 1, 540540.54, 59459.46, '2026-03-26 19:23:56'),
(14, 76, 3, 540540.54, 59459.46, '2026-03-26 19:24:42'),
(15, 77, 3, 600000.00, 66000.00, '2026-03-26 19:25:08'),
(20, 82, 6, 540540.54, 59459.46, '2026-03-27 02:01:21'),
(22, 64, 6, 491400.49, 59459.46, '2026-03-27 02:27:40'),
(23, 68, 6, 819000.82, 99099.10, '2026-03-27 04:15:43'),
(24, 70, 6, 819000.82, 99099.10, '2026-03-27 05:08:53'),
(25, 72, 6, 819000.82, 99099.10, '2026-03-27 05:17:07'),
(26, 74, 6, 4095004.10, 495495.49, '2026-03-27 05:46:57'),
(27, 76, 6, 81900081.90, 9909909.91, '2026-03-27 06:20:07'),
(29, 82, 6, 81900081.90, 9909909.91, '2026-03-27 12:01:56'),
(30, 84, 6, 37814342.34, 4575535.43, '2026-03-27 12:10:55'),
(31, 85, 6, 14246519.25, 1723828.83, '2026-03-27 12:11:03'),
(32, 86, 6, 8173628.17, 989009.01, '2026-03-27 12:11:15'),
(33, 87, 6, 4312858.31, 521855.86, '2026-03-27 12:11:22'),
(34, 88, 6, 12769500.41, 1545109.55, '2026-03-27 12:11:30'),
(35, 89, 6, 33711004.91, 4079031.60, '2026-03-27 12:11:39'),
(36, 99, 6, 9009009.01, 1090090.09, '2026-03-27 16:12:34');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(1) NOT NULL,
  `company_id` int(1) NOT NULL,
  `branch_id` int(1) NOT NULL,
  `role` enum('worker','hotel_hr','hotel_fo','hotel_hk','hotel_fnb_service','hotel_fnb_production','hotel_fna','hotel_eng','hotel_sales','hotel_gm','admin') NOT NULL DEFAULT 'worker',
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

INSERT INTO `users` (`id`, `company_id`, `branch_id`, `role`, `name`, `email`, `phone`, `password`, `photo`, `is_active`, `last_login_at`, `created_at`, `created_by`, `updated_at`, `updated_by`, `deleted_at`, `deleted_by`) VALUES
(1, 0, 0, 'admin', 'Mick Jagger', 'admin@admin.com', '0812', '$2y$10$TYZN8k0YxaB.jxCtqA4sl.JnllEeN3/UF9oGYK5.LTvbGlCe7HE82', NULL, 'active', '2026-02-21 23:27:27', '2026-01-18 12:25:53', 1, '2026-02-21 23:27:27', NULL, NULL, NULL),
(2, 1, 3, 'admin', 'Arya Seftian', 'yerblues6@gmail.com', '895330907220', '$2y$10$relLlluCofLYvJKJDW65zuxFadTF4X4A.mCur9V2uEbiZVW8vGhaa', '2.png', 'active', '2026-03-27 16:36:19', '2026-01-18 18:59:55', 1, '2026-03-27 16:36:19', NULL, NULL, NULL),
(3, 1, 3, 'admin', 'Muhammad', 'muhammad@gmail.com', '99988776', '$2y$10$relLlluCofLYvJKJDW65zuxFadTF4X4A.mCur9V2uEbiZVW8vGhaa', '3.png', 'active', '2026-04-01 13:39:24', '2026-01-19 10:53:08', 1, '2026-04-01 13:39:24', NULL, NULL, NULL),
(9, 1, 3, '', 'Fazry', 'fazry@gmail.com', '9090900', '$2y$10$kZ7bWdcazHMWAXNLYck2ZOsRCHUq.9NKuaHz4RWkBmY4EwhP7KGJW', NULL, 'active', NULL, '2026-03-26 18:06:30', NULL, '2026-03-26 18:06:30', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_memberships`
--

CREATE TABLE `user_memberships` (
  `id` bigint(20) NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `tier_id` bigint(20) DEFAULT NULL,
  `total_spending` decimal(14,2) DEFAULT 0.00,
  `total_points` int(11) DEFAULT 0,
  `start_date` datetime DEFAULT NULL,
  `expire_date` datetime DEFAULT NULL,
  `status` enum('active','expired') DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_memberships`
--

INSERT INTO `user_memberships` (`id`, `user_id`, `tier_id`, `total_spending`, `total_points`, `start_date`, `expire_date`, `status`, `created_at`) VALUES
(1, 3, 5, 270645009.00, 16955, NULL, NULL, 'active', '2026-03-11 04:09:21');

-- --------------------------------------------------------

--
-- Table structure for table `user_points`
--

CREATE TABLE `user_points` (
  `id` bigint(20) NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `points` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_points`
--

INSERT INTO `user_points` (`id`, `user_id`, `points`) VALUES
(1, 3, 16955),
(4, 9, 0),
(5, 2, 0);

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
(2, 2, 2, 0),
(3, 4, 2, 0);

-- --------------------------------------------------------

--
-- Table structure for table `variants`
--

CREATE TABLE `variants` (
  `id` bigint(20) NOT NULL,
  `item_id` bigint(20) DEFAULT NULL,
  `name` enum('hot','ice') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `no_po` varchar(50) NOT NULL,
  `pic` varchar(50) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `address` text NOT NULL,
  `status` enum('Aktif','Non Aktif','') DEFAULT NULL,
  `is_delete` int(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `kode`, `no_po`, `pic`, `phone`, `address`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 'Salam Supply', 'SS', 'SS/0326-0001', 'LOREM', '0812233445', 'Jl. Sancang', 'Aktif', 0, '2026-03-27 12:41:58', '2026-03-27 12:41:58');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_items`
--

CREATE TABLE `vendor_items` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `sparepart` varchar(255) NOT NULL,
  `type` enum('Sayur','Buah','Elektrik') NOT NULL,
  `harga` int(11) NOT NULL,
  `no_seri` varchar(255) NOT NULL,
  `status` enum('Aktif','Non Aktif') DEFAULT NULL,
  `is_delete` int(1) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_items`
--

INSERT INTO `vendor_items` (`id`, `vendor_id`, `sparepart`, `type`, `harga`, `no_seri`, `status`, `is_delete`, `created_at`, `updated_at`) VALUES
(1, 1, 'Lampu LED 20 Watt', 'Elektrik', 10000, '', 'Aktif', 0, '2026-03-27 12:43:25', '2026-03-27 12:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint(20) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `discount_type` enum('percent','fixed') DEFAULT NULL,
  `discount_value` decimal(12,2) DEFAULT NULL,
  `max_usage` int(11) DEFAULT NULL,
  `used_count` int(11) DEFAULT 0,
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `code`, `discount_type`, `discount_value`, `max_usage`, `used_count`, `start_date`, `end_date`, `status`) VALUES
(1, 'DISC10', 'percent', 10.00, 1, 0, '2026-03-09 23:39:54', '2026-03-10 23:39:54', 'active'),
(2, 'DISC20', 'percent', 20.00, 1, 0, '2026-03-17 01:24:00', '2026-03-19 01:24:00', 'active');

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

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `id` bigint(20) NOT NULL,
  `user_id` int(20) DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`id`, `user_id`, `balance`) VALUES
(1, 3, 45000.00);

-- --------------------------------------------------------

--
-- Table structure for table `wallet_transactions`
--

CREATE TABLE `wallet_transactions` (
  `id` bigint(20) NOT NULL,
  `wallet_id` bigint(20) DEFAULT NULL,
  `type` enum('credit','debit') DEFAULT NULL,
  `amount` decimal(12,2) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` bigint(20) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallet_transactions`
--

INSERT INTO `wallet_transactions` (`id`, `wallet_id`, `type`, `amount`, `reference_type`, `reference_id`, `description`, `created_at`) VALUES
(1, 1, 'credit', 100000.00, 'topup', NULL, 'Wallet Topup', '2026-03-09 23:34:10'),
(2, 1, 'credit', 250000.00, 'topup', NULL, 'Wallet Topup', '2026-03-09 23:37:59'),
(3, 1, 'debit', 35000.00, 'order', 8, 'Order payment', '2026-03-22 18:05:48'),
(4, 1, 'debit', 35000.00, 'order', 9, 'Order payment', '2026-03-22 18:17:19'),
(5, 1, 'debit', 35000.00, 'order', 10, 'Order payment', '2026-03-22 18:20:47'),
(6, 1, 'debit', 35000.00, 'order', 11, 'Order payment', '2026-03-22 18:22:39'),
(7, 1, 'debit', 35000.00, 'order', 12, 'Order payment', '2026-03-22 18:24:59'),
(8, 1, 'debit', 35000.00, 'order', 13, 'Order payment', '2026-03-22 18:27:17'),
(9, 1, 'debit', 35000.00, 'order', 14, 'Order payment', '2026-03-22 18:29:08'),
(10, 1, 'debit', 35000.00, 'order', 15, 'Order payment', '2026-03-22 18:29:52'),
(11, 1, 'debit', 25000.00, 'order', 16, 'Order payment', '2026-03-22 18:32:15');

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
-- Indexes for table `branches_target`
--
ALTER TABLE `branches_target`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`) USING BTREE;

--
-- Indexes for table `branch_items`
--
ALTER TABLE `branch_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `branch_variant` (`branch_id`,`variant_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `variant_id` (`variant_id`) USING BTREE;

--
-- Indexes for table `branch_opening_hours`
--
ALTER TABLE `branch_opening_hours`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `branch_referral_rules`
--
ALTER TABLE `branch_referral_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `business_partners`
--
ALTER TABLE `business_partners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chats`
--
ALTER TABLE `chats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_id` (`chat_id`);

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
-- Indexes for table `form_pengajuan`
--
ALTER TABLE `form_pengajuan`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_pengajuan_detail`
--
ALTER TABLE `form_pengajuan_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pengajuan_detail_pengajuan` (`pengajuan_id`),
  ADD KEY `fk_pengajuan_detail_vendor_item` (`vendor_item_id`);

--
-- Indexes for table `form_purchasing`
--
ALTER TABLE `form_purchasing`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_purchasing_pengajuan` (`pengajuan_id`);

--
-- Indexes for table `inventori`
--
ALTER TABLE `inventori`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventori_vendor` (`vendor_id`),
  ADD KEY `fk_inventori_vendor_item` (`vendor_item_id`),
  ADD KEY `fk_inventori_purchasing` (`form_purchasing_id`);

--
-- Indexes for table `inventori_items`
--
ALTER TABLE `inventori_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inventori_items_inventori` (`inventori_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

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
-- Indexes for table `loyalty_tiers`
--
ALTER TABLE `loyalty_tiers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenances`
--
ALTER TABLE `maintenances`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `maintenance_orders`
--
ALTER TABLE `maintenance_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_id` (`maintenance_id`),
  ADD KEY `fk_maintenance_orders_inventori` (`inventori_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `branch_id` (`branch_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `point_rules`
--
ALTER TABLE `point_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `ratio_spend`
--
ALTER TABLE `ratio_spend`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hotel` (`hotel_id`),
  ADD KEY `idx_department` (`department_category`),
  ADD KEY `idx_range` (`min_value`,`max_value`);

--
-- Indexes for table `ratio_worker`
--
ALTER TABLE `ratio_worker`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_hotel` (`hotel_id`),
  ADD KEY `idx_department` (`department_category`),
  ADD KEY `idx_range` (`min_value`,`max_value`);

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
-- Indexes for table `transaction_taxes`
--
ALTER TABLE `transaction_taxes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transaction_id` (`transaction_id`),
  ADD KEY `tax_code_id` (`tax_code_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `tier_id` (`tier_id`);

--
-- Indexes for table `user_points`
--
ALTER TABLE `user_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `variants`
--
ALTER TABLE `variants`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_items`
--
ALTER TABLE `vendor_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vendor_items_vendor` (`vendor_id`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wallet_id` (`wallet_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `branches_target`
--
ALTER TABLE `branches_target`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `branch_items`
--
ALTER TABLE `branch_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `branch_opening_hours`
--
ALTER TABLE `branch_opening_hours`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `branch_referral_rules`
--
ALTER TABLE `branch_referral_rules`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `business_partners`
--
ALTER TABLE `business_partners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `chats`
--
ALTER TABLE `chats`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `coa`
--
ALTER TABLE `coa`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `coa_opening_balances`
--
ALTER TABLE `coa_opening_balances`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `form_pengajuan`
--
ALTER TABLE `form_pengajuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `form_pengajuan_detail`
--
ALTER TABLE `form_pengajuan_detail`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `form_purchasing`
--
ALTER TABLE `form_purchasing`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventori`
--
ALTER TABLE `inventori`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `inventori_items`
--
ALTER TABLE `inventori_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `journal_approvals`
--
ALTER TABLE `journal_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_details`
--
ALTER TABLE `journal_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=242;

--
-- AUTO_INCREMENT for table `journal_headers`
--
ALTER TABLE `journal_headers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `journal_taxes`
--
ALTER TABLE `journal_taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loyalty_tiers`
--
ALTER TABLE `loyalty_tiers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `maintenances`
--
ALTER TABLE `maintenances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `maintenance_orders`
--
ALTER TABLE `maintenance_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `point_rules`
--
ALTER TABLE `point_rules`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `point_transactions`
--
ALTER TABLE `point_transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `ratio_spend`
--
ALTER TABLE `ratio_spend`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `ratio_worker`
--
ALTER TABLE `ratio_worker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `transaction_account_map`
--
ALTER TABLE `transaction_account_map`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `transaction_taxes`
--
ALTER TABLE `transaction_taxes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user_memberships`
--
ALTER TABLE `user_memberships`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_points`
--
ALTER TABLE `user_points`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `variants`
--
ALTER TABLE `variants`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `vendor_items`
--
ALTER TABLE `vendor_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- Constraints for table `branches_target`
--
ALTER TABLE `branches_target`
  ADD CONSTRAINT `fk_branches_target_branch` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `branch_items`
--
ALTER TABLE `branch_items`
  ADD CONSTRAINT `branch_items_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`),
  ADD CONSTRAINT `branch_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `branch_opening_hours`
--
ALTER TABLE `branch_opening_hours`
  ADD CONSTRAINT `branch_opening_hours_ibfk_1` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `business_partners`
--
ALTER TABLE `business_partners`
  ADD CONSTRAINT `business_partners_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `chats`
--
ALTER TABLE `chats`
  ADD CONSTRAINT `chats_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `chats_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD CONSTRAINT `chat_messages_ibfk_1` FOREIGN KEY (`chat_id`) REFERENCES `chats` (`id`);

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
-- Constraints for table `form_pengajuan_detail`
--
ALTER TABLE `form_pengajuan_detail`
  ADD CONSTRAINT `fk_pengajuan_detail_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `form_pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pengajuan_detail_vendor_item` FOREIGN KEY (`vendor_item_id`) REFERENCES `vendor_items` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `form_purchasing`
--
ALTER TABLE `form_purchasing`
  ADD CONSTRAINT `fk_purchasing_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `form_pengajuan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inventori`
--
ALTER TABLE `inventori`
  ADD CONSTRAINT `fk_inventori_purchasing` FOREIGN KEY (`form_purchasing_id`) REFERENCES `form_purchasing` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventori_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventori_vendor_item` FOREIGN KEY (`vendor_item_id`) REFERENCES `vendor_items` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `inventori_items`
--
ALTER TABLE `inventori_items`
  ADD CONSTRAINT `fk_inventori_items_inventori` FOREIGN KEY (`inventori_id`) REFERENCES `inventori` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

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
-- Constraints for table `maintenance_orders`
--
ALTER TABLE `maintenance_orders`
  ADD CONSTRAINT `fk_maintenance_orders_header` FOREIGN KEY (`maintenance_id`) REFERENCES `maintenances` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_maintenance_orders_inventori` FOREIGN KEY (`inventori_id`) REFERENCES `inventori` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `point_transactions`
--
ALTER TABLE `point_transactions`
  ADD CONSTRAINT `point_transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

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

--
-- Constraints for table `transaction_taxes`
--
ALTER TABLE `transaction_taxes`
  ADD CONSTRAINT `fk_transaction_tax_code` FOREIGN KEY (`tax_code_id`) REFERENCES `tax_codes` (`id`),
  ADD CONSTRAINT `fk_transaction_tax_trx` FOREIGN KEY (`transaction_id`) REFERENCES `transactions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD CONSTRAINT `user_memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `user_memberships_ibfk_2` FOREIGN KEY (`tier_id`) REFERENCES `loyalty_tiers` (`id`);

--
-- Constraints for table `user_points`
--
ALTER TABLE `user_points`
  ADD CONSTRAINT `user_points_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vendor_items`
--
ALTER TABLE `vendor_items`
  ADD CONSTRAINT `fk_vendor_items_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `wallet_transactions`
--
ALTER TABLE `wallet_transactions`
  ADD CONSTRAINT `wallet_transactions_ibfk_1` FOREIGN KEY (`wallet_id`) REFERENCES `wallets` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
