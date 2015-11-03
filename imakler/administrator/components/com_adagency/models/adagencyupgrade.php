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

jimport ("joomla.aplication.component.model");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminModeladagencyupgrade extends JModelLegacy {
	
	function __construct () {
		parent::__construct();
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadObject();
		return $res;
	}
	
	function getZones(){
		$db = JFactory::getDBO();
		$sql = "SELECT m . * , MIN( mm.menuid ) AS pages, z . *
				FROM #__modules AS m
				LEFT JOIN #__modules_menu AS mm ON mm.moduleid = m.id
				LEFT JOIN #__ad_agency_zone AS z ON z.zoneid = m.id
				WHERE m.module = 'mod_ijoomla_adagency_zone'
				GROUP BY m.id
				ORDER BY m.id ASC
				";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		if($res != NULL){
			foreach($res as $element) {
				$element->adparams = @unserialize($element->adparams);
			}
		}
		
		return $res;
	}
	
	function getPacks(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_order_type ORDER BY tid";
		$db->setQuery($sql);
		$res = $db->loadObjectList();
		return $res;
	}
	
	function getCamps(){
		$db = JFactory::getDBO();
		if(!isset($_SESSION['limit_upgrade_cmp_1'])||($_SESSION['limit_upgrade_cmp_1'] == NULL)) { $_SESSION['limit_upgrade_cmp_1'] = 0; } 
		if(!isset($_SESSION['limit_upgrade_cmp_2'])||($_SESSION['limit_upgrade_cmp_2'] == NULL)) { $_SESSION['limit_upgrade_cmp_2'] = 5; } 
		$sql = "SELECT u.name AS company, c.id, c.name, c.quantity, c.validity, c.start_date, a.user_id, a.aid, c.type, c.approved, c.STATUS, count( DISTINCT cb.banner_id ) cnt, p.tid AS package_id, p.description, z.zoneid, z.z_title, z.adparams, usr.name AS advertiser
		FROM #__ad_agency_campaign AS c
		LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON c.id = cb.campaign_id
		LEFT JOIN #__ad_agency_advertis AS a ON a.aid = c.aid
		LEFT JOIN #__users AS u ON u.id = a.user_id
		LEFT JOIN #__ad_agency_order_type AS p ON c.otid = p.tid
		LEFT JOIN #__ad_agency_zone AS z ON p.location = z.zoneid
		LEFT JOIN #__users AS usr ON a.user_id = usr.id
		WHERE 1 = 1
		AND (((c.type = 'cpm') OR (c.type = 'pc')) AND (c.quantity >0)) 
		OR ((c.type = 'fr') AND (c.validity > NOW()))
		GROUP BY c.id
		ORDER BY c.id DESC LIMIT ".$_SESSION['limit_upgrade_cmp_1'].",".$_SESSION['limit_upgrade_cmp_2'];
		$db->setQuery($sql);
		$camps = $db->loadObjectList();
		$types2 = array();$nrzz=0;
		if(isset($camps) && (count($camps)>0)){
			foreach($camps as $camp) {
				$camp->allzones = $this->getZonesForPack($camp->package_id);
				$sql = NULL;$types = NULL;$with_size = NULL;$b_with_size = false;
				$camp->adparams = @unserialize($camp->adparams);
				//echo "<pre>";var_dump($camp->allzones);echo "</pre><hr />";//die();
				foreach($camp->allzones as $element){
					$b_with_size = false;
					$element->adparams = @unserialize($element->adparams); 
					$types = array("'a'","'b'");				
					if(isset($element->adparams)&&is_array($element->adparams)){
						foreach($element->adparams as $key=>$value) {
							if($key == 'affiliate') { $types[] = "'Advanced'"; $b_with_size = true; }
							if($key == 'textad') { $types[] = "'TextLink'"; }
							if($key == 'standard') { $types[] = "'Standard'"; $b_with_size = true; }
							if($key == 'flash') { $types[] = "'Flash'"; $b_with_size = true; }
							if($key == 'popup') { $types[] = "'Popup'"; }
							if($key == 'transition') { $types[] = "'Transition'"; }
							if($key == 'floating') { $types[] = "'Floating'"; }
						}
						
						if($b_with_size == true) { 
							if(isset($element->adparams['width'])&&($element->adparams['width'] != '')) {
								$with_size = " AND b.width ='".$element->adparams['width']."' AND b.height='".$element->adparams['height']."'"; 
							} else {
								$with_size = NULL;
							}
						} else { 
							$with_size = NULL; 
						}
						
						if(is_array($types)) {
							$types2[$nrzz] = "(b.media_type IN (".implode(',',$types).")".$with_size.")";
							$nrzz++;
						}
					}
				}
				//echo "<pre>";var_dump($types2);echo "</pre><hr />";
				if(is_array($types2)){
					$types2 = " AND (".implode(" OR ",$types2).")";
				} else { $types2 = NULL; }
				//echo "<pre>";var_dump($types2);echo "</pre><hr />";//die();
				
				$sql = "SELECT DISTINCT b.id, b.title, b.media_type, b.parameters, b.width, b.height, b.approved, cb.relative_weighting FROM #__ad_agency_banners AS b LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb on cb.campaign_id=$camp->id AND cb.banner_id=b.id WHERE b.advertiser_id=$camp->aid ".$types2;
				//echo $sql;die();
				$types2 = NULL;
				$sqlz[] = $sql;
				$db->setQuery($sql);
				$camp->banners = $db->loadObjectList();
				$cbrw = NULL;
				if(is_array($camp->banners)) {
					foreach($camp->banners as $elm) {
						$cbrw[] = $elm->id;
					}
					$cbrw = @implode(',',$cbrw);
				}
				$camp->cbrw = $cbrw;
				$camp->banners = $this->updateMediaType($camp->banners);
				$camp->banners = $this->updateZoneList($camp->banners,$camp->allzones,$camp->id);
			}
		}		
		
		return $camps;
	}
	
	function getZonesForPack($pack){
		$db = JFactory::getDBO();
		if(isset($pack)&&(intval($pack)>0)){
			$sql = "SELECT z.*
					FROM #__ad_agency_package_zone AS pz
					LEFT JOIN #__modules AS m ON pz.zone_id = m.id
					LEFT JOIN #__ad_agency_zone AS z ON m.id = z.zoneid
					WHERE pz.package_id =".intval($pack);
			$db->setQuery($sql);
			$res =  $db->loadObjectList();
			
			//echo "<pre>";var_dump($packs);die();
			return $res;
		} else {
			return NULL;
		}
	}	

	function updateMediaType($bans){
		if(isset($bans)&&(is_array($bans))){
			foreach($bans as $ban){
				$ban->media_type = strtolower($ban->media_type);
				if($ban->media_type == 'advanced') { $ban->media_type = 'affiliate'; }
				if($ban->media_type == 'textlink') { $ban->media_type = 'textad'; }
			}
		}
		
		return $bans;
	}

	function updateZoneList($bans,$zones,$camp_id = 0){
		$db = JFactory::getDBO();
		if(isset($bans) && isset($zones)){
			foreach($bans as $ban){
				if(isset($camp_id)&&(intval($camp_id)>0)&&isset($ban->id)&&(intval($ban->id)>0)){
					$sql = "SELECT zone FROM #__ad_agency_campaign_banner WHERE banner_id = ".intval($ban->id)." AND campaign_id = ".intval($camp_id);
					$db->setQuery($sql);
					$sel_zone = $db->loadResult();
				} else {
					$sel_zone = NULL;
				}
				$ban->zones = "<select class='w145' name='adzones[".$camp_id."][".$ban->id."]' id='adzones_".$ban->id."'>";
				$ban->zones .= "<option value='0'>".JText::_("ADAG_ZONE_FOR_AD")."</option>";
				foreach($zones as $zone){
					$types = NULL; $b_with_size = false;
					foreach($zone->adparams as $key=>$value){
						if($key == 'affiliate') { $types[] = $key; $b_with_size = true; }
						if($key == 'textad') { $types[] = $key; }
						if($key == 'standard') { $types[] = $key; $b_with_size = true; }
						if($key == 'flash') { $types[] = $key; $b_with_size = true; }
						if($key == 'popup') { $types[] = $key; }
						if($key == 'transition') { $types[] = $key; }
						if($key == 'floating') { $types[] = $key; }
					}
					if(in_array($ban->media_type,$types)) {
						if((!isset($zone->adparams['width'])||($zone->adparams['width'] == '')||!isset($zone->adparams['height'])||($zone->adparams['height'] == ''))||($b_with_size == false)){
							if($sel_zone == $zone->zoneid) { $selected = " selected='selected' "; } else { $selected = NULL;}
							$ban->zones .= "<option value='".$zone->zoneid."' ".$selected.">".$zone->z_title."</option>";
						} elseif(($b_with_size == true)&&($zone->adparams['width'] == $ban->width)&&($zone->adparams['height'] == $ban->height)){
							if($sel_zone == $zone->zoneid) { $selected = " selected='selected' "; } else { $selected = NULL;}
							$ban->zones .= "<option value='".$zone->zoneid."' ".$selected.">".$zone->z_title."</option>";
						}
					}
				}
				$ban->zones .= "</select>";
			}
		}
		//echo "<pre>";var_dump($bans);die();
		return $bans;
	}
		
	function upgradezones(){
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$sqlz = NULL;
		//echo "<pre>";var_dump($data);echo "<hr />";
		if(isset($data['zone'])){
			foreach($data['zone'] as $element=>$value){
				if(isset($value['adparams']['width']) && isset($value['adparams']['height'])){
					if((intval($value['adparams']['width']) == 0)||(intval($value['adparams']['height']) == 0)) { $value['adparams']['width'] = ""; $value['adparams']['height'] = ""; }
				}				
				if(isset($value['adparams'])) {	$aux = @serialize($value['adparams']); }
				if(isset($element)&&(isset($aux))){
					$sql = "UPDATE `#__ad_agency_zone` SET `adparams` = '".$aux."' WHERE `zoneid` =".intval($element).";";
					$db->setQuery($sql);
					$db->query();
					$sqlz[] = $sql;
				}			
			}
		}
		//echo "<pre>";var_dump($sqlz);die();
	}
	
	function upgradepack(){
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$sqlz = NULL;
		if(isset($data['pack'])) {
			if(is_array($data['pack'])) {
				foreach($data['pack'] as $key=>$value){
					if(is_array($value)) {
						foreach($value as $element){
							$sql ="INSERT INTO `#__ad_agency_package_zone` (`package_id` ,`zone_id`) VALUES ('".intval($key)."', '".intval($element)."');";
							$db->setQuery($sql);
							$db->query();
							$sqlz[] = $sql;
						}
					}
				}
			}
		}
		//echo "<pre>";var_dump($sqlz);die();
	}	
	
	function upgradecamp(){
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		//echo "<pre>";var_dump($data);echo "<hr />";//die();
		if(isset($data['bnrs']['del']))
		foreach($data['bnrs']['del'] as $key=>$value){
			foreach($value as $val){
				$sqlz[] = "DELETE FROM `#__ad_agency_campaign_banner` WHERE `campaign_id` = ".intval($key)." AND `banner_id` = ".intval($val);
				$db->setQuery($sql);
				$db->query();
			}
		}
		if(isset($data['bnrs']['add']))
		foreach($data['bnrs']['add'] as $key=>$value){
			foreach($value as $val){
				if(!isset($data['adzones'][$key][$val])) { $data['adzones'][$key][$val] = NULL; }
				$sql = "INSERT INTO `#__ad_agency_campaign_banner` (`campaign_id` ,`banner_id` ,`relative_weighting` ,`thumb`, `zone`) VALUES (
						'".$key."', '".$val."', '".$data['cmps'][$key][$val]['rw']."', NULL, ".$data['adzones'][$key][$val].");";
				$sqlz[] = $sql;
				$db->setQuery($sql);
				$db->query();
			}
		}
		if(isset($data['cbrw']))
		foreach($data['cbrw'] as $key=>$val){
			if(($val != NULL) && ($val != '')) {
				$sql = "DELETE FROM `#__ad_agency_campaign_banner` WHERE `campaign_id` = ".intval($key)." AND `banner_id` NOT IN (".$val.")";
				$sqlz[] = $sql;
				$db->setQuery($sql);
				$db->query();
			}
		}
		
		if(isset($data['camp_ids'])&&(is_array($data['camp_ids']))){
			foreach($data['camp_ids'] as $val){
				if(isset($data['adzones'][$val])&&(is_array($data['adzones'][$val]))){ 
					foreach($data['adzones'][$val] as $key=>$val2){
						$sql = "UPDATE `#__ad_agency_campaign_banner` SET `zone` = '".$val2."' WHERE `campaign_id` = ".intval($val)." AND `banner_id` = ".intval($key).";";
						$db->setQuery($sql);
						$db->query();
					}
				}
			}
		}

		$_SESSION['limit_upgrade_cmp_1'] += 5;
		$_SESSION['limit_upgrade_cmp_2'] += 5;
		return true;
	}

};
?>