<?php

class Installer {

    private static $_errorList = [];

    public static function getErrorList() {
        return Installer::$_errorList;
    }

    public static function setError($group, $idx, $error) {
        if (!isset(Installer::$_errorList[$group])) {
            Installer::$_errorList[$group] = [];
        }
        Installer::$_errorList[$group][$idx] = $error;
    }

    public static function getError($group = "", $idx = -1) {
        if ($group == "") {
            return Installer::$_errorList;
        } else if (!isset(Installer::$_errorList[$group])) {
            return false;
        } else {
            if ($idx == -1) {
                return true;
            } else if (isset(Installer::$_errorList[$group][$idx])) {
                return Installer::$_errorList[$group][$idx];
            } else {
                return false;
            }
        }
    }

    public static function checkServerVar() {
        $vars = array('HTTP_HOST', 'SERVER_NAME', 'SERVER_PORT', 'SCRIPT_NAME', 'SCRIPT_FILENAME', 'PHP_SELF', 'HTTP_ACCEPT', 'HTTP_USER_AGENT');
        $missing = array();
        foreach ($vars as $var) {
            if (!isset($_SERVER[$var]))
                $missing[] = $var;
        }
        if (!empty($missing)) {
            return Setting::t('$_SERVER does not have {vars}.', array('{vars}' => implode(', ', $missing)));
        }

        if (realpath($_SERVER["SCRIPT_FILENAME"]) !== realpath(Setting::$entryScript)) {
            return Setting::t('$_SERVER["SCRIPT_FILENAME"] <br/> `{s}`<br/><br/> must be the same as the entry script file path `{r}`.', [
                        '{s}' => realpath($_SERVER["SCRIPT_FILENAME"]),
                        '{r}' => realpath(__FILE__)
            ]);
        }

        if (!isset($_SERVER["REQUEST_URI"]) && isset($_SERVER["QUERY_STRING"])) {
            return Setting::t('Either $_SERVER["REQUEST_URI"] or $_SERVER["QUERY_STRING"] must exist.');
        }

        if (!isset($_SERVER["PATH_INFO"]) && strpos($_SERVER["PHP_SELF"], $_SERVER["SCRIPT_NAME"]) !== 0) {
            return Setting::t('Unable to determine URL path info. Please make sure $_SERVER["PATH_INFO"] (or $_SERVER["PHP_SELF"] and $_SERVER["SCRIPT_NAME"]) contains proper value.');
        }

        if (version_compare(PHP_VERSION, '5.6.6') >= 0 && ini_get("always_populate_raw_post_data") != -1) {
          ## see http://stackoverflow.com/questions/26261001/warning-about-http-raw-post-data-being-deprecated
          return Setting::t("Please set 'always_populate_raw_post_data' to '-1' in php.ini and restart your server.");
        }

        return true;
    }

    public static function getCheckList($checkGroup = "") {
        $checkLists = [
            "Checking Directory Permission" => [
                [
                    "title" => 'Checking base directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getBasePath(), true);
                    }
                ],
                [
                    "title" => 'Checking app directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getAppPath());
                    }
                ],
                [
                    "title" => 'Checking assets directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getAssetPath(), true);
                    }
                ],
                [
                    "title" => 'Checking runtime directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getRuntimePath(), true);
                    }
                ],
                [
                    "title" => 'Checking config directory permissions',
                    "check" => function() {
                        return Setting::checkPath(Setting::getConfigPath(), true);
                    }
                ],
                [
                    "title" => 'Checking repository directory permissions',
                    "check" => function() {
                        $repo = Setting::get('repo.path');
                        if (!is_dir($repo)) {
                            @mkdir($repo, 0777, true);
                        }

                        return Setting::checkPath(realpath($repo), true);
                    }
                ]
            ],
            "Checking Framework Requirements" => [
                [
                    'title' => 'Checking PHP Version ( > 5.5.0 )',
                    'check' => function() {
                        $result = version_compare(PHP_VERSION, "5.5.0", ">");
                        $msg = "Current PHP version is:" . PHP_VERSION;
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'Reflection Extension',
                    'check' => function() {
                        $result = class_exists('Reflection', false);
                        $msg = "Reflection class does not exists!";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'PCRE Extension',
                    'check' => function() {
                        $result = extension_loaded("pcre");
                        $msg = "Extension \"pcre\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'SPL extension',
                    'check' => function() {
                        $result = extension_loaded("SPL");
                        $msg = "Extension \"SPL\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'DOM extension',
                    'check' => function() {
                        $result = class_exists("DOMDocument", false);

                        $msg = "DomDocument class does not exists!";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'PDO extension',
                    'check' => function() {
                        $result = extension_loaded("pdo");
                        $msg = "Extension \"pdo\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'PDO MySQL extension',
                    'check' => function() {
                        $result = extension_loaded("pdo_mysql");
                        $msg = "Extension \"pdo_mysql\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'Mcrypt extension',
                    'check' => function() {
                        $result = extension_loaded("mcrypt");
                        $msg = "Extension \"mcrypt\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'CURL extension',
                    'check' => function() {
                        $result = extension_loaded("curl");
                        $msg = "Extension \"curl\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'GD extension with FreeType support<br />or ImageMagick extension with <br/> PNG support',
                    'check' => function() {
                        if (extension_loaded('imagick')) {
                            $imagick = new Imagick();
                            $imagickFormats = $imagick->queryFormats('PNG');
                        }
                        if (extension_loaded('gd'))
                            $gdInfo = gd_info();
                        if (isset($imagickFormats) && in_array('PNG', $imagickFormats))
                            return true;
                        elseif (isset($gdInfo)) {
                            if ($gdInfo['FreeType Support'])
                                return true;

                            return "GD Extension is loaded but no Freetype support";
                        }
                        return "GD Extension / ImageMagick is not loaded";
                    }
                ],
                [
                    'title' => 'Ctype extension',
                    'check' => function() {
                        $result = extension_loaded("ctype");
                        $msg = "Extension \"ctype\" is not loaded";
                        return $result !== true ? $msg : true;
                    }
                ],
                [
                    'title' => 'Checking Server variables',
                    'check' => function() {
                        return Installer::checkServerVar();
                    }
                ]
            ],
        ];

        if ($checkGroup == "") {
            return $checkLists;
        } else {
            return [$checkGroup => $checkLists[$checkGroup]];
        }
    }

    public static function checkInstall($checkGroup = "") {
        $checkList = Installer::getCheckList($checkGroup);
        $success = false;
        foreach ($checkList as $group => $groupItem) {
            foreach ($groupItem as $i => $c) {
                $check = $c['check']();
                if ($check !== true) {
                    Installer::setError($group, $i, $check);
                    $success = false;
                }
            }
        }

        return $success;
    }

    public static function createIndexFile($mode = "install") {
        $path = Setting::getApplicationPath() . DIRECTORY_SEPARATOR . "index.php";
        $file = file_get_contents($path);

        $file = str_replace([
            '$mode = "init"',
            '$mode = "install"',
            '$mode = "running"'
                ], '$mode = "' . $mode . '"', $file);


        Setting::$mode = $mode;

        if (!is_file($path)) {
            return @file_put_contents(Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php", $file);
        } else {
            $oldpath = Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php";
            $oldfile = @file_get_contents($oldpath);
            if ($oldfile != $file) {
                return @file_put_contents($oldpath, $file);
            } else {
                return true;
            }
        }
    }

    public static function init($config) {
        ## we hare to make sure the error page is shown
        ## so we need to strip yii unneeded config to make sure it is running

        $config['defaultController'] = "install";
        $config['components']['errorHandler'] = ['errorAction' => 'install/default/index'];

        Installer::checkInstall();

        if (Setting::$mode == "init") {
            $url = explode("/plansys", Setting::fullPath());
            if (is_file(Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php")) {
                header("Location: " . $url[0] . "/index.php");
                die();
            }

            if (!Installer::createIndexFile()) {
                Setting::redirError("Failed to write in \"{path}\" <br/> Permission denied", [
                    '{path}' => Setting::getRootPath() . DIRECTORY_SEPARATOR . "index.php"
                ]);
                return $config;
            } else {
                header("Location: " . $url[0] . "/index.php?r=install/default/index");
                die();
            }
        }


        return $config;
    }

    public static function resetDB() {
        $sql = <<<EOF
SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `p_audit_trail`;
CREATE TABLE `p_audit_trail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `url` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `pathinfo` text COLLATE utf8_unicode_ci,
  `module` text COLLATE utf8_unicode_ci,
  `ctrl` text COLLATE utf8_unicode_ci,
  `action` text COLLATE utf8_unicode_ci,
  `params` text COLLATE utf8_unicode_ci,
  `data` text COLLATE utf8_unicode_ci,
  `stamp` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  `key` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `form_class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_class` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `model_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `p_audit_trail_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `p_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `p_email_queue`;
CREATE TABLE `p_email_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `content` text,
  `body` text,
  `template` varchar(255) DEFAULT NULL,
  `status` int(1) DEFAULT '0' COMMENT '1,0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `p_email_queue_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `p_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;                
                
DROP TABLE IF EXISTS `p_nfy_messages`;
CREATE TABLE `p_nfy_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_id` varchar(255) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sender_id` int(11) DEFAULT NULL,
  `message_id` int(11) DEFAULT NULL,
  `subscription_id` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `timeout` int(11) DEFAULT NULL,
  `reserved_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `read_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `sent_on` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `mimetype` varchar(255) NOT NULL DEFAULT 'text/json',
  `body` text,
  `identifier` varbinary(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `{{nfy_messages}}_queue_id_idx` (`queue_id`),
  KEY `{{nfy_messages}}_sender_id_idx` (`sender_id`),
  KEY `{{nfy_messages}}_message_id_idx` (`message_id`),
  KEY `{{nfy_messages}}_status_idx` (`status`),
  KEY `{{nfy_messages}}_reserved_on_idx` (`reserved_on`),
  KEY `{{nfy_messages}}_subscription_id_idx` (`subscription_id`),
  CONSTRAINT `p_nfy_messages_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `p_nfy_subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `p_nfy_subscriptions`;
CREATE TABLE `p_nfy_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `queue_id` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `subscriber_id` int(11) NOT NULL,
  `created_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `{{nfy_subscriptions}}_queue_id_subscriber_id_idx` (`queue_id`,`subscriber_id`),
  KEY `{{nfy_subscriptions}}_queue_id_idx` (`queue_id`),
  KEY `{{nfy_subscriptions}}_subscriber_id_idx` (`subscriber_id`),
  KEY `{{nfy_subscriptions}}_is_deleted_idx` (`is_deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `p_nfy_subscription_categories`;
CREATE TABLE `p_nfy_subscription_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subscription_id` int(11) NOT NULL,
  `category` varchar(255) NOT NULL,
  `is_exception` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `{{nfy_subscription_categories}}_subscription_id_category_idx` (`subscription_id`,`category`),
  KEY `{{nfy_subscription_categories}}_subscription_id_idx` (`subscription_id`),
  CONSTRAINT `p_nfy_subscription_categories_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `p_nfy_subscriptions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



DROP TABLE IF EXISTS `p_role`;
CREATE TABLE `p_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `role_name` varchar(255) NOT NULL,
  `role_description` varchar(255) NOT NULL,
  `menu_path` varchar(255) DEFAULT NULL,
  `home_url` varchar(255) DEFAULT NULL,
  `repo_path` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `p_todo`;
CREATE TABLE `p_todo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(30) NOT NULL DEFAULT 'todo',
  `note` text NOT NULL,
  `options` text NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `p_todo_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `p_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `p_user`;
CREATE TABLE `p_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `nip` varchar(255) NOT NULL COMMENT 'NIP',
  `fullname` varchar(255) NOT NULL COMMENT 'Fullname',
  `email` varchar(255) NOT NULL COMMENT 'E-Mail',
  `phone` varchar(255) DEFAULT NULL COMMENT 'Phone',
  `username` varchar(255) NOT NULL COMMENT 'Username',
  `password` varchar(255) NOT NULL COMMENT 'Password',
  `last_login` datetime DEFAULT NULL,
  `is_deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `p_user_role`;
CREATE TABLE `p_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `user_id` int(11) DEFAULT NULL COMMENT 'User ID',
  `role_id` int(11) DEFAULT NULL,
  `is_default_role` enum('Yes','No') NOT NULL DEFAULT 'No',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `role_id` (`role_id`),
  CONSTRAINT `p_user_role_ibfk_5` FOREIGN KEY (`role_id`) REFERENCES `p_role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `p_user_role_ibfk_7` FOREIGN KEY (`user_id`) REFERENCES `p_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `p_role` (`id`, `role_name`, `role_description`, `menu_path`, `home_url`, `repo_path`) VALUES
(1,	'dev',	'IT - Developer',	'',	'',	'');

INSERT INTO `p_user_role` (`id`, `user_id`, `role_id`, `is_default_role`) VALUES
(1,	1,	1,	'Yes');

INSERT INTO `p_user` (`id`, `nip`, `fullname`, `email`, `phone`, `username`, `password`, `last_login`, `is_deleted`) VALUES
(1,	'-',	'Developer',	'-',	'-',	'dev',	md5('dev'),	now(),	0);

                
EOF;
        Yii::import('application.components.model.*');
        ActiveRecord::execute($sql);
    }

}
