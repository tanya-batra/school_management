-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 03, 2025 at 04:35 PM
-- Server version: 5.7.23-23
-- PHP Version: 8.1.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `designin_littlelegends`
--

-- --------------------------------------------------------

--
-- Table structure for table `change_fee`
--

CREATE TABLE `change_fee` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `fee_change` enum('Add','Subtract') DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `class`
--

CREATE TABLE `class` (
  `id` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `fees` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `class`
--

INSERT INTO `class` (`id`, `class_name`, `fees`) VALUES
(1, 'Kg', 75600),
(2, 'Nursery', 68880),
(3, 'Pre Nursey', 74000),
(4, 'Grade 1', 72000),
(5, 'Class2', 78000);

-- --------------------------------------------------------

--
-- Table structure for table `fee_transactions`
--

CREATE TABLE `fee_transactions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `amount` int(11) NOT NULL,
  `payment_mode` enum('cash','online','bank') NOT NULL,
  `comment` text,
  `session` varchar(50) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fee_transactions`
--

INSERT INTO `fee_transactions` (`id`, `student_id`, `amount`, `payment_mode`, `comment`, `session`, `created_by`, `created_at`, `updated_at`, `date`) VALUES
(1, 1, 6300, 'online', NULL, '2025-26', 'test@gmail.com', '2025-03-28 06:59:28', '2025-03-28 06:59:28', '2025-03-28'),
(2, 7, 7000, 'bank', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:30:49', '2025-03-31 08:30:49', '2025-03-31'),
(3, 4, 5000, 'online', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:32:01', '2025-03-31 08:32:01', '2025-03-31'),
(4, 6, 8000, 'cash', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:32:42', '2025-03-31 08:32:42', '2025-03-31'),
(5, 3, 400, 'cash', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:33:27', '2025-03-31 08:33:27', '2025-03-31'),
(6, 3, 400, 'cash', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:33:27', '2025-03-31 08:33:27', '2025-03-31'),
(7, 2, 700, 'online', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:34:05', '2025-03-31 08:34:05', '2025-03-31'),
(8, 2, 700, 'online', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:34:05', '2025-03-31 08:34:05', '2025-03-31'),
(9, 5, 1900, 'cash', NULL, '2025-26', 'test@gmail.com', '2025-03-31 08:35:04', '2025-03-31 08:35:04', '2025-03-31'),
(10, 9, 6000, 'cash', NULL, '2025-26', 'test@gmail.com', '2025-04-01 05:00:33', '2025-04-01 05:00:33', '2025-04-01'),
(11, 8, 7200, 'bank', NULL, '2025-26', 'test@gmail.com', '2025-04-01 05:01:06', '2025-04-01 05:01:06', '2025-04-01'),
(12, 8, 7200, 'bank', NULL, '2025-26', 'test@gmail.com', '2025-04-01 05:01:07', '2025-04-01 05:01:07', '2025-04-01');

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE `session` (
  `id` int(11) NOT NULL,
  `session` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `session`
--

INSERT INTO `session` (`id`, `session`, `status`) VALUES
(1, '2025-26', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff_expenditure`
--

CREATE TABLE `staff_expenditure` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `amount` int(11) NOT NULL,
  `comment` text NOT NULL,
  `session` varchar(50) NOT NULL,
  `created_by` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `staff_expenditure`
--

INSERT INTO `staff_expenditure` (`id`, `date`, `amount`, `comment`, `session`, `created_by`, `created_at`) VALUES
(1, '2025-03-21', 780, 'Driver Salary', '2025-26', 'test@gmail.com', '2025-03-28 07:06:56'),
(2, '2025-03-17', 1100, 'Rereshment', '2025-26', 'test@gmail.com', '2025-03-31 08:10:26'),
(3, '2025-03-31', 890, 'Staff', '2025-26', 'test@gmail.com', '2025-03-31 08:10:53'),
(4, '2025-03-01', 578, 'Hari Ran', '2025-26', 'test@gmail.com', '2025-03-31 08:26:19'),
(5, '2025-03-22', 7000, 'Petrol', '2025-26', 'test@gmail.com', '2025-04-01 05:03:28'),
(6, '2025-03-01', 900, 'Madam', '2025-26', 'test@gmail.com', '2025-04-01 05:06:07');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `admission_no` varchar(100) NOT NULL,
  `class_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `relation` varchar(10) NOT NULL,
  `father_name` varchar(255) NOT NULL,
  `mother_name` varchar(255) NOT NULL,
  `adhar_no` varchar(12) NOT NULL,
  `contact_no` varchar(10) NOT NULL,
  `profile_img` varchar(255) DEFAULT NULL,
  `total_fees` int(11) NOT NULL,
  `balance` int(11) DEFAULT '0',
  `session` varchar(20) NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `admission_no`, `class_id`, `name`, `relation`, `father_name`, `mother_name`, `adhar_no`, `contact_no`, `profile_img`, `total_fees`, `balance`, `session`, `status`, `created_at`) VALUES
(1, '234', 1, 'Noor Bindlish', 'D/O', 'Karan Bindlish', 'Mehar Bindlish', '123456789112', '9992021684', '', 75600, 69300, '2025-26', 1, '2025-03-28 06:51:35'),
(2, '2346', 2, 'Amaria', 'D/O', 'Sukhdev', 'Ranibhatti', '345672233111', '9812021543', '', 6200, 4800, '2025-26', 1, '2025-03-31 07:26:05'),
(3, '124', 4, 'Kabir', 'S/O', 'Kamal Sharma', 'Tina', '567892342222', '9920124562', '', 6000, 5200, '2025-26', 1, '2025-03-31 07:28:54'),
(4, 'Megan', 2, 'Megan', 'S/O', 'Sumer', 'Fatima', '234567892222', '8845672222', '', 6200, 1200, '2025-26', 1, '2025-03-31 07:32:37'),
(5, '789', 1, 'Kiyaan', 'S/O', 'N.P Garg', 'Sandhya', '456367899977', '9415978956', '', 75600, 73700, '2025-26', 1, '2025-03-31 07:48:04'),
(6, '780', 2, 'Mehar', 'S/O', 'Charanjeet Singh', 'Satinder Kaur7867888', '789676755555', '1234567891', '', 68880, 60880, '2025-26', 1, '2025-03-31 07:51:30'),
(7, 'Sunita', 4, 'Sunita', 'S/O', 'Lok Nath', 'Bimla', '6789456777', '3423444459', '', 72000, 65000, '2025-26', 1, '2025-03-31 07:54:18'),
(8, 'Advik', 4, 'Advik', 'S/O', 'Vijay Gupta', 'Kamini Gupta', '77777777789', '8912021654', '', 72000, 57600, '2025-26', 1, '2025-04-01 04:56:06'),
(9, 'Nivvan', 1, 'Niwan', 'S/O', 'Ajay Gupta', 'Sunita', '888888888888', '9812021684', '', 75600, 69600, '2025-26', 1, '2025-04-01 04:59:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'accountant',
  `password` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role`, `password`, `status`) VALUES
(1, 'test', 'test@gmail.com', 'accountant', '123456', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `change_fee`
--
ALTER TABLE `change_fee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `class`
--
ALTER TABLE `class`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fee_transactions`
--
ALTER TABLE `fee_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `session`
--
ALTER TABLE `session`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staff_expenditure`
--
ALTER TABLE `staff_expenditure`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `change_fee`
--
ALTER TABLE `change_fee`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class`
--
ALTER TABLE `class`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `fee_transactions`
--
ALTER TABLE `fee_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `session`
--
ALTER TABLE `session`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `staff_expenditure`
--
ALTER TABLE `staff_expenditure`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `change_fee`
--
ALTER TABLE `change_fee`
  ADD CONSTRAINT `change_fee_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`class_id`) REFERENCES `class` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
