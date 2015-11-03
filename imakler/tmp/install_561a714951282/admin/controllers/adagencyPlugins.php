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

class adagencyAdminControlleradagencyPlugins extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listPlugins");
		$this->_model = $this->getModel("adagencyPlugin");
		$this->registerTask ("unpublish", "publish");	
	}

	function listPlugins() {
		$view = $this->getView("adagencyPlugins", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyPlugins", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('PLUGSAVED');
		} else {
			$msg = JText::_('PLUGFAILED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyPlugins";
		$this->setRedirect($link, $msg);
	}

	function upload () {
		$msg = $this->_model->upload();
		$link = "index.php?option=com_adagency&controller=adagencyPlugins";
		$this->setRedirect($link, $msg);
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('PLUGREMERR');
		} else {
		 	$msg = JText::_('PLUGREMSUCC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyPlugins";
		$this->setRedirect($link, $msg);
	}

	function cancel () {
	 	$msg = JText::_('PLUGCANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyPlugins";
		$this->setRedirect($link, $msg);
	}

	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('PLUGPUBERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('PLUGUNPUB');
		} elseif ($res == 1) {
			$msg = JText::_('PLUGPUB');
		} else {
                 	$msg = JText::_('PLUGUNSPEC');
		}
		$link = "index.php?option=com_adagency&controller=adagencyPlugins";
		$this->setRedirect($link, $msg);
	}

};
?>