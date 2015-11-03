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

class adagencyAdminViewadagencyPlugins extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('AD_PLUGINS_MANAGERS'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::deleteList();
		$plugins = $this->get('listPlugins');
		$this->assignRef('plugins', $plugins);
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		$db = JFactory::getDBO();
		$plugin = $this->get('plugin');
		$plugin_data = $this->_models['adagencyplugin']->BEPluginHandler();
		$isNew = ($plugin->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		JToolBarHelper::title(JText::_('Plugin').":<small>[".$text."]</small>");
		JToolBarHelper::save();
		if ($isNew) {
			JToolBarHelper::cancel();
		} else {
			JToolBarHelper::cancel ('cancel', 'Close');
		}
		$this->assign("plugin", $plugin);
		$configs = $this->_models['adagencyconfig']->getConfigs();
		$lists = null;
		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("plugin_data", $plugin_data);
		parent::display($tpl);
	}
}
?>