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

class adagencyControlleradagencyReports extends adagencyController {
	var $_model = null;
	
	function __construct(){
		parent::__construct();
		$this->registerTask ("", "listReports");
		$this->registerTask ("campaigns", "listReports");
		$this->registerTask ("campaigns_csv", "campaignsCSV");
		$this->registerTask ("campaigns_pdf", "campaignsPDF");
		
		$this->_model = $this->getModel("adagencyReports");
	}

	function listReports() {
		$view = $this->getView("adagencyReports", "html");
        $model2 = $this->getModel("adagencyConfig");
		$view->setModel($this->_model, true);
        $view->setModel($model2);
		//////////////////////////////
		$my	= JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database =  JFactory :: getDBO();
		$item_id = $model2->getItemid('adagencyadvertiser');
		if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }			
		$link="index.php?option=com_adagency".$Itemid;
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
		} elseif($adv_id[1]!='Y'){
            // check if the user is not approved as an advertiser)
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		}
		//////////////////////////////////
		$view->display();
	}

	function rotator() {
		$this->_model->rotator();
		die();
	}
	
	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyLanguages", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
	
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		//////////////////////////////
		$my	= JFactory::getUser();
		$mosConfig_absolute_path = JPATH_BASE; 
		$mosConfig_live_site = JURI::base();
		$database =  JFactory :: getDBO();
		$link="index.php";
		$sql = "SELECT `aid`,`approved` FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."' ";
		$database->setQuery($sql);
		$adv_id= $database->loadRow();
		// check if the user is not an advertiser 
		if(!$adv_id[0]){
			$this->setRedirect($link, JText::_('AD_FAILEDACCESS'));
		}
		// check if the user is not approved as an advertiser)
		if($adv_id[1]=='N'){
			$this->setRedirect($link, JText::_('AD_FAILEDAPPROVE'));
		}
		///////////////////////////////
		$view->editForm();
	}
	
	function creat () { 
		$view = $this->getView("adagencyReports", "html");    
        $model2 = $this->getModel("adagencyConfig");
		$view->setModel($this->_model, true);
        $view->setModel($model2);

		$view->display();
	}
	
	function emptyrep () { 
		$view = $this->getView("adagencyReports", "html");
		$view->setModel($this->_model, true);

		$view->emptyrep();
	}

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('LANGSAVED');
		} else {
			$msg = JText::_('LANGSAVEFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function upload () {
		$msg = $this->_model->upload();

		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('LANGREMERROR');
		} else {
		 	$msg = JText::_('LALNGREMSUCC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('LANGCANCELED');	
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('LANGPUBLICHERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('LANGUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('LANGPUBSUCC');
		} else {
           	$msg = JText::_('LANGUNSPECERROR');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyLanguages";
		$this->setRedirect($link, $msg);
	}
	
	function campaignsCSV(){
		$this->_model->campaignsCSV();
	}
	
	function campaignsPDF(){
		$this->_model->campaignsPDF();
	}
};
?>