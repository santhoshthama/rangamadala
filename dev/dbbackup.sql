-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 03:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rangamandala_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `dramas`
--

CREATE TABLE `dramas` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_time` time DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `ticket_price` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dramas`
--

INSERT INTO `dramas` (`id`, `title`, `description`, `category_id`, `venue`, `event_date`, `event_time`, `duration`, `ticket_price`, `image`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Maname', 'A touching story about family bonds and redemption', 1, 'Lionel Wendt Theatre', '2025-12-25', '19:00:00', '2 hours', 1500.00, NULL, 1, '2025-12-16 13:19:56', '2025-12-16 13:19:56'),
(2, 'Sinhabahu', 'A classical tale of ancient Sri Lankan mythology', 4, 'Elphinstone Theatre', '2025-12-30', '18:30:00', '2.5 hours', 2000.00, NULL, 1, '2025-12-16 13:19:56', '2025-12-16 13:19:56'),
(3, 'Maname', 'A classical Sinhala drama exploring family relationships and societal values', 1, 'Lionel Wendt Theatre, Colombo', '2025-01-15', '19:00:00', '120', 1500.00, NULL, NULL, '2025-12-16 13:31:52', '2025-12-16 13:31:52'),
(4, 'Sinhabahu', 'Epic tale of the legendary king Sinhabahu and his journey', 1, 'Nelum Pokuna Theatre, Colombo', '2025-01-20', '18:30:00', '150', 2000.00, NULL, NULL, '2025-12-16 13:31:52', '2025-12-16 13:31:52'),
(5, 'Kolamba Kathawa', 'A comedy drama depicting urban life in Colombo', 3, 'Elphinstone Theatre, Maradana', '2025-02-05', '19:30:00', '90', 1000.00, NULL, NULL, '2025-12-16 13:31:52', '2025-12-16 13:31:52'),
(6, 'Nari Bena', 'Traditional folk drama with dance and music', 7, 'BMICH, Colombo', '2025-02-10', '18:00:00', '105', 1200.00, NULL, NULL, '2025-12-16 13:31:52', '2025-12-16 13:31:52'),
(7, 'Vijayaba Kollaya', 'Historical drama about ancient Sri Lankan warriors', 1, 'Regal Theatre, Colombo', '2025-02-15', '19:00:00', '135', 1800.00, NULL, NULL, '2025-12-16 13:31:52', '2025-12-16 13:31:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dramas`
--
ALTER TABLE `dramas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dramas`
--
ALTER TABLE `dramas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dramas`
--
ALTER TABLE `dramas`
  ADD CONSTRAINT `dramas_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `dramas_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
