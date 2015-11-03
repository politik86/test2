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

class TableadagencyPlugin extends JTable {
	var $id = null;
	var $name = null;
	var $classname = null;
	var $value = null;
	var $filename = null;
	var $type = null;
	var $published = null;
	var $def = null;
	var $sandbox = null;
	var $reqhttps = null;
	
	function TableadagencyPlugin (&$db) {
		parent::__construct('#__ad_agency_plugins', 'id', $db);
	}

	function store () {
		$res = parent::store();
		if (!$res) return $res;

		return true;		
	}

	
};


?>