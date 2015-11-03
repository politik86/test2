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

class adagencyAdminControlleradagencyGeo extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "channels");
		$this->registerTask ("savechannel", "applychannel");
		$this->registerTask ("settings", "settings");
		$this->_model = $this->getModel("adagencygeo");
	}

	function settings(){
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencygeo", "html");
		$view->setLayout("settings");
		$view->setModel($this->_model, true);
		$view->settings();
	}
	
	function channels(){
		$view = $this->getView("adagencyGeo", "html");
		$view->setLayout("default");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->display();
	}	

	function cancel () {
	 	$link = "index.php?option=com_adagency";
		$this->setRedirect($link);
	}
	
	function cancelGoToChannels() {
		$link = "index.php?option=com_adagency&controller=adagencyGeo";
		$this->setRedirect($link);
	}
	
	function savesettings(){
		if($this->_model->saveSettings()) {
			$msg = JText::_('ADAG_GEOSETSAVE');
			$type = "message";
		} else { 
			$msg = JText::_('ADAG_GEOSETNSAV'); 
			$type = "notice";
		}
		$link = "index.php?option=com_adagency&controller=adagencyGeo";
		
		$this->setRedirect($link,$msg,$type);
	}

	function applysettings(){
		if($this->_model->saveSettings()) {
			$msg = JText::_('ADAG_GEOSETSAVE');
			$type = "message";
		} else { 
			$msg = JText::_('ADAG_GEOSETNSAV'); 
			$type = "notice";
		}
		$link = "index.php?option=com_adagency&controller=adagencyGeo&task=settings";
		
		$this->setRedirect($link,$msg,$type);
	}
	
	function applychannel(){
		$data = JRequest::get('post');
		$cid = $data['cid'];
		
		if($this->_model->checkDuplicate()){
			if($id = $this->_model->saveChannel()) {
				$msg = JText::_('ADAG_CHANNEL_SAVED');
				$type = "message";
			} else { 
				$msg = JText::_('ADAG_CHANNEL_FAIL'); 
				$type = "notice";
			}
		} else {
			if(isset($cid[0])&&(intval($cid[0])!='0')) { $id = $cid[0]; } else { $id = 0; }
			$msg = JText::_('ADAG_CHANNEL_DUPLICATE'); 
			$type = "notice";		
		}
		if($data['task'] == 'savechannel') {
			$link = "index.php?option=com_adagency&controller=adagencyGeo&task=channels";
		} elseif ($data['task'] == 'applychannel') {
			$link = "index.php?option=com_adagency&controller=adagencyGeo&task=edit&cid[]=".intval($id);
		}
		$this->setRedirect($link,$msg,$type);
	}
	
	function publish(){
		if (!$this->_model->publish()) {
			$msg = JText::_('ADAG_NOT_CHANGE_PUBLIC');
			$type = "notice";		
		} else {
		 	$msg = JText::_('ADAG_SUCCESS_CHANGE_PUBLIC');
			$type = "message";
		}		
		$link = "index.php?option=com_adagency&controller=adagencyGeo&task=channels";
		$this->setRedirect($link, $msg);
	}
	
	function unpublish(){
		if (!$this->_model->unpublish()) {
			$msg = JText::_('ADAG_NOT_CHANGE_PUBLIC');
			$type = "notice";		
		} else {
		 	$msg = JText::_('ADAG_SUCCESS_CHANGE_PUBLIC');
			$type = "message";
		}
		$link = "index.php?option=com_adagency&controller=adagencyGeo&task=channels";
		$this->setRedirect($link, $msg);
	}
		
	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyGeo", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}
	
	function delete () {
		$status = "";
		if (!$this->_model->delete()) {
			$msg = JText::_('ADAG_GEO_DEL_FAIL');
			$status = "error";
		} else {
		 	$msg = JText::_('ADAG_GEO_DEL_SUC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyGeo&task=channels";
		$this->setRedirect($link, $msg, $status);
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencygeo");
		// Save the ordering
		$return = $model->saveorder($pks, $order);
		if ($return){
			echo "1";
		}
		// Close the application
		JFactory::getApplication()->close();
	}
		
};
?>