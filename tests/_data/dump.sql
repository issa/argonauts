/* Replace this file with actual dump of your database */
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
) ENGINE=InnoDB ROW_FORMAT=DYNAMIC;


INSERT INTO `plugins` (`pluginid`, `pluginclassname`, `pluginpath`, `pluginname`, `plugintype`, `enabled`, `navigationpos`, `dependentonid`, `automatic_update_url`, `automatic_update_secret`) VALUES
(17, 'ArgonautsPlugin', 'luniki/ArgonautsPlugin', 'Argonauts', 'SystemPlugin', 'yes', 1, NULL, NULL, NULL);
