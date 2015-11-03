CREATE TABLE IF NOT EXISTS `#__ad_agency_advertis` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_banners` (
  `id` int(11) NOT NULL auto_increment,
  `advertiser_id` int(11) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `description` varchar(255) default NULL,
  `media_type` enum('Standard','Advanced','Popup','Flash','TextLink','Transition','Floating') default 'Standard',
  `image_url` varchar(255) default NULL,
  `swf_url` varchar(255) default NULL,
  `target_url` varchar(255) default NULL,
  `width` smallint(5) unsigned default NULL,
  `height` smallint(5) unsigned default NULL,
  `ad_code` mediumtext,
  `use_ad_code_in_netscape` enum('N','Y') default 'N',
  `ad_code_netscape` mediumtext,
  `parameters` mediumtext,
  `approved` enum('Y','N','P') character set latin1 default 'P',
  `zone` int(11) NOT NULL default '0',
  `frequency` int(11) default NULL,
  `created` date default NULL,
  `ordering` int(8) NOT NULL default '0',
  `keywords` varchar(255) default NULL,
  `key` varchar(100) default NULL,
  `channel_id` int(11) default NULL,
  `ad_start_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ad_end_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `checked_out` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_campaign` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_campaign_banner` (
  `campaign_id` int(11) NOT NULL default '0',
  `banner_id` int(11) NOT NULL default '0',
  `relative_weighting` int(11) NOT NULL default '100',
  `thumb` varchar(255) default NULL,
  `zone` int(11) default NULL,
  PRIMARY KEY  (`campaign_id`,`banner_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_channels` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_channel_set` (
  `id` int(11) NOT NULL auto_increment,
  `channel_id` int(11) NOT NULL,
  `type` varchar(25) NOT NULL,
  `logical` varchar(25) NOT NULL,
  `option` varchar(25) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_currencies` (
  `id` int(11) NOT NULL auto_increment,
  `plugname` varchar(30) NOT NULL default '',
  `currency_name` varchar(20) NOT NULL default '',
  `currency_full` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_order` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_order_type` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_package_zone` (
  `package_id` int(11) NOT NULL,
  `zone_id` int(11) NOT NULL,
  PRIMARY KEY  (`package_id`,`zone_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_plugins` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_plugin_settings` (
  `pluginid` int(11) NOT NULL default '0',
  `setting` varchar(200) NOT NULL default '',
  `description` text NOT NULL,
  `value` text NOT NULL,
  KEY `pluginid` (`pluginid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_promocodes` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_settings` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_stat` (
  `entry_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ip_address` bigint(15) NOT NULL default '0',
  `advertiser_id` int(11) NOT NULL default '0',
  `campaign_id` int(11) NOT NULL default '0',
  `banner_id` int(11) NOT NULL default '0',
  `type` enum('click','impressions') NOT NULL default 'impressions',
  `how_many` int(9) NOT NULL default '0',
  KEY `idxip_address` (`ip_address`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_states` (
  `id` int(20) NOT NULL auto_increment,
  `state` varchar(30) NOT NULL default '',
  `country` varchar(30) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `#__ad_agency_zone` (
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
) ENGINE=MyISAM DEFAULT CHARSET=utf8;