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
	$db = JFactory::getDBO();
	$sql = "SELECT * FROM #__ad_agency_zone WHERE zoneid=".intval($module_id)." LIMIT 1";
	$db->setQuery($sql);
	$zoneSettings = $db->loadAssocList();
	
	$zone_text_below = $zoneSettings["0"]["zone_text_below"];
	
	$zone_text_below = '<div class="adg_row adg_content_message"><div class="zone-text-below">'.$zone_text_below."</div></div>";
	
	$zone_content_location = $zoneSettings["0"]["zone_content_location"];
	$zone_content_visibility = $zoneSettings["0"]["zone_content_visibility"];
	if($zone_content_visibility == 0){
		$my = JFactory::getUser();
		if($my->id == 0){
			$zone_text_below = "";
		}
		else{
			$sql = "select count(*) from #__ad_agency_advertis where `user_id`=".intval($my->id);
			$db->setQuery($sql);
			$db->query();
			$count = $db->loadColumn();
			$count = @$count["0"];
			if(intval($count) == 0){
				$zone_text_below = "";
			}
		}
	}
	
	$doc = JFactory::getDocument();
	$doc->addStyleSheet(JURI::root() . 'modules/mod_ijoomla_adagency_zone/tmpl/mod_ijoomlazone.css');
	echo "<div class='mod_ijoomlazone' id='ijoomlazone".$module_id."'><div class='adg_row'><div class='adg_cell'>";
	if($zone_content_location == 1){
		echo $zone_text_below . $zone_output;
	}
	else{
		echo $zone_output . $zone_text_below;
	}
	echo "</div></div></div>";
?>