-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 01:43 PM
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
-- Database: `firewall_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `firewall`
--

CREATE TABLE `firewall` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `firewall_name` varchar(255) NOT NULL,
  `ip_address` varchar(15) NOT NULL,
  `port` varchar(10) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `firewall`
--

INSERT INTO `firewall` (`id`, `customer_name`, `firewall_name`, `ip_address`, `port`, `location`, `model`) VALUES
(13, 'Skye Cloud (London)', 'SC-LON-FW01', '45.145.29.9', '9443', 'Birchin Court', 'Draytek Vigor 2860'),
(14, 'Skye Cloud (Dartford)', 'Crossway-FW01', '10.1.1.254', '9443', 'Dartford', 'Draytek Vigor 2865');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `firewall`
--
ALTER TABLE `firewall`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ip_address` (`ip_address`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `firewall`
--
ALTER TABLE `firewall`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
