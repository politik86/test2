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

class TableadagencyZone extends JTable {
	var $zoneid = null;
	var $banners = null;
	var $z_title = null;
	var $z_ordering = null;
	var $z_position = null;
	var $show_title = null;
	var $suffix = null;

	function TableadagencyZone (&$db) {
		parent::__construct('#__ad_agency_zone', 'zoneid', $db);
	}

};


?>
