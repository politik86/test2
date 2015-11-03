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

class adagencyAdminControlleradagencyOrders extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "listOrders");
		$this->_model = $this->getModel("adagencyOrder");
	}

	function listOrders() {
		$view = $this->getView("adagencyOrders", "html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->display();
	}
	
	function confirm() {
		if (!$this->_model->confirm()) {
			$msg = JText::_('AD_ORD_NOTCONF');
		} else {
		 	$msg = JText::_('AD_ORD_CONF');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}

	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyOrders", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}
	
	function add () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyOrders", "html");
		$view->setLayout("addOrder");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->addOrder();
	}	

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('ORDSAVED');
		} else {
			$msg = JText::_('ORDFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}
	
	function savenew () {
		if ($this->_model->storenew() ) {
			$msg = JText::_('ORDSAVED');
		} else {
			$msg = JText::_('ORDFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('ORDREMERR');
		} else {
		 	$msg = JText::_('ORDREMSUCC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}
	
	function delete () {
		if (!$this->_model->remove()) {
			$msg = JText::_('ORDREMERR');
		} else {
		 	$msg = JText::_('ORDREMSUCC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('ORDCANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('ORDBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('ORDUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('ORDPUB');
		} else {
                 	$msg = JText::_('ORDUNSPEC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyOrders";
		$this->setRedirect($link, $msg);
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyOrder");
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