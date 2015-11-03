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

class adagencyAdminControlleradagencyBlacklist extends adagencyAdminController {
	var $_model = null;
	
	function __construct () {
		parent::__construct();
		$this->registerTask ("", "blacklist");
		$this->registerTask ("blacklist", "blacklist");
		
		$this->_model = $this->getModel("adagencyBlacklist");
	}

	function blacklist() {
		$view = $this->getView("adagencyBlacklist", "html");
		$view->setModel($this->_model, true);
		$view->display();
	}
	
	function save(){
		$return = $this->_model->save();
		$msg = "";
		$type = "";
		
		if($return){
			$msg = JText::_("ADAG_IP_SAVED_SUCCESSFULLY");
			$type = "message";
		}
		elseif(!$return){
			$msg = JText::_("ADAG_IP_NOT_SAVED_SUCCESSFULLY");
			$type = "merror";
		}
		$this->setRedirect("index.php?option=com_adagency", $msg, $type);
	}
	
	function apply(){
		$return = $this->_model->save();
		$msg = "";
		$type = "";
		
		if($return){
			$msg = JText::_("ADAG_IP_SAVED_SUCCESSFULLY");
			$type = "message";
		}
		elseif(!$return){
			$msg = JText::_("ADAG_IP_NOT_SAVED_SUCCESSFULLY");
			$type = "merror";
		}
		$this->setRedirect("index.php?option=com_adagency&controller=adagencyBlacklist", $msg, $type);
	}
	
	function cancel(){
		$this->setRedirect("index.php?option=com_adagency");
	}
};
?>
