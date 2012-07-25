-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 25, 2012 at 09:40 AM
-- Server version: 5.1.63
-- PHP Version: 5.3.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `healthcareabroad`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE IF NOT EXISTS `admin_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `admin_user_type_id` int(3) unsigned NOT NULL,
  `status` tinyint(3) NOT NULL,
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
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `admin_user_roles`;
CREATE TABLE IF NOT EXISTS `admin_user_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_user_roles`
--

INSERT INTO `admin_user_roles` (`id`, `name`, `status`) VALUES
(1, 'dsfdsafdsf', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_types`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `admin_user_types`;
CREATE TABLE IF NOT EXISTS `admin_user_types` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_user_types`
--

INSERT INTO `admin_user_types` (`id`, `name`, `status`) VALUES
(1, 'Editor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_type_roles`
--
-- Creation: Jul 10, 2012 at 03:31 AM
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
-- Creation: Jul 11, 2012 at 06:36 AM
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `name`, `status`) VALUES
(1, 1, 'New York', 1),
(2, 1, 'California', 1),
(3, 2, 'Ottawa', 1),
(4, 2, 'Edmonton', 1);

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--
-- Creation: Jul 11, 2012 at 05:21 AM
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `status`) VALUES
(1, 'USA', 1),
(2, 'Canada', 1),
(3, 'Japan', 0),
(4, 'China', 1);

-- --------------------------------------------------------

--
-- Table structure for table `invitation_tokens`
--
-- Creation: Jul 17, 2012 at 08:50 AM
--

DROP TABLE IF EXISTS `invitation_tokens`;
CREATE TABLE IF NOT EXISTS `invitation_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(64) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=53 ;

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
(31, '688ae59d082ee69199b3c6aa02c82f29e67cfde67ca4bf455b9afea80a6d3d50', '2012-07-17 09:12:43', '2012-08-16 09:12:46', 1),
(32, 'ccada8f78a3cf6254308b8909fc1c66a717802d007127cfbe118e16509a719a6', '2012-07-17 09:25:30', '2012-08-16 09:25:33', 1),
(33, 'f8376f445a81ddda9d597de6f40246fb4759408eacc5e1d03149420e8b30668c', '2012-07-17 09:34:57', '2012-08-16 09:35:00', 1),
(34, '26aa832c5442435062c09b65930f6dbfef94f68687d2cd8e07a69506761dcab5', '2012-07-17 09:37:47', '2012-08-16 09:37:50', 1),
(35, 'b3aae45732c51c0afa42ab700b5f86c72f05f52b8bad30d9c9843bd817300405', '2012-07-17 09:41:21', '2012-08-16 09:41:23', 1),
(36, '8f036c0dbf6a9a549c22f278ba0f11c228dab7109139d427a3bed256574cf02d', '2012-07-17 09:42:47', '2012-08-16 09:42:50', 1),
(37, '592665d5325bc5ef1f52c72351a93ba918182c6e4635733f7735e90f04c778a8', '2012-07-17 09:43:54', '2012-08-16 09:43:56', 1),
(38, '8f570d379db6834fca21f0517ebe0e97768590ef065154cd2620c4844dbe91ba', '2012-07-17 09:44:34', '2012-08-16 09:44:37', 1),
(39, '5eee87178be3b95c37482d561bd6d88ea032ba295a26e3978d587832b008d813', '2012-07-17 09:53:43', '2012-08-16 09:53:45', 1),
(40, 'f513853cff65d18a55fed008266c558cad051c6f6fc0db01b1b85911d8e908c8', '2012-07-17 09:54:55', '2012-08-16 09:54:58', 1),
(41, 'a7a138fec07ee1f45f4959cf932d00f156a4d532808ee9158163e963c1fc3bbc', '2012-07-18 07:14:27', '2012-08-17 07:14:30', 1),
(42, '83c717cb67c3661fcccfd67e2bb37fc6a8d813b31f49926b5f046d95c92d77b2', '2012-07-19 01:01:16', '2012-08-18 01:01:18', 1),
(43, '7778a0cb59cf98c794b3300f77a3d79a6d75bdcebe1ce13aecab741f1f02e958', '2012-07-19 01:19:55', '2012-08-18 01:19:58', 1),
(44, '2f18094e5e49d7d9809ee59d27c52d7f7309fdf7c4b3d10b377ab1580ad69e5e', '2012-07-19 02:03:43', '2012-08-18 02:03:45', 1),
(45, 'd66d91e01ccdb0b212a20beb11f7b89534fe9e0ccaa2d4264868565f1d99c790', '2012-07-19 02:04:34', '2012-08-18 02:04:36', 1),
(46, 'f2473409cedbb3617ead97b3c7e90b7f2df8fe697174e4933198041da299700c', '2012-07-19 02:08:04', '2012-07-19 02:08:07', 1),
(47, '31c54a9d33f4772f663c9eafdc0c4e8922dfc594f8ae24c2e515d5b49840b03c', '2012-07-19 02:08:43', '2012-07-19 02:08:46', 1),
(48, '097b907ac120eb8cae45bba848c371326211bce230af3cba34ae5ee30143d08f', '2012-07-19 02:09:07', '2012-07-19 02:09:09', 1),
(49, 'a51017900b1715419989055fc1b4a0d1cd85a0a24c725d2ce090e034d66061f2', '2012-07-19 02:09:44', '2012-08-18 02:09:47', 1),
(50, 'd9d56d2cab41af423300b12e33471ae9cccc889a0b0e4e222e7ef66af8b1019a', '2012-07-19 02:11:03', '2012-08-18 02:11:06', 1),
(51, 'b08ebe327608bc9f0b7984e902ae5e493e2acc663d029459d5774106a376a999', '2012-07-19 02:16:49', '2012-08-18 02:16:51', 1),
(52, '01e2899265d04beb04691580363556baafce675ff2f77a237032eda490b00ac7', '2012-07-19 03:25:26', '2012-08-18 03:25:29', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--
-- Creation: Jul 13, 2012 at 12:31 AM
--

DROP TABLE IF EXISTS `listings`;
CREATE TABLE IF NOT EXISTS `listings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `medical_procedure_id` int(10) unsigned NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(100) DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` datetime NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `provider_id_2` (`provider_id`),
  KEY `procedure_id` (`medical_procedure_id`),
  KEY `medical_procedure_id` (`medical_procedure_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `provider_id`, `medical_procedure_id`, `title`, `description`, `logo`, `date_modified`, `date_created`, `status`) VALUES
(1, 1, 1, '1', 'sdfsdfdf', NULL, '2012-07-20 01:53:19', '2012-07-20 13:02:50', 1),
(2, 1, 1, 'sdfsdf', 'description', NULL, '2012-07-22 21:07:58', '2012-07-20 13:03:49', 1),
(3, 1, 2, '121', 'description 2', NULL, '2012-07-22 21:47:49', '2012-07-20 11:05:24', 1),
(4, 1, 1, 'sdfsdfsdfds', 'sdfsdfsdf', NULL, '2012-07-24 05:49:48', '2012-07-24 13:49:48', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listing_locations`
--
-- Creation: Jul 12, 2012 at 07:43 AM
--

DROP TABLE IF EXISTS `listing_locations`;
CREATE TABLE IF NOT EXISTS `listing_locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` bigint(20) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `address` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`),
  KEY `country_id` (`country_id`),
  KEY `city_id` (`city_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=58 ;

--
-- Dumping data for table `listing_locations`
--

INSERT INTO `listing_locations` (`id`, `listing_id`, `country_id`, `city_id`, `address`) VALUES
(54, 1, 2, 3, 'sdfsdf'),
(55, 2, 2, 4, 'sdfsdfsdf'),
(56, 3, 1, 1, 'asdfa'),
(57, 4, 1, 2, 'sdfsdf');

-- --------------------------------------------------------

--
-- Table structure for table `listing_photos`
--
-- Creation: Jul 11, 2012 at 06:39 AM
--

DROP TABLE IF EXISTS `listing_photos`;
CREATE TABLE IF NOT EXISTS `listing_photos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `listing_id` bigint(20) unsigned NOT NULL,
  `photo_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `listing_id` (`listing_id`,`photo_id`),
  KEY `photo_id` (`photo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `listing_properties`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `listing_properties`;
CREATE TABLE IF NOT EXISTS `listing_properties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `listing_properties`
--

INSERT INTO `listing_properties` (`id`, `name`, `status`) VALUES
(1, 'Country', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listing_property_choices`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `listing_property_choices`;
CREATE TABLE IF NOT EXISTS `listing_property_choices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `listing_property_id` int(10) unsigned NOT NULL,
  `value` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `listing_property_id` (`listing_property_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `listing_property_choices`
--

INSERT INTO `listing_property_choices` (`id`, `listing_property_id`, `value`, `status`) VALUES
(1, 1, 'USA', 1),
(2, 1, 'Canada', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listing_property_details`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `listing_property_details`;
CREATE TABLE IF NOT EXISTS `listing_property_details` (
  `listing_id` bigint(20) unsigned NOT NULL,
  `listing_property_id` int(10) unsigned NOT NULL,
  `listing_property_choice_id` bigint(20) unsigned NOT NULL,
  `value` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`listing_id`,`listing_property_id`,`listing_property_choice_id`),
  KEY `listing_property_id` (`listing_property_id`),
  KEY `listing_property_choice_id` (`listing_property_choice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `medical_procedures`
--
-- Creation: Jul 11, 2012 at 07:47 AM
--

DROP TABLE IF EXISTS `medical_procedures`;
CREATE TABLE IF NOT EXISTS `medical_procedures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `medical_procedures`
--

INSERT INTO `medical_procedures` (`id`, `name`, `status`) VALUES
(1, 'Hearth', 1),
(2, 'Liver Churva', 1),
(3, 'dsfdfdf updated', 1),
(4, 'tesdgf sae', 1),
(5, 'adelbert procedure', 0),
(6, 'berty proce', 1),
(7, 'dfgfdgdfg updated', 0),
(8, 'sdfds', 1),
(9, 'testme', 1),
(10, 'sdfsadfdsf', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medical_procedure_tags`
--
-- Creation: Jul 20, 2012 at 06:48 AM
--

DROP TABLE IF EXISTS `medical_procedure_tags`;
CREATE TABLE IF NOT EXISTS `medical_procedure_tags` (
  `medical_procedure_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`medical_procedure_id`,`tag_id`),
  KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `medical_procedure_tags`
--

INSERT INTO `medical_procedure_tags` (`medical_procedure_id`, `tag_id`) VALUES
(1, 1),
(3, 1),
(4, 1),
(6, 1),
(7, 1),
(9, 1),
(10, 1),
(1, 3),
(3, 3),
(4, 3),
(5, 3),
(10, 3),
(3, 4),
(4, 4),
(7, 4),
(9, 5);

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--
-- Creation: Jul 11, 2012 at 06:18 AM
--

DROP TABLE IF EXISTS `photos`;
CREATE TABLE IF NOT EXISTS `photos` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(50) NOT NULL,
  `caption` varchar(100) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--
-- Creation: Jul 10, 2012 at 06:08 AM
--

DROP TABLE IF EXISTS `providers`;
CREATE TABLE IF NOT EXISTS `providers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(50) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=49 ;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `name`, `description`, `logo`, `slug`, `status`) VALUES
(1, 'Belo Churvaness', 'The quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.', '/pathtologo/filename.jpg', 'belo_churva', 1),
(2, 'Kalayan Chenes', 'Lorem ipsum dolor sit amit. Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.', '/pathtolog/filename1.jpg', 'kalayan_chenes', 1),
(3, 'Belo Medical Group', 'offers cosmetic surgery', '', 'offers cosmetic surgery', 1),
(4, 'Belo Medical Group', 'offers cosmetic surgery', '', 'offers cosmetic surgery', 1),
(35, 'MyHealth Clinic', 'offers diagnostic exams', '', 'offers diagnostic exams', 1),
(41, 'Marie France', 'asd asd', '', 'asd asd', 1),
(42, 'Marie France', 'slimming and whitening', '', 'slimming and whitening', 1),
(43, 'jasdj jasd', 'njkasd', '', 'njkasd', 1),
(44, 'asd jasdk', 'ka', '', 'ka', 1),
(45, 'asd asdal', 'jas', '', 'jas', 1),
(46, 'Marie France', 'asd', '', 'asd', 1),
(47, 'Marie France', 'asd', '', 'asd', 1),
(48, 'MyHealth Clinic', 'asdasdasd asda ', '', 'asdasdasd asda ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `provider_invitations`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `provider_invitations`;
CREATE TABLE IF NOT EXISTS `provider_invitations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(250) NOT NULL,
  `message` varchar(400) NOT NULL,
  `name` varchar(250) NOT NULL,
  `invitation_token_id` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `invitation_token_id` (`invitation_token_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `provider_invitations`
--

INSERT INTO `provider_invitations` (`id`, `email`, `message`, `name`, `invitation_token_id`, `date_created`, `status`) VALUES
(1, 'alnie.jacobe@chromedia.com', 'Message-ID: <1342659679.50075c5f489da@healthcareabroad.local>\r\nDate: Thu, 19 Jul 2012 09:01:19 +0800\r\nSubject: Activate your account with HealthCareAbroad\r\nFrom: alnie.jacobe@chromedia.com\r\nTo: alnie.jacobe@chromedia.com\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n=09\r\n   Dear alnie jacobe,\r\n=09\r\n   To complete your registration, p=\r\nleas', 'alnie jacobe', 42, '2012-07-19 01:01:16', 1),
(2, 'alnie.jacobe@chromedia.com', 'Message-ID: <1342660798.500760becb5bf@healthcareabroad.local>\r\nDate: Thu, 19 Jul 2012 09:19:58 +0800\r\nSubject: Activate your account with HealthCareAbroad\r\nFrom: alnie.jacobe@chromedia.com\r\nTo: alnie.jacobe@chromedia.com\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n=09\r\n   Dear alnie jacobe,\r\n=09\r\n   To complete your registration, p=\r\nleas', 'alnie jacobe', 43, '2012-07-19 01:19:56', 1),
(3, 'asdkj.asd@yahoo.com', 'Message-ID: <1342663425.50076b01d56a7@healthcareabroad.local>\r\nDate: Thu, 19 Jul 2012 10:03:45 +0800\r\nSubject: Activate your account with HealthCareAbroad\r\nFrom: alnie.jacobe@chromedia.com\r\nTo: asdkj.asd@yahoo.com\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n=09\r\n Dear alnie jacobe,\r\n=09\r\n   To complete your registration, p=\r\nlease click', 'alnie jacobe', 44, '2012-07-19 02:03:43', 0),
(4, 'alnie.jacobe@chromedia.com', 'Message-ID: <1342663477.50076b352e381@healthcareabroad.local>\r\nDate: Thu, 19 Jul 2012 10:04:37 +0800\r\nSubject: Activate your account with HealthCareAbroad\r\nFrom: alnie.jacobe@chromedia.com\r\nTo: alnie.jacobe@chromedia.com\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n=09\r\n   Dear alnie jacobe,\r\n=09\r\n   To complete your registration, p=\r\nleas', 'alnie jacobe', 45, '2012-07-19 02:04:34', 0),
(5, 'alnie.jacobe@chromedia.com', 'Message-ID: <1342664211.50076e13f2659@healthcareabroad.local>\r\nDate: Thu, 19 Jul 2012 10:16:51 +0800\r\nSubject: Activate your account with HealthCareAbroad\r\nFrom: alnie.jacobe@chromedia.com\r\nTo: alnie.jacobe@chromedia.com\r\nMIME-Version: 1.0\r\nContent-Type: text/plain; charset=utf-8\r\nContent-Transfer-Encoding: quoted-printable\r\n\r\n=09\r\n   Dear alnie jacobe,\r\n=09\r\n   To complete your registration, p=\r\nleas', 'alnie jacobe', 51, '2012-07-19 02:16:49', 0);

-- --------------------------------------------------------

--
-- Table structure for table `provider_users`
--
-- Creation: Jul 10, 2012 at 06:10 AM
--

DROP TABLE IF EXISTS `provider_users`;
CREATE TABLE IF NOT EXISTS `provider_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `provider_user_type_id` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `provider_id` (`provider_id`),
  KEY `provider_user_type_id` (`provider_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provider_users`
--

INSERT INTO `provider_users` (`account_id`, `provider_id`, `provider_user_type_id`, `date_created`, `status`) VALUES
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
-- Table structure for table `provider_user_invitations`
--
-- Creation: Jul 18, 2012 at 07:04 AM
--

DROP TABLE IF EXISTS `provider_user_invitations`;
CREATE TABLE IF NOT EXISTS `provider_user_invitations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `invitation_token_id` int(10) unsigned DEFAULT NULL,
  `email` varchar(250) NOT NULL,
  `message` varchar(400) NOT NULL,
  `first_name` varchar(250) NOT NULL,
  `middle_name` varchar(250) NOT NULL,
  `last_name` varchar(250) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `invitation_token_id` (`invitation_token_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_roles`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `provider_user_roles`;
CREATE TABLE IF NOT EXISTS `provider_user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `provider_user_roles`
--

INSERT INTO `provider_user_roles` (`id`, `name`, `description`, `status`) VALUES
(1, 'LISTING_CREATOR', 'Can create listing', 1),
(2, 'LISTING_EDITOR', 'Can edit listing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_types`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `provider_user_types`;
CREATE TABLE IF NOT EXISTS `provider_user_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `provider_user_types`
--

INSERT INTO `provider_user_types` (`id`, `provider_id`, `name`, `status`) VALUES
(1, 1, 'Paragkagud', 1);

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_type_roles`
--
-- Creation: Jul 10, 2012 at 03:31 AM
--

DROP TABLE IF EXISTS `provider_user_type_roles`;
CREATE TABLE IF NOT EXISTS `provider_user_type_roles` (
  `provider_user_type_id` int(10) unsigned NOT NULL,
  `provider_user_role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`provider_user_type_id`,`provider_user_role_id`),
  KEY `provider_user_role_id` (`provider_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--
-- Creation: Jul 20, 2012 at 06:48 AM
--

DROP TABLE IF EXISTS `tags`;
CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `type` smallint(5) unsigned NOT NULL,
  `status` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `name`, `type`, `status`) VALUES
(1, 'Liposunction updated', 1, 1),
(2, 'Breast Enhancement', 2, 1),
(3, 'Face surgery', 1, 1),
(4, 'Butt Surgery', 1, 1),
(5, 'testtagwithtype', 1, 1),
(6, 'tagasClass', 1, 1),
(7, 'sdfs dsfdf', 1, 1),
(8, 'sdfdf', 1, 1),
(9, 'sstatuscheck active', 1, 0);

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
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_6` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listings_ibfk_7` FOREIGN KEY (`medical_procedure_id`) REFERENCES `medical_procedures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listing_locations`
--
ALTER TABLE `listing_locations`
  ADD CONSTRAINT `listing_locations_ibfk_9` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_locations_ibfk_7` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_locations_ibfk_8` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listing_photos`
--
ALTER TABLE `listing_photos`
  ADD CONSTRAINT `listing_photos_ibfk_2` FOREIGN KEY (`photo_id`) REFERENCES `photos` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_photos_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `listing_property_choices`
--
ALTER TABLE `listing_property_choices`
  ADD CONSTRAINT `listing_property_choices_ibfk_1` FOREIGN KEY (`listing_property_id`) REFERENCES `listing_properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listing_property_details`
--
ALTER TABLE `listing_property_details`
  ADD CONSTRAINT `listing_property_details_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_property_details_ibfk_2` FOREIGN KEY (`listing_property_id`) REFERENCES `listing_properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_property_details_ibfk_3` FOREIGN KEY (`listing_property_choice_id`) REFERENCES `listing_property_choices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_procedure_tags`
--
ALTER TABLE `medical_procedure_tags`
  ADD CONSTRAINT `medical_procedure_tags_ibfk_2` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `medical_procedure_tags_ibfk_1` FOREIGN KEY (`medical_procedure_id`) REFERENCES `medical_procedures` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_invitations`
--
ALTER TABLE `provider_invitations`
  ADD CONSTRAINT `provider_invitations_ibfk_1` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `provider_users`
--
ALTER TABLE `provider_users`
  ADD CONSTRAINT `provider_users_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `provider_users_ibfk_2` FOREIGN KEY (`provider_user_type_id`) REFERENCES `provider_user_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `provider_user_invitations`
--
ALTER TABLE `provider_user_invitations`
  ADD CONSTRAINT `provider_user_invitations_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `provider_user_invitations_ibfk_2` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `provider_user_types`
--
ALTER TABLE `provider_user_types`
  ADD CONSTRAINT `provider_user_types_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_user_type_roles`
--
ALTER TABLE `provider_user_type_roles`
  ADD CONSTRAINT `provider_user_type_roles_ibfk_1` FOREIGN KEY (`provider_user_type_id`) REFERENCES `provider_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `provider_user_type_roles_ibfk_2` FOREIGN KEY (`provider_user_role_id`) REFERENCES `provider_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
