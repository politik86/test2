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

class modAdAgencyRemoteHelper
{
	function getParams(&$params)
	{
	//website url
	$parameters[0] = $params->get( 'website_url', '' );
	//zone id
	$parameters[1] = $params->get( 'zone_id', '' );
	/*//module class suffix
	$parameters[2] = $params->get( 'moduleclass_sfx', '' );*/
	
	return $parameters;
	}
}
?>