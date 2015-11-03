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

class TableadagencyPackage extends JTable {
	var $tid = null;
	var $description = null;
	var $quantity = null;
	var $type = null;
	var $cost = null;
	var $validity = null;
	var $visibility = null;
	var $sid = null;
	var $zones = null;
	var $pack_description = null;

	function TableadagencyPackage (&$db) {
		parent::__construct('#__ad_agency_order_type', 'tid', $db);
	}

};


?>