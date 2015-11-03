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

class adagencyAdminControlleradagencyCampaigns extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listCampaigns");
		$this->_model = $this->getModel("adagencyCampaigns");
		$this->registerTask ("unpublish", "publish");	
		$this->registerTask ("details", "details");
	}

	function listCampaigns() {
		$view = $this->getView("adagencyCampaigns", "html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->display();
	}

	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyCampaigns", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('AD_CMP_SAVED');
		} else {
			$msg = JText::_('AD_CMP_NOT_SAVED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('AD_CMP_CANTREMOVED');
		} else {
		 	$msg = JText::_('AD_CMP_REMOVED');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('AD_OP_CANCELED');
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
	
	function approve () { 
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('AD_CMP_UNERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_CMP_AP');
		} elseif ($res == 1) {
			$msg = JText::_('AD_CMP_APPV');
		} else {
			$msg = JText::_('AD_CMP_UNERROR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
	
	function pending(){
		$res = $this->_model->publish();
		if(!isset($res)) {
			$msg = JText::_('AD_CMP_UNERROR');
		} elseif ($res == 0){
			$msg = JText::_('ADAG_PENMSG');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link,$msg);
	}

	function unapprove () {
		$res = $this->_model->pbl();
		if (!$res) {
			$msg = JText::_('AD_CMP_ERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_CMP_UNNAP');
		} elseif ($res == 1) {
			$msg = JText::_('AD_CMP_APPV');
		} else {
           	$msg = JText::_('AD_CMP_ERROR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
		
	function publish () { 
		$res = $this->_model->pbl();
		
		if (!$res) { 
			$msg = JText::_('AD_CMP_UNERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_CMP_UNNAP');
		} elseif ($res == 1) {
			$msg = JText::_('AD_CMP_APPV');
		} else {
	       	$msg = JText::_('AD_CMP_UNERROR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
	
	function unpublish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('AD_CMP_ERROR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_CMP_UNNAP');
		} elseif ($res == 1) {
			$msg = JText::_('AD_CMP_APPV');
		} else {
          	$msg = JText::_('AD_CMP_ERROR');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
		
	function pause () {
		if (!$this->_model->pause()) {
			$msg = JText::_('AD_CMP_CANTPAUSE');
		} else {
		 	$msg = JText::_('AD_CMP_PAUSED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
		
	function unpause () {
		if (!$this->_model->unpause()) {
			$msg = JText::_('AD_CMP_CANTUNPAUSE');
		} else {
		 	$msg = JText::_('AD_CMP_UNPAUSED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyCampaigns";
		$this->setRedirect($link, $msg);
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyCampaigns");
		// Save the ordering
		$return = $model->saveorder($pks, $order);
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
	
	function details(){
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyCampaigns", "html");
		$view->setLayout("details");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}
};
?>