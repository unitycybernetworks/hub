-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2025 at 03:41 PM
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
-- Database: `autotask_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `companyName` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `webAddress` varchar(255) DEFAULT NULL,
  `additionalAddressInformation` text DEFAULT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postalCode` varchar(20) DEFAULT NULL,
  `countryID` int(11) DEFAULT NULL,
  `companyCategoryID` int(11) DEFAULT NULL,
  `companyType` int(11) DEFAULT NULL,
  `createDate` datetime DEFAULT NULL,
  `createdByResourceID` int(11) DEFAULT NULL,
  `currencyID` int(11) DEFAULT NULL,
  `fax` varchar(50) DEFAULT NULL,
  `invoiceEmailMessageID` int(11) DEFAULT NULL,
  `invoiceMethod` int(11) DEFAULT NULL,
  `invoiceNonContractItemsToParentCompany` tinyint(1) DEFAULT NULL,
  `invoiceTemplateID` int(11) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT NULL,
  `isClientPortalActive` tinyint(1) DEFAULT NULL,
  `isEnabledForComanaged` tinyint(1) DEFAULT NULL,
  `isTaskFireActive` tinyint(1) DEFAULT NULL,
  `isTaxExempt` tinyint(1) DEFAULT NULL,
  `lastActivityDate` datetime DEFAULT NULL,
  `lastTrackedModifiedDateTime` datetime DEFAULT NULL,
  `ownerResourceID` int(11) DEFAULT NULL,
  `sharepointSite` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `companyName`, `phone`, `webAddress`, `additionalAddressInformation`, `address1`, `address2`, `city`, `state`, `postalCode`, `countryID`, `companyCategoryID`, `companyType`, `createDate`, `createdByResourceID`, `currencyID`, `fax`, `invoiceEmailMessageID`, `invoiceMethod`, `invoiceNonContractItemsToParentCompany`, `invoiceTemplateID`, `isActive`, `isClientPortalActive`, `isEnabledForComanaged`, `isTaskFireActive`, `isTaxExempt`, `lastActivityDate`, `lastTrackedModifiedDateTime`, `ownerResourceID`, `sharepointSite`) VALUES
(0, 'Skye Cloud', '03450754913', 'https://www.skye-cloud.com', '', 'Suite 1, Blake House', 'Schooner Court, Crossways Business Park', 'Dartford', 'Kent', 'DA2 6QQ', 236, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 1, 1, 0, 0, 0, '2025-05-29 11:15:53', '2024-12-23 22:02:03', NULL, 'https://skyecloudltd.sharepoint.com/');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1126;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_ibfk_1` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
