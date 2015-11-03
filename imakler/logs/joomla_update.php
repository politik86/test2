#
#<?php die('Forbidden.'); ?>
#Date: 2013-11-18 15:27:26 UTC
#Software: Joomla Platform 13.1.0 Stable [ Curiosity ] 24-Apr-2013 00:00 GMT

#Fields: datetime	priority	category	message
2013-11-18T15:27:26+00:00	INFO	update	Finalising installation.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: /* Core 3.2 schema updates */  ALTER TABLE `#__content_types` ADD COLUMN `conten.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__content_types` SET `content_history_options` = '{"formFile":"administ.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__content_types` SET `content_history_options` = '{"formFile":"administ.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__content_types` SET `content_history_options` = '{"formFile":"administ.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__content_types` SET `content_history_options` = '{"formFile":"administ.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__content_types` SET `content_history_options` = '{"formFile":"administ.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__content_types` SET `content_history_options` = '{"formFile":"administ.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `rules`, `f.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__extensions` SET `params` = '{"template_positions_display":"0","upload.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: UPDATE `#__extensions` SET `params` = '{"lineNumbers":"1","lineWrapping":"1","ma.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `ty.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: ALTER TABLE `#__modules` ADD COLUMN `asset_id` INT(10) UNSIGNED NOT NULL DEFAULT.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: CREATE TABLE `#__postinstall_messages` (   `postinstall_message_id` bigint(20) u.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: INSERT INTO `#__postinstall_messages` (`extension_id`, `title_key`, `description.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: CREATE TABLE IF NOT EXISTS `#__ucm_history` (   `version_id` int(10) unsigned NO.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: ALTER TABLE `#__users` ADD COLUMN `otpKey` varchar(1000) NOT NULL DEFAULT '' COM.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: ALTER TABLE `#__users` ADD COLUMN `otep` varchar(1000) NOT NULL DEFAULT '' COMME.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: CREATE TABLE IF NOT EXISTS `#__user_keys` (   `id` int(10) unsigned NOT NULL AUT.
2013-11-18T15:27:27+00:00	INFO	update	Ran query from file 3.2.0. Query text: /* Update bad params for two cpanel modules */  UPDATE `#__modules` SET `params`.
2013-11-18T15:27:27+00:00	INFO	update	Deleting removed files and folders.
2013-11-18T15:27:28+00:00	INFO	update	Cleaning up after installation.
2013-11-18T15:27:28+00:00	INFO	update	Update to version 3.2.0 is complete.
2014-02-12T10:48:03+00:00	INFO	update	Update started by user Super User (152). Old version is 3.2.0.
2014-02-12T10:48:03+00:00	INFO	update	Downloading update file from .
2014-02-12T10:48:06+00:00	INFO	update	File Joomla_3.2.x_to_3.2.2-Stable-Patch_Package.zip successfully downloaded.
2014-02-12T10:48:06+00:00	INFO	update	Starting installation of new version.
2014-02-12T10:48:14+00:00	INFO	update	Finalising installation.
2014-02-12T10:48:14+00:00	INFO	update	Ran query from file 3.2.1. Query text: DELETE FROM `#__postinstall_messages` WHERE `title_key` = 'PLG_USER_JOOMLA_POSTI.
2014-02-12T10:48:14+00:00	INFO	update	Ran query from file 3.2.2-2013-12-22. Query text: ALTER TABLE `#__update_sites` ADD COLUMN `extra_query` VARCHAR(1000) DEFAULT '';.
2014-02-12T10:48:15+00:00	INFO	update	Ran query from file 3.2.2-2013-12-22. Query text: ALTER TABLE `#__updates` ADD COLUMN `extra_query` VARCHAR(1000) DEFAULT '';.
2014-02-12T10:48:15+00:00	INFO	update	Ran query from file 3.2.2-2013-12-28. Query text: UPDATE `#__menu` SET `component_id` = (SELECT `extension_id` FROM `#__extensions.
2014-02-12T10:48:15+00:00	INFO	update	Ran query from file 3.2.2-2014-01-08. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2014-02-12T10:48:15+00:00	INFO	update	Ran query from file 3.2.2-2014-01-15. Query text: INSERT INTO `#__postinstall_messages` (`extension_id`, `title_key`, `description.
2014-02-12T10:48:15+00:00	INFO	update	Ran query from file 3.2.2-2014-01-18. Query text: /* Update updates version length */ ALTER TABLE `#__updates` MODIFY `version` va.
2014-02-12T10:48:15+00:00	INFO	update	Ran query from file 3.2.2-2014-01-23. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2014-02-12T10:48:15+00:00	INFO	update	Deleting removed files and folders.
2014-02-12T10:48:17+00:00	INFO	update	Cleaning up after installation.
2014-02-12T10:48:17+00:00	INFO	update	Update to version 3.2.2 is complete.
2014-03-07T14:35:24+00:00	INFO	update	Update started by user Super User (385). Old version is 3.2.2.
2014-03-07T14:35:25+00:00	INFO	update	Downloading update file from .
2014-03-07T14:35:25+00:00	INFO	update	File Joomla_3.2.2_to_3.2.3-Stable-Patch_Package.zip successfully downloaded.
2014-03-07T14:35:26+00:00	INFO	update	Starting installation of new version.
2014-03-07T14:35:45+00:00	INFO	update	Update started by user Super User (385). Old version is 3.2.2.
2014-03-07T14:35:45+00:00	INFO	update	File Joomla_3.2.2_to_3.2.3-Stable-Patch_Package.zip successfully downloaded.
2014-03-07T14:35:46+00:00	INFO	update	Starting installation of new version.
2014-03-11T09:57:15+00:00	INFO	update	COM_JOOMLAUPDATE_UPDATE_LOG_DELETE_FILES
2014-05-27T08:01:26+00:00	INFO	update	Update started by user Super User (671). Old version is 3.2.3.
2014-05-27T08:01:26+00:00	INFO	update	Downloading update file from .
2014-05-27T08:02:03+00:00	INFO	update	File Joomla_3.3.0-Stable-Update_Package.zip successfully downloaded.
2014-05-27T08:02:03+00:00	INFO	update	Starting installation of new version.
2014-05-27T08:02:10+00:00	INFO	update	Finalising installation.
2014-05-27T08:02:21+00:00	INFO	update	Ran query from file 3.3.0-2014-02-16. Query text: ALTER TABLE `#__users` ADD COLUMN `requireReset` tinyint(4) NOT NULL DEFAULT 0 C.
2014-05-27T08:02:21+00:00	INFO	update	Ran query from file 3.3.0-2014-04-02. Query text: INSERT INTO `#__extensions` (`extension_id`, `name`, `type`, `element`, `folder`.
2014-05-27T08:02:21+00:00	INFO	update	Deleting removed files and folders.
2014-05-27T08:02:23+00:00	INFO	update	Cleaning up after installation.
2014-05-27T08:02:23+00:00	INFO	update	Update to version 3.3.0 is complete.
2014-06-12T10:50:39+00:00	INFO	update	Update started by user Super User (671). Old version is 3.3.0.
2014-06-12T10:50:39+00:00	INFO	update	Downloading update file from .
2014-06-12T10:50:41+00:00	INFO	update	File Joomla_3.3.x_to_3.3.1-Stable-Patch_Package.zip successfully downloaded.
2014-06-12T10:50:41+00:00	INFO	update	Starting installation of new version.
2014-06-12T10:50:46+00:00	INFO	update	Finalising installation.
2014-06-12T10:50:47+00:00	INFO	update	Deleting removed files and folders.
2014-06-12T10:50:49+00:00	INFO	update	Cleaning up after installation.
2014-06-12T10:50:49+00:00	INFO	update	Update to version 3.3.1 is complete.
