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
jimport ('joomla.application.component.controller');

class adagencyController extends JControllerLegacy {
	var $_customer = null;
	function __construct() {
		parent::__construct();
		$this->addMetaTitle();
		$this->checkCampaignsExpiration();
	}

	function display ($cachable = false, $urlparams = Array()) {
		parent::display(false, null);	
	}

	function setclick($msg = '') {
	}
	
	function checkCampaignsExpiration(){
		$db = JFactory::getDBO();
		$date_today = date("Y-m-d");
		
		$sql = "select `last_check_date` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$last_check_date = $db->loadResult(); 
		if($last_check_date != $date_today." 00:00:00"){
			$sql = "select id from #__ad_agency_campaign where `validity` like '".$date_today."%'";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadColumn();
			
			if(isset($result) && is_array($result) && count($result) > 0){
				$sql = "update #__ad_agency_campaign set `activities` = concat(activities, 'Expired - ".date("Y-m-d H:i:s")."', ';') where id in (".implode(",", $result).")";
				$db->setQuery($sql);
				$db->query();
			}
			
			$sql = "update #__ad_agency_settings set `last_check_date`='".$date_today." 00:00:00'";
			$db->setQuery($sql);
			$db->query();
		}
	}
	
	function addMetaTitle(){
		$controller = JRequest::getVar("controller", "");
		if(trim($controller) == ""){
			$controller = JRequest::getVar("view", "");
		}
		
		$task = JRequest::getVar("task", "");
		$doc = JFactory::getDocument();
		$meta_title = "";
		
		if($controller == "adagencyCPanel"){
			$meta_title = JText::_("ADAG_META_TITLE_CPANEL");
		}
		elseif($controller == "adagencyAdvertisers"){
			if($task == "edit"){
				$meta_title = JText::_("ADAG_META_TITLE_MY_PROFILE");
			}
			elseif($task == "overview"){
				$meta_title = JText::_("ADAG_META_TITLE_OVERVIEW");
			}
		}
		elseif($controller == "adagencyAds"){
			if($task == "addbanners"){
				$meta_title = JText::_("ADAG_META_TITLE_ADD_ADS");
			}
			else{
				$meta_title = JText::_("ADAG_META_TITLE_MY_ADS");
			}
		}
		elseif($controller == "adagencyOrders"){
			$meta_title = JText::_("ADAG_META_TITLE_MY_ORDERS");
		}
		elseif($controller == "adagencyStandard"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_STANDARD_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_STANDARD_AD");
			}
		}
		elseif($controller == "adagencyFlash"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_FLASH_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_FLASH_AD");
			}
		}
		elseif($controller == "adagencyAdcode"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_AFFILIATE_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_AFFILIATE_AD");
			}
		}
		elseif($controller == "adagencyPopup"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_POPUP_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_POPUP_AD");
			}
		}
		elseif($controller == "adagencyTextlink"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_TEXT_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_TEXT_AD");
			}
		}
		elseif($controller == "adagencyFloating"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_FLOATING_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_FLOATING_AD");
			}
		}
		elseif($controller == "adagencyTransition"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_TRANSITION_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_TRANSITION_AD");
			}
		}
		elseif($controller == "adagencyJomsocial"){
			$cid = JRequest::getVar("cid", array("0"), "get", "array");
			$cid = $cid["0"];
			
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_JOMSOCIAL_AD");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_JOMSOCIAL_AD");
			}
		}
		elseif($controller == "adagencyReports"){
			$meta_title = JText::_("ADAG_META_TITLE_REPORTS");
		}
		elseif($controller == "adagencyCampaigns"){
			$cid = JRequest::getVar("cid", "0");
			if($task == "edit" && intval($cid) == 0){
				$meta_title = JText::_("ADAG_META_TITLE_NEW_CAMPAIGNS");
			}
			elseif($task == "edit" && intval($cid) != 0){
				$meta_title = JText::_("ADAG_META_TITLE_EDIT_CAMPAIGNS");
			}
			else{
				$meta_title = JText::_("ADAG_META_TITLE_CAMPAIGNS");
			}
		}
		elseif($controller == "adagencyPackages" || $controller == "adagencyPackage" || $controller == "adagencypackage"){
			$meta_title = JText::_("ADAG_META_TITLE_PACKAGES");
		}
		
		if(trim($meta_title) != ""){
			$doc->setTitle($meta_title);
			$doc->setMetaData("title", $meta_title);
		}
	}
};
?>

