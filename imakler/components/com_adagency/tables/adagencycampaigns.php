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

class TableadagencyCampaigns extends JTable {
	var $id = null;
	var $aid = null;
	var $name = null;
	var $notes = null;
	var $default = null;
	var $start_date = null;
	var $type = null;
	var $quantity = null;
	var $validity = null;
	var $cost = null;
	var $otid = null;
	var $approved = null;
	var $status = null;
	var $exp_notice = null;
	var $key = null; 
    var $params = null;
	var $renewcmp = null;

	function TableadagencyCampaigns (&$db) {
		parent::__construct('#__ad_agency_campaign', 'id', $db);
	}

};


?>