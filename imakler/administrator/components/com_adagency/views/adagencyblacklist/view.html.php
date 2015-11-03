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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyBlacklist extends JViewLegacy {
	
	function display($tpl = null){
		JToolBarHelper::title(JText::_('AD_BLCKLIST'));
		JToolBarHelper::apply('apply');
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel');
		
		$black_list = $this->get("BlackList");
		$this->black_list = $black_list;

		parent::display($tpl);
	}
}
?>
