<?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

/**
 * Script file of AltaLeda component
 */
class com_adagencyInstallerScript{

	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install(){ 
		$db = JFactory::getDBO();
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_advertis` (
				  `aid` int(11) NOT NULL auto_increment,
				  `user_id` int(11) NOT NULL default '0',
				  `company` varchar(100) NOT NULL default '',
				  `description` varchar(255) default NULL,
				  `website` varchar(255) default NULL,
				  `address` varchar(100) default NULL,
				  `country` varchar(100) default NULL,
				  `city` varchar(100) default NULL,
				  `state` varchar(100) default NULL,
				  `zip` varchar(12) default NULL,
				  `telephone` varchar(20) default NULL,
				  `fax` varchar(20) default NULL,
				  `logo` varchar(255) default NULL,
				  `email_daily_report` enum('Y','N') default 'N',
				  `email_weekly_report` enum('Y','N') default 'N',
				  `email_month_report` enum('Y','N') default 'N',
				  `email_campaign_expiration` enum('Y','N') default 'N',
				  `approved` enum('Y','N','P') character set latin1 default 'P',
				  `lastreport` bigint(20) default NULL,
				  `weekreport` bigint(20) default NULL,
				  `monthreport` bigint(20) default NULL,
				  `paywith` text NOT NULL,
				  `apr_ads` enum('G','Y','N') default 'G',
				  `apr_cmp` enum('G','Y','N') default 'G',
				  `key` varchar(100) default NULL,
				  `ordering` int(10) NOT NULL default '0',
				  `checked_out` int(10) NOT NULL default '0',
				  PRIMARY KEY  (`aid`),
				  KEY `user_id` (`user_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_banners` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `advertiser_id` int(11) NOT NULL DEFAULT '0',
			  `title` varchar(255) NOT NULL DEFAULT '',
			  `description` varchar(255) DEFAULT NULL,
			  `media_type` enum('Standard','Advanced','Popup','Flash','TextLink','Transition','Floating','Jomsocial') DEFAULT 'Standard',
			  `image_url` varchar(255) DEFAULT NULL,
			  `swf_url` varchar(255) DEFAULT NULL,
			  `target_url` varchar(255) DEFAULT NULL,
			  `width` smallint(5) unsigned DEFAULT NULL,
			  `height` smallint(5) unsigned DEFAULT NULL,
			  `ad_code` mediumtext,
			  `use_ad_code_in_netscape` enum('N','Y') DEFAULT 'N',
			  `ad_code_netscape` mediumtext,
			  `parameters` mediumtext,
			  `approved` enum('Y','N','P') CHARACTER SET latin1 DEFAULT 'P',
			  `zone` int(11) NOT NULL DEFAULT '0',
			  `frequency` int(11) DEFAULT NULL,
			  `created` date DEFAULT NULL,
			  `ordering` int(8) NOT NULL DEFAULT '0',
			  `keywords` varchar(255) DEFAULT NULL,
			  `key` varchar(100) DEFAULT NULL,
			  `channel_id` int(11) DEFAULT NULL,
			  `ad_start_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `ad_end_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
			  `checked_out` int(10) NOT NULL DEFAULT '0',
			  `image_content` varchar(255) NOT NULL,
			  `ad_headline` varchar(255) NOT NULL,
			  `ad_text` text NOT NULL,
			  `access` int(3) NOT NULL,
			  PRIMARY KEY  (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_campaign` (
				  `id` int(11) NOT NULL auto_increment,
				  `aid` int(11) NOT NULL default '0',
				  `name` varchar(255) NOT NULL default '',
				  `notes` text,
				  `default` enum('Y','N') NOT NULL default 'N',
				  `start_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `type` enum('cpm','pc','fr','in') NOT NULL default 'cpm',
				  `quantity` int(11) default '0',
				  `validity` datetime NOT NULL default '0000-00-00 00:00:00',
				  `cost` decimal(5,2) default '0.00',
				  `otid` int(11) default '0',
				  `approved` enum('Y','N','P') character set latin1 default 'P',
				  `status` tinyint(1) NOT NULL default '1',
				  `exp_notice` int(11) NOT NULL default '0',
				  `key` varchar(100) default NULL,
				  `params` varchar(255) default NULL,
				  `renewcmp` int(3) NOT NULL DEFAULT '0',
				  `activities` text NOT NULL,
				  `ordering` int(10) NOT NULL default '0',
				  `checked_out` int(10) NOT NULL default '0',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_campaign_banner` (
				  `campaign_id` int(11) NOT NULL default '0',
				  `banner_id` int(11) NOT NULL default '0',
				  `relative_weighting` int(11) NOT NULL default '100',
				  `thumb` varchar(255) default NULL,
				  `zone` int(11) default NULL,
				  PRIMARY KEY  (`campaign_id`,`banner_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_channels` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(100) NOT NULL,
				  `banner_id` int(11) default '0',
				  `advertiser_id` int(11) default '0',
				  `public` enum('Y','N') NOT NULL default 'N',
				  `created` datetime default NULL,
				  `created_by` int(11) default NULL,
				  `from` enum('F','B') NOT NULL default 'B',
				  `ordering` int(10) NOT NULL default '0',
				  `checked_out` int(10) NOT NULL default '0',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_channel_set` (
				  `id` int(11) NOT NULL auto_increment,
				  `channel_id` int(11) NOT NULL,
				  `type` varchar(25) NOT NULL,
				  `logical` varchar(25) NOT NULL,
				  `option` varchar(25) NOT NULL,
				  `data` text NOT NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_currencies` (
				  `id` int(11) NOT NULL auto_increment,
				  `plugname` varchar(30) NOT NULL default '',
				  `currency_name` varchar(20) NOT NULL default '',
				  `currency_full` varchar(50) NOT NULL default '',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_order` (
				  `oid` int(11) NOT NULL auto_increment,
				  `tid` int(11) NOT NULL default '0',
				  `aid` int(11) NOT NULL default '0',
				  `type` enum('cpm','pc','fr', 'in') NOT NULL default 'cpm',
				  `quantity` int(11) NOT NULL default '0',
				  `cost` decimal(10,2) NOT NULL default '0.00',
				  `order_date` date NOT NULL default '0000-00-00',
				  `payment_type` varchar(20) NOT NULL,
				  `card_number` varchar(20) default NULL,
				  `expiration` varchar(4) default NULL,
				  `card_name` varchar(255) default NULL,
				  `notes` varchar(255) NOT NULL default '',
				  `status` enum('not_paid','paid','rejected') default 'not_paid',
				  `pack_id` enum('0','1') NOT NULL default '0',
				  `currency` varchar(50) NOT NULL,
				  `promocodeid` int(3) NOT NULL,
				  `ordering` int(10) NOT NULL default '0',
				  `checked_out` int(10) NOT NULL default '0',
				  PRIMARY KEY  (`oid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_order_type` (
				  `tid` int(11) NOT NULL auto_increment,
				  `description` varchar(255) NOT NULL default '',
				  `pack_description` text NOT NULL,
				  `quantity` int(11) NOT NULL default '0',
				  `type` enum('cpm','pc','fr', 'in') NOT NULL default 'cpm',
				  `cost` decimal(10,2) NOT NULL default '0.00',
				  `validity` mediumtext,
				  `sid` int(11) NOT NULL default '0',
				  `published` tinyint(1) NOT NULL default '1',
				  `visibility` tinyint(4) NOT NULL default '1',
				  `zones` text NOT NULL,
				  `zones_wildcard` text,
				  `ordering` int(11) NOT NULL default '0',
				  `hide_after` tinyint(2) NOT NULL default '0',
				  `location` int(11) NOT NULL,
				  `checked_out` int(10) NOT NULL default '0',
				  PRIMARY KEY  (`tid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_package_zone` (
				  `package_id` int(11) NOT NULL,
				  `zone_id` int(11) NOT NULL,
				  PRIMARY KEY  (`package_id`,`zone_id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_plugins` (
				  `id` int(11) NOT NULL auto_increment,
				  `name` varchar(40) NOT NULL default '',
				  `classname` varchar(40) NOT NULL default '',
				  `value` text NOT NULL,
				  `filename` varchar(40) NOT NULL default '',
				  `type` varchar(10) NOT NULL default 'payment',
				  `published` int(11) NOT NULL default '0',
				  `def` varchar(30) NOT NULL default '',
				  `sandbox` int(11) NOT NULL default '0',
				  `reqhttps` int(11) NOT NULL default '0',
				  `display_name` varchar(20) default NULL,
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_plugin_settings` (
				  `pluginid` int(11) NOT NULL default '0',
				  `setting` varchar(200) NOT NULL default '',
				  `description` text NOT NULL,
				  `value` text NOT NULL,
				  KEY `pluginid` (`pluginid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_promocodes` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `title` varchar(200) NOT NULL DEFAULT '',
				  `code` varchar(100) NOT NULL DEFAULT '',
				  `codelimit` int(11) NOT NULL DEFAULT '0',
				  `amount` float NOT NULL DEFAULT '0',
				  `codestart` int(11) NOT NULL DEFAULT '0',
				  `codeend` int(11) NOT NULL DEFAULT '0',
				  `forexisting` int(11) NOT NULL DEFAULT '0',
				  `published` int(11) NOT NULL DEFAULT '0',
				  `aftertax` int(11) NOT NULL DEFAULT '0',
				  `promotype` int(11) NOT NULL DEFAULT '0',
				  `used` int(11) NOT NULL DEFAULT '0',
				  `ordering` int(11) NOT NULL DEFAULT '0',
				  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
				  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `code` (`code`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_settings` (
				  `id` int(11) NOT NULL default '1',
				  `lastsend` bigint(20) NOT NULL default '0',
				  `adminemail` text,
				  `fromemail` text,
				  `fromname` text,
				  `imgfolder` text,
				  `maxchars` int(11) default NULL,
				  `captcha` tinyint(4) NOT NULL default '0',
				  `allowstand` varchar(2) default NULL,
				  `allowadcode` varchar(2) default NULL,
				  `allowpopup` varchar(2) default NULL,
				  `allowswf` varchar(2) default NULL,
				  `allowtxtlink` varchar(2) default NULL,
				  `allowtrans` varchar(2) default NULL,
				  `allowfloat` varchar(2) default NULL,
				  `txtafterreg` text,
				  `bodyafterreg` text,
				  `sbafterreg` text,
				  `bodyactivation` text,
				  `sbactivation` text,
				  `bodyrep` text,
				  `sbrep` text,
				  `bodycmpappv` text,
				  `sbcmpappv` text,
				  `bodycmpdis` text,
				  `sbcmpdis` text,
				  `bodyadappv` text,
				  `sbadappv` text,
				  `bodyaddisap` text,
				  `sbaddisap` text,
				  `bodyadvdis` text,
				  `sbadvdis` text,
				  `bodynewad` text,
				  `sbnewad` text,
				  `bodynewcmp` text,
				  `sbnewcmp` text,
				  `bodycmpex` text,
				  `sbcmpex` text,
				  `bodynewuser` text,
				  `sbnewuser` text,
				  `currencydef` text,
				  `indextbl` int(2) NOT NULL default '0',
				  `askterms` enum('0','1') NOT NULL default '0',
				  `termsid` int(11) NOT NULL,
				  `overviewcontent` text NOT NULL,
				  `showpreview` tinyint(2) NOT NULL default '1',
				  `show` varchar(255) NOT NULL default 'captcha;refresh;',
				  `mandatory` varchar(50) default NULL,
				  `params` varchar(255) default NULL,
				  `sbafterregaa` text,
				  `bodyafterregaa` text,
				  `countryloc` varchar(150) NOT NULL default 'geoip/countries',
				  `cityloc` varchar(150) NOT NULL default 'geoip/GeoLiteCity.dat',
				  `codeloc` varchar(150) NOT NULL default 'geoip/code',
				  `payment` varchar(50) NOT NULL,
				  `geoparams` text NOT NULL,
				  `limit_ip` int(10) NOT NULL default '100',
				  `sbcmpexpadm` text NOT NULL,
				  `bodycmpexpadm` text NOT NULL,
				  `version` varchar(100) default '2.0.10',
				  `forcehttps` int(3) NOT NULL default '0',
				  `jomfields` varchar(255) DEFAULT NULL,
				  `allow_add_keywords` int(3) NOT NULL DEFAULT '0',
				  `imagetools` int(3) NOT NULL DEFAULT '1',
				  `sbadchanged` text NOT NULL,
				  `boadchanged` text NOT NULL,
				  `last_check_date` datetime NOT NULL,
				  `showpromocode` int(3) NOT NULL DEFAULT '0',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_stat` (
				  `entry_date` datetime NOT NULL default '0000-00-00 00:00:00',
				  `ip_address` bigint(15) NOT NULL default '0',
				  `advertiser_id` int(11) NOT NULL default '0',
				  `campaign_id` int(11) NOT NULL default '0',
				  `banner_id` int(11) NOT NULL default '0',
				  `type` enum('click','impressions') NOT NULL default 'impressions',
				  `how_many` int(9) NOT NULL default '0',
				  KEY `idxip_address` (`ip_address`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_states` (
				  `id` int(20) NOT NULL auto_increment,
				  `state` varchar(30) NOT NULL default '',
				  `country` varchar(30) NOT NULL default '',
				  PRIMARY KEY  (`id`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_zone` (
				  `zoneid` int(11) NOT NULL default '0',
				  `banners` tinyint(2) unsigned NOT NULL default '0',
				  `banners_cols` tinyint(2) NOT NULL default '1',
				  `z_title` text NOT NULL,
				  `z_ordering` int(11) NOT NULL default '0',
				  `z_position` varchar(50) default NULL,
				  `show_title` tinyint(3) unsigned NOT NULL default '1',
				  `suffix` text NOT NULL,
				  `rotatebanners` enum('0','1') NOT NULL default '0',
				  `rotating_time` int(11) NOT NULL default '10000',
				  `rotaterandomize` enum('0','1') NOT NULL default '1',
				  `show_adv_link` tinyint(1) NOT NULL default '1',
				  `cellpadding` int(11) NOT NULL default '0',
				  `link_taketo` tinyint(1) NOT NULL default '0',
				  `taketo_url` varchar(255) default NULL,
				  `itemid` int(10) default '0',
				  `defaultad` int(8) default NULL,
				  `keywords` tinyint(2) NOT NULL default '0',
				  `adparams` varchar(255) NOT NULL,
				  `ignorestyle` enum('0','1') NOT NULL default '0',
				  `textadparams` varchar(255) NOT NULL,
				  `zone_text_below` text NOT NULL,
				  `zone_content_location` int(3) NOT NULL DEFAULT '0',
				  `zone_content_visibility` int(3) NOT NULL DEFAULT '0',
				  `ordering` int(10) NOT NULL default '0',
				  `checked_out` int(10) NOT NULL default '0',
				  `inventory_zone` int(3) NOT NULL default '0',
				  PRIMARY KEY  (`zoneid`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_statistics` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `entry_date` date NOT NULL DEFAULT '0000-00-00',
				  `impressions` longtext,
				  `click` longtext,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_ips` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `entry_date` date NOT NULL DEFAULT '0000-00-00',
				  `ips_impressions` longtext,
				  `ips_clicks` longtext NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($sql);
		$db->query();
		
		// 27.04.2012 modification made by Alin
		//$imagepath = str_replace("/administrator", "", JPATH_BASE);
		//$imagepath = $imagepath . "/images/stories";
		$imagepath = JPATH_SITE."/images/stories";

		// check if the stories folder exists, if not create it
		$full_path2 = $imagepath;
		
		if (!JFolder::exists($full_path2)) {
			JFolder::create($full_path2);
		}
		if (JPath::canChmod($full_path2)) {
			JPath::setPermissions($full_path2);
		}
		
		// 27.04.2012 modification made by Alin
		//$imagepath = str_replace("/administrator", "", JPATH_BASE);
		//$imagepath = $imagepath . "/images/stories/";
		$imagepath = JPATH_SITE."/images/stories";
	   
	   $newimgfolder = 'ad_agency';
		// 27.04.2012 modification made by Alin
		//$full_path = JFolder::makeSafe($imagepath . $newimgfolder);
		//$full_path = JFolder::makeSafe($imagepath."/".$newimgfolder);
		$full_path = $imagepath."/".$newimgfolder;
		
		if (!JFolder::exists($full_path)) {
			JFolder::create($full_path);
			// mkdir ( $imagepath . $newimgfolder );
		}
		if (JPath::canChmod($full_path)) {
			JPath::setPermissions($full_path);
		}

		$this->installAlertUploadPlugins();
		$this->addNewColumns();

		$database = JFactory::getDBO();
		$db = JFactory::getDBO();

		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_jomsocial` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `ad_id` int(10) NOT NULL,
			  `field_id` int(11) NOT NULL,
			  `value` text NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "SHOW columns FROM `#__ad_agency_settings`";
		$database->setQuery($sql);		
		$res_confs = $database->loadColumn();
		
		if(!in_array("blacklist",$res_confs)) { 
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `blacklist` longtext NULL ";
			$db->setQuery($sql);
			$db->query();
		}
		
		if(!in_array("jomfields",$res_confs)) { 
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `jomfields` VARCHAR( 255 ) NULL ";
			$db->setQuery($sql);
			$db->query();
		}
		
		if(!in_array("forcehttps",$res_confs)) { 
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `forcehttps` int(3) NOT NULL default '0' ";
			$db->setQuery($sql);
			$db->query();
		}
		
		$sql = "SHOW columns FROM `#__ad_agency_campaign`";
		$database->setQuery($sql);
		$res_confs = $database->loadColumn();

		if(!in_array("renewcmp", $res_confs)){
			$sql = "ALTER TABLE `#__ad_agency_campaign` ADD `renewcmp` int(3) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		
		
		$sql = "ALTER TABLE `#__ad_agency_settings` CHANGE `params` `params` TEXT CHARACTER 
				   SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ";
		$db->setQuery($sql);
		$db->query();
		
		//see if we have any settings in the settings table and add the default email settings
		$sql = "SELECT `txtafterreg` FROM `#__ad_agency_settings` LIMIT 1";
		$sqlz[] = $sql;
		$database->setQuery($sql);
			$textafterreg = $database->loadColumn();
		
		$txtafterreg='<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
						<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
						<div id="adg-mail-cont">
						<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
						<div>
						<div class="adg-mail-title-wrap">
						<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span>Thank you {name}!</h2>
						</div>
						<div class="adg-mail-text-wrap">
						<p class="adg-mail-text">Thank you for registering to our Ad Agency. Your account is pending approval. We will notify you by email when your account is approved. Please save your login information for future reference: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"> <span style="display: block;"><strong class="adg-block">Username</strong> - {login} </span> <strong class="adg-block">Password</strong> - {password}</span></p>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>';
		
		$bdyafterreg='<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span>Hello {name}!</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">We have approved your application! Now you can login to our site with the following information and manage your account, add banners, create campaigns,etc. <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"> <span style="display: block;"><strong class="adg-block">Username</strong> - {login} </span></span></p>
					<p class="adg-mail-text">Ad Agency Manager</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$afterregaap ='<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
						<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
						<div id="adg-mail-cont">
						<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
						<div>
						<div class="adg-mail-title-wrap">
						<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
						</div>
						<div class="adg-mail-text-wrap">
						<p class="adg-mail-text">Thank you for registering to our Ad Agency. Your account is now approved.</p>
						<p class="adg-mail-text">Your login information is listed below. <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Username</strong> - {login} </span> <strong class="adg-block">Password</strong> - {password}</span></p>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>';
		$afterregneedsapp ='<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
						<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
						<div id="adg-mail-cont">
						<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
						<div>
						<div class="adg-mail-title-wrap">
						<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
						</div>
						<div class="adg-mail-text-wrap">
						<p class="adg-mail-text">Thank you for registering to our Ad Agency. Your account is now pending approval. We will send you an email as soon as we approve it. <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Username</strong> - {login} </span> <strong class="adg-block">Password</strong> - {password}</span></p>
						<p class="adg-mail-text">Please click the following link to activate your user account: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Activation link</strong> - {activate_url} </span> </span></p>
						<p class="adg-mail-text">This will give you access to our site as a user, not as advertiser. When we approve your advertiser account, we will notify you via email.</p>
						<p class="adg-mail-text">Ad Agency Manager</p>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>';
		$reportemail ='<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
						<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
						<div id="adg-mail-cont">
						<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
						<div>
						<div class="adg-mail-title-wrap">
						<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear {name}!</h2>
						</div>
						<div class="adg-mail-text-wrap">
						<p class="adg-mail-text">The following is a clicks and impressions report for {daterange} <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Campaign Name:</strong> - {campaign} </span> <strong class="adg-block">Total clicks:</strong> - {clicks} <br /> <strong class="adg-block">Total impressions:</strong> - {impressions} </span></p>
						<p class="adg-mail-text">{used_for_more_campaigns}</p>
						<p class="adg-mail-text">You may login to your advertiser interface for more detailed reports.</p>
						<p class="adg-mail-text">Thank you!</p>
						<p class="adg-mail-text">Admin</p>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>';
		$campapp = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">Your campaign {campaign} has been approved!</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$campdisapp ='<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
						<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
						<div id="adg-mail-cont">
						<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
						<div>
						<div class="adg-mail-title-wrap">
						<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
						</div>
						<div class="adg-mail-text-wrap">
						<p class="adg-mail-text">Your campaign {campaign} has been suspended by an administrator!</p>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>';
		$bannerapp = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">Your banner {banner} been approved!</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$bannerdisapp = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">Your banner {banner} has been dissapproved.</p>
					<p class="adg-mail-text">Possible reasons:</p>
					<ul>
					<li>Banner is in the wrong size</li>
					<li>Banner is inappropriate to our needs</li>
					<li>Banner has porn or other problems</li>
					</ul>
					<p class="adg-mail-text">If you have any questions regarding this, please reply to this email.</p>
					<p class="adg-mail-text">Thank you,</p>
					<p class="adg-mail-text">Ad Agency Admin</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$advdisapp = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Hello {name}!</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">Your advertiser account on our Ad Agency has been disapproved.</p>
					<p class="adg-mail-text">Possible reasons:</p>
					<ul>
					<li>We do not accept new advertisers at this point</li>
					<li>We do not advertise your kind of products on our site</li>
					</ul>
					<p class="adg-mail-text">If you have any questions regarding this, please reply to this email.</p>
					<p class="adg-mail-text">Thank you,</p>
					<p class="adg-mail-text">Ad Agency Admin</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$campexp = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear {name}!</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">Campaign {campaign} has expired {expire_date}. Please click on the link below to start a new campaign with the same package:</p>
					<p class="adg-mail-text"><span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> {campaign_renew_URL}</span> </span></p>
					<p class="adg-mail-text">To purchase a different package, click here:</p>
					<p class="adg-mail-text"><span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> {packages_url}</span> </span></p>
					<p class="adg-mail-text">Admin</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		
		$adminbanneradd = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
						<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
						<div id="adg-mail-cont">
						<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
						<div>
						<div class="adg-mail-title-wrap">
						<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear admin,</h2>
						</div>
						<div class="adg-mail-text-wrap">
						<p class="adg-mail-text">A new banner/ad was added: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Advertiser Name:</strong> {name} <br /> <strong class="adg-block">Company Name:</strong> {company} <br /> <strong class="adg-block">Email:</strong> {email} <br /> <strong class="adg-block">Phone:</strong> {phone} <br /> <strong class="adg-block">Banner Name::</strong> {banner} <br /> <strong class="adg-block">Banner Preview:</strong> {banner_preview_url} <br /> <br /> ---------------------------------------- <br /> <strong class="adg-block">Status:</strong>{approval_status} <br /> ---------------------------------------- <br /> </span> </span></p>
						<p class="adg-mail-text">To approve this ad, click on this URL or copy and paste it into your browser window: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">{approve_banner_url}</strong> </span> </span></p>
						<p class="adg-mail-text">To decline this ad, click on this URL or copy and paste it into your browser window: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">{decline_banner_url}</strong> </span> </span></p>
						</div>
						</div>
						</div>
						</div>
						</div>
						</div>';
		$admincampadd = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear admin,</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">A new campaign was added: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Advertiser Name:</strong> {name} <br /> <strong class="adg-block">Company Name:</strong> {company} <br /> <strong class="adg-block">Email:</strong> {email} <br /> <strong class="adg-block">Phone:</strong> {phone} <br /> <strong class="adg-block">Campaign Name:</strong> {campaign} <br /> <strong class="adg-block">Package name used to create this campaign::</strong> {package} <br /> <br /> ---------------------------------------- <br /> <strong class="adg-block">Status:</strong>{approval_status} <br /> ---------------------------------------- <br /> </span> </span></p>
					<p class="adg-mail-text">To approve this campaign, click on this URL or copy and paste it into your browser window: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">{approve_campaign_url} </strong> </span> </span></p>
					<p class="adg-mail-text">To decline this campaign, click on this URL or copy and paste it into your browser window: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">{decline_campaign_url}</strong> </span> </span></p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$adminadvreg = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear admin,</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">The following advertiser has signed up to your advertising program. <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">Full Name:</strong> {name} <br /> <strong class="adg-block">Email:</strong> {email} <br /> <strong class="adg-block">Phone:</strong> {phone} <br /> <strong class="adg-block">URL:</strong> {url} <br /> <strong class="adg-block">Username:</strong> {username} <br /> <strong class="adg-block">Company Name:</strong> {company} <br /> <strong class="adg-block">Description:</strong> {description} <br /> <strong class="adg-block">Street :</strong> {street} <br /> <strong class="adg-block">Country :</strong> {country} <br /> <strong class="adg-block">State/Province:</strong> {state} <br /> <strong class="adg-block">Zip Code:</strong> {zipcode} <br /> ---------------------------------------- <br /> <strong class="adg-block">Status:</strong> {approval_status} <br /> ---------------------------------------- <br /> </span> </span></p>
					<p class="adg-mail-text">To approve this advertiser, click on this URL or copy and paste it into your browser window: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">{approve_advertiser_url} </strong> </span> </span></p>
					<p class="adg-mail-text">To decline this advertiser, click on this URL or copy and paste it into your browser window: <span class="adg-mail-info" style="background: #E5F6FF; border-radius: 4px; border: 1px solid #A3E0FF; padding: 10px; display: block; margin-top: 10px;"><span style="display: block;"> <strong class="adg-block">{decline_advertiser_url}</strong> </span> </span></p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$admincampexp = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear admin,</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">Campaign {campaign} of company {company} has expired {expire_date}.</p>
					<p class="adg-mail-text">Admin</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
		$admincampmod = '<div id="adg-mail-gwrap" style="background: #eee; padding: 20px 0;">
					<div id="adg-mail-wrap" style="width: 75%; position: relative; margin: auto;">
					<div id="adg-mail-cont">
					<div style="background: #fff; padding: 10px; border-radius: 4px; border: 1px solid #ccc;">
					<div>
					<div class="adg-mail-title-wrap">
					<h2 class="adg-mail-title" style="font-size: 22px; background: #444; margin: -11px -11px 0px -11px; border-radius: 4px 4px 0 0; padding: 15px 10px; lineheight: 40px; color: #fff;"><span style="background: #FFFFFF; border-radius: 3px; box-shadow: 0 0 1px #000000; font-size: 20px; margin-right: 10px; padding: 0 6px;"><img src="components/com_adagency/images/envelope.png" alt="ij" width="15px" height="auto" /></span> Dear admin,</h2>
					</div>
					<div class="adg-mail-text-wrap">
					<p class="adg-mail-text">The ad {banner} has been modified by {name}.</p>
					<p class="adg-mail-text">Ad status: {approval_status}</p>
					</div>
					</div>
					</div>
					</div>
					</div>
					</div>';
					
		if(@!$textafterreg["0"]) {
			$no_setting_in_db = true;
			$config = new JConfig();
				$sql = "INSERT INTO `#__ad_agency_settings` (`id`, `lastsend`, `adminemail`, `fromemail`, `fromname`, `imgfolder`, `maxchars`, `captcha`, `allowstand`, `allowadcode`, `allowpopup`, `allowswf`, `allowtxtlink`, `allowtrans`, `allowfloat`, `txtafterreg`, `bodyafterreg`, `sbafterreg`, `bodyactivation`, `sbactivation`, `bodyrep`, `sbrep`, `bodycmpappv`, `sbcmpappv`, `bodycmpdis`, `sbcmpdis`, `bodyadappv`, `sbadappv`, `bodyaddisap`, `sbaddisap`, `bodyadvdis`, `sbadvdis`, `bodynewad`, `sbnewad`, `bodynewcmp`, `sbnewcmp`, `bodycmpex`, `sbcmpex`, `bodynewuser`, `sbnewuser`, `currencydef`, `indextbl`, `askterms`, `termsid`, `overviewcontent`, `showpreview`, `show`, `mandatory`, `params`, `sbafterregaa`, `bodyafterregaa`, `geoparams`, `sbcmpexpadm`, `bodycmpexpadm`, `sbadchanged`, `boadchanged`) VALUES
	(1, ".time().", '".$config->mailfrom."', '".$config->mailfrom."', 'Ad Agency', 'ad_agency', 250, 0, '1', '1', '1', '1', '1', '1', '1', '".$txtafterreg."', '".$bdyafterreg."', 'Thank you for registering at iJoomla Ad Agency', '".$afterregneedsapp."', 'Thank you for registering at iJoomla Ad Agency', '".$reportemail."', 'Subject Report', '".$campapp. "', 'Campaign Approved', '".$campdisapp."', 'Campaign {campaign} has been suspended', '".$bannerapp."', '{banner}  Approved', '".$bannerdisapp."', 'Banner {banner} has been suspended', '".$advdisapp."', 'Your account on iJoomla Ad Agency has been suspended', '".$adminbanneradd."', '{name} has added a banner {banner}', '".$admincampadd."', '{name} has added a campaign {campaign}', '".$campexp."', 'Campaign {campaign} has expired', '".$adminadvreg."', '{name} has joined the Ad Agency', 'USD', 0, '0', 0, '<p>Welcome to our advertising opportunity page.</p><p>MySiteName is one of the most trafficked sites about MySiteTopic on the web. It gets well over X unique visitors a month and close to X pageviews.</p><p>How to get started</p><p>Step 1: <a href=\"index.php?option=com_adagency&amp;controller=adagencyAdvertisers&amp;task=register\">Register as an advertiser</a><br />Step 2: <a href=\"index.php?option=com_adagency&amp;controller=adagencyAds&amp;task=addbanners\">Add banners/ads</a><br />Step 3: <a href=\"index.php?option=com_adagency&amp;controller=adagencyCampaigns&amp;task=edit&amp;cid=0\">Start a campaign</a><br />Step 4: Get traffic to your site</p>
	<p>{packages}</p>', 0, 'zinfo;aftercamp2;wizzard;url;phone;', '', 'a:5:{s:11:\"click_limit\";s:2:\"10\";s:12:\"jquery_front\";s:1:\"0\";s:11:\"jquery_back\";s:1:\"0\";s:10:\"timeformat\";s:1:\"0\";s:8:\"jom_back\";s:1:\"1\";}', 'Thank you for registering to our advertising program', '".$afterregaap."', 'a:9:{s:14:\"allowcontinent\";s:1:\"1\";s:12:\"allowlatlong\";s:1:\"1\";s:12:\"allowcountry\";s:1:\"1\";s:2:\"c1\";s:1:\"1\";s:2:\"c2\";s:1:\"1\";s:2:\"c3\";s:1:\"1\";s:2:\"c4\";s:1:\"1\";s:2:\"c5\";s:1:\"1\";s:2:\"c6\";s:1:\"1\";}', 'Campaign {campaign} has expired', '".$admincampexp."', 'Ad {banner} has been modified by {name}', '".$admincampmod."');
	";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$database->query();
		}

		// Check for zones
		$sql = "SELECT * FROM #__ad_agency_zone";
		$sqlz[] = $sql;
		$database->setQuery($sql);
		$isZone = $database->loadColumn();

		$sql = "SELECT * FROM #__ad_agency_advertis";
		$sqlz[] = $sql;
		$database->setQuery($sql);
		$isAdvert = $database->loadColumn();

		$sql = "SELECT * FROM #__ad_agency_banners";
		$sqlz[] = $sql;
		$database->setQuery($sql);
		$isAd = $database->loadColumn();

		$sql = "SELECT * FROM #__ad_agency_campaign";
		$sqlz[] = $sql;
		$database->setQuery($sql);
		$isCamp = $database->loadColumn();
		
		if(($isZone == NULL)&&($isAdvert == NULL)&&($isAd == NULL)&&($isCamp == NULL)&&(isset($no_setting_in_db))) {
			$sql = "SELECT id FROM #__users WHERE username = 'ijoomla' ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$existingUID = $database->loadColumn();
			$existingUID = $existingUID["0"];
			$ok = true;
			if(!$existingUID) {
				// if the user doesn't exist then we create it
				$acl =& JFactory::getACL();
				jimport('joomla.application.component.helper');
				$usersParams = &JComponentHelper::getParams( 'com_users' );
				$user = JFactory::getUser(0);
				$data = array();
				$usertype = 'Registered';
				$data['name'] = 'iJoomla';
				$data['username'] = 'ijoomla';
				$data['email'] = 'demo@ijoomla.com';
				$data['gid'] = 18;
				$password = $this->random_password();
				$data['password'] = $password;
				$data['password2'] = $password;
				$data['sendEmail'] = 0;
				$data['block'] = 0;
				if (!$user->bind($data)) { $ok = false;	}
				if (!$user->save()) { $ok = false; }
				$sql = "SELECT id FROM #__users WHERE username = 'ijoomla' ";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$existingUID = $database->loadColumn();
				$existingUID = $existingUID["0"];
			}
			if($existingUID) {
				$sql = "INSERT INTO `#__ad_agency_advertis` (`aid`, `user_id`, `company`, `description`, `website`, `address`, `country`, `city`, `state`, `zip`, `telephone`, `fax`, `logo`, `email_daily_report`, `email_weekly_report`, `email_month_report`, `email_campaign_expiration`, `approved`, `lastreport`, `weekreport`, `monthreport`, `paywith`, `apr_ads`, `apr_cmp`, `key`, `ordering`, `checked_out`) VALUES
	(2, ".$existingUID.", '', NULL, 'http://ijoomla.com', NULL, NULL, NULL, NULL, NULL, '', '10', NULL, 'N', 'N', 'N', 'N', 'Y', 1373395233, 1373395233, 1373395233, '', 'G', 'G', NULL, 0, 0);
	";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
				
				// make default user registered
				$sql = "select count(*) from #__user_usergroup_map where `user_id`=".intval($existingUID);
				$database->setQuery($sql);
				$database->query();
				$exist_map = $database->loadColumn();
				$exist_map = $exist_map["0"];
				
				if($exist_map == 0){
					$sql = "INSERT INTO `#__user_usergroup_map` (`user_id` ,`group_id`) VALUES ('" . $existingUID . "', '2');";
					$database->setQuery($sql);
					$database->query();
				}
			}
			
			$sql = "SELECT id FROM #__users WHERE username = 'jomsocial' ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$existingUID = $database->loadColumn();
			$existingUID = $existingUID["0"];
			$ok = true;
			
			if(!$existingUID) {
				// if the user doesn't exist then we create it
				$acl =& JFactory::getACL();
				jimport('joomla.application.component.helper');
				$usersParams = &JComponentHelper::getParams( 'com_users' );
				$user = JFactory::getUser(0);
				$data = array();
				$usertype = 'Registered';
				$data['name'] = 'JomSocial';
				$data['username'] = 'jomsocial';
				$data['email'] = 'demo@jomsocial.com';
				$data['gid'] = 18;
				$password = $this->random_password();
				$data['password'] = $password;
				$data['password2'] = $password;
				$data['sendEmail'] = 0;
				$data['block'] = 0;
				if (!$user->bind($data)) { $ok = false;	}
				if (!$user->save()) { $ok = false; }
				$sql = "SELECT id FROM #__users WHERE username = 'jomsocial' ";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$existingUID = $database->loadColumn();
				$existingUID = $existingUID["0"];
			}
			
			if($existingUID) {
				$sql = "INSERT INTO `#__ad_agency_advertis` (`aid`, `user_id`, `company`, `description`, `website`, `address`, `country`, `city`, `state`, `zip`, `telephone`, `fax`, `logo`, `email_daily_report`, `email_weekly_report`, `email_month_report`, `email_campaign_expiration`, `approved`, `lastreport`, `weekreport`, `monthreport`, `paywith`, `apr_ads`, `apr_cmp`, `key`, `ordering`, `checked_out`) VALUES
	(1, ".$existingUID.", '', NULL, 'http://jomsocial.com', NULL, NULL, NULL, NULL, NULL, '', '10', NULL, 'N', 'N', 'N', 'N', 'Y', 1373395050, 1373395050, 1373395050, '', 'G', 'G', '', 0, 0);
	";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
				
				// make default user registered
				$sql = "select count(*) from #__user_usergroup_map where `user_id`=".intval($existingUID);
				$database->setQuery($sql);
				$database->query();
				$exist_map = $database->loadColumn();
				$exist_map = $exist_map["0"];
				
				if($exist_map == 0){
					$sql = "INSERT INTO `#__user_usergroup_map` (`user_id` ,`group_id`) VALUES ('" . $existingUID . "', '2');";
					$database->setQuery($sql);
					$database->query();
				}
			}
			// Add the default advertiser if no advertiser - END

			// Add the default zones if there is no zone - BEGIN
			if($ok) {
				$sql = "INSERT INTO `#__modules` (`title`, `note`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `publish_up`, `publish_down`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`, `language`)  VALUES  ('Zone - Banners', '', '', 0, 'position-7', 0, '0000-00-00 00:00:00', '2013-07-09 18:56:46', '0000-00-00 00:00:00', 1, 'mod_ijoomla_adagency_zone', 1, 1, '{\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 0, ''),
('Zone - Text Ads', '', '', 0, 'position-7', 0, '0000-00-00 00:00:00', '2013-07-09 18:56:33', '0000-00-00 00:00:00', 1, 'mod_ijoomla_adagency_zone', 1, 1, '{\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 0, ''),
('Floating, Transitions, Popups', '', '', 0, 'debug', 0, '0000-00-00 00:00:00', '2013-07-09 18:57:15', '0000-00-00 00:00:00', 1, 'mod_ijoomla_adagency_zone', 1, 0, '{\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 0, ''), ('Inventory Zone', '', '', 0, 'position-7', 0, '0000-00-00 00:00:00', '2013-07-09 18:59:37', '0000-00-00 00:00:00', 1, 'mod_ijoomla_adagency_zone', 1, 1, '{\"moduleclass_sfx\":\"\",\"cache\":\"0\"}', 0, '');";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				if(!$database->query()) {$ok = false;}
			}

			$sql = "SELECT id FROM `#__modules` WHERE title = 'Zone - Banners' and module = 'mod_ijoomla_adagency_zone' ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$leftZoneId = $database->loadColumn();
			$leftZoneId = $leftZoneId["0"];
			
			if($leftZoneId){
				$sql = "INSERT INTO `#__ad_agency_zone` (`zoneid`, `banners`, `banners_cols`, `z_title`, `z_ordering`, `z_position`, `show_title`, `suffix`, `rotatebanners`, `rotating_time`, `rotaterandomize`, `show_adv_link`, `cellpadding`, `link_taketo`, `taketo_url`, `itemid`, `defaultad`, `keywords`, `adparams`, `ignorestyle`, `textadparams`, `zone_text_below`, `zone_content_location`, `zone_content_visibility`, `ordering`, `checked_out`, `inventory_zone`) VALUES (".$leftZoneId.", 1, 2, 'Zone - Banners', 0, 'position-7', 1, 'moduleclass_sfx=', '0', 10000, '1', 0, 1, 3, 'http://', 0, 0, 0, 'a:5:{s:8:\"standard\";s:1:\"1\";s:9:\"affiliate\";s:1:\"1\";s:5:\"flash\";s:1:\"1\";s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 0, 0, 0, 0, 0)";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
				// Assign module to all pages
				$sql = "INSERT INTO `#__modules_menu` (`moduleid` ,`menuid`) VALUES ('".$leftZoneId."', '0');";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				if(!$database->query()) {$ok = false;}
			}

			$sql = "SELECT id FROM `#__modules` WHERE title = 'Zone - Text Ads' and module = 'mod_ijoomla_adagency_zone' ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$rightZoneId = $database->loadColumn();
			$rightZoneId = $rightZoneId["0"];
			
			if($rightZoneId){
				$sql = "INSERT INTO `#__ad_agency_zone` (`zoneid`, `banners`, `banners_cols`, `z_title`, `z_ordering`, `z_position`, `show_title`, `suffix`, `rotatebanners`, `rotating_time`, `rotaterandomize`, `show_adv_link`, `cellpadding`, `link_taketo`, `taketo_url`, `itemid`, `defaultad`, `keywords`, `adparams`, `ignorestyle`, `textadparams`, `zone_text_below`, `zone_content_location`, `zone_content_visibility`, `ordering`, `checked_out`, `inventory_zone`) VALUES (".$rightZoneId.", 2, 1, 'Zone - Text Ads', 0, 'position-7', 1, 'moduleclass_sfx=', '0', 10000, '1', 1, 1, 3, 'http://', 0, 0, 0, 'a:3:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";s:6:\"textad\";s:1:\"1\";}', '', 'a:6:{s:6:\"mxsize\";s:2:\"50\";s:6:\"mxtype\";s:1:\"w\";s:2:\"ia\";s:1:\"l\";s:8:\"wrap_img\";s:1:\"1\";s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 0, 0, 0, 0, 0);
	";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
				// Assign module to all pages
				$sql = "INSERT INTO `#__modules_menu` (`moduleid` ,`menuid`) VALUES ('".$rightZoneId."', '0');";
				$database->setQuery($sql);
				if(!$database->query()) {$ok = false;}

			}

			$sql = "SELECT id FROM `#__modules` WHERE title = 'Floating, Transitions, Popups' and module = 'mod_ijoomla_adagency_zone' ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$floatingZoneId = $database->loadColumn();
			$floatingZoneId = $floatingZoneId["0"];
			
			if($floatingZoneId){
				$sql = "INSERT INTO `#__ad_agency_zone` (`zoneid`, `banners`, `banners_cols`, `z_title`, `z_ordering`, `z_position`, `show_title`, `suffix`, `rotatebanners`, `rotating_time`, `rotaterandomize`, `show_adv_link`, `cellpadding`, `link_taketo`, `taketo_url`, `itemid`, `defaultad`, `keywords`, `adparams`, `ignorestyle`, `textadparams`, `zone_text_below`, `zone_content_location`, `zone_content_visibility`, `ordering`, `checked_out`, `inventory_zone`) VALUES (".$floatingZoneId.", 1, 1, 'Floating, Transitions, Popups', 0, 'debug', 0, 'moduleclass_sfx=', '0', 10000, '1', 0, 1, 3, 'http://', 0, 0, 0, 'a:5:{s:5:\"popup\";s:1:\"1\";s:10:\"transition\";s:1:\"1\";s:8:\"floating\";s:1:\"1\";s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 0, 0, 0, 0, 0);";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
				// Assign module to all pages
				$sql = "INSERT INTO `#__modules_menu` (`moduleid` ,`menuid`) VALUES ('".$floatingZoneId."', '0');";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				if(!$database->query()) {$ok = false;}
			}
			
			$sql = "SELECT id FROM `#__modules` WHERE title = 'Inventory Zone' and module = 'mod_ijoomla_adagency_zone' ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$inventoryZoneId = $database->loadColumn();
			$inventoryZoneId = $inventoryZoneId["0"];
			
			if($inventoryZoneId){
				$sql = "INSERT INTO `#__ad_agency_zone` (`zoneid`, `banners`, `banners_cols`, `z_title`, `z_ordering`, `z_position`, `show_title`, `suffix`, `rotatebanners`, `rotating_time`, `rotaterandomize`, `show_adv_link`, `cellpadding`, `link_taketo`, `taketo_url`, `itemid`, `defaultad`, `keywords`, `adparams`, `ignorestyle`, `textadparams`, `zone_text_below`, `zone_content_location`, `zone_content_visibility`, `ordering`, `checked_out`, `inventory_zone`) VALUES (".$inventoryZoneId.", 2, 2, 'Inventory Zone', 0, 'position-7', 1, '', '0', 10000, '1', 1, 1, 3, 'http://', 0, 0, 0, 'a:5:{s:8:\"standard\";s:1:\"1\";s:9:\"affiliate\";s:1:\"1\";s:5:\"flash\";s:1:\"1\";s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 'a:2:{s:5:\"width\";s:0:\"\";s:6:\"height\";s:0:\"\";}', '', 0, 0, 0, 0, 1);";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
				// Assign module to all pages
				$sql = "INSERT INTO `#__modules_menu` (`moduleid` ,`menuid`) VALUES ('".$inventoryZoneId."', '0');";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				if(!$database->query()) {$ok = false;}
			}

			// Add the default zones if there is no zone - END

			// Add the default banners if there are none - BEGIN
			if(($leftZoneId)&&($rightZoneId)&&($floatingZoneId)&&($inventoryZoneId)){
				$sql = "INSERT INTO `#__ad_agency_banners` (`id`, `advertiser_id`, `title`, `description`, `media_type`, `image_url`, `swf_url`, `target_url`, `width`, `height`, `ad_code`, `use_ad_code_in_netscape`, `ad_code_netscape`, `parameters`, `approved`, `zone`, `frequency`, `created`, `ordering`, `keywords`, `key`, `channel_id`, `ad_start_date`, `ad_end_date`, `checked_out`) VALUES (1, 2, 'iJoomla SEO 120*240', '', 'Advanced', NULL, NULL, NULL, 120, 240, '<a href=\"ad_url\" target=\"_top\"><img src=\"http://ijoomla.com/affiliates/accounts/default1/banners/abbd7714.gif\" alt=\"SEO\" title=\"SEO\" width=\"120\" height=\"240\" /></a><img style=\"border:0\" src=\"http://www.ijoomla.com/affiliates/scripts/imp.php?a_aid=ijoomla&amp;a_bid=abbd7714\" width=\"1\" height=\"1\" alt=\"\" />', 'N', NULL, 'a:2:{s:13:\"target_window\";s:6:\"_blank\";s:9:\"linktrack\";s:55:\"http://seo.ijoomla.com?a_aid=ijoomla&amp;a_bid=abbd7714\";}', 'Y', 0, NULL, '2010-11-24', 3, '', '', -1, '1969-12-31 19:00:00', '0000-00-00 00:00:00', 0),
(2, 2, 'iJoomla SEO', '', 'TextLink', '', NULL, 'http://seo.ijoomla.com/', '', '', NULL, 'N', NULL, 'a:22:{s:11:\"font_family\";s:0:\"\";s:9:\"font_size\";s:2:\"14\";s:11:\"title_color\";s:6:\"0066CC\";s:11:\"font_weight\";s:18:\"lighter underlined\";s:8:\"alt_text\";s:67:\"Become a search engine magnet with this amazing Joomla Extension ! \";s:10:\"alt_text_t\";s:11:\"iJoomla SEO\";s:10:\"alt_text_a\";s:13:\"Learn More >>\";s:13:\"font_family_b\";s:0:\"\";s:11:\"font_size_b\";s:2:\"12\";s:10:\"body_color\";s:6:\"000000\";s:13:\"font_weight_b\";s:6:\"normal\";s:13:\"font_family_a\";s:0:\"\";s:11:\"font_size_a\";s:2:\"12\";s:12:\"action_color\";s:6:\"0066CC\";s:13:\"font_weight_a\";s:18:\"lighter underlined\";s:6:\"border\";s:1:\"0\";s:7:\"padding\";s:1:\"0\";s:12:\"border_color\";s:6:\"000000\";s:8:\"bg_color\";s:6:\"FFFFFF\";s:5:\"align\";s:4:\"left\";s:13:\"target_window\";s:6:\"_blank\";s:7:\"img_alt\";s:24:\"http://seo2.ijoomla.com/\";}', 'Y', 0, NULL, '2010-11-24', 0, '', '', -1, '1969-12-31 19:00:00', '0000-00-00 00:00:00', 0),
(3, 2, 'iJoomla Surveys 125*125', '', 'Advanced', NULL, NULL, NULL, 125, 125, '<a href=\"ad_url\" target=\"_top\"><img src=\"http://www.ijoomla.com/affiliates/accounts/default1/banners/fa5650ff.gif\" alt=\"Surveys\" title=\"Surveys\" width=\"125\" height=\"125\" /></a><img style=\"border:0\" src=\"http://www.ijoomla.com/affiliates/scripts/imp.php?a_aid=ijoomla&a_bid=fa5650ff\" width=\"1\" height=\"1\" alt=\"\" />                                                                                                                                ', 'N', NULL, 'a:2:{s:13:\"target_window\";s:6:\"_blank\";s:9:\"linktrack\";s:0:\"\";}', 'Y', 0, NULL, '2010-11-24', 2, '', '', -1, '1969-12-31 19:00:00', '0000-00-00 00:00:00', 0),
(4, 2, 'Guru 125*125', '', 'Advanced', NULL, NULL, NULL, 125, 125, '<a href=\"ad_url\" target=\"_top\"><img src=\"http://ijoomla.com/affiliates/accounts/default1/banners/42945100.gif\" alt=\"\" title=\"\" width=\"125\" height=\"125\" /></a><img style=\"border:0\" src=\"http://www.ijoomla.com/affiliates/scripts/imp.php?a_aid=ijoomla&amp;a_bid=42945100\" width=\"1\" height=\"1\" alt=\"\" />', 'N', NULL, 'a:2:{s:13:\"target_window\";s:6:\"_blank\";s:9:\"linktrack\";s:56:\"http://guru.ijoomla.com?a_aid=ijoomla&amp;a_bid=42945100\";}', 'Y', 0, NULL, '2010-11-24', 1, '', '', -1, '1969-12-31 19:00:00', '0000-00-00 00:00:00', 0),
(5, 1, 'JomSocial 250*250', '', 'Advanced', NULL, NULL, NULL, 250, 250, '                                                        <a href=\"ad_url\" target=\"_blank\"><img src=\"http://www.ijoomla.com/affiliates/accounts/default1/banners/957f42d0.png\" alt=\"Start a Social Network\" title=\"Start a Social Network\" width=\"200\" height=\"200\" /></a><img style=\"border:0\" src=\"http://www.ijoomla.com/affiliates/scripts/imp.php?a_aid=ijoomla&amp;a_bid=957f42d0\" width=\"1\" height=\"1\" alt=\"\" />', 'N', NULL, 'a:2:{s:13:\"target_window\";s:6:\"_blank\";s:9:\"linktrack\";s:58:\"http://www.jomsocial.com/?a_aid=ijoomla&amp;a_bid=957f42d0\";}', 'Y', 0, NULL, '2013-07-09', 4, '', '', NULL, '2013-07-09 00:00:00', '0000-00-00 00:00:00', 0),
(6, 1, 'JomSocial 160*140', '', 'Advanced', NULL, NULL, NULL, 160, 140, '<a href=\"ad_url\" target=\"_blank\"><img src=\"http://www.ijoomla.com/affiliates/accounts/default1/banners/79b4a921.png\" alt=\"Start a Social Network\" title=\"Start a Social Network\" width=\"160\" height=\"140\" /></a><img style=\"border:0\" src=\"http://www.ijoomla.com/affiliates/scripts/imp.php?a_aid=ijoomla&amp;a_bid=79b4a921\" width=\"1\" height=\"1\" alt=\"\" />', 'N', NULL, 'a:2:{s:13:\"target_window\";s:6:\"_blank\";s:9:\"linktrack\";s:58:\"http://www.jomsocial.com/?a_aid=ijoomla&amp;a_bid=79b4a921\";}', 'Y', 0, NULL, '2013-07-09', 4, '', '', NULL, '2013-07-09 00:00:00', '0000-00-00 00:00:00', 0);
	";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
			}
			// Add the default banners if there are none - END

			// Add the default packages if there are none - BEGIN
			$sql = "INSERT INTO `#__ad_agency_order_type` (`tid`, `description`, `pack_description`, `quantity`, `type`, `cost`, `validity`, `sid`, `published`, `visibility`, `zones`, `zones_wildcard`, `ordering`, `hide_after`, `location`, `checked_out`) VALUES
	(1, '1000 Impressions', 'Try our advertising system with 1000 free impressions!', 1000, 'cpm', 0.00, '', 0, 1, 1, '', NULL, 3, 1, ".$leftZoneId.", 0),
	(2, '1 Month Text Ads', 'One month of advertising your text ad on our site', 0, 'fr', 49.99, '1|day', 0, 1, 1, '', NULL, 2, 0, ".$leftZoneId.", 0),
	(3, 'Internal Package ', 'This package is for internal use, it''s invisible on front end. Use it to display your own banners.', 0, 'fr', 0.00, '10|year', 0, 1, 0, '', NULL, 1, 0, ".$rightZoneId.", 0),
	(4, '100 Clicks', 'This package will get you 100 clicks of your banners.', 100, 'pc', 99.99, '', 0, 1, 1, '', NULL, 4, 0, ".$leftZoneId.", 0),
	(6, 'Inventory Package', 'This package has a limit on how many times it can be purchased for a time period.', 0, 'in', 99.00, '1|month', 0, 1, 1, '', NULL, 0, 0, 0, 0);";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			if(!$database->query()) {$ok = false;}
			// Add the default packages if there are none - END

			// Add the default campaign if there is none - BEGIN
			$sql = "INSERT INTO `#__ad_agency_campaign` (`id`, `aid`, `name`, `notes`, `default`, `start_date`, `type`, `quantity`, `validity`, `cost`, `otid`, `approved`, `status`, `exp_notice`, `key`, `params`, `renewcmp`, `activities`, `ordering`, `checked_out`) VALUES (3, 2, 'iJoomla Campaign', '', 'Y', '2013-07-09 19:06:28', 'fr', 0, '2023-07-09 19:06:28', 0.00, 3, 'Y', 1, 0, NULL, 'a:1:{s:6:\"adslim\";i:999;}', 0, '', 0, 0),
(4, 1, 'JomSocial Campaign', '', 'Y', '2013-07-09 19:07:11', 'fr', 0, '2023-07-09 19:07:11', 0.00, 3, 'Y', 1, 0, NULL, 'a:1:{s:6:\"adslim\";i:999;}', 0, '', 0, 0); ";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			if(!$database->query()) {$ok = false;}
			// Set banners to campaigns - BEGIN
			if($ok){
				$sql = "INSERT INTO `#__ad_agency_campaign_banner` (`campaign_id`, `banner_id`, `relative_weighting`, `thumb`, `zone`) VALUES
						(3, 3, 100, NULL, ".intval($leftZoneId)."),
						(3, 4, 100, NULL, ".intval($leftZoneId)."),
						(3, 1, 100, NULL, ".intval($leftZoneId)."),
						(3, 2, 100, NULL, ".intval($rightZoneId)."),
						(4, 5, 100, NULL, ".intval($leftZoneId).");
						";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				if(!$database->query()) {$ok = false;}
			}
			// Set banners to campaigns - END

			// Add the default campaign if there is none - END

			// Update to 2.0.3
			$sql = "INSERT INTO `#__ad_agency_package_zone` (`package_id`, `zone_id`) VALUES
					(1, ".intval($leftZoneId)."),
					(2, ".intval($rightZoneId)."),
					(3, ".intval($leftZoneId)."),
					(3, ".intval($rightZoneId)."),
					(3, ".intval($floatingZoneId)."),
					(4, ".intval($leftZoneId)."),
					(5, ".intval($leftZoneId)."),
					(6, ".intval($inventoryZoneId).");";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$db->query();
			// - end update 2.0.3
		}

		$sql = "SELECT aid,user_id FROM #__ad_agency_advertis ORDER BY aid ASC LIMIT 1";
		$sqlz[] = $sql;
		$database->setQuery($sql);
		$first_adv = $database->loadObject();

		$sql = "SELECT * FROM `#__ad_agency_channels` ORDER BY id DESC LIMIT 1";
		$sqlz[] = $sql;
		$database->setQuery($sql);
		$new_channels = $database->loadColumn();

		// Add channels - BEGIN
		if ((!$new_channels) && isset($first_adv) && ($first_adv != NULL)) {
			$sql = "INSERT INTO `#__ad_agency_channels` (`id`, `name`, `banner_id`, `advertiser_id`, `public`, `created`, `created_by`, `from`) VALUES
			(1, 'United States', 0, '".$first_adv->aid."', 'Y', '2010-11-26 00:00:00', ".$first_adv->user_id.", 'B'),
			(2, 'Europe', 0, '".$first_adv->aid."', 'Y', '2010-11-26 00:00:00', ".$first_adv->user_id.", 'B'),
			(3, 'California', 0, '".$first_adv->aid."', 'Y', '2010-11-26 00:00:00', ".$first_adv->user_id.", 'B'),
			(4, 'San Diego and Los Angeles', 0, '".$first_adv->aid."', 'Y', '2010-11-26 00:00:00', ".$first_adv->user_id.", 'B'),
			(5, 'Beverly Hills', 0, '".$first_adv->aid."', 'Y', '2010-11-26 00:00:00', ".$first_adv->user_id.", 'B'),
			(6, 'Germany', 0, '".$first_adv->aid."', 'Y', '2010-11-26 00:00:00', ".$first_adv->user_id.", 'B');";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$database->query();

			$sql = "INSERT INTO `#__ad_agency_channel_set` (`id`, `channel_id`, `type`, `logical`, `option`, `data`) VALUES
			(1, 1, 'country', 'AND', 'is', '[\"US\"]'),
			(2, 2, 'continent', 'AND', 'is', '[\"EU\"]'),
			(3, 3, 'region', 'AND', 'is', '[\"US\",\"CA\"]'),
			(4, 4, 'dma', 'AND', '==', '[\"825\",\"803\"]'),
			(5, 5, 'postalcode', 'AND', '==', '[\"90210\"]'),
			(6, 6, 'country', 'AND', 'is', '[\"DE\"]');";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$database->query();

			$sql = "ALTER TABLE `#__ad_agency_settings` CHANGE `countryloc` `countryloc` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'geoip/countries'";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$database->query();

			$sql = "UPDATE `#__ad_agency_settings` SET `countryloc` = 'geoip/countries'";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$database->query();
		}

		// Add channels - END

		// v.1.6.7 updates - END
		jimport('joomla.filesystem.archive');

		// Insert Itemid part
		if(!isset($db)) {
			$db = &JFactory::getDBO();
		}
		$sql = "SELECT id FROM #__menu_types WHERE `menutype` = 'adagency' ";
		$sqlz[] = $sql;
		$db->setQuery($sql);
		$res = $db->loadColumn();

		$sql = "SELECT extension_id FROM #__extensions WHERE `element` = 'com_adagency' ";
		$sqlz[] = $sql;
		$db->setQuery($sql);
		$componentid = $db->loadColumn();
		$componentid = $componentid["0"];

		if (!$componentid) {
			$sql = "SELECT extension_id FROM #__extensions ORDER BY extension_id DESC LIMIT 1";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$componentid = $db->loadColumn();
			$componentid = $componentid["0"]++;
		}

		if($res == NULL) {
			$sql = "INSERT INTO `#__menu_types` (`menutype`, `title`, `description`) VALUES ('adagency', 'Ad Agency', 'Ad Agency Advertiser Menu');";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$db->query();
		}
		
		$sql = "select count(*) from #__menu where `link`='index.php?option=com_adagency&view=adagencycpanel'";
		$db->setQuery($sql);
		$db->query();
		$count_menu = $db->loadColumn();
		$count_menu = $count_menu["0"];
		
		if($count_menu == "0"){
			$sql = "INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`) VALUES
	( 'adagency', 'Control Panel', 'my-adagency-control-panel', '', 'adagency-cpanel', 'index.php?option=com_adagency&view=adagencycpanel', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'My profile', 'my-advertising-profile', '', 'adagency-profile', 'index.php?option=com_adagency&view=adagencyadvertisers&layout=editform', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'Ads', 'my-adagency-ads', '', 'adagency-ads', 'index.php?option=com_adagency&view=adagencyads', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'Campaigns', 'my-adagency-campaigns', '', 'adagency-campaigns', 'index.php?option=com_adagency&view=adagencycampaigns', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'Reports', 'my-adagency-reports', '', 'adagency-reports', 'index.php?option=com_adagency&view=adagencyreports', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'Packages', 'my-adagency-packages', '', 'adagency-packages', 'index.php?option=com_adagency&view=adagencypackage', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'My Orders', 'my-adagency-orders', '', 'adagency-orders', 'index.php?option=com_adagency&view=adagencyorders', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0),
	( 'adagency', 'Overview', 'my-adagency-overview', '', 'adagency-overview', 'index.php?option=com_adagency&view=adagencyadvertisers&layout=overview', 'component', 1, 1, 1, ".$componentid.", 0, '0000-00-00 00:00:00', 0, 1, '', 0, '{\"menu-anchor_title\":\"\",\"menu-anchor_css\":\"\",\"menu_image\":\"\",\"menu_text\":1,\"page_title\":\"\",\"show_page_heading\":0,\"page_heading\":\"\",\"pageclass_sfx\":\"\",\"menu-meta_description\":\"\",\"menu-meta_keywords\":\"\",\"robots\":\"\",\"secure\":0}', 0, 1, 0, '*', 0)";
			
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$db->query();

			$sql = "INSERT INTO `#__modules` (`title`, `content`, `ordering`, `position`, `checked_out`, `checked_out_time`, `published`, `module`, `access`, `showtitle`, `params`, `client_id`) VALUES
			('Ad Agency', '', 2, 'left', 0, '0000-00-00 00:00:00', 0, 'mod_mainmenu', 0, 1, 'menutype=adagency\nmenu_style=list\nstartLevel=0\nendLevel=0\nshowAllChildren=0\nwindow_open=\nshow_whitespace=0\ncache=1\ntag_id=\nclass_sfx=\nmoduleclass_sfx=_menu\nmaxdepth=10\nmenu_images=0\nmenu_images_align=0\nmenu_images_link=0\nexpand_menu=0\nactivate_parent=0\nfull_active_id=0\nindent_image=0\nindent_image1=\nindent_image2=\nindent_image3=\nindent_image4=\nindent_image5=\nindent_image6=\nspacer=\nend_spacer=\n\n', 0);";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$db->query();

			$sql = "SELECT id FROM `#__modules` ORDER BY id DESC LIMIT 1";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$module_id_adagency = $db->loadColumn();

			$sql = "INSERT INTO `#__modules_menu` (`moduleid` ,`menuid`) VALUES ('" . $module_id_adagency["0"] . "', '0');";
			$sqlz[] = $sql;
			$db->setQuery($sql);
			$db->query();
		}
		
		$sql = "UPDATE `#__menu` SET `component_id` = '" . $componentid . "', `parent_id` = '1', `level` = '1', `access` = '1' WHERE `menutype` = 'adagency' ";
		$db->setQuery($sql);
		$db->query();
		$sql = "UPDATE `#__modules` SET `module` = 'mod_menu' WHERE `title` = 'Ad Agency' ";
		$db->setQuery($sql);
		$db->query();
		// *********** END ITEMID PART ********************

		$this->add_plugins();
		$this->add_currencies();
		$this->add_countries();
	}
	
	function addNewColumns(){
		$db = JFactory::getDBO();
		
		$sql = "SHOW COLUMNS FROM #__ad_agency_zone";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		if(!in_array("zone_text_below", $result)){
			$sql = "ALTER TABLE `#__ad_agency_zone` ADD `zone_text_below` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("zone_content_location", $result)){
			$sql = "ALTER TABLE `#__ad_agency_zone` ADD `zone_content_location` int(3) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("zone_content_visibility", $result)){
			$sql = "ALTER TABLE `#__ad_agency_zone` ADD `zone_content_visibility` int(3) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__ad_agency_banners";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		if(!in_array("ad_start_date", $result)){
			$sql = "ALTER TABLE `#__ad_agency_banners` ADD `ad_start_date` datetime NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("ad_end_date", $result)){
			$sql = "ALTER TABLE `#__ad_agency_banners` ADD `ad_end_date` datetime NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("access", $result)){
			$sql = "ALTER TABLE `#__ad_agency_banners` ADD `access` int(3) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__ad_agency_campaign";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		if(!in_array("activities", $result)){
			$sql = "ALTER TABLE `#__ad_agency_campaign` ADD `activities` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__ad_agency_order";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		if(!in_array("promocodeid", $result)){
			$sql = "ALTER TABLE `#__ad_agency_order` ADD `promocodeid` int(3) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------
		$sql = "SHOW COLUMNS FROM #__ad_agency_settings";
		$db->setQuery($sql);
		$result = $db->loadColumn();
		
		if(!in_array("allow_add_keywords", $result)){
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `allow_add_keywords` int(3) NOT NULL DEFAULT '0'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("imagetools", $result)){
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `imagetools` int(3) NOT NULL DEFAULT '1'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("sbadchanged", $result)){
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `sbadchanged` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
			
			$sql = "update `#__ad_agency_settings` set `sbadchanged`='Ad {banner} has been modified by {name}'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("boadchanged", $result)){
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `boadchanged` text NOT NULL";
			$db->setQuery($sql);
			$db->query();
			
			$sql = "update `#__ad_agency_settings` set `boadchanged`='<p>Dear admin, <br /><br /> The ad {banner} has been modified by {name}.<br /> Ad status: {approval_status}</p>'";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("last_check_date", $result)){
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `last_check_date` datetime NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		if(!in_array("showpromocode", $result)){
			$sql = "ALTER TABLE `#__ad_agency_settings` ADD `showpromocode` int(3) NOT NULL";
			$db->setQuery($sql);
			$db->query();
		}
		//------------------------------------------------------------------------------------
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_promocodes` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `title` varchar(200) NOT NULL DEFAULT '',
				  `code` varchar(100) NOT NULL DEFAULT '',
				  `codelimit` int(11) NOT NULL DEFAULT '0',
				  `amount` float NOT NULL DEFAULT '0',
				  `codestart` int(11) NOT NULL DEFAULT '0',
				  `codeend` int(11) NOT NULL DEFAULT '0',
				  `forexisting` int(11) NOT NULL DEFAULT '0',
				  `published` int(11) NOT NULL DEFAULT '0',
				  `aftertax` int(11) NOT NULL DEFAULT '0',
				  `promotype` int(11) NOT NULL DEFAULT '0',
				  `used` int(11) NOT NULL DEFAULT '0',
				  `ordering` int(11) NOT NULL DEFAULT '0',
				  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
				  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				  PRIMARY KEY (`id`),
				  UNIQUE KEY `code` (`code`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($sql);
		$db->query();
	}

	function installAlertUploadPlugins(){
		$db =& JFactory::getDBO();
		$sql = "select count(*) from #__extensions where element='ijoomlanews'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = $count["0"];

		$component_dir = JPATH_SITE.'/administrator/components/com_adagency/plugins';

		if($count == 0){
		   $query = "INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,custom_data,system_data,checked_out, 	checked_out_time,ordering,state)"
			."\n VALUES ('iJoomla News', 'plugin', 'ijoomlanews', 'system', 0, 1, 1, 0, '', '{\"nr_articles\":\"3\",\"text_length\":\"100\",\"image_width\":\"50\"}', '', '', 0, '0000-00-00 00:00:00' , -10300, 0)";
			$db->setQuery($query);
			$db->query();
		}

		$sql = "select element from #__extensions where element='ijoomlaupdate'";
		$db->setQuery($sql);
		$db->query();
		$name = $db->loadColumn();

		if (empty($name["0"])){
		   $query = "INSERT INTO #__extensions (name,type,element,folder,client_id,enabled,access,protected,manifest_cache,params,custom_data,system_data,checked_out, 	checked_out_time,ordering,state)"
			."\n VALUES ('iJoomla Upgrade Alert', 'plugin', 'ijoomlaupdate', 'system', 0, 1, 1, 0, '', '{\"lastcheck\":\"0\"}', '', '', 0, '0000-00-00 00:00:00' , -10300, 0)";
			$db->setQuery($query);
			$db->query();
		}

		//----------------------------------------start news plugin
		$news_dir = JPATH_SITE.'/plugins/system/ijoomlanews';
		if(!is_dir($news_dir)){
			mkdir($news_dir, 0755);
		}
		$news_php = 'ijoomlanews.php';
		$news_xml = 'ijoomlanews.xml';
		$news_folder = 'ijoomlanews';
		@chmod($news_dir, 0755);
		if(!copy($component_dir."/ijoomlanews/".$news_xml, $news_dir."/".$news_xml)){
			echo 'Error copying ijoomlanews.xml'."<br/>";
		}
		if(!copy($component_dir."/ijoomlanews/".$news_php, $news_dir."/".$news_php)){
			echo 'Error copying ijoomlanews.php'."<br/>";
		}
		if(!is_dir($news_dir."/".$news_folder)){
			mkdir($news_dir."/".$news_folder, 0755);
		}
		if(!copy($component_dir."/ijoomlanews/".$news_folder."/feed.php", $news_dir."/".$news_folder."/feed.php")){
			echo 'Error copying feed.php'."<br/>";
		}
		if(!copy($component_dir."/ijoomlanews/".$news_folder."/tabs.php", $news_dir."/".$news_folder."/tabs.php")){
			echo 'Error copying tabs.php'."<br/>";
		}
		if(!copy($component_dir."/ijoomlanews/".$news_folder."/index.html", $news_dir."/".$news_folder."/index.html")){
			echo 'Error copying index.html'."<br/>";
		}
		
		if(!unlink($component_dir.'/ijoomlanews/'.$news_php)){
			echo 'Cannot delete '.$component_dir.'/ijoomlanews/'.$news_php."<br/>";
		}
		if(!unlink($component_dir.'/ijoomlanews/'.$news_xml)){
			echo 'Cannot delete '.$component_dir.'/ijoomlanews/'.$news_xml."<br/>";
		}
		//----------------------------------------stop news plugin

		//----------------------------------------start upgrade plugin
		$update_dir = JPATH_SITE.'/plugins/system/ijoomlaupdate';
		if(!is_dir($update_dir)){
			mkdir($update_dir, 0755);
		}
		$update_php = 'ijoomlaupdate.php';
		$update_xml = 'ijoomlaupdate.xml';
		$update_folder = 'ijoomlaupdate';
		@chmod($update_dir, 0755);
		if(!copy($component_dir."/ijoomlaupdate/".$update_xml, $update_dir."/".$update_xml)){
			echo 'Error copying ijoomlaupdate.xml'."<br/>";
		}
		if(!copy($component_dir."/ijoomlaupdate/".$update_php, $update_dir."/".$update_php)){
			echo 'Error copying ijoomlaupdate.php'."<br/>";
		}
		if(!is_dir($update_dir."/".$update_folder)){
			mkdir($update_dir."/".$update_folder, 0755);
		}
		if(!copy($component_dir."/ijoomlaupdate/".$update_folder."/editversions.php", $update_dir."/".$update_folder."/editversions.php")){
			echo 'Error copying editversions.php'."<br/>";
		}
		if(!copy($component_dir."/ijoomlaupdate/".$update_folder."/ijoomla.gif", $update_dir."/".$update_folder."/ijoomla.gif")){
			echo 'Error copying ijoomla.gif'."<br/>";
		}
		if(!copy($component_dir."/ijoomlaupdate/".$update_folder."/logo.png", $update_dir."/".$update_folder."/logo.png")){
			echo 'Error copying logo.png'."<br/>";
		}
		if(!copy($component_dir."/ijoomlaupdate/".$update_folder."/index.html", $update_dir."/".$update_folder."/index.html")){
			echo 'Error copying index.html'."<br/>";
		}
		@chmod($component_dir.'/ijoomlaupdate/'.$update_php, 0755);
		@chmod($component_dir.'/ijoomlaupdate/'.$update_xml, 0755);
		@chmod($component_dir.'/ijoomlaupdate/'.$update_folder, 0755);
		if(!unlink($component_dir.'/ijoomlaupdate/'.$update_php)){
			echo 'Cannot delete '.$component_dir.'/ijoomlaupdate/'.$update_php."<br/>";
		}
		if(!unlink($component_dir.'/ijoomlaupdate/'.$update_xml)){
			echo 'Cannot delete '.$component_dir.'/ijoomlaupdate/'.$update_xml."<br/>";
		}
		//----------------------------------------stop upgrade plugin
	}

	function random_password(){
		//generate a random password
		$pass = "";
		$salt = "abchefghjkmnpqrstuvwxyz0123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		while ($i <= 7) {
			$num = rand() % 33;
			$tmp = substr($salt, $num, 1);
			$pass = $pass . $tmp;
			$i++;
		}
		return $pass;
	}

	function add_plugins() {
		$db = &JFactory::getDBO();
		$sql = "SELECT `id` FROM #__ad_agency_plugins LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadColumn();
		if (!$res) {
			$sql = "INSERT INTO `#__ad_agency_plugins`
					   (`id`, `name`, `classname`, `value`, `filename`, `type`, `published`, `def`, `sandbox`, `reqhttps`, `display_name`)
					   VALUES
					   (1, 'paypal', 'paypal', '0', 'paypal_payment.php', 'payment', 1, 'default', 0, 0, 'PayPal');";
			$db->setQuery($sql);
			$db->query();
			$sql = "INSERT INTO `#__ad_agency_plugin_settings`
					   (`pluginid`, `setting`, `description`, `value`)
					   VALUES
					   (1, 'paypal_email', 'email of your paypal account', '')";
			$db->setQuery($sql);
			$db->query();
		}
	}

	function add_currencies() {
		$db = &JFactory::getDBO();
		$sql = "SELECT `id` FROM #__ad_agency_currencies WHERE `plugname` = 'paypal'  LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadColumn();
		if (!$res) {
			$sql = "
			INSERT INTO `#__ad_agency_currencies` (`id`, `plugname`, `currency_name`, `currency_full`) VALUES
			(1, 'paypal', 'USD', 'U.S. Dollar'),
			(2, 'paypal', 'AUD', 'Australian Dollar'),
			(3, 'paypal', 'CAD', 'Canadian Dollar'),
			(4, 'paypal', 'CHF', 'Swiss Franc'),
			(5, 'paypal', 'CZK', 'Czech Koruna'),
			(6, 'paypal', 'DKK', 'Danish Krone'),
			(7, 'paypal', 'EUR', 'Euro'),
			(8, 'paypal', 'GBP', 'Pound Sterling'),
			(9, 'paypal', 'HKD', 'Hong Kong Dollar'),
			(10, 'paypal', 'HUF', 'Hungarian Forint'),
			(11, 'paypal', 'JPY', 'Japanese Yen'),
			(12, 'paypal', 'NOK', 'Norwegian Krone'),
			(13, 'paypal', 'NZD', 'New Zeeland Dollar'),
			(14, 'paypal', 'PLN', 'Polish Zloty'),
			(15, 'paypal', 'SEK', 'Swedish Krona'),
			(16, 'paypal', 'SGD', 'Singapore Dollar'),
			(17, 'paypal', 'ZAR', 'South African Rand'),
			(18, 'paypal', 'BRL', 'Brazil Real');
			";
			$db->setQuery($sql);
			$db->query();
		}
	}

	function add_countries() {
		$db = JFactory::getDBO();
		$sql = "SELECT `id` FROM #__ad_agency_states LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadColumn();
		if (!$res) {
			$sql = "INSERT INTO `#__ad_agency_states` (`id`, `state`, `country`) VALUES
					(1, 'Afghanistan', 'Afghanistan'),
					(2, 'Algeria', 'Algeria'),
					(3, 'American-Samoa', 'American-Samoa'),
					(4, 'Andorra', 'Andorra'),
					(5, 'Angola', 'Angola'),
					(6, 'Anguilla', 'Anguilla'),
					(7, 'Antigua-and-Barbuda', 'Antigua-and-Barbuda'),
					(8, 'Argentina', 'Argentina'),
					(9, 'Armenia', 'Armenia'),
					(10, 'Ascension-Island', 'Ascension-Island'),
					(11, 'Australian Capital Territory', 'Australia'),
					(13, 'New South Wales', 'Australia'),
					(14, 'Northern Territory', 'Australia'),
					(15, 'Queensland', 'Australia'),
					(16, 'South Australia', 'Australia'),
					(17, 'Tasmania', 'Australia'),
					(18, 'Victoria', 'Australia'),
					(19, 'Western Australia', 'Australia'),
					(20, 'Austria', 'Austria'),
					(21, 'Azerbaijan', 'Azerbaijan'),
					(22, 'Bahamas', 'Bahamas'),
					(23, 'Bahrain', 'Bahrain'),
					(24, 'Bangladesh', 'Bangladesh'),
					(25, 'Barbados', 'Barbados'),
					(26, 'Belarus', 'Belarus'),
					(27, 'Belgium', 'Belgium'),
					(28, 'Belize', 'Belize'),
					(29, 'Benin', 'Benin'),
					(30, 'Bermuda', 'Bermuda'),
					(31, 'Bhutan', 'Bhutan'),
					(32, 'Bolivia', 'Bolivia'),
					(33, 'Bosnia-and-Herzegovina', 'Bosnia-and-Herzegovina'),
					(34, 'Botswana', 'Botswana'),
					(35, 'Brazil', 'Brazil'),
					(36, 'Sergipe', 'Brazil'),
					(37, 'British-Indian-Ocean-Territory', 'British-Indian-Ocean-Territory'),
					(38, 'Brunei-Darussalam', 'Brunei-Darussalam'),
					(39, 'Bulgaria', 'Bulgaria'),
					(40, 'Burkina-Faso', 'Burkina-Faso'),
					(41, 'Burundi', 'Burundi'),
					(42, 'Camaroon', 'Camaroon'),
					(43, 'Cambodia', 'Cambodia'),
					(44, 'Cameroon', 'Cameroon'),
					(45, 'Alberta', 'Canada'),
					(46, 'British Columbia', 'Canada'),
					(47, 'Manitoba', 'Canada'),
					(48, 'New Brunswick', 'Canada'),
					(49, 'Newfoundland', 'Canada'),
					(50, 'Northwest Territories', 'Canada'),
					(51, 'Nova Scotia', 'Canada'),
					(52, 'Nunavut', 'Canada'),
					(53, 'Ontario', 'Canada'),
					(54, 'Prince Edward Island', 'Canada'),
					(55, 'Quebec', 'Canada'),
					(56, 'Saskatchewan', 'Canada'),
					(57, 'Yukon', 'Canada'),
					(58, 'Cape-Verde', 'Cape-Verde'),
					(59, 'Cayman-Islands', 'Cayman-Islands'),
					(60, 'Central-African-Republic', 'Central-African-Republic'),
					(61, 'Chad', 'Chad'),
					(62, 'Chile', 'Chile'),
					(63, 'Anhui', 'China'),
					(64, 'Beijing', 'China'),
					(65, 'China', 'China'),
					(66, 'Fujian', 'China'),
					(67, 'Gansu', 'China'),
					(68, 'Guangdong', 'China'),
					(69, 'Guangxi', 'China'),
					(70, 'Guizhou', 'China'),
					(71, 'Hebei', 'China'),
					(72, 'Heilongjiang', 'China'),
					(73, 'Henan', 'China'),
					(74, 'Hong-Kong', 'China'),
					(75, 'Hubei', 'China'),
					(76, 'Hunan', 'China'),
					(77, 'Jiangsu', 'China'),
					(78, 'Jiangxi', 'China'),
					(79, 'Jilin', 'China'),
					(80, 'Liaoning', 'China'),
					(81, 'Macau', 'China'),
					(82, 'Nei Mongol', 'China'),
					(83, 'Ningxia', 'China'),
					(84, 'Quinghai', 'China'),
					(85, 'Shaanxi', 'China'),
					(86, 'Shandong', 'China'),
					(87, 'Shanghai', 'China'),
					(88, 'Shanxi', 'China'),
					(89, 'Sichuan', 'China'),
					(90, 'Taiwan', 'China'),
					(91, 'Tianjin', 'China'),
					(92, 'Xinjiang', 'China'),
					(93, 'Xizang', 'China'),
					(94, 'Yunnan', 'China'),
					(95, 'Zhejiang', 'China'),
					(96, 'Colombia', 'Colombia'),
					(97, 'Comoros', 'Comoros'),
					(98, 'Congo', 'Congo'),
					(99, 'Aitutaki', 'Cook-Islands'),
					(100, 'Atiu', 'Cook-Islands'),
					(101, 'Mangaia', 'Cook-Islands'),
					(102, 'Manuae', 'Cook-Islands'),
					(103, 'Mauke', 'Cook-Islands'),
					(104, 'Mitiaro', 'Cook-Islands'),
					(105, 'Palmerston', 'Cook-Islands'),
					(106, 'Rarotonga', 'Cook-Islands'),
					(107, 'Suwarrow', 'Cook-Islands'),
					(108, 'Tatutea', 'Cook-Islands'),
					(109, 'Costa-Rica', 'Costa-Rica'),
					(110, 'Cote-D-Ivoire', 'Cote-D-Ivoire'),
					(111, 'Croatia', 'Croatia'),
					(112, 'Cuba', 'Cuba'),
					(113, 'Cyprus', 'Cyprus'),
					(114, 'Czech-Republic', 'Czech-Republic'),
					(115, 'Denmark', 'Denmark'),
					(116, 'Djibouti', 'Djibouti'),
					(117, 'Dominica', 'Dominica'),
					(118, 'Dominican-Republic', 'Dominican-Republic'),
					(119, 'Ecuador', 'Ecuador'),
					(120, 'Egypt', 'Egypt'),
					(121, 'El-Salvador', 'El-Salvador'),
					(122, 'Equatorial-Guinea', 'Equatorial-Guinea'),
					(123, 'Eritrea', 'Eritrea'),
					(124, 'Estonia', 'Estonia'),
					(125, 'Ethiopia', 'Ethiopia'),
					(126, 'Falkland-Islands', 'Falkland-Islands'),
					(127, 'Faroe-Islands', 'Faroe-Islands'),
					(128, 'Federated-States-of-Micronesia', 'Federated-States-of-Micronesia'),
					(129, 'Kandavu', 'Fiji'),
					(130, 'Lau Group', 'Fiji'),
					(131, 'Ono-I-Lau', 'Fiji'),
					(132, 'Rotuma', 'Fiji'),
					(133, 'Taveuni', 'Fiji'),
					(134, 'Vanua Levu', 'Fiji'),
					(135, 'Vanua Mbalavu', 'Fiji'),
					(136, 'Vita Levu', 'Fiji'),
					(137, 'Finland', 'Finland'),
					(138, 'Hame', 'Finland'),
					(139, 'France', 'France'),
					(140, 'French-Guiana', 'French-Guiana'),
					(141, 'Ahunui Island', 'French-Polynesia'),
					(142, 'Amanu Island', 'French-Polynesia'),
					(143, 'Anaa Island', 'French-Polynesia'),
					(144, 'Bellingshausen Island', 'French-Polynesia'),
					(145, 'Bora-Bora Island', 'French-Polynesia'),
					(146, 'Fakarava Island', 'French-Polynesia'),
					(147, 'Hao Island', 'French-Polynesia'),
					(148, 'Haraiki Island', 'French-Polynesia'),
					(149, 'Hikueru Island', 'French-Polynesia'),
					(150, 'Hiva Oa Island', 'French-Polynesia'),
					(151, 'Kaukura Island', 'French-Polynesia'),
					(152, 'Makatea Island', 'French-Polynesia'),
					(153, 'Makemo Island', 'French-Polynesia'),
					(154, 'Manuhangi Island', 'French-Polynesia'),
					(155, 'Maria Island', 'French-Polynesia'),
					(156, 'Marutea Island', 'French-Polynesia'),
					(157, 'Mataiva Island', 'French-Polynesia'),
					(158, 'Mehetia Island', 'French-Polynesia'),
					(159, 'Moorea Island', 'French-Polynesia'),
					(160, 'Mopelia Island', 'French-Polynesia'),
					(161, 'Nengonengo Island', 'French-Polynesia'),
					(162, 'Raevavae Island', 'French-Polynesia'),
					(163, 'Raiatea Island', 'French-Polynesia'),
					(164, 'Rapa Island', 'French-Polynesia'),
					(165, 'Raraka Island', 'French-Polynesia'),
					(166, 'Raroia Island', 'French-Polynesia'),
					(167, 'Ravahere Island', 'French-Polynesia'),
					(168, 'Rimatara Island', 'French-Polynesia'),
					(169, 'Rurutu Island', 'French-Polynesia'),
					(170, 'Scilly Island', 'French-Polynesia'),
					(171, 'Tahaa Island', 'French-Polynesia'),
					(172, 'Tahiti Island', 'French-Polynesia'),
					(173, 'Tahuata Island', 'French-Polynesia'),
					(174, 'Takapoto Island', 'French-Polynesia'),
					(175, 'Tematangi Island', 'French-Polynesia'),
					(176, 'Tetiaroa Island', 'French-Polynesia'),
					(177, 'Tikei Island', 'French-Polynesia'),
					(178, 'Vanavana Island', 'French-Polynesia'),
					(179, 'Gabon', 'Gabon'),
					(180, 'Georgia', 'Georgia'),
					(181, 'Bayern', 'Germany'),
					(182, 'Brandenburg', 'Germany'),
					(183, 'Bremen', 'Germany'),
					(184, 'Germany', 'Germany'),
					(185, 'Nordrhein-Westfalen', 'Germany'),
					(186, 'Rheinland-Pfalz', 'Germany'),
					(187, 'Sachsen', 'Germany'),
					(188, 'Sachsen-Anhalt', 'Germany'),
					(189, 'Thuringen', 'Germany'),

					(190, 'Ghana', 'Ghana'),
					(191, 'Gibralter', 'Gibralter'),
					(192, 'Greece', 'Greece'),

					(193, 'Greenland', 'Greenland'),
					(194, 'Grenada', 'Grenada'),
					(195, 'Guadeloupe', 'Guadeloupe'),
					(196, 'Guam', 'Guam'),
					(197, 'Guatemala', 'Guatemala'),
					(198, 'Guinea', 'Guinea'),
					(199, 'Guinea-Bissau', 'Guinea-Bissau'),
					(200, 'Guyana', 'Guyana'),
					(201, 'Haiti', 'Haiti'),
					(202, 'Honduras', 'Honduras'),
					(203, 'Hong-Kong', 'Hong-Kong'),
					(204, 'Hungary', 'Hungary'),
					(205, 'Iceland', 'Iceland'),
					(206, 'Bass', 'Ilots'),
					(207, 'de Gdansk (Danzig)', 'Ind. before 1939'),
					(208, 'Andhra Pradesh', 'India'),
					(209, 'Delhi', 'India'),
					(210, 'Gujarat', 'India'),
					(211, 'Haryana', 'India'),
					(212, 'India', 'India'),
					(213, 'Karnataka', 'India'),
					(214, 'Kerala', 'India'),
					(215, 'Madhya Pradesh', 'India'),
					(216, 'Maharashtra', 'India'),
					(217, 'Orissa', 'India'),
					(218, 'Punjab', 'India'),
					(219, 'Rajastan', 'India'),
					(220, 'Tamil Nadu', 'India'),
					(221, 'Uttar Pradesh', 'India'),
					(222, 'West Bengal', 'India'),
					(223, 'Bali', 'Indonesia'),
					(224, 'Bangka', 'Indonesia'),
					(225, 'Belitung', 'Indonesia'),
					(226, 'Borneo (Kalimantan)', 'Indonesia'),
					(227, 'Buru', 'Indonesia'),
					(228, 'Flores', 'Indonesia'),
					(229, 'Halmahera', 'Indonesia'),
					(230, 'Indonesia', 'Indonesia'),
					(231, 'Jawa (Java)', 'Indonesia'),
					(232, 'Kabaena', 'Indonesia'),
					(233, 'Kepulauan Alor (Pulau Alor)', 'Indonesia'),
					(234, 'Kepulauan Anambas', 'Indonesia'),
					(235, 'Kepulauan Aru', 'Indonesia'),
					(236, 'Kepulauan Banda', 'Indonesia'),
					(237, 'Kepulauan Banggai', 'Indonesia'),
					(238, 'Kepulauan Batu', 'Indonesia'),
					(239, 'Kepulauan Bowokan', 'Indonesia'),
					(240, 'Kepulauan Bunguran Selatan', 'Indonesia'),
					(241, 'Kepulauan Bunguran Utara', 'Indonesia'),
					(242, 'Kepulauan Kai', 'Indonesia'),
					(243, 'Kepulauan Kangean', 'Indonesia'),
					(244, 'Kepulauan Leti', 'Indonesia'),
					(245, 'Kepulauan Lingga', 'Indonesia'),
					(246, 'Kepulauan Mentawai', 'Indonesia'),
					(247, 'Kepulauan Sangihe', 'Indonesia'),
					(248, 'Kepulauan Schouten', 'Indonesia'),
					(249, 'Kepulauan Tanimbar', 'Indonesia'),
					(250, 'Kepulauan Togian', 'Indonesia'),
					(251, 'Kepulauan Tukangbesi', 'Indonesia'),
					(252, 'Lombok', 'Indonesia'),
					(253, 'Madura', 'Indonesia'),
					(254, 'Maluku', 'Indonesia'),
					(255, 'Muna', 'Indonesia'),
					(256, 'New Guinea (Irian Jaya)', 'Indonesia'),
					(257, 'Pulau Babar', 'Indonesia'),
					(258, 'Pulau Bacan', 'Indonesia'),
					(259, 'Pulau Bawean', 'Indonesia'),
					(260, 'Pulau Bengkalis', 'Indonesia'),
					(261, 'Pulau Bintan', 'Indonesia'),
					(262, 'Pulau Butung', 'Indonesia'),
					(263, 'Pulau Damar', 'Indonesia'),
					(264, 'Pulau Enggano', 'Indonesia'),
					(265, 'Pulau Kalao', 'Indonesia'),
					(266, 'Pulau Kundur', 'Indonesia'),
					(267, 'Pulau Laut', 'Indonesia'),
					(268, 'Pulau Lomblen', 'Indonesia'),
					(269, 'Pulau Misool', 'Indonesia'),
					(270, 'Pulau Nias', 'Indonesia'),
					(271, 'Pulau Obi', 'Indonesia'),
					(272, 'Pulau Padang', 'Indonesia'),
					(273, 'Pulau Pantar', 'Indonesia'),
					(274, 'Pulau Rangsang', 'Indonesia'),
					(275, 'Pulau Rupat', 'Indonesia'),
					(276, 'Pulau Sebangka', 'Indonesia'),
					(277, 'Pulau Seleyar (Salayar)', 'Indonesia'),
					(278, 'Pulau Siberut', 'Indonesia'),
					(279, 'Pulau Simeulue', 'Indonesia'),
					(280, 'Pulau Singkep', 'Indonesia'),
					(281, 'Pulau Tanahjampea', 'Indonesia'),
					(282, 'Pulau Tebingtinggi', 'Indonesia'),
					(283, 'Pulau Waigeo', 'Indonesia'),
					(284, 'Pulau Wowoni', 'Indonesia'),
					(285, 'Pulau Yapen', 'Indonesia'),
					(286, 'Salawati', 'Indonesia'),
					(287, 'Seram (Ceram)', 'Indonesia'),
					(288, 'Sulawesi (Celebes)', 'Indonesia'),
					(289, 'Sumatera (Sumatra)', 'Indonesia'),
					(290, 'Sumba', 'Indonesia'),
					(291, 'Sumbawa', 'Indonesia'),
					(292, 'Timor (Timur)', 'Indonesia'),
					(293, 'Iran', 'Iran'),
					(294, 'Iraq', 'Iraq'),
					(295, 'Ireland', 'Ireland'),
					(296, 'Isle-of-Man', 'Isle-of-Man'),
					(297, 'Golan Heights', 'Israel'),
					(298, 'Israel', 'Israel'),
					(299, 'Italy', 'Italy'),
					(300, 'Jamaica', 'Jamaica'),
					(301, 'Japan', 'Japan'),
					(302, 'Mie', 'Japan'),
					(303, 'Shizuoka', 'Japan'),
					(304, 'Toyama', 'Japan'),
					(305, 'Jordan', 'Jordan'),
					(306, 'Kazakhstan', 'Kazakhstan'),
					(307, 'Kenya', 'Kenya'),
					(308, 'Abemama Island', 'Kiribati'),
					(309, 'Arorae Island', 'Kiribati'),
					(310, 'Banaba Island', 'Kiribati'),
					(311, 'Beru Island', 'Kiribati'),
					(312, 'Birnie Island', 'Kiribati'),
					(313, 'Canton Island', 'Kiribati'),
					(314, 'Caroline Atoll', 'Kiribati'),
					(315, 'Christmas Island', 'Kiribati'),
					(316, 'Enderbury Island', 'Kiribati'),
					(317, 'Fanning Island', 'Kiribati'),
					(318, 'Filippo Reef', 'Kiribati'),
					(319, 'Flint Island', 'Kiribati'),
					(320, 'Gardner Island', 'Kiribati'),
					(321, 'Hull Island', 'Kiribati'),
					(322, 'Kiritimati(Christmas) Is', 'Kiribati'),
					(323, 'Kuria Island', 'Kiribati'),
					(324, 'Makin Island', 'Kiribati'),
					(325, 'Malden Island', 'Kiribati'),
					(326, 'Merlin Seamount', 'Kiribati'),
					(327, 'Nikunau Island', 'Kiribati'),
					(328, 'Nonouti Island', 'Kiribati'),
					(329, 'Onotoa Island', 'Kiribati'),
					(330, 'Phoenix Island', 'Kiribati'),
					(331, 'Starbuck Island', 'Kiribati'),
					(332, 'Sydney Island', 'Kiribati'),
					(333, 'Tabiteuea Island', 'Kiribati'),
					(334, 'Tamana Island', 'Kiribati'),
					(335, 'Tapeteuea Island', 'Kiribati'),
					(336, 'Tarawa Island', 'Kiribati'),
					(337, 'Vostok Island', 'Kiribati'),
					(338, 'Washington Island', 'Kiribati'),
					(339, 'Korea-(Peoples-Republic-of)', 'Korea-(Peoples-Republic-of)'),
					(340, 'Korea-(Republic-of)', 'Korea-(Republic-of)'),
					(341, 'Kuwait', 'Kuwait'),
					(342, 'Kyrgyzstan', 'Kyrgyzstan'),
					(343, 'Laos', 'Laos'),
					(344, 'Latvia', 'Latvia'),
					(345, 'Lebanon', 'Lebanon'),
					(346, 'Lesotho', 'Lesotho'),
					(347, 'Liberia', 'Liberia'),
					(348, 'Libya', 'Libya'),
					(349, 'Liechtenstein', 'Liechtenstein'),
					(350, 'Lithuania', 'Lithuania'),
					(351, 'Luxembourg', 'Luxembourg'),
					(352, 'Macau', 'Macau'),
					(353, 'Macedonia', 'Macedonia'),
					(354, 'Madagascar', 'Madagascar'),
					(355, 'Malawi', 'Malawi'),
					(356, 'Labuan', 'Malaysia'),
					(357, 'Malaya', 'Malaysia'),
					(358, 'Malaysia', 'Malaysia'),
					(359, 'Sarawak', 'Malaysia'),
					(360, 'Maldives', 'Maldives'),
					(361, 'Mali', 'Mali'),
					(362, 'Malta', 'Malta'),
					(363, 'Marshall-Islands', 'Marshall-Islands'),
					(364, 'Martinique', 'Martinique'),
					(365, 'Mauritius', 'Mauritius'),
					(366, 'Mayotte', 'Mayotte'),
					(367, 'Aguascalientes', 'Mexico'),
					(368, 'Baja California Norte', 'Mexico'),
					(369, 'Baja California Sur', 'Mexico'),
					(370, 'Campeche', 'Mexico'),
					(371, 'Chiapas', 'Mexico'),
					(372, 'Chihuahua', 'Mexico'),
					(373, 'Coahuila', 'Mexico'),
					(374, 'Coalima', 'Mexico'),
					(375, 'District Federal', 'Mexico'),
					(376, 'Durango', 'Mexico'),
					(377, 'Guanajuato', 'Mexico'),
					(378, 'Guerrero', 'Mexico'),
					(379, 'Hidalgo', 'Mexico'),
					(380, 'Jalisco', 'Mexico'),
					(381, 'Mexico', 'Mexico'),
					(382, 'Morelos', 'Mexico'),
					(383, 'Nayarit', 'Mexico'),
					(384, 'Oaxaca', 'Mexico'),
					(385, 'Puebla', 'Mexico'),
					(386, 'Quintana Roo', 'Mexico'),
					(387, 'Sinaloa', 'Mexico'),
					(388, 'Sonora', 'Mexico'),
					(389, 'Tabasco', 'Mexico'),
					(390, 'Tamaulipas', 'Mexico'),
					(391, 'Tlaxcala', 'Mexico'),
					(392, 'Veracruz', 'Mexico'),
					(393, 'Yucatan', 'Mexico'),
					(394, 'Zacatecas', 'Mexico'),
					(395, 'Moldavia', 'Moldavia'),
					(396, 'Monaco', 'Monaco'),
					(397, 'Mongolia', 'Mongolia'),
					(398, 'Montenegro', 'Montenegro'),
					(399, 'Montserrat', 'Montserrat'),
					(400, 'Morocco', 'Morocco'),
					(401, 'Sidi Ifni (since 1969)', 'Morocco'),
					(402, 'Spanish Morocco', 'Morocco'),
					(403, 'Tangier (since 1956)', 'Morocco'),
					(404, 'Mozambique', 'Mozambique'),
					(405, 'Myanmar', 'Myanmar'),
					(406, 'Namibia', 'Namibia'),
					(407, 'Nauru', 'Nauru'),
					(408, 'Nepal', 'Nepal'),
					(409, 'Netherlands', 'Netherlands'),
					(410, 'Aruba Island', 'Netherlands-Antilles'),
					(411, 'Bonaire Island', 'Netherlands-Antilles'),
					(412, 'Cura ao Island', 'Netherlands-Antilles'),
					(413, 'Saba Island', 'Netherlands-Antilles'),
					(414, 'Sint Maarten Island', 'Netherlands-Antilles'),
					(415, 'Hunter Island', 'New-Caledonia'),
					(416, 'New Caledonia Island', 'New-Caledonia'),
					(417, 'New-Caledonia', 'New-Caledonia'),
					(418, 'Arapawa Island', 'New-Zealand'),
					(419, 'Campbell Island', 'New-Zealand'),
					(420, 'New-Zealand', 'New-Zealand'),
					(421, 'Nicaragua', 'Nicaragua'),
					(422, 'Niger', 'Niger'),
					(423, 'Nigeria', 'Nigeria'),
					(424, 'Niue', 'Niue'),
					(425, 'Norfolk-Island', 'Norfolk-Island'),
					(426, 'Northern-Mariana-Islands', 'Northern-Mariana-Islands'),
					(427, 'Aker-Shus', 'Norway'),
					(428, 'Norway', 'Norway'),
					(429, 'Oman', 'Oman'),
					(430, 'Baluchistan', 'Pakistan'),
					(431, 'Nothern Areas', 'Pakistan'),
					(432, 'Pakistan', 'Pakistan'),
					(433, 'Palau', 'Palau'),
					(434, 'Panama', 'Panama'),
					(435, 'Bismark Islands', 'Papua-New-Guinea'),
					(436, 'Bougainville', 'Papua-New-Guinea'),
					(437, 'd Entrecasteaux Islands', 'Papua-New-Guinea'),
					(438, 'Feni Islands', 'Papua-New-Guinea'),
					(439, 'Green Islands', 'Papua-New-Guinea'),
					(440, 'Hermit Islands', 'Papua-New-Guinea'),
					(441, 'Kaniet Islands', 'Papua-New-Guinea'),
					(442, 'Lihir Islands', 'Papua-New-Guinea'),
					(443, 'Louisiade Islands', 'Papua-New-Guinea'),
					(444, 'Manus Island', 'Papua-New-Guinea'),
					(445, 'Mussau Island', 'Papua-New-Guinea'),
					(446, 'New Britain', 'Papua-New-Guinea'),
					(447, 'New Guinea', 'Papua-New-Guinea'),
					(448, 'New Hanover Island', 'Papua-New-Guinea'),
					(449, 'New Ireland', 'Papua-New-Guinea'),
					(450, 'Ninigo Islands', 'Papua-New-Guinea'),
					(451, 'Nuguria Islands', 'Papua-New-Guinea'),
					(452, 'Nukumanu Islands', 'Papua-New-Guinea'),
					(453, 'Rossel Island', 'Papua-New-Guinea'),
					(454, 'Tabar Islands', 'Papua-New-Guinea'),
					(455, 'Tagula Island', 'Papua-New-Guinea'),
					(456, 'Tanga Islands', 'Papua-New-Guinea'),
					(457, 'Tauu Islands', 'Papua-New-Guinea'),
					(458, 'Trobriand Island', 'Papua-New-Guinea'),
					(459, 'Woodlark Island', 'Papua-New-Guinea'),
					(460, 'Wuvulu Island', 'Papua-New-Guinea'),
					(461, 'Paraguay', 'Paraguay'),
					(462, 'Peru', 'Peru'),
					(463, 'Philippines', 'Philippines'),
					(464, 'Pitcairn', 'Pitcairn'),
					(465, 'Austrian before 1918', 'Poland'),
					(466, 'German (Prussian) before 1918', 'Poland'),
					(467, 'German (Prussian) before 1944', 'Poland'),
					(468, 'Poland', 'Poland'),
					(469, 'Azores', 'Portugal'),
					(470, 'Madeira Islands', 'Portugal'),
					(471, 'Mainland Portugal', 'Portugal'),
					(472, 'Portugal', 'Portugal'),
					(473, 'Puerto-Rico', 'Puerto-Rico'),
					(474, 'Qatar', 'Qatar'),
					(475, 'Reunion', 'Reunion'),
					(476, 'Romania', 'Romania'),
					(477, 'Asian RSFSR', 'Russian-Federation'),
					(478, 'European RSFSR', 'Russian-Federation'),
					(479, 'Kaliningrad area', 'Russian-Federation'),
					(480, 'Komandorskije Ostrova', 'Russian-Federation'),
					(481, 'Kuril Islands', 'Russian-Federation'),
					(482, 'Novaja Zeml area', 'Russian-Federation'),
					(483, 'Novosibirskije Ostrova', 'Russian-Federation'),
					(484, 'Ostrov Sachalin', 'Russian-Federation'),
					(485, 'Russian-Federation', 'Russian-Federation'),
					(486, 'Severnaja Zeml area', 'Russian-Federation'),
					(487, 'Rwanda', 'Rwanda'),
					(488, 'Bequia Island', 'Saint-Vincent-and-the-Grenadin'),
					(489, 'Canouan Island', 'Saint-Vincent-and-the-Grenadin'),
					(490, 'Carriacou Island', 'Saint-Vincent-and-the-Grenadin'),
					(491, 'Saint Vincent Island', 'Saint-Vincent-and-the-Grenadin'),
					(492, 'San-Marino', 'San-Marino'),
					(493, 'Sao-Tome-and-Principe', 'Sao-Tome-and-Principe'),
					(494, 'Saudi-Arabia', 'Saudi-Arabia'),
					(495, 'Senegal', 'Senegal'),
					(496, 'Serbia', 'Serbia'),
					(497, 'Seychelles', 'Seychelles'),
					(498, 'Sierra-Leone', 'Sierra-Leone'),
					(499, 'Singapore', 'Singapore'),
					(500, 'Slovakia', 'Slovakia'),
					(501, 'Slovenia', 'Slovenia'),
					(502, 'Choiseul', 'Solomon-Islands'),
					(503, 'Guadalcanal', 'Solomon-Islands'),
					(504, 'Malaita', 'Solomon-Islands'),
					(505, 'New Georgia', 'Solomon-Islands'),
					(506, 'San Cristobal', 'Solomon-Islands'),
					(507, 'Santa Isabel', 'Solomon-Islands'),
					(508, 'Somalia', 'Somalia'),
					(509, 'Eastern Cape', 'South-Africa'),
					(510, 'Kwazulu-Natal', 'South-Africa'),
					(511, 'Mpumalanga', 'South-Africa'),
					(512, 'South-Africa', 'South-Africa'),
					(513, 'South-Georgia', 'South-Georgia'),
					(514, 'Balearic Islands', 'Spain'),
					(515, 'Canary Islands', 'Spain'),
					(516, 'Mainland', 'Spain'),
					(517, 'Spain', 'Spain'),
					(518, 'Spanish Morocco', 'Spain'),
					(519, 'Sri-Lanka', 'Sri-Lanka'),
					(520, 'St.-Kitts-and-Nevis', 'St.-Kitts-and-Nevis'),
					(521, 'St.-Lucia', 'St.-Lucia'),
					(522, 'St.-Pierre-and-Miquelon', 'St.-Pierre-and-Miquelon'),
					(523, 'Sudan', 'Sudan'),
					(524, 'Suriname', 'Suriname'),
					(525, 'Swaziland', 'Swaziland'),
					(526, 'Sweden', 'Sweden'),
					(527, 'Switzerland', 'Switzerland'),
					(528, 'Syrian-Arab-Republic', 'Syrian-Arab-Republic'),
					(529, 'Taiwan', 'Taiwan'),
					(530, 'Tajikistan', 'Tajikistan'),
					(531, 'Tanzania', 'Tanzania'),
					(532, 'Thailand', 'Thailand'),
					(533, 'The-Gambia', 'The-Gambia'),
					(534, 'Togo', 'Togo'),
					(535, 'Tokelau', 'Tokelau'),
					(536, 'Ata Island', 'Tonga'),
					(537, 'Eua Island', 'Tonga'),
					(538, 'Tafahi Island', 'Tonga'),
					(539, 'Toku Island', 'Tonga'),
					(540, 'Tongatapu Islands', 'Tonga'),
					(541, 'Vava u Islands', 'Tonga'),
					(542, 'Tobago', 'Trinidad-and-Tobago'),
					(543, 'Trinidad', 'Trinidad-and-Tobago'),
					(544, 'Trinidad-and-Tobago', 'Trinidad-and-Tobago'),
					(545, 'Tunisia', 'Tunisia'),
					(546, 'Asian', 'Turkey'),
					(547, 'European', 'Turkey'),
					(548, 'Turkey', 'Turkey'),
					(549, 'Turkmenistan', 'Turkmenistan'),
					(550, 'Turks-and-Caicos-Islands', 'Turks-and-Caicos-Islands'),
					(551, 'Tuvalu', 'Tuvalu'),
					(552, 'Uganda', 'Uganda'),
					(553, 'Ukraine', 'Ukraine'),
					(554, 'United-Arab-Emirates', 'United-Arab-Emirates'),
					(555, 'England', 'United-Kingdom'),
					(556, 'Essex', 'United-Kingdom'),
					(557, 'Guernsey', 'United-Kingdom'),
					(558, 'Isle of Man', 'United-Kingdom'),
					(559, 'Jersey', 'United-Kingdom'),
					(560, 'Northern-Ireland', 'United-Kingdom'),
					(561, 'Scotland', 'United-Kingdom'),
					(562, 'Wales', 'United-Kingdom'),
					(563, 'Alabama', 'United-States'),
					(564, 'Alaska', 'United-States'),
					(565, 'Arizona', 'United-States'),
					(566, 'Arkansas', 'United-States'),
					(567, 'California', 'United-States'),
					(568, 'Colorado', 'United-States'),
					(569, 'Connecticut', 'United-States'),
					(570, 'Delaware', 'United-States'),
					(571, 'District of Columbia', 'United-States'),
					(572, 'Florida', 'United-States'),
					(573, 'Georgia', 'United-States'),
					(574, 'Hawaii', 'United-States'),
					(575, 'Idaho', 'United-States'),
					(576, 'Illinois', 'United-States'),
					(577, 'InRoss', 'United-States'),
					(578, 'Iowa', 'United-States'),
					(579, 'Kansas', 'United-States'),
					(580, 'Kentucky', 'United-States'),
					(581, 'Louisiana', 'United-States'),
					(582, 'Maine', 'United-States'),
					(583, 'Maryland', 'United-States'),
					(584, 'Massachusetts', 'United-States'),
					(585, 'Michigan', 'United-States'),
					(586, 'Minnesota', 'United-States'),
					(587, 'Mississippi', 'United-States'),
					(588, 'Missouri', 'United-States'),
					(589, 'Montana', 'United-States'),
					(590, 'Nebraska', 'United-States'),
					(591, 'Nevada', 'United-States'),
					(592, 'New Hampshire', 'United-States'),
					(593, 'New Jersey', 'United-States'),
					(594, 'New Mexico', 'United-States'),
					(595, 'New York', 'United-States'),
					(596, 'North Carolina', 'United-States'),
					(597, 'North Dakota', 'United-States'),
					(598, 'Ohio', 'United-States'),
					(599, 'Oklahoma', 'United-States'),
					(600, 'Oregon', 'United-States'),
					(601, 'Pennsylvania', 'United-States'),
					(602, 'Rhode Island', 'United-States'),
					(603, 'South Carolina', 'United-States'),
					(604, 'South Dakota', 'United-States'),
					(605, 'Tennessee', 'United-States'),
					(606, 'Texas', 'United-States'),
					(607, 'United-States', 'United-States'),
					(608, 'Utah', 'United-States'),
					(609, 'Vermont', 'United-States'),
					(610, 'Virginia', 'United-States'),
					(611, 'Washington', 'United-States'),
					(612, 'West Virginia', 'United-States'),
					(613, 'Wisconsin', 'United-States'),
					(614, 'Wyoming', 'United-States'),
					(615, 'Uruguay', 'Uruguay'),
					(616, 'Uzbekistan', 'Uzbekistan'),
					(617, 'Ambrim Island', 'Vanuatu'),
					(618, 'Aneityum Island', 'Vanuatu'),
					(619, 'Efate Island', 'Vanuatu'),
					(620, 'Epi Island', 'Vanuatu'),
					(621, 'Eromanga Island', 'Vanuatu'),
					(622, 'Espritu Santo Island', 'Vanuatu'),
					(623, 'Maewo Island', 'Vanuatu'),
					(624, 'Malekula Island', 'Vanuatu'),
					(625, 'Oba Island', 'Vanuatu'),
					(626, 'Pentecost Island', 'Vanuatu'),
					(627, 'Santa Maria Island', 'Vanuatu'),
					(628, 'Tana Island', 'Vanuatu'),
					(629, 'Torres Islands', 'Vanuatu'),
					(630, 'Vanua Lava Island', 'Vanuatu'),
					(631, 'Vanuatu', 'Vanuatu'),
					(632, 'Venezuela', 'Venezuela'),
					(633, 'North Vietnam', 'Viet-Nam'),
					(634, 'South Vietnam', 'Viet-Nam'),
					(635, 'Viet-Nam', 'Viet-Nam'),
					(636, 'Virgin-Islands-(U.K.)', 'Virgin-Islands-(U.K.)'),
					(637, 'Virgin-Islands-(U.S.)', 'Virgin-Islands-(U.S.)'),
					(638, 'Wallis-and-Futanu-Islands', 'Wallis-and-Futanu-Islands'),
					(639, 'Savai i', 'Western-Samoa'),
					(640, 'Upolu', 'Western-Samoa'),
					(641, 'Yemen', 'Yemen'),
					(642, 'Yugoslavia', 'Yugoslavia'),
					(643, 'Zaire', 'Zaire'),
					(644, 'Zambia', 'Zambia'),
					(645, 'Zimbabwe', 'Zimbabwe')";
			$db->setQuery($sql);
			$db->query();
		}
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent){
		//$db 	= JFactory::getDBO(); 
		//$query	= $db->getQuery(true);	

		// $parent is the class calling this method
		//echo '<p>' . JText::_('COM_ALTACOACH_UNINSTALL_TEXT') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		$this->install();
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_('COM_ALTACOACH_PREFLIGHT_' . $type . '_TEXT') . '</p>';
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		// $parent is the class calling this method
		// $type is the type of change (install, update or discover_install)
		//echo '<p>' . JText::_('COM_ALTACOACH_POSTFLIGHT_' . $type . '_TEXT') . '</p>';
		
		$app = JFactory::getApplication("admin");
		$app->redirect(JURI::root().'administrator/index.php?option=com_adagency&installer=1');
	}
}