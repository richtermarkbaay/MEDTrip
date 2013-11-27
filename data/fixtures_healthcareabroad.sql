-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 18, 2013 at 04:54 PM
-- Server version: 5.5.24
-- PHP Version: 5.3.10-1ubuntu3.2

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `admin_actions`
--

INSERT INTO `admin_actions` (`id`, `name`, `description`, `status`) VALUES
(1, 'index', 'View Admin Home Page', 1),
(2, 'medical_centers.index', 'View All Medical Centers', 1),
(3, 'medical_centers.add', 'Add new medical center', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='advertisement table' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`id`, `institution_id`, `object_id`, `advertisement_type_id`, `title`, `description`, `date_created`, `date_expiry`, `status`) VALUES
(1, 1, 0, 1, 'test', 'test', '2013-02-14 06:47:16', '0000-00-00 00:00:00', 1);

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
  `external_url` varchar(200) NOT NULL,
  `highlights` text NOT NULL,
  `highlight_doctors` varchar(300) NOT NULL,
  `highlight_specializations` varchar(300) NOT NULL,
  `highlight_sub_specializations` varchar(300) NOT NULL,
  `highlight_treatments` varchar(300) NOT NULL,
  `highlight_featured_images` text,
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=7 ;

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

DROP TABLE IF EXISTS `awarding_bodies`;
CREATE TABLE IF NOT EXISTS `awarding_bodies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `website` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` smallint(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `awarding_bodies`
--

INSERT INTO `awarding_bodies` (`id`, `name`, `details`, `website`, `status`) VALUES
(1, 'Join Commission International (JCI)', 'Joint Commission International (JCI) is Healthcare Accreditation Body that is established in 1994. W', 'http://www.jointcommissio', 1),
(2, 'International Organization for Standardization (IS', 'ISO (International Organization for Standardization) which is founded in 1947 has become the world', 'http://www.iso.org/iso/ho', 1);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `business_hours`
--

DROP TABLE IF EXISTS `business_hours`;
CREATE TABLE IF NOT EXISTS `business_hours` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `weekday_bit_value` int(10) unsigned NOT NULL,
  `opening` time DEFAULT NULL,
  `closing` time DEFAULT NULL,
  `notes` varchar(250) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
CREATE TABLE IF NOT EXISTS `cities` (
  `id` int(10) unsigned NOT NULL,
  `state_id` bigint(20) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` char(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `institution_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `state_country_name` (`state_id`,`country_id`,`name`,`institution_id`),
  KEY `country_id` (`country_id`),
  KEY `state_id` (`state_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `name`, `slug`, `geo_city_id`, `old_id`, `status`) VALUES
(1, 1, 'test', 'test', 1, 1, 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `contact_details`
--

DROP TABLE IF EXISTS `contact_details`;
CREATE TABLE IF NOT EXISTS `contact_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) DEFAULT NULL,
  `number` varchar(25) NOT NULL,
  `country_id` int(10) DEFAULT NULL,
  `country_code` int(11) DEFAULT NULL,
  `area_code` varchar(25) DEFAULT NULL,
  `abbr` varchar(25) DEFAULT NULL,
  `extension` varchar(5) DEFAULT NULL,
  `from_new_widget` tinyint(4) DEFAULT NULL,
  `is_invalid` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2731 ;


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
  `geo_country_id` int(10) unsigned DEFAULT NULL,
  `old_id` int(10) unsigned DEFAULT NULL,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `name`, `abbr`, `code`, `slug`, `geo_country_id`, `old_id`, `status`) VALUES
(1, 'test', 'test', 'test', 'test', 1, 1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

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
  `suffix` varchar(50) DEFAULT NULL,
  `gender` smallint(1) unsigned DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `contact_number` text,
  `details` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `country_id` int(10) unsigned DEFAULT NULL,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `media_id` (`media_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `first_name`, `middle_name`, `last_name`, `suffix`, `gender`, `contact_email`, `contact_number`, `details`, `country_id`, `media_id`, `date_created`, `status`) VALUES
(1, 'test', NULL, 'test', NULL, NULL, NULL, '[{"number":"13241234324","type":"phone"}]', 'Dr Arihant Surana is a senior practicing cosmetic dermatologist and minimal invasive hair transplant surgeon. After his post graduate degree in dermatology, he has been practicing in the field of cosmetic dermatology and trichology and is the most sought after hair transplant surgeon in India.\r\nSurgical Expertise: Dr. Surana has done extensive research in the field of hair transplant and was first one to introduce customized hair transplant to all his clients.\r\nHonors &amp; Expertise: He specializes in minimal invasive painless hair transplant and many dermatological procedures like lasers, fillers, etc.', 1, NULL, '2013-01-17 03:43:25', 1),
(2, 'Pankaj', NULL, 'Chaturvedi', NULL, NULL, NULL, '[{"number":"","type":"phone"}]', 'Surgical Expertise: He is known widely for his acne and acne scar treatments, laser treatments, Botox&reg;, filler and anti-ageing treatments and state of the art hair transplantation procedures. He currently holds the position of senior consultant and co-director of dermatology at Adiva aesthetics.\r\nHonors &amp; Expertise: He has an excellent academic career and has achieved many prestigious awards nationally and internationally for his path breaking researches on hair disorders and baldness treatments.\r\n&nbsp;', 1, NULL, '2013-01-17 03:43:25', 1);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_contact_details`
--

DROP TABLE IF EXISTS `doctor_contact_details`;
CREATE TABLE IF NOT EXISTS `doctor_contact_details` (
  `doctor_id` bigint(20) unsigned NOT NULL,
  `contact_detail_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`doctor_id`,`contact_detail_id`),
  KEY `contact_detail_id` (`contact_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


--
-- Database: `healthcareabroad`
--

-- --------------------------------------------------------

--
-- Table structure for table `doctor_medical_specialities`
--

DROP TABLE IF EXISTS `doctor_medical_specialities`;
CREATE TABLE IF NOT EXISTS `doctor_medical_specialities` (
  `doctor_id` bigint(10) unsigned NOT NULL,
  `medical_speciality_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`doctor_id`,`medical_speciality_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `medical_speciality_id` (`medical_speciality_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

--
-- Dumping data for table `doctor_specializations`
--

INSERT INTO `doctor_specializations` (`doctor_id`, `specialization_id`) VALUES
(1, 1),
(2, 1);

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_messages`
--

DROP TABLE IF EXISTS `feedback_messages`;
CREATE TABLE IF NOT EXISTS `feedback_messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(500) NOT NULL,
  `email_address` varchar(250) NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `remote_address` varchar(100) NOT NULL,
  `country_id` int(15) NOT NULL,
  `http_user_agent` varchar(500) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 COMMENT='feedback messages' AUTO_INCREMENT=2 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='frontend dynamic routes' AUTO_INCREMENT=1 ;

INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(1, '/philippines/cebu/dentistry', 'FrontendBundle:Default:listCitySpecialization', '{"countryId":11,"cityId":23654,"specializationId":59}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(2, '/philippines/loyola/bariatric-surgery', 'FrontendBundle:Default:listCitySpecialization', '{"countryId":11,"cityId":107087,"specializationId":23}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(3, '/singapore/cardiothoracic-surgery/adult-cardiac-surgery', 'FrontendBundle:Default:listCountrySubSpecialization', '{"countryId":84,"specializationId":8,"subSpecializationId":128}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(4, '/singapore/bukit-timah/cardiothoracic-surgery/abdominal-aortic-aneurysm-aaa-surgery/treatment', 'FrontendBundle:Default:listCityTreatment', '{"countryId":84,"cityId":109057,"specializationId":8,"treatmentId":499}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(5, '/brazil/minas-gerais/cardiology', 'FrontendBundle:Default:listCitySpecialization', '{"countryId":90,"cityId":49034,"specializationId":3}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(6, '/brazil/cardiology', 'FrontendBundle:Default:listCountrySpecialization', '{"countryId":90,"specializationId":3}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(7, '/turkey/etiler/cardiology', 'FrontendBundle:Default:listCitySpecialization', '{"countryId":44,"cityId":341860,"specializationId":3}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(8, '/india/orthopedic-surgery/ankle-arthroplasty/treatment', 'FrontendBundle:Default:listCountryTreatment', '{"countryId":6,"specializationId":27,"treatmentId":283}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(9, '/turkey/dentistry/dental-implants-1/treatment', 'FrontendBundle:Default:listCountryTreatment', '{"countryId":44,"specializationId":59,"treatmentId":785}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(10, '/albania/cardiothoracic-surgery/adult-cardiac-surgery', 'FrontendBundle:Default:listCountrySubSpecialization', '{"countryId":7,"specializationId":8,"subSpecializationId":128}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(11, '/philippines/cebu-city/bariatric-surgery', 'FrontendBundle:Default:listCitySpecialization', '{"countryId":206,"cityId":1862103,"specializationId":23}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(12, '/philippines/bariatric-surgery', 'FrontendBundle:Default:listCountrySpecialization', '{"countryId":206,"specializationId":23}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(13, '/philippines/amaga/bariatric-surgery', 'FrontendBundle:Default:listCitySpecialization', '{"countryId":206,"cityId":1869528,"specializationId":23}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(14, '/philippines/amaga/bariatric-surgery/roux-en-y-gastric-bypass/treatment', 'FrontendBundle:Default:listCityTreatment', '{"countryId":206,"cityId":1869528,"specializationId":23,"treatmentId":1342}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(15, '/philippines/bariatric-surgery/roux-en-y-gastric-bypass/treatment', 'FrontendBundle:Default:listCountryTreatment', '{"countryId":206,"specializationId":23,"treatmentId":1342}', 1);
INSERT INTO `frontend_routes` (`id`, `uri`, `controller`, `variables`, `status`) VALUES(16, '/philippines/bariatric-surgery/bertsubspectest2', 'FrontendBundle:Default:listCountrySubSpecialization', '{"countryId":206,"specializationId":23,"subSpecializationId":192}', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `institution_id`, `date_created`) VALUES
(1, 1, '2013-02-14 06:50:55'),
(2, 2, '2013-02-14 06:50:55');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='awards, certificates and affiliations data' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `global_awards`
--

INSERT INTO `global_awards` (`id`, `type`, `name`, `details`, `awarding_body_id`, `country_id`, `status`) VALUES
(1, 1, 'test', 'test', 1, 1, 1),
(2, 2, 'certificate', 'gsgdg', 1, 1, 1),
(3, 3, 'affiliation', NULL, 1, 1, 1),
(4, 4, 'accreditation', NULL, 1, 1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `helper_text`
--

INSERT INTO `helper_text` (`id`, `route`, `details`, `status`) VALUES
(1, 'institution_signUp', 'Please provide the correct information below, and we will create an exclusive medical account listing for you.', 1),
(2, 'institution_login', '<h4>Some Text here that urge medical institution to sign up for Health Care Abroad Medical Listing!</h4>\r\n                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>', 1),
(3, 'test_route', 'test', 1);

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
  `contact_number` text,
  `message` text NOT NULL,
  `inquiry_subject_id` int(10) unsigned DEFAULT NULL,
  `clinic_name` varchar(100) DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `remote_address` varchar(250) NOT NULL,
  `http_user_agent` varchar(250) DEFAULT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inquiry_subject_id` (`inquiry_subject_id`),
  KEY `country_id` (`country_id`),
  KEY `city_id` (`city_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `inquiries`
--

INSERT INTO `inquiries` (`id`, `first_name`, `last_name`, `email`, `contact_number`, `message`, `inquiry_subject_id`, `clinic_name`, `country_id`, `city_id`, `date_created`, `remote_address`, `http_user_agent`, `status`) VALUES
(1, 'alnie', 'jacobe', 'alniejacobe@yahoo.com', NULL, 'this is test', 1, NULL, NULL, NULL, '2012-08-15 02:10:21', '', '', 1),
(3, 'alnie', 'jacobe', 'alniejacobe@yahoo.com', NULL, 'sad', 2, NULL, NULL, NULL, '2012-08-15 02:15:27', '', '', 1),
(4, 'alnie', 'jaocbe', 'alnite@yahoo.com', NULL, 'adasd asd asdas', 2, NULL, NULL, NULL, '2012-08-30 01:35:42', '', '', 1),
(5, 'sdfsdfsdf', 'sdfsdfdsf', 'sdfsdf@yahoo.com', 'sdfsdfsd', 'sdf sdf dsfsdf sdf', NULL, NULL, 9, 281344, '2013-03-07 03:10:18', '127.0.0.1', '', 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `inquiry_subjects`
--

INSERT INTO `inquiry_subjects` (`id`, `name`, `slug`, `status`) VALUES
(1, 'membership', 'test', 1),
(2, 'fees', 'saf', 1);

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
  `featured_media_id` bigint(20) unsigned DEFAULT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_number` text NOT NULL,
  `websites` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `website_back_up` text,
  `social_media_sites` varchar(250) DEFAULT '{facebook:"",twitter:"",googleplus:""}',
  `address1` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address_hint` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `city_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `zip_code` char(10) DEFAULT NULL,
  `state_id` bigint(20) DEFAULT NULL,
  `state` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `state_bak` varchar(225) DEFAULT NULL,
  `coordinates` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `paying_client` smallint(1) unsigned DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `signup_step_status` smallint(1) unsigned NOT NULL DEFAULT '1',
  `total_clinic_ranking_points` int(11) DEFAULT NULL COMMENT 'temporary field. this is the sum all ranking points of clinics of this institution',
  `is_from_internal_admin` smallint(1) unsigned DEFAULT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `city_id` (`city_id`),
  KEY `country_id` (`country_id`),
  KEY `logo_id` (`logo_id`),
  KEY `featured_media_id` (`featured_media_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=267 ;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `institution_type`, `name`, `description`, `logo_id`, `featured_media_id`, `contact_email`, `contact_number`, `websites`, `website_back_up`, `social_media_sites`, `address1`, `address_hint`, `city_id`, `country_id`, `zip_code`, `state_id`, `state`, `state_bak`, `coordinates`, `paying_client`, `date_modified`, `date_created`, `slug`, `signup_step_status`, `total_clinic_ranking_points`, `is_from_internal_admin`, `status`) VALUES

(1, 1, 'Ahalia Eye Hospital', 'Ahalia Foundation Eye Hospital, a unit of Ahalia International Foundation which started in 2005 stands tall on a rock solid reputation of reliability, affordability, quality and innovation. Now it has added one more feather to its cap-accreditation by the prestigious Joint Commission International, USA. JCI accreditation is the ultimate recognition in the field of health care and is awarded after a strenuous quality audit conducted by a team of international healthcare experts. \r\n\r\nWith this recognition, Ahalia is proud to herald the arrival of international standard health care to Kerala with special focus on quality improvement, patient safety and infection control. \r\n\r\nEquipped with six operation theaters and state of the art equipments, AFEH has the expertise in all the areas of ophthalmology including advanced Phaco surgery for cataract, latest Lasik treatment for refractive errors and specialty services for Retina, Glaucoma, Pediatric Ophthalmology, Low Vision Aids etc - all this at very affordable rates.', 1, NULL, 'mail@afeh.org', '{"country_code":"1","area_code":"4923","number":"225 000"}', '{"main":"http://www.ahaliafoundationeyehospital.org","facebook":"https://www.facebook.com/pages/Ahalia-foundation-eye-hospitals/387889344602118","twitter":"http://"}', NULL, '{facebook:"",twitter:"",googleplus:""}', '{"room_number":"","building":"","street":"Kanal Pirivu"}', NULL, 1, 1, '1344', 1, 'Kerala', NULL, '', 1, '2013-01-16 05:08:04', '2012-12-06 06:29:26', 'ahalia-eye-hospital', 0, NULL,NULL, 9),
(2, 3, 'Test Single Hospital', 'Apollo Gleneagles Hospitals Kolkata, a 510-bedded multispecialty tertiary care hospital, is a perfect blend of technological excellence, complete infrastructure, competent care and heartfelt hospitality.\r\n\r\nApollo Gleneagles Hospitals is a joint venture of Apollo Group of Hospitals, India and Parkway Health of Singapore.\r\n\r\nThe Parkway Group is a leading healthcare group in Asia. It provides more than 70% of private healthcare in Singapore. Its subsidiaries include Parkway Group Healthcare, which owns a network of regional hospitals and medical centers in Malaysia, India and Brunei; and Parkway Hospitals Singapore, which owns three hospitals in Singapore - East Shore, Gleneagles, Mount Elizabeth Hospitals and Parkway Health Day surgery Centre.', NULL, NULL, 'hospital@apollogleneagles.in', '{"country_code":"1","area_code":" 3323","number":" 203 040"}', '{"main":"http:\\/\\/kolkata.apollohospitals.com\\/","facebook":"","twitter":""}', NULL, '{facebook:"",twitter:"",googleplus:""}', '{"room_number":"","building":"Apollo Gleneagles Hospital","street":"No. 58, Canal Circular Road"}', NULL, NULL, NULL, '700054', NULL, 'West Bengal', NULL, '', 1, '2013-01-16 05:08:04', '2012-12-06 06:49:56', 'test-single-hospital', 0, NULL,NULL, 9),
(3, 1, 'Apollo Hospital, Bangalore', 'Apollo Hospitals, Bangalore ', NULL, NULL, 'customercare_bangalore@apollohospitals.com', '{"country_code":"1","area_code":"8026","number":"304 050"}', '{"main":"http:\\/\\/www.apollohospitalsbangalore.com\\/","facebook":"","twitter":""}', NULL, '{facebook:"",twitter:"",googleplus:""}', '{"room_number":"","building":"Apollo Hospitals - Bangalore","street":"154\\/11, Opp. IIM B, Bannerghatta Road"}', NULL, 1, 1, '560076', NULL, 'Karnataka', NULL, '', NULL, '2013-01-16 05:08:04', '2012-12-06 07:01:40', 'apollo-hospital-bangalore', 1, NULL,NULL, 9),
(4, 1, 'Apollo Hospital, Chennai', 'The flagship hospital of the Apollo Group, Apollo Hospitals Chennai, was established in 1983. Today it is one of the most respected hospitals in the world, and is also amongst the most preferred destinations for both patients from several parts of India, as well as for medical tourism and medical value travel. The hospital specializes in cutting-edge medical procedures. It has over 60 departments spearheaded by internationally trained doctors who are skillfully supported by dedicated patient-care personnel. It is one of the few hospitals in Chennai that have state of the art facilities for various health disorders.\r\n\r\nIt has been a pioneer among the hospitals in Chennai, and even in India, in many different treatments and procedures.', NULL, NULL, 'enquiry@apollohospitals.com', '{"country_code":"","area_code":"","number":""}', '{"main":"","facebook":"","twitter":""}', NULL, '{facebook:"",twitter:"",googleplus:""}', '{"room_number":"","building":"","street":"Apollo Hospitals - Chennai No. 21, Greams Lane, Off. Greams Road, Chennai 600006 India"}', NULL, 1, 1, '600006', NULL, 'Tammil Nadu', NULL, '', NULL, '2013-01-16 05:08:04', '2012-12-06 07:46:49', 'apollo-hospital-chennai', 0, NULL,NULL, 9),
(5, 3, 'Apollo Hospital, Hyderabad', 'Today, Apollo Hospitals, Hyderabad has risen to be on par with the best in the world, in terms of technical expertise, deliverables and outcomes. It has now evolved into a one of a kind institution , the Apollo Health City, Hyderabad, which is the first health city in Asia and a perfect example of an integrated healthcare system offering solutions across the healthcare space. A 350 bedded multi-specialty hospital with over 50 specialties and super-specialties, 10 Centers of Excellence, education, research, information technology, all in one sprawling campus creates an environment dedicated to healing.\r\n\r\nApollo Health City , Hyderabad covers the entire spectrum from illness to wellness and is thus a health city and not a medical city. Institutes for Heart Diseases, Cancer, Joint Diseases, Emergency, Renal Diseases, Neurosciences, Eye and Cosmetic Surgery are all centers of excellence and are positioned to offer the best care in the safest manner to every patient.\r\n\r\nApart from patient care, each of these Centers of Excellence spend a significant amount of time in training and research essentially aimed at preventing disease and improving outcomes when the disease does occur.\r\n\r\nMost of the consultants at the Health city have international experience either educational, work experience - related or observational. The average staff to patient ratio for the hospital is 3:1 with a 1:1 ratio prevailing in priority areas like the Intensive Care Unit and the Cardiac Care Unit.\r\n\r\nApollo Healthcity, Hyderabad handles close to 100,000 patients a year. International patients from Tanzania, the USA, the UAE, Kenya, Oman and neighbouring Asian countries are treated by the hospital every year.', NULL, NULL, 'apollohealthcity@apollohospitals.com', '', '', NULL, '{facebook:"",twitter:"",googleplus:""}', 'Apollo Hospitals Jubilee Hills Hyderabad Andhra Pradesh 500033 India', NULL, 1, 1, '500033', NULL, 'Andra Pradesh', NULL, '', NULL, '2013-01-16 05:08:04', '2012-12-06 07:56:18', 'apollo-hospital-hyderabad', 0, NULL,NULL, 9);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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

DROP TABLE IF EXISTS `institution_inquiries`;
CREATE TABLE IF NOT EXISTS `institution_inquiries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_medical_center_id` bigint(20) unsigned DEFAULT NULL COMMENT 'Null if this was made in the hospital profile page',
  `inquirer_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `inquirer_email` varchar(500) NOT NULL,
  `message` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `country_id` int(11) DEFAULT NULL,
  `remote_address` varchar(250) DEFAULT NULL,
  `http_user_agent` varchar(250) DEFAULT NULL,
  `status` tinyint(3) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COMMENT='inquiries for an institution or a medical center' AUTO_INCREMENT=3 ;

INSERT INTO `institution_inquiries` (`id`, `institution_id`, `institution_medical_center_id`, `inquirer_name`, `inquirer_email`, `message`, `date_created`, `country_id`, `remote_address`, `http_user_agent`, `status`) VALUES(1, 1, NULL, 'Hazel', 'hazel.caballero@chromedia.com', 'Test inquiry', '2013-07-05 07:34:46', NULL, NULL, NULL, 2);
INSERT INTO `institution_inquiries` (`id`, `institution_id`, `institution_medical_center_id`, `inquirer_name`, `inquirer_email`, `message`, `date_created`, `country_id`, `remote_address`, `http_user_agent`, `status`) VALUES(2, 1, 617, 'Kim Kieu', 'kieuk1@msn.com', 'How much (in US dollars) would a toal facelift and facial contouring costs including lodging? I am from USA. How long is the downtime? I ma 56 years old 114 lbs  5 feet 3. Also what can stem cell do for me? Thanks.', '2013-03-01 18:21:21', NULL, NULL, NULL, 1);
INSERT INTO `institution_inquiries` (`id`, `institution_id`, `institution_medical_center_id`, `inquirer_name`, `inquirer_email`, `message`, `date_created`, `country_id`, `remote_address`, `http_user_agent`, `status`) VALUES(3, 1, NULL, 'test from HCA', 'sdfsdf@yahoo.com', 'sadfsdf sdf dfsad fsd f', '2013-07-05 07:31:31', NULL, NULL, NULL, 1);
INSERT INTO `institution_inquiries` (`id`, `institution_id`, `institution_medical_center_id`, `inquirer_name`, `inquirer_email`, `message`, `date_created`, `country_id`, `remote_address`, `http_user_agent`, `status`) VALUES(4, 1, NULL, 'Alnie Jacobe', 'asldni@uahoo.com', 'kjahs jahsd', '2013-07-17 01:49:26', NULL, NULL, NULL, 2);
INSERT INTO `institution_inquiries` (`id`, `institution_id`, `institution_medical_center_id`, `inquirer_name`, `inquirer_email`, `message`, `date_created`, `country_id`, `remote_address`, `http_user_agent`, `status`) VALUES(5, 1, NULL, 'jer jacobe', 'kajsd@ayka.com', 'kal lahsdkjh', '2013-06-17 08:41:49', NULL, NULL, NULL, 0);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_centers`
--

CREATE TABLE IF NOT EXISTS `institution_medical_centers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `address` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `address_hint` varchar(250) COLLATE ucs2_unicode_ci DEFAULT NULL,
  `coordinates` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `business_hours` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `old_business_hours` varchar(500) COLLATE ucs2_unicode_ci DEFAULT NULL,
  `is_always_open` tinyint(1) DEFAULT NULL,
  `contact_number` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `contact_email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `websites` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `website_back_up` text COLLATE ucs2_unicode_ci,
  `social_media_sites` varchar(300) COLLATE ucs2_unicode_ci DEFAULT NULL,
  `description_highlight` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'this is the text that will appear in the results pages. 200 chars only',
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `logo_id` bigint(20) unsigned DEFAULT NULL,
  `paying_client` smallint(1) unsigned DEFAULT NULL,
  `ranking_points` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` varchar(250) CHARACTER SET latin1 NOT NULL,
  `is_from_internal_admin` smallint(1) unsigned DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id` (`institution_id`,`name`),
  KEY `logo_id` (`logo_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=ucs2 COLLATE=ucs2_unicode_ci AUTO_INCREMENT=734 ;

--
-- Dumping data for table `institution_medical_centers`
--

INSERT INTO `institution_medical_centers` (`id`, `institution_id`, `name`, `address`, `address_hint`, `coordinates`, `business_hours`, `old_business_hours`, `is_always_open`, `contact_number`, `contact_email`, `websites`, `website_back_up`, `social_media_sites`, `description_highlight`, `description`, `logo_id`, `paying_client`, `ranking_points`, `date_created`, `date_updated`, `slug`, `is_from_internal_admin`, `status`) VALUES
(1, 1, 'Pre-Admission', NULL, NULL, '', NULL, NULL, NULL, '', '', '', NULL, NULL, 'test', 'Location: Level B2, TTSH Medical Center\nContact Information: 6357 2244\nFax: 6357 2244\nRelated Departments and Clinics: Department of Anaesthesiology, Intensive Care and Pain Medicine\n&nbsp;\n\n&nbsp;', NULL, NULL, NULL, '2012-12-07 06:12:50', '2012-12-07 03:24:30', 'pre-admission-counselling-and-evaluation-pace-clinic',NULL, 2),
(2, 1, 'Audiology Services', NULL, NULL, '', NULL, NULL, NULL, '', '', '', NULL, NULL, 'test', '&nbsp;Location: Clinic 1 B, Level 1, TTSH Medical Center\nContact Information: 6357 8007 (Inquiry), 6357 8384\nFax: 6357 8384\nRelated Department: ENT (Audiology Services)&nbsp;', NULL, NULL, NULL, '2012-12-07 06:14:47', '2012-12-07 03:30:59', 'audiology-services',NULL, 2),
(3, 2, 'test single', NULL, NULL, '', NULL, NULL, NULL, '', '', '', NULL, NULL, 'test', '&nbsp;Location: Clinic 1 B, Level 1, TTSH Medical Center\nContact Information: 6357 8007 (Inquiry), 6357 8384\nFax: 6357 8384\nRelated Department: ENT (Audiology Services)&nbsp;', NULL, 1, NULL, '2012-12-07 06:14:47', '2012-12-07 03:30:59', 'test-single',NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_center_contact_details`
--

DROP TABLE IF EXISTS `institution_medical_center_contact_details`;
CREATE TABLE IF NOT EXISTS `institution_medical_center_contact_details` (
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `contact_detail_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`institution_medical_center_id`,`contact_detail_id`),
  KEY `contact_detail_id` (`contact_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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

--
-- Dumping data for table `institution_medical_center_doctors`
--

INSERT INTO `institution_medical_center_doctors` (`institution_medical_center_id`, `doctor_id`) VALUES
(1, 1),
(2, 2),
(3, 1);

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

INSERT INTO `institution_medical_center_media` (`institution_medical_center_id`, `media_id`) VALUES(1, 1);
INSERT INTO `institution_medical_center_media` (`institution_medical_center_id`, `media_id`) VALUES(2, 2);
INSERT INTO `institution_medical_center_media` (`institution_medical_center_id`, `media_id`) VALUES(3, 3);
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `institution_medical_center_properties`
--

INSERT INTO `institution_medical_center_properties` (`id`, `institution_id`, `institution_medical_center_id`, `institution_property_type_id`, `value`, `extra_value`) VALUES
(1, 1, 1, 1, '1', NULL),
(2, 2, 3, 1, '2', NULL),
(3, 2, 3, 3, '2', NULL),
(4, 1, 1, 3, '1', NULL);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `institution_properties`
--

INSERT INTO `institution_properties` (`institution_id`, `institution_property_type_id`, `value`, `extra_value`) VALUES
(1, 1, '1', 'test2'),
( 1, 3, '1', 'test23'),
( 2, 3, '2', 'test123'),
( 2, 1, '2', 'test1223');
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

--
-- Dumping data for table `institution_specializations`
--

INSERT INTO `institution_specializations` (`id`, `institution_medical_center_id`, `specialization_id`, `description`, `date_created`, `date_modified`, `status`) VALUES
(1, 1, 1, '<p>asdfsdf asdf asdf asdf</p>', '2012-12-08 17:31:05', '2012-12-08 17:31:05', 1),
(2, 2, 1, '<p>etc etc</p>', '2012-12-08 17:34:18', '2012-12-08 17:34:18', 1),
(3, 3, 3, 'test 3', '2013-02-22 07:23:45', '0000-00-00 00:00:00', 1);

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

--
-- Dumping data for table `institution_treatments`
--

INSERT INTO `fixtures_healthcareabroad`.`institution_treatments` (`institution_specialization_id`, `treatment_id`) VALUES ('1', '1');

-- --------------------------------------------------------

--
-- Table structure for table `institution_users`
--

DROP TABLE IF EXISTS `institution_users`;
CREATE TABLE IF NOT EXISTS `institution_users` (
  `account_id` bigint(20) unsigned NOT NULL,
  `institution_id` int(10) unsigned NOT NULL,
  `institution_user_type_id` int(10) unsigned DEFAULT NULL,
  `job_title` varchar(100) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`account_id`),
  KEY `institution_id` (`institution_id`),
  KEY `institution_user_type_id` (`institution_user_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institution_users`
--

INSERT INTO `institution_users` (`account_id`, `institution_id`, `institution_user_type_id`, `job_title`, `date_created`, `status`) VALUES
(1, 1, 1, 'job title test', '2013-02-13 02:12:02', 1),
(2, 1, 1, 'admin', '2013-02-15 02:02:39', 1),
(3, 2, 2, 'coder', '2013-02-15 05:59:58', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `institution_user_password_tokens`
--

DROP TABLE IF EXISTS `institution_user_password_tokens`;
CREATE TABLE IF NOT EXISTS `institution_user_password_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` bigint(20) NOT NULL,
  `token` varchar(64) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expiration_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `status` smallint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `institution_user_password_tokens`
--

INSERT INTO `institution_user_password_tokens` (`id`, `account_id`, `token`, `date_created`, `expiration_date`, `status`) VALUES
(1, 1, '25883977e3635cf8cc47bfeb8d822e4aeff213fb3f34d6b427278542a7db32f1', '2013-08-11 01:48:48', '2013-08-18 01:48:48', 1),
(2, 2, 'a2846338db37bb3cca03211ceb8910822a5bb7862c02efb211dce7859b426036', '2013-07-01 08:25:48', '2013-07-04 08:25:47', 1),
(3, 34, '25883977e3635cf8cc47bfeb8d822e4aeff213fb3f34d6b427278542a7db32f6', '2013-08-11 01:48:48', '2013-08-18 01:48:48', 1);
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `institution_user_types`
--

INSERT INTO `institution_user_types` (`id`, `institution_id`, `name`, `status`) VALUES
(1, 1, 'ADMIN', 3),
(2, 2, 'ADMIN', 3),
(3, 3, 'ADMIN', 3);

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
(1, 1),
(2, 2);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `iso_code`, `name`, `status`) VALUES
(1, 'en', 'English', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

INSERT INTO `media` (`id`, `uuid`, `name`, `caption`, `context`, `content_type`, `metadata`, `width`, `height`, `date_created`, `date_modified`) VALUES(1, '1358214261', '1358214261.jpg', 'hussein.jpg', '0', 'image/jpeg', NULL, 104, 104, '2013-01-15 01:44:21', '2013-01-15 01:44:21');
INSERT INTO `media` (`id`, `uuid`, `name`, `caption`, `context`, `content_type`, `metadata`, `width`, `height`, `date_created`, `date_modified`) VALUES(2, '1358998235', '135899823585.png', 'Screen Shot 2013-01-22 at 1.09.37 PM.png', '0', 'image/png', NULL, 276, 164, '2013-01-24 03:30:35', '2013-01-24 03:30:35');
INSERT INTO `media` (`id`, `uuid`, `name`, `caption`, `context`, `content_type`, `metadata`, `width`, `height`, `date_created`, `date_modified`) VALUES(3, '1358998362', '1358998362100.jpg', '1 (1).jpg', '0', 'image/jpeg', NULL, 750, 344, '2013-01-24 03:32:43', '2013-01-24 03:32:43');
INSERT INTO `media` (`id`, `uuid`, `name`, `caption`, `context`, `content_type`, `metadata`, `width`, `height`, `date_created`, `date_modified`) VALUES(4, '1359079896', '135907989669.jpg', '1 (1).jpg', '0', 'image/jpeg', NULL, 750, 344, '2013-01-25 02:11:36', '2013-01-25 02:11:36');

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=19 ;

--
-- Dumping data for table `medical_provider_groups`
--

INSERT INTO `medical_provider_groups` (`id`, `name`, `description`, `date_created`, `status`) VALUES
(18, 'test', 'test', '2013-07-18 07:54:53', 1);

-- --------------------------------------------------------

--
-- Table structure for table `medical_specialities`
--

DROP TABLE IF EXISTS `medical_specialities`;
CREATE TABLE IF NOT EXISTS `medical_specialities` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `specialization_id` int(10) unsigned NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`specialization_id`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=75 ;

--
-- Dumping data for table `medical_specialities`
--

INSERT INTO `medical_specialities` (`id`, `name`, `specialization_id`, `date_created`, `status`) VALUES
(1, 'Accupressure', 84, '2013-10-23 01:43:02', 1),
(2, 'Accupuncture', 84, '2013-10-23 01:43:02', 1),
(3, 'Ayurvedic Medicine', 84, '2013-10-23 01:43:02', 1),
(4, 'Chiropractic Therapy', 84, '2013-10-23 01:43:02', 1),
(5, 'Hyperbaric & Underwater Medicine', 84, '2013-10-23 01:43:02', 1),
(6, 'Traditional Chinese Medicine', 84, '2013-10-23 01:43:28', 1),
(7, 'Anesthesiology', 3, '2013-10-23 01:47:11', 1),
(8, 'Cardiothoracic Surgery', 3, '2013-10-23 01:47:11', 1),
(9, 'Vascular Surgery', 3, '2013-10-23 01:47:11', 1),
(10, 'Anti-Aging Medicine', 53, '2013-10-23 01:47:11', 1),
(11, 'Oral and Maxillofacial Surgery', 59, '2013-10-23 01:47:11', 1),
(12, 'Oral and Maxillofacial Radiology', 59, '2013-10-23 01:47:11', 1),
(13, 'Endoscopy', 5, '2013-10-23 01:47:11', 1),
(14, 'Adolescent Medicine', 93, '2013-10-23 01:47:11', 1),
(15, 'Aviation Medicine', 93, '2013-10-23 01:47:11', 1),
(16, 'Family Medicine', 93, '2013-10-23 01:47:11', 1),
(17, 'Geriatric Medicine', 93, '2013-10-23 01:47:11', 1),
(18, 'Breast Surgery', 42, '2013-10-23 01:51:05', 1),
(19, 'Colorectal Surgery', 42, '2013-10-23 01:51:05', 1),
(20, 'Endocrine Surgery', 42, '2013-10-23 01:51:05', 1),
(21, 'Hepato-biliary Pancreatic Surgery', 42, '2013-10-23 01:51:05', 1),
(22, 'Laparoscopic Surgery', 42, '2013-10-23 01:51:05', 1),
(23, 'Organ Transplantation', 42, '2013-10-23 01:51:05', 1),
(24, 'Surgical Oncology', 42, '2013-10-23 01:51:05', 1),
(25, 'Trauma Surgery', 42, '2013-10-23 01:51:05', 1),
(26, 'Urological Surgery', 42, '2013-10-23 01:51:05', 1),
(27, 'Endocrinology, Diabetes and Metabolism', 75, '2013-10-23 01:51:05', 1),
(28, 'Hepatology', 75, '2013-10-23 01:54:20', 1),
(29, 'Rheumatology', 75, '2013-10-23 01:54:20', 1),
(30, 'Neuromuscular Medicine', 19, '2013-10-23 01:54:20', 1),
(31, 'Pain Medicine', 19, '2013-10-23 01:54:20', 1),
(32, 'Parkinson and Movement Disorder', 19, '2013-10-23 01:54:20', 1),
(33, 'Speech-Language Pathology', 19, '2013-10-23 01:54:20', 1),
(34, 'Sleep Medicine', 19, '2013-10-23 01:54:20', 1),
(35, 'Vascular Neurology (Stroke)', 19, '2013-10-23 01:54:20', 1),
(36, 'Spine Surgery', 20, '2013-10-23 01:54:20', 1),
(37, 'Interventional Nephrology', 50, '2013-10-23 01:54:20', 1),
(38, 'Female Pelvic Medicine and Reconstructive Surgery', 72, '2013-10-23 01:57:27', 1),
(39, 'Maternal and Fetal Medicine', 72, '2013-10-23 01:57:27', 1),
(40, 'Dermato-oncology', 6, '2013-10-23 01:57:27', 1),
(41, 'Gastrointestinal Oncology', 6, '2013-10-23 01:57:27', 1),
(42, 'Gynecologic Oncology', 6, '2013-10-23 01:57:27', 1),
(43, 'Hematologic Oncology', 6, '2013-10-23 01:57:27', 1),
(44, 'Interventional Oncology', 6, '2013-10-23 01:57:27', 1),
(45, 'Medical Oncology', 6, '2013-10-23 01:57:27', 1),
(46, 'Neurologic Oncology', 6, '2013-10-23 01:57:27', 1),
(47, 'Radiation Oncology', 6, '2013-10-23 01:57:27', 1),
(48, 'Surgical Oncology', 6, '2013-10-23 02:00:35', 1),
(49, 'Urologic Oncology', 6, '2013-10-23 02:00:35', 1),
(50, 'Neuro-ophthalmology', 16, '2013-10-23 02:00:35', 1),
(51, 'Ophthalmic Pathology', 16, '2013-10-23 02:00:35', 1),
(52, 'Ophthalmic Surgery', 16, '2013-10-23 02:00:35', 1),
(53, 'Orthopedic Surgery', 89, '2013-10-23 02:00:35', 1),
(54, 'Podiatry', 89, '2013-10-23 02:00:35', 1),
(55, 'Sports Medicine', 89, '2013-10-23 02:00:35', 1),
(56, 'Audiology', 67, '2013-10-23 02:00:35', 1),
(57, 'Pediatric Cardiology', 68, '2013-10-23 02:00:35', 1),
(58, 'Pediatric Dermatology', 68, '2013-10-23 02:02:35', 1),
(59, 'Pediatric Gastroenterology', 68, '2013-10-23 02:02:35', 1),
(60, 'Pediatric Nephrology', 68, '2013-10-23 02:02:35', 1),
(61, 'Pediatric Neurology', 68, '2013-10-23 02:02:35', 1),
(62, 'Pediatric Neurosurgery', 68, '2013-10-23 02:02:35', 1),
(63, 'Pediatric Oncology', 68, '2013-10-23 02:02:35', 1),
(64, 'Pediatric Ophthalmology', 68, '2013-10-23 02:02:35', 1),
(65, 'Pediatric Pulmonology', 68, '2013-10-23 02:02:35', 1),
(66, 'Pediatric Surgery', 68, '2013-10-23 02:02:35', 1),
(67, 'Pediatric Transplant', 68, '2013-10-23 02:02:35', 1),
(68, 'Occupational Therapy', 24, '2013-10-23 02:05:50', 1),
(69, 'Bariatric Surgery', 12, '2013-10-23 02:05:50', 1),
(70, 'Cosmetic Surgery', 12, '2013-10-23 02:05:50', 1),
(71, 'Hair Restoration', 12, '2013-10-23 02:05:50', 1),
(72, 'Reconstructive Surgery', 12, '2013-10-23 02:05:50', 1),
(73, 'Psychology', 50, '2013-10-23 02:05:50', 1),
(74, 'Biology', 61, '2013-10-23 02:05:50', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

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
-- Table structure for table `page_meta_configurations`
--

DROP TABLE IF EXISTS `page_meta_configurations`;
CREATE TABLE IF NOT EXISTS `page_meta_configurations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `keywords` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `description` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `page_type` tinyint(2) unsigned NOT NULL COMMENT '1=static pages;2=institution pages;3=search results pages',
  `url` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'page url',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=155 ;

-- --------------------------------------------------------

--
-- Table structure for table `search_terms`
--

DROP TABLE IF EXISTS `search_terms`;
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
  `specialization_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `sub_specialization_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `treatment_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `country_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `city_name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `specializations`
--

DROP TABLE IF EXISTS `specializations`;
CREATE TABLE IF NOT EXISTS `specializations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `media_id` bigint(20) unsigned DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `media_id` (`media_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `specializations`
--

INSERT INTO `specializations` (`id`, `name`, `description`, `media_id`, `date_created`, `slug`, `status`) VALUES
(1, 'Allergy and Immunology', 'Medical center or department specializing in immunological disorders (autoimmune diseases, hypersensitivities, immune deficiency, transplant rejection, etc.)', NULL, '2012-09-03 03:50:10', 'allergy-and-immunology', 1),
(2, 'Pathology', 'Medical centers specialized in the diagnosis and characterization of disease in living patients by examining biopsies or bodily fluids.', NULL, '2012-09-03 06:54:38', 'pathology', 1),
(3, 'test', 'test', NULL, '2013-02-19 05:26:06', 'test', 1),
(4, 'dermatology', 'test', NULL, '2013-02-22 07:00:50', 'test', 1),
(5, 'test1', 'test1', NULL, '2013-02-19 05:26:06', 'test2', 1);


-- --------------------------------------------------------

--
-- Table structure for table `states`
--


DROP TABLE IF EXISTS `states`;
CREATE TABLE IF NOT EXISTS `states` (
  `id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `administrative_code` varchar(3) DEFAULT NULL,
  `institution_id` int(10) unsigned NOT NULL DEFAULT '0',
  `status` smallint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`,`country_id`,`institution_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `states`
--

INSERT INTO `fixtures_healthcareabroad`.`states` (`id`, `name`, `country_id`, `administrative_code`, `status`) VALUES ('1', 'test', '1', '01', '1');

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='treatments' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `sub_specializations`
--

INSERT INTO `sub_specializations` (`id`, `specialization_id`, `name`, `description`, `date_modified`, `date_created`, `slug`, `status`) VALUES
(1, 1, 'test ', 'test', '2013-02-20 00:39:04', '0000-00-00 00:00:00', 'test', 1),
(2, 2, 'sub specialization', 'test', '2013-02-20 00:39:30', '0000-00-00 00:00:00', 'test', 1),
(3, 3, 'sub specialization test', 'test', '2013-02-22 07:08:56', '0000-00-00 00:00:00', 'test1', 1),
(4, 5, 'sub specialization test1', 'test1', '2013-02-22 07:08:56', '0000-00-00 00:00:00', 'test1', 1);

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
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `terms`
--

DROP TABLE IF EXISTS `terms`;
CREATE TABLE IF NOT EXISTS `terms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `slug` varchar(250) NOT NULL,
  `internal` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `term_documents`
--

DROP TABLE IF EXISTS `term_documents`;
CREATE TABLE IF NOT EXISTS `term_documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `term_id` bigint(20) unsigned NOT NULL,
  `document_id` int(10) unsigned NOT NULL,
  `elements` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT 'specialization, subspecialization, treatment ids',
  `type` tinyint(3) unsigned NOT NULL COMMENT '1-SPECIALIZATION, 2-SUBSPECIALIZATION, 3-TREATMENT',
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`,`document_id`,`type`),
  KEY `document_id` (`document_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `timezones`
--

DROP TABLE IF EXISTS `timezones`;
CREATE TABLE IF NOT EXISTS `timezones` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `value` int(11) NOT NULL,
  `name` varchar(250) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

--
-- Dumping data for table `treatments`
--

INSERT INTO `treatments` (`id`, `specialization_id`, `name`, `description`, `slug`, `status`) VALUES
(1, 1, 'treatment', 'test', 'tset', 1),
(2, 2, 'test', 'test', 'test', 1),
(3, 1, 'test 3', 'test', 'test', 1),
(4, 3, 'testing', 'test', 'test', 1),
(5, 4, 'test treatment', 'test', 'test', 1),
(6, 5, 'treat', 'ererer', 'resr', 1),
(7, 1, 'testest', 'tsetes', '', 1);


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
-- Table structure for table `user_contact_details`
--

DROP TABLE IF EXISTS `user_contact_details`;
CREATE TABLE IF NOT EXISTS `user_contact_details` (
  `account_id` bigint(20) unsigned NOT NULL,
  `contact_detail_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`account_id`,`contact_detail_id`),
  KEY `contact_detail_id` (`contact_detail_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

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
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

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
-- Constraints for table `business_hours`
--
ALTER TABLE `business_hours`
  ADD CONSTRAINT `business_hours_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `doctor_contact_details`
--
ALTER TABLE `doctor_contact_details` ADD FOREIGN KEY ( `doctor_id` ) REFERENCES `fixtures_healthcareabroad`.`doctors` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

ALTER TABLE `doctor_contact_details` ADD FOREIGN KEY ( `contact_detail_id` ) REFERENCES `fixtures_healthcareabroad`.`contact_details` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

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
-- Constraints for table `institutions`
--
ALTER TABLE `institutions` ADD FOREIGN KEY ( `logo_id` ) REFERENCES `fixtures_healthcareabroad`.`media` ( `id` ) ON DELETE SET NULL ON UPDATE CASCADE ;

ALTER TABLE `institutions` ADD FOREIGN KEY ( `featured_media_id` ) REFERENCES `fixtures_healthcareabroad`.`media` ( `id` ) ON DELETE SET NULL ON UPDATE CASCADE ;

--
-- Constraints for table `institution_users`
--
ALTER TABLE `institution_users`
  ADD CONSTRAINT `institution_users_ibfk_2` FOREIGN KEY (`institution_user_type_id`) REFERENCES `institution_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_users_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `term_documents`
--
ALTER TABLE `term_documents` ADD FOREIGN KEY ( `term_id` ) REFERENCES `fixtures_healthcareabroad`.`terms` ( `id` ) ON DELETE CASCADE ON UPDATE CASCADE ;

--
-- Constraints for table `states`
--
ALTER TABLE `states` ADD CONSTRAINT `states_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;
