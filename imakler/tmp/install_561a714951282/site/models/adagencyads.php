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
require_once("components/com_adagency/helpers/helper.php");

class adagencyModeladagencyAds extends JModelLegacy {
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');

		$this->setId((int)$cids[0]);
		global $mainframe, $option;

		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_license = null;
	}

	function getCurrentAdvertiser(){
		$db = JFactory::getDBO();
		$my = JFactory::getUser();
		$sql = "SELECT * FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getListAds();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

    // getCampLimInfo() returns an array like:
    //  [campaign id]=>
    //  array(2) {
    //      ["occurences"]=> ..
    //      ["adslim"]=> ..
    //  }
    // ..
    function getCampLimInfo($advertiser_id = 0) {
        $db = JFactory::getDBO();
        $camps = array();
        $bid = JRequest::getInt('bid', '0', 'get');

        $sql = "SELECT id, params FROM `#__ad_agency_campaign` ";
        $db->setQuery($sql);
        $res = $db->loadObjectList();

        if ( is_array($res) ) {
            foreach($res as $element) {
                $tmp = @unserialize($element->params);
                if ( isset($tmp['adslim']) ) {
                    $adslim = $tmp['adslim'];
                } else {
                    $adslim = 999;
                }
                $camps[$element->id] = $adslim;
            }
        }

        if ($advertiser_id == 0 ) {
            $current_adv = $this->getCurrentAdvertiser()->aid;
        } else {
            $current_adv = $advertiser_id;
        }
        $sql = "
            SELECT DISTINCT cb.campaign_id, COUNT( cb.campaign_id ) AS occurences
            FROM `#__ad_agency_campaign_banner` AS cb
            LEFT JOIN `#__ad_agency_campaign` AS c
            ON c.id = cb.campaign_id
            WHERE c.aid =".intval($current_adv);
        if ($bid != 0) {
            $sql .= " AND cb.banner_id <> ".intval($bid);
        }
        $sql .= " GROUP BY cb.campaign_id";

        //echo $sql;die();

        $db->setQuery($sql);
        $res = $db->loadObjectList();

        $key_val = array();
        if ( is_array($res) ) {
            foreach ($res as $element) {
                $tmp_array['occurences'] = $element->occurences;
                $tmp_array['adslim'] = $camps[$element->campaign_id];
                $key_val[$element->campaign_id] = $tmp_array;
            }
        }
        return $key_val;
    }

	function rememberChannel(){
		$data = JRequest::get('post');
		//echo "<pre>";var_dump($data);die();
		if(isset($data['geo_type'])&&($data['geo_type'] == 1)){
			if(isset($data['limitation'])&&($data['limitation'] != '')) {
				$temp = NULL;
				$region_city_exist = false;
				if(is_array($data['limitation'])){
					foreach($data['limitation'] as $element) {
						if(($element['type'] == 'region')||($element['type'] == 'city')) {
							$region_city_exist = true;
						} elseif($element['type'] == 'country') {
							$the_country = array($element['data'][0]);
						}
					}
					foreach($data['limitation'] as $element) {
						if(($element['type'] == 'region')||($element['type'] == 'city')) {
							$element['data'] = array_merge($the_country, $element['data']);
						} elseif (($element['type'] == 'country')&&($region_city_exist)) { continue; }
			//			$temp[] = "( NULL , '".$channel_id."', '".$element['type']."','AND','".$element['option']."', '".json_encode($element['data'])."' )";
						$ctemp = new stdClass();
						$ctemp->type = $element['type'];
						$ctemp->data = json_encode($element['data']);
						$_SESSION['channelz'] = $ctemp;
			//			echo "1";die();
					}
				}
			}
		} elseif(isset($data['geo_type'])&&($data['geo_type'] == 2)){
			if(isset($data['limitation_existing'])&&($data['limitation_existing'] != 0)) {
				$_SESSION['channelz2'] = $data['limitation_existing'];
		//		echo "2";die();
			}
		}

		return true;
	}

	function getChannelInfo(){
		require_once(JPATH_BASE.'/components/com_adagency/helpers/geoipotherdata.php');
		require_once(JPATH_BASE.'/components/com_adagency/helpers/geoipregionvars.php');
		if (!function_exists('json_encode')) {
			require_once(JPATH_BASE.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');
		}
		//echo "<pre>";var_dump($HELPER_GEOIP_CONTINENTS);//die();
		$db = JFactory::getDBO();
		$cid = JRequest::getInt('cid', 0);
		$output = "<table>";
		if(isset($cid)&&($cid != 0)) {
			$sql = "SELECT `type`, `logical`, `option`, `data` FROM `#__ad_agency_channel_set` WHERE channel_id = ".intval($cid)." ORDER BY id ASC";
			$db->setQuery($sql);
			$result = $db->loadObjectList();
			
			if(isset($result)) {
				$counter = 0;
				foreach($result as $element){
					$counterREG = 0;$aux = NULL;$values = NULL;$not_any = NULL;
					$element->data = json_decode($element->data);
					
					if($counter>=1) {
						$output.="<tr><th align='left'>".JText::_('ADAG_'.$element->logical)."</th></tr>";
					}
					if($element->type == 'continent') {
						foreach($element->data as &$val){
							$val = $HELPER_GEOIP_CONTINENTS[$val];
						}
						$values = implode(', ',$element->data);
						$output.= "<tr><td>".JText::_('ADAG_CONTINENT').": ".$values."</tr></td>";
					} elseif($element->type == 'country'){
						foreach($element->data as &$val){
							$val = $HELPER_GEOIP_COUNTRIES[$val];
						}
						$values = implode(', ',$element->data);
						if($element->option != 'is') { $not_any = JText::_('ADAG_NOT_ANY'); }
						$output.= "<tr><td>".JText::_('ADAG_COUNTRY').$not_any.": ".$values."</tr></td>";
					} elseif($element->type == 'region'){
						if(isset($element->data)){
							if(isset($element->data["1"])){
								$zones = explode(",", $element->data["1"]);
								unset($element->data["1"]);
								foreach($zones as $key_zone=>$value_zone){
									if(trim($value_zone) != ""){
										$element->data[] = $value_zone;
									}
								}
							}
							
							foreach($element->data as &$val){
								if($counterREG == 0){
									$aux = $val;
									$val = $HELPER_GEOIP_COUNTRIES[$val];
								} else {
									$val = $GEOIP_REGION_NAME[$aux][$val];
								}
								$counterREG++;
							}
						}
						if($element->option != 'is') { $not_any = JText::_('ADAG_NOT_ANY'); }
						$output.= "<tr><td>".JText::_('ADAG_COUNTRY').": ".$element->data[0]."<br />";
						$element->data[0] = NULL;unset($element->data[0]);
						$values = implode(', ',$element->data);
						$output.= JText::_('ADAG_REGION').$not_any.": ".$values;
						$output.= "</tr></td>";
					} elseif($element->type == 'city'){
						if($element->option == '==') { $not_any = JText::_('ADAG_IS_EQUAL'); }
						elseif($element->option == '!=') { $not_any = JText::_('ADAG_IS_DIFFERENT'); }
						elseif($element->option == '=~') { $not_any = JText::_('ADAG_CONTAINS'); }
						elseif($element->option == '!~') { $not_any = JText::_('ADAG_NOT_CONTAINS'); }
						$output.= "<tr><td>".JText::_('ADAG_COUNTRY').": ".$HELPER_GEOIP_COUNTRIES[$element->data[0]]."<br />";
						$values = $element->data[1];
						$output.= JText::_('ADAG_CITY').$not_any.": ".$values;
						$output.= "</tr></td>";
					} elseif($element->type == 'dma'){
						if($element->option != 'is') { $not_any = JText::_('ADAG_NOT_ANY'); }
						foreach($element->data as &$val){
							$val = $HELPER_GEOIP_DMA[$val];
						}
						$values = implode(', ',$element->data);
						$output.= "<tr><td>".JText::_('ADAG_DMA').$not_any.": ".$values."</td></tr>";
					} elseif($element->type == 'latitude'){
						if($element->option == '==') { $not_any = JText::_('ADAG_BETWEEN'); } else { $not_any = JText::_('ADAG_NOT_BETWEEN'); }
						$output.= "<tr><td>".JText::_('ADAG_LATLONG').$not_any.": <br />";
						$output.= $element->data->a." ".JText::_('ADAG_AND')." ".$element->data->b."<br />";
						$output.= $element->data->c." ".JText::_('ADAG_AND')." ".$element->data->d."<br />";
						$output.= "</td></tr>";
					} elseif($element->type == 'usarea'){
						if($element->option == '==') { $not_any = JText::_('ADAG_IS_EQUAL'); }
						elseif($element->option == '!=') { $not_any = JText::_('ADAG_IS_DIFFERENT'); }
						elseif($element->option == '=~') { $not_any = JText::_('ADAG_CONTAINS'); }
						elseif($element->option == '!~') { $not_any = JText::_('ADAG_NOT_CONTAINS'); }
						$output.= "<tr><td>".JText::_('ADAG_USAREA').": ".$element->data[0]."</tr></td>";
					} elseif($element->type == 'postalcode'){
						if($element->option == '==') { $not_any = JText::_('ADAG_IS_EQUAL'); }
						elseif($element->option == '!=') { $not_any = JText::_('ADAG_IS_DIFFERENT'); }
						elseif($element->option == '=~') { $not_any = JText::_('ADAG_CONTAINS'); }
						elseif($element->option == '!~') { $not_any = JText::_('ADAG_NOT_CONTAINS'); }
						$output.= "<tr><td>".JText::_('ADAG_POSTAL_COD').": ".$element->data[0]."</tr></td>";
					}
					$counter++;
				}
			}
			$output.= "</table>";
			//echo "<pre>";var_dump($result);die();
			echo $output;
		}
	}

	function getChannel(){
		$db = JFactory::getDBO();
		$bid = JRequest::getInt('bid', 0);
		if(isset($bid)&&($bid != 0)) {
			$sql = "SELECT * FROM `#__ad_agency_channels` WHERE id =".intval($bid)." LIMIT 1";
			$db->setQuery($sql);
			$result = $db->loadObject();
			if(!isset($result->id)) { return NULL; }
			$sql = "SELECT s.type, s.option, s.logical, s.data FROM `#__ad_agency_channels` AS c, `#__ad_agency_channel_set` AS s WHERE c.id = ".intval($result->id)." AND c.id = s.channel_id ORDER BY s.id ASC";
			$db->setQuery($sql);
			$result->sets = $db->loadObjectList();
			$current = $result;
			if(isset($current)) {
				$counter = 1;
				$output = "ADAG(document).ready(function(){";
				if(isset($current->sets)){
					foreach($current->sets as $element) {
						$out_p = NULL;$temp_set = NULL;
						$element->data = json_decode($element->data);
						if(is_array($element->data)) {
							$temp_set = "'".implode('|', $element->data)."'";
						} else { $temp_set = 'null'; }
						if(($element->type != 'region')&&($element->type != 'city')) {
							$output .= " var aux = ".$temp_set."; ";
							$output .= " selim('".$element->type."', aux);";
						} else {
							$output .= " selim('country', '".$element->data[0]."');";
							$output .= " ADAG('#country_container').show(); ";
							$temp_set = "'".implode('|', $element->data)."'";

							$output .= " var aux = ".$temp_set."; ";

							if($element->type == 'region') {
								$output .= " ADAG('#secondOption').prop('checked','checked');
											 ADAG('<table id=\"region_container\">').insertAfter(ADAG('#secondOption').next('label'));
											 ADAG('#city_container').remove();
											 selim('region', aux);
								 ";
							} else {
								$output .= " ADAG('#thirdOption').prop('checked','checked');
											 ADAG('<table id=\"city_container\">').insertAfter(ADAG('#thirdOption').next('label'));
											 ADAG('#region_container').remove();
											 selim('city');
											 ADAG('.city input').val('".$element->data[1]."');
											 ";
							}
						}
						switch($element->type) {
							case 'country':
								$output .= "ADAG('#country_container').show(); ADAG('#firstOption').prop('checked','checked');";
								break;
							case 'city':
								if(isset($element->data[0]) && isset($element->data[1])) {
									$output .= " ADAG('#limitation-".$counter."city').val('".$element->data[1]."');";
								}
								break;
							case 'latitude':
								$out_p.= "
									if(index == 0) { ADAG(this).val('".$element->data->a."'); }
									else if(index == 1) { ADAG(this).val('".$element->data->b."'); }
									else if(index == 2) { ADAG(this).val('".$element->data->c."'); }
									else if(index == 3) { ADAG(this).val('".$element->data->d."'); }
								";
								$output .= " ADAG('.latitude td:eq(1)').find('input').each(function(index){".$out_p."}); ";
								break;
							case 'postalcode':
								$output .= " ADAG('.postalcode td:eq(1)').find('input').val('".$element->data[0]."'); ";
								break;
							case 'usarea':
								$output .= " ADAG('.usarea td:eq(1)').find('input').val('".$element->data[0]."'); ";
								break;
							default:
								break;
						}

						$counter++;
					}
				}
				$output .= "}); ";
			}
			echo $output;die();
		} else {
			$result = NULL;
		}
	}

	function getCampsByParams(){
		$data = JRequest::get('get');
		$db = JFactory::getDBO();
		if(isset($data['aid']) && (intval($data['aid'])>0) && isset($data['adm']) && ($data['adm']='Z5GsFeQ2')){
			$advertiser_aid = $data['aid'];
		} elseif(isset($data['adm'])&&($data['adm']='Z5GsFeQ2')){
			die();
		} elseif(isset($data['aid']) && intval($data['aid']) > 0){
			$advertiser_aid = $data['aid'];
		}
		else {
			$advertiser = $this->getCurrentAdvertiser();
			$advertiser_aid = $advertiser->aid;
		}
		
		if(isset($advertiser_aid)&&(isset($data['type']))&&(isset($data['width']))&&(isset($data['height']))){
			$sql = "SELECT c.id, c.name, z.*
					FROM #__ad_agency_campaign AS c
					LEFT JOIN #__ad_agency_order_type AS o
					ON c.otid = o.tid
					LEFT JOIN #__ad_agency_package_zone AS pz
					ON pz.package_id = o.tid
					LEFT JOIN #__ad_agency_zone AS z
					ON z.zoneid = pz.zone_id
					WHERE c.aid = ".intval($advertiser_aid)." AND z.adparams LIKE '%".$data['type']."%'";

			$db->setQuery($sql);
			$camps = $db->loadObjectList();
			if(is_array($camps)) {
				foreach ($camps as &$camp){
					$camp->adparams = @unserialize($camp->adparams);
				}
			}
			//echo "<pre>";var_dump($camps);die();
			$displayed[] = NULL;
			if($camps != NULL){
				$camps2 = array();
				foreach ($camps as &$camp){
					//echo "<pre>";var_dump($camp->adparams);echo "<hr />";
					if((!isset($camp->adparams['width']))||(!isset($camp->adparams['height']))||($camp->adparams['height'] == '')||($camp->adparams['width'] == '')) {
						if(!in_array($camp->id,$displayed)) {
							$zz = $this->getAllZonesForCampByCampId($camps,$camp->id,$data['width'],$data['height']);
							$oz = NULL;
							//echo "<pre>";var_dump($zz);echo "<hr />";
							foreach($zz as $zone){
								$oz[] = $zone['zoneid']."=".$zone['z_title'];
							}
							$oz = implode(";",$oz);

							$camps2[] = $camp->id."@".$camp->name."*".$oz;
							$displayed[] = $camp->id;
						}
					} elseif(($data['width'] != $camp->adparams['width'])||($data['height'] != $camp->adparams['height'])) {
						$camp = NULL;
					} else {
						$zz = $this->getAllZonesForCampByCampId($camps,$camp->id,$data['width'],$data['height']);
						//echo "<pre>";var_dump($zz);echo "<hr />";
						$oz = NULL;
						foreach($zz as $zone){
							$oz[] = $zone['zoneid']."=".$zone['z_title'];
						}
						$oz = implode(";",$oz);

						if(!in_array($camp->id,$displayed)) {
							$camps2[] = $camp->id."@".$camp->name."*".$oz;
							$displayed[] = $camp->id;
						}
					}
				}
				if(!empty($camps2)) {
					$camps2 = @implode('|',$camps2);
					echo $camps2;die();
				}
			}
			die();
		}
	}

	function getAllZonesForCampByCampId($camps, $camp_id, $w = 0, $h = 0){
		$resp = array();
		//echo "<pre>";var_dump($camps);die();
		if(isset($camps)&&(is_array($camps))){
			$i = 1;
			foreach($camps as $camp){
				if((isset($camp->id))&&($camp->id == $camp_id)) {
					if((($camp->adparams['width'] == '')||($camp->adparams['height'] == ''))||(($w == $camp->adparams['width']) && ($h == $camp->adparams['height']))){
						$resp[$i]['zoneid'] = $camp->zoneid;
						$resp[$i]['z_title'] = $camp->z_title;
						//echo $camp->z_title."<hr />";
					}
					$i++;
				}
			}
		}
		//die();
		return $resp;
	}

	function getlistAds(){
		$my =  JFactory::getUser();
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		
		if (empty ($this->_licenses)) {
			$database->setQuery("SELECT aid FROM #__ad_agency_advertis WHERE user_id=".intval($my->id));
			$advertiser_id = $database->loadResult();
			if (!$advertiser_id) return;
			
			$and = "";
			$search_text = JRequest::getVar("search_text", "");
			$type_select = JRequest::getVar("type_select", "all");
			
			if(trim($search_text) != ""){
				$and .= " and b.`title` like '%".$search_text."%'";
			}
			
			if(trim($type_select) != "all"){
				$and .= " and b.`media_type`='".$type_select."'";
			}
			
			$sql = "select a.aid as advertiser_id2, a.company as advertiser, concat(width,'x',height) as size_type, b.* FROM #__ad_agency_banners as b 
						left outer join #__ad_agency_advertis as a on b.advertiser_id = a.aid  
					WHERE advertiser_id =".intval($advertiser_id).$and." ORDER BY b.ordering ASC, b.id DESC";
			
			$camp_id = JRequest::getVar("camp_id", "0");
			if(intval($camp_id) > 0){
				$and .= " AND cb.`campaign_id`=".intval($camp_id);
				$sql = "select a.aid as advertiser_id2, a.company as advertiser, concat(width,'x',height) as size_type, b.* FROM #__ad_agency_banners as b 
						left outer join #__ad_agency_advertis as a on b.advertiser_id = a.aid 
						left outer join #__ad_agency_campaign_banner cb on b.`id`=cb.`banner_id` 
					WHERE advertiser_id =".intval($advertiser_id).$and." ORDER BY b.ordering ASC, b.id DESC";
			}
			
		$this->_licenses = $this->_getList($sql);

		$rows = $this->_licenses;
		$ids = array();
		foreach($rows as $row){
			array_push($ids, $row->id);
		}
		
		$ids_array = $ids;
		$ids = implode(",", $ids);

		if($ids == ""){
			$ids = "null";
		}
		
		$sql = "select * from #__ad_agency_statistics";
		$db->setQuery($sql);
		$db->query();
		$statistics = $db->loadAssocList();
		
		if(isset($statistics) && count($statistics) > 0){
			$temp = array();
			foreach($statistics as $key=>$value){
				$impressions = @json_decode($value["impressions"], true);
				$click = @json_decode($value["click"], true);
				
				if(isset($impressions) && is_array($impressions) && count($impressions) > 0){
					foreach($impressions as $key_imp=>$value_imp){
						$banner_id = @$value_imp["banner_id"];
						if(in_array($banner_id, $ids_array)){
							@$temp[$banner_id]["impressions"] += $value_imp["how_many"];
						}
					}
				}
				
				if(isset($click) && is_array($click) && count($click) > 0){
					foreach($click as $key_click=>$value_click){
						$banner_id = $value_click["banner_id"];
						if(in_array($banner_id, $ids_array)){
							@$temp[$banner_id]["click"] += $value_click["how_many"];
						}
					}
				}
			}
			
			if(isset($temp) && count($temp) > 0){
				foreach($temp as $key=>$value){
					$temp2 = (object)array();
					
					$temp2->banner_id = $key;
					if(!isset($temp[$key]["impressions"])){
						$temp2->impressions = 0;
					}
					else{
						$temp2->impressions = $temp[$key]["impressions"];
					}
					
					if(!isset($temp[$key]["click"])){
						$temp2->click = 0;
					}
					else{
						$temp2->click = $temp[$key]["click"];
					}
					
					$click_rate = 0;
					if($temp[$key]["impressions"] / 100 > 0){
						$click_rate = @$temp[$key]["click"] / ($temp[$key]["impressions"] / 100);
						$click_rate = number_format($click_rate, 2, '.', ' ');
					}
					
					$temp2->click_rate = $click_rate;
					
					$temp[$key] = $temp2;
				}
				$this->_licenses = $temp;
			}
			else{
				$this->_licenses = (object)array();
			}
		}
		
		$rows2 = $this->_licenses;
		
		foreach($rows2 as $row2)
			foreach($rows as $k=>$row)
				if(@$row2->banner_id == $row->id)
				{
					$rows[$k]->impressions = $row2->impressions;
					$rows[$k]->click = $row2->click;
					$rows[$k]->click_rate = $row2->click_rate;
					$sqlz	= "SELECT count(c.id) FROM #__ad_agency_campaign c INNER JOIN #__ad_agency_campaign_banner cb on c.id = cb.campaign_id WHERE banner_id =".intval($row->id);
					$this->_licenses = $this->_getList($sqlz);

				}
			$this->_licenses = $rows;
		}
		
		return $this->_licenses;
	}

	function getLicense() {
		if (empty ($this->_license)) {
			$sql = "select l.*,p.name as productname, u.username from #__adagency_licenses l, #__adagency_products p, #__users u where l.productid=p.id and l.userid=u.id and l.id=".intval($this->_id);
			$this->_total = $this->_getListCount($sql);

			$this->_license = $this->_getList($sql);//->load($this->_id);
			if (count ($this->_license) > 0) $this->_license = $this->_license[0];
			else {
				$this->_license = $this->getTable("adagencyLicense");
				$this->_license->username = "";
			}
		}
		return $this->_license;

	}

	function getCampaignCount($banner_id) {
		$database = JFactory::getDBO();
		$sql	= "SELECT count(DISTINCT c.id) FROM #__ad_agency_campaign c INNER JOIN #__ad_agency_campaign_banner cb on c.id = cb.campaign_id WHERE banner_id =".intval($banner_id);
		$database->setQuery($sql);
		$result = $database->loadResult();
		return $result;
	}

	function store () {
		$item = $this->getTable('adagencyLicense');
		$data = JRequest::get('post');
		$res = true;
		if (!$item->bind($data)){
			$res = false;
		}

		if (!$item->check()) {
			$res = false;
		}

		if (!$item->store()) {
			$res = false;
		}
		return $res;
	}

	function manage($key,$action,$cid){
		global $mainframe;
		$db	=  JFactory::getDBO();
		
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
		$sql = "SELECT approved FROM `#__ad_agency_banners` WHERE `id`='".intval($cid)."' AND `key`='".$key."' LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		if(isset($res)&&($res!=NULL)){
			if($action == "approve"){
				$sql = "UPDATE `#__ad_agency_banners` SET `approved` = 'Y' WHERE `id` ='".intval($cid)."'";
				$db->setQuery($sql);
				if($db->query()){
					echo "<img src='".JURI::root()."components/com_adagency/images/tick.png' />".JText::_('ADAG_BAMSG');
				}

				//send email notifications
				$sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id = '".intval($cid)."' GROUP BY b.id";
				$db->setQuery($sql);
				if(!$result = $db->query()) {
					echo $db->stderr();
					return;
				}
				$user = $db->loadObject();

				$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
				$db->setQuery($sql);
				$configs = $db->loadObject();

				$subject=$configs->sbadappv;
				$message=$configs->bodyadappv;
				$subject =str_replace('{name}',$user->name,$subject);
				$subject =str_replace('{login}',$user->username,$subject);
				$subject =str_replace('{email}',$user->email,$subject);
				$subject =str_replace('{banner}',$user->title,$subject);
				$message =str_replace('{name}',$user->name,$message);
				$message =str_replace('{login}',$user->username,$message);
				$message =str_replace('{email}',$user->email,$message);
				$message =str_replace('{banner}',$user->title,$message);
				
				if($params["send_ban_app"] == 1){
					JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $user->email, $subject, $message, 1);
				}
				//send email notifications

			} elseif ($action == "decline"){
				$sql = "UPDATE `#__ad_agency_banners` SET `approved` = 'N' WHERE `id` ='".intval($cid)."'";
				$db->setQuery($sql);
				if($db->query()){
					echo "<img src='".JURI::root()."components/com_adagency/images/publish_x.png' />".JText::_('ADAG_BDMSG');
				}

				//send email notifications
				$sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id = '".intval($cid)."' GROUP BY b.id";
				$db->setQuery($sql);
				if(!$result = $db->query()) {
					echo $db->stderr();
					return;
				}
				$user = $db->loadObject();

				$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
				$db->setQuery($sql);
				$configs = $db->loadObject();

				$subject=$configs->sbaddisap;
				$message=$configs->bodyaddisap;
				$subject =str_replace('{name}',$user->name,$subject);
				$subject =str_replace('{login}',$user->username,$subject);
				$subject =str_replace('{email}',$user->email,$subject);
				$subject =str_replace('{banner}',$user->title,$subject);
				$message =str_replace('{name}',$user->name,$message);
				$message =str_replace('{login}',$user->username,$message);
				$message =str_replace('{email}',$user->email,$message);
				$message =str_replace('{banner}',$user->title,$message);				
				if($params["send_ban_dis"] == 1){
					JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $user->email, $subject, $message, 1);
				}
				//send email notifications

			} else {
				$mainframe->redirect("index.php");
			}
		} else {
			$mainframe->redirect("index.php");
		}
	}

	function delete () {
		$database = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array());
		
		$item = $this->getTable('adagencyAds');
		if($cids){
			$query = "DELETE FROM #__ad_agency_banners WHERE id in (0,".implode(",", $cids).")";
			$database->setQuery( $query );
			$database->query();
			
			
			foreach($cids as $key=>$bid){
				$sql = "select * from #__ad_agency_statistics where `impressions` like '%\"banner_id\":".intval($bid).",%' OR `click` like '%\"banner_id\":".intval($bid).",%'";
				$database->setQuery($sql);
				$database->query();
				$result = $database->loadAssocList();
				
				if(isset($result) && count($result) > 0){
					foreach($result as $res_key=>$value){
						$impressions = @json_decode($value["impressions"], true);
						$click = @json_decode($value["click"], true);
						
						if(isset($impressions) && count($impressions) > 0){
							$temp = array();
							foreach($impressions as $key_imp=>$value_imp){
								if($value_imp["banner_id"] != intval($bid)){
									$temp[] = $value_imp;
								}
							}
							$impressions = $temp;
						}
						
						if(isset($click) && count($click) > 0){
							$temp = array();
							foreach($click as $key_click=>$value_click){
								if($value_click["banner_id"] != intval($bid)){
									$temp[] = $value_click;
								}
							}
							$click = $temp;
						}
						
						$result[$res_key]["impressions"] = json_encode($impressions);
						$result[$res_key]["click"] = json_encode($click);
						
						$sql = "update #__ad_agency_statistics set `impressions`='".$result[$res_key]["impressions"]."', `click`='".$result[$res_key]["click"]."' where `id`=".intval($result[$res_key]["id"]);
						$database->setQuery($sql);
						$database->query();
					}
				}
			}

			$query = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id in (0,".implode(",", $cids).")";
			$database->setQuery( $query );
			$database->query();

		}
		return true;
	}

	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyAds');
		if ($task == 'publish'){
			$sql = "update #__ad_agency_banners set approved='Y' where id in ('".implode("','", $cids)."')";
		} else {
			$sql = "update #__ad_agency_banners set approved='N' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function approve () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyAds');
		$sql = "update #__ad_agency_banners set approved='Y' where id in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function unapprove () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyAds');
		$sql = "update #__ad_agency_banners set approved='N' where id in ('".implode("','", $cids)."')";
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function startBar($id = 2) {
		?>
		<style type="text/css">
		table#toolbar<?php echo $id; ?> {
			border: 0px solid #DDD;
			width: 100%;
			/*border: 1px solid #c24733; */
		}
		table#toolbar<?php echo $id; ?> td {
			padding: 2px 3px;
			vertical-align: middle;
		}

		table#toolbar<?php echo $id; ?> a.toolbar {
			color : #808080;
			text-decoration : none;
		}
		table#toolbar<?php echo $id; ?> a.toolbar:hover {
			color : #C64934;
			cursor: pointer;
		}
		table#toolbar<?php echo $id; ?> a.toolbar:active {
			color : #FF9900;
		}
		table#toolbar<?php echo $id; ?> a.toolbarA {
			color : #880000;
			text-decoration : none;
		}
		table#toolbar<?php echo $id; ?> a.toolbarA:hover {
			color : #C64934;
			cursor: pointer;
		}
		table#toolbar<?php echo $id; ?> a.toolbarA:active {
			color : #FF9900;
		}
		</style>

<table cellpadding="0" cellspacing="0" border="0" id="toolbar<?php echo $id; ?>">
		<tr valign="middle" align="left">
			<td align="left" nowrap>
		<?php
	}

	/**
	* Writes a custom option and task button for the button bar
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
	function custom( $task='', $alt='', $listSelect=true, $active=false ) {
		if ($listSelect) {
			$href = "javascript:if (document.adminForm.boxchecked.value == 0){ alert('Please make a selection from the list to $alt');}else{submitbutton('$task')}";
		} else {
			$href = "javascript:submitbutton('$task')";
		}
		?>
			<a class="toolbar<?php echo $active?'A':''; ?>" href="<?php echo $href;?>"
				>&raquo;&nbsp;<?php echo $alt;?></a>
		<?php
	}

	/**
	* Writes a custom option and task button for the button bar
	* @param string The task to perform (picked up by the switch($task) blocks
	* @param string The image to display
	* @param string The image to display when moused over
	* @param string The alt text for the icon image
	* @param boolean True if required to check that a standard list item is checked
	*/
	function customGet($option, $task='', $alt='', $active=false ) {
		$href = "index.php?option={$option}&controller={$task}";
		?>
			&nbsp;&nbsp;<a class="<?php echo $active?'toolbarA':''; ?>" href="<?php echo $href;?>"
				>&raquo;&nbsp;<?php echo $alt;?></a>
		<?php
	}

	function bannerTask($option, $task='', $alt='', $active=false ) {
		$href = "index.php?option={$option}&controller={$task}&task=edit&cid[]=0";
		?>
			&nbsp;&raquo;&nbsp;<a class="<?php echo $active?'toolbarA':''; ?>" href="<?php echo $href;?>"
				><?php echo $alt;?></a>
		<?php
	}
	/**
	* Writes a spacer cell
	* @param string The width for the cell
	*/
	function spacer( $width='' ) {
		echo ' </td><td> ';
		return;
		if ($width != '') {
			?>
			<td width="<?php echo $width;?>">&nbsp;</td>
			<?php
		} else {
			?>
			<td>&nbsp;</td>
			<?php
		}
	}

	/**
	* Writes the end of the bar table
	*/
	function endBar() {
		?>
			</td>
		</tr>
		</table>
		<?php
	}

	function displayToolbar($option, $activeSection = '', $customFunc = NULL) {
		$mosConfig_absolute_path =JPATH_BASE;
		$mosConfig_live_site     =JURI::base();
		require_once( $mosConfig_absolute_path .'/includes/HTML_toolbar.php'); ?>
		<div class="cotentheading"><?php echo JText::_('_JAS_FLD_ADVERTISER') ?> <?php echo JText::_('_JAS_CONTROL_PANEL') ?></div>
		<div style="align: right;">
			<?php
			adagencyModeladagencyAds::startBar(2);
			adagencyModeladagencyAds::customGet($option, 'profile', JText::_('_JAS_PROFILE'), $activeSection == 'profile');
			adagencyModeladagencyAds::customGet($option, 'listbanners', JText::_('_JAS_BANNERS'), $activeSection == 'banners');
			adagencyModeladagencyAds::customGet($option, 'listcampaigns', JText::_('_JAS_CAMPAIGNS'), $activeSection == 'campaigns');
			adagencyModeladagencyAds::customGet($option, 'viewreports', JText::_('_JAS_REPORTS'), $activeSection == 'reports');
			if ($customFunc) adagencyModeladagencyAds::$customFunc($option);
			adagencyModeladagencyAds::endBar();
			?>
		</div>
<?php
	}

	function displayBannerToolbar($option, $activeSection = '', $configs,$customFunc = NULL) {
		$mosConfig_absolute_path =JPATH_BASE;
		$mosConfig_live_site     =JURI::base();
		$abmConfig = $configs;
		$task="";
		require_once( $mosConfig_absolute_path .'/includes/HTML_toolbar.php' ); ?>
		<div class="ads_tableborder">
		<?php
			adagencyModeladagencyAds::startBar(3);
			echo '&nbsp;<b>'.JText::_('JAS_ADDNEW').':</b>';
			if ($abmConfig->allowstand) adagencyModeladagencyAds::bannerTask($option, 'adagencyStandard', JText::_('JAS_STANDART'), $task == 'adagencyStandard');
			if ($abmConfig->allowadcode) adagencyModeladagencyAds::bannerTask($option, 'adagencyAdcode', JText::_('JAS_BANNER_CODE'), $task == 'adagencyAdcode');
			if ($abmConfig->allowpopup) adagencyModeladagencyAds::bannerTask($option, 'adagencyPopup', JText::_('JAS_POPUP'), $task == 'adagencyPopup');
			if ($abmConfig->allowswf) adagencyModeladagencyAds::bannerTask($option, 'adagencyFlash', JText::_('JAS_FLASH'), $task == 'adagencyFlash');
			if ($abmConfig->allowtxtlink) adagencyModeladagencyAds::bannerTask($option, 'adagencyTextlink', JText::_('JAS_TEXT_LINK'), $task == 'adagencyTextlink');
			if ($abmConfig->allowtrans) adagencyModeladagencyAds::bannerTask($option, 'adagencyTransition', JText::_('JAS_TRANSITION') , $task == 'adagencyTransition');
			if ($abmConfig->allowfloat) adagencyModeladagencyAds::bannerTask($option, 'adagencyFloating',  JText::_('JAS_FLOATING'), $task == 'adagencyFloating');
			adagencyModeladagencyAds::spacer(30);
			if ($customFunc) $customFunc($option);
			adagencyModeladagencyAds::endBar();
			?>
		</div>
<?php
	}

		function click() {
			$database = JFactory::getDBO();
			$aid = JRequest::getVar('aid', "");
			$bid = JRequest::getVar('bid', "");
			$cid = JRequest::getVar('cid', "");

			$sql = "select `params` from #__ad_agency_settings";
	        $database->setQuery( $sql );
			$configs = $database->loadColumn();
		 	$configs = $configs['0'];
			@$configs = @unserialize($configs);

			$mosConfig_absolute_path = JPATH_BASE;
			include_once($mosConfig_absolute_path."/administrator/components/com_adagency/tables/adagencyads.php");
			$bans = new TableadagencyAds($database);
			
			$click_limit = $configs["click_limit"];
			
            if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
            {
                $ip_address=$_SERVER['HTTP_CLIENT_IP'];
            }
            elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
            {
                $ip_address=$_SERVER['HTTP_X_FORWARDED_FOR'];
            }
            // check if isset REMOTE_ADDR and != empty
            elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
            {
                $ip_address = $_SERVER['REMOTE_ADDR'];
            // you're probably on localhost
            } else {
                $ip_address = "127.0.0.1";
            }

			if(strpos($ip_address, ",") !== FALSE){
				$ip_address = explode(",", $ip_address);
				$ip_address = $ip_address["0"];
			}

			function checkbot2($user_agent){

				//if no user agent is supplied then assume it's a bot
				if($user_agent == "") {return 1;}

				$bots_array = array("AdsBot-Google", "googlebot", "FeedFetcher-Google", "DotBot", "Bloglines", "Charlotte", "Quihoobot", "WebAlta", "LinkWalker", "sogou", "Baiduspider", "MSNbot-media", "BSpider", "DNAbot", "becomebot", "legs", "Nutch", "Spiderman", "SurveyBot", "BBot", "Netcraft", "Exabot", "bot", "robot", "Speedy Spider", "spider", "crawl", "Teoma", "ia_archiver", "froogle", "archiver", "curl", "python", "nambu", "twitt", "perl", "sphere", "PEAR", "java", "wordpress", "radian", "yandex", "eventbox", "monitor", "mechanize", "inktomi", "looksmart", "URL_Spider_SQL", "Firefly", "NationalDirectory", "Ask Jeeves", "TECNOSEEK", "InfoSeek", "www.galaxy.com", "Scooter", "ScoutJet", "Slurp", "MSNBot", "blogscope", "appie", "FAST", "WebBug", "Spade", "ZyBorg", "rabaz", "spider", "TechnoratiSnoop" , "blogpulse", "jobo", "facebookexternalhit");

				foreach($bots_array as $bot){
					if(strpos(strtolower($user_agent),strtolower($bot)) !== false) { return 1; }
				}

				return 0;
			}

			if(isset($_SERVER['HTTP_USER_AGENT'])&&checkbot2($_SERVER['HTTP_USER_AGENT'])!=1){

		$sql="SELECT b.target_url,c.type FROM #__ad_agency_banners AS b
				LEFT JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id = ".intval($bid)." AND cb.campaign_id = ".intval($cid)."
				LEFT JOIN #__ad_agency_campaign AS c ON c.id = cb.campaign_id
				WHERE b.advertiser_id = c.aid AND b.id = cb.banner_id AND b.advertiser_id = ".intval($aid)." AND b.approved = 'Y'";
        //echo "<pre>";var_dump($sql);die();
		$database->setQuery($sql);
        //echo "<pre>";var_dump(JRequest::get('get'));die();

		if (!$database->query()) {
			return JError::raiseError( 500, $database->getErrorMsg() );
			exit();
		}
		$ban_row = $database->loadRow();
		$bans->load($bid);
		$bans->parameters = unserialize($bans->parameters);


		if ('pc'==$ban_row[1]) {
			$sql = "UPDATE #__ad_agency_campaign SET quantity = quantity-1 WHERE id=".intval($cid);
			$database->setQuery($sql);
			if (!$database->query()) {
				return JError::raiseError( 500, $database->getErrorMsg() );
				exit;
			}
			$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".intval($cid);
			$database->setQuery($sql);
			if (!$database->query()) {
				return JError::raiseError( 500, $database->getErrorMsg() );
				return;
			}
			$quantity = $database->loadResult();
			if ($quantity == 0) {
				$nowdatetime = date("Y-m-d H:i:s");
				$sql = "UPDATE #__ad_agency_campaign SET validity = '".trim($nowdatetime)."' WHERE id=".intval($cid);
				$database->setQuery($sql);
				if (!$database->query()) {
					return JError::raiseError( 500, $database->getErrorMsg() );
					return;
				}

			}
		}
		//============================================================
		if (!isset($ban_row[0])) $ban_row[0]="#";
		//============================================================
		if ($ban_row[0]) {
				$time_interval = date("Y-m-d");
				$real_ip = $this->iJoomlaGetRealIpAddrModuleClick();
				$stop_count = false;
				
				// start limit impression count for a banner per ip ----------------------
				$sql = "select `ips_clicks` from #__ad_agency_ips where `entry_date`='".$time_interval."'";
				$database->setQuery($sql);
				$database->query();
				$all_ips = $database->loadColumn();
				
				if(is_array($all_ips) && count($all_ips) > 0){
					$all_ips = json_decode($all_ips["0"], true);
					$update = FALSE;
					
					if(isset($all_ips) && count($all_ips) > 0){
						foreach($all_ips as $key=>$value){
							if($value["ip"] == ip2long($real_ip) && $value["banner_id"] == intval($bans->id)){
								if($value["how_many"] < $click_limit){
									$update = TRUE;
									$all_ips[$key]["how_many"] += 1;
									break;
								}
								else{
									// max limit impressions per IP for one ad per day
									$stop_count = TRUE;
									break;
								}
							}
						}
					}
					
					if(!$update){
						$new_ip_added = array("ip"=>ip2long($real_ip), "banner_id"=>intval($bans->id), "how_many"=>"1");
						$all_ips[] = $new_ip_added;
					}
					
					$sql = "update #__ad_agency_ips set `ips_clicks`='".json_encode($all_ips)."' where `entry_date`='".$time_interval."'";
					$database->setQuery($sql);
					$database->query();
				}
				else{
					$temp_ips1 = array("ip"=>ip2long($real_ip), "banner_id"=>intval($bannerID), "how_many"=>"1");
					$temp_ips2 = array("ip"=>'0000000000', "banner_id"=>"0", "how_many"=>"0");
					$temp_ips = array("0"=>$temp_ips1, "1"=>$temp_ips2);
					
					$sql = "insert into #__ad_agency_ips (`entry_date`, `ips_clicks`) values ('".$time_interval."', '".json_encode($temp_ips)."')";
					$database->setQuery($sql);
					$database->query();
				}
				// stop limit impression count for a banner per ip -----------------------
				
				if(!$stop_count){
					$sql = "select * from #__ad_agency_statistics where `entry_date`='".$time_interval."'";
					$database->setQuery($sql);
					$database->query();
					$result = $database->loadAssocList();
	
					if(isset($result) && count($result) > 0){
						foreach($result as $key=>$value){
							if(isset($value["click"]) && trim($value["click"]) != ""){
								$clicks = json_decode($value["click"], true);
								if(isset($clicks) && count($clicks) > 0){
									$find = false;
									foreach($clicks as $key_click=>$click_value){
										if($click_value["advertiser_id"] == intval($aid) && $click_value["campaign_id"] == intval($cid) && $click_value["banner_id"] == intval($bid)){
											if(!$find){
												$clicks[$key_click]["how_many"] ++;
											}
											$find = true;
										}
									}
									if(!$find){
										$temp2 = array("advertiser_id"=>intval($aid), "campaign_id"=>intval($cid), "banner_id"=>intval($bid), "how_many"=>"1");
										$clicks[] = $temp2;
									}
								}
								$result[$key]["click"] = json_encode($clicks);
							}
							else{
								$temp1 = array();
								$temp2 = array("advertiser_id"=>intval($aid), "campaign_id"=>intval($cid), "banner_id"=>intval($bid), "how_many"=>"1");
								$temp1[] = $temp2;
								$result[$key]["click"] = json_encode($temp1);
							}
						}
						
						$sql = "update #__ad_agency_statistics set `click`='".$result[$key]["click"]."' where `id`=".intval($result[$key]["id"]);
						$database->setQuery($sql);
						$database->query();
					}
					else{
						$temp1 = array();
						$temp2 = array("advertiser_id"=>intval($aid), "campaign_id"=>intval($cid), "banner_id"=>intval($bid), "how_many"=>"1");
						$temp1[] = $temp2;
						$clicks = json_encode($temp1);
						
						$sql = "insert into #__ad_agency_statistics (`entry_date`, `impressions`, `click`) values ('".$time_interval."', '', '".$clicks."')";
						$database->setQuery($sql);
						$database->query();
					}
				}
				
			if ($bans->media_type=="Advanced" && strpos($bans->ad_code,'ad_url'))
				return $bans->parameters['linktrack'];
			else if ($bans->media_type=="Floating" || $bans->media_type=="Transition" || ($bans->media_type=="Popup" && !isset($bans->image_url))) {
					$lid=$_GET['lid'];
					return $bans->parameters['linktrack'][$lid];
				  }
			else {
				if(strpos(' '.$ban_row[0],'mailto:') >= 1) {
					echo "<script type='text/javascript'>
					document.location.href=\"".$ban_row[0]."\";
					</script>";
					die();
				} else {
					return $ban_row[0];
				}
			}
		}
	}
 }
 
function iJoomlaGetRealIpAddrModuleClick(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
		$ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
	// check if isset REMOTE_ADDR and != empty
    elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
    {
    	$ip = $_SERVER['REMOTE_ADDR'];
	// you're probably on localhost
    } else {
		$ip = "127.0.0.1";
	}
    return $ip;
}

};
?>
