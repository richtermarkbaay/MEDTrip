-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 11, 2012 at 04:59 PM
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

CREATE TABLE IF NOT EXISTS `admin_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `admin_user_type_id` int(3) unsigned NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `admin_user_type_id` (`admin_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_roles`
--

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

CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `invitation_tokens`
--

CREATE TABLE IF NOT EXISTS `invitation_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `invitation_tokens`
--

INSERT INTO `invitation_tokens` (`id`, `token`, `date_created`, `expiration_date`, `status`) VALUES
(1, '9f5bfb96ff6ae7af18b2a4a9948e1f45', '2012-07-11 08:09:30', '2012-07-17 08:09:33', 1),
(2, '5a1030be9fc8aea6a6c0febb846cc924', '2012-07-11 08:10:57', '2012-07-17 08:10:59', 1),
(3, 'f3c027b60b18b90e3e783ae44d85ee7f', '2012-07-11 08:14:01', '2012-07-17 08:14:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listings`
--

CREATE TABLE IF NOT EXISTS `listings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `medical_procedure_id` int(10) unsigned NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(100) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_created` datetime NOT NULL,
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`),
  KEY `provider_id_2` (`provider_id`),
  KEY `procedure_id` (`medical_procedure_id`),
  KEY `medical_procedure_id` (`medical_procedure_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `listings`
--

INSERT INTO `listings` (`id`, `provider_id`, `medical_procedure_id`, `title`, `description`, `logo`, `date_modified`, `date_created`, `status`) VALUES
(1, 1, 1, 'dsfsdfsdf', 'Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.', '', '0000-00-00 00:00:00', '2012-07-10 13:31:27', 1),
(2, 1, 1, 'Breast Enhancement', 'Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.', '', '0000-00-00 00:00:00', '2012-07-10 13:31:27', 1),
(3, 1, 1, 'sdfsdfsdf', 'sdfsdfdf', '', '2012-07-10 08:53:13', '2012-07-10 16:53:13', 1);

-- --------------------------------------------------------

--
-- Table structure for table `listing_locations`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `listing_photos`
--

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

CREATE TABLE IF NOT EXISTS `medical_procedures` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `medical_procedures`
--

INSERT INTO `medical_procedures` (`id`, `name`, `status`) VALUES
(1, 'Hearth ', 1);

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

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

CREATE TABLE IF NOT EXISTS `providers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(50) NOT NULL,
  `slug` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `name`, `description`, `logo`, `slug`, `status`) VALUES
(1, 'Belo Churva', 'The quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.', '/pathtologo/filename.jpg', 'belo_churva', 1),
(2, 'Kalayan Chenes', 'Lorem ipsum dolor sit amit. Lorem ipsum dolor sit amit.Lorem ipsum dolor sit amit.', '/pathtolog/filename1.jpg', 'kalayan_chenes', 1);

-- --------------------------------------------------------

--
-- Table structure for table `provider_invitations`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `provider_users`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_invitations`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_roles`
--

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

CREATE TABLE IF NOT EXISTS `provider_user_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_type_roles`
--

CREATE TABLE IF NOT EXISTS `provider_user_type_roles` (
  `provider_user_type_id` int(10) unsigned NOT NULL,
  `provider_user_role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`provider_user_type_id`,`provider_user_role_id`),
  KEY `provider_user_role_id` (`provider_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
  ADD CONSTRAINT `listings_ibfk_5` FOREIGN KEY (`medical_procedure_id`) REFERENCES `medical_procedures` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `listings_ibfk_3` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `listing_locations`
--
ALTER TABLE `listing_locations`
  ADD CONSTRAINT `listing_locations_ibfk_6` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_locations_ibfk_4` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_locations_ibfk_5` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;

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

