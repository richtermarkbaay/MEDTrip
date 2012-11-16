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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `admin_user_roles`
--

INSERT INTO `admin_user_roles` (`id`, `name`, `label`, `status`) VALUES
(1, 'SUPER_ADMIN', 'Owner/Super Admin', 3),
(2, 'CAN_VIEW_INSTITUTIONS', 'View all institutions', 2),
(3, 'CAN_MANAGE_INSTITUTION', 'Add or edit an institution', 2),
(4, 'CAN_DELETE_INSTITUTION', 'Delete or deactivate an institution', 2),
(5, 'CAN_VIEW_MEDICAL_CENTERS', 'View all medical centers', 2),
(6, 'CAN_MANAGE_MEDICAL_CENTER', 'Add or edit a medical center', 2),
(7, 'CAN_DELETE_MEDICAL_CENTER', 'Delete or deactivate a medical center', 2),
(8, 'CAN_VIEW_PROCEDURE_TYPES', 'View all medical procedure types', 2),
(9, 'CAN_MANAGE_PROCEDURE_TYPE', 'Add or edit a medical procedure type', 2),
(10, 'CAN_DELETE_PROCEDURE_TYPE', 'Delete or deactivate a procedure type', 2),
(11, 'CAN_VIEW_MEDICAL_PROCEDURES', 'View all medical procedures', 2),
(12, 'CAN_MANAGE_MEDICAL_PROCEDURE', 'Add or edit a medical procedure', 2),
(13, 'CAN_DELETE_MEDICAL_PROCEDURE', 'Delete or deactivate a medical procedure', 2);


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
(1, 'Test User Type with super admin role', 3),
(2, 'Normal user type', 2);

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

--
-- Dumping data for table `advertisements`
--

INSERT INTO `advertisements` (`id`, `institution_id`, `object_id`, `advertisement_type`, `title`, `description`, `date_created`, `status`) VALUES
(1, 1, 0, 1, 'Lorem ipsum', 'Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam, quis nostrud exerci tation ullamcorper suscipit lobortis nisl ut aliquip ex ea commodo consequat. Duis autem vel eum iriure dolor in hendrerit in vulputate velit esse molestie consequat, vel illum dolore eu feugiat nulla facilisis at vero eros et accumsan et iusto odio dignissim qui blandit praesent luptatum zzril delenit augue duis dolore te feugait nulla facilisi.', '2012-10-05 08:17:44', 1),
(2, 1, 108, 3, 'Claritas est', 'Claritas est etiam processus dynamicus, qui sequitur mutationem consuetudium lectorum. Mirum est notare quam littera gothica, quam nunc putamus parum claram, anteposuerit litterarum formas humanitatis per seacula quarta decima et quinta decima. Eodem modo typi, qui nunc nobis videntur parum clari, fiant sollemnes in futurum.', '2012-10-05 08:28:14', 1);

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
-- Table structure for table `awarding_bodies`
--

DROP TABLE IF EXISTS `awarding_bodies`;
CREATE TABLE IF NOT EXISTS `awarding_bodies` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `details` varchar(100) NOT NULL,
  `website` varchar(25) NOT NULL,
  `status` smallint(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;


--
-- Dumping data for table `awarding_bodies`
--

INSERT INTO `awarding_bodies` (`id`, `name`, `details`, `website`, `status`) VALUES
(1, 'test Again', 'test', 'test.com', 1),
(2, 'test', 'test', 'test.com', 1);


-- --------------------------------------------------------

--
-- Table structure for table `affiliations`
--

DROP TABLE IF EXISTS `affiliations`;
CREATE TABLE IF NOT EXISTS `affiliations` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `details` varchar(100) NOT NULL,
  `awarding_bodies_id` int(10) NOT NULL,
  `country_id` int(10) unsigned NOT NULL,
  `status` smallint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `country_id` (`country_id`),
  UNIQUE KEY `awarding_bodies_id` (`awarding_bodies_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `affiliations`
--

INSERT INTO `affiliations` (`id`, `name`, `details`, `awarding_bodies_id`, `country_id`, `status`) VALUES
(1, 'test affiliation', 'details test', 1, 11, 1);

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

--
-- Dumping data for table `cities`
--

INSERT INTO `cities` (`id`, `country_id`, `name`, `slug`, `status`) VALUES(1, 1, 'cebu', 'test', 1);
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

INSERT INTO `countries` (`id`, `name`, `abbr` , `code`, `slug`, `status`) VALUES(1, 'Philippines', 'PH', 63 ,'test', 1);

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
  `contact_email` varchar(100) NOT NULL,
  `contact_number` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `doctors`
--

INSERT INTO `doctors` (`id`, `first_name`, `middle_name`, `last_name`, `contact_email`, `contact_number`, `date_created`, `status`) VALUES
(1, 'Alnie', 'Leones', 'Jacobes', 'leons@test.com', '123123', '2012-09-27 08:58:06', 1),
(2, 'chaz', 'veloso', 'blance', 'blance@test.com', '321312', '2012-09-28 01:00:53', 1);

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
(1, 1);

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
  `controller` varchar(250) NOT NULL,
  `variables` text NOT NULL COMMENT 'JSON variables for this route',
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='frontend dynamic routes';


--
-- Dumping data for table `frontend_routes`
--

INSERT INTO `frontend_routes` (`id`, `uri`, `variables`, `status`) VALUES
(1, '/usa/new-york/some-data/for-database-storage', '{"countryId":1,"cityId":"1"}', 1);

-- --------------------------------------------------------

--
-- Table structure for table `frontend_route_variables`
--

DROP TABLE IF EXISTS `frontend_route_variables`;
CREATE TABLE IF NOT EXISTS `frontend_route_variables` (
  `frontend_route_id` bigint(20) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` bigint(20) NOT NULL,
  KEY `frontend_route_id` (`frontend_route_id`)
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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
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
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `logo` varchar(100) NOT NULL,
  `contact_email` varchar(100) NOT NULL,
  `contact_number` varchar(150) NOT NULL,
  `websites` varchar(200) NOT NULL,
  `address1` text NOT NULL,
  `city_id` int(10) unsigned DEFAULT NULL,
  `country_id` int(10) unsigned DEFAULT NULL,
  `zip_code` int(11) NOT NULL,
  `state` varchar(50) DEFAULT NULL COMMENT 'country state or province',
  `coordinates` varchar(100) NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `city_id` (`city_id`),
  KEY `country_id` (`country_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `institutions`
--

INSERT INTO `institutions` (`id`, `institution_type`, `name`, `description`, `logo`, `contact_email`, `contact_number`, `websites`, `address1`, `city_id`, `country_id`, `zip_code`, `state`, `coordinates`, `date_modified`, `date_created`, `slug`, `status`) VALUES
(1, 3, 'Dubai Hospital', 'The quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.\r\n\r\nThe quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.\r\n\r\nThe quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.\r\n\r\nThe quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.\r\n\r\nThe quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.\r\n\r\nThe quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.\r\n\r\nThe quick brown fox jump over the lazy dog. The quick brown fox jump over the lazy dog.', 'logo.jpg', '', '', '', 'eng,', 1, 1, 0, NULL, '', '2012-10-22 03:24:41', '2012-09-03 16:00:00', 'belo-churvaness-ness', 9),
(2, 3, 'Test Institution', 'dsadsfdasf', '', '', '', '', 'afadsfsd', 1, 1, 0, NULL, '', '2012-10-22 03:24:49', '2012-09-13 08:15:55', 'test-institution', 17);

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

INSERT INTO `institution_invitations` (`id`, `email`, `message`, `name`, `invitation_token_id`, `date_created`, `status`) VALUES
(1, 'test-invited-institution-user@chromedia.com', 'lorem ipsum', 'Test', '1', '2012-08-02 06:21:36', 1);


-- --------------------------------------------------------

--
-- Table structure for table `institution_languages_spoken`
--

DROP TABLE IF EXISTS `institution_languages_spoken`;
CREATE TABLE IF NOT EXISTS `institution_languages_spoken` (
  `institution_id` int(10) unsigned NOT NULL,
  `language_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`institution_id`,`language_id`),
  KEY `language_id` (`language_id`)
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
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `institution_id` int(10) unsigned NOT NULL,
  `name` varchar(250) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_updated` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` varchar(250) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `institution_id_2` (`institution_id`,`name`),
  KEY `institution_id` (`institution_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `institution_medical_centers`
--

INSERT INTO `institution_medical_centers` (`id`, `institution_id`, `name`, `description`, `date_created`, `date_updated`, `slug`, `status`) VALUES
(1, 1, 'adelbert center', '<p>the quick churva nels.</p>', '2012-10-23 07:04:47', '2012-10-22 01:26:37', 'adelbert-center', 2);


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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;


--
-- Dumping data for table `institution_specializations`
--

INSERT INTO `institution_specializations` (`id`, `institution_medical_center_id`, `specialization_id`, `description`, `date_created`, `date_modified`, `status`) VALUES
(1, 1, 1, 'sdf sdaf sadfa sdfasd fsd fsda fasd fsadf', '2012-10-22 01:28:19', '2012-10-22 01:28:19', 1);



-- --------------------------------------------------------

--
-- Table structure for table `institution_medical_center_doctors`
--

DROP TABLE IF EXISTS `institution_medical_center_doctors`;
CREATE TABLE IF NOT EXISTS `institution_medical_center_doctors` (
  `institution_medical_center_id` bigint(20) unsigned NOT NULL,
  `doctor_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`institution_medical_center_id`,`doctor_id`),
  KEY `doctor_id` (`doctor_id`),
  KEY `institution_medical_center_id` (`institution_medical_center_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='doctors to instiution medical center group association table';

-- --------------------------------------------------------


--
-- Dumping data for table `institution_medical_center_group_doctors`
--

INSERT INTO `institution_medical_center_doctors` (`institution_medical_center_id`, `doctor_id`) VALUES
(1, 1);


-- --------------------------------------------------------

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

--
-- Dumping data for table `institution_users`
--

INSERT INTO `institution_users` (`account_id`, `institution_id`, `institution_user_type_id`, `date_created`, `status`) VALUES
(1, 1, 2, '2012-08-02 03:43:12', 1),
(4, 1, 1, '2012-08-02 03:43:12', 1);


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

INSERT INTO `institution_user_invitations` (`id`, `institution_id`, `invitation_token_id`, `email`, `message`, `first_name`, `middle_name`, `last_name`, `date_created`, `status`) VALUES
(1, 1, 1, 'test-invited-institution-user@chromedia.com', 'lorem ipsum', 'Test', 'Invited', 'User', '2012-08-02 06:21:36', 1);

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
(1, 'add_treatment_procedure', 'Add medical procedure', 1),
(2, 'SUPER_ADMIN', 'owner/admin', 3)
;

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
(1, 1, 'Content Staff', 1),
(2, 1, 'SUPER_ADMIN', 3),
(3, 1, 'Content Staff-2', 2);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

--
-- Dumping data for table `invitation_tokens`
--

INSERT INTO `invitation_tokens` (`id`, `token`, `date_created`, `expiration_date`, `status`) VALUES
(1, '94f348d1f65c54cae854b22e5fcc949b408da4682efd9567a66fdbe8323595b7', '2012-08-02 06:19:20', '2012-09-01 06:19:20', 1);


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
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


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

DROP TABLE IF EXISTS `specializations`;
CREATE TABLE IF NOT EXISTS `specializations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `slug` char(100) NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=110 ;

--
-- Dumping data for table `medical_centers`
--

INSERT INTO `specializations` (`id`, `name`, `description`, `date_created`, `slug`, `status`) VALUES
(1, 'Specialization 1', 'this is from test', '2012-07-30 00:34:30', 'specialization-1', 1);


-- --------------------------------------------------------

--
-- Table structure for table `sub_specializations`
--

DROP TABLE IF EXISTS `sub_specializations`;
CREATE TABLE IF NOT EXISTS `sub_specializations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialization_id` int(10) unsigned NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='treatments';

--
-- Dumping data for table `sub_specializations`
--

INSERT INTO `sub_specializations` (`id`, `specialization_id`, `name`, `description`, `date_modified`, `date_created`, `slug`, `status`) VALUES
(1, 1, 'Sub with treatments', 'test', '2012-09-25 16:23:12', '2012-07-29 23:40:08', 'procedure-type-for-philippine-center', 1),
(2, 1, 'Sub with no treatments', 'test', '2012-09-25 16:23:12', '2012-07-29 23:40:08', 'procedure-type-for-philippine-center', 1);


-- --------------------------------------------------------

--
-- Table structure for table `treatments`
--

DROP TABLE IF EXISTS `treatments`;
CREATE TABLE IF NOT EXISTS `treatments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `specialization_id` int(10) unsigned NOT NULL,
  `name` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `slug` char(100) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `specialization_id` (`specialization_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `treatments`
--

INSERT INTO `treatments` (`id`, `specialization_id`, `name`, `description`, `slug`, `status`) VALUES
(1, 1, 'Treatment with sub specialization', '', 'treatment-1', 1),
(2, 1, 'Treatment with no sub specialization', '', 'treatment-2', 1);


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

--
-- Dumping data for table `treatment_sub_specializations`
--

INSERT INTO `treatment_sub_specializations` (`treatment_id`, `sub_specialization_id`) VALUES
(1, 2);



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
  `description` varchar(250) NOT NULL,
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

--
-- Dumping data for table `offered_services`
--

INSERT INTO `offered_services` (`id`, `name`, `status`, `date_created`) VALUES
(1, 'spa massage', 1, '2012-09-20 03:59:22'),
(2, 'Pedicure', 1, '2012-09-21 01:53:29');


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

INSERT INTO `gallery` (`id`, `institution_id`) VALUES
(1, 1);

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
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=35 ;


--
-- Constraints for dumped tables
--


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
-- Constraints for table `affiliations`
--
ALTER TABLE `affiliations`
  ADD CONSTRAINT `affiliations_ibfk_1` FOREIGN KEY (`awarding_bodies_id`) REFERENCES `awarding_bodies` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `affiliations_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_center_media`
--
ALTER TABLE `institution_medical_center_media`
  ADD CONSTRAINT `institution_medical_center_media_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `institution_medical_center_media_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD CONSTRAINT `admin_users_ibfk_1` FOREIGN KEY (`admin_user_type_id`) REFERENCES `admin_user_types` (`id`);

--
-- Constraints for table `admin_user_type_roles`
--
ALTER TABLE `admin_user_type_roles`
  ADD CONSTRAINT `admin_user_type_roles_ibfk_2` FOREIGN KEY (`admin_user_role_id`) REFERENCES `admin_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `admin_user_type_roles_ibfk_1` FOREIGN KEY (`admin_user_type_id`) REFERENCES `admin_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;


--
-- Constraints for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD CONSTRAINT `advertisements_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `advertisement_media`
--
ALTER TABLE `advertisement_media`
  ADD CONSTRAINT `advertisement_media_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`),
  ADD CONSTRAINT `advertisement_media_ibfk_1` FOREIGN KEY (`advertisement_id`) REFERENCES `advertisements` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `cities`
--
ALTER TABLE `cities`
  ADD CONSTRAINT `cities_ibfk_1` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `doctor_specializations`
--
ALTER TABLE `doctor_specializations`
  ADD CONSTRAINT `doctor_specializations_ibfk_2` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `doctor_specializations_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
  
--
-- Constraints for table `frontend_route_variables`
--
ALTER TABLE `frontend_route_variables`
  ADD CONSTRAINT `frontend_route_variables_ibfk_1` FOREIGN KEY (`frontend_route_id`) REFERENCES `frontend_routes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD CONSTRAINT `inquiries_ibfk_1` FOREIGN KEY (`inquiry_subject_id`) REFERENCES `inquiry_subjects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `institutions`
--
ALTER TABLE `institutions`
  ADD CONSTRAINT `institutions_ibfk_2` FOREIGN KEY (`country_id`) REFERENCES `countries` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institutions_ibfk_1` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON UPDATE CASCADE;


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
-- Constraints for table `institution_languages_spoken`
--
ALTER TABLE `institution_languages_spoken`
  ADD CONSTRAINT `institution_languages_spoken_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_languages_spoken_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_media`
--
ALTER TABLE `institution_media`
  ADD CONSTRAINT `institution_media_ibfk_1` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_medical_centers`
--
ALTER TABLE `institution_medical_centers`
  ADD CONSTRAINT `institution_medical_centers_ibfk_1` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `institution_specializations`
--
ALTER TABLE `institution_specializations`
  ADD CONSTRAINT `institution_specializations_ibfk_2` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_specializations_ibfk_1` FOREIGN KEY (`institution_medical_center_id`) REFERENCES `institution_medical_centers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `institution_treatments`
--
ALTER TABLE `institution_treatments`
  ADD CONSTRAINT `institution_treatments_ibfk_2` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_treatments_ibfk_1` FOREIGN KEY (`institution_specialization_id`) REFERENCES `institution_specializations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
  ADD CONSTRAINT `institution_user_type_roles_ibfk_2` FOREIGN KEY (`institution_user_role_id`) REFERENCES `institution_user_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `institution_user_type_roles_ibfk_1` FOREIGN KEY (`institution_user_type_id`) REFERENCES `institution_user_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`log_class_id`) REFERENCES `log_classes` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `sub_specializations`
--
ALTER TABLE `sub_specializations`
  ADD CONSTRAINT `sub_specializations_ibfk_1` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `treatments`
--
ALTER TABLE `treatments`
  ADD CONSTRAINT `treatments_ibfk_1` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON UPDATE CASCADE;
  
--
-- Constraints for table `treatment_sub_specializations`
--
ALTER TABLE `treatment_sub_specializations`
  ADD CONSTRAINT `treatment_sub_specializations_ibfk_2` FOREIGN KEY (`sub_specialization_id`) REFERENCES `sub_specializations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `treatment_sub_specializations_ibfk_1` FOREIGN KEY (`treatment_id`) REFERENCES `treatments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `medical_term_suggestion_details`
--
ALTER TABLE `medical_term_suggestion_details`
  ADD CONSTRAINT `medical_term_suggestion_details_ibfk_1` FOREIGN KEY (`medical_term_suggestion_id`) REFERENCES `medical_term_suggestions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
