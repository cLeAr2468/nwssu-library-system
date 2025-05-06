-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 03, 2025 at 03:28 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `user_id`, `activity_type`, `activity_details`, `activity_date`, `status`) VALUES
(1, '21-SJ00126', 'click', 'User accessed top_collection page', '2025-05-02 16:34:20', 'active'),
(2, '21-SJ00126', 'View Books', 'Domestic TOURISM', '2025-05-02 16:34:29', 'active'),
(3, '21-SJ00126', 'View Books', 'Environmental Management', '2025-05-02 16:34:49', 'active'),
(4, '21-SJ00126', 'View Books', 'The Philippines Recommends for Watershed Management', '2025-05-02 16:35:02', 'active'),
(5, '21-SJ00126', 'View Books', 'The Philippines Recommends for Watershed Management', '2025-05-02 16:35:27', 'active'),
(6, '21-SJ00126', 'View Books', 'MultiCultural management', '2025-05-02 16:35:41', 'active'),
(7, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-02 16:38:49', 'active'),
(8, '21-SJ00126', 'View Books', 'TOURISM Planning and Development', '2025-05-02 16:54:18', 'active'),
(9, '21-SJ00126', 'View Books', 'TOURISM Planning and Development', '2025-05-02 16:55:19', 'active'),
(10, '21-SJ00126', 'click', 'User accessed Book Catalog page', '2025-05-02 16:58:11', 'active'),
(11, '21-SJ00126', 'logout', 'User signed out of the system', '2025-05-02 17:03:18', 'active');

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`id`, `title`, `message`, `image`, `date`) VALUES
(1, 'Kupal', 'kupal kaba boss?', '../uploaded_file/bos.jpg', '2024-12-03 19:28:34'),
(2, 'Bossing', 'csakcmakdjawoddw', NULL, '2024-12-03 19:43:11');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `material_type`, `sub_type`, `category`, `title`, `author`, `publisher`, `status`, `subject`, `content`, `summary`, `issn`, `call_no`, `ISBN`, `copyright`, `page_number`, `edition`, `copies`, `date_acquired`, `books_image`, `catalog_date`) VALUES
(1, 'Book', 'Others', 'BSF', 'MultiCultural management', 'FARID ELASHMAWI et al', 'National Book Store', 'available', 'Programming', 'aaaaa', 'aaaaa', '00000', '658 E37', '9831363825', '000000', 234, 'N/A', 3, '2025-03-27', NULL, '2025-04-02'),
(2, 'Journal', 'Others', 'BSF', 'The Philippines Recommends for Watershed Management', 'Dr. Vicente P. Veracion et al', 'DOST', 'available', 'Animal Science, Agriculture', 'd', 'd', '1157787', '658 P364 1991', '9712001822', '00000', 88, 'N/A', 5, '2025-03-27', NULL, '2025-04-02'),
(3, 'Book', 'Others', 'BSF', 'Environmental Management', 'Dr. Swapan C Deb', 'JAICO BOOKS', 'available', 'Animal Science, Agriculture', 'ds', 'ds', '000000', '658.4 D299 2003', '8179921344', '000000', 243, 'N/A', 2, '2025-03-27', NULL, '2025-04-08'),
(4, 'Book', 'Others', 'BSF', 'TOURISM Planning and Development', 'Zenaida Lansangan Cruz, Ph.D.', 'National Book Store', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 O111 2011', '9710870424', '000000', 145, 'N/A', 1, '2025-03-27', NULL, '2025-04-02'),
(5, 'Book', 'Others', 'BSF', 'Ornamental Horticulture and Landscape Gardening', 'Dr. Mahesh Kumar', 'RANDOM PUBLICATIONS LLP', 'available', 'Animal Science, Agriculture', 'a', 'a', '00000', '712 K95 2022', '9789393884527', '000000', 306, 'N/A', 4, '2025-03-27', NULL, '2025-04-02'),
(10, 'Book', 'Others', 'BSF', 'Domestic TOURISM', 'Carlos M. Libosada, Jr.', 'ANVIL PIBLISHING, INC.', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 L11 2003', '9712713989', '000000', 214, 'N/A', 2, '2025-03-27', '../uploaded_file/bgss.png', '2025-04-08'),
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
  `return_sched` varchar(250) NOT NULL,
  `borrowed_date` varchar(250) NOT NULL,
  `fine` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`user_id`, `book_id`, `copies`, `status`, `return_sched`, `borrowed_date`, `fine`) VALUES
('21-SJ00307', '10', 1, 'returned', '2025-04-24', '2025-04-10', NULL),
('21-SJ00307', '10', 1, 'returned', '2025-04-25', '2025-04-11', NULL),
('21-SJ00307', '3', 1, 'returned', '2025-05-01', '2025-04-17', NULL),
('21-SJ00307', '10', 1, 'returned', '2025-05-01', '2025-04-17', NULL),
('21-SJ00126', '10', 1, 'returned', '2025-05-01', '2025-04-17', NULL),
('21-SJ00127', '2', 1, 'returned', '2025-05-01', '2025-04-17', NULL),
('21-SJ00126', '10', 1, 'returned', '2025-05-15', '2025-05-01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pay`
--

CREATE TABLE `pay` (
  `id` int(50) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `user_name` varchar(250) NOT NULL,
  `patron_type` varchar(250) NOT NULL,
  `total_pay` int(50) NOT NULL,
  `payment_date` varchar(50) NOT NULL,
  `ISBN` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pay`
--

INSERT INTO `pay` (`id`, `user_id`, `user_name`, `patron_type`, `total_pay`, `payment_date`, `ISBN`) VALUES
(1, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 6, '2024-11-24 11:23:25', ''),
(2, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 9, '2024-11-24 11:23:32', ''),
(3, '21-SJ00318', 'Angelique B. Villanueva', 'student-BSIT', 3, '2024-11-24 18:36:53', ''),
(4, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 9, '2024-11-25 09:29:47', ''),
(5, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 0, '2024-11-25 09:47:00', ''),
(6, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 0, '2024-11-25 09:54:03', ''),
(7, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 2, '2024-11-25 09:54:56', ''),
(8, '21-SJ00318', 'Angelique B. Villanueva', 'student-BSIT', 3, '2024-11-25 10:05:01', ''),
(9, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 4, '2024-11-25 10:06:03', ''),
(10, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 3, '2024-11-25 11:08:53', '3322'),
(11, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 0, '2024-12-03 15:07:02', '1332255'),
(12, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 0, '2024-12-05 15:15:52', '9789713703545'),
(13, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 18, '2024-12-05 15:16:07', ''),
(14, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 0, '2024-12-05 15:50:28', '9781942270065'),
(15, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 0, '2024-12-05 15:50:35', '971087036'),
(16, '21-SJ00126', 'Modesto Morado', 'student-BSIT', 13, '2024-12-05 15:51:01', ''),
(17, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 0, '2024-12-05 15:57:44', '0078285402'),
(18, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 10, '2024-12-05 16:09:24', ''),
(19, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 0, '2024-12-05 16:11:20', '0078285402'),
(20, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 3, '2024-12-05 16:15:08', ''),
(21, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 6, '2024-12-05 17:13:02', '9781774691281'),
(22, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 3, '2024-12-05 17:14:47', ''),
(23, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 1, '2024-12-05 17:39:30', ''),
(24, '21-SJ00307', 'Jerald Reyes', 'student-BSIT', 1, '2024-12-06 06:55:19', '');

-- --------------------------------------------------------

--
-- Table structure for table `reserve_books`
--

CREATE TABLE `reserve_books` (
  `user_id` varchar(50) NOT NULL,
  `book_id` int(50) NOT NULL,
  `reserved_date` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `copies` int(50) NOT NULL,
  `cancel_date` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reserve_books`
--

INSERT INTO `reserve_books` (`user_id`, `book_id`, `reserved_date`, `status`, `copies`, `cancel_date`) VALUES
('21-SJ00307', 3, '2025-04-10 19:12:49', 'borrowed', 1, ''),
('21-SJ00307', 10, '2025-04-11 19:00:16', 'borrowed', 1, NULL),
('21-SJ00126', 10, '2025-04-13 14:09:01', 'borrowed', 1, NULL),
('21-SJ00126', 10, '2025-05-01 14:01:20', 'borrowed', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `return_books`
--

CREATE TABLE `return_books` (
  `user_id` varchar(50) NOT NULL,
  `book_id` int(50) NOT NULL,
  `copies` int(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `return_date` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `return_books`
--

INSERT INTO `return_books` (`user_id`, `book_id`, `copies`, `status`, `return_date`) VALUES
('21-SJ00307', 10, 1, 'returned', '2025-04-11'),
('21-SJ00307', 10, 1, 'returned', '2025-04-11'),
('21-SJ00307', 3, 1, 'returned', '2025-04-17'),
('21-SJ00307', 10, 1, 'returned', '2025-04-17'),
('21-SJ00126', 10, 1, 'returned', '2025-04-17'),
('21-SJ00127', 2, 1, 'returned', '2025-04-17'),
('21-SJ00126', 10, 1, 'returned', '2025-05-01');

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`id`, `user_id`, `first_name`, `middle_name`, `last_name`, `patron_type`, `email`, `address`, `password`, `images`, `status`, `account_status`) VALUES
(5, '21-SJ00', 'Mark', 'M.', 'Molleda', 'Student-BSIT', 'Mark@gmail.com', 'Tarangnan', '$2b$10$HPmMa6siJ/xiC04fXF5bcu.4mN1eC.MNwsm5pRRQIRbROc/w7Qfna', NULL, 'approved', 'active'),
(6, '21-SJ00307', 'Jerald', 'Chavez', 'Reyes', 'student-BSCRIM', 'reyesjerald638@gmail.com', 'Inoraguiao Sta. Margarita Samar', '$2y$10$bd1pcMnsoXnQXMOU9VCp4.AaiOiMZbBHRCjqca/A9ohnbd8O3xegq', '67f8bfee894c8-bgs.png', 'approved', 'active'),
(7, '21-SJ00126', 'Modesto Jr.', 'Velarde', 'Elizalde', 'student-BSIT', 'modestoelizalde1@gmail.com', 'Gandara', '$2y$10$Hm6PK3e3o6H5tJ/ofEb3X.NNnS3eVlT9uRvZRU.Io5lG5dgdAnDka', NULL, 'approved', 'active'),
(8, '21-SJ00127', 'Mark Francis', 'Velarde', 'Molleda', 'student-BSIT', 'Mark@gmail.com', 'Tarangnan Samar', '$2y$10$Pjeu863jKTyZuC6suVvq4OcJACj8VlhxxyVUc4RES4IxkDn9Rw99C', NULL, 'approved', 'inactive');

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
-- Indexes for table `pay`
--
ALTER TABLE `pay`
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
  MODIFY `activity_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
-- AUTO_INCREMENT for table `pay`
--
ALTER TABLE `pay`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `student_list`
--
ALTER TABLE `student_list`
  MODIFY `ID` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_info`
--
ALTER TABLE `user_info`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
