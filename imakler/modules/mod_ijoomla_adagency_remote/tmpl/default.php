<?php 
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html) * @author  iJoomla.com webmaster@ijoomla.com
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
defined('_JEXEC') or die('Restricted access');
	$doc = JFactory::getDocument();
	$doc->addStyleSheet('modules/mod_ijoomla_adagency_remote/tmpl/mod_adagencyremote.css');
	
	$suffix = $params->get('moduleclass_sfx', '');
	$params->set('moduleclass_sfx', '');
	
	$site_url = trim($parameters["0"]);
	if(substr($site_url, -1) != "/"){
		$site_url .= "/";
	}
	
	echo '<div class="adagency_remote_container '.$suffix.'">';
	echo '<script type="text/javascript" language="javascript" src="'.$site_url.'index.php?option=com_adagency&controller=adagencyAds&task=remote_ad&tmpl=component&format=raw&zid='.intval($parameters[1]).'"></script>';
	echo '</div>';
?>