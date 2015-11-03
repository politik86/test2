<?php 
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html) * @author  iJoomla.com webmaster@ijoomla.com
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');
// Include the helper functions only once
if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once (dirname(__FILE__).DS.'helper.php');

$doc = JFactory::getDocument();
$doc->addScript(JURI::root()."modules/mod_ijoomla_adagency_zone/includes/helper.js");

$real_ip = iJoomlaGetRealIpAddrModule();
$blacklist = getIPBlocklist();
if(in_array($real_ip, $blacklist)){
	return "";
}

// Get data from helper class
$helper= new modAdAgencyZoneHelper();
$zone_output = $helper->getParams($module);
$module_id = $module->id;

// Run default template script for output
require(JModuleHelper::getLayoutPath('mod_ijoomla_adagency_zone'));