CREATE TABLE `auth_user_md5` (
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `username` varchar(64) NOT NULL DEFAULT '',
  `password` varbinary(64) NOT NULL DEFAULT '',
  `perms` enum('user','autor','tutor','dozent','admin','root') NOT NULL DEFAULT 'user',
  `Vorname` varchar(64) DEFAULT NULL,
  `Nachname` varchar(64) DEFAULT NULL,
  `Email` varchar(64) DEFAULT NULL,
  `validation_key` varchar(10) NOT NULL DEFAULT '',
  `auth_plugin` varchar(64) DEFAULT 'standard',
  `locked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lock_comment` varchar(255) DEFAULT NULL,
  `locked_by` varchar(32) DEFAULT NULL,
  `visible` enum('global','always','yes','unknown','no','never') NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `k_username` (`username`),
  KEY `perms` (`perms`)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

CREATE TABLE `contact` (
  `owner_id` varchar(32) NOT NULL DEFAULT '',
  `user_id` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`owner_id`,`user_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

CREATE TABLE `plugins` (
  `pluginid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pluginclassname` varchar(255) NOT NULL DEFAULT '',
  `pluginpath` varchar(255) NOT NULL DEFAULT '',
  `pluginname` varchar(45) NOT NULL DEFAULT '',
  `plugintype` text NOT NULL,
  `enabled` enum('yes','no') NOT NULL DEFAULT 'no',
  `navigationpos` int(10) unsigned NOT NULL DEFAULT '0',
  `dependentonid` int(10) unsigned DEFAULT NULL,
  `automatic_update_url` varchar(256) DEFAULT NULL,
  `automatic_update_secret` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`pluginid`)
) ENGINE=InnoDB AUTO_INCREMENT=8 ROW_FORMAT=DYNAMIC;

CREATE TABLE `user_info` (
  `user_id` varchar(32) NOT NULL DEFAULT '',
  `hobby` varchar(255) NOT NULL DEFAULT '',
  `lebenslauf` mediumtext NOT NULL,
  `publi` mediumtext NOT NULL,
  `schwerp` text NOT NULL,
  `Home` varchar(200) NOT NULL DEFAULT '',
  `privatnr` varchar(32) NOT NULL DEFAULT '',
  `privatcell` varchar(32) NOT NULL DEFAULT '',
  `privadr` varchar(64) NOT NULL DEFAULT '',
  `score` int(11) unsigned NOT NULL DEFAULT '0',
  `geschlecht` tinyint(4) NOT NULL DEFAULT '0',
  `mkdate` int(20) NOT NULL DEFAULT '0',
  `chdate` int(20) NOT NULL DEFAULT '0',
  `title_front` varchar(64) NOT NULL DEFAULT '',
  `title_rear` varchar(64) NOT NULL DEFAULT '',
  `preferred_language` varchar(20) DEFAULT NULL,
  `smsforward_copy` tinyint(1) NOT NULL DEFAULT '1',
  `smsforward_rec` varchar(32) NOT NULL DEFAULT '',
  `email_forward` tinyint(4) NOT NULL DEFAULT '0',
  `smiley_favorite` varchar(255) NOT NULL DEFAULT '',
  `motto` varchar(255) NOT NULL DEFAULT '',
  `lock_rule` varchar(32) NOT NULL DEFAULT '',
  PRIMARY KEY (`user_id`),
  KEY `score` (`score`)
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;

INSERT INTO `auth_user_md5` (`user_id`, `username`, `password`, `perms`, `Vorname`, `Nachname`, `Email`, `validation_key`, `auth_plugin`, `locked`, `lock_comment`, `locked_by`, `visible`) VALUES('76ed43ef286fb55cf9e41beadb484a9f', 'root@studip', '$2a$08$E2oDjsPcmCrnJT3GiSvHGugLhyUc1mlpcB3FstDzgMwgnIdzi8iSW', 'root', 'Root', 'Studip', 'root@localhost', '', 'standard', 0, NULL, NULL, 'unknown');
INSERT INTO `auth_user_md5` (`user_id`, `username`, `password`, `perms`, `Vorname`, `Nachname`, `Email`, `validation_key`, `auth_plugin`, `locked`, `lock_comment`, `locked_by`, `visible`) VALUES('205f3efb7997a0fc9755da2b535038da', 'test_dozent', '$2a$08$ajIvgEjd17MiiDcFr6msc.xldknH/tTGajUXVhDxDKNJVX0H0iv0i', 'dozent', 'Testaccount', 'Dozent', 'dozent@studip.de', '', 'standard', 0, NULL, NULL, 'unknown');
INSERT INTO `auth_user_md5` (`user_id`, `username`, `password`, `perms`, `Vorname`, `Nachname`, `Email`, `validation_key`, `auth_plugin`, `locked`, `lock_comment`, `locked_by`, `visible`) VALUES('6235c46eb9e962866ebdceece739ace5', 'test_admin', '$2a$08$svvSma20vIxIR4J5gc0jIu31gws1WibmiQ/HDhCTukFA5GqhscY1G', 'admin', 'Testaccount', 'Admin', 'admin@studip.de', '', 'standard', 0, NULL, NULL, 'unknown');
INSERT INTO `auth_user_md5` (`user_id`, `username`, `password`, `perms`, `Vorname`, `Nachname`, `Email`, `validation_key`, `auth_plugin`, `locked`, `lock_comment`, `locked_by`, `visible`) VALUES('7e81ec247c151c02ffd479511e24cc03', 'test_tutor', '$2a$08$mGhBl85TPsiItumZ4xjbgOnQ1vqIhLAC9giCfWcFzpkE1jqe4lmby', 'tutor', 'Testaccount', 'Tutor', 'tutor@studip.de', '', 'standard', 0, NULL, NULL, 'unknown');
INSERT INTO `auth_user_md5` (`user_id`, `username`, `password`, `perms`, `Vorname`, `Nachname`, `Email`, `validation_key`, `auth_plugin`, `locked`, `lock_comment`, `locked_by`, `visible`) VALUES('e7a0a84b161f3e8c09b4a0a2e8a58147', 'test_autor', '$2a$08$xvbrvPhkcsvkzPZsNh.kceLw2IIwiNJ.1jGOwY3.H/dR2f8PG5X3O', 'autor', 'Testaccount', 'Autor', 'autor@studip.de', '', 'standard', 0, NULL, NULL, 'unknown');

INSERT INTO contact (owner_id, user_id) VALUES
       ('76ed43ef286fb55cf9e41beadb484a9f', '205f3efb7997a0fc9755da2b535038da'),
       ('76ed43ef286fb55cf9e41beadb484a9f', '6235c46eb9e962866ebdceece739ace5'),
       ('76ed43ef286fb55cf9e41beadb484a9f', '7e81ec247c151c02ffd479511e24cc03'),
       ('76ed43ef286fb55cf9e41beadb484a9f', 'e7a0a84b161f3e8c09b4a0a2e8a58147');

INSERT INTO `plugins` (`pluginid`, `pluginclassname`, `pluginpath`, `pluginname`, `plugintype`, `enabled`, `navigationpos`, `dependentonid`, `automatic_update_url`, `automatic_update_secret`) VALUES
(17, 'ArgonautsPlugin', 'luniki/ArgonautsPlugin', 'Argonauts', 'SystemPlugin', 'yes', 1, NULL, NULL, NULL);

INSERT INTO user_info SET user_id ='76ed43ef286fb55cf9e41beadb484a9f';
INSERT INTO user_info SET user_id ='205f3efb7997a0fc9755da2b535038da';
INSERT INTO user_info SET user_id ='6235c46eb9e962866ebdceece739ace5';
INSERT INTO user_info SET user_id ='7e81ec247c151c02ffd479511e24cc03';
INSERT INTO user_info SET user_id ='e7a0a84b161f3e8c09b4a0a2e8a58147';
