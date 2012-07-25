-- phpMyAdmin SQL Dump
-- version 3.3.9.2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 04, 2012 at 05:01 PM
-- Server version: 5.5.9
-- PHP Version: 5.3.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `healthcareabroad`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--
-- Creation: Jul 04, 2012 at 01:11 PM
--

DROP TABLE IF EXISTS `admin_users`;
CREATE TABLE `admin_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `admin_user_type_id` int(3) unsigned NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `admin_user_type_id` (`admin_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `admin_user_roles`
--
-- Creation: Jul 04, 2012 at 01:11 PM
--

DROP TABLE IF EXISTS `admin_user_roles`;
CREATE TABLE `admin_user_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `admin_user_roles`
--


-- --------------------------------------------------------

--
-- Table structure for table `admin_user_types`
--
-- Creation: Jul 04, 2012 at 01:11 PM
--

DROP TABLE IF EXISTS `admin_user_types`;
CREATE TABLE `admin_user_types` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `admin_user_types`
--

INSERT INTO `admin_user_types` VALUES(1, 'Editor', 1);

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_type_roles`
--
-- Creation: Jul 04, 2012 at 04:52 PM
--

DROP TABLE IF EXISTS `admin_user_type_roles`;
CREATE TABLE `admin_user_type_roles` (
  `admin_user_type_id` int(3) unsigned NOT NULL,
  `admin_user_role_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`admin_user_type_id`,`admin_user_role_id`),
  KEY `admin_user_role_id` (`admin_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_user_type_roles`
--


-- --------------------------------------------------------

--
-- Table structure for table `invitation_tokens`
--
-- Creation: Jul 04, 2012 at 04:38 PM
--

DROP TABLE IF EXISTS `invitation_tokens`;
CREATE TABLE `invitation_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(32) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `invitation_tokens`
--


-- --------------------------------------------------------

--
-- Table structure for table `listings`
--
-- Creation: Jul 04, 2012 at 04:54 PM
--

DROP TABLE IF EXISTS `listings`;
CREATE TABLE `listings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `listings`
--


-- --------------------------------------------------------

--
-- Table structure for table `listing_properties`
--
-- Creation: Jul 04, 2012 at 04:55 PM
--

DROP TABLE IF EXISTS `listing_properties`;
CREATE TABLE `listing_properties` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `listing_properties`
--


-- --------------------------------------------------------

--
-- Table structure for table `listing_property_choices`
--
-- Creation: Jul 04, 2012 at 04:57 PM
--

DROP TABLE IF EXISTS `listing_property_choices`;
CREATE TABLE `listing_property_choices` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `listing_property_id` int(10) unsigned NOT NULL,
  `value` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `listing_property_id` (`listing_property_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `listing_property_choices`
--


-- --------------------------------------------------------

--
-- Table structure for table `listing_property_details`
--
-- Creation: Jul 04, 2012 at 05:00 PM
--

DROP TABLE IF EXISTS `listing_property_details`;
CREATE TABLE `listing_property_details` (
  `listing_id` bigint(20) unsigned NOT NULL,
  `listing_property_id` int(10) unsigned NOT NULL,
  `listing_property_choice_id` bigint(20) unsigned NOT NULL,
  `value` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`listing_id`,`listing_property_id`,`listing_property_choice_id`),
  KEY `listing_property_id` (`listing_property_id`),
  KEY `listing_property_choice_id` (`listing_property_choice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `listing_property_details`
--


-- --------------------------------------------------------

--
-- Table structure for table `providers`
--
-- Creation: Jul 04, 2012 at 04:46 PM
--

DROP TABLE IF EXISTS `providers`;
CREATE TABLE `providers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `slug` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `providers`
--


-- --------------------------------------------------------

--
-- Table structure for table `provider_invitations`
--
-- Creation: Jul 04, 2012 at 04:50 PM
--

DROP TABLE IF EXISTS `provider_invitations`;
CREATE TABLE `provider_invitations` (
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

--
-- Dumping data for table `provider_invitations`
--


-- --------------------------------------------------------

--
-- Table structure for table `provider_users`
--
-- Creation: Jul 04, 2012 at 04:15 PM
--

DROP TABLE IF EXISTS `provider_users`;
CREATE TABLE `provider_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `provider_id` int(10) unsigned NOT NULL,
  `provider_user_type_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `provider_id` (`provider_id`),
  KEY `provider_user_type_id` (`provider_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provider_users`
--


-- --------------------------------------------------------

--
-- Table structure for table `provider_user_invitations`
--
-- Creation: Jul 04, 2012 at 04:51 PM
--

DROP TABLE IF EXISTS `provider_user_invitations`;
CREATE TABLE `provider_user_invitations` (
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

--
-- Dumping data for table `provider_user_invitations`
--


-- --------------------------------------------------------

--
-- Table structure for table `provider_user_roles`
--
-- Creation: Jul 04, 2012 at 04:46 PM
--

DROP TABLE IF EXISTS `provider_user_roles`;
CREATE TABLE `provider_user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `provider_user_roles`
--

INSERT INTO `provider_user_roles` VALUES(1, 'LISTING_CREATOR', 'Can create listing', 1);
INSERT INTO `provider_user_roles` VALUES(2, 'LISTING_EDITOR', 'Can edit listing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `provider_user_types`
--
-- Creation: Jul 04, 2012 at 04:46 PM
--

DROP TABLE IF EXISTS `provider_user_types`;
CREATE TABLE `provider_user_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `provider_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `provider_id` (`provider_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `provider_user_types`
--


-- --------------------------------------------------------

--
-- Table structure for table `provider_user_type_roles`
--
-- Creation: Jul 04, 2012 at 04:20 PM
--

DROP TABLE IF EXISTS `provider_user_type_roles`;
CREATE TABLE `provider_user_type_roles` (
  `provider_user_type_id` int(10) unsigned NOT NULL,
  `provider_user_role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`provider_user_type_id`,`provider_user_role_id`),
  KEY `provider_user_role_id` (`provider_user_role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `provider_user_type_roles`
--


--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`admin_user_type_id`) REFERENCES `admin_user_types` (`id`);

--
-- Constraints for table `admin_user_type_roles`
--
ALTER TABLE `admin_user_type_roles`
  ADD CONSTRAINT `admin_user_type_roles_ibfk_1` FOREIGN KEY (`admin_user_type_id`) REFERENCES `admin_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_user_type_roles_ibfk_2` FOREIGN KEY (`admin_user_role_id`) REFERENCES `admin_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listings`
--
ALTER TABLE `listings`
  ADD CONSTRAINT `listings_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `listing_property_choices`
--
ALTER TABLE `listing_property_choices`
  ADD CONSTRAINT `listing_property_choices_ibfk_1` FOREIGN KEY (`listing_property_id`) REFERENCES `listing_properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `listing_property_details`
--
ALTER TABLE `listing_property_details`
  ADD CONSTRAINT `listing_property_details_ibfk_3` FOREIGN KEY (`listing_property_choice_id`) REFERENCES `listing_property_choices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_property_details_ibfk_1` FOREIGN KEY (`listing_id`) REFERENCES `listings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `listing_property_details_ibfk_2` FOREIGN KEY (`listing_property_id`) REFERENCES `listing_properties` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_invitations`
--
ALTER TABLE `provider_invitations`
  ADD CONSTRAINT `provider_invitations_ibfk_1` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `provider_users`
--
ALTER TABLE `provider_users`
  ADD CONSTRAINT `provider_users_ibfk_2` FOREIGN KEY (`provider_user_type_id`) REFERENCES `provider_user_types` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `provider_users_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_user_invitations`
--
ALTER TABLE `provider_user_invitations`
  ADD CONSTRAINT `provider_user_invitations_ibfk_2` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `provider_user_invitations_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_user_types`
--
ALTER TABLE `provider_user_types`
  ADD CONSTRAINT `provider_user_types_ibfk_1` FOREIGN KEY (`provider_id`) REFERENCES `providers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `provider_user_type_roles`
--
ALTER TABLE `provider_user_type_roles`
  ADD CONSTRAINT `provider_user_type_roles_ibfk_2` FOREIGN KEY (`provider_user_role_id`) REFERENCES `provider_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `provider_user_type_roles_ibfk_1` FOREIGN KEY (`provider_user_type_id`) REFERENCES `provider_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
