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



class TableadagencyOrder extends JTable {
	var $oid = null;
	var $tid = null;
	var $aid = null;
	var $type = null;
	var $quantity = null;
	var $cost = null;
	var $order_date = null;
	var $payment_type = null;
	var $card_number = null;
	var $expiration = null;
	var $card_name = null;
	var $notes = null;
	var $status = null;
	var $pack_id = null;
	var $checked_out = null;
	var $ordering = null;

	function TableadagencyOrder (&$db) {
		parent::__construct('#__ad_agency_order', 'oid', $db);
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__ad_agency_order set `ordering`=".intval($lft_array[$key])." where `oid`=".intval($id);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}

};

?>