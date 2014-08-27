
/*Table structure for table `p_audit_trail` */

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

/*Data for the table `p_audit_trail` */

/*Table structure for table `p_group` */

DROP TABLE IF EXISTS `p_group`;

CREATE TABLE `p_group` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `organization_id` int(11) NOT NULL COMMENT 'Foreign Key Organization ID',
  `group_name` varchar(255) CHARACTER SET utf8 NOT NULL COMMENT 'Group Name',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `p_group` */

/*Table structure for table `p_organization` */

DROP TABLE IF EXISTS `p_organization`;

CREATE TABLE `p_organization` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `organization_name` varchar(255) NOT NULL COMMENT 'Organization Name',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `p_organization` */

/*Table structure for table `p_role` */

DROP TABLE IF EXISTS `p_role`;

CREATE TABLE `p_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `role_name` varchar(255) NOT NULL,
  `role_description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=latin1;

/*Data for the table `p_role` */

insert  into `p_role`(`id`,`role_name`,`role_description`) values (1,'admin','ADMIN - Administrator/Manajemen');

/*Table structure for table `p_user` */

DROP TABLE IF EXISTS `p_user`;

CREATE TABLE `p_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nip` varchar(255) NOT NULL COMMENT 'NIP',
  `firstname` varchar(255) NOT NULL COMMENT 'Firstname',
  `lastname` varchar(255) NOT NULL COMMENT 'Lastname',
  `email` varchar(255) NOT NULL COMMENT 'E-Mail',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Phone',
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` varchar(255) NOT NULL COMMENT 'Password',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='User Management';

/*Data for the table `p_user` */

insert  into `p_user`(`id`,`nip`,`firstname`,`lastname`,`email`,`phone`,`username`,`password`) values (1,'12345','Admin','Admin','admin@web.com','00000000','admin','21232f297a57a5a743894a0e4a801fc3');

/*Table structure for table `p_user_info` */

DROP TABLE IF EXISTS `p_user_info`;

CREATE TABLE `p_user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `p_user_info` */

/*Table structure for table `p_user_role` */

DROP TABLE IF EXISTS `p_user_role`;

CREATE TABLE `p_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) NOT NULL COMMENT 'User ID',
  `role_id` int(11) NOT NULL COMMENT 'ROle ID',
  `default_role` char(1) DEFAULT '1' COMMENT 'Default Role',
  `group_id` int(11) DEFAULT NULL COMMENT 'Group ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

/*Data for the table `p_user_role` */

insert  into `p_user_role`(`id`,`user_id`,`role_id`,`default_role`,`group_id`) values (1,1,1,'1',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
