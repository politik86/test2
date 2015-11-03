<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.html.parameter' );
require_once JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'helper.php';

/* Showad Model */
class socialadsModelShowad extends JModelLegacy
{

	function getAds($adid = '')
	{
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');

		if(empty($adid))
		{
			$adid = 0;
		}
		$adRetriever = new adRetriever();
		$preview =  $adRetriever->getAdHTML($adid, 1);
		return $preview;
	}

}//class end



