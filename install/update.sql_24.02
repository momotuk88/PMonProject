ALTER TABLE `switch_log` CHANGE `types` `types` ENUM('cron','system','user','switch','deletonu','addonu') CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 'system';
CREATE TABLE `pingstats` (  `id` int(11) UNSIGNED NOT NULL,  `datetime` datetime DEFAULT NULL,  `time` text DEFAULT NULL,  `system` text DEFAULT NULL,  `status` int(11) DEFAULT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `pingstats` CHANGE `system` `system` INT(11) NULL DEFAULT NULL;
ALTER TABLE `pingstats`  ADD PRIMARY KEY (`id`);
ALTER TABLE `pingstats`  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
CREATE TABLE `monitoring` (  `id` int(11) UNSIGNED NOT NULL,  `datetime` datetime DEFAULT NULL, `types` text DEFAULT NULL, `values` text DEFAULT NULL, `deviceid` text DEFAULT NULL) ENGINE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `monitoring` CHANGE `deviceid` `deviceid` INT(11) NULL DEFAULT NULL;
ALTER TABLE `monitoring`  ADD PRIMARY KEY (`id`);
ALTER TABLE `monitoring`  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `log` CHANGE `progress` `progress` ENUM('user','system','switch','telegram','config','onudelet') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `log` CHANGE `userid` `userid` INT(11) NULL DEFAULT NULL;
ALTER TABLE `switch` ADD `ping` ENUM('up','down') NOT NULL DEFAULT 'up' AFTER `id`;
ALTER TABLE `switch` ADD `timeping` DATETIME NULL DEFAULT NULL AFTER `groups`;
INSERT INTO `config` (`name`,`value`) VALUES ('logsignal','on');




















