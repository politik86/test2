 <?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

require_once('components/com_adagency/helpers/legacy.php');
global $mainframe;
$mainframe = JFactory::getApplication();
//check for access
$my = JFactory::getUser();
$doc = JFactory::getDocument();

// Load uikit framework
$doc->addStyleSheet(JURI::root().'components/com_adagency/includes/css/uikit.almost-flat.min.css');

// Load Ad-Agency styles
$doc->addStyleSheet(JURI::root().'components/com_adagency/includes/css/ad-agency.css');
$doc->addStyleSheet(JURI::root().'components/com_adagency/includes/css/font-awesome.min.css');

$database =  JFactory :: getDBO();
$sql = "select `params` from #__ad_agency_settings";
        $database->setQuery( $sql );
		$configs = $database->loadColumn();
	 	$configs = $configs['0'];
		@$configs = @unserialize($configs);
if(!isset($configs['jquery_front']) || @$configs['jquery_front'] == "0"){
	$doc->addScript( JURI::root()."components/com_adagency/includes/js/jquery.noConflict.js" );
	$doc->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
}

//$doc->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
//$doc->addScript( JURI::root()."components/com_adagency/includes/js/jquery.noConflict.js" );

$meniu=0;
$task = JRequest::getVar('task', '');
$control = JRequest::getVar('controller', '', 'get');
$available_controllers = array('adagencyAdcode','adagencyAds','adagencyAdvertisers','adagencyCampaigns','adagencyCPanel','adagencyFlash','adagencyFloating','adagencyOrders','adagencyPackages','adagencyPopup','adagencyReports','adagencySajax','adagencyStandard','adagencyTextlink','adagencyTransition','adagencyJomsocial');

require_once (JPATH_COMPONENT.DS.'controller.php');
require_once( JPATH_COMPONENT.DS.'helpers'.DS.'helper.php' );
if(!class_exists('Mobile_Detect')){
	require_once(JPATH_BASE . "/components/com_adagency/helpers/Mobile_Detect.php");
}
$controller = JRequest::getWord('controller');
if(!in_array($controller,$available_controllers)) { $controller = '';}
$view = JRequest::getWord('view');
$layout = JRequest::getWord('layout');
if(($task =='')&&($layout!='')) {
	if(strtolower($layout)=='editform') {$layout = 'edit';}
	$task = $layout;
}

if ($controller) {
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once($path);
	} else {
	}
} else {
	if($view) { 
		$controller='adagency'.ucfirst(str_replace('adagency','',$view)); 
		if($view == 'adagencycpanel') {$controller = 'adagencyCPanel';}
		if($view == 'adagencypackage') {$controller = 'adagencyPackages';}
	} else {	$controller = 'adagencyPackages'; }
	$specialCases = array('adagencyflash','adagencyadcode','adagencyfloating','adagencypopup','adagencystandard','adagencytextlink','adagencytransition','adagencyjomsocial');
	if(in_array(strtolower($controller),$specialCases)&&(($task=='')||($task=='default'))) { $task='edit'; }
	if((strtolower($controller) == 'adagencyadvertisers')&&(($task == 'default')||($task == ''))) {
		$task = 'edit';
	}
	$path = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
	if (file_exists($path)) {
		require_once($path);
	} else {
	}
}

JHtml::_('behavior.framework',true);
$ajax_req = JRequest::getVar("no_html", 0, "request");

if($ajax_req == 0 && $task != "campaigns_csv" && $task != "campaigns_pdf" && $task != "target" && $task != "remote_ad" && $task != "captcha" && $task != "rotator"){
?>
<div id="adagency_container" class="ij_adagency ij_adagency_container clearfix">
<?php
}

$classname = "adagencyController".$controller;
if(class_exists($classname)){
	$controller = new $classname();
	$controller->execute($task);
	$controller->redirect();
}
	
if($ajax_req == 0 && $task != "campaigns_csv" && $task != "campaigns_pdf" && $task != "target" && $task != "remote_ad" && $task != "captcha" && $task != "rotator"){
?> 
</div>
<?php
}
?>