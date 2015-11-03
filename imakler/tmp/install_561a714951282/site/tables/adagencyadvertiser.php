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

class TableadagencyAdvertiser extends JTable {
	var $aid = null;
	var $user_id = null;
	var $company = null;
	var $description = null;
	var $website = null;
	var $address = null;
	var $country = null;
	var $city = null;
	var $state = null;
	var $zip = null;
	var $telephone = null;
	var $fax = null;
	var $logo = null;
	var $email_daily_report = null;
	var $email_weekly_report = null;
	var $email_month_report = null;
	var $email_campaign_expiration = null;
	var $approved = null;
	var $lastreport = null;
	var $weekreport = null;
	var $monthreport = null;
	var $paywith = null;
	var $show = null;
	var $mandatory = null;
	var $key = null;

	function TableadagencyAdvertiser (&$db) {
		parent::__construct('#__ad_agency_advertis', 'aid', $db);
	}

	function load ($id = 0) {
		$db = JFactory::getDBO();
		$sql = "select aid from #__ad_agency_advertis where user_id='".$id."'";
		//echo  $sql;die();
		$db->setQuery($sql);
		$realid = $db->loadResult();
		$post_cid = JRequest::getInt('cid');
		if (isset($post_cid)) $realid = $id;
		parent::load($realid);
	}

	function store(){ 
		$db = JFactory::getDBO(); 
		parent::store();
		return true;
	}

};


?>