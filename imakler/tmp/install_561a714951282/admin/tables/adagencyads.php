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

class TableadagencyAds extends JTable {
	var $id = null;
	var $advertiser_id = null;
	var $title = null;
	var $description = null;
	var $media_type = null;
	var $image_url = null;
	var $swf_url = null;
	var $target_url = null;
	var $width = null;
	var $height = null;
	var $ad_code = null;
	var $use_ad_code_in_netscape = null;
	var $ad_code_netscape = null;
	var $parameters = null;
	var $approved = null;
	var $zone = null;
	var $frequency = null;
	var $created = null;
	var $ordering = null;
	var $keywords = null;
	var $key = null;
	var $channel_id = null;
	var $ad_start_date = null;
	var $ad_end_date = null;
	var $checked_out = null;
	var $image_content = null;
	var $ad_headline = null;
	var $ad_text = null;
	
	function TableadagencyAds (&$db) {
		parent::__construct('#__ad_agency_banners', 'id', $db);
	}

	function load($id = 0, $reset = true) {
		parent::load($id,true);
		
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__ad_agency_banners set `ordering`=".intval($lft_array[$key])." where `id`=".intval($id);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}

};

?>