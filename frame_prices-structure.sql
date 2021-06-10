-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 10, 2021 at 11:50 AM
-- Server version: 10.3.29-MariaDB-log
-- PHP Version: 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cpnlnmm63984_dmvarna`
--

-- --------------------------------------------------------

--
-- Table structure for table `frame_prices`
--

CREATE TABLE `frame_prices` (
  `fp_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price1` float NOT NULL,
  `price2` float NOT NULL,
  `price3` float NOT NULL,
  `price4` float NOT NULL,
  `price5` float NOT NULL,
  `price6` float NOT NULL,
  `price7` float NOT NULL,
  `price8` float NOT NULL,
  `price9` float NOT NULL,
  `price10` float NOT NULL,
  `price11` float NOT NULL,
  `price12` float NOT NULL,
  `price13` float NOT NULL,
  `price14` float NOT NULL,
  `price15` float NOT NULL,
  `promo1` float NOT NULL,
  `promo2` float NOT NULL,
  `promo3` float NOT NULL,
  `promo4` float NOT NULL,
  `promo5` float NOT NULL,
  `promo6` float NOT NULL,
  `promo7` float NOT NULL,
  `promo8` float NOT NULL,
  `promo9` float NOT NULL,
  `promo10` float NOT NULL,
  `promo11` float NOT NULL,
  `promo12` float NOT NULL,
  `promo13` float NOT NULL,
  `promo14` float NOT NULL,
  `promo15` float NOT NULL,
  `price0_desc` varchar(400) NOT NULL,
  `price1_desc` varchar(400) NOT NULL,
  `price2_desc` varchar(400) NOT NULL,
  `price3_desc` varchar(400) NOT NULL,
  `price4_desc` varchar(400) NOT NULL,
  `price5_desc` varchar(400) NOT NULL,
  `price6_desc` varchar(400) NOT NULL,
  `price7_desc` varchar(400) NOT NULL,
  `price8_desc` varchar(400) NOT NULL,
  `price9_desc` varchar(400) NOT NULL,
  `price10_desc` varchar(400) NOT NULL,
  `price11_desc` varchar(400) NOT NULL,
  `price12_desc` varchar(400) NOT NULL,
  `price13_desc` varchar(400) NOT NULL,
  `price14_desc` varchar(400) NOT NULL,
  `price15_desc` varchar(400) NOT NULL,
  `pic0` varchar(10) NOT NULL,
  `pic1` varchar(10) NOT NULL,
  `pic2` varchar(10) NOT NULL,
  `pic3` varchar(10) NOT NULL,
  `pic4` varchar(10) NOT NULL,
  `pic5` varchar(10) NOT NULL,
  `pic6` varchar(10) NOT NULL,
  `pic7` varchar(10) NOT NULL,
  `pic8` varchar(10) NOT NULL,
  `pic9` varchar(10) NOT NULL,
  `pic10` varchar(10) NOT NULL,
  `pic11` varchar(10) NOT NULL,
  `pic12` varchar(10) NOT NULL,
  `pic13` varchar(10) NOT NULL,
  `pic14` varchar(10) NOT NULL,
  `pic15` varchar(10) NOT NULL,
  `description` varchar(500) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `frame_prices`
--
ALTER TABLE `frame_prices`
  ADD PRIMARY KEY (`fp_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `frame_prices`
--
ALTER TABLE `frame_prices`
  MODIFY `fp_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
