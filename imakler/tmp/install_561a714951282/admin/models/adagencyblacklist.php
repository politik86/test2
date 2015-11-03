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

jimport ("joomla.aplication.component.model");
require_once('components/com_adagency/helpers/legacy.php');
require_once("components/com_adagency/helpers/helper.php");

class adagencyAdminModeladagencyBlacklist extends JModelLegacy {
	
	function __construct () {
		parent::__construct();
	}
	
	function getBlackList(){
		$db = JFactory::getDBO();
		$sql = "select `blacklist` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$blacklist = $db->loadColumn();
		$blacklist = @$blacklist["0"];
		return $blacklist;
	}
	
	function save(){
		$db = JFactory::getDBO();
		$blacklist = JRequest::getVar("blacklist", "");
		$blacklist = str_replace("\n", "||", $blacklist);
		$sql = "update #__ad_agency_settings set `blacklist`='".addslashes(trim($blacklist))."'";
		$db->setQuery($sql);
		if($db->query()){
			return true;
		}
		return false;
	}
};

?>