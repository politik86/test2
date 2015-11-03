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

class adagencyAdminControlleradagencyTextlink extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("apply", "apply");
		$this->registerTask ("", "listAd");
		$this->_model = $this->getModel("adagencyTextlink");
		$this->registerTask ("unpublish", "publish");	
	}

	function listAd() {
		$view = $this->getView("adagencyTextlink", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}

	function edit () {
		JRequest::setVar ("hidemainmenu", 1);
		$view = $this->getView("adagencyTextlink", "html");
		$view->setLayout("editForm");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyConfig");
		$view->setModel($model);
		$view->editForm();
	}

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('AD_ADSAVED');
		} else {
			$msg = JText::_('AD_ADSAVEFAIL');
		}
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function apply(){
		if ($this->_model->store() ) {
			if ($_POST['id']!=0) { 
				//$this->edit(); 
				$msg = JText::_('AD_ADSAVED');
				$link = "index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=".intval($_POST['id']);
				$this->setRedirect($link, $msg);
			} 
			else { 
				$link ="index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=".intval($this->_model->last_ad("TextLink"))."";
				$this->setRedirect($link, "");
			}
		} else {
			$msg = JText::_('AD_ADSAVEFAIL');
			$link = "index.php?option=com_adagency&controller=adagencyAds";
			$this->setRedirect($link, $msg);
		}
	}

	function cancel () {
	 	$msg = JText::_('AD_SAVECANCEL');
		$link = "index.php?option=com_adagency&controller=adagencyAds";
		$this->setRedirect($link, $msg);
	}
	
	function upload() { 
		JRequest::setVar("after_upload_image", "true");
		JRequest::setVar("hidemainmenu", 1);
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

};
?>