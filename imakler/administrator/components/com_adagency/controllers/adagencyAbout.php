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

class adagencyAdminControlleradagencyAbout extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("add", "edit");
		$this->registerTask ("", "listCategories");
		$this->registerTask ("unpublish", "publish");	
		$this->_model = $this->getModel("adagencyabout");
	}

	function listCategories() {
       	JRequest::setVar ("view", "adagencyabout");
		$view = $this->getView("adagencyabout", "html");
		$view->setModel($this->_model, true);
		parent::display();
	}

	function vimeo(){
   		JRequest::setVar( 'view', 'adagencyAbout' );
		JRequest::setVar( 'layout', 'vimeo'  );
        $view = $this->getView("adagencyAbout", "html");
		$view->setLayout("vimeo");
        $view->vimeo();
        die();
    }

	function cancel () {
	 	$link = "index.php?option=com_adagency";
		$this->setRedirect($link, $msg);
	}
};
?>