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

class adagencyAdminControlleradagencyPromocodes extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("apply", "save");
		$this->registerTask ("", "listPromos");
		$this->_model = $this->getModel("adagencyPromocodes");
		$this->registerTask ("unpublish", "publish");	
	}

	function listPromos() {
		$view = $this->getView("adagencyPromocodes", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}


	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyPromocodes", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}


	function save(){
		if ($this->_model->store()){
			$msg = JText::_('PROMSAVED');
		}
		else{
			$msg = JText::_('PROMFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyPromocodes";

		if ( JRequest::getVar('task','') == 'save' ) {
			$link = "index.php?option=com_adagency&controller=adagencyPromocodes";
		} else {
			$promo_id = JRequest::getVar('id','');
			$link = "index.php?option=com_adagency&controller=adagencyPromocodes&task=edit&cid[]=" . intval($promo_id);
		}
		
		$this->setRedirect($link, $msg);

	}


	function remove(){
		if (!$this->_model->delete()) {
			$msg = JText::_('PROMREMERR');
		} else {
		 	$msg = JText::_('PROMREMSUCC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyPromocodes";
		$this->setRedirect($link, $msg);
		
	}

	function cancel(){
	 	$msg = JText::_('PROMCANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyPromocodes";
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('PROMBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PROMUNPUBSUCC');
		} elseif ($res == 1) {
			$msg = JText::_('PROMPPUBSUCC');
		} else {
                 	$msg = JText::_('PROMUNSPEC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyPromocodes";
		$this->setRedirect($link, $msg);
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyPromocodes");
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