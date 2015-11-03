<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
if(!defined('DS')){
	define('DS',DIRECTORY_SEPARATOR);
	}

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_socialads')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// Load js assets
jimport('joomla.filesystem.file');
$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

if (JFile::exists($tjStrapperPath))
{
	require_once $tjStrapperPath;
	TjStrapper::loadTjAssets('com_socialads');
}

$document = JFactory::getDocument();
// Load CSS & JS resources.
if (JVERSION > '3.0')
{
	// Load bootstrap CSS and JS.
	JHtml::_('bootstrap.loadcss');
	JHtml::_('bootstrap.framework');

	// Load TJ namespace JS.
	$document->addScript(JUri::root() . 'media/techjoomla_strapper/js/namespace.js');
}
else
{
	// Load bootstrap CSS.
	$document->addStylesheet(JUri::root() . 'media/techjoomla_strapper/css/bootstrap.min.css');
	$document->addStylesheet(JUri::root() . 'media/techjoomla_strapper/css/bootstrap-responsive.min.css');
	$document->addStylesheet(JUri::root() . 'media/techjoomla_strapper/css/strapper.css');

	// Load TJ jQuery.
	$document->addScript(JUri::root() . 'media/techjoomla_strapper/js/akeebajq.js');

	// Load bootstrap JS.
	$document->addScript(JUri::root() . 'media/techjoomla_strapper/js/bootstrap.min.js');

	// Load TJ namespace JS.
	$document->addScript(JUri::root() . 'media/techjoomla_strapper/js/namespace.js');
}

$document->addScript(JUri::root().'components'.DS.'com_socialads'.DS.'js'.DS.'socialads.js');
$document->addScript(JUri::base().'components'.DS.'com_socialads'.DS.'js'.DS.'adminsocialads.js');
$document->addStylesheet( JUri::base().'components'.DS.'com_socialads'.DS.'css'.DS.'yes_no_toggle.css' );
$document->addScript( JUri::base().'components'.DS.'com_socialads'.DS.'js'.DS.'yes_no_toggle.js' );
$document->addStyleSheet(JUri::base().'components'.DS.'com_socialads'.DS.'css'.DS.'socialads.css');
$document->addStyleSheet(JUri::root().'components'.DS.'com_socialads'.DS.'css'.DS.'socialads.css');


require_once( JPATH_COMPONENT.DS.'config'.DS.'config.php' ); // TODO: Use helper to get config data
if(JVERSION>=3.0)
{
	   JHtml::_('bootstrap.tooltip');
	   JHtml::_('behavior.multiselect');
	  JHtml::_('formbehavior.chosen', 'select');
}
// Require the base controller.
require_once( JPATH_COMPONENT.DS.'controller.php' );
$input=JFactory::getApplication()->input;
// Require specific controller if requested
if( $controller = JRequest::getWord('controller'))
{
   $path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if( file_exists($path))
	{
		require_once $path;
	} else
	{
		$controller = '';
	}
}

//Load Helper

$path = JPATH_SITE . '/components/com_socialads/helpers.php';

if (!class_exists('socialadshelper'))
{
	JLoader::register('socialadshelper', $path);
	JLoader::load('socialadshelper');
}

$helperPath=JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'helpers'.DS.'createad.php';
if(!class_exists('createAdHelper'))
{
  //require_once $path;
   JLoader::register('createAdHelper', $helperPath );
   JLoader::load('createAdHelper');
}

// Create the controller
$classname    = 'socialadsController'.$controller;
$controller   = new $classname( );

// Perform the Request task
$controller->execute( $input->get( 'task' ) );

// Redirect if set by the controller
$controller->redirect();
