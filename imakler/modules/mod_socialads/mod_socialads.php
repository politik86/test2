<?php
/**
 * @package Social Ads
 * @copyright Copyright (C) 2009 -2010 Techjoomla, Tekdi Web Solutions . All rights reserved.
 * @license GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     http://www.techjoomla.com
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')){
define('DS',DIRECTORY_SEPARATOR);
}
jimport('joomla.filesystem.folder');
$doc =JFactory::getDocument();
if( JFolder::exists( JPATH_ROOT.'/components/com_socialads') )
{

	// Load js assets
	jimport('joomla.filesystem.file');
	$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

	if (JFile::exists($tjStrapperPath))
	{
		require_once $tjStrapperPath;
		TjStrapper::loadTjAssets('com_socialads');
	}

	// Load CSS & JS resources.
	if (JVERSION > '3.0')
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$laod_boostrap=$socialads_config['load_bootstrap'];
		if(!empty($laod_boostrap))
		{
			// Load bootstrap CSS and JS.
			JHtml::_('bootstrap.loadcss');
			JHtml::_('bootstrap.framework');
		}
	}

	/*@ Need to check
	 * $namespace_js = JUri::root().'media'.DS.'techjoomla_strapper'.DS.'js'.DS.'namespace.js';
		$flg=0;
		foreach($doc->_scripts as $name=>$ar)
		{
			if($name == $namespace_js )
				$flg=1;
		}
		if($flg==0)
		$doc->addScript($namespace_js);
	*
	* */



	//require_once(JPATH_SITE . DS .'components'. DS .'com_community'. DS .'libraries'. DS .'core.php');
	// Include the syndicate functions only once
	require_once (dirname(__FILE__).DS.'helper.php');

	$helperPath=JPATH_SITE . DS .'components' .DS .'com_socialads' . DS .'helper.php';
	if(!class_exists('socialadshelper'))
	{
	   JLoader::register('socialadshelper', $helperPath );
	   JLoader::load('socialadshelper');
	}

	require_once(JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php');


	$lang =  JFactory::getLanguage();
	$lang->load('mod_socialads', JPATH_SITE);

	$moduleid = $module->id;
	$zone_id	=	$params->get('zone',0);

	$modSocialadsHelper	=	new modSocialadsHelper();
	$ad_type	=	$modSocialadsHelper->getAdtypebyZone($zone_id);
	if($params->get('create',1)){
		$socialadshelper = new socialadshelper();
		$Itemid = $socialadshelper->getSocialadsItemid('buildad');
	}
	$adRetriever = new adRetriever();
	$ads	=	$adRetriever->getAdsforZone($params,$moduleid);
	require(JModuleHelper::getLayoutPath('mod_socialads'));
}
?>

