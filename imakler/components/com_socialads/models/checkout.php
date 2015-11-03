<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined( '_JEXEC' ) or die( ';)' );

jimport( 'joomla.application.component.model' );
jimport( 'joomla.utilities.utility' );
jimport( 'joomla.html.parameter' );

/* Showad Model */
class socialadsModelCheckout extends JModelLegacy
{

	// To Fetch country list from Db
	function getCountry()
	{
		$db = JFactory::getDBO();
		$query="SELECT `country` FROM `#__ad_geo_country`";
		$db->setQuery($query);
		$rows = $db->loadColumn();
		return $rows;
	}
	function getuserState($country)
	{
		$db = JFactory::getDBO();
		$query="SELECT r.region FROM `#__ad_geo_region` AS r LEFT JOIN `#__ad_geo_country` as c
		ON r.country_code=c.country_code where c.country=\"".$country."\"";
		$db->setQuery($query);
		$rows = $db->loadColumn();
		return $rows;
	}
}//class end



