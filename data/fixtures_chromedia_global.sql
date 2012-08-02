-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 23, 2012 at 02:18 PM
-- Server version: 5.1.63
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fixtures_chromedia_global`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE IF NOT EXISTS `accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `middle_name` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `date_created` timestamp NULL DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='global account information';

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`id`, `first_name`, `last_name`, `middle_name`, `email`, `password`, `date_created`, `date_modified`, `status`) VALUES
(1, 'test', 'user', 'm', 'test.user@chromedia.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2012-07-23 14:17:03', '2012-07-23 14:17:03', 1);
INSERT INTO `accounts` (`id`, `first_name`, `last_name`, `middle_name`, `email`, `password`, `date_created`, `date_modified`, `status`) VALUES
(2, 'test-2', 'admin user', 'm', 'test.adminuser@chromedia.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '2012-07-23 14:17:03', '2012-07-23 14:17:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `account_applications`
--

DROP TABLE IF EXISTS `account_applications`;
CREATE TABLE IF NOT EXISTS `account_applications` (
  `account_id` bigint(20) unsigned NOT NULL,
  `application_id` int(10) unsigned NOT NULL,
  `token` varchar(64) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`account_id`,`application_id`),
  KEY `application_id` (`application_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

DROP TABLE IF EXISTS `applications`;
CREATE TABLE IF NOT EXISTS `applications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `domain` varchar(250) NOT NULL,
  `secret` varchar(64) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `name`, `domain`, `secret`, `status`) VALUES
(1, 'Test Application', 'testapplication.chromedia.com', '620de3ad308942cb43181ed4369e8d5e0c6147907b9c670c0e132988dcccc8f1', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `account_applications`
--
ALTER TABLE `account_applications`
  ADD CONSTRAINT `account_applications_ibfk_2` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `account_applications_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
