Logins: 
username: Admin1, xyz, Diu Medic

passowrd: password1234 {same for all}






RAW SQL CODE: 

**There is also a sql file included that can be imported**

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2025 at 08:20 AM
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
-- Database: `hospital_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `patient_id`, `doctor_id`, `appointment_date`, `appointment_time`, `status`, `description`, `created_at`) VALUES
(1, 2, 1, '2025-06-06', '20:40:00', 'Pending', NULL, '2025-04-12 13:40:59'),
(2, 3, 2, '2025-06-06', '09:12:00', 'Pending', NULL, '2025-04-12 16:07:28'),
(5, 3, 1, '2026-01-21', '12:34:00', 'Cancelled', NULL, '2025-04-12 16:09:03'),
(6, 1, 2, '2025-08-08', '21:08:00', 'Confirmed', NULL, '2025-04-12 19:37:18'),
(7, 4, 3, '2025-07-07', '19:06:00', 'Confirmed', NULL, '2025-04-13 01:27:51'),
(8, 5, 3, '2025-07-07', '16:56:00', 'Pending', NULL, '2025-04-13 03:00:41'),
(9, 5, 1, '2025-05-06', '15:05:00', 'Pending', NULL, '2025-04-13 03:01:13'),
(10, 3, 2, '2027-06-06', '09:08:00', 'Pending', NULL, '2025-04-19 05:40:07');

-- --------------------------------------------------------

--
-- Table structure for table `bills`
--

CREATE TABLE `bills` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('unpaid','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bills`
--

INSERT INTO `bills` (`id`, `patient_id`, `doctor_id`, `appointment_id`, `amount`, `description`, `status`, `created_at`, `paid_at`) VALUES
(1, 3, 2, 2, 7000.00, ':)', 'paid', '2025-04-12 19:44:37', '2025-04-13 01:46:30'),
(2, 3, 2, 2, 8000.00, ',,,,,,,,,,,,,,,,', 'unpaid', '2025-04-13 01:26:04', NULL),
(3, 1, 2, 6, 600.00, '.........', 'paid', '2025-04-13 03:04:33', '2025-04-19 11:43:06'),
(4, 3, 2, 2, 560.00, 'Medicine', 'unpaid', '2025-04-19 05:42:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'xyz', 'xyz@gmail.com', '0191231255', 'Unknnown', 'dvsdvfskovsvbaspibjasp', 'read', '2025-04-12 17:26:32');

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `qualification` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `full_name`, `specialization`, `qualification`, `phone`) VALUES
(1, 2, 'Mac Doc', 'Heart', '', NULL),
(2, 8, 'Diu Medic', 'Family medicine', 'PHD', '01921321453'),
(3, 9, 'abc', 'Neurology', 'Unknown', '0192315356'),
(4, 13, 'pc1', 'Cancer', 'PHD', '019215423546');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `full_name`, `dob`, `gender`, `address`, `phone`, `blood_group`) VALUES
(1, 1, 'ash ', '2019-01-23', 'Male', 'no', '01923151513', 'A+'),
(2, 5, 'Ashab Rahman', '0000-00-00', 'Male', NULL, '01924115123', NULL),
(3, 10, 'xxyyzz', '0000-00-00', 'Male', NULL, '0152312553', NULL),
(4, 12, 'bd', '0000-00-00', 'Male', NULL, '0182415325', NULL),
(5, 14, 'user1', '0000-00-00', 'Male', NULL, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','doctor','patient') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'Ashab', '$2y$10$X0cArFFTqtptopZw5bNl7utxK2asvCG2EtkGOtCqD4UfnCpUbjEFu', 'ash@gmail.com', 'patient', '2025-04-12 13:05:23'),
(2, 'mac', '$2y$10$ZMY.aYwDxF5iZaabqFVBN.WqNl10bVfexekRix5N/PLsQx9DStjQy', 'mac@gmail.com', 'doctor', '2025-04-12 13:21:25'),
(3, 'Admin', '$2y$10$CEcf.lVaXXAhp8AJkjJZkuEYHGnVjc11/JvYILpkOROPv2rqulIhm', 'admin@gmail.com', 'admin', '2025-04-12 13:23:27'),
(5, 'Ashabb', '$2y$10$mtV7mGGNyOqIJFoR0aj9g.Iv7CDU2rNG.1n8EpHTvzjj1jhznA4z6', 'ashabb@gmail.com', 'patient', '2025-04-12 13:26:42'),
(8, 'Diu Medic', '$2y$10$MPy/59ON/Az3Jp.xURcgtuU7r2hMvi1KWPFLdekeY06mFT//MkU2i', 'medic@gmail.com', 'doctor', '2025-04-12 15:53:25'),
(9, 'abc_Doc', '$2y$10$rP8.kvqKDcWMRjt9wniIb.nWjkKrk7VYsv3Lh36U837uvPmMSvuHW', 'abc@gmail.com', 'doctor', '2025-04-12 15:56:44'),
(10, 'xyz', '$2y$10$wQzkXLIoCom.Eb59DUoob.huYEoUHW24jombHK5BEmnrP1y3e0CX.', 'xyz@gmail.com', 'patient', '2025-04-12 16:06:59'),
(11, 'Admin1', '$2y$10$2wjqy11XzqgbMT.Y1ZT9x.9HSM.Njxqq5/8mZ6gHoH1399Y2BMYSq', 'admin1@gmail.com', 'admin', '2025-04-12 16:25:53'),
(12, 'bd', '$2y$10$VL65gjQFvl/BE8YSOXfqb.1.jhm3iuq7X05YPsRXVSnfPBj0vcSnq', 'bd@gmail.com', 'patient', '2025-04-13 01:27:20'),
(13, 'pc', '$2y$10$x.C8durvuqOyaYCmKmLzFuAjWx85X2wKsfJqzIGFxEpzjQSeO3EMu', 'pc@gmail.com', 'doctor', '2025-04-13 02:45:07'),
(14, 'user', '$2y$10$f7p5LH3aXkQFlqi31v/G5.8xXPwUIwakZ8GlOs4dVMzXMAU1P9bJm', 'user@gmail.com', 'patient', '2025-04-13 03:00:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `bills`
--
ALTER TABLE `bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `bills`
--
ALTER TABLE `bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`);

--
-- Constraints for table `bills`
--
ALTER TABLE `bills`
  ADD CONSTRAINT `bills_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`),
  ADD CONSTRAINT `bills_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`),
  ADD CONSTRAINT `bills_ibfk_3` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;



*******Backup code if the above doesn't work************

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `appointments` VALUES
(1, 2, 1, '2025-06-06', '20:40:00', 'Pending', NULL, '2025-04-12 13:40:59'),
(2, 3, 2, '2025-06-06', '09:12:00', 'Pending', NULL, '2025-04-12 16:07:28'),
(5, 3, 1, '2026-01-21', '12:34:00', 'Cancelled', NULL, '2025-04-12 16:09:03'),
(6, 1, 2, '2025-08-08', '21:08:00', 'Confirmed', NULL, '2025-04-12 19:37:18'),
(7, 4, 3, '2025-07-07', '19:06:00', 'Confirmed', NULL, '2025-04-13 01:27:51'),
(8, 5, 3, '2025-07-07', '16:56:00', 'Pending', NULL, '2025-04-13 03:00:41'),
(9, 5, 1, '2025-05-06', '15:05:00', 'Pending', NULL, '2025-04-13 03:01:13'),
(10, 3, 2, '2027-06-06', '09:08:00', 'Pending', NULL, '2025-04-19 05:40:07');

CREATE TABLE `bills` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('unpaid','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `paid_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `patient_id` (`patient_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `appointment_id` (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `bills` VALUES
(1, 3, 2, 2, 7000.00, ':)', 'paid', '2025-04-12 19:44:37', '2025-04-13 01:46:30'),
(2, 3, 2, 2, 8000.00, ',,,,,,,,,,,,,,,,', 'unpaid', '2025-04-13 01:26:04', NULL),
(3, 1, 2, 6, 600.00, '.........', 'paid', '2025-04-13 03:04:33', '2025-04-19 11:43:06'),
(4, 3, 2, 2, 560.00, 'Medicine', 'unpaid', '2025-04-19 05:42:21', NULL);

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `status` enum('unread','read','replied') DEFAULT 'unread',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `contact_messages` VALUES
(1, 'xyz', 'xyz@gmail.com', '0191231255', 'Unknnown', 'dvsdvfskovsvbaspibjasp', 'read', '2025-04-12 17:26:32');

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `specialization` varchar(100) NOT NULL,
  `qualification` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `doctors` VALUES
(1, 2, 'Mac Doc', 'Heart', '', NULL),
(2, 8, 'Diu Medic', 'Family medicine', 'PHD', '01921321453'),
(3, 9, 'abc', 'Neurology', 'Unknown', '0192315356'),
(4, 13, 'pc1', 'Cancer', 'PHD', '019215423546');

CREATE TABLE `patients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `dob` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `blood_group` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `patients` VALUES
(1, 1, 'ash ', '2019-01-23', 'Male', 'no', '01923151513', 'A+'),
(2, 5, 'Ashab Rahman', '0000-00-00', 'Male', NULL, '01924115123', NULL),
(3, 10, 'xxyyzz', '0000-00-00', 'Male', NULL, '0152312553', NULL),
(4, 12, 'bd', '0000-00-00', 'Male', NULL, '0182415325', NULL),
(5, 14, 'user1', '0000-00-00', 'Male', NULL, '', NULL);

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','doctor','patient') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` VALUES
(1, 'Ashab', '$2y$10$X0cArFFTqtptopZw5bNl7utxK2asvCG2EtkGOtCqD4UfnCpUbjEFu', 'ash@gmail.com', 'patient', '2025-04-12 13:05:23'),
(2, 'mac', '$2y$10$ZMY.aYwDxF5iZaabqFVBN.WqNl10bVfexekRix5N/PLsQx9DStjQy', 'mac@gmail.com', 'doctor', '2025-04-12 13:21:25'),
(3, 'Admin', '$2y$10$CEcf.lVaXXAhp8AJkjJZkuEYHGnVjc11/JvYILpkOROPv2rqulIhm', 'admin@gmail.com', 'admin', '2025-04-12 13:23:27'),
(5, 'Ashabb', '$2y$10$mtV7mGGNyOqIJFoR0aj9g.Iv7CDU2rNG.1n8EpHTvzjj1jhznA4z6', 'ashabb@gmail.com', 'patient', '2025-04-12 13:26:42'),
(8, 'Diu Medic', '$2y$10$MPy/59ON/Az3Jp.xURcgtuU7r2hMvi1KWPFLdekeY06mFT//MkU2i', 'medic@gmail.com', 'doctor', '2025-04-12 15:53:25'),
(9, 'abc_Doc', '$2y$10$rP8.kvqKDcWMRjt9wniIb.nWjkKrk7VYsv3Lh36U837uvPmMSvuHW', 'abc@gmail.com', 'doctor', '2025-04-12 15:56:44'),
(10, 'xyz', '$2y$10$wQzkXLIoCom.Eb59DUoob.huYEoUHW24jombHK5BEmnrP1y3e0CX.', 'xyz@gmail.com', 'patient', '2025-04-12 16:06:59'),
(11, 'Admin1', '$2y$10$2wjqy11XzqgbMT.Y1ZT9x.9HSM.Njxqq5/8mZ6gHoH1399Y2BMYSq', 'admin1@gmail.com', 'admin', '2025-04-12 16:25:53'),
(12, 'bd', '$2y$10$VL65gjQFvl/BE8YSOXfqb.1.jhm3iuq7X05YPsRXVSnfPBj0vcSnq', 'bd@gmail.com', 'patient', '2025-04-13 01:27:20'),
(13, 'pc', '$2y$10$x.C8durvuqOyaYCmKmLzFuAjWx85X2wKsfJqzIGFxEpzjQSeO3EMu', 'pc@gmail.com', 'doctor', '2025-04-13 02:45:07'),
(14, 'user', '$2y$10$f7p5LH3aXkQFlqi31v/G5.8xXPwUIwakZ8GlOs4dVMzXMAU1P9bJm', 'user@gmail.com', 'patient', '2025-04-13 03:00:25');

COMMIT;




