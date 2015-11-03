<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
/**
 * HTML View class for the socialads Component
 *
 * @package    socialads
 * @subpackage Views
 */
class socialadsViewCheckout extends JViewLegacy
{
    /**
     * Checkout view display method
     * @return void
     **/
	function display($tpl = null)
	{
		$this->country=$this->get("Country");
		$this->setLayout('default');

		// load social ads config params
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$selected_gateways = $socialads_config['gateways'];

		//getting GETWAYS
		$dispatcher = JDispatcher::getInstance();
		JPluginHelper::importPlugin('payment');
		//$params->get( 'gateways' ) = array('0' => 'paypal','1'=>'Payu');

		if(  !is_array($selected_gateways) )
		{
			$gateway_param[] = $selected_gateways;
		}
		else
		{
			$gateway_param = $selected_gateways;
		}

		if(!empty($gateway_param))
		{
			$gateways = $dispatcher->trigger('onTP_GetInfo',array($gateway_param));
		}
		$this->gateways = $gateways;


		parent::display($tpl);
	}

}// class

