<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined( '_JEXEC' ) or die( ';)' );

jimport('joomla.application.component.controller');
include_once(JPATH_COMPONENT.DS.'controller.php');

class socialadsControllerCheckout extends JControllerLegacy
{

	function loadState()
	{
		$db= JFactory::getDBO();
		$jinput=JFactory::getApplication()->input;
		$country = $jinput->get('country','','STRING');

		$model = $this->getModel('checkout');

		$state = $model->getuserState($country);
		echo json_encode($state);
		jexit();

	}
	function adsPlaceOrder()
	{
		$db= JFactory::getDBO();
		$jinput=JFactory::getApplication()->input;
die("asdfadsf");
		$state = $model->getuserState('India');
		echo json_encode($state);
		jexit();
	}
}// class end


