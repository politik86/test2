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

if(!class_exists('upload')){

	require_once('components/com_adagency/helpers/class.upload.php');

}



class adagencyAdminModeladagencyCampaigns extends JModelLegacy {

	var $_attributes;

	var $_attribute;

	var $_id = null;

	var $old_limit = -1;



	function __construct () {

		parent::__construct();

		$cids = JRequest::getVar('cid', 0, '', 'array');

		$this->setId( (int)$cids[0] );

		global $mainframe, $option;

		// Get the pagination request variables

		$limit = $mainframe->getUserStateFromRequest( 

            'global.list.limit', 'limit', 

            $mainframe->getCfg('list_limit'), 'int' 

        );

		$limitstart = $mainframe->getUserStateFromRequest( 

            $option . 'limitstart', 'limitstart', 0, 'int' 

        );

		

		if(JRequest::getVar("limitstart") == JRequest::getVar("old_limit")){

			JRequest::setVar("limitstart", "0");		

			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');

		}

		

		$this->setState('limit', $limit); // Set the limit variable for query later on

		$this->setState('limitstart', $limitstart);

	}



	function getPagination() {

		// Lets load the content if it doesn't already exist

		if ( empty($this->_pagination) )	{

			jimport('joomla.html.pagination');

			if (!$this->_total) $this->getlistCampaigns();

			$this->_pagination = new JPagination( 

                $this->_total, $this->getState('limitstart'), $this->getState('limit') 

            );

		}

		return $this->_pagination;

	}

	

	function setId($id) {

		$this->_id = $id;

		$this->_attribute = null;

	}

	

	function getAdvById($id) {

		$db = JFactory::getDBO();

		$id = intval($id);

		$sql = "SELECT * FROM #__ad_agency_advertis WHERE aid = " . intval($id);

		$db->setQuery($sql);

		$res =  $db->loadObject();

		return $res;

	}



	function getlistCampaigns () {	

		$data = JRequest::get();

		$and_filter = "WHERE 1=1";

		$limit_cond = NULL;

		$search_pack = NULL;

		$search_zone = NULL;

		$db =JFactory::getDBO();

		if( isset($_GET['apr']) && ($_GET['apr']==true) ) {

            $and_filter.= " AND c.approved = 'P' "; 

        }		

		/* adding the campaign title search condition - start */	



		if ( isset($data['search_campaign']) ) {

			$_SESSION['search_campaign'] = $data['search_campaign'];

			$search_campaign = $data['search_campaign'];

		}

		elseif ( isset($_SESSION['search_campaign']) ) {

			$search_campaign = $_SESSION['search_campaign'];

		}

		

		if( isset($search_campaign) && $search_campaign!='' ) {

			$and_filter = $and_filter . " AND (c.name LIKE '%" . $search_campaign 

                            . "%' OR c.id LIKE '%" . $search_campaign . "%')"; 

		}

		/* adding the campaign title search condition - end */		



		/* adding the package search condition - start */

		if ( isset($data['selpack1']) ) {

			$_SESSION['selpack1'] = $data['selpack1'];

			$search_pack = $data['selpack1'];

		} elseif ( isset($_SESSION['selpack1']) ) {

			$search_pack = $_SESSION['selpack1'];

		}

		

		if ( isset($search_pack) && $search_pack != '0' ) {

			$and_filter = $and_filter . " AND p.tid = " . $search_pack . " "; 

		}

		/* adding the package search condition - end */		



		/* adding the zone search condition - start */

		

		if(isset($data['selzone1']))

		{

			$_SESSION['selzone1'] = $data['selzone1'];

			$search_zone = $data['selzone1'];

		} elseif ( isset($_SESSION['selzone1']) ) {

			$search_zone = $_SESSION['selzone1'];

		}

		

		if( isset($search_zone) && $search_zone != '0' ) {

			$and_filter = $and_filter . " AND z.zoneid = " . $search_zone . " "; 

		}

		/* adding the zone search condition - end */	

		

		/* adding the advertiser select condition - start */			

		if( isset($data['advertiser_id']) ) {

            $advertiser = intval( $data['advertiser_id'] );

            $_SESSION['advertiser_id'] = intval( $data['advertiser_id'] );

        } elseif ( isset($_SESSION['advertiser_id']) ) {

			$advertiser = $_SESSION['advertiser_id'];

        }

		if ( isset($advertiser) && intval($advertiser) != '0' ) {

            $and_filter = $and_filter . " AND a.aid = '" . $advertiser . "' "; 

        }

		/* adding the advertiser select condition - stop */

		

		/* adding the status campaign select condition - start */

		if( isset($_REQUEST['campaign_status']) && $_REQUEST['campaign_status'] != "" ) {

				$campaign_status = JRequest::getVar("campaign_status", "YN");

				$_SESSION['campaign_status'] = JRequest::getVar("campaign_status", "YN");

		} elseif ( isset($_SESSION['campaign_status']) ) {

			$campaign_status = JRequest::getVar("campaign_status", "YN");

		}

		

		if ( isset($campaign_status) && $campaign_status != "" && $campaign_status != "YN" )

			$and_filter .=" AND c.approved LIKE '%" . $campaign_status . "%'";

		/* adding the status campaign select condition - stop */

		

		if ( empty($this->_attributes) ) {

			if (!isset($_GET['id'])) {

				$sql = "SELECT u.name as company, c.id, c.name, c.quantity, c.validity, c.start_date, a.user_id, 

                           c.type, c.approved, c.status, c.ordering, COUNT(DISTINCT cb.banner_id) cnt, p.tid AS package_id, 

                           p.description, z.zoneid, z.z_title 

                           FROM #__ad_agency_campaign AS c 

                           LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON c.id = cb.campaign_id	

                           LEFT JOIN #__ad_agency_advertis AS a ON a.aid=c.aid 

                           LEFT JOIN #__users AS u ON u.id = a.user_id 

                           LEFT JOIN #__ad_agency_order_type AS p ON c.otid = p.tid 

                           LEFT JOIN #__ad_agency_zone AS z ON cb.zone = z.zoneid 

                           " . $and_filter . " GROUP BY c.id ORDER BY c.id DESC";

            } else { 

				$and_filter .= " AND cb.banner_id=" . intval($_GET['id']);

				$sql = "SELECT u.name as company, c.id, c.name, c.quantity, c.validity, c.start_date, a.user_id, 

                           c.type, c.approved, c.status, c.ordering, count(DISTINCT cb.banner_id) cnt, p.tid AS package_id, 

                           p.description, z.zoneid, z.z_title 

                           FROM #__ad_agency_campaign AS c 

                           LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON c.id=cb.campaign_id 

                           LEFT JOIN #__ad_agency_advertis AS a ON a.aid=c.aid 

                           LEFT JOIN #__users AS u ON u.id = a.user_id 

                           LEFT JOIN #__ad_agency_order_type AS p ON c.otid = p.tid 

                           LEFT JOIN #__ad_agency_zone AS z ON cb.zone = z.zoneid 

                           ".$and_filter." GROUP BY c.id ORDER BY c.id DESC";

			}

			

			$limitstart = $this->getState('limitstart');

			$limit = $this->getState('limit');

				

			if ($limit!=0) {

				$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";

			} else {

				$limit_cond = NULL;

			}

			$this->_total = $this->_getListCount($sql);

			$this->_attributes = $this->_getList($sql.$limit_cond);

			

			if(!isset($this->_attributes) || count($this->_attributes) == 0){

				$limitstart = 0;

				$limit_cond = " LIMIT " . $limitstart . "," . $limit . " ";

				$this->_attributes = $this->_getList($sql.$limit_cond);

			}

		}

		

		return $this->_attributes;

	}	



	function getCampaign() {

		if (empty ($this->_attribute)) { 

			$this->_attribute = $this->getTable("adagencycampaigns");

			$this->_attribute->load( $this->_id );

		}

        $data = JRequest::get('request');

        

		if ( !$this->_attribute->bind($data) ) {

            $this->setError( $item->getErrorMsg() );

            return false;

        }

        if (!$this->_attribute->check()) {

            $this->setError( $item->getErrorMsg() );

            return false;

        }

		return $this->_attribute;

	}

	

	function getAllCamps() {

		$db =  JFactory::getDBO();

		$sql = "SELECT u.name as company, c.id, c.name, c.quantity, c.validity, c.start_date, 

                   a.user_id, c.type, c.approved, c.status, 

                   COUNT(DISTINCT cb.banner_id) cnt, p.tid AS package_id, p.description, z.zoneid, z.z_title 

                   FROM #__ad_agency_campaign AS c 

                   LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON c.id=cb.campaign_id

                   LEFT JOIN #__ad_agency_advertis AS a ON a.aid=c.aid LEFT JOIN #__users AS u 

                   ON u.id = a.user_id 

                   LEFT JOIN #__ad_agency_order_type AS p 

                   ON c.otid = p.tid 

                   LEFT JOIN #__ad_agency_zone AS z 

                   ON p.location = z.zoneid 

                   GROUP BY c.id ORDER BY c.ordering ASC";	

		$db->setQuery($sql);

		$all_camps = $db->loadObjectList();

		if ( is_array($all_camps) && (count($all_camps)>0) ) {

			foreach($all_camps as $element){

				$element->allzones = $this->getZonesForPack( $element->package_id );

			}

		}

		return $all_camps;

	}

	

	function getlistPackages () { 

		$database = JFactory::getDBO();

		$sql = "SELECT p.* FROM #__ad_agency_order_type AS p";

		$database->setQuery( $sql );

		$rows = $database->loadObjectList();

		foreach($rows as $row) {

			$row->adparams = @unserialize( $row->adparams );

		}

		return $rows;

	}

	

	function getPackById($tid) {

		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__ad_agency_order_type WHERE tid = " . intval($tid);

		$db->setQuery($sql);

		$res = $db->loadObject();

		return $res;

	}

	

	function getZonesForCamps($camps) {

		if( isset($camps) && (is_array($camps)) ) {

			foreach($camps as $camp) {

				$camp->allzones = $this->getZonesForPack($camp->package_id);

			}

		}

		return $camps;

	}

	

	function getZonesForPack($pack) {

		$db = JFactory::getDBO();

		if ( isset($pack) && (intval($pack)>0) ) {

			$sql = "SELECT z.*

					FROM #__ad_agency_package_zone AS pz

					LEFT JOIN #__modules AS m ON pz.zone_id = m.id

					LEFT JOIN #__ad_agency_zone AS z ON m.id = z.zoneid

					WHERE pz.package_id =".intval($pack);

			$db->setQuery($sql);

			$res =  $db->loadObjectList();

			return $res;

		} else {

			return NULL;

		}

	}	



	function updateMediaType($bans) {

		if (isset($bans) && (is_array($bans))) {

			foreach($bans as $ban){

				$ban->media_type = strtolower($ban->media_type);

				if($ban->media_type == 'advanced') { $ban->media_type = 'affiliate'; }

				if($ban->media_type == 'textlink') { $ban->media_type = 'textad'; }

			}

		}	

		return $bans;

	}

	

	function updateZoneList($bans, $zones, $camp_id = 0) {

		$db = JFactory::getDBO();

		if( isset($bans) && isset($zones) ) {

			foreach($bans as $ban){

				if( isset($camp_id) && (intval($camp_id)>0) && isset($ban->id) && (intval($ban->id)>0) ) {

					$sql = "SELECT zone FROM #__ad_agency_campaign_banner 

                               WHERE banner_id = " . intval($ban->id) . " AND campaign_id = " . intval($camp_id);

					$db->setQuery( $sql );

					$sel_zone = $db->loadResult();

				} else {

					$sel_zone = NULL;

				}

				$ban->zones = "<select class='w145' name='adzones[" . $ban->id . "]' id='adzones_" . $ban->id . "'>";
				$ban->zones .= "<option value=''>" . JText::_("ADAG_ZONE_FOR_AD") . "</option>";
				$find_zones = false;

				foreach($zones as $zone) {
					$types = NULL; $b_with_size = false;

					if ( is_array($zone->adparams) )
					foreach($zone->adparams as $key=>$value) {
						if($key == 'affiliate') { $types[] = $key; $b_with_size = true; }
						if($key == 'textad') { $types[] = $key; }
						if($key == 'standard') { $types[] = $key; $b_with_size = true; }
						if($key == 'flash') { $types[] = $key; $b_with_size = true; }
						if($key == 'popup') { $types[] = $key; }
						if($key == 'transition') { $types[] = $key; }
						if($key == 'floating') { $types[] = $key; }
					}

					if ( is_array($types) && in_array($ban->media_type, $types) ) {
						if((!isset($zone->adparams['width']) || ($zone->adparams['width'] == '') || !isset($zone->adparams['height']) || ($zone->adparams['height'] == '')) || ($b_with_size == false)){
                            if($sel_zone == $zone->zoneid){
								$selected = " selected='selected' ";
							}
							else{
								$selected = NULL;
							}
							$ban->zones .= "<option value='" . $zone->zoneid . "' " . $selected . ">" . $zone->z_title . "</option>";
							$find_zones = true;
						}
						elseif ( ($b_with_size == true) && ($zone->adparams['width'] == $ban->width) && ($zone->adparams['height'] == $ban->height) ) {
							if($sel_zone == $zone->zoneid){
								$selected = " selected='selected' ";
							}
							else{
								$selected = NULL;
							}
							$ban->zones .= "<option value='" . $zone->zoneid . "' " . $selected . ">" . $zone->z_title . "</option>";
							$find_zones = true;
						}
					}
				}
				$ban->zones .= "</select>";
				
				if(!$find_zones){
					$ban->zones = " - ";
				}
			}

		}

		return $bans;

	}



	function formatime2($time,$option = 1) {

		$date_time = explode(" ", $time);

		$date_time[0] = str_replace("/", "-", $date_time[0]);

		$tdate = explode("-", $date_time[0]);

		if ( ($option == 1) || ($option == 2) || ($option == 7) || ($option == 8) ) {

			$aux = $tdate[0];

			$tdate[0] = $tdate[2];

			$tdate[2] = $aux;

		} elseif ( ($option == 3) || ($option == 4) || ($option == 9) || ($option == 10) ) {

			//mm-dd-yyyy

			$aux=$tdate[0];

			$tdate[0] = $tdate[2];

			$tdate[2] = $tdate[1];

			$tdate[1] = $aux;	

		}

		$output = NULL;

		if( !isset($date_time[1]) ) { $date_time[1] = NULL; }

		$output = $tdate[0] . "-" . $tdate[1] . "-" . $tdate[2] . " " . $date_time[1];			

		return trim($output);

	}

	  

	function store () {

		$item = $this->getTable('adagencyCampaigns');

		$data = JRequest::get('post');

		$sendmail = $data['sendmail'];

		$database =  JFactory::getDBO();

		$db =  JFactory::getDBO();

        $configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");

		$configs = $configs->getConfigs();

		$configs->params = @unserialize( $configs->params );

       

        // Stuff for existing campaigns

		if ( isset($data['id']) && ($data['id']>0) ) {

			// Get current campaign data

            $sql = "SELECT * FROM #__ad_agency_campaign WHERE id = '".intval($data['id'])."'";

			$db->setQuery($sql);

			$currentcmp = $db->loadObject();

            $currentcmp->params = @unserialize( $currentcmp->params );

            

			$cappr = $data['approved'];

			

            // Delete existing campaign-banners bindings

            $sql = "DELETE FROM `#__ad_agency_campaign_banner` 

                       WHERE `campaign_id` = " . intval($data['id']);

            $db->setQuery( $sql );

            $db->query();			

            

            if ( isset($currentcmp->params['adslim']) ) {

                $adslim = (int)$currentcmp->params['adslim'];

            } else {

                $adslim = 999;

            }

            

        // Stuff for new campaigns

        } else {

            if ( isset($configs->params['adslim']) ) {

                $data['params']['adslim'] = (int)$configs->params['adslim'];

            } else { 

                $data['params']['adslim'] = 999;

            }            

            $adslim = $data['params']['adslim'] ;

            $data['params'] = @serialize( $data['params'] );            

        }

        

		$data['key'] = '';

		

		$sql = "SELECT imgfolder FROM #__ad_agency_settings LIMIT 1";

		$db->setQuery($sql);

		$imgfolder = $db->loadResult();

		

		$data['start_date'] = date("Y-m-d H:i:s", strtotime($data['start_date']));
		
		if(isset($data['validity']) && ($data["validity"] == "" || $data["validity"] == "Never")){
			$data['validity'] = "0000-00-00 00:00:00";
		}
		elseif(isset($data['validity']) && $data["validity"] != "" && $data["validity"] != "Never"){
			$data['validity'] = date("Y-m-d H:i:s", strtotime($data['validity']));
		}

		$creat = false;

		$data['key'] = NULL;

		if (!$data['id']) $creat = true;

		

		if ($creat && $data['otid'] > 0) {

			$sql = "SELECT * FROM #__ad_agency_order_type WHERE tid=" . intval($data['otid']);

            $database->setQuery($sql);

            if (!$result = $database->query()) {

                echo $database->stderr();

                return;

            }

            $rows = $database->loadObjectList();

            $package_row = $rows[0];

            $data['type'] = $package_row->type;

            $data['cost'] = $package_row->cost;

            

            if ($data['type'] == "fr" || $data['type'] == "in") {

                $tmp_date_time = explode(" ", $data['start_date'], 2);

				

                if ($tmp_date_time[0]) {

                    $tmp_date = explode("-", $tmp_date_time[0], 3);

                } else {

                    $tmp_date[0] = 0;

                    $tmp_date[1] = 0;

                    $tmp_date[2] = 0;

                }                

                if ($tmp_date_time[1]) {

                    $tmp_time = explode(":", $tmp_date_time[1], 3);

                } else {

                    $tmp_time[0] = 0;

                    $tmp_time[1] = 0;

                    $tmp_time[2] = 0;

                }                

                $tmp = explode("|", $package_row->validity, 2);                

                if ($tmp[1]=="day") {

                    $data['validity'] = date(

                        "Y-m-d H:i:s", 

                        mktime( 

                            $tmp_time[0], $tmp_time[1], $tmp_time[2], 

                            $tmp_date[1], $tmp_date[2] + $tmp[0], $tmp_date[0]

                        )

                    );

                } elseif ($tmp[1]=="week") {

                    $data['validity'] = date( 

                        "Y-m-d H:i:s", 

                        mktime(

                            $tmp_time[0], $tmp_time[1], $tmp_time[2],

                            $tmp_date[1], $tmp_date[2] + (7*$tmp[0]), $tmp_date[0]

                        )

                    );

                } elseif ($tmp[1]=="month") {

                    $data['validity'] = date(

                        "Y-m-d H:i:s",

                        mktime(

                            $tmp_time[0], $tmp_time[1], $tmp_time[2],

                            $tmp_date[1] + $tmp[0], $tmp_date[2], $tmp_date[0]

                        )

                    );

                } elseif ($tmp[1]=="year") {

                    if ($tmp_date[0]+$tmp[0]>2038) {

                        $tmp[0] = 2037-$tmp_date[0];

                    }

                    $data['validity'] = date(

                        "Y-m-d H:i:s", 

                        mktime(

                            $tmp_time[0], $tmp_time[1], $tmp_time[2], 

                            $tmp_date[1], $tmp_date[2], $tmp_date[0] + $tmp[0]

                        )

                    );

                }

                

            }

            else {

                $data['quantity'] = $package_row->quantity;

            }			

        }

		

		if ( !$item->bind($data) ) {

			$this->setError( $item->getErrorMsg() );

			return false;

		}



		if (!$item->check()) {

			$this->setError( $item->getErrorMsg() );

			return false;

		}



		if (!$item->store()) {

			$this->setError( $item->getErrorMsg() );

			return false;

		}

		

		if (!isset($data['id']) || $data['id']==0) {

            $data['id'] = mysql_insert_id();

			$user = JFactory::getUser();

			/*$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, 'Purchased(new) - ".date("Y-m-d H:i:s")."', ' - ".intval($user->id)."', ';') WHERE `id` = '".intval($data['id'])."' ";

			$db->setQuery($sql);

			$db->query();*/

        }	

		if ($data['id']==0) {

			$ask = "SELECT `id` FROM `#__ad_agency_campaign` ORDER BY `id` DESC LIMIT 1 ";

			$db->setQuery( $ask );

			$data['id'] = $db->loadResult();

		}		

		$sql = "SELECT z.adparams FROM #__ad_agency_campaign AS c 

                   LEFT JOIN #__ad_agency_order_type AS p ON c.otid = p.tid 

                   LEFT JOIN #__ad_agency_zone AS z ON p.location = z.zoneid 

                   WHERE c.id = " . intval($data['id']) . " LIMIT 1";

		$db->setQuery( $sql );

		$res = $db->loadResult();

		$zparams = @unserialize( $res );

		

		$JPATH_BASE = str_replace('administrator', '', JPATH_BASE);

		

		foreach ($data['banner'] as $key=>$val) {

			$rw = intval($val['rw'])!=0 ? intval($val['rw']) :100;

			// add

			if ( isset($val['add']) && ($adslim != 0) ) {

				$query = "INSERT INTO #__ad_agency_campaign_banner(campaign_id, banner_id, relative_weighting) 

                               SELECT {$data['id']} , {$key}, {$val['rw']} FROM #__ad_agency_banners 

                               WHERE id = {$key} and advertiser_id = {$data['aid']}";

				$database->setQuery( $query );

				if (!$database->query()) {}

				

				$sql = "SELECT advertiser_id, media_type, image_url FROM #__ad_agency_banners WHERE id = {$key}";

				$db->setQuery($sql);

				$_banner = $db->loadObject();

				

				if ( ($_banner->media_type == 'TextLink') && ($_banner->image_url != NULL) 

                    && (strlen($_banner->image_url)>4) ) {

					if(isset($_banner->image_url)&&($_banner->image_url != NULL)) {

						$img_url = $JPATH_BASE . 'images' . DS . 'stories' . DS . $imgfolder 

                                        . DS . $_banner->advertiser_id . DS . $_banner->image_url;

						@list($width_orig, $height_orig, $type, $attr) = @getimagesize($img_url);

						

						if(isset($data['adzones'][$key])){

							$query = "SELECT z.adparams,z.textadparams 

                                           FROM #__ad_agency_zone AS z 

                                           WHERE z.zoneid = " . $data['adzones'][$key];

							$db->setQuery($query);

							$res = $db->loadObject();

							$zparams = @unserialize($res->adparams);

							$paramz = @unserialize($res->textadparams);

		

							if ( isset($paramz['mxsize']) && (intval($paramz['mxsize'])>0) && (isset($paramz['mxtype'])) ) {

								if ( ( ($paramz['mxtype'] == 'w') && ($paramz['mxsize'] < $zparams['width']) ) 

                                    || ($zparams['width'] == '') ) {

									$zparams['width'] = $paramz['mxsize'];

								} elseif ( ( ($paramz['mxtype'] == 'h') && ($paramz['mxsize'] < $zparams['height']) ) 

                                            || ($zparams['height'] == '') ) {

									$zparams['height'] = $paramz['mxsize'];

								}

							}

						}

						

						if( isset($zparams['width']) && isset($zparams['height']) && isset($width_orig) 

                            && ($width_orig != NULL) && isset($height_orig) 

                            && ( ($width_orig > $zparams['width']) || ($height_orig > $zparams['height']) ) ) {

							// make the thumb with the maximum size or the zone

							

							$thumb = $this->makeThumb($img_url, $zparams['width'], $zparams['height']);

							if($thumb != NULL) { 

								$sql = "UPDATE `#__ad_agency_campaign_banner` 

                                           SET `thumb` = '" . $thumb . "' 

                                           WHERE `campaign_id` =" . intval($data['id']) . " AND `banner_id` =" . intval($key) . ";";

							} else {

								$sql = "UPDATE `#__ad_agency_campaign_banner` 

                                           SET `thumb` = NULL 

                                           WHERE `campaign_id` =" . intval($data['id']) . " AND `banner_id` =" . intval($key) . ";";

							}

							$sss[] = $sql;

							$db->setQuery($sql);

							$db->query();

						

						}

					}

				}

                $adslim--;

			}

			

			if($this->bannerExist($key, $data['id'])){

				$sql = "UPDATE `#__ad_agency_campaign_banner` 

                           SET `zone` = '".$data['adzones'][$key]."' 

                           WHERE `campaign_id` =".intval($data['id'])." AND `banner_id` =".intval($key);

				$db->setQuery($sql);

				$db->query();

			}

			

			// del

			if (isset($val['del'])) {

				$query = "DELETE FROM #__ad_agency_campaign_banner 

                               WHERE campaign_id={$data['id']} AND banner_id={$key}";

				$database->setQuery($query);

				if (!$database->query()) {}

			}

			// rw update

			if (!isset($val['del']) && !isset($val['add'])) {

				$query = "UPDATE #__ad_agency_campaign_banner SET relative_weighting = {$rw} 

                               WHERE campaign_id={$data['id']} AND banner_id={$key} AND relative_weighting!={$rw}";

				$database->setQuery($query);

				if (!$database->query()) {

				}

			}

		}

		

		if( ($sendmail) && ( isset($currentcmp) && ($currentcmp->approved != $cappr) && ($cappr!='P') ) ) {

			$this->publish($currentcmp->id, $cappr);

		}

		

		if($currentcmp->approved != $cappr){

			$user = JFactory::getUser();

			$action = "Approved";

			if($cappr == "Y"){

				$action = "Approved";

			}

			elseif($cappr == "N"){

				$action = "Declined";

			}

			elseif($cappr == "P"){

				$action = "Pending";

			}

			$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, '".$action." - ".date("Y-m-d H:i:s")." - ".intval($user->id).";') WHERE `id` = '".intval($data['id'])."' ";

			$db->setQuery($sql);

			$db->query();

		}

		

		return true;

	}	



	function bannerExist($b_id, $c_id) {

		$db = JFactory::getDBO();

		$sql = "SELECT COUNT(*) FROM #__ad_agency_campaign_banner 

                   WHERE banner_id='" . intval($b_id) . "' AND campaign_id=" . intval($c_id);

		$db->setQuery($sql);

		$db->query();

		$result = $db->loadResult();

		if($result == "0") {

			return false;

		}

		return true;

	}	



	function makeThumb($file_url, $width, $height) {

		$handle = new Upload($file_url);

		

		list($width_orig, $height_orig) = @getimagesize($file_url);

		if( !isset($width_orig) || ($width_orig == NULL) ) { return false; }

		$ratio_orig = $width_orig/$height_orig;

		if($width == '') { 

            $width = $height*$ratio_orig; 

        } elseif($height == '') { 

            $height = $width/$ratio_orig; 

        }

		if ($width/$height > $ratio_orig) {

		   $width = $height*$ratio_orig;

		} else {

		   $height = $width/$ratio_orig;

		}

		

		$check['width'] = (int) $width;

		$check['height'] = (int) $height;

		if( ($check['width'] == 0) && ($check['height'] == 0) ) {

			return NULL;

		}

		

		$width = number_format($width,0);

		$height = number_format($height,0);



		$pieces = explode(DS,$file_url);

		$filename = explode('.',$pieces[count($pieces)-1]);

		$pieces[count($pieces)-1] = '';

		$pieces = implode(DS,$pieces);

		$dir_dest = $pieces;



		if ($handle->uploaded) {

			$handle->image_resize			= true;

			$handle->image_x				= $width;

			$handle->image_y				= $height;

			$handle->file_new_name_body		= $filename[0].'_w'.$width.'_h'.$height;

			$handle->file_overwrite			= true;

			$handle->jpeg_quality 			= 100;



			$handle->Process($dir_dest);

			if ($handle->processed) {

				return $handle->file_dst_name;

			}  else {

				return false;

			}

		} else {

			return false;

		}

	}	



	function delete(){

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$item = $this->getTable('adagencyCampaigns');

		$database = JFactory::getDBO();

		foreach ($cids as $cid) {

			if (!$item->delete($cid)) {

				$this->setError( $item->getErrorMsg() );

				return false;

			}

			$query = "DELETE FROM #__ad_agency_campaign_banner WHERE campaign_id = '".intval($cid)."'";
			$database->setQuery($query);
			$database->query();

			$sql = "select * from #__ad_agency_statistics where `impressions` like '%\"campaign_id\":\"".intval($cid)."\",%' OR `impressions` like '%\"campaign_id\":".intval($cid).",%' OR `click` like '%\"campaign_id\":\"".intval($cid)."\",%' OR `click` like '%\"campaign_id\":".intval($cid).",%'";
			$database->setQuery($sql);
			$database->query();
			$statistics = $database->loadAssocList();
			
			if(isset($statistics) && count($statistics) > 0){
				foreach($statistics as $key=>$value){
					$id = $value["id"];
					$impressions = @json_decode($value["impressions"], true);
					$click = @json_decode($value["click"], true);
					
					foreach($impressions as $key_imp=>$value_imp){
						if($value_imp["campaign_id"] == intval($cid)){
							unset($impressions[$key_imp]);
						}
					}
					
					foreach($click as $key_click=>$value_click){
						if($value_click["campaign_id"] == intval($cid)){
							unset($click[$key_click]);
						}
					}
					
					$sql = "update #__ad_agency_statistics set `impressions`='".json_encode($impressions)."', `click`='".json_encode($click)."' where `id`=".intval($id);
					$database->setQuery($sql);
					$database->query();
				}
			}
		}

		return true;

	}

	

	function pbl() {
		$db = JFactory::getDBO();
		$data = JRequest::get('post');
		
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
		if($data['task'] == 'unpublish'){
			$mode = 'N';
			$status = "-1";
		}
		elseif($data['task'] == 'publish'){
			$mode= 'Y';
			$status = "1";
		}
		
		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
		$configs = $configs->getConfigs();

		foreach($data['cid'] as $element) {
			$sql = "UPDATE `#__ad_agency_campaign` SET `approved` = '" . stripslashes($mode) . "' WHERE `id` = '" . intval($element) . "';";
			$db->setQuery( $sql );

			if(!$db->query()){
				return false;
			}
			
			//send email notifications
			$sql = "SELECT c.name AS cname, u.name, u.username, u.email 
					   FROM #__ad_agency_campaign AS c 
					   LEFT JOIN #__ad_agency_advertis AS a 
					   ON a.aid=c.aid 
					   LEFT JOIN #__users AS u 
					   ON u.id=a.user_id 
					   WHERE c.id = '".intval($element)."' GROUP BY c.id";
			$db->setQuery($sql);
	
			if(!$result = $db->query()) {
				echo $db->stderr();
				return;
			} 
	
			$user = $db->loadObjectList();
			
			foreach($user as $value){
				$ok_send_email = 1;
				if($data['task'] == 'publish'){
					$subject = $configs->sbcmpappv;
					$message = $configs->bodycmpappv;
					$ok_send_email = $params["send_camp_app"];
				}
				elseif($data['task'] == 'unpublish'){
					$subject = $configs->sbcmpdis;
					$message = $configs->bodycmpdis;
					$ok_send_email = $params["send_camp_dis"];
				}
				
				$subject = str_replace('{name}', $value->name, $subject);
				$subject = str_replace('{login}', $value->username, $subject);
				$subject = str_replace('{email}', $value->email, $subject);
				$subject = str_replace('{campaign}', $value->cname, $subject);
				$message = str_replace('{name}', $value->name, $message);
				$message = str_replace('{login}', $value->username, $message);
				$message = str_replace('{email}', $value->email, $message);
				$message = str_replace('{campaign}', $value->cname, $message);
				// mail publish campaign
				if($ok_send_email == 1){
					JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $value->email, $subject, $message, 1);
				}
			}
			//send email notifications
		}

		if($mode == N){
			return -1;
		}
		else{
			return 1;
		}
	}



	function publish ($cid, $task) {

		$db = JFactory::getDBO();

		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);

		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");

		$configs = $configs->getConfigs();

		$item = $this->getTable('adagencyCampaigns');

		if ($task == 'Y') {

			$sql = "UPDATE #__ad_agency_campaign SET approved='Y' WHERE id = '" . intval($cid) . "' ";

			$ret = 1;

		} elseif($task == 'P') {

			$sql = "UPDATE #__ad_agency_campaign SET approved='P' WHERE id = '" . intval($cid) . "' ";

			$ret = 0;

		} else {

			$sql = "UPDATE #__ad_agency_campaign SET approved='N' WHERE id = '" . intval($cid) . "' ";

			$ret = -1;	

		}

		$db->setQuery($sql);

		if (!$db->query()) {

			$this->setError($db->getErrorMsg());

			return false;

		}



		//send email notifications

        $sql = "SELECT c.name AS cname, u.name, u.username, u.email 

                   FROM #__ad_agency_campaign AS c 

                   LEFT JOIN #__ad_agency_advertis AS a 

                   ON a.aid=c.aid 

                   LEFT JOIN #__users AS u 

                   ON u.id=a.user_id 

                   WHERE c.id = '" . intval($cid) . "' GROUP BY c.id";

        $db->setQuery($sql);

        if(!$result = $db->query()) {

            echo $db->stderr();

            return;

        } 

        $user = $db->loadObjectList();

        foreach ( $user as $value) {			
			$ok_send_email = 1;
			
			if($task == 'Y'){
                $subject = $configs->sbcmpappv;
                $message = $configs->bodycmpappv;
				$ok_send_email = $params["send_camp_app"];
            }
			else{
                $subject = $configs->sbcmpdis;
                $message = $configs->bodycmpdis;
				$ok_send_email = $params["send_camp_dis"];
            }

            $subject = str_replace('{name}', $value->name, $subject);

            $subject = str_replace('{login}', $value->username, $subject);

            $subject = str_replace('{email}', $value->email, $subject);

            $subject = str_replace('{campaign}', $value->cname, $subject);

            $message = str_replace('{name}', $value->name, $message);

            $message = str_replace('{login}', $value->username, $message);

            $message = str_replace('{email}', $value->email, $message);

            $message = str_replace('{campaign}', $value->cname, $message);

            // mail publish campaign            
			if($ok_send_email == 1){
				JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $value->email, $subject, $message, 1);			
			}
        }

		//send email notifications

		return $ret;

	}

	

	function getcmplistAdvertisers () {

		if (empty ($this->_package)) {

			$db = JFactory::getDBO();

			$sql = "SELECT a.aid, b.name AS company, a.user_id 

                       FROM #__ad_agency_advertis AS a, #__users AS b 

                       WHERE a.user_id = b.id ORDER BY company ASC";

			$db->setQuery($sql);

			if (!$db->query()) {

				echo $db->stderr();

				return;

			}

			$this->_package = $db->loadObjectList();

		}

		return $this->_package;

	}

	

	function pause() {

		$db = JFactory::getDBO();

        $cmp = JRequest::getInt('cid');

		$sql = "UPDATE #__ad_agency_campaign SET status='0' WHERE id='" . intval($cmp) . "'";

		$db->setQuery($sql);

		if (!$db->query() ){

			$this->setError($db->getErrorMsg());

			return false;

		}

		

		// -----------------------------------

		$user = JFactory::getUser();

		$action = "Paused";

		$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, '".$action." - ".date("Y-m-d H:i:s")." - ".intval($user->id).";') WHERE `id` = '".intval($cmp)."' ";

		$db->setQuery($sql);

		$db->query();

		// -----------------------------------

		

		return true;	

	}

	

	function unpause() {

		$db = JFactory::getDBO();

        $cmp = JRequest::getInt('cid');

		$sql = "UPDATE #__ad_agency_campaign SET status='1' WHERE id='" . intval($cmp) . "'";

		$db->setQuery($sql);

        if (!$db->query() ){

            $this->setError($db->getErrorMsg());

            return false;

        }

		

		// -----------------------------------

		$user = JFactory::getUser();

		$action = "Active";

		$sql = "UPDATE `#__ad_agency_campaign` SET `activities` = concat(activities, '".$action." - ".date("Y-m-d H:i:s")." - ".intval($user->id).";') WHERE `id` = '".intval($cmp)."' ";

		$db->setQuery($sql);

		$db->query();

		// -----------------------------------

		

		return true;

	}

	

	function saveorder($idArray = null, $lft_array = null){

		// Get an instance of the table object.

		$table = $this->getTable("adagencyCampaigns");



		if(!$table->saveorder($idArray, $lft_array)){

			$this->setError($table->getError());

			return false;

		}

		// Clean the cache

		$this->cleanCache();

		return true;

	}

	

	function getlistCampsAds(){

		$cid = JRequest::getVar("cid", array(), "get", "array");

		$db = JFactory::getDBO();

		@$sql = "select `banner_id` from #__ad_agency_campaign_banner where `campaign_id`=".intval($cid["0"]);

		$db->setQuery($sql);

		$db->query();

		$result = $db->loadColumn();

		return $result;

	}



};

?>