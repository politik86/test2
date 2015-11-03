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


class Tableadagencygeo extends JTable {
	var $id = null;
	var $name = null;
	var $banner_id = null;
	var $advertiser_id = null;
	var $public = null;
	var $created = null;
	var $created_by = null;
	var $from = null;
	var $ordering = null;
	var $checked_out = null;
	
	function Tableadagencygeo (&$db) {
		parent::__construct('#__ad_agency_channels', 'id', $db);
	}

	function load($id = 0) {
		parent::load($id);
		
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__ad_agency_channels set `ordering`=".intval($lft_array[$key])." where `id`=".intval($id);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}

};

?>