-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2025 at 12:46 PM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `birth_death_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `status_logs`
--

CREATE TABLE `status_logs` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `old_status` varchar(50) NOT NULL,
  `new_status` varchar(50) NOT NULL,
  `remark` text DEFAULT NULL,
  `application_type` enum('birth','death') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbladmin`
--

CREATE TABLE `tbladmin` (
  `id` int(11) NOT NULL,
  `admin_name` varchar(120) NOT NULL,
  `username` varchar(50) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `email` varchar(200) NOT NULL,
  `password` varchar(200) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbladmin`
--

INSERT INTO `tbladmin` (`id`, `admin_name`, `username`, `mobile_number`, `email`, `password`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', '1234567890', 'admin@admin.com', 'admin123', 'active', '2025-01-15 19:37:36', '2024-12-28 09:18:49', '2025-01-15 19:37:36');

-- --------------------------------------------------------

--
-- Table structure for table `tblbirthapplications`
--

CREATE TABLE `tblbirthapplications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registration_no` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `father_brn` varchar(100) NOT NULL,
  `father_nid` varchar(50) NOT NULL,
  `father_occupation` varchar(100) NOT NULL,
  `mother_name` varchar(100) NOT NULL,
  `mother_brn` varchar(100) NOT NULL,
  `mother_nid` varchar(50) NOT NULL,
  `mother_occupation` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `place_of_birth` varchar(100) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `permanent_address` text NOT NULL,
  `hospital_paper` varchar(255) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `nationality` varchar(50) NOT NULL,
  `blood_group` varchar(5) NOT NULL,
  `marital_status` varchar(20) NOT NULL,
  `order_of_child` int(11) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `division` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `upazila` varchar(50) NOT NULL,
  `pouroshova` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tbldeathapplications`
--

CREATE TABLE `tbldeathapplications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registration_no` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `father_brn` varchar(100) NOT NULL,
  `father_nid` varchar(50) NOT NULL,
  `father_occupation` varchar(100) NOT NULL,
  `mother_name` varchar(100) NOT NULL,
  `mother_brn` varchar(100) NOT NULL,
  `mother_nid` varchar(50) NOT NULL,
  `mother_occupation` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `date_of_death` date NOT NULL,
  `place_of_death` varchar(100) NOT NULL,
  `cause_of_death` text NOT NULL,
  `permanent_address` text NOT NULL,
  `nid_number` varchar(50) NOT NULL,
  `nid_document` varchar(255) NOT NULL,
  `hospital_paper` varchar(255) NOT NULL,
  `application_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `nationality` varchar(50) NOT NULL,
  `blood_group` varchar(5) NOT NULL,
  `marital_status` varchar(20) NOT NULL,
  `age_at_death` int(11) NOT NULL,
  `gender` varchar(10) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `division` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `upazila` varchar(50) NOT NULL,
  `pouroshova` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------

--
-- Table structure for table `tblfaceverification`
--

CREATE TABLE `tblfaceverification` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `face_image` longtext NOT NULL,
  `face_embedding` longtext DEFAULT NULL,
  `verification_date` datetime DEFAULT current_timestamp(),
  `verification_status` varchar(20) DEFAULT 'pending',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `tblfaceverification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE tblverification_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    verification_type VARCHAR(50) NOT NULL,
    status VARCHAR(50) NOT NULL,
    distance FLOAT,
    verification_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES tbluser(ID)
);


-- --------------------------------------------------------

--
-- Table structure for table `tblphoneverification`
--

CREATE TABLE `tblphoneverification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `otp` varchar(6) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `verification_attempts` int(11) DEFAULT 0,
  `last_otp_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tblphoneverification`
--

INSERT INTO `tblphoneverification` (`id`, `user_id`, `phone_number`, `otp`, `verified`, `verification_attempts`, `last_otp_time`) VALUES
(2, 10, '+8801623744577', '860032', 1, 1, NULL),
(3, 11, '+8801623744578', '285382', 1, 1, NULL),
(4, 12, '+8801950368488', '195122', 1, 1, NULL),
(6, 14, '+8801623744574', '971952', 1, 1, NULL),
(0, 30, '+8802222222222', '957745', 1, 1, NULL),
(0, 31, '+8804444444444', '104236', 1, 1, NULL),
(0, 32, '+8804444444445', '863510', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbluser`
--

CREATE TABLE `tbluser` (
  `ID` int(10) NOT NULL,
  `FirstName` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `LastName` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `MobileNumber` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Address` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `Email` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `token_expiry` datetime DEFAULT NULL,
  `BloodGroup` varchar(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MaritalStatus` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `BNROption` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UserBRN` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `FatherBRN` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `MotherBRN` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive','suspended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `last_login` timestamp NULL DEFAULT NULL,
  `account_locked` tinyint(1) DEFAULT 0,
  `lock_expires_at` timestamp NULL DEFAULT NULL,
  `email_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `face_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tbluser`
--

INSERT INTO `tbluser` (`ID`, `FirstName`, `LastName`, `MobileNumber`, `Address`, `Email`, `Password`, `remember_token`, `RegDate`, `reset_token`, `token_expiry`, `BloodGroup`, `MaritalStatus`, `BNROption`, `UserBRN`, `FatherBRN`, `MotherBRN`, `status`, `last_login`, `account_locked`, `lock_expires_at`, `email_verified`, `verification_token`, `face_image`) VALUES
(1, 'Test', 'User', '1234567890', '', 'test_1735384960@example.com', 'Test@123', NULL, '2024-12-28 11:22:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(6, 'Mohit', 'Jahan', '1950368487', '', 'thistestmail000@gmail.com', 'Password123@', NULL, '2024-12-28 11:31:22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(9, 'Mohit', 'Shuvo', '1568057569', '', 'thistestmail@gmail.com', 'Password123@', NULL, '2024-12-28 13:08:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(10, 'Mohit', 'Jahan', '1623744577', '', 'thistest@gmail.com', 'Password123@', NULL, '2024-12-28 13:29:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(11, 'Rizwan', 'Islam', '1623744578', '', 'this@gmail.com', 'Password123@', NULL, '2024-12-28 15:53:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(12, 'ho', 'ko', '1950368488', '', 't@gmail.com', 'Test@123', NULL, '2025-01-07 17:15:11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(13, 'Mohit', 'Jahan', '1950368489', '', 'thiss@gmail.com', 'Password123@', NULL, '2025-01-08 07:58:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(14, 'MD. Mohit', 'Jahan Shuvo', '1950368486', 'Dhaka Mirpur 10', 'toto@gmail.com', 'Password123#', NULL, '2025-01-08 08:04:17', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(15, 'Manna', 'Khatun', '1745667788', 'Doulodia', 'mc@gmail.com', 'Password123#', NULL, '2025-01-11 19:44:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(16, 'Razon', 'Razzo', '1950368477', '', 'razon@gmail.com', 'Password123@', NULL, '2025-01-13 17:03:56', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(17, 'Rizwan', 'Khatun', '1950368479', '', 'to@gmail.com', 'Password123#', NULL, '2025-01-13 19:34:04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(18, 'MD', 'MOHIT JAHAN SHUVO', '1950368411', '', 'mohitjahan@gmail.com', 'Password123#', NULL, '2025-01-15 09:54:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(19, 'To', 'Hu', '1623744555', '', 'th@gmail.com', 'Password123#', NULL, '2025-01-15 09:56:20', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(20, 'MD', 'MOHIT JAHAN SHUVO', '1951368487', '', 'mohitjaha@gmail.com', 'Password123#', NULL, '2025-01-15 10:05:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(21, 'To', 'Hu', '1950368422', '', 'tha@gmail.com', 'Password123#', NULL, '2025-01-15 10:07:02', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(22, 'Rizwan', 'Islam', '1568057669', '', 'riz@gmail.com', 'Password123#', NULL, '2025-01-15 10:19:09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(23, 'chu', 'chu', '1950468477', '', 'chu@gmail.com', 'Password123#', NULL, '2025-01-15 11:51:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(24, 'vv', 'ff', '1951468477', '', 'chuu@gmail.com', 'Password123#', NULL, '2025-01-15 11:56:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(25, 'MD', 'MOHIT JAHAN SHUVO', '1750368487', '', 'mohitjah@gmail.com', 'Password123#', NULL, '2025-01-15 12:00:19', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(26, 'Mohit', 'Kk', '1950378477', '', 'chuuu@gmail.com', 'Password123#', NULL, '2025-01-15 12:08:34', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(27, 'rr', 'tt', '1951378477', '', 'rr@gmail.com', 'Password123#', NULL, '2025-01-15 12:15:18', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(28, 'Rizwan', 'Khatun', '1952378477', '', 'r@gmail.com', 'Password123#', NULL, '2025-01-15 12:21:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(29, 'Vv', 'Cc', '1111111111', '', 'visici4305@cumzle.com', 'Password123#', NULL, '2025-01-15 12:28:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL),
(30, 'Rizwan', 'Khatun', '2222222222', '', 'rrr@gmail.com', 'Password123#', NULL, '2025-01-15 12:44:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', '2025-01-15 13:00:22', 0, NULL, 0, NULL, NULL),
(31, 'tt', 'yy', '4444444444', '', 'ty@gmail.com', 'Password123#', NULL, '2025-01-15 14:06:38', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'active', NULL, 0, NULL, 0, NULL, NULL),
(32, 'Yy', 'Uu', '4444444445', '', 'y@gmail.com', 'Password123#', NULL, '2025-01-15 15:58:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', NULL, 0, NULL, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbluserdetails`
--

CREATE TABLE `tbluserdetails` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `father_name` varchar(100) NOT NULL,
  `father_brn` varchar(50) NOT NULL,
  `mother_name` varchar(100) NOT NULL,
  `mother_brn` varchar(50) NOT NULL,
  `present_address` text NOT NULL,
  `permanent_address` text NOT NULL,
  `nationality` varchar(50) NOT NULL,
  `blood_group` varchar(5) NOT NULL,
  `marital_status` varchar(20) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `order_of_child` int(11) NOT NULL,
  `occupation` varchar(100) NOT NULL,
  `division` varchar(50) NOT NULL,
  `district` varchar(50) NOT NULL,
  `upazila` varchar(50) NOT NULL,
  `pouroshova` varchar(100) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbluserdetails`
--

INSERT INTO `tbluserdetails` (`id`, `user_id`, `father_name`, `father_brn`, `mother_name`, `mother_brn`, `present_address`, `permanent_address`, `nationality`, `blood_group`, `marital_status`, `sex`, `order_of_child`, `occupation`, `division`, `district`, `upazila`, `pouroshova`, `registration_date`) VALUES
(2, 10, 'Mr. X', '11111111111111111', 'Ms. Y', '11111111111111112', 'jhsj', ',mjhzsdgf', 'Bangladeshi', 'B+', 'Single', 'Male', 3, 'Student', '', '', '', '', '2024-12-28 19:51:45'),
(3, 11, 'Mr. X', '11111111111111113', 'Ms. Y', '11111111111111114', 'fh', 'fh', 'Bangladeshi', 'AB+', 'Divorced', 'Female', 1, 'Designer', '', '', '', '', '2024-12-28 21:55:24'),
(4, 12, 'jh', '77777777777777777', 'vgh', '77777777777777778', 'bjn', 'jh', 'Bangladeshi', 'A-', 'Single', 'Male', 2, 'Student', '', '', '', '', '2025-01-07 23:16:53'),
(5, 13, 'Mr. X', '22222222222222222', 'Ms. Y', '22222222222222223', 'jhsj', ',mjhzsdgf', 'Bangladeshi', 'A-', 'Married', 'Male', 4, 'Student', '', '', '', '', '2025-01-08 14:00:30'),
(6, 14, 'Mr. X', '22222222222222222', 'Ms. Y', '22222222222222223', 'Dhaka Mirpur 10', 'kushtia', 'Bangladeshi', 'A+', 'Married', 'Male', 5, 'Student', '', '', '', '', '2025-01-08 14:05:00'),
(7, 14, '', '', '', '', 'Dhaka Mirpur 10', 'kushtia', 'Bangladeshi', 'A+', '', '', 0, '', '', '', '', '', '2025-01-08 22:05:42'),
(8, 14, '', '', '', '', 'Dhaka Mirpur 10', 'kushtia', 'Bangladeshi', 'A+', '', '', 0, '', '', '', '', '', '2025-01-08 22:06:04'),
(9, 14, '', '', '', '', 'Dhaka Mirpur 10', 'kushtia', 'Bangladeshi', 'A+', '', '', 0, '', '', '', '', '', '2025-01-08 22:06:44'),
(10, 15, 'Pp', '22222222222222224', 'Po', '22222222222222225', 'Doulodia', 'j', 'Bangladeshi', 'B+', 'Divorced', 'Male', 1, 'Student', '', '', '', '', '2025-01-12 01:49:52'),
(11, 18, 'Sh', '77777777777777777', 'Sa', '77777777777777778', 'Dhk', 'Kst', 'Bangladeshi', 'A+', 'Single', 'Male', 4, 'Student', 'Khulna', 'Kushtia', 'Kushtia Sadar', 'Kushtia Pouroshova', '2025-01-15 15:57:43'),
(12, 19, 'h', '22222222222222222', 'a', '22222222222222223', 'fh', 'fh', 'Bangladeshi', 'A+', 'Single', 'Female', 1, 'Student', 'Khulna', 'Kushtia', 'Kushtia Sadar', 'Kushtia Pouroshova', '2025-01-15 16:00:25'),
(13, 21, 'h', '22222222222222222', 'd', '22222222222222223', 'fh', 'fh', 'Bangladeshi', 'A+', 'Single', 'Female', 1, 'Student', 'Khulna', 'Kushtia', 'Kushtia Sadar', 'Kushtia Pouroshova', '2025-01-15 16:07:50'),
(14, 20, 'Sh', '77777777777777777', 'Sa', '77777777777777778', 'Dhk', 'Kst', 'Bangladeshi', 'A+', 'Single', 'Male', 4, 'Student', 'Khulna', 'Kushtia', 'Kushtia Sadar', 'Kushtia Pouroshova', '2025-01-15 16:12:08'),
(15, 22, 'Mr. X', '22222222222222222', 'Ms. Y', '22222222222222223', 'jhsj', ',mjhzsdgf', 'Bangladeshi', 'AB+', 'Divorced', 'Female', 1, 'Student', 'Chittagong', 'Comilla', 'Comilla Sadar', 'Comilla City Corporation', '2025-01-15 16:20:03'),
(16, 23, 'Mr. X', '22222222222222222', 'Ms. Y', '22222222222222223', 'fh', ',mjhzsdgf', 'Bangladeshi', 'A-', 'Single', 'Male', 4, 'Student', 'Barisal', 'Bhola', 'Manpura', 'Manpura', '2025-01-15 17:51:57'),
(17, 24, 'h', '22222222222222222', 'd', '22222222222222223', 'jhsj', 'fh', 'Bangladeshi', 'O+', 'Widowed', 'Female', 4, 'Student', 'Chittagong', 'Cox\'s Bazar', 'Cox\'s Bazar Sadar', 'Cox\'s Bazar Pouroshova', '2025-01-15 17:56:42'),
(18, 25, 'Sh', '77777777777777777', 'Sa', '77777777777777778', 'Dhk', 'Kst', 'Bangladeshi', 'B-', 'Divorced', 'Male', 4, 'Student', 'Khulna', 'Kushtia', 'Kushtia Sadar', 'Kushtia Pouroshova', '2025-01-15 18:00:56'),
(19, 26, 'h', '22222222222222222', 'd', '11111111111111112', 'fh', ',mjhzsdgf', 'Bangladeshi', 'B-', 'Single', 'Male', 4, 'Student', 'Chittagong', 'Rangamati', 'Kaptai', 'Wagga', '2025-01-15 18:09:14'),
(20, 27, 'h', '22222222222222222', 'g', '22222222222222223', 'fh', 'fh', 'Bangladeshi', 'AB-', 'Married', 'Female', 5, 'Student', 'Rajshahi', 'Chapainawabganj', 'Nachole', 'Fatehpur', '2025-01-15 18:16:13'),
(21, 28, 'h', '22222222222222222', 'g', '22222222222222223', 'jhsj', 'fh', 'Bangladeshi', 'AB+', 'Married', 'Male', 2, 'Student', 'Khulna', 'Satkhira', 'Kalaroa', 'Kalaroa Pouroshova', '2025-01-15 18:22:16'),
(22, 29, 'jh', '77777777777777777', 'vgh', '77777777777777778', 'bjn', 'jh', 'Bangladeshi', 'AB-', 'Divorced', 'Male', 6, 'Student', 'Sylhet', 'Sunamganj', 'Jagannathpur', 'Raniganj', '2025-01-15 18:29:38'),
(23, 30, 'h', '22222222222222222', 'g', '22222222222222223', 'fh', 'fh', 'Bangladeshi', 'AB+', 'Divorced', 'Other', 5, 'Designer', 'Rajshahi', 'Chapainawabganj', 'Shibganj', 'Kansat', '2025-01-15 18:45:07'),
(24, 31, 'h', '22222222222222222', 'd', '22222222222222223', 'fh', 'fh', 'Bangladeshi', 'A+', 'Married', 'Male', 5, 'Student', 'Chittagong', 'Rangamati', 'Kawkhali', 'Betbunia', '2025-01-15 20:07:17'),
(25, 32, 'Sh', '77777777777777777', 'Sa', '77777777777777778', 'Dhk', 'Kst', 'Bangladeshi', 'A+', 'Single', 'Male', 3, 'Student', 'Khulna', 'Kushtia', 'Bheramara', 'Mokarimpur', '2025-01-15 21:59:29');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_id` (`admin_id`);

--
-- Indexes for table `tbladmin`
--
ALTER TABLE `tbladmin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tblfaceverification`
--
ALTER TABLE `tblfaceverification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbluser`
--
ALTER TABLE `tbluser`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `MobileNumber` (`MobileNumber`);

--
-- Indexes for table `tbluserdetails`
--
ALTER TABLE `tbluserdetails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `status_logs`
--
ALTER TABLE `status_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbladmin`
--
ALTER TABLE `tbladmin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblfaceverification`
--
ALTER TABLE `tblfaceverification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbluser`
--
ALTER TABLE `tbluser`
  MODIFY `ID` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `tbluserdetails`
--
ALTER TABLE `tbluserdetails`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `status_logs`
--
ALTER TABLE `status_logs`
  ADD CONSTRAINT `status_logs_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `tbladmin` (`id`);

--
-- Constraints for table `tblfaceverification`
--
ALTER TABLE `tblfaceverification`
  ADD CONSTRAINT `tblfaceverification_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`ID`) ON DELETE CASCADE;

--
-- Constraints for table `tbluserdetails`
--
ALTER TABLE `tbluserdetails`
  ADD CONSTRAINT `tbluserdetails_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tbluser` (`ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
