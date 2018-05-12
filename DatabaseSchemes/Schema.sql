-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 12, 2018 at 09:54 AM
-- Server version: 5.5.50
-- PHP Version: 5.6.24-1~dotdeb+7.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `HeFotovoltaico`
--

-- --------------------------------------------------------

--
-- Table structure for table `contents`
--

CREATE TABLE IF NOT EXISTS `contents` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `research_id` int(11) unsigned NOT NULL,
  `research_element_id` int(11) unsigned NOT NULL,
  `subject_id` int(11) unsigned NOT NULL,
  `link` text NOT NULL,
  `content` text NOT NULL,
  `created_at` datetime NOT NULL,
  `social_id` text NOT NULL,
  `language` varchar(4) NOT NULL,
  `favorite_count` int(11) NOT NULL,
  `retweet_count` int(11) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `comfort` int(11) NOT NULL,
  `energy` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `research_id` (`research_id`),
  KEY `research_element_id` (`research_element_id`),
  KEY `subject_id` (`subject_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=849008 ;

-- --------------------------------------------------------

--
-- Table structure for table `contents_entities`
--

CREATE TABLE IF NOT EXISTS `contents_entities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned NOT NULL,
  `research_id` int(11) unsigned NOT NULL,
  `research_element_id` int(11) unsigned NOT NULL,
  `entity_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `research_id` (`research_id`),
  KEY `research_element_id` (`research_element_id`),
  KEY `entity_id` (`entity_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=605059 ;

-- --------------------------------------------------------

--
-- Table structure for table `emotions`
--

CREATE TABLE IF NOT EXISTS `emotions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `research_id` int(11) unsigned NOT NULL,
  `research_element_id` int(11) unsigned NOT NULL,
  `content_id` int(11) unsigned NOT NULL,
  `emotion_type_id` int(11) unsigned NOT NULL,
  `c` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `research_id` (`research_id`),
  KEY `research_element_id` (`research_element_id`),
  KEY `content_id` (`content_id`),
  KEY `emotion_type_id` (`emotion_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=289259 ;

-- --------------------------------------------------------

--
-- Table structure for table `emotion_types`
--

CREATE TABLE IF NOT EXISTS `emotion_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `color` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '0x000000',
  `comfort` int(11) NOT NULL DEFAULT '0',
  `energy` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=37 ;

-- --------------------------------------------------------

--
-- Table structure for table `entities`
--

CREATE TABLE IF NOT EXISTS `entities` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `entity_type_id` int(11) unsigned NOT NULL,
  `entity` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `entity_type_id` (`entity_type_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=154168 ;

-- --------------------------------------------------------

--
-- Table structure for table `entity_types`
--

CREATE TABLE IF NOT EXISTS `entity_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `relations`
--

CREATE TABLE IF NOT EXISTS `relations` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `research_id` int(11) unsigned NOT NULL,
  `research_element_id` int(11) unsigned NOT NULL,
  `subject_1_id` int(11) unsigned NOT NULL,
  `subject_2_id` int(11) unsigned NOT NULL,
  `c` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `research_id` (`research_id`),
  KEY `research_element_id` (`research_element_id`),
  KEY `subject_1_id` (`subject_1_id`),
  KEY `subject_2_id` (`subject_2_id`),
  KEY `c` (`c`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=738171 ;

-- --------------------------------------------------------

--
-- Table structure for table `researches`
--

CREATE TABLE IF NOT EXISTS `researches` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `twitter_consumer_key` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `twitter_consumer_secret` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `twitter_token` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `twitter_token_secret` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `twitter_bearer_token` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `insta_client_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `insta_token` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fb_app_id` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fb_app_secret` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `last_updated` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=124 ;

-- --------------------------------------------------------

--
-- Table structure for table `research_elements`
--

CREATE TABLE IF NOT EXISTS `research_elements` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `research_element_type_id` int(11) unsigned NOT NULL,
  `research_id` int(11) unsigned NOT NULL,
  `content` text NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `language` varchar(4) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `updated_last` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `research_element_type_id` (`research_element_type_id`),
  KEY `research_id` (`research_id`),
  KEY `updated_last` (`updated_last`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=198 ;

-- --------------------------------------------------------

--
-- Table structure for table `research_element_types`
--

CREATE TABLE IF NOT EXISTS `research_element_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `smiley_emotions`
--

CREATE TABLE IF NOT EXISTS `smiley_emotions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `smiley` varchar(12) COLLATE utf8_unicode_ci NOT NULL,
  `emotion_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=70 ;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE IF NOT EXISTS `subjects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `research_element_id` int(11) unsigned NOT NULL,
  `research_id` int(11) unsigned NOT NULL,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `social_id` text NOT NULL,
  `screen_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `followers_count` int(11) NOT NULL,
  `friends_count` int(11) NOT NULL,
  `listed_count` int(11) NOT NULL,
  `language` varchar(4) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `profile_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `profile_image_url` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `research_element_id` (`research_element_id`),
  KEY `research_id` (`research_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=780277 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `word_emotions`
--

CREATE TABLE IF NOT EXISTS `word_emotions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `word` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `emotion_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1588 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `contents`
--
ALTER TABLE `contents`
  ADD CONSTRAINT `contents_ibfk_1` FOREIGN KEY (`research_id`) REFERENCES `researches` (`id`),
  ADD CONSTRAINT `contents_ibfk_2` FOREIGN KEY (`research_element_id`) REFERENCES `research_elements` (`id`);

--
-- Constraints for table `contents_entities`
--
ALTER TABLE `contents_entities`
  ADD CONSTRAINT `contents_entities_ibfk_3` FOREIGN KEY (`research_id`) REFERENCES `researches` (`id`),
  ADD CONSTRAINT `contents_entities_ibfk_4` FOREIGN KEY (`research_element_id`) REFERENCES `research_elements` (`id`),
  ADD CONSTRAINT `contents_entities_ibfk_5` FOREIGN KEY (`entity_id`) REFERENCES `entities` (`id`);

--
-- Constraints for table `emotions`
--
ALTER TABLE `emotions`
  ADD CONSTRAINT `emotions_ibfk_1` FOREIGN KEY (`research_id`) REFERENCES `researches` (`id`),
  ADD CONSTRAINT `emotions_ibfk_2` FOREIGN KEY (`research_element_id`) REFERENCES `research_elements` (`id`),
  ADD CONSTRAINT `emotions_ibfk_4` FOREIGN KEY (`emotion_type_id`) REFERENCES `emotion_types` (`id`);

--
-- Constraints for table `entities`
--
ALTER TABLE `entities`
  ADD CONSTRAINT `entities_ibfk_1` FOREIGN KEY (`entity_type_id`) REFERENCES `entity_types` (`id`);

--
-- Constraints for table `relations`
--
ALTER TABLE `relations`
  ADD CONSTRAINT `relations_ibfk_1` FOREIGN KEY (`research_id`) REFERENCES `researches` (`id`),
  ADD CONSTRAINT `relations_ibfk_2` FOREIGN KEY (`research_element_id`) REFERENCES `research_elements` (`id`);

--
-- Constraints for table `researches`
--
ALTER TABLE `researches`
  ADD CONSTRAINT `researches_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `research_elements`
--
ALTER TABLE `research_elements`
  ADD CONSTRAINT `research_elements_ibfk_1` FOREIGN KEY (`research_element_type_id`) REFERENCES `research_element_types` (`id`),
  ADD CONSTRAINT `research_elements_ibfk_2` FOREIGN KEY (`research_id`) REFERENCES `researches` (`id`);

--
-- Constraints for table `subjects`
--
ALTER TABLE `subjects`
  ADD CONSTRAINT `subjects_ibfk_1` FOREIGN KEY (`research_element_id`) REFERENCES `research_elements` (`id`),
  ADD CONSTRAINT `subjects_ibfk_2` FOREIGN KEY (`research_id`) REFERENCES `researches` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
