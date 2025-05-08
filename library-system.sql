-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 01:48 PM
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
(1, 'Book', 'Others', 'BSF', 'MultiCultural management', 'FARID ELASHMAWI et al', 'National Book Store', 'available', 'Programming', 'aaaaa', 'aaaaa', '00000', '658 E37', '9831363825', '000000', 234, 'N/A', 3, '2025-03-27', NULL, '2025-04-02'),
(2, 'Journal', 'Others', 'BSF', 'The Philippines Recommends for Watershed Management', 'Dr. Vicente P. Veracion et al', 'DOST', 'available', 'Animal Science, Agriculture', 'd', 'd', '1157787', '658 P364 1991', '9712001822', '00000', 88, 'N/A', 8, '2025-03-27', NULL, '2025-04-02'),
(3, 'Book', 'Others', 'BSF', 'Environmental Management', 'Dr. Swapan C Deb', 'JAICO BOOKS', 'available', 'Animal Science, Agriculture', 'ds', 'ds', '000000', '658.4 D299 2003', '8179921344', '000000', 243, 'N/A', 1, '2025-03-27', NULL, '2025-04-08'),
(4, 'Book', 'Others', 'BSF', 'TOURISM Planning and Development', 'Zenaida Lansangan Cruz, Ph.D.', 'National Book Store', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 O111 2011', '9710870424', '000000', 145, 'N/A', 1, '2025-03-27', NULL, '2025-04-02'),
(5, 'Book', 'Others', 'BSF', 'Ornamental Horticulture and Landscape Gardening', 'Dr. Mahesh Kumar', 'RANDOM PUBLICATIONS LLP', 'available', 'Animal Science, Agriculture', 'a', 'a', '00000', '712 K95 2022', '9789393884527', '000000', 306, 'N/A', 4, '2025-03-27', NULL, '2025-04-02'),
(10, 'E-Book', 'Others', 'BSF', 'Domestic TOURISM', 'Carlos M. Libosada, Jr.', 'ANVIL PIBLISHING, INC.', 'available', 'Animal Science, Agriculture', 's', 's', '00000', '790 L11 2003', '9712713989', '000000', 214, 'N/A', 2, '2025-03-27', '../uploaded_file/bgss.png', '2025-05-07'),
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
  `return_sched` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `borrowed_date` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `fine` int(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `borrowed_books`
--

INSERT INTO `borrowed_books` (`user_id`, `book_id`, `copies`, `status`, `return_sched`, `borrowed_date`, `fine`) VALUES
('21-SJ00127', '2', 1, 'returned', '2025-05-08 00:00:00.000000', '2025-05-07 00:00:00.000000', NULL),
('123', '2', 1, 'borrowed', '2025-05-08 00:00:00.000000', '2025-05-07 00:00:00.000000', NULL);

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `return_books`
--

CREATE TABLE `return_books` (
  `user_id` varchar(50) NOT NULL,
  `book_id` int(50) NOT NULL,
  `copies` int(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `return_date` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `return_books`
--

INSERT INTO `return_books` (`user_id`, `book_id`, `copies`, `status`, `return_date`) VALUES
('21-SJ00127', 2, 1, 'returned', '2025-05-07 00:00:00.000000');

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
  MODIFY `activity_id` int(50) NOT NULL AUTO_INCREMENT;

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
