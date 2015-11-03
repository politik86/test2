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

class adagencyControlleradagencyAds extends adagencyController {
	var $_model = null;

	function __construct(){
		parent::__construct();

		$this->registerTask ("add", "edit","preview");
		$this->registerTask ("", "listAds");
        $this->registerTask ("default", "listAds");
		$this->registerTask ("remote_ad", "getRemoteAd");
		$this->registerTask ("unpublish", "publish");
		$this->registerTask ("loadflash", "loadflash");
		
		$this->_model = $this->getModel("adagencyAds");
	}

	function listAds() {
		global $mainframe;
		$view = $this->getView("adagencyAds", "html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		////////////////////////////////////
		$my	= JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE;
		$mosConfig_live_site = JURI::base();
		$database =  JFactory :: getDBO();
        $itemid = $model->getItemid('adagencyadvertiser');
        if($itemid != 0) { $Itemid = "&Itemid=" . intval($itemid); } else { $Itemid = NULL; }
		$link = "index.php?option=com_adagency" . $Itemid;
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();

		// Check if user is logged in
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" .$Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!isset($adv_id[0])){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" .$Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$shown = explode(";",$model->getConfigs()->show);
		foreach($shown as $element){
			if($element == "wizzard") { $isWizzard = 1; }
			if($element == "nwone") {$isBanners =1;}
		}

		$isWizzard = $model->isWizzard();

		// check if the user is not approved as an advertiser
		if(($adv_id[1]=='N')||(($adv_id[1]=='P')&&(!$isWizzard)&&(!$isBanners))){
			$mainframe->redirect($link, JText::_('AD_FAILEDAPPROVE'));
		} elseif (($adv_id[1]=='P')&&($isWizzard!=0)&&(!isset($_GET['w']))) {
			$_GET['w']=1;
		}
		//////////////////////////////////////
		$view->display();
	}

	function manage(){
		$key = JRequest::getVar('key','');
		$action = JRequest::getVar('action','');
		$cid = JRequest::getInt('cid', 0);
		if(($key!='')&&($action!='')&&($cid!=0)){
			$this->_model->manage($key,$action,$cid);
		} else {
			$this->setRedirect("index.php",'No variable');
		}
	}

	function getChannel() {
		$this->_model->getChannel();
		die();
	}

	function getChannelInfo() {
		$this->_model->getChannelInfo();
		die();
	}

	function getCampsByParams() {
		$this->_model->getCampsByParams();
		die();
	}

    function getCampLimInfo() {
        $aid = JRequest::getInt('aid', '0', 'get');
        echo json_encode($this->_model->getCampLimInfo($aid));
        die();
    }

	function addbanners(){
		////////////////////////////////////
		$my	= JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE;
		$mosConfig_live_site = JURI::base();
		$database = JFactory :: getDBO();
		$link="index.php";
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();
		$model = $this->getModel("adagencyConfig");

		$item_id = JRequest::getInt('Itemid','0','get');
		if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }

		// Check if user is logged in
		// and if user is advertiser
		if($my->id == 0){
			$link = "index.php?option=com_adagency&controller=adagencyAdvertisers&task=register".$Itemid;
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!isset($adv_id[0])){
			$link = "index.php?option=com_adagency&controller=adagencyAdvertisers&task=register".$Itemid;
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$isWizzard = 0;

		$shown = explode(";",$model->getConfigs()->show);
		foreach($shown as $element){
			if($element == "wizzard") { $isWizzard = 1; }
			if($element == "nwone") { $isBanners =1; }
		}

		// check if the user is not approved as an advertiser)
		if(($adv_id[1]=='N')||(($adv_id[1]=='P')&&(!$isWizzard)&&(!$isBanners))){
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		} elseif (($adv_id[1]=='P')&&($isWizzard!=0)&&(!isset($_GET['w']))) {
			$_GET['w']=1;
		}
		//////////////////////////////////////

		$view = $this->getView("adagencyAds", "html");
		$view->setLayout('addbanners');
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->addbanners();
	}

	function remove () {
		$item_id = JRequest::getInt('Itemid','0','get');
		if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }

		if (!$this->_model->delete()) {
			$msg = JText::_('AD_BANNERNOREMOVED');
		} else {
		 	$msg = JText::_('AD_BANNERREMOVED');
		}

		$link = "index.php?option=com_adagency&controller=adagencyAds".$Itemid;
		$this->setRedirect($link, $msg);

	}

	function click() {
		$res = $this->_model->click();
		$this->setRedirect($res);
	}

	function preview(){
		$view = $this->getView("adagencyAds", "html");
		$view->setLayout("preview");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model2 = $this->getModel("adagencyPlugin");
		$view->setModel($model2);
		$view->display();
	}
	
	function getRemoteAd(){
		include_once(JPATH_SITE.DS."components".DS."com_adagency".DS."ijoomla_ad_agency_zone.php");
	}
	
	function loadflash(){
		$flashurl = JRequest::getVar("url", "");
		$width = JRequest::getVar("width", "0");
		$height = JRequest::getVar("height", "0");
		$flashurl = urldecode($flashurl);
		
		echo '
			<style>
				body{
					margin:1px;
					padding:0px;
				}
			</style>
			<EMBED SRC="'.$flashurl.'" width="100%" height="100%" QUALITY="high" wmode="transparent" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>';
		die();
	}
};

?>
