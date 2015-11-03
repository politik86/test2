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

class adagencyControlleradagencyPackages extends adagencyController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "listOrders");
        $this->registerTask ("default", "listOrders");
		$this->_model = $this->getModel("adagencyPackage");
        $this->model2 = $this->getModel("adagencyConfig");
	}

	function listOrders() {
		$view = $this->getView("adagencyPackage", "html");
		$view->setModel($this->_model, true);
        $view->setModel($this->model2);
		$view->display();
	}
	
	function packs(){
		$view = $this->getView("adagencyPackage", "html");
		$view->setModel($this->_model, true);
		$view->setLayout("packs");
		$view->packs();
	}

	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyPackage", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$view->editForm();
	}

	function save () { 
		if ($this->_model->store() ) {

			$msg = JText::_('PACKAGESAVED');
		} else {
			$msg = JText::_('PACKAGEFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyPackages";
		$this->setRedirect($link, $msg);
	}


	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('PACKAGEREMERR');
		} else {
		 	$msg = JText::_('PACKAGEREMSUCC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyPackages";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('PACKAGECANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyPackages";
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('PACKAGEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PACKAGEUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PACKAGEPUB');
		} else {
           	$msg = JText::_('PACKAGEUNSPEC');
		}
		
		$link = "index.php?option=com_adagency&controller=adagencyPackages";
		$this->setRedirect($link, $msg);
	}
	
	function unpublish () {
		$res = $this->_model->unpublish();
		if (!$res) {
			$msg = JText::_('PACKAGEBLOCKERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PACKAGEUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PACKAGEPUB');
		} else {
           	$msg = JText::_('PACKAGEUNSPEC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyPackages";
		$this->setRedirect($link, $msg);
	}
	
	function preview () {
		JRequest::setVar ("hidemainmenu", 1); 
		$view = $this->getView("adagencyPackage", "html");
		$view->setLayout("preview");
		$view->setModel($this->_model, true);
		$view->preview();
	}		
};
?>