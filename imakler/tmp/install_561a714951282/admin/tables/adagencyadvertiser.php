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
	var $apr_ads = null;
	var $apr_cmp = null;
	var $key = null;
	var $ordering = null;
	var $checked_out = null;

	function TableadagencyAdvertiser (&$db) {
		parent::__construct('#__ad_agency_advertis', 'aid', $db);
	}

	function load ($id = 0, $reset = true) {
		$db = JFactory::getDBO();
		$sql = "select aid from #__ad_agency_advertis where user_id='".intval($id)."'";
		$db->setQuery($sql);
		$realid = $db->loadResult();
		if (isset($_POST['cid'])) $realid = $id;
		parent::load($realid, true);
	}

	function store($updateNulls = false){ 
		$db = JFactory::getDBO(); 
		parent::store(false);
		return true;
	}
	
	public function saveorder($idArray = null, $lft_array = null){
		if(isset($idArray) && isset($lft_array)){
			$query = $this->_db->getQuery(true);
			$db = JFactory::getDBO();
			foreach($idArray as $key=>$id){
				$sql = "update #__ad_agency_advertis set `ordering`=".intval($lft_array[$key])." where `aid`=".intval($id);
				$db->setQuery($sql);
				$db->query();
			}
		}
	}

};
?>