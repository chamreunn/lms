-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 08, 2024 at 03:19 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `elms`
--

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `hdepartment_id` int DEFAULT NULL,
  `ddepartment_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `hdepartment_id`, `ddepartment_id`, `created_at`, `updated_at`) VALUES
(1, 'នាយកដ្ឋានសវនកម្មទី២', 'adsf', NULL, NULL, '2024-07-10 01:27:08', '2024-07-10 01:27:08'),
(2, 'នាយកដ្ឋានសវនកម្មទី១', '', NULL, NULL, '2024-07-10 01:31:21', '2024-07-10 01:31:21'),
(3, 'នាយកដ្ឋានកិច្ចការទូទៅ', '', NULL, NULL, '2024-07-10 01:31:34', '2024-07-10 01:31:34'),
(4, 'អង្គភាពសវនកម្មផ្ទៃក្នុង', '', NULL, NULL, '2024-07-10 19:55:22', '2024-07-10 19:55:22');

-- --------------------------------------------------------

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
CREATE TABLE IF NOT EXISTS `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `position_id` int NOT NULL,
  `office_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`),
  KEY `position_id` (`position_id`),
  KEY `office_id` (`office_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `employees`
--

INSERT INTO `employees` (`id`, `user_id`, `position_id`, `office_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2024-07-08 09:06:42', '2024-07-08 09:06:42'),
(2, 2, 1, 1, '2024-07-08 09:09:40', '2024-07-08 09:09:40');

-- --------------------------------------------------------

--
-- Table structure for table `latetype`
--

DROP TABLE IF EXISTS `latetype`;
CREATE TABLE IF NOT EXISTS `latetype` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `color` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `latetype`
--

INSERT INTO `latetype` (`id`, `user_id`, `name`, `color`, `created_at`, `updated_at`) VALUES
(3, 3, 'លិខិតចូលយឺត', 'bg-blue', '2024-07-23 03:58:49', '2024-07-23 03:58:49'),
(4, 3, 'លិខិតចេញយឺត', 'bg-orange', '2024-07-23 03:59:06', '2024-07-23 03:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `late_in_out`
--

DROP TABLE IF EXISTS `late_in_out`;
CREATE TABLE IF NOT EXISTS `late_in_out` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `att_id` int NOT NULL,
  `date` date NOT NULL,
  `late_in` time DEFAULT NULL,
  `late_out` time DEFAULT NULL,
  `late` int DEFAULT NULL,
  `reasons` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `att_id` (`att_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `late_in_out`
--

INSERT INTO `late_in_out` (`id`, `user_id`, `att_id`, `date`, `late_in`, `late_out`, `late`, `reasons`, `created_at`, `updated_at`) VALUES
(1, 11, 0, '2024-08-01', '09:05:00', NULL, 5, 'sdf', '2024-08-01 05:52:00', '2024-08-01 05:52:00'),
(2, 11, 0, '2024-08-06', '09:10:00', NULL, 10, 'asdfsadf', '2024-08-06 02:40:57', '2024-08-06 02:40:57'),
(3, 11, 0, '2024-08-06', '09:05:00', NULL, 5, 'okay', '2024-08-06 09:03:55', '2024-08-06 09:03:55');

-- --------------------------------------------------------

--
-- Table structure for table `leave_approvals`
--

DROP TABLE IF EXISTS `leave_approvals`;
CREATE TABLE IF NOT EXISTS `leave_approvals` (
  `id` int NOT NULL AUTO_INCREMENT,
  `leave_request_id` int NOT NULL,
  `approver_id` int NOT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL,
  `remarks` text,
  `signature` longblob,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `leave_request_id` (`leave_request_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_approvals`
--

INSERT INTO `leave_approvals` (`id`, `leave_request_id`, `approver_id`, `status`, `remarks`, `signature`, `created_at`, `updated_at`) VALUES
(1, 6, 12, 'Approved', 'អាចឈប់សម្រាកបាន', 0x36366231386136653965373164382e36363635373830312e706e67, '2024-08-06 02:29:02', '2024-08-06 02:29:02'),
(2, 7, 12, 'Rejected', 'មិនអនុម័ត', NULL, '2024-08-06 02:29:37', '2024-08-06 02:29:37'),
(3, 6, 13, 'Approved', 'okay', NULL, '2024-08-06 02:32:11', '2024-08-06 02:32:11'),
(14, 7, 12, 'Approved', 'អាចឈប់សម្រាកបាន', 0x36366231656561313966366264362e38303835313535392e706e67, '2024-08-06 09:36:33', '2024-08-06 09:36:33'),
(15, 9, 12, 'Approved', 'អាចឈប់សម្រាកបាន', 0x36366231656562303630653738312e31323835363934392e706e67, '2024-08-06 09:36:48', '2024-08-06 09:36:48'),
(16, 9, 13, 'Approved', 'okay', NULL, '2024-08-06 09:37:51', '2024-08-06 09:37:51'),
(17, 9, 18, 'Approved', 'okay', NULL, '2024-08-06 09:38:16', '2024-08-06 09:38:16'),
(18, 9, 19, 'Approved', 'okay', NULL, '2024-08-06 09:38:37', '2024-08-06 09:38:37');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

DROP TABLE IF EXISTS `leave_requests`;
CREATE TABLE IF NOT EXISTS `leave_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `leave_type_id` int DEFAULT NULL,
  `leave_type` varchar(255) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `attachment` varchar(512) DEFAULT NULL,
  `signature` varchar(512) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `dhead_office` enum('Pending','Approved','Rejected','Pending Further Approval') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Pending',
  `head_office` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `dhead_department` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `head_department` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `dhead_unit` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `head_unit` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `remarks` text,
  `num_date` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `fk_leave_requests_leave_types` (`leave_type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `user_id`, `leave_type_id`, `leave_type`, `start_date`, `end_date`, `attachment`, `signature`, `status`, `dhead_office`, `head_office`, `dhead_department`, `head_department`, `dhead_unit`, `head_unit`, `remarks`, `num_date`, `created_at`, `updated_at`) VALUES
(9, 11, 6, 'ផ្សេងៗ', '2024-08-06', '2024-08-07', NULL, '66b1ee74678aa3.63697363.png', 'Pending', 'Approved', 'Approved', 'Approved', 'Approved', 'Pending', 'Pending', 'បាក់ស្ពាន', 2, '2024-08-06 09:35:48', '2024-08-07 01:27:05'),
(7, 16, 2, 'ច្បាប់ឈប់សម្រាករយៈពេលវែង', '2024-08-06', '2024-11-06', '66b18a0e2627a0.98736588.docx', '66b18a0e268b08.56366558.png', 'Pending', 'Approved', 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', 'មាតុភាព', 67, '2024-08-06 02:27:26', '2024-08-06 09:36:33'),
(8, 12, 6, 'ផ្សេងៗ', '2024-08-06', '2024-08-06', '66b18acdf39e12.49975670.docx', '66b18acdf3c710.20531394.png', 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', 'Pending', 'ាសដថសដាថសាដថ', 1, '2024-08-06 02:30:38', '2024-08-06 09:29:25');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

DROP TABLE IF EXISTS `leave_types`;
CREATE TABLE IF NOT EXISTS `leave_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `duration` int NOT NULL,
  `attachment_required` enum('Yes','No') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'Yes',
  `color` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `description`, `created_at`, `updated_at`, `duration`, `attachment_required`, `color`) VALUES
(1, 'ឈប់សម្រាករយៈពេលខ្លី', 'អាពាហ៍ពិពាហ៍,​ ការព្យាបាលជំងឺ, ការចូលរួមមរណៈទុក្ខ, លំហែបិតុភាព', '2024-08-06 02:16:08', '2024-08-06 02:16:08', 5, '', 'bg-blue'),
(2, 'ច្បាប់ឈប់សម្រាករយៈពេលវែង', 'លំហែមាតុភាព', '2024-08-06 02:16:42', '2024-08-06 02:16:42', 90, 'Yes', 'bg-azure'),
(3, 'ច្បាប់ឈប់សម្រាកព្យបាលជំងឺ', '', '2024-08-06 02:17:23', '2024-08-06 02:17:23', 90, 'Yes', 'bg-indigo'),
(4, 'ច្បាប់ប្រចាំឆ្នាំ', 'អាចឈប់សម្រាកបាន ១៥ថ្ងៃ និងអាចទទួលបាន​ ៣០ថ្ងៃប្រសិនបើមន្ត្រីធ្វើការ២ឆ្នាំជាប់គ្នា', '2024-08-06 02:18:55', '2024-08-06 02:18:55', 30, '', 'bg-purple'),
(5, 'ច្បាប់ឈប់សម្រាកដោយមានធុរៈផ្ទាល់ខ្លួន', '', '2024-08-06 02:19:30', '2024-08-06 02:19:30', 90, '', 'bg-pink'),
(6, 'ផ្សេងៗ', 'បាតុភូតធម្មជាតិ ឬគ្រោះមហន្តរាយទាំងឡាយដែលកើតឡើងដោយយថាហេតុ', '2024-08-06 02:20:16', '2024-08-06 02:20:16', 3, '', 'bg-orange');

-- --------------------------------------------------------

--
-- Table structure for table `login_traces`
--

DROP TABLE IF EXISTS `login_traces`;
CREATE TABLE IF NOT EXISTS `login_traces` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `login_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_traces`
--

INSERT INTO `login_traces` (`id`, `user_id`, `login_time`, `ip_address`) VALUES
(1, 22, '2024-08-01 11:31:27', '::1'),
(2, 12, '2024-08-01 11:31:52', '::1'),
(3, 13, '2024-08-01 11:35:49', '::1'),
(4, 18, '2024-08-01 12:24:05', '::1'),
(5, 19, '2024-08-01 12:25:13', '::1'),
(6, 11, '2024-08-02 08:51:01', '::1'),
(7, 11, '2024-08-02 16:21:32', '::1'),
(8, 11, '2024-08-02 16:21:50', '::1'),
(9, 11, '2024-08-02 16:22:40', '::1'),
(10, 11, '2024-08-02 16:23:08', '::1'),
(11, 11, '2024-08-02 16:24:44', '::1'),
(12, 11, '2024-08-02 16:25:30', '::1'),
(13, 11, '2024-08-02 16:26:15', '::1'),
(14, 11, '2024-08-02 16:30:33', '::1'),
(15, 11, '2024-08-02 16:31:08', '::1'),
(16, 11, '2024-08-02 16:45:23', '::1'),
(17, 11, '2024-08-06 08:24:20', '::1'),
(18, 11, '2024-08-06 09:00:51', '::1'),
(19, 11, '2024-08-06 09:11:27', '::1'),
(20, 11, '2024-08-06 09:12:32', '::1'),
(21, 3, '2024-08-06 09:14:15', '::1'),
(22, 11, '2024-08-06 09:20:45', '::1'),
(23, 12, '2024-08-06 09:25:56', '::1'),
(24, 16, '2024-08-06 09:26:47', '::1'),
(25, 13, '2024-08-06 09:31:23', '::1'),
(26, 18, '2024-08-06 09:33:02', '::1'),
(27, 19, '2024-08-06 09:33:55', '::1'),
(28, 11, '2024-08-06 09:34:43', '::1'),
(29, 16, '2024-08-06 09:35:08', '::1'),
(30, 20, '2024-08-06 09:35:58', '::1'),
(31, 11, '2024-08-06 09:37:04', '::1'),
(32, 3, '2024-08-06 13:40:28', '::1'),
(33, 11, '2024-08-06 14:58:32', '::1'),
(34, 3, '2024-08-06 15:20:16', '::1'),
(35, 21, '2024-08-06 15:30:43', '::1'),
(36, 11, '2024-08-06 15:34:18', '::1'),
(37, 12, '2024-08-06 15:35:54', '::1'),
(38, 21, '2024-08-06 15:40:43', '::1'),
(39, 11, '2024-08-06 15:43:25', '::1'),
(40, 11, '2024-08-06 15:49:28', '::1'),
(41, 16, '2024-08-06 16:32:46', '::1'),
(42, 11, '2024-08-06 16:35:14', '::1'),
(43, 12, '2024-08-06 16:36:16', '::1'),
(44, 13, '2024-08-06 16:37:24', '::1'),
(45, 18, '2024-08-06 16:38:01', '::1'),
(46, 19, '2024-08-06 16:38:28', '::1'),
(47, 11, '2024-08-06 16:38:56', '::1'),
(48, 11, '2024-08-07 14:49:44', '::1'),
(49, 3, '2024-08-07 15:05:06', '::1'),
(50, 3, '2024-08-07 15:05:24', '::1'),
(51, 3, '2024-08-07 15:06:40', '::1'),
(52, 3, '2024-08-07 15:06:55', '::1'),
(53, 11, '2024-08-07 15:32:07', '::1'),
(54, 11, '2024-08-07 15:48:44', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `missions`
--

DROP TABLE IF EXISTS `missions`;
CREATE TABLE IF NOT EXISTS `missions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `num_date` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `request_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('unread','read') NOT NULL DEFAULT 'unread',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `request_id`, `receiver_id`, `user_id`, `message`, `created_at`, `status`) VALUES
(1, 1, 12, 11, 'លោក ជា ច័ន្ទបូរី បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 04:26:07', 'unread'),
(2, 2, 12, 11, 'លោក ជា ច័ន្ទបូរី បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 04:30:12', 'unread'),
(3, 4, 12, 11, 'លោក ជា ច័ន្ទបូរី បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 07:08:05', 'unread'),
(4, 5, 12, 11, 'លោក ជា ច័ន្ទបូរី បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 07:22:04', 'unread'),
(5, 6, 12, 11, 'លោក ជា ច័ន្ទបូរី បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 02:24:59', 'unread'),
(6, 7, 12, 16, 'លោកស្រី ស៊ាប ម៉ីជីង បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 02:27:29', 'unread'),
(7, 6, 11, 12, 'លោក ចិន​ វាសនា បាន Approved ច្បាប់ឈប់សម្រាក។', '2024-08-06 02:29:06', 'unread'),
(8, 7, 16, 12, 'លោក ចិន​ វាសនា បាន Rejected ច្បាប់ឈប់សម្រាក។', '2024-08-06 02:29:42', 'unread'),
(9, 8, 12, 12, 'លោក ចិន​ វាសនា បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 02:30:41', 'unread'),
(10, 9, 12, 11, 'លោក ជា ច័ន្ទបូរី បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 09:35:55', 'unread'),
(11, 7, 16, 12, 'លោក ចិន​ វាសនា បាន Approved ច្បាប់ឈប់សម្រាក។', '2024-08-06 09:36:37', 'unread'),
(12, 9, 11, 12, 'លោក ចិន​ វាសនា បាន Approved ច្បាប់ឈប់សម្រាក។', '2024-08-06 09:36:52', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

DROP TABLE IF EXISTS `offices`;
CREATE TABLE IF NOT EXISTS `offices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `department_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `hoffice_id` int DEFAULT NULL,
  `doffice_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `offices`
--

INSERT INTO `offices` (`id`, `name`, `department_id`, `hoffice_id`, `doffice_id`, `created_at`, `updated_at`) VALUES
(8, 'ការិយាល័យរដ្ឋបាល និងហិរញ្ញវត្ថុ', '3', 16, 21, '2024-07-31 01:51:26', '2024-07-31 02:20:44'),
(2, 'ការិយាល័យគ្រប់គ្រងព័ត៌មានវិទ្យា', '3', 13, 12, '2024-07-10 01:37:40', '2024-07-15 22:02:09'),
(3, 'ការិយាល័យផែនការ និងបណ្តុះបណ្តាល', '3', 16, 21, '2024-07-10 01:38:06', '2024-07-31 02:02:10'),
(4, 'ការិយាល័យសវនកម្មទី១', '2', NULL, NULL, '2024-07-10 01:38:22', '2024-07-10 01:38:22'),
(5, 'ការិយាល័យសវនកម្មទី២', '2', NULL, NULL, '2024-07-10 01:38:35', '2024-07-10 01:38:35'),
(6, 'ការិយាល័យសវនកម្មទី៣', '1', NULL, NULL, '2024-07-10 01:38:51', '2024-07-10 01:38:51'),
(7, 'ការិយាល័យសវនកម្មទី៤', '1', NULL, NULL, '2024-07-10 01:39:04', '2024-07-10 01:39:04');

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
CREATE TABLE IF NOT EXISTS `positions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `department_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `department_id` (`department_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`id`, `name`, `color`, `department_id`, `created_at`, `updated_at`) VALUES
(1, 'ប្រធានអង្គភាព', 'bg-blue', 0, '2024-07-10 08:02:27', '2024-07-10 08:02:27'),
(2, 'អនុប្រធានអង្គភាព', 'bg-indigo', 0, '2024-07-10 08:02:55', '2024-07-11 09:00:02'),
(3, 'ប្រធាននាយកដ្ឋាន', 'bg-indigo', 0, '2024-07-10 08:03:06', '2024-07-10 08:03:06'),
(4, 'អនុប្រធាននាយកដ្ឋាន', 'bg-purple', 0, '2024-07-10 08:03:26', '2024-07-10 08:03:26'),
(5, 'ប្រធានការិយាល័យ', 'bg-pink', 0, '2024-07-10 08:03:45', '2024-07-10 08:03:45'),
(6, 'អនុប្រធានការិយាល័យ', 'bg-red', 0, '2024-07-10 08:03:56', '2024-07-10 08:03:56'),
(7, 'មន្ត្រីលក្ខខន្តិកៈ', 'bg-orange', 0, '2024-07-10 08:04:17', '2024-07-10 08:04:17'),
(8, 'ភ្នាក់ងាររដ្ឋបាល', 'bg-yellow', 0, '2024-07-10 08:04:34', '2024-07-10 08:04:34');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `description`) VALUES
(1, 'Admin', 'Administrator with full access'),
(15, 'Head Of Department', 'Department manager with higher privileges'),
(4, 'Deputy Head Of Department', 'Department manager with higher privileges'),
(16, 'Deputy Of Unit 1', 'Unit manager with higher privileges'),
(12, 'User', 'Regular User'),
(13, 'Deputy Head Of Office', 'DHO'),
(14, 'Head Of Office', 'HO'),
(17, 'Deputy Of Unit 2', 'Unit manager with higher privileges');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `khmer_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `english_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `username` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `address` text,
  `phone_number` varchar(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('ប្រុស','ស្រី') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `office_id` int DEFAULT NULL,
  `position_id` int DEFAULT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `khmer_name`, `english_name`, `username`, `password_hash`, `remember_token`, `email_verified_at`, `profile_picture`, `address`, `phone_number`, `date_of_birth`, `gender`, `department_id`, `office_id`, `position_id`, `role`, `status`, `created_at`) VALUES
(3, 'admin@gmail.com', 'Admin', 'Admin', 'Admin', '$2y$10$E5YJHBD2C7/wMZ/2Fgthyeyb91ATxDh0vooVOzbpL.8ZyXbLOtEyG', NULL, NULL, 'public/uploads/profiles/default_image.svg', '', '', '1899-12-01', 'ប្រុស', 1, 1, 4, 'Admin', 'Active', '2024-07-08 09:28:53'),
(16, 'seapmeiching@gmail.com', 'លោកស្រី ស៊ាប ម៉ីជីង', 'Ms.Seab Meiching', 'meiching', '$2y$10$.lFaPaKg9aGgF8x9PDHfC.OyijFez8/YPc9KRvwTRDbG5apvGrFP6', NULL, NULL, 'public/uploads/profiles/default_image.svg', 'pnhom penh', '(+855) 098-765-432-1', '2024-07-21', 'ស្រី', 3, 2, 7, 'User', 'Active', '2024-07-21 13:18:19'),
(9, 'pothchamreun@gmail.com', 'ពុធ ចំរើន', 'POTH CHAMREUN', 'Poth_Chamreun', '$2y$10$aN5WdocOADuoWBfT10pys.1MOMNI382nnJCBKolKD2TMQFApoOr8i', NULL, NULL, 'public/uploads/profiles/669dfe0666cc7_441713033_1368093670535668_9037546287717202544_n.jpg', 'jhgfdasf', '(+855) 098-765-432-1', '2020-06-20', 'ប្រុស', 3, 2, 7, 'User', 'Active', '2024-07-11 08:09:08'),
(12, 'chamreunn6@gmail.com', 'លោក ចិន​ វាសនា', 'Mr.Chen Veasna', 'chenveasna', '$2y$10$C1B4FrrbbZQWb0fbeoTc4.pszRuW9/iAr/i179BTWbuasP8P7iEtG', NULL, NULL, 'public/uploads/profiles/66b1dec38af4f_IMG_4890.JPG', 'Phnom Penh', '779083447', '2024-07-15', 'ប្រុស', 3, 2, 6, 'Deputy Head Of Office', 'Active', '2024-07-15 08:50:18'),
(11, 'boreychea25@gmail.com', 'លោក ជា ច័ន្ទបូរី', 'Mr.Chea Chanborey', 'Chea_Chanborey', '$2y$10$xn7OCNs97fTdqGyv.0x08eHgspFl/7FLHZ4.KaYD6OhWB5v7jWene', NULL, NULL, 'public/uploads/profiles/IMG_6484 (2).JPG', 'Phnom Penh', '(+855) 098-765-432-1', '2024-07-10', 'ប្រុស', 3, 2, 7, 'User', 'Active', '2024-07-12 07:07:54'),
(13, 'puthchamreun@gmail.com', 'លោក ជា​​ សេរីបណ្ឌិត', 'Mr.Chea Sereibondeth', 'sereibondeth', '$2y$10$z1ROZgCchvbgZ5TLhaifHue/38.UuiKXxDl0i30D1D0qcz1CRmvYy', NULL, NULL, 'public/uploads/profiles/default_image.svg', 'Phnom Penh', '(+855) 098-765-432-1', '2024-07-15', 'ប្រុស', 3, 2, 5, 'Head Of Office', 'Active', '2024-07-15 08:51:37'),
(18, 'thavkimrong@gmail.com', 'លោក​ ថៅ គីមរ៉ុង', 'Mr.Thav Kimrong', 'kimrong', '$2y$10$F11Acufy7snQ8DvuwHr6Y.I9SwnU/d/Py6DANH9iHALX2MM5eU9fe', NULL, NULL, 'public/uploads/profiles/default_image.svg', 'phnom penh', '(+855) 098-765-432-1', '2024-07-21', 'ប្រុស', 3, 0, 4, 'Deputy Head Of Department', 'Active', '2024-07-21 14:07:24'),
(22, 'puthhchamreun@gmail.com', 'កញ្ញ ធាង សុធារ៉ា', 'Ms.Theang Sotheara', 'theara', '$2y$10$CyQMRl5X4z2.6ufAgKGvjuN9nolBpbp28fSxIXR1Pw8kTOmz7zf1y', NULL, NULL, 'public/uploads/profiles/default_image.svg', 'phnom penh', '(+855) 098-765-432-1', '2024-07-31', 'ស្រី', 3, 8, 7, 'User', 'Active', '2024-07-31 08:44:25'),
(19, 'nuonsamratana@gmail.com', 'លោក​ នួន សំរតនា', 'Mr. Nuon Samratana', 'samratna', '$2y$10$dF7WWYsgdOv2dXUO.GJTj.uU.qsQjgd6l0MT.aeZGp5yCiqL5N4Hi', NULL, NULL, 'public/uploads/profiles/669d214946dbc_441713033_1368093670535668_9037546287717202544_n.jpg', 'phnom penh', '(+855) 098-765-432-1', '2024-07-22', 'ប្រុស', 3, 0, 3, 'Head Of Department', 'Active', '2024-07-21 14:53:49'),
(20, 'limchanna@gmail.com', 'លោក លឹម ចាន់ណា', 'Mr. Lim Channa', 'limchanna', '$2y$10$fONFqkNecuI766VU70PWa.s3QgQ0VXauM8FMWaIIygY1aIxzp76VK', NULL, NULL, 'public/uploads/profiles/default_image.svg', 'pnhom penh', '(+855) 098-765-432-1', '2024-07-21', 'ប្រុស', 4, 0, 2, 'Deputy Of Unit 1', 'Active', '2024-07-21 15:42:43'),
(21, 'pchamreunpoth@gmail.com', 'កញ្ញា សេង ចន្ធី', 'Ms.Seng Chanthy', 'chanthy', '$2y$10$n9AySj9/mRlVBhiYqlvoGOVFxCzVyFIZnWbtk5uhFflY6QXoYJwrq', NULL, NULL, 'public/uploads/profiles/66b1df0962108_photo_2023-05-11_15-11-49.jpg', '', '(+855) 098-765-432-1', '2024-07-14', 'ស្រី', 3, 8, 6, 'Deputy Head Of Office', 'Active', '2024-07-22 03:24:51');

-- --------------------------------------------------------

--
-- Table structure for table `user_activity_log`
--

DROP TABLE IF EXISTS `user_activity_log`;
CREATE TABLE IF NOT EXISTS `user_activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_activity_log`
--

INSERT INTO `user_activity_log` (`id`, `user_id`, `action`, `timestamp`, `details`, `ip_address`) VALUES
(1, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 11:26:02', '::1', '::1'),
(2, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 11:30:09', '::1', '::1'),
(3, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 11:31:04', '::1', '::1'),
(4, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 14:07:59', '::1', '::1'),
(5, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-02 14:21:53', '::1', '::1'),
(6, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 09:24:55', '::1', '::1'),
(7, 16, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 09:27:26', '::1', '::1'),
(8, 12, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 09:30:38', '::1', '::1'),
(9, 11, 'បានស្នើសុំច្បាប់ឈប់សម្រាក។', '2024-08-06 16:35:48', '::1', '::1');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
