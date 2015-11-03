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

class adagencyControlleradagencyTextlink extends adagencyController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "edit");
		$this->_model = $this->getModel("adagencyTextlink");
		$this->registerTask ("unpublish", "publish");	
	}
	
	function upload() {
		$item_id = JRequest::getInt('Itemid','0');
		if($item_id != 0) { 
			$Itemid = "&Itemid=".intval($item_id); 
			JRequest::setVar('Itemid',$item_id,'get');
		} else { $Itemid = NULL; }
		
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyTextlink", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$model2 = $this->getModel("adagencyAds");
		$view->setModel($model2);		
		$view->uploaded_file=$view->uploadbannerimage();
		$view->editForm();
	}

	function edit () {
		global $mainframe;
		$my	= JFactory::getUser();
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyTextlink", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		/////////////////////////////////////
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database = JFactory :: getDBO();
		$item_id = $model->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }
		$link = "index.php?option=com_adagency" . $Itemid;
		$advertiser = $this->_model->getCurrentAdvertiser();
		
		// Check if user is logged in 
		// and if user is advertiser
		if($my->id == 0){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'), 'notice');
		} elseif(!$advertiser->aid){
			$link = JRoute::_("index.php?option=com_adagency&controller=adagencyAdvertisers&task=register" . $Itemid);
			$this->setRedirect($link, JText::_('ADAG_FAIL_NO_ADV'), 'notice');
		}

		$isWizzard = $model->isWizzard();
		// check if the user is not approved as an advertiser
		if(($advertiser->approved=='N')||(($advertiser->approved=='P')&&(!$isWizzard))){
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