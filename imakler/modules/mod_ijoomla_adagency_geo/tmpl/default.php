<?php 
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html) * @author  iJoomla.com webmaster@ijoomla.com
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0 
 * More info at http://www.ijoomla.com/licensing/
*/
//no direct access
	defined('_JEXEC') or die('Restricted access');
	$doc = JFactory::getDocument();
	$doc->addStyleSheet('modules/mod_ijoomla_adagency_geo/tmpl/mod_ijoomla_adagencygeo.css');
		 
	echo "<div class='mod_adagencygeo'>";
	echo $geo_output;
	echo "</div>";
?>