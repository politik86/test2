<?php
/*
	* @package SocialAds Plugin for J!MailALerts Component
	* @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
	* @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
	* @link     http://www.techjoomla.com
*/
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/*load language file for plugin frontend*/
$lang =  JFactory::getLanguage();
$lang->load('plg_emailalerts_jma_socialads', JPATH_ADMINISTRATOR);

/*
 * SocialAds Plugin for JMailAlerts
 * This Plugin pulls ads from SocialAds to the J!MailAlerts component.
*/

class plgEmailalertsjma_socialads extends JPlugin
{
	function plgEmailalertsSocialads(& $subject, $config)
	{
		parent::__construct($subject, $config);
		if($this->params===false)
		{//unknown
			$jPlugin = JPluginHelper::getPlugin('emailalerts','jma_socialads');
			$this->params = new JParameter( $jPlugin->params);
		}
	}

	function onEmail_jma_socialads($id, $date, $userparam, $fetch_only_latest)
	{
		$areturn	=  array();
		$areturn[0]	= $this->_name;
		$check = $this->_chkextension();
		if(!($check)){
			$areturn[1]	= '';
			$areturn[2]	= '';
			return $areturn;
		}
		$userparam['alt_ad'] = 1;
		$userparam['ad_rotation'] = 0;
//$id = 54;
		$input=JFactory::getApplication()->input;
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');
		require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'helper.php');
		$html = '<span>';
		$cssdata= '';
		$simulate = '';
		$sim_flag = $input->get('flag',0,'INT');
		if($sim_flag == 1)	// to check if called from simulate in admin
			$simulate = '&amp;simulate=1';

		if($id == '' || !($id) || !isset($id) )
			$adRetriever = new adRetriever(-1,1);
		else
			$adRetriever = new adRetriever($id,1);
		$socialadshelper = new socialadshelper();
		$adsdata = array();
		$adsdata = $adRetriever->fillslots($userparam, $adRetriever);	//echo '<br>data';//print_r($adsdata);
		if(!empty($adsdata))
		{
			//$random_ads = $adRetriever->getRandomId($adsdata,$userparam);
			$itemid = $socialadshelper->getSocialadsItemid('buildad');
			foreach($adsdata as $key => $random_ad){
				if($random_ad->ad_id != -999){
					$addata = $adRetriever->getAdDetails($random_ad);
				}
				else{
					$addata = null;
				}
				if($addata) {
					$html .= '<div>';
					$html .= $adRetriever->getAdHTML($addata);
					$html .= '<img alt="" src="'.JUri::root().'index.php?option=com_socialads&amp;task=getimprimage&amp;adid='.$random_ad->ad_id.$simulate.'"  border="0"  width="1" height="1"/>';
					$html .= '</div>';
					if(JVERSION >= '1.6.0')
						$cssfile=JPATH_SITE.DS.'plugins'.DS.'socialadslayout'.DS.'plug_'.$addata->layout.DS.'plug_'.$addata->layout.DS.'layout.css';
					else
						$cssfile=JPATH_SITE.DS.'plugins'.DS.'socialadslayout'.DS.'plug_'.$addata->layout.DS.'layout.css';
					$cssdata.=file_get_contents($cssfile);
				}
			}
			if($userparam['create']==1)
			$html.='<div style="clear:both;"></div><a class ="create" target="_blank" href="'.JRoute::_(JUri::root().'index.php?option=com_socialads&amp;view=buildad&amp;Itemid='.$itemid).'">'.$userparam['create_text'].'</a>';
		}
		$html .= '</span>';

		if(empty($adsdata))
		{
			$areturn[1]	= '';
			$areturn[2]	= '';
		}
		else
		{
			$areturn[1]	= $html;
			$areturn[2] = $cssdata;
		}
//		print_r($areturn);
    return $areturn;
	}//onEmail_jma_socialads() ends

	//_chkextension function checks if the extension folder is present
	function _chkextension()
	{
		jimport('joomla.filesystem.file');
		$extpath = JPATH_ROOT.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_socialads';
		if(JFolder::exists($extpath) )
			return 1;
		else
			return 0;
	}


}//class plgEmailalertsJsntwrk  ends
