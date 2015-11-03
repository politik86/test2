<?php

// no direct access
defined( '_JEXEC' ) or die( ';)' );


if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
}

$app=JFactory::getApplication();
$doc =JFactory::getDocument();

// Load CSS & JS resources.
if (JVERSION > '3.0')
{
	require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
	$laod_boostrap=$socialads_config['load_bootstrap'];
	if(!empty($laod_boostrap))
	{
		// Load bootstrap CSS.
		JHtml::_('bootstrap.loadcss');
	}
}

// Load js assets
jimport('joomla.filesystem.file');
$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

if (JFile::exists($tjStrapperPath))
{
	require_once $tjStrapperPath;
	TjStrapper::loadTjAssets('com_socialads');
}

$doc->addStyleSheet(JUri::root().'components'.DS.'com_socialads'.DS.'css'.DS.'socialads.css');
$doc->addScript(JUri::root().'components/com_socialads/js/socialads.js');
$doc->addScript(JUri::root().'components/com_socialads/js/managead.js');
//Load Helper

$helperPath=JPATH_COMPONENT . DS . 'helper.php';
if(!class_exists('socialadshelper'))
{
   JLoader::register('socialadshelper', $helperPath );
   JLoader::load('socialadshelper');
}

$helperPath=JPATH_ROOT.DS.'components'.DS.'com_socialads'.DS.'adshelper.php';
if(!class_exists('adRetriever'))
{
   JLoader::register('adRetriever', $helperPath );
   JLoader::load('adRetriever');
}

$helperPath=JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'helpers'.DS.'createad.php';
if(!class_exists('createAdHelper'))
{
   JLoader::register('createAdHelper', $helperPath );
   JLoader::load('createAdHelper');
}

// Require the base controller
require_once( JPATH_COMPONENT.DS.'controller.php' );
$input=JFactory::getApplication()->input;

$view   = $input->get('view', 'billing','STRING');
//$controller_default   = JRequest::getCmd('controller', 'buildad');
$input->get('view',$view,'STRING');
//JRequest::setVar('controller',$controller_default);

// Require specific controller if requested
if( $controller = JRequest::getWord('controller'))
{
   $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
   if( file_exists($path))
	{
       require_once $path;
   	}
   else
   	{
       $controller = '';
   	}
}

$classname    = 'socialadsController'.$controller;//die;
$controller   = new $classname( );

// Perform the Request task
$controller->execute( $input->get('task','','STRING') );

// Redirect if set by the controller
$controller->redirect();
