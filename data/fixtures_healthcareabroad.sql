-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 30, 2012 at 11:28 AM
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

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`account_id`, `admin_user_type_id`, `status`) VALUES
(2, 1, 1),
(5, 2, 1);

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

--
-- Dumping data for table `admin_user_roles`
--

INSERT INTO `admin_user_roles` (`id`, `name`, `label`, `status`) VALUES
(1, 'SUPER_ADMIN', 'Owner/Super Admin', 3),
(2, 'CAN_VIEW_INSTITUTIONS', 'View all institutions', 2),
(3, 'CAN_MANAGE_INSTITUTION', 'Add or edit an institution', 2),
(4, 'CAN_DELETE_INSTITUTION', 'Delete or deactivate an institution', 2),
(5, 'CAN_VIEW_SPECIALIZATIONS', 'View all specializations', 2),
(6, 'CAN_MANAGE_SPECIALIZATION', 'Add or edit a specialization', 2),
(7, 'CAN_DELETE_SPECIALIZATION', 'Delete or deactivate a specialization', 2),
(8, 'CAN_VIEW_SUB_SPECIALIZATIONS', 'View all sub specializations', 2),
(9, 'CAN_MANAGE_SUB_SPECIALIZATION', 'Add or edit a sub specialization', 2),
(10, 'CAN_DELETE_SUB_SPECIALIZATION', 'Delete a sub specialization', 2),
(11, 'CAN_VIEW_TREATMENTS', 'View all treatments', 2),
(12, 'CAN_MANAGE_TREATMENT', 'Add or edit a treatment', 2),
(13, 'CAN_DELETE_TREATMENT', 'Delete a treatment', 2),
(14, 'CAN_VIEW_NEWS', 'View all news', 2),
(15, 'CAN_MANAGE_NEWS', 'Add or edit news', 2),
(16, 'CAN_MANAGE_LANGUAGE', 'Add or edit a language data', 2),
(17, 'CAN_MANAGE_OFFERED_SERVICE', 'Manage Ancilliary Services', 1),
(18, 'CAN_VIEW_OFFERED_SERVICE', 'View Ancilliary Service', 1);

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

--
-- Dumping data for table `admin_user_types`
--

INSERT INTO `admin_user_types` (`id`, `name`, `status`) VALUES
(1, 'Test User Type with super admin role', 3),
(2, 'Normal user type', 2);

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

--
-- Dumping data for table `admin_user_type_roles`
--

INSERT INTO `admin_user_type_roles` (`admin_user_type_id`, `admin_user_role_id`) VALUES
(1, 1);


-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

DROP TABLE IF EXISTS `advertisements`;
CREATE TABLE IF NOT EXISTS `advertisements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `advertisement_type_id` smallint(3) unsigned NOT NULL,
  `title` char(250) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_expiry` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `advertisement_type_id` (`advertisement_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='advertisement table';


-- --------------------------------------------------------

--
-- Table structure for table `advertisement_denormalized_properties`
--

DROP TABLE IF EXISTS `advertisement_denormalized_properties`;
CREATE TABLE IF NOT EXISTS `advertisement_denormalized_properties` (
  `id` bigint(20) unsigned NOT NULL,
  `institution_id` int(10) unsigned NOT NULL,
  `advertisement_type_id` smallint(3) unsigned NOT NULL,
  `title` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `treatment_id` int(10) unsigned NOT NULL,
  `sub_specialization_id` int(10) unsigned NOT NULL,
  `specialization_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  `video_url` varchar(200) NOT NULL,
  `highlight_doctors` varchar(300) NOT NULL,
  `highlight_specializations` varchar(300) NOT NULL,
  `highlight_sub_specializations` varchar(300) NOT NULL,
  `highlight_treatments` varchar(300) NOT NULL,
  `highlight_featured_images` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_expiry` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `advertisement_media`
--

DROP TABLE IF EXISTS `advertisement_media`;
CREATE TABLE IF NOT EXISTS `advertisement_media` (
  `advertisement_id` bigint(20) unsigned NOT NULL,
  `media_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`advertisement_id`,`media_id`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `advertisement_property_names`
--

DROP TABLE IF EXISTS `advertisement_property_names`;
CREATE TABLE IF NOT EXISTS `advertisement_property_names` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `label` varchar(100) NOT NULL,
  `data_type_id` smallint(3) unsigned NOT NULL,
  `data_class` char(250) NOT NULL,
  `property_config` varchar(500) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `data_type_id` (`data_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


--
-- Dumping data for table `advertisement_property_names`
--

INSERT INTO `advertisement_property_names` (`id`, `name`, `label`, `data_type_id`, `data_class`, `property_config`, `status`) VALUES
(1, 'specialization_id', 'Specialization', 5, 'HealthCareAbroad\\TreatmentBundle\\Entity\\Specialization', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionSpecializationListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":false,"attr":{}}}', 1),
(2, 'sub_specialization_id', 'Sub Specialization', 5, 'HealthCareAbroad\\TreatmentBundle\\Entity\\SubSpecialization', '', 0),
(3, 'treatment_id', 'Treatment', 5, 'HealthCareAbroad\\TreatmentBundle\\Entity\\Treatment', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionTreatmentListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":false,"attr":{}}}', 0),
(4, 'country_id', 'Country', 5, 'HealthCareAbroad\\HelperBundle\\Entity\\Country', '{"type":"country_list","isClass":false, "hasParams":false,"config":{"virtual": false,"attr":{}}}', 0),
(5, 'city_id', 'City', 5, 'HealthCareAbroad\\CityBundle\\Entity\\City', '{"type":"autocomplete", "multiple":false}', 0),
(6, 'media_id', 'Image', 5, 'HealthCareAbroad\\MediaBundle\\Entity\\Media', '{"type":"file","isClass":false, "hasParams":false,"config":{"virtual": false,"attr":{}}}', 1),
(7, 'highlight_doctors', 'Highlight Doctors', 6, 'HealthCareAbroad\\DoctorBundle\\Entity\\Doctor', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionDoctorListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":true,"empty_value": false,"expanded":false,"attr":{}}}', 0),
(8, 'highlight_specializations', 'highlight Specializations', 6, 'HealthCareAbroad\\TreatmentBundle\\Entity\\Specialization', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionSpecializationListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":true,"attr":{}}}', 0),
(9, 'highlight_sub_specializations', 'highlight Sub-specialization', 6, 'HealthCareAbroad\\TreatmentBundle\\Entity\\SubSpecialization', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionSpecializationListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":true,"attr":{}}}', 0),
(10, 'highlight_treatments', 'Highlight Treatments', 6, 'HealthCareAbroad\\TreatmentBundle\\Entity\\Treatment', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionTreatmentListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":true,"attr":{}}}', 0),
(13, 'institution_medical_center_id', 'Clinic', 5, 'HealthCareAbroad\\InstitutionBundle\\Entity\\InstitutionMedicalCenter', '{"type":"HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionMedicalCenterListType","isClass":true, "hasParams":true,"config":{"virtual": false,"multiple":false,"attr":{}}}', 0),
(14, 'vide', 'Video Url', 3, '', '', 0);


-- --------------------------------------------------------

--
-- Table structure for table `advertisement_property_values`
--

DROP TABLE IF EXISTS `advertisement_property_values`;
CREATE TABLE IF NOT EXISTS `advertisement_property_values` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `advertisement_id` bigint(20) unsigned NOT NULL,
  `advertisement_property_name_id` smallint(3) unsigned NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `advertisement_types`
--

DROP TABLE IF EXISTS `advertisement_types`;
CREATE TABLE IF NOT EXISTS `advertisement_types` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `advertisement_types`
--

INSERT INTO `advertisement_types` (`id`, `name`, `status`) VALUES
(1, 'Premier Home Page Feature', 1),
(2, 'Home Page Clinic Feature', 1),
(3, 'Home Page Destination Sponsorship', 1),
(4, 'Home Page Featured Service', 1),
(5, 'Home Page Featured Video', 1),
(6, 'News', 1);


-- --------------------------------------------------------

--
-- Table structure for table `advertisement_type_configurations`
--

DROP TABLE IF EXISTS `advertisement_type_configurations`;
CREATE TABLE IF NOT EXISTS `advertisement_type_configurations` (
  `advertisement_type_id` smallint(3) unsigned NOT NULL,
  `advertisement_property_name_id` smallint(3) unsigned NOT NULL,
  PRIMARY KEY (`advertisement_type_id`,`advertisement_property_name_id`),
  KEY `advertisement_property_name_id` (`advertisement_property_name_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awarding_bodies`
--

-- --------------------------------------------------------

--
-- Table structure for table `awarding_bodies`
--

DROP TABLE IF EXISTS `awarding_bodies`;
CREATE TABLE IF NOT EXISTS `awarding_bodies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `awarding_bodies`
--

INSERT INTO `awarding_bodies` (`id`, `name`, `details`, `website`, `status`) VALUES
(1, 'Join Commission International (JCI)', 'Joint Commission International (JCI) is Healthcare Accreditation Body that is established in 1994. W', 'http://www.jointcommissio', 1),
(2, 'International Organization for Standardization (IS', 'ISO (International Organization for Standardization) which is founded in 1947 has become the world’s', 'http://www.iso.org/iso/ho', 1);

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
  `id` int(10) unsigned NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_id_2` (`country_id`,`name`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cities`
--

INSERT INTO `fixtures_healthcareabroad`.`cities` (`id`, `country_id`, `name`, `slug`, `status`) VALUES ('1', '1', 'test', 'test', '1');
-- --------------------------------------------------------

--
-- Table structure for table `command_script_logs`
--

DROP TABLE IF EXISTS `command_script_logs`;
CREATE TABLE IF NOT EXISTS `command_script_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `script_name` char(100) NOT NULL,
  `description` varchar(500) NOT NULL,
  `attempts` smallint(3) unsigned NOT NULL DEFAULT '0',
  `last_run_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `abbr` varchar(10) DEFAULT NULL,
  `code` char(11) DEFAULT NULL,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `countries`
--

INSERT INTO `fixtures_healthcareabroad`.`countries` (`id`, `name`, `abbr`, `code`, `slug`, `status`) VALUES ('1', 'test', 'test', 'test', 'test', '1');

-- --------------------------------------------------------

--
-- Table structure for table `data_types`
--

DROP TABLE IF EXISTS `data_types`;
CREATE TABLE IF NOT EXISTS `data_types` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `column_type` char(30) NOT NULL,
  `form_field` char(30) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `column_type` (`column_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `data_types`
--

INSERT INTO `data_types` (`id`, `column_type`, `form_field`) VALUES
(1, 'int', 'integer'),
(2, 'bigint', 'integer'),
(3, 'string', 'text'),
(4, 'text', 'textarea'),
(5, 'entity', 'entity'),
(6, 'collection', 'entity'),
(7, 'bool', 'radio');


-- --------------------------------------------------------

--
-- Table structure for table `doctors`
--

DROP TABLE IF EXISTS `doctors`;
CREATE TABLE IF NOT EXISTS `doctors` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `first_name` char(250) NOT NULL,
  `middle_name` char(250) DEFAULT NULL,
  `last_name` char(250) NOT NULL,
  `gender` smallint(1) unsigned DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_number` text,
  `details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `country_id` int(10) unsigned DEFAULT NULL,
  `media_id` bigint(20) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `doctor_specializations`
--

DROP TABLE IF EXISTS `doctor_specializations`;
CREATE TABLE IF NOT EXISTS `doctor_specializations` (
  `doctor_id` bigint(20) unsigned NOT NULL,
  `specialization_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`doctor_id`,`specialization_id`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
-- Table structure for table `frontend_routes`
--

DROP TABLE IF EXISTS `frontend_routes`;
CREATE TABLE IF NOT EXISTS `frontend_routes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uri` varchar(500) NOT NULL,
  `controller` varchar(250) CHARACTER SET utf32 COLLATE utf32_unicode_ci NOT NULL,
  `variables` text NOT NULL COMMENT 'JSON variables for this route',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='frontend dynamic routes';


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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
-- Table structure for table `global_awards`
--

DROP TABLE IF EXISTS `global_awards`;
CREATE TABLE IF NOT EXISTS `global_awards` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL,
  `name` varchar(250) NOT NULL,
  `details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `awarding_body_id` int(10) NOT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `country_id` (`country_id`),
  KEY `type` (`type`),
  KEY `awarding_body_id` (`awarding_body_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='awards, certificates and affiliations data';


-- --------------------------------------------------------

--
-- Table structure for table `helper_text`
--

DROP TABLE IF EXISTS `helper_text`;
CREATE TABLE IF NOT EXISTS `helper_text` (
  `id` int(15) NOT NULL AUTO_INCREMENT,
  `route` varchar(50) DEFAULT NULL,
  `details` text NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


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
--
-- Dumping data for table `inquiry_subjects`
--

INSERT INTO `inquiry_subjects` (`id`, `name`, `slug`, `status`) VALUES(1, 'membership', 'test', 1);
INSERT INTO `inquiry_subjects` (`id`, `name`, `slug`, `status`) VALUES(2, 'fees', 'saf', 1);


-- --------------------------------------------------------

--
-- Table structure for table `institutions`
--

DROP TABLE IF EXISTS `institutions`;
CREATE TABLE IF NOT EXISTS `institutions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_type` tinyint(1) NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_number` text NOT NULL,
  `websites` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `address1` text NOT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `zip_code` char(10) DEFAULT NULL,
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `coordinates` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) NOT NULL,
  `signup_step_status` smallint(1) unsigned NOT NULL DEFAULT '1',
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `city_id` (`city_id`),
  KEY `country_id` (`country_id`),
  KEY `logo_id` (`logo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `institution_type`, `name`, `description`, `logo_id`, `contact_email`, `contact_number`, `websites`, `address1`, `city_id`, `country_id`, `zip_code`, `state`, `coordinates`, `date_modified`, `date_created`, `slug`, `signup_step_status`, `status`) VALUES
(1, 1, 'Ahalia Eye Hospital', 'Ahalia Foundation Eye Hospital, a unit of Ahalia International Foundation which started in 2005 stands tall on a rock solid reputation of reliability, affordability, quality and innovation. Now it has added one more feather to its cap-accreditation by the prestigious Joint Commission International, USA. JCI accreditation is the ultimate recognition in the field of health care and is awarded after a strenuous quality audit conducted by a team of international healthcare experts. \r\n\r\nWith this recognition, Ahalia is proud to herald the arrival of international standard health care to Kerala with special focus on quality improvement, patient safety and infection control. \r\n\r\nEquipped with six operation theaters and state of the art equipments, AFEH has the expertise in all the areas of ophthalmology including advanced Phaco surgery for cataract, latest Lasik treatment for refractive errors and specialty services for Retina, Glaucoma, Pediatric Ophthalmology, Low Vision Aids etc - all this at very affordable rates.', NULL, 'mail@afeh.org', '{"country_code":"1","area_code":"4923","number":"225 000"}', '{"main":"http:\/\/www.ahaliafoundationeyehospital.org","facebook":"https:\/\/www.facebook.com\/pages\/Ahalia-foundation-eye-hospitals\/387889344602118","twitter":"http:\/\/"}', '{"room_number":"","building":"","street":"Kanal Pirivu"}', 1, 1, '1', 'Kerala', '', '2013-01-16 05:08:04', '2012-12-06 06:29:26', 'ahalia-eye-hospital', 0, 9),
(2, 1, 'Apollo Gleneagles Hospital, Kolkata', 'Apollo Gleneagles Hospitals Kolkata, a 510-bedded multispecialty tertiary care hospital, is a perfect blend of technological excellence, complete infrastructure, competent care and heartfelt hospitality.\r\n\r\nApollo Gleneagles Hospitals is a joint venture of Apollo Group of Hospitals, India and Parkway Health of Singapore.\r\n\r\nThe Parkway Group is a leading healthcare group in Asia. It provides more than 70% of private healthcare in Singapore. Its subsidiaries include Parkway Group Healthcare, which owns a network of regional hospitals and medical centers in Malaysia, India and Brunei; and Parkway Hospitals Singapore, which owns three hospitals in Singapore - East Shore, Gleneagles, Mount Elizabeth Hospitals and Parkway Health Day surgery Centre.', NULL, 'hospital@apollogleneagles.in', '{"country_code":"1","area_code":" 3323","number":" 203 040"}', '{"main":"http:\\/\\/kolkata.apollohospitals.com\\/","facebook":"","twitter":""}', '{"room_number":"","building":"Apollo Gleneagles Hospital","street":"No. 58, Canal Circular Road"}', 40810, 1, '700054', 'West Bengal', '', '2013-01-16 05:08:04', '2012-12-06 06:49:56', 'apollo-gleneagles-hospital-kolkata', 0, 8),
(3, 1, 'Apollo Hospital, Bangalore', 'Apollo Hospitals, Bangalore – a world class JCI accredited super speciality hospital, a six-storied facility situated on Bannerghatta road is equipped with the latest in the medical world. \r\n\r\nApollo Hospitals, Bangalore is a tertiary care flagship unit of the Apollo Hospitals Group. The Hospital focuses on centers of excellence like Cardiac Sciences, Neuro Sciences, Orthopaedics, Cancer, Emergency Medicine and Solid Organ Transplants besides the complete range of more than 35 allied medical disciplines under the same roof.', NULL, 'customercare_bangalore@apollohospitals.com', '{"country_code":"1","area_code":"8026","number":"304 050"}', '{"main":"http:\\/\\/www.apollohospitalsbangalore.com\\/","facebook":"","twitter":""}', '{"room_number":"","building":"Apollo Hospitals - Bangalore","street":"154\\/11, Opp. IIM B, Bannerghatta Road"}', 7454, 1, '560076', 'Karnataka', '', '2013-01-16 05:08:04', '2012-12-06 07:01:40', 'apollo-hospital-bangalore', 0, 8),
(4, 1, 'Apollo Hospital, Chennai', 'The flagship hospital of the Apollo Group, Apollo Hospitals Chennai, was established in 1983. Today it is one of the most respected hospitals in the world, and is also amongst the most preferred destinations for both patients from several parts of India, as well as for medical tourism and medical value travel. The hospital specializes in cutting-edge medical procedures. It has over 60 departments spearheaded by internationally trained doctors who are skillfully supported by dedicated patient-care personnel. It is one of the few hospitals in Chennai that have state of the art facilities for various health disorders.\r\n\r\nIt has been a pioneer among the hospitals in Chennai, and even in India, in many different treatments and procedures.', NULL, 'enquiry@apollohospitals.com', '{"country_code":"","area_code":"","number":""}', '{"main":"","facebook":"","twitter":""}', '{"room_number":"","building":"","street":"Apollo Hospitals - Chennai No. 21, Greams Lane, Off. Greams Road, Chennai 600006 India"}', 22151, 1, '600006', 'Tammil Nadu', '', '2013-01-16 05:08:04', '2012-12-06 07:46:49', 'apollo-hospital-chennai', 0, 8),
(5, 1, 'Apollo Hospital, Hyderabad', 'Today, Apollo Hospitals, Hyderabad has risen to be on par with the best in the world, in terms of technical expertise, deliverables and outcomes. It has now evolved into a one of a kind institution , the Apollo Health City, Hyderabad, which is the first health city in Asia and a perfect example of an integrated healthcare system offering solutions across the healthcare space. A 350 bedded multi-specialty hospital with over 50 specialties and super-specialties, 10 Centers of Excellence, education, research, information technology, all in one sprawling campus creates an environment dedicated to healing.\r\n\r\nApollo Health City , Hyderabad covers the entire spectrum from illness to wellness and is thus a health city and not a medical city. Institutes for Heart Diseases, Cancer, Joint Diseases, Emergency, Renal Diseases, Neurosciences, Eye and Cosmetic Surgery are all centers of excellence and are positioned to offer the best care in the safest manner to every patient.\r\n\r\nApart from patient care, each of these Centers of Excellence spend a significant amount of time in training and research essentially aimed at preventing disease and improving outcomes when the disease does occur.\r\n\r\nMost of the consultants at the Health city have international experience either educational, work experience - related or observational. The average staff to patient ratio for the hospital is 3:1 with a 1:1 ratio prevailing in priority areas like the Intensive Care Unit and the Cardiac Care Unit.\r\n\r\nApollo Healthcity, Hyderabad handles close to 100,000 patients a year. International patients from Tanzania, the USA, the UAE, Kenya, Oman and neighbouring Asian countries are treated by the hospital every year.', NULL, 'apollohealthcity@apollohospitals.com', '', '', 'Apollo Hospitals Jubilee Hills Hyderabad Andhra Pradesh 500033 India', 20484, 1, '500033', 'Andra Pradesh', '', '2013-01-16 05:08:04', '2012-12-06 07:56:18', 'apollo-hospital-hyderabad', 0, 8);


-- --------------------------------------------------------

--
-- Table structure for table `institution_global_awards`
--

DROP TABLE IF EXISTS `institution_global_awards`;
CREATE TABLE IF NOT EXISTS `institution_global_awards` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `global_award_id` int(10) NOT NULL,
  `year_awarded` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`),
  KEY `global_award_id` (`global_award_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `institution_groups`
--

DROP TABLE IF EXISTS `institution_groups`;
CREATE TABLE IF NOT EXISTS `institution_groups` (
  `institution_id` int(10) unsigned NOT NULL,
  `medical_provider_group_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `institution_id` (`institution_id`),
  KEY `medical_provider_group_id` (`medical_provider_group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_inquiries`
--

CREATE TABLE IF NOT EXISTS `institution_inquiries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Null if this was made in the hospital profile page',
  `inquirer_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `inquirer_email` int(11) NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

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
-- Table structure for table `institution_medical_centers`
--

DROP TABLE IF EXISTS `institution_medical_centers`;
CREATE TABLE IF NOT EXISTS `institution_medical_centers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET latin1 NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `coordinates` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `business_hours` varchar(500) COLLATE ucs2_unicode_ci DEFAULT NULL,
  `contact_number` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `websites` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `description` text CHARACTER SET latin1 NOT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` varchar(250) CHARACTER SET latin1 NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`,`name`),
  KEY `logo_id` (`logo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=ucs2 COLLATE=ucs2_unicode_ci;

--
-- Dumping data for table `institution_medical_centers`
--
INSERT INTO `institution_medical_centers` (`id`, `institution_id`, `name`, `address`, `coordinates`, `business_hours`, `contact_number`, `contact_email`, `websites`, `description`, `logo_id`, `date_created`, `date_updated`, `slug`, `status`) VALUES
(1, 1, 'Pre-Admission Counselling and Evaluation (PACE) Clinic', NULL, '', '{"Monday":{"from":" 8:30 AM","to":" 5:30 PM"},"Tuesday":{"from":" 8:30 AM","to":" 5:30 PM"},"Wednesday":{"from":" 8:30 AM","to":" 5:30 PM"},"Thursday":{"from":" 8:30 AM","to":" 5:30 PM"},"Friday":{"from":" 8:30 AM","to":" 5:30 PM"},"Saturday":{"from":" 8:30 AM","to":"12:30 PM"}}', '', '', '', 'Location: Level B2, TTSH Medical Center\nContact Information: 6357 2244\nFax: 6357 2244\nRelated Departments and Clinics: Department of Anaesthesiology, Intensive Care and Pain Medicine\n&nbsp;\n\n&nbsp;', NULL, '2012-12-07 06:12:50', '2012-12-07 03:24:30', 'pre-admission-counselling-and-evaluation-pace-clinic', 2),
(2, 2, 'Audiology Services', NULL, '', '{"Monday":{"from":" 8:00 AM","to":" 5:30 PM"},"Tuesday":{"from":" 8:00 AM","to":" 5:30 PM"},"Wednesday":{"from":" 8:00 AM","to":" 5:30 PM"},"Thursday":{"from":" 8:00 AM","to":" 5:30 PM"},"Friday":{"from":" 8:00 AM","to":" 5:30 PM"},"Saturday":{"from":" 8:00 AM","to":"12:30 PM"}}', '', '', '', '&nbsp;Location: Clinic 1 B, Level 1, TTSH Medical Center\nContact Information: 6357 8007 (Inquiry), 6357 8384\nFax: 6357 8384\nRelated Department: ENT (Audiology Services)&nbsp;', NULL, '2012-12-07 06:14:47', '2012-12-07 03:30:59', 'audiology-services', 2);

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_center_doctors`
--

DROP TABLE IF EXISTS `institution_medical_center_doctors`;
CREATE TABLE IF NOT EXISTS `institution_medical_center_doctors` (
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `doctor_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`institution_medical_center_id`,`doctor_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`),
  KEY `doctor_id` (`doctor_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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
-- Table structure for table `institution_medical_center_properties`
--

DROP TABLE IF EXISTS `institution_medical_center_properties`;
CREATE TABLE IF NOT EXISTS `institution_medical_center_properties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `institution_property_type_id` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  `extra_value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT 'extra property value. needed if value is restricted to be an id and there are optional property values',
  PRIMARY KEY (`id`),
  KEY `institution_property_type_id` (`institution_property_type_id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


-- --------------------------------------------------------

--
-- Table structure for table `institution_properties`
--

DROP TABLE IF EXISTS `institution_properties`;
CREATE TABLE IF NOT EXISTS `institution_properties` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_property_type_id` int(10) unsigned NOT NULL,
  `value` text NOT NULL,
  `extra_value` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `institution_property_type_id` (`institution_property_type_id`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `institution_property_types`
--

DROP TABLE IF EXISTS `institution_property_types`;
CREATE TABLE IF NOT EXISTS `institution_property_types` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) NOT NULL,
  `label` varchar(250) NOT NULL,
  `data_type_id` smallint(3) unsigned NOT NULL,
  `data_class` varchar(250) NOT NULL,
  `form_configuration` text NOT NULL COMMENT 'JSON format for configuration of this form field',
  `status` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `data_type_id` (`data_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institution_property_types`
--

INSERT INTO `institution_property_types` (`id`, `name`, `label`, `data_type_id`, `data_class`, `form_configuration`, `status`) VALUES
(1, 'ancilliary_service_id', 'Ancilliary Service', 5, 'HealthCareAbroad\\AdminBundle\\Entity\\OfferedService', '{"type":"\\\\HealthCareAbroad\\\\InstitutionBundle\\\\Form\\\\ListType\\\\InstitutionOfferedServiceListType","multiple":"true","expanded":"true"}', 1),
(2, 'language_id', 'Language Spoken', 5, 'HealthCareAbroad\\AdminBundle\\Entity\\Language', '{"multiple":true,"expanded":true,"type":"InstitutionLanguageSpokenFormType"}', 1),
(3, 'global_award_id', 'Global Award', 5, 'HealthCareAbroad\\HelperBundle\\Entity\\GlobalAward', '', 1);


-- --------------------------------------------------------

--
-- Table structure for table `institution_specializations`
--

DROP TABLE IF EXISTS `institution_specializations`;
CREATE TABLE IF NOT EXISTS `institution_specializations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `specialization_id` int(10) unsigned NOT NULL,
  `description` text CHARACTER SET latin1 NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_medical_center_id` (`institution_medical_center_id`,`specialization_id`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `institution_treatments`
--

DROP TABLE IF EXISTS `institution_treatments`;
CREATE TABLE IF NOT EXISTS `institution_treatments` (
  `institution_specialization_id` bigint(20) unsigned NOT NULL,
  `treatment_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`institution_specialization_id`,`treatment_id`),
  KEY `treatment_id` (`treatment_id`),
  KEY `institution_specialization_id` (`institution_specialization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


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

INSERT INTO `fixtures_healthcareabroad`.`institution_users` (`account_id`, `institution_id`, `institution_user_type_id`, `date_created`, `status`) VALUES ('2', '1', '1', CURRENT_TIMESTAMP, '1');
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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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
-- Table structure for table `institution_user_roles`
--

CREATE TABLE IF NOT EXISTS `institution_user_roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` varchar(250) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `institution_user_roles`
--

INSERT INTO `institution_user_roles` (`id`, `name`, `description`, `status`) VALUES
(1, 'SUPER_ADMIN', 'owner/admin', 3),
(2, 'CAN_VIEW_MEDICAL_CENTERS', 'View all medical centers', 2),
(3, 'CAN_MANAGE_MEDICAL_CENTER', 'Add or edit a medical center', 2),
(4, 'CAN_DELETE_MEDICAL_CENTER', 'Delete or deactivate a medical center', 2),
(5, 'CAN_VIEW_STAFF', 'View all staff', 2),
(6, 'CAN_MANAGE_STAFF', 'Add or Edit staff', 2),
(7, 'CAN_DELETE_STAFF', 'Delete or deactivate a staff', 2),
(8, 'CAN_MANAGE_INSTITUTION', 'Add or Edit Institution Details', 2),
(9, 'CAN_VIEW_PROCEDURE_TYPES', 'View all medical procedure types', 2),
(10, 'CAN_MANAGE_PROCEDURE_TYPES', 'Add or Edit medical procedure types', 2);

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
(1, 1, 'ADMIN', 3);

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

--
-- Dumping data for table `institution_user_type_roles`
--

INSERT INTO `institution_user_type_roles` (`institution_user_type_id`, `institution_user_role_id`) VALUES
(1, 1);

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

--
-- Dumping data for table `languages`
--
INSERT INTO `languages` (`id`, `iso_code`, `name`, `status`) VALUES(1, 'en', 'English', 1);


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
-- Table structure for table `mail_queue`
--

DROP TABLE IF EXISTS `mail_queue`;
CREATE TABLE IF NOT EXISTS `mail_queue` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `message_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'serialized message data',
  `send_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'when this message should be sent',
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `failed_attempts` int(10) unsigned DEFAULT '0' COMMENT 'number of failed attempts for this message',
  `status` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `search_terms`
--

CREATE TABLE IF NOT EXISTS `search_terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` int(10) unsigned NOT NULL,
  `term_document_id` bigint(20) unsigned NOT NULL COMMENT 'term_documents.id',
  `document_id` int(10) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL COMMENT '1-SPECIALIZATION, 2-SUBSPECIALIZATION, 3-TREATMENT',
  `status` tinyint(1) unsigned NOT NULL,
  `specialization_id` int(10) unsigned DEFAULT NULL,
  `sub_specialization_id` int(10) unsigned DEFAULT NULL,
  `treatment_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `specialization_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_specialization_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `treatment_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `city_name` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`),
  KEY `document_id` (`document_id`),
  KEY `type` (`type`),
  KEY `specialization_id` (`specialization_id`),
  KEY `sub_specialization_id` (`sub_specialization_id`),
  KEY `treatment_id` (`treatment_id`),
  KEY `country_id` (`country_id`),
  KEY `city_id` (`city_id`),
  KEY `specialization_name` (`specialization_name`),
  KEY `sub_specialization_name` (`sub_specialization_name`),
  KEY `treatment_name` (`treatment_name`),
  KEY `country_name` (`country_name`),
  KEY `city_name` (`city_name`),
  KEY `term_id` (`term_id`),
  KEY `term_document_id` (`term_document_id`),
  KEY `status` (`status`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

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
-- Table structure for table `medical_provider_groups`
--

DROP TABLE IF EXISTS `medical_provider_groups`;
CREATE TABLE IF NOT EXISTS `medical_provider_groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


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
-- Table structure for table `newsletters_subscribers`
--

DROP TABLE IF EXISTS `newsletters_subscribers`;
CREATE TABLE IF NOT EXISTS `newsletters_subscribers` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `ip_address` varchar(20) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
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

--
-- Dumping data for table `offered_services`
--

INSERT INTO `offered_services` (`id`, `name`, `status`, `date_created`) VALUES
(1, 'Text Message Reminders', 1, '2012-12-21 04:06:03'),
(2, 'Booking for Hotel Accommodation', 1, '2012-12-21 04:12:40'),
(3, 'Language Translation Services', 1, '2012-12-21 04:13:48'),
(4, 'Local Travel Guide', 1, '2012-12-21 04:14:35'),
(5, 'Booking for Airline Travel', 1, '2012-12-21 04:15:02'),
(6, 'Airport Pick-up', 1, '2012-12-21 04:16:26'),
(7, 'Laundry Service', 1, '2012-12-21 04:16:48'),
(8, 'Local Transportation', 1, '2012-12-21 04:17:48'),
(9, 'Free Wifi', 1, '2012-12-21 04:22:56'),
(10, 'Accommodation', 1, '2012-12-21 04:32:19'),
(11, 'Hospital Arrangements Facilitator', 1, '2012-12-21 04:32:45'),
(12, 'Direct Hospital Admission', 1, '2012-12-21 04:33:17'),
(13, 'Local Travel Facilitator', 1, '2012-12-21 04:34:59'),
(14, 'Hotel Pickup', 1, '2012-12-21 04:35:51'),
(15, 'Online Appointments', 1, '2012-12-21 04:36:22'),
(16, 'Online Payments', 1, '2012-12-21 04:36:42'),
(17, 'Online Consultation', 1, '2012-12-21 04:47:46'),
(18, 'Spa Services', 1, '2012-12-21 04:51:39'),
(19, 'Entertainment and Gaming Zone', 1, '2012-12-21 04:51:56'),
(20, 'English - speaking staff', 1, '2012-12-21 06:34:26'),
(21, 'Pharmacy', 1, '2013-01-02 01:20:07'),
(22, 'Interpreters', 1, '2013-01-04 02:22:21'),
(23, 'Internet', 1, '2013-01-04 02:23:17'),
(24, 'Foreign Exchange', 1, '2013-01-04 02:24:36'),
(25, 'Single Window for All Payments', 1, '2013-01-04 02:25:12'),
(26, 'Blood Bank', 1, '2013-01-07 03:00:40'),
(27, 'Stem Cell Bank', 1, '2013-01-07 03:00:55'),
(28, 'International Insurance', 1, '2013-01-07 03:01:37'),
(29, 'Dietetics', 1, '2013-01-09 02:17:09'),
(30, 'Cafeteria', 1, '2013-01-11 09:10:33'),
(31, 'Library', 1, '2013-01-11 09:10:56'),
(32, 'Fax Services', 1, '2013-01-11 09:12:16'),
(33, 'Information Center', 1, '2013-01-11 09:12:35'),
(34, 'Multi-Purpose Shop', 1, '2013-01-11 09:13:07'),
(35, 'ATM', 1, '2013-01-11 09:13:30'),
(36, 'Free Telephone Use', 1, '2013-01-11 09:13:31');

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

DROP TABLE IF EXISTS `specializations`;
CREATE TABLE IF NOT EXISTS `specializations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sub_specializations`
--

DROP TABLE IF EXISTS `sub_specializations`;
CREATE TABLE IF NOT EXISTS `sub_specializations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialization_id` int(10) unsigned NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `specialization_id_2` (`specialization_id`,`name`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='treatments';

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

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
-- Table structure for table `terms`
--

CREATE TABLE IF NOT EXISTS `terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(250) NOT NULL,
  `internal` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `term_documents`
--

CREATE TABLE IF NOT EXISTS `term_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL,
  `document_id` int(10) unsigned NOT NULL,
  `elements` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'specialization, subspecialization, treatment ids',
  `type` tinyint(3) unsigned NOT NULL COMMENT '1-SPECIALIZATION, 2-SUBSPECIALIZATION, 3-TREATMENT',
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`,`document_id`,`type`),
  KEY `document_id` (`document_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

DROP TABLE IF EXISTS `treatments`;
CREATE TABLE IF NOT EXISTS `treatments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialization_id` int(10) unsigned NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `specialization_id_2` (`specialization_id`,`name`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `treatment_sub_specializations`
--

DROP TABLE IF EXISTS `treatment_sub_specializations`;
CREATE TABLE IF NOT EXISTS `treatment_sub_specializations` (
  `treatment_id` int(10) unsigned NOT NULL,
  `sub_specialization_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`treatment_id`,`sub_specialization_id`),
  KEY `sub_specialization_id` (`sub_specialization_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='association table for treatment and sub_specializations';



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
-- Constraints for table `admin_user_type_roles`
--
ALTER TABLE `admin_user_type_roles`
  ADD CONSTRAINT `admin_user_type_roles_ibfk_1` FOREIGN KEY (`admin_user_type_id`) REFERENCES `admin_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_user_type_roles_ibfk_2` FOREIGN KEY (`admin_user_role_id`) REFERENCES `admin_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD CONSTRAINT `advertisements_ibfk_3` FOREIGN KEY (`advertisement_type_id`) REFERENCES `advertisement_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `advertisements_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `advertisement_media`
--
ALTER TABLE `advertisement_media`
  ADD CONSTRAINT `advertisement_media_ibfk_1` FOREIGN KEY (`advertisement_id`) REFERENCES `advertisements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `advertisement_media_ibfk_3` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `advertisement_property_names`
--
ALTER TABLE `advertisement_property_names`
  ADD CONSTRAINT `advertisement_property_names_ibfk_1` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `advertisement_type_configurations`
--
ALTER TABLE `advertisement_type_configurations`
  ADD CONSTRAINT `advertisement_type_configurations_ibfk_2` FOREIGN KEY (`advertisement_property_name_id`) REFERENCES `advertisement_property_names` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `advertisement_type_configurations_ibfk_1` FOREIGN KEY (`advertisement_type_id`) REFERENCES `advertisement_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctor_specializations`
--
ALTER TABLE `doctor_specializations`
  ADD CONSTRAINT `doctor_specializations_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `doctor_specializations_ibfk_3` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gallery`
--
ALTER TABLE `gallery`
  ADD CONSTRAINT `gallery_ibfk_2` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `gallery_media`
--
ALTER TABLE `gallery_media`
  ADD CONSTRAINT `gallery_media_ibfk_1` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `gallery_media_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `global_awards`
--
ALTER TABLE `global_awards`
  ADD CONSTRAINT `global_awards_ibfk_5` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`inquiry_subject_id`) REFERENCES `inquiry_subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `institutions_ibfk_1` FOREIGN KEY (`logo_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;
  
--
-- Constraints for table `institution_invitations`
--
ALTER TABLE `institution_invitations`
  ADD CONSTRAINT `institution_invitations_ibfk_1` FOREIGN KEY (`invitation_token_id`) REFERENCES `invitation_tokens` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_centers`
--
ALTER TABLE `institution_medical_centers`
  ADD CONSTRAINT `institution_medical_centers_ibfk_2` FOREIGN KEY (`logo_id`) REFERENCES `media` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_centers_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_center_media`
--
ALTER TABLE `institution_medical_center_media`
  ADD CONSTRAINT `institution_medical_center_media_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_center_media_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_center_properties`
--
ALTER TABLE `institution_medical_center_properties`
  ADD CONSTRAINT `institution_medical_center_properties_ibfk_3` FOREIGN KEY (`institution_property_type_id`) REFERENCES `institution_property_types` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_center_properties_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_medical_center_properties_ibfk_2` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_property_types`
--
ALTER TABLE `institution_property_types`
  ADD CONSTRAINT `institution_property_types_ibfk_1` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_specializations`
--
ALTER TABLE `institution_specializations`
  ADD CONSTRAINT `institution_specializations_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_specializations_ibfk_4` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_treatments`
--
ALTER TABLE `institution_treatments`
  ADD CONSTRAINT `institution_treatments_ibfk_1` FOREIGN KEY (`institution_specialization_id`) REFERENCES `institution_specializations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_treatments_ibfk_4` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON UPDATE CASCADE;

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
-- Constraints for table `medical_term_suggestion_details`
--
ALTER TABLE `medical_term_suggestion_details`
  ADD CONSTRAINT `medical_term_suggestion_details_ibfk_1` FOREIGN KEY (`medical_term_suggestion_id`) REFERENCES `medical_term_suggestions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `sub_specializations`
--
ALTER TABLE `sub_specializations`
  ADD CONSTRAINT `sub_specializations_ibfk_3` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `treatments`
--
ALTER TABLE `treatments`
  ADD CONSTRAINT `treatments_ibfk_1` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `treatment_sub_specializations`
--
ALTER TABLE `treatment_sub_specializations`
  ADD CONSTRAINT `treatment_sub_specializations_ibfk_1` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `treatment_sub_specializations_ibfk_2` FOREIGN KEY (`sub_specialization_id`) REFERENCES `sub_specializations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
