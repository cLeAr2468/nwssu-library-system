-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2025 at 05:11 AM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `library-system`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(50) NOT NULL,
  `user_id` varchar(250) NOT NULL,
  `activity_type` varchar(250) NOT NULL,
  `activity_details` varchar(250) NOT NULL,
  `activity_date` varchar(250) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `user_id`, `activity_type`, `activity_details`, `activity_date`, `status`) VALUES
(1, '21-SJ00126', 'login', 'User logged in successfully', '2025-05-07 13:58:15', 'active'),
(2, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-07 13:58:18', 'active'),
(3, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-07 13:58:25', 'active'),
(4, '21-SJ00126', 'View Books', 'Domestic TOURISM', '2025-05-07 13:58:54', 'active'),
(5, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-07 13:59:04', 'active'),
(6, '21-SJ00126', 'reservation', 'Reserved book: Environmental Management', '2025-05-07 13:59:45', 'active'),
(7, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-07 13:59:50', 'active'),
(8, '21-SJ00126', 'View Books', 'Domestic TOURISM', '2025-05-07 13:59:53', 'active'),
(9, '21-SJ00126', 'logout', 'User signed out of the system', '2025-05-07 14:00:04', 'active'),
(10, '12345', 'login', 'User logged in successfully', '2025-05-07 14:00:51', 'active'),
(11, '12345', 'click', 'User accessed Book Catalog page', '2025-05-07 14:00:58', 'active'),
(12, '12345', 'View Books', 'MultiCultural management', '2025-05-07 14:01:02', 'active'),
(13, '12345', 'View Books', 'Domestic TOURISM', '2025-05-07 14:01:08', 'active'),
(14, '12345', 'View Books', 'MultiCultural management', '2025-05-07 14:01:14', 'active'),
(15, '12345', 'reservation', 'Reserved book: MultiCultural management', '2025-05-07 14:01:18', 'active'),
(16, '12345', 'click', 'User accessed Book Catalog page', '2025-05-07 14:27:46', 'active'),
(17, '12345', 'View Books', 'Domestic TOURISM', '2025-05-07 14:28:53', 'active'),
(18, '12345', 'View Books', 'Environmental Management', '2025-05-07 14:29:05', 'active'),
(19, '21-SJ00126', 'login', 'User logged in successfully', '2025-05-08 13:46:22', 'active'),
(20, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-08 06:59:45', 'active'),
(21, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-08 07:29:44', 'active'),
(22, '21-SJ00126', 'click', 'User accessed top_collection page', '2025-05-08 07:29:45', 'active'),
(23, '21-SJ00126', 'click', 'User accessed new_collection page', '2025-05-08 07:29:47', 'active'),
(24, '21-SJ00126', 'click', 'User accessed top_collection page', '2025-05-08 07:29:54', 'active'),
(25, '21-SJ00126', 'View Books', 'the science', '2025-05-08 07:29:56', 'active'),
(26, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-08 07:30:51', 'active'),
(27, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-08 07:55:11', 'active'),
(28, '21-SJ00126', 'View Books', 'Domestic TOURISM', '2025-05-08 07:55:17', 'active'),
(29, '21-SJ00126', 'View Books', 'the science', '2025-05-08 07:55:22', 'active'),
(30, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-08 07:55:29', 'active'),
(31, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-08 07:55:31', 'active'),
(32, '21-SJ00126', 'logout', 'User signed out of the system', '2025-05-08 07:55:48', 'active'),
(33, '21-SJ00307', 'login', 'User logged in successfully', '2025-05-08 07:55:55', 'active'),
(34, '21-SJ00307', 'click', 'User accessed Book Catalog page', '2025-05-08 07:55:58', 'active'),
(35, '21-SJ00307', 'View Books', 'MultiCultural management', '2025-05-08 07:55:59', 'active'),
(36, '21-SJ00307', 'reservation', 'Reserved book: MultiCultural management', '2025-05-08 07:56:02', 'active'),
(37, '21-SJ00307', 'click', 'User accessed Book Catalog page', '2025-05-08 08:04:29', 'active'),
(38, '21-SJ00307', 'logout', 'User signed out of the system', '2025-05-10 08:27:42', 'active'),
(39, '21-SJ00126', 'login', 'User logged in successfully', '2025-05-10 08:27:48', 'active'),
(40, '21-SJ00126', 'login', 'User logged in successfully', '2025-05-10 09:14:09', 'active'),
(41, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-10 09:14:11', 'active'),
(42, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-10 09:14:15', 'active'),
(43, '21-SJ00126', 'reservation', 'Reserved book: Environmental Management', '2025-05-10 09:14:18', 'active'),
(44, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-10 09:14:31', 'active'),
(45, '21-SJ00126', 'logout', 'User signed out of the system', '2025-05-11 09:14:48', 'active'),
(46, '21-SJ00126', 'login', 'User logged in successfully', '2025-05-11 09:25:34', 'active'),
(47, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 09:25:42', 'active'),
(48, '21-SJ00126', 'View Books', 'Pig Feeder', '2025-05-11 09:26:52', 'active'),
(49, '21-SJ00126', 'View Books', 'Ornamental Horticulture and Landscape Gardening', '2025-05-11 09:27:02', 'active'),
(50, '21-SJ00126', 'reservation', 'Reserved book: Ornamental Horticulture and Landscape Gardening', '2025-05-11 09:27:07', 'active'),
(51, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 09:31:04', 'active'),
(52, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 09:31:54', 'active'),
(53, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-13 09:54:18', 'active'),
(54, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 09:54:20', 'active'),
(55, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-13 09:55:08', 'active'),
(56, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 09:55:11', 'active'),
(57, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 09:59:24', 'active'),
(58, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-13 09:59:28', 'active'),
(59, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 09:59:57', 'active'),
(60, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-13 10:00:04', 'active'),
(61, '21-SJ00126', 'reservation', 'Reserved book: Environmental Management', '2025-05-13 10:00:09', 'active'),
(62, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 10:00:22', 'active'),
(63, '21-SJ00126', 'View Books', 'Ornamental Horticulture and Landscape Gardening', '2025-05-13 10:00:26', 'active'),
(64, '21-SJ00126', 'reservation', 'Reserved book: Ornamental Horticulture and Landscape Gardening', '2025-05-13 10:00:31', 'active'),
(65, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 10:00:38', 'active'),
(66, '21-SJ00126', 'View Books', 'Domestic TOURISM', '2025-05-13 10:00:42', 'active'),
(67, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-13 10:00:50', 'active'),
(68, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-13 10:00:53', 'active'),
(69, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-14 10:01:35', 'active'),
(70, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-14 10:01:38', 'active'),
(71, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-14 10:02:01', 'active'),
(72, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-14 10:02:07', 'active'),
(73, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-14 10:02:30', 'active'),
(74, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-14 10:07:39', 'active'),
(75, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-14 10:07:43', 'active'),
(76, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-14 10:07:49', 'active'),
(77, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-14 10:07:54', 'active'),
(78, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 10:08:36', 'active'),
(79, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:08:39', 'active'),
(80, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 10:11:06', 'active'),
(81, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:16:25', 'active'),
(82, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 10:16:30', 'active'),
(83, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:16:31', 'active'),
(84, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 10:16:38', 'active'),
(85, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:16:39', 'active'),
(86, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:20:06', 'active'),
(87, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 10:20:08', 'active'),
(88, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:20:09', 'active'),
(89, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 10:20:12', 'active'),
(90, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:20:13', 'active'),
(91, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-15 10:20:25', 'active'),
(92, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-15 10:20:29', 'active'),
(93, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-15 10:24:24', 'active'),
(94, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-15 10:27:31', 'active'),
(95, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 10:27:36', 'active'),
(96, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-16 10:27:58', 'active'),
(97, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-16 10:28:00', 'active'),
(98, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-16 10:28:06', 'active'),
(99, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-16 10:28:07', 'active'),
(100, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-16 10:28:13', 'active'),
(101, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-16 10:28:15', 'active'),
(102, '21-SJ00126', 'login', 'User logged in successfully', '2025-05-09 04:08:41', 'active'),
(103, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-09 04:10:22', 'active'),
(104, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-09 04:10:32', 'active'),
(105, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-09 04:10:37', 'active'),
(106, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 04:11:56', 'active'),
(107, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 04:12:11', 'active'),
(108, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 04:12:20', 'active'),
(109, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 04:12:22', 'active'),
(110, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 04:12:27', 'active'),
(111, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 04:12:29', 'active'),
(112, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 04:21:34', 'active'),
(113, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 04:21:36', 'active'),
(114, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-11 04:21:41', 'active'),
(115, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-11 04:21:45', 'active'),
(116, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 04:21:50', 'active'),
(117, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 04:21:52', 'active'),
(118, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-11 04:21:56', 'active'),
(119, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-11 04:21:58', 'active'),
(120, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-13 04:22:19', 'active'),
(121, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 04:22:21', 'active'),
(122, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-13 04:22:27', 'active'),
(123, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 04:22:56', 'active'),
(124, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-13 04:23:06', 'active'),
(125, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-13 04:23:10', 'active'),
(126, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 04:23:14', 'active'),
(127, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-13 04:23:31', 'active'),
(128, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-13 04:23:33', 'active'),
(129, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 04:24:07', 'active'),
(130, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-15 04:24:09', 'active'),
(131, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 04:24:11', 'active'),
(132, '21-SJ00126', 'View Books', 'Domestic TOURISM', '2025-05-15 04:28:25', 'active'),
(133, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-15 04:28:36', 'active'),
(134, '21-SJ00126', 'reservation', 'Reserved book: Environmental Management', '2025-05-15 04:28:40', 'active'),
(135, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-15 04:35:43', 'active'),
(136, '21-SJ00126', 'View Books', 'Ornamental Horticulture and Landscape Gardening', '2025-05-15 04:35:48', 'active'),
(137, '21-SJ00126', 'reservation', 'Reserved book: MultiCultural management', '2025-05-15 04:35:58', 'active'),
(138, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-17 04:48:41', 'active'),
(139, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-17 04:51:17', 'active'),
(140, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-17 04:51:22', 'active'),
(141, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-17 04:51:23', 'active'),
(142, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-17 04:51:25', 'active'),
(143, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-17 04:51:27', 'active'),
(144, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-17 04:51:29', 'active'),
(145, '21-SJ00126', 'reservation', 'Reserved book: Environmental Management', '2025-05-17 04:51:32', 'active'),
(146, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-19 04:52:39', 'active'),
(147, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-19 04:52:48', 'active'),
(148, '21-SJ00126', 'click', 'User accessed Home page', '2025-05-19 04:52:52', 'active'),
(149, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-19 04:52:53', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `id` int(50) NOT NULL,
  `title` varchar(250) NOT NULL,
  `message` varchar(5000) NOT NULL,
  `image` varchar(300) DEFAULT NULL,
  `date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`id`, `title`, `message`, `image`, `date`) VALUES
(1, 'Kupal', 'kupal kaba boss?', '../uploaded_file/bos.jpg', '2024-12-03 19:28:34'),
(2, 'Bossing', 'a', NULL, '2025-05-06 14:50:49');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(100) NOT NULL,
  `material_type` varchar(250) NOT NULL,
  `sub_type` varchar(250) NOT NULL,
  `category` varchar(250) NOT NULL,
  `title` varchar(500) NOT NULL,
  `author` varchar(500) NOT NULL,
  `publisher` varchar(500) NOT NULL,
  `status` varchar(100) NOT NULL,
  `subject` varchar(250) NOT NULL,
  `content` varchar(5000) NOT NULL,
  `summary` varchar(5000) NOT NULL,
  `issn` varchar(250) NOT NULL,
  `call_no` varchar(250) NOT NULL,
  `ISBN` varchar(250) NOT NULL,
  `copyright` varchar(250) NOT NULL,
  `page_number` int(250) NOT NULL,
  `edition` varchar(250) NOT NULL,
  `copies` int(250) NOT NULL,
  `date_acquired` varchar(250) NOT NULL,
  `books_image` varchar(400) DEFAULT NULL,
  `catalog_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `material_type`, `sub_type`, `category`, `title`, `author`, `publisher`, `status`, `subject`, `content`, `summary`, `issn`, `call_no`, `ISBN`, `copyright`, `page_number`, `edition`, `copies`, `date_acquired`, `books_image`, `catalog_date`) VALUES
(1, 'Book', 'Others', 'BSF', 'MultiCultural management', 'FARID ELASHMAWI et al', 'National Book Store', 'available', 'Programming', 'aaaaa', 'aaaaa', '00000', '658 E37', '9831363825', '000000', 234, 'N/A', 17, '2025-03-27', NULL, '2025-04-02'),
(2, 'Journal', 'Others', 'BSF', 'The Philippines Recommends for Watershed Management', 'Dr. Vicente P. Veracion et al', 'DOST', 'available', 'Animal Science, Agriculture', 'd', 'd', '1157787', '658 P364 1991', '9712001822', '00000', 88, 'N/A', 10, '2025-03-27', NULL, '2025-04-02'),
(3, 'Book', 'Others', 'BSF', 'Environmental Management', 'Dr. Swapan C Deb', 'JAICO BOOKS', 'available', 'Animal Science, Agriculture', 'ds', 'ds', '000000', '658.4 D299 2003', '8179921344', '000000', 243, 'N/A', 10, '2025-03-27', NULL, '2025-04-08'),
(4, 'Book', 'Others', 'BSF', 'TOURISM Planning and Development', 'Zenaida Lansangan Cruz, Ph.D.', 'National Book Store', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 O111 2011', '9710870424', '000000', 145, 'N/A', 1, '2025-03-27', NULL, '2025-04-02'),
(5, 'Book', 'Others', 'BSF', 'Ornamental Horticulture and Landscape Gardening', 'Dr. Mahesh Kumar', 'RANDOM PUBLICATIONS LLP', 'available', 'Animal Science, Agriculture', 'a', 'a', '00000', '712 K95 2022', '9789393884527', '000000', 306, 'N/A', 6, '2025-03-27', NULL, '2025-04-02'),
(10, 'E-Book', 'Others', 'BSF', 'Domestic TOURISM', 'Carlos M. Libosada, Jr.', 'ANVIL PIBLISHING, INC.', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 L11 2003', '9712713989', '000000', 214, 'N/A', 3, '2025-03-27', '../uploaded_file/bgss.png', '2025-05-07'),
(11, 'E-Book', 'Fiction', 'BSA-Animal Science', 'the science', 'Carlos M. Libosada, Jr.', 'ANVIL PIBLISHING, INC.', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 L11 2003', '0000', '00000', 276, 'N/A', 5, '2025-04-08', NULL, '2025-04-08'),
(13, 'Unpublished Material', 'Thesis', 'BSIT', 'Pig Feeder', 'Mark Francis', 'Molleda Fam', 'available', 'Programming', 'de', 'da', '11111', '11111', '111111', '2025', 276, 'N/A', 1, '2025-04-25', '67f4f161f20f5_2.png', '2025-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `borrowed_books`
--

CREATE TABLE `borrowed_books` (
  `user_id` varchar(50) NOT NULL,
  `book_id` varchar(50) NOT NULL,
  `copies` int(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `return_sched` datetime(1) NOT NULL DEFAULT CURRENT_TIMESTAMP(1),
  `borrowed_date` datetime(1) NOT NULL DEFAULT CURRENT_TIMESTAMP(1),
  `fine` int(50) DEFAULT NULL,
  `fine_updated` timestamp(1) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`user_id`, `book_id`, `copies`, `status`, `return_sched`, `borrowed_date`, `fine`, `fine_updated`) VALUES
('21-SJ00126', '2', 1, 'returned', '2025-05-09 07:25:50.0', '2025-05-08 13:25:50.2', NULL, NULL),
('21-SJ00126', '2', 1, 'overdue', '2025-05-09 07:33:57.0', '2025-05-08 13:33:57.8', 10, '2025-05-19 03:03:33.0'),
('21-SJ00126', '3', 1, 'overdue', '2025-05-09 07:34:12.0', '2025-05-08 13:34:12.1', 30, NULL),
('21-SJ00126', '5', 1, 'overdue', '2025-05-09 07:34:32.0', '2025-05-08 13:34:32.1', 30, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pay`
--

CREATE TABLE `pay` (
  `user_id` varchar(50) NOT NULL,
  `book_id` int(250) NOT NULL,
  `total_pay` int(50) NOT NULL,
  `payment_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pay`
--

INSERT INTO `pay` (`user_id`, `book_id`, `total_pay`, `payment_date`) VALUES
('21-SJ00126', 5, 5, '2025-05-19'),
('21-SJ00126', 5, 20, '2025-05-19'),
('21-SJ00126', 2, 20, '2025-05-19');

-- --------------------------------------------------------

--
-- Table structure for table `reserve_books`
--

CREATE TABLE `reserve_books` (
  `user_id` varchar(50) NOT NULL,
  `book_id` int(50) NOT NULL,
  `reserved_date` datetime(1) NOT NULL DEFAULT CURRENT_TIMESTAMP(1),
  `status` varchar(50) NOT NULL,
  `copies` int(50) NOT NULL,
  `cancel_date` varchar(50) DEFAULT NULL,
  `expiration_schedule` timestamp(1) NULL DEFAULT NULL,
  `expired_date` timestamp(1) NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `reserve_books`
--

INSERT INTO `reserve_books` (`user_id`, `book_id`, `reserved_date`, `status`, `copies`, `cancel_date`, `expiration_schedule`, `expired_date`) VALUES
('21-SJ00126', 1, '2025-05-09 10:10:37.0', 'expired', 1, NULL, '2025-05-09 09:00:00.0', NULL),
('21-SJ00126', 1, '2025-05-11 10:21:45.0', 'expired', 1, NULL, '2025-05-11 09:00:00.0', NULL),
('21-SJ00126', 1, '2025-05-13 10:23:10.0', 'expired', 1, NULL, '2025-05-13 09:00:00.0', NULL),
('21-SJ00126', 3, '2025-05-15 10:28:40.0', 'expired', 1, NULL, '2025-05-15 09:00:00.0', '2025-05-16 20:51:03.0'),
('21-SJ00126', 1, '2025-05-15 10:35:58.0', 'expired', 1, NULL, '2025-05-15 09:00:00.0', '2025-05-16 20:51:03.0'),
('21-SJ00126', 3, '2025-05-17 10:51:32.0', 'expired', 1, NULL, '2025-05-17 09:00:00.0', '2025-05-18 20:52:39.0');

-- --------------------------------------------------------

--
-- Table structure for table `return_books`
--

CREATE TABLE `return_books` (
  `user_id` varchar(50) NOT NULL,
  `book_id` int(50) NOT NULL,
  `copies` int(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `return_date` datetime(1) NOT NULL DEFAULT CURRENT_TIMESTAMP(1)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `return_books`
--

INSERT INTO `return_books` (`user_id`, `book_id`, `copies`, `status`, `return_date`) VALUES
('21-SJ00126', 2, 1, 'returned', '2025-05-08 13:26:24.8');

-- --------------------------------------------------------

--
-- Table structure for table `student_list`
--

CREATE TABLE `student_list` (
  `ID` int(50) NOT NULL,
  `Student_No` varchar(250) NOT NULL,
  `Lastname` varchar(250) NOT NULL,
  `Firstname` varchar(250) NOT NULL,
  `Middlename` varchar(250) NOT NULL,
  `Course` varchar(255) NOT NULL,
  `Gmail` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `student_list`
--

INSERT INTO `student_list` (`ID`, `Student_No`, `Lastname`, `Firstname`, `Middlename`, `Course`, `Gmail`) VALUES
(1, '21-SJ00126', 'Elizalde', 'Modesto Jr.', 'Velarde', 'BSIT', 'modestoelizalde1@gmail.com'),
(2, '21-SJ00307', 'Reyes', 'Jerald', 'Chavez', 'BSCRIM', 'reyesjerald638@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `id` int(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `middle_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `patron_type` varchar(250) NOT NULL,
  `email` varchar(300) NOT NULL,
  `address` varchar(300) NOT NULL,
  `password` varchar(250) NOT NULL,
  `images` varchar(300) DEFAULT NULL,
  `status` varchar(50) NOT NULL,
  `account_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `first_name`, `middle_name`, `last_name`, `patron_type`, `email`, `address`, `password`, `images`, `status`, `account_status`) VALUES
(6, '21-SJ00307', 'Jerald', 'Chavez', 'Reyes', 'student-BSCRIM', 'reyesjerald638@gmail.com', 'Inoraguiao Sta. Margarita Samar', '$2y$10$tEf0G2JRKCJAwZw5RKnjy.i4gYQV9ehdODDx14kZT8O7xBw8VB/yO', '67f8bfee894c8-bgs.png', 'approved', 'active'),
(7, '21-SJ00126', 'Modesto Jr.', 'Velarde', 'Elizalde', 'student-BSIT', 'modestoelizalde1@gmail.com', 'Gandara', '$2y$10$Hm6PK3e3o6H5tJ/ofEb3X.NNnS3eVlT9uRvZRU.Io5lG5dgdAnDka', NULL, 'approved', 'active'),
(8, '21-SJ00127', 'Mark Francis', 'Velarde', 'Molleda', 'student-BSIT', 'Mark@gmail.com', 'Tarangnan Samar', '$2y$10$Pjeu863jKTyZuC6suVvq4OcJACj8VlhxxyVUc4RES4IxkDn9Rw99C', NULL, 'approved', 'inactive'),
(9, '12345', 'japheth', 'uragon', 'manyakol', 'faculty-Non-Teaching', 'qwe@gmail.com', 'dsa', '$2y$10$2xw2.NRMrN4EvVoWMA5BI.dFsu6RR7GotAgyu963H5YPGzBYOyrPy', NULL, 'approved', 'active'),
(10, '123', 'japheth', 'uragon', 'manyakol', 'student-BSCRIM', 'sa@gmail.com', 'ds', '$2y$10$MJwC/IZhve4gkoQGgSBRFO9kEA8vNVBVhoNlWlz.z7HFD2BeWmRUW', NULL, 'approved', 'active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_list`
--
ALTER TABLE `student_list`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=150;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `ID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
