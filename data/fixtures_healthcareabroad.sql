-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2012 at 10:55 AM
-- Server version: 5.1.63
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `fixtures_healthcareabroad`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE IF NOT EXISTS `admin_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `admin_user_type_id` int(3) unsigned NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `admin_user_type_id` (`admin_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`account_id`, `admin_user_type_id`, `status`) VALUES
(3, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_roles`
--

DROP TABLE IF EXISTS `admin_user_roles`;
CREATE TABLE IF NOT EXISTS `admin_user_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_user_roles`
--

INSERT INTO `admin_user_roles` (`id`, `name`, `status`) VALUES
(1, 'dsfdsafdsf', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_types`
--

DROP TABLE IF EXISTS `admin_user_types`;
CREATE TABLE IF NOT EXISTS `admin_user_types` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` smallint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_user_types`
--

INSERT INTO `admin_user_types` (`id`, `name`, `status`) VALUES
(1, 'Editor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_type_roles`
--

DROP TABLE IF EXISTS `admin_user_type_roles`;
CREATE TABLE IF NOT EXISTS `admin_user_type_roles` (
  `admin_user_type_id` int(3) unsigned NOT NULL,
  `admin_user_role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`admin_user_type_id`,`admin_user_role_id`),
  KEY `admin_user_role_id` (`admin_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `name`, `slug`, `status`) VALUES
(1, 1, 'New York', '', 1),
(2, 1, 'California', '', 1),
(3, 2, 'Ottawa', '', 1),
(4, 2, 'Edmonton', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

DROP TABLE IF EXISTS `contact_details`;
CREATE TABLE IF NOT EXISTS `contact_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` smallint(1) unsigned NOT NULL,
  `value` varchar(50) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `slug`, `status`) VALUES
(1, 'USA', '', 1),
(2, 'Canada', '', 1),
(3, 'Japan', '', 0),
(4, 'China', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
CREATE TABLE IF NOT EXISTS `institutions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(100) NOT NULL,
  `address1` text NOT NULL,
  `address2` text NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `name`, `description`, `logo`, `address1`, `address2`, `city_id`, `country_id`, `date_modified`, `date_created`, `slug`, `status`) VALUES
(1, 'Belo Churvaness', 'The quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.', '/pathtologo/filename.jpg', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(2, 'Kalayan Chenes', 'Lorem ipsum dolor sit amit. Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.', '/pathtolog/filename1.jpg', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(3, 'Belo Medical Group', 'offers cosmetic surgery', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(4, 'Belo Medical Group', 'offers cosmetic surgery', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(35, 'MyHealth Clinic', 'offers diagnostic exams', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(41, 'Marie France', 'asd asd', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(42, 'Marie France', 'slimming and whitening', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(43, 'jasdj jasd', 'njkasd', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(44, 'asd jasdk', 'ka', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(45, 'asd asdal', 'jas', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(46, 'Marie France', 'asd', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(47, 'Marie France', 'asd', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0),
(48, 'MyHealth Clinic', 'asdasdasd asda ', '', '', '', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `institution_contact_details`
--

DROP TABLE IF EXISTS `institution_contact_details`;
CREATE TABLE IF NOT EXISTS `institution_contact_details` (
  `institution_id` int(10) unsigned NOT NULL,
  `contact_detail_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`institution_id`,`contact_detail_id`),
  KEY `contact_detail_id` (`contact_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_groups`
--

DROP TABLE IF EXISTS `institution_groups`;
CREATE TABLE IF NOT EXISTS `institution_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_invitations`
--

DROP TABLE IF EXISTS `institution_invitations`;
CREATE TABLE IF NOT EXISTS `institution_invitations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` char(100) NOT NULL,
  `message` text NOT NULL,
  `name` varchar(100) NOT NULL,
  `invitation_token_id` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invitation_token_id` (`invitation_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_media`
--

DROP TABLE IF EXISTS `institution_media`;
CREATE TABLE IF NOT EXISTS `institution_media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `media_id` bigint(20) unsigned NOT NULL,
  `type` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_centers`
--

DROP TABLE IF EXISTS `institution_medical_centers`;
CREATE TABLE IF NOT EXISTS `institution_medical_centers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `medical_center_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`,`medical_center_id`),
  KEY `medical_center_id` (`medical_center_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_procedures`
--

DROP TABLE IF EXISTS `institution_medical_procedures`;
CREATE TABLE IF NOT EXISTS `institution_medical_procedures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `medical_procedure_id` int(10) unsigned NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`,`medical_procedure_id`),
  KEY `medical_procedure_id` (`medical_procedure_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_procedure_types`
--

DROP TABLE IF EXISTS `institution_medical_procedure_types`;
CREATE TABLE IF NOT EXISTS `institution_medical_procedure_types` (
  `institution_medical_center_id` int(11) unsigned NOT NULL,
  `medical_procedure_type_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`institution_medical_center_id`,`medical_procedure_type_id`),
  KEY `medical_procedure_type_id` (`medical_procedure_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_users`
--

DROP TABLE IF EXISTS `institution_users`;
CREATE TABLE IF NOT EXISTS `institution_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_user_type_id` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_user_type_id` (`institution_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institution_users`
--

INSERT INTO `institution_users` (`account_id`, `institution_id`, `institution_user_type_id`, `date_created`, `status`) VALUES
(2, 1, 1, '2012-07-13 02:37:50', 1),
(4, 1, 1, '2012-07-18 00:34:19', 1),
(5, 1, 1, '2012-07-18 06:02:30', 1),
(6, 1, 1, '2012-07-18 07:15:04', 1),
(36, 35, 1, '2012-07-19 03:25:36', 1),
(42, 41, 1, '2012-07-19 03:38:31', 1),
(43, 42, 1, '2012-07-19 03:39:11', 1),
(44, 43, 1, '2012-07-19 06:43:07', 1),
(45, 44, 1, '2012-07-19 06:43:47', 1),
(46, 47, 1, '2012-07-19 06:46:32', 1),
(47, 48, 1, '2012-07-19 06:52:55', 1),
(49, 1, 1, '2012-07-19 08:59:54', 1);

-- --------------------------------------------------------

--
-- Table structure for table `institution_user_invitations`
--

DROP TABLE IF EXISTS `institution_user_invitations`;
CREATE TABLE IF NOT EXISTS `institution_user_invitations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `invitation_token_id` int(10) unsigned DEFAULT NULL,
  `email` char(100) NOT NULL,
  `message` text NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `invitation_token_id` (`invitation_token_id`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_user_roles`
--

DROP TABLE IF EXISTS `institution_user_roles`;
CREATE TABLE IF NOT EXISTS `institution_user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institution_user_roles`
--

INSERT INTO `institution_user_roles` (`id`, `name`, `description`, `status`) VALUES
(1, 'LISTING_CREATOR', 'Can create listing', 1),
(2, 'LISTING_EDITOR', 'Can edit listing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `institution_user_types`
--

DROP TABLE IF EXISTS `institution_user_types`;
CREATE TABLE IF NOT EXISTS `institution_user_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institution_user_types`
--

INSERT INTO `institution_user_types` (`id`, `institution_id`, `name`, `status`) VALUES
(1, 1, 'Paragkagud', 1);

-- --------------------------------------------------------

--
-- Table structure for table `institution_user_type_roles`
--

DROP TABLE IF EXISTS `institution_user_type_roles`;
CREATE TABLE IF NOT EXISTS `institution_user_type_roles` (
  `institution_user_type_id` int(10) unsigned NOT NULL,
  `institution_user_role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`institution_user_type_id`,`institution_user_role_id`),
  KEY `institution_user_role_id` (`institution_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `invitation_tokens`
--

DROP TABLE IF EXISTS `invitation_tokens`;
CREATE TABLE IF NOT EXISTS `invitation_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `invitation_tokens`
--

INSERT INTO `invitation_tokens` (`id`, `token`, `date_created`, `expiration_date`, `status`) VALUES
(1, '9f5bfb96ff6ae7af18b2a4a9948e1f45', '2012-07-11 08:09:30', '2012-07-17 08:09:33', 1),
(2, '5a1030be9fc8aea6a6c0febb846cc924', '2012-07-11 08:10:57', '2012-07-17 08:10:59', 1),
(3, 'f3c027b60b18b90e3e783ae44d85ee7f', '2012-07-11 08:14:01', '2012-07-17 08:14:03', 1),
(4, '0124bd3ed70377b6ad5ce15cf02ef63a', '2012-07-12 03:56:17', '2012-07-18 03:56:19', 1),
(5, '94f48db50d5f778e12cba69f07f3da52', '2012-07-12 03:56:36', '2012-07-18 03:56:39', 1),
(6, 'ceac3c6209e4ce33907561fdc4d66cbd', '2012-07-12 06:49:59', '2012-07-18 06:50:01', 1),
(7, 'a1afdcfddb19855271bc3dd8d3db6bbe', '2012-07-12 06:50:21', '2012-07-18 06:50:24', 1),
(8, 'eabba4c90564d3ae81888767c8dca2e7', '2012-07-12 06:54:14', '2012-07-18 06:54:16', 1),
(9, 'e0696f6b055a32b7d0e60957fada89bf', '2012-07-12 06:56:33', '2012-07-18 06:56:36', 1),
(10, 'dd89fc357f5346d9d193cc4636473f6b', '2012-07-12 07:03:51', '2012-07-18 07:03:54', 1),
(11, '744defe43d46b23da5ffc1e1a56ed2d9', '2012-07-12 07:05:27', '2012-07-18 07:05:29', 1),
(12, '385a8f01fa9d965a67a84e17e46d3596', '2012-07-12 07:06:19', '2012-07-18 07:06:22', 1),
(13, '4eae40ec714061512434d99e02db54c4', '2012-07-12 07:07:38', '2012-07-18 07:07:41', 1),
(14, '9d0030d31acd9fff383109566478cce8', '2012-07-12 07:07:44', '2012-07-18 07:07:46', 1),
(15, '560536bb96bcd5c31592711c14da40f5', '2012-07-12 07:09:13', '2012-07-18 07:09:16', 1),
(16, '292067c6590f8d4f39b220d137f0f828', '2012-07-12 07:16:42', '2012-07-18 07:16:44', 1),
(17, 'dd89fc357f5346d9d193cc4636473f6b', '2012-07-12 07:19:51', '2012-07-18 07:19:54', 1),
(18, 'b49f42a4c917520c51e357d368674606', '2012-07-12 07:21:52', '2012-07-18 07:21:55', 1),
(19, '660e2efd1ab808e4816dae7428ccc726', '2012-07-12 07:27:40', '2012-07-18 07:27:43', 1),
(20, 'fe06d7b052b74935532f268539a9e3cd', '2012-07-12 07:27:42', '2012-07-18 07:27:45', 1),
(21, 'f5d075dd27e0157de9dea8785619f7f0', '2012-07-17 08:47:46', '2012-08-16 08:47:49', 1),
(22, '6dc69b4af62656e3faa085f5ba487abb', '2012-07-17 08:49:27', '2012-08-16 08:49:30', 1),
(23, 'c1913a1ed7780224b505aed516e021f7b9220550a63593bedbdb4ed29c7a18b1', '2012-07-17 08:57:00', '2012-08-16 08:57:03', 1),
(24, 'e66f6c9d2be3ad999fd72572bb85095f58134c439cb402f1bb22c256f35afa1d', '2012-07-17 09:00:50', '2012-08-16 09:00:53', 1),
(25, '1e28184f7aa3140a52672e262436dd8fd4da330c86ae3a7378b7b008e491c7d1', '2012-07-17 09:02:14', '2012-08-16 09:02:17', 1),
(26, '849be1f8beacfa5b2b68f6f35a0fbb23ab06b7c3d6ec3f7e2f7db2c6b25c39a7', '2012-07-17 09:04:11', '2012-08-16 09:04:13', 1),
(27, '3749d390fa13a3d70c02ff9e0e8e2e5abdefc136b16d17e4ad3df636a8550d89', '2012-07-17 09:04:26', '2012-08-16 09:04:29', 1),
(28, 'daf6e6b2dd32239c7651041c301eebae4a2c36fe1eaa6e7b3a39ba88bbb1764e', '2012-07-17 09:06:48', '2012-08-16 09:06:51', 1),
(29, '87a7d92394a02b6dff62725783dceb4fcf6b8642fb67dd6cc0a66b3b346107e6', '2012-07-17 09:07:19', '2012-08-16 09:07:22', 1),
(30, '1e28184f7aa3140a52672e262436dd8fd4da330c86ae3a7378b7b008e491c7d1', '2012-07-17 09:08:14', '2012-08-16 09:08:17', 1),
(42, '83c717cb67c3661fcccfd67e2bb37fc6a8d813b31f49926b5f046d95c92d77b2', '2012-07-19 01:01:16', '2012-08-18 01:01:18', 1),
(43, '7778a0cb59cf98c794b3300f77a3d79a6d75bdcebe1ce13aecab741f1f02e958', '2012-07-19 01:19:55', '2012-08-18 01:19:58', 1),
(44, '2f18094e5e49d7d9809ee59d27c52d7f7309fdf7c4b3d10b377ab1580ad69e5e', '2012-07-19 02:03:43', '2012-08-18 02:03:45', 1),
(45, 'd66d91e01ccdb0b212a20beb11f7b89534fe9e0ccaa2d4264868565f1d99c790', '2012-07-19 02:04:34', '2012-08-18 02:04:36', 1),
(46, 'f2473409cedbb3617ead97b3c7e90b7f2df8fe697174e4933198041da299700c', '2012-07-19 02:08:04', '2012-07-19 02:08:07', 1),
(51, 'b08ebe327608bc9f0b7984e902ae5e493e2acc663d029459d5774106a376a999', '2012-07-19 02:16:49', '2012-08-18 02:16:51', 1);

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(100) NOT NULL,
  `caption` varchar(100) NOT NULL,
  `type` smallint(2) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `medical_centers`
--

DROP TABLE IF EXISTS `medical_centers`;
CREATE TABLE IF NOT EXISTS `medical_centers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medical_centers`
--

INSERT INTO `medical_centers` (`id`, `name`, `description`, `date_created`, `slug`, `status`) VALUES
(1, '1st Medical Center', 'The very first one!', '2012-07-30 00:34:30', '1st-medical-center', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medical_procedures`
--

DROP TABLE IF EXISTS `medical_procedures`;
CREATE TABLE IF NOT EXISTS `medical_procedures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `medical_procedure_type_id` int(10) unsigned NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_procedure_type_id` (`medical_procedure_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `medical_procedures`
--

INSERT INTO `medical_procedures` (`id`, `medical_procedure_type_id`, `name`, `slug`, `status`) VALUES
(1, 1, 'sdfsdfsdf updated', '', 0),
(2, 1, 'dsfsdaf sdf', '', 1),
(3, 1, 'test tests', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medical_procedure_types`
--

DROP TABLE IF EXISTS `medical_procedure_types`;
CREATE TABLE IF NOT EXISTS `medical_procedure_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `medical_procedure_types`
--

INSERT INTO `medical_procedure_types` (`id`, `name`, `description`, `date_modified`, `date_created`, `slug`, `status`) VALUES
(1, 'medType1', 'sdfsdaf sadf sadf sadfsd f', '2012-07-30 00:00:10', '2012-07-29 16:00:00', 'med-type1', 1),
(2, 'inactiveType', 'sdf sdf sdfsd f', '2012-07-30 02:24:25', '2012-07-29 16:00:00', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medical_procedure_type_centers`
--

DROP TABLE IF EXISTS `medical_procedure_type_centers`;
CREATE TABLE IF NOT EXISTS `medical_procedure_type_centers` (
  `medical_procedure_type_id` int(10) unsigned NOT NULL,
  `medical_center_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`medical_procedure_type_id`,`medical_center_id`),
  KEY `medical_center_id` (`medical_center_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `medical_term_suggestions`
--

DROP TABLE IF EXISTS `medical_term_suggestions`;
CREATE TABLE IF NOT EXISTS `medical_term_suggestions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `medical_term_suggestion_details`
--

DROP TABLE IF EXISTS `medical_term_suggestion_details`;
CREATE TABLE IF NOT EXISTS `medical_term_suggestion_details` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `medical_term_suggestion_id` int(10) unsigned NOT NULL,
  `type` smallint(3) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `medical_term_suggestion_id` (`medical_term_suggestion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` smallint(1) unsigned NOT NULL,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `type`, `slug`, `status`) VALUES
(1, 'Liposunction updated', 1, '', 1),
(2, 'Breast Enhancement', 2, '', 0),
(3, 'Face surgery', 1, '', 0),
(4, 'Butt Surgery', 1, '', 1),
(5, 'testtagwithtype', 1, '', 1),
(6, 'tagasClass', 1, '', 1),
(7, 'sdfs dsfdf', 1, '', 1),
(8, 'sdfdf', 1, '', 1),
(9, 'sstatuscheck active', 1, '', 1),
(10, 'sdfsdfsdftest', 1, '', 1),
(11, 'dfgfdgfdg', 1, '', 1),
(12, 'sdfsdf', 1, '', 1),
(13, 'test', 1, '', 1),
(14, 'zerovalue', 1, '', 1),
(15, 'berbert', 1, '', 1),
(16, 'berbertbert', 1, '', 1);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`admin_user_type_id`) REFERENCES `admin_user_types` (`id`);

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_contact_details`
--
ALTER TABLE `institution_contact_details`
  ADD CONSTRAINT `institution_contact_details_ibfk_3` FOREIGN KEY (`contact_detail_id`) REFERENCES `contact_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_contact_details_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_invitations`
--
ALTER TABLE `institution_invitations`
  ADD CONSTRAINT `institution_invitations_ibfk_1` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `institution_media`
--
ALTER TABLE `institution_media`
  ADD CONSTRAINT `institution_media_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_centers`
--
ALTER TABLE `institution_medical_centers`
  ADD CONSTRAINT `institution_medical_centers_ibfk_2` FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_centers_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_procedures`
--
ALTER TABLE `institution_medical_procedures`
  ADD CONSTRAINT `institution_medical_procedures_ibfk_2` FOREIGN KEY (`medical_procedure_id`) REFERENCES `medical_procedures` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_procedures_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_procedure_types`
--
ALTER TABLE `institution_medical_procedure_types`
  ADD CONSTRAINT `institution_medical_procedure_types_ibfk_2` FOREIGN KEY (`medical_procedure_type_id`) REFERENCES `medical_procedure_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_procedure_types_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_users`
--
ALTER TABLE `institution_users`
  ADD CONSTRAINT `institution_users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_users_ibfk_2` FOREIGN KEY (`institution_user_type_id`) REFERENCES `institution_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_user_invitations`
--
ALTER TABLE `institution_user_invitations`
  ADD CONSTRAINT `institution_user_invitations_ibfk_2` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_user_invitations_ibfk_3` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_user_types`
--
ALTER TABLE `institution_user_types`
  ADD CONSTRAINT `institution_user_types_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_user_type_roles`
--
ALTER TABLE `institution_user_type_roles`
  ADD CONSTRAINT `institution_user_type_roles_ibfk_2` FOREIGN KEY (`institution_user_role_id`) REFERENCES `institution_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_user_type_roles_ibfk_1` FOREIGN KEY (`institution_user_type_id`) REFERENCES `institution_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_procedures`
--
ALTER TABLE `medical_procedures`
  ADD CONSTRAINT `medical_procedures_ibfk_1` FOREIGN KEY (`medical_procedure_type_id`) REFERENCES `medical_procedure_types` (`id`);

--
-- Constraints for table `medical_procedure_type_centers`
--
ALTER TABLE `medical_procedure_type_centers`
  ADD CONSTRAINT `medical_procedure_type_centers_ibfk_2` FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medical_procedure_type_centers_ibfk_1` FOREIGN KEY (`medical_procedure_type_id`) REFERENCES `medical_procedure_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_term_suggestion_details`
--
ALTER TABLE `medical_term_suggestion_details`
  ADD CONSTRAINT `medical_term_suggestion_details_ibfk_1` FOREIGN KEY (`medical_term_suggestion_id`) REFERENCES `medical_term_suggestions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
