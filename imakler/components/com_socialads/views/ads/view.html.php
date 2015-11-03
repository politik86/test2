<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.view');
jimport( 'joomla.html.parameter' );

class socialadsViewads extends JViewLegacy
{
    /* ads view display method */
	function display($tpl = null)
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$jinput = JFactory::getApplication()->input;
		$adData = $jinput->get('adData','',"RAW");
		if(!empty($adData))
		{
			$adData = json_decode($adData,true);
			if( !empty($adData['ads_params']['ad_unit']) && !empty($adData['ads_params']['zone']) ){
				$adData['ads_params']['alt_ad'] = 1;
				$adData['ads_params']['debug'] = 0;
				$this->adData = $adData ;
				$session = JFactory::getSession();
				$session->set('userData',$adData);
				require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'remote.php');
				$adRetriever = new remoteAdRetriever(1);
				$this->ads	 = $adRetriever->getnumberofAds($adData,$adData['ads_params']['ad_unit'],$adRetriever);
				$this->adRetriever = $adRetriever;
				$this->moduleid = $adData['ads_params']['ad_unit'];
				$this->zone = $adData['ads_params']['zone'];
			}
		}
		parent::display($tpl);
	}

}// class end
