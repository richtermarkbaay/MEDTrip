-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 24, 2012 at 10:22 AM
-- Server version: 5.5.24
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `healthcareabroad`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_actions`
--

DROP TABLE IF EXISTS `admin_actions`;
CREATE TABLE IF NOT EXISTS `admin_actions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_roles`
--

DROP TABLE IF EXISTS `admin_user_roles`;
CREATE TABLE IF NOT EXISTS `admin_user_roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `label` varchar(250) NOT NULL,
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `admin_user_types`
--

DROP TABLE IF EXISTS `admin_user_types`;
CREATE TABLE IF NOT EXISTS `admin_user_types` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` smallint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE IF NOT EXISTS `advertisements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `advertisement_type` int(10) unsigned NOT NULL,
  `title` char(250) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='advertisement table';

-- --------------------------------------------------------

--
-- Table structure for table `breadcrumb_tree`
--

DROP TABLE IF EXISTS `breadcrumb_tree`;
CREATE TABLE IF NOT EXISTS `breadcrumb_tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `route` varchar(64) NOT NULL,
  `label` varchar(250) NOT NULL,
  `root_id` int(10) unsigned DEFAULT NULL,
  `left_value` int(10) unsigned DEFAULT NULL,
  `right_value` int(10) unsigned DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `level` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `route` (`route`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

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
  UNIQUE KEY `country_id_2` (`country_id`,`name`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` char(250) NOT NULL,
  `middle_name` char(250) NOT NULL,
  `last_name` char(250) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `entity_classes`
--

DROP TABLE IF EXISTS `entity_classes`;
CREATE TABLE IF NOT EXISTS `entity_classes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL COMMENT 'fully qualified class name',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='list of classes often used in tables with generic objects';

-- --------------------------------------------------------

--
-- Table structure for table `error_logs`
--

DROP TABLE IF EXISTS `error_logs`;
CREATE TABLE IF NOT EXISTS `error_logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `error_type` smallint(1) unsigned NOT NULL,
  `message` varchar(500) NOT NULL,
  `stacktrace` text NOT NULL,
  `http_user_agent` varchar(500) NOT NULL,
  `remote_address` varchar(50) NOT NULL,
  `server_json` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `error_reports`
--

DROP TABLE IF EXISTS `error_reports`;
CREATE TABLE IF NOT EXISTS `error_reports` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reporter_name` varchar(250) NOT NULL,
  `details` text NOT NULL,
  `logged_user_id` bigint(20) unsigned DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gallery_media`
--

DROP TABLE IF EXISTS `gallery_media`;
CREATE TABLE IF NOT EXISTS `gallery_media` (
  `gallery_id` int(10) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`gallery_id`,`media_id`),
  KEY `gallery_id` (`gallery_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

DROP TABLE IF EXISTS `inquiries`;
CREATE TABLE IF NOT EXISTS `inquiries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` char(100) NOT NULL,
  `message` text NOT NULL,
  `inquiry_subject_id` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_subject_id` (`inquiry_subject_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `inquiry_subjects`
--

DROP TABLE IF EXISTS `inquiry_subjects`;
CREATE TABLE IF NOT EXISTS `inquiry_subjects` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
  `city_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `city_id` (`city_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_contact_details`
--

DROP TABLE IF EXISTS `institution_contact_details`;
CREATE TABLE IF NOT EXISTS `institution_contact_details` (
  `institution_id` int(10) unsigned NOT NULL,
  `contact_detail_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`institution_id`,`contact_detail_id`),
  KEY `institution_contact_details_ibfk_3` (`contact_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_doctors`
--

DROP TABLE IF EXISTS `institution_doctors`;
CREATE TABLE IF NOT EXISTS `institution_doctors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `doctor_id` bigint(20) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`,`doctor_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_listings`
--

DROP TABLE IF EXISTS `institution_listings`;
CREATE TABLE IF NOT EXISTS `institution_listings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` bigint(10) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_centers`
--

DROP TABLE IF EXISTS `institution_medical_centers`;
CREATE TABLE IF NOT EXISTS `institution_medical_centers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `medical_center_id` int(10) unsigned NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`,`medical_center_id`),
  KEY `medical_center_id` (`medical_center_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_center_media`
--

DROP TABLE IF EXISTS `institution_medical_center_media`;
CREATE TABLE IF NOT EXISTS `institution_medical_center_media` (
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`institution_medical_center_id`,`media_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_procedures`
--

DROP TABLE IF EXISTS `institution_medical_procedures`;
CREATE TABLE IF NOT EXISTS `institution_medical_procedures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_medical_procedure_type_id` bigint(20) unsigned NOT NULL,
  `medical_procedure_id` int(10) unsigned NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_medical_procedure_type_id` (`institution_medical_procedure_type_id`,`medical_procedure_id`),
  KEY `medical_procedure_id` (`medical_procedure_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_procedure_types`
--

DROP TABLE IF EXISTS `institution_medical_procedure_types`;
CREATE TABLE IF NOT EXISTS `institution_medical_procedure_types` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `medical_procedure_type_id` int(11) unsigned NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_medical_center_id` (`institution_medical_center_id`,`medical_procedure_type_id`),
  KEY `medical_procedure_type_id` (`medical_procedure_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_offered_services`
--

DROP TABLE IF EXISTS `institution_offered_services`;
CREATE TABLE IF NOT EXISTS `institution_offered_services` (
  `institution_id` int(10) unsigned NOT NULL,
  `offered_service_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`institution_id`,`offered_service_id`),
  KEY `offered_service_id` (`offered_service_id`)
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

-- --------------------------------------------------------

--
-- Table structure for table `institution_user_type_roles`
--

DROP TABLE IF EXISTS `institution_user_type_roles`;
CREATE TABLE IF NOT EXISTS `institution_user_type_roles` (
  `institution_user_type_id` int(10) unsigned NOT NULL,
  `institution_user_role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`institution_user_type_id`,`institution_user_role_id`),
  KEY `institution_user_type_roles_ibfk_2` (`institution_user_role_id`)
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

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

DROP TABLE IF EXISTS `languages`;
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `iso_code` char(5) NOT NULL,
  `name` char(100) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

DROP TABLE IF EXISTS `logs`;
CREATE TABLE IF NOT EXISTS `logs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) unsigned NOT NULL,
  `application_context` tinyint(1) NOT NULL,
  `action` char(100) NOT NULL,
  `object_id` bigint(20) unsigned NOT NULL,
  `log_class_id` int(10) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `log_class_id` (`log_class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `log_classes`
--

DROP TABLE IF EXISTS `log_classes`;
CREATE TABLE IF NOT EXISTS `log_classes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
CREATE TABLE IF NOT EXISTS `media` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `caption` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `context` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `content_type` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `metadata` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `width` int(10) unsigned DEFAULT NULL,
  `height` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
  UNIQUE KEY `medical_procedure_type_id_2` (`medical_procedure_type_id`,`name`),
  KEY `medical_procedure_type_id` (`medical_procedure_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `medical_procedure_types`
--

DROP TABLE IF EXISTS `medical_procedure_types`;
CREATE TABLE IF NOT EXISTS `medical_procedure_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `medical_center_id` int(10) unsigned NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `medical_center_id` (`medical_center_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `slug` char(10) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `offered_services`
--

DROP TABLE IF EXISTS `offered_services`;
CREATE TABLE IF NOT EXISTS `offered_services` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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

-- --------------------------------------------------------

--
-- Table structure for table `test_autosuggest`
--

DROP TABLE IF EXISTS `test_autosuggest`;
CREATE TABLE IF NOT EXISTS `test_autosuggest` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `medical_procedure_type_id` int(11) unsigned DEFAULT NULL,
  `name` varchar(143) DEFAULT NULL,
  `slug` varchar(250) NOT NULL,
  `status` int(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `version_entries`
--

DROP TABLE IF EXISTS `version_entries`;
CREATE TABLE IF NOT EXISTS `version_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `action` varchar(250) NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `object_id` bigint(20) unsigned DEFAULT NULL,
  `object_class` varchar(500) NOT NULL,
  `version` int(10) unsigned NOT NULL,
  `username` varchar(250) DEFAULT NULL,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `gallery_media`
--
ALTER TABLE `gallery_media`
  ADD CONSTRAINT `gallery_media_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gallery_media_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`inquiry_subject_id`) REFERENCES `inquiry_subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `institutions_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institutions_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_contact_details`
--
ALTER TABLE `institution_contact_details`
  ADD CONSTRAINT `institution_contact_details_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_contact_details_ibfk_3` FOREIGN KEY (`contact_detail_id`) REFERENCES `contact_details` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_doctors`
--
ALTER TABLE `institution_doctors`
  ADD CONSTRAINT `institution_doctors_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_doctors_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_invitations`
--
ALTER TABLE `institution_invitations`
  ADD CONSTRAINT `institution_invitations_ibfk_1` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `institution_listings`
--
ALTER TABLE `institution_listings`
  ADD CONSTRAINT `institution_listings_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`),
  ADD CONSTRAINT `institution_listings_ibfk_2` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`);

--
-- Constraints for table `institution_medical_centers`
--
ALTER TABLE `institution_medical_centers`
  ADD CONSTRAINT `institution_medical_centers_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_centers_ibfk_2` FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_center_media`
--
ALTER TABLE `institution_medical_center_media`
  ADD CONSTRAINT `institution_medical_center_media_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `institution_medical_center_media_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_procedures`
--
ALTER TABLE `institution_medical_procedures`
  ADD CONSTRAINT `institution_medical_procedures_ibfk_1` FOREIGN KEY (`institution_medical_procedure_type_id`) REFERENCES `institution_medical_procedure_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_procedures_ibfk_2` FOREIGN KEY (`medical_procedure_id`) REFERENCES `medical_procedures` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_procedure_types`
--
ALTER TABLE `institution_medical_procedure_types`
  ADD CONSTRAINT `institution_medical_procedure_types_ibfk_1` FOREIGN KEY (`medical_procedure_type_id`) REFERENCES `medical_procedure_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_procedure_types_ibfk_2` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_offered_services`
--
ALTER TABLE `institution_offered_services`
  ADD CONSTRAINT `institution_offered_services_ibfk_2` FOREIGN KEY (`offered_service_id`) REFERENCES `offered_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_offered_services_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `institution_user_type_roles_ibfk_1` FOREIGN KEY (`institution_user_type_id`) REFERENCES `institution_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_user_type_roles_ibfk_2` FOREIGN KEY (`institution_user_role_id`) REFERENCES `institution_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`log_class_id`) REFERENCES `log_classes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `medical_procedures`
--
ALTER TABLE `medical_procedures`
  ADD CONSTRAINT `medical_procedures_ibfk_1` FOREIGN KEY (`medical_procedure_type_id`) REFERENCES `medical_procedure_types` (`id`);

--
-- Constraints for table `medical_procedure_types`
--
ALTER TABLE `medical_procedure_types`
  ADD CONSTRAINT `medical_procedure_types_ibfk_1` FOREIGN KEY (`medical_center_id`) REFERENCES `medical_centers` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `medical_term_suggestion_details`
--
ALTER TABLE `medical_term_suggestion_details`
  ADD CONSTRAINT `medical_term_suggestion_details_ibfk_1` FOREIGN KEY (`medical_term_suggestion_id`) REFERENCES `medical_term_suggestions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
