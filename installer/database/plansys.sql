-- Adminer 4.1.0 MySQL dump
SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `p_audit_trail`;
CREATE TABLE `p_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `old_value` text COLLATE utf8_unicode_ci,
  `new_value` text COLLATE utf8_unicode_ci,
  `action` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `model` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `field` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `stamp` datetime NOT NULL,
  `user_id` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `p_role`;
CREATE TABLE `p_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `role_name` varchar(255) NOT NULL,
  `role_description` varchar(255) NOT NULL,
  `parent_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `p_role` (`id`, `role_name`, `role_description`, `parent_id`) VALUES
(1,	'dev',	'Developer',	0);

DROP TABLE IF EXISTS `p_user`;
CREATE TABLE `p_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nip` varchar(255) NOT NULL COMMENT 'NIP',
  `fullname` varchar(255) NOT NULL COMMENT 'Fullname',
  `email` varchar(255) NOT NULL COMMENT 'E-Mail',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Phone',
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` varchar(255) NOT NULL COMMENT 'Password',
  `date` date NOT NULL COMMENT 'Date',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `p_user` (`id`, `nip`, `fullname`, `email`, `phone`, `username`, `password`, `date`) VALUES
(1,	'12345',	'Admin',	'admin@web.com',	'00000000',	'admin',	'827ccb0eea8a706c4c34a16891f84e7b',	'0000-00-00');

DROP TABLE IF EXISTS `p_user_info`;
CREATE TABLE `p_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `p_user_info_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `p_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `p_user_role`;
CREATE TABLE `p_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `role_id` int(11) NOT NULL COMMENT 'Role ID',
  `default_role` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `p_user_role_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `p_user` (`id`),
  CONSTRAINT `p_user_role_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `p_role` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `p_user_role` (`id`, `user_id`, `role_id`, `default_role`) VALUES
(1,	1,	1,	1);

-- 2014-08-27 09:30:37