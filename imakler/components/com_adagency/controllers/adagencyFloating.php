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

class adagencyControlleradagencyFloating extends adagencyController {
	var $_model = null;
	
	function __construct () {

		parent::__construct();

		$this->registerTask ("add", "edit");
		$this->registerTask ("", "edit");
		$this->_model = $this->getModel("adagencyFloating");
		$this->registerTask ("unpublish", "publish");	
	}

	function edit () {
		global $mainframe;
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyFloating", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		
		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }	
		
		$view->setModel($model);
		//////////////////////////////
		$my	= JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database =  JFactory :: getDBO();
		$link = "index.php?option=com_adagency" . $Itemid;
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();
		// Check if user is logged in 
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!$adv_id[0]){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$isWizzard = $model->isWizzard();
		// check if the user is not approved as an advertiser
		if(($adv_id[1]=='N')||(($adv_id[1]=='P')&&(!$isWizzard))){
			$mainframe->redirect($link, JText::_('AD_FAILEDAPPROVE'));
		} 
		//////////////////////////////////////
		$view->editForm();

	}


	function save () {
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }	
		
		if ($this->_model->store() ) {
			$msg = JText::_('AD_BANNERSAVED');
		} else {
			$msg = JText::_('AD_BANNERFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds".$Itemid;
		$this->setRedirect($link, $msg);

	}
};

?>