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


class TableadagencyPromocodes extends JTable {
	var $id = null;
	var $title = null;
	var $code = null;
	var $codelimit = null;
	var $amount = null;
	var $codestart = null;
	var $codeend = null;
	var $forexisting = null;
	var $published = null;
	var $aftertax = null;
	var $promotype = null;
	var $used = null;
	var $ordering = null;
	var $checked_out = null;
	var $checked_out_time = null;

	function TableadagencyPromocodes (&$db) {
		parent::__construct('#__ad_agency_promocodes', 'id', $db);
	}


	function store ($updateNulls = false) {
		if ((int)$this->codeend != 0 ) {
//			$end_date = parse_date($dat);
		} else {
			
			$this->codeend = 0;	
		}
		if (!parent::store(false)) return false;
		return true;
		
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__ad_agency_promocodes set `ordering`=".intval($lft_array[$key])." where `id`=".intval($id);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}
};


?>
