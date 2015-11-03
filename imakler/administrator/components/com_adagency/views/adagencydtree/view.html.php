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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyDtree extends JViewLegacy {

	function showDtree ($tpl =  null ) {
		parent::display($tpl);
	}
	
	function getPendingAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_advertis a, #__users u where a.`user_id`=u.`id` and a.`approved` = 'P'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		return $count;
	}
	
	function getPendingAds(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_banners where `approved` = 'P'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		return $count;
	}
	
	function getPendingCampaigns(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_campaign where `approved` = 'P'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		return $count;
	}
	
	function getPendingPayments(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_order where `status` = 'not_paid'";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		return $count;
	}
}
?>