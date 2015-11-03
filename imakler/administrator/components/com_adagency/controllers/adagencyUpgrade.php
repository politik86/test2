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

class adagencyAdminControlleradagencyUpgrade extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "upgrade");
		$this->_model = $this->getModel("adagencyupgrade");
	}

	function upgrade() {
       	JRequest::setVar("view", "adagencyupgrade");
		$view = $this->getView("adagencyupgrade", "html");
		$view->setModel($this->_model, true);
		parent::display();
	}
	
	function packs() {
       	JRequest::setVar("view", "adagencyupgrade");
		$view = $this->getView("adagencyupgrade", "html");
		$view->setLayout("packs");
		$view->setModel($this->_model, true);
		$view->packs();
	}
	
	function camps(){
       	JRequest::setVar("view", "adagencyupgrade");
		$view = $this->getView("adagencyupgrade", "html");
		$view->setLayout("camps");
		$view->setModel($this->_model, true);
		$view->camps();
	}
	
	function final_up(){
       	JRequest::setVar("view", "adagencyupgrade");
		$view = $this->getView("adagencyupgrade", "html");
		$view->setLayout("final_up");
		$view->setModel($this->_model, true);
		$view->final_up();
	}
	
	function upgradezone() {
		global $mainframe;
		$this->_model->upgradezones();
		$mainframe->redirect('index.php?option=com_adagency&controller=adagencyUpgrade&task=packs');
	}

	function upgradepack(){
		global $mainframe;
		$this->_model->upgradepack();
		$mainframe->redirect('index.php?option=com_adagency&controller=adagencyUpgrade&task=camps');
	}
	
	function upgradecamp(){
		global $mainframe;
		$this->_model->upgradecamp();
		$mainframe->redirect('index.php?option=com_adagency&controller=adagencyUpgrade&task=camps');
	}
	
};
?>