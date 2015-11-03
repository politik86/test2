<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.application.component.view');
class socialadsViewsettings extends JViewLegacy
{
	function display($tpl = null)
	{
		if(JVERSION < 3.0)
		JHTML::_('behavior.mootools');
		else
		JHtmlBehavior::framework();
		$this->_setToolBar();
		$this->setLayout('settings');

		$model = $this->getModel( 'settings' );
		$gatewayplugin = $this->get('APIpluginData');
		$this->assignRef('gatewayplugin', $gatewayplugin);
		$model->refreshUpdateSite();
		$geotable_list = $model->checkgeotables();
		if(!empty($geotable_list))
		$geotablepresent=0;
		else
		$geotablepresent=1;
		$this->assignRef('geotablepresent',$geotablepresent);
		if(JVERSION>=3.0)
			$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}
	function _setToolBar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');
		JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
		JToolBarHelper::title( JText::_( 'socialads_TITLE' ), 'icon-48-social.png' );
		JToolBarHelper::save('save',JText::_('SAVE'));
		// Options button.
		if (JFactory::getUser()->authorise('core.admin', 'com_socialads')) {
			JToolBarHelper::preferences('com_socialads','', '', $alt = JText::_('COM_SOCIALADS_PERMISION_OPT') );
		}
		//JToolBarHelper::cancel( 'cancel', 'Close' );
	}
}
?>
