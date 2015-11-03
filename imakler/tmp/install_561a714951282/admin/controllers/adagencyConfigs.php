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

class adagencyAdminControlleradagencyConfigs extends adagencyAdminController {

	var $_model = null;
	
	function __construct () {
		parent::__construct();
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
		$this->registerTask ("", "listConfigs");
		$this->registerTask("general", "listConfigs");
		$this->registerTask("payments", "listConfigs");
		$this->registerTask("content", "listConfigs");
		$this->_model = $this->getModel("adagencyConfig");
	}

	function listConfigs() {
		$view = $this->getView("adagencyConfigs", "html");
		$view->setModel($this->_model, true);
		$model = $this->getModel("adagencyPlugin");
		$view->setModel($model);
		$view->display();
	}

	function save () {
		if ($this->_model->store() ) {
			$msg = JText::_('CONFIGSAVED');
		} else {
			$msg = JText::_('');
		}
		$link = "index.php?option=com_adagency";
		$this->setRedirect($link, $msg);
	}
	
	function apply () {
		$tab = JRequest::getVar('tab');
		if ($this->_model->store() ) {
			$msg = JText::_('CONFIGSAVED');
		} else {
			$msg = JText::_('');
		}
		if(isset($_POST['task2'])&&($_POST['task2']!='')){ $task2="&task2=".$_POST['task2'];} else { $task2 = NULL;}
		if(isset($tab)&&($tab != NULL)&&($tab !='')) {
			$pieces = explode('-',$tab);
			$task2 = '&task2='.$pieces[0];
		} 
		//echo "<pre>";var_dump($_POST);echo "<hr />";var_dump($tab);echo "<hr />";var_dump($task2);echo "<hr />";die();
		$link = "index.php?option=com_adagency&controller=adagencyConfigs".stripslashes($task2);
		$this->setRedirect($link, $msg);
	}
	
};
?>