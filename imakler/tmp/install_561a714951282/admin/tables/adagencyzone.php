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
	var $rotatebanners = null;
	var $rotating_time = null;
	var $rotaterandomize = null;
	var $show_adv_link = null;
	var $link_taketo = null;
	var $taketo_url = null;
	var $banners_cols = null;
	var $cellpadding = null;
	var $defaultad = null;
	var $keywords = null; 
	var $adparams = null;
	var $ignorestyle = null;
	var $textadparams = null;
	var $zone_text_below = null;
	var $zone_content_location = null;
	var $zone_content_visibility = null;
	var $checked_out = null;
	var $ordering = null;
	var $inventory_zone = null;

	function TableadagencyZone (&$db) {
		parent::__construct('#__ad_agency_zone', 'zoneid', $db);
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__ad_agency_zone set `ordering`=".intval($lft_array[$key])." where `zoneid`=".intval($id);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}

};

?>