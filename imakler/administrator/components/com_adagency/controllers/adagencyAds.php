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

class adagencyAdminControlleradagencyAds extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "listAds");
		$this->_model = $this->getModel("adagencyAds");
		$this->registerTask ("unpublish", "publish");	
	}

	function listAds() {
		$this->_model->saveorder();
		$view = $this->getView("adagencyAds", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function add () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyAds", "html");
		$view->setLayout("add");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->add();
	}

	function remove () {
		if (!$this->_model->delete()) {
			$msg = JText::_('AD_REMOVE_ERR');
		} else {
		 	$msg = JText::_('AD_REMOVED');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function publish () {
		$res = $this->_model->publish();
		if (!$res) {
			$msg = JText::_('AD_PUB_ERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_UNPUBLISHED');
		} elseif ($res == 1) {
			$msg = JText::_('AD_PUBLISHED');
		} else {
                 	$msg = JText::_('AD_PUB_ERR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function getChannelInfo(){
		$this->_model->getChannelInfo();
		die();
	}

	function approve () {
		$res = $this->_model->approve();
		if (!$res) {
			$msg = JText::_('AD_UNPUB_ERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_UNPUBLISHED');
		} elseif ($res == 1) {
			$msg = JText::_('AD_PUBLISHED');
		} else {
	       	$msg = JText::_('AD_UNPUB_ERR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function pending () {
		$res = $this->_model->pending();
		if($res==0){
			$msg = JText::_('ADAG_PENMSG');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
		
	function unapprove () {
		$res = $this->_model->unapprove();
		if (!$res) {
			$msg = JText::_('AD_UNPUB_ERR');
		} elseif ($res == -1) {
		 	$msg = JText::_('AD_UNPUBLISHED');
		} elseif ($res == 1) {
			$msg = JText::_('AD_PUBLISHED');
		} else {
 			$msg = JText::_('AD_UNPUB_ERR');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function saveorder(){
		$this->_model->saveorder();
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link);
	}

	function copy () {
		$res = $this->_model->copy();
		if (!$res) {
			$msg = JText::_('AD_COPY_ERR');
		} elseif ($res == 1) {
		 	$msg = JText::_('AD_COPIED');
		}			
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	public function saveOrderAjax(){
		// Get the arrays from the Request
		$pks   = $this->input->post->get('cid', null, 'array');
		$order = $this->input->post->get('order', null, 'array');
		$originalOrder = explode(',', $this->input->getString('original_order_values'));

		$model = $this->getModel("adagencyAds");
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