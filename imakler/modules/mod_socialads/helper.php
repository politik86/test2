<?php
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

defined( '_JEXEC' ) or die( 'Restricted Access' );
//require_once (dirname(__FILE__).DS.'mod_socialads.php');

class modSocialadsHelper
{

  function getdebuggingValues($addata)
  {
		$db = JFactory::getDBO();
		$query = "Select a.*,b.* FROM #__ad_data as a , #__ad_fields as b WHERE a.ad_id = b.adfield_ad_id AND a.ad_id = $addata->ad_id ";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		if($result)
		{
			echo "ad_creator =>".$result[0]->ad_creator;
			echo " ad_published =>".$result[0]->ad_published;
			echo " ad_approved =>".$result[0]->ad_approved;
			echo " ad_alternative =>".$result[0]->ad_alternative;
			echo " ad_noexpiry =>".$result[0]->ad_noexpiry;
			echo " ad_field_gender =>".$result[0]->field_gender;
			echo " ad_birthdaylow =>".$result[0]->field_birthday_low;
			echo " ad_birthdayhigh =>".$result[0]->field_birthday_high;
			echo " ad_graduationlow =>".$result[0]->field_graduation_low;
			echo " ad_graduationhigh =>".$result[0]->field_graduation_high;
			echo " ad_country =>".$result[0]->field_country;
			echo " ad_city =>".$result[0]->field_city;
			echo " ad_state =>".$result[0]->field_state;
			echo " relevance =>".$addata->relevance;
		}
		else{
			$query = "Select a.* FROM #__ad_data as a  WHERE a.ad_id = $addata->ad_id ";
			$db->setQuery($query);
			$result = $db->loadObjectList();
			echo "ad_creator =>".$result[0]->ad_creator;
			echo " ad_published =>".$result[0]->ad_published;
			echo " ad_approved =>".$result[0]->ad_approved;
			echo " ad_alternative =>".$result[0]->ad_alternative;
			echo " ad_guest =>".$result[0]->ad_guest;
			echo " ad_noexpiry =>".$result[0]->ad_noexpiry;
		}
  }

  //added for sa_jbolo integration
  function getAdcreator($adid)
	{
		$db = JFactory::getDBO();
		$query = "Select a.ad_creator FROM #__ad_data as a  WHERE a.ad_id = $adid ";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	function getAdChargeType($adid)
	{
		$db = JFactory::getDBO();
		$query = "Select ad_payment_type FROM #__ad_data   WHERE ad_id = $adid ";
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;
	}

	function isOnline($userid)
	{
		$db = JFactory::getDBO();
		$db->setQuery("SELECT userid FROM #__session WHERE userid = $userid AND client_id=0" );
		$result = $db->loadResult();
		if($result)
			return 1;
		else
			return 0;
	}
	function buildAdLayout($ad_id)
	{
		$adRetriever	=	 new adRetriever();
		$adRetriever->getAdHTML($addata);

	}
	function getAdtypebyZone($zone_id)
	{
		$db = JFactory::getDBO();
		$query = "SELECT az.ad_type FROM #__ad_zone as az WHERE az.id =".$zone_id;
		$db->setQuery($query);
		$result = $db->loadResult();
		return $result;

	}

	//end added for sa_jbolo integration


}
/* End Functions required by the module */

