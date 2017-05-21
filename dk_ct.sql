-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: May 21, 2017 at 05:15 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dk_ct`
--

-- --------------------------------------------------------

--
-- Table structure for table `mcu`
--

CREATE TABLE `mcu` (
  `id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `mcu`
--

INSERT INTO `mcu` (`id`, `name`, `ip`, `update_time`) VALUES
(10, 'M1', '172.20.10.12', '2017-05-15 09:17:11'),
(22, 'M2', '172.20.10.13', '2017-05-15 09:17:11');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `username` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `key_remember` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `username`, `password`, `key_remember`, `create_time`, `update_time`) VALUES
(1, 'Admin', '202cb962ac59075b964b07152d234b70', 'da3e1869f4474d78a2ce8966d8f9ef3a', '2017-05-15 08:44:58', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `node_active`
--

CREATE TABLE `node_active` (
  `id` int(11) NOT NULL,
  `name` varchar(120) COLLATE utf8_unicode_ci NOT NULL,
  `status` int(11) NOT NULL,
  `mcu_id` int(11) NOT NULL,
  `pin_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `node_active`
--

INSERT INTO `node_active` (`id`, `name`, `status`, `mcu_id`, `pin_id`) VALUES
(1, 'Node 1', 1, 10, 30),
(4, 'Node 3', 1, 10, 37),
(10, 'Node 5', 1, 10, 34),
(15, 'Node 6', 1, 10, 37);

-- --------------------------------------------------------

--
-- Table structure for table `pin`
--

CREATE TABLE `pin` (
  `id` int(11) NOT NULL,
  `pin_id` int(11) NOT NULL,
  `node_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `switch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=COMPACT;

--
-- Dumping data for table `pin`
--

INSERT INTO `pin` (`id`, `pin_id`, `node_id`, `status`, `switch`) VALUES
(30, 0, 10, 1, 1),
(31, 1, 10, 0, 1),
(32, 2, 10, 0, 0),
(33, 3, 10, 0, 0),
(34, 4, 10, 0, 0),
(35, 5, 10, 0, 0),
(36, 6, 10, 0, 0),
(37, 7, 10, 0, 0),
(46, 0, 22, 0, 1),
(47, 1, 22, 0, 1),
(48, 2, 22, 0, 1),
(49, 3, 22, 0, 1),
(50, 4, 22, 0, 1),
(51, 5, 22, 0, 1),
(52, 6, 22, 0, 1),
(53, 7, 22, 0, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `mcu`
--
ALTER TABLE `mcu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `node_active`
--
ALTER TABLE `node_active`
  ADD PRIMARY KEY (`id`),
  ADD KEY `MCU_ID` (`mcu_id`),
  ADD KEY `PIN_ID` (`pin_id`);

--
-- Indexes for table `pin`
--
ALTER TABLE `pin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `node_id` (`node_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `mcu`
--
ALTER TABLE `mcu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;
--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `node_active`
--
ALTER TABLE `node_active`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
--
-- AUTO_INCREMENT for table `pin`
--
ALTER TABLE `pin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `node_active`
--
ALTER TABLE `node_active`
  ADD CONSTRAINT `MCU_ID` FOREIGN KEY (`mcu_id`) REFERENCES `mcu` (`id`),
  ADD CONSTRAINT `PIN_ID` FOREIGN KEY (`pin_id`) REFERENCES `pin` (`id`);

--
-- Constraints for table `pin`
--
ALTER TABLE `pin`
  ADD CONSTRAINT `node_id` FOREIGN KEY (`node_id`) REFERENCES `mcu` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
