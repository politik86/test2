<?php
/**
 * @version    SVN:<SVN_ID>
 * @package    Com_Socialads
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved
 * @license    GNU General Public License version 2, or later
 */
// No direct access
defined('_JEXEC') or die(';)');

jimport('joomla.application.component.view');

/**
 * View class for a dashboard of Socialads.
 *
 * @since  1.6
 */
class SocialadsViewDashboard extends JViewLegacy
{
	/**
	 * Display the view
	 *
	 * @param   array  $tpl  An optional associative array.
	 *
	 * @return  array
	 *
	 * @since 1.6
	 */
	public function display($tpl = null)
	{
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$this->_setToolBar();

		// Get model
		$model = $this->getModel();

		// Get download id
		$params = JComponentHelper::getParams('com_socialads');
		$this->downloadid = $params->get('downloadid');

		// Get data from the model
		$allincome = $this->get('AllOrderIncome');
		$MonthIncome = $this->get('MonthIncome');
		$AllMonthName = $this->get('Allmonths');
		$orderscount = $this->get('orderscount');

		$tot_periodicorderscount = $this->get('periodicorderscount');
		$this->assignRef('tot_periodicorderscount', $tot_periodicorderscount);

		$socialadsModelDashboard = new socialadsModelDashboard;
		$statsforbar = $socialadsModelDashboard->statsforbar();
		$this->assignRef('statsforbar', $statsforbar);

		// Calling line-graph function
		$statsforpie = $socialadsModelDashboard->statsforpie();
		$this->assignRef('statsforpie', $statsforpie);

		$ignoreCount = $this->get('ignoreCount');
		$this->assignRef('ignoreCount123', $ignoreCount);

		// Get installed version from xml file
		$xml     = JFactory::getXML(JPATH_COMPONENT . '/socialads.xml');
		$version = (string) $xml->version;
		$this->version = $version;

		// Refresh update site
		$model->refreshUpdateSite();

		// Get new version
		$this->latestVersion = $model->getLatestVersion();

		// Get data from the model
		$this->assignRef('allincome', $allincome);

		$this->assignRef('MonthIncome', $MonthIncome);
		$this->assignRef('AllMonthName', $AllMonthName);

			if (JVERSION >= 3.0)
			{
				$this->sidebar = JHtmlSidebar::render();
			}

		parent::display($tpl);
	}
	// Function display ends here

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  1.6
	 */
	public function _setToolBar()
	{
		// Get the toolbar object instance
		$bar = JToolBar::getInstance('toolbar');

		// JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
		JToolBarHelper::title(JText::_('DASHBOARD'), 'icon-48-social.png');

		// JToolBarHelper::cancel( 'cancel', 'Close' );
	}
}
// Class
