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
jimport('phpmailer.phpmailer');
if(!class_exists('upload')){
	require_once('administrator/components/com_adagency/helpers/class.upload.php');
}

class adagencyModeladagencyCampaigns extends JModelLegacy {
	var $_attributes;
	var $_attribute;
	var $_id = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
	}

	function setId($id) {
		$this->_id = $id;
		$this->_attribute = null;
	}

	function getlistCampaigns(){
		if(empty ($this->_attributes)){
			$and = "";
			$status = JRequest::getVar("status_filter", "-1");
			$payment_filter = JRequest::getVar("payment_filter", "-1");
			$approval_filter = JRequest::getVar("approval_filter", "-1");
			
			if($status != "-1"){
				switch($status){
					case "1" : {//active
							$and .= " and c.status = 1 ";
							break;
						}
					case "0" : {//inactive
							$and .= " and c.status = 0 ";
							break;
						}
					case "2" : {//expired
							$config = JFactory::getConfig();
							$siteOffset = $config->get('offset');
							$jnow = JFactory::getDate('now', $siteOffset);
							
							$and .= " and (c.validity < '".$jnow."' and c.validity <> '0000-00-00 00:00:00' ) ";
							break;
						}
				}
			}
			
			if($approval_filter != "-1"){
				switch($approval_filter){
					case "Y" : {//approved
							$and .= " and c.approved = 'Y' ";
							break;
						}
					case "P" : {//pending
							$and .= " and c.approved = 'P' ";
							break;
						}
					case "N" : {//declined
							$and .= " and c.approved = 'N' ";
							break;
						}
				}
			}
			
			$my = JFactory::getUser();
			$sql =  "SELECT c.id, c.name, c.quantity, c.validity AS camp_validity, c.start_date, a.company, a.user_id, 
                        c.type, c.approved, c.status, COUNT(DISTINCT cb.banner_id) cnt, pk.validity, c.otid
                        FROM #__ad_agency_campaign AS c 
                        LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb 
                        ON c.id=cb.campaign_id	
                        LEFT JOIN #__ad_agency_order_type AS pk
                        ON c.otid = pk.tid
                        LEFT JOIN #__ad_agency_advertis AS a ON a.aid=c.aid 
                        WHERE a.user_id=".intval($my->id)."
						".$and."
						and c.approved <> 'N'
						and c.status <> -1
						GROUP BY c.id ORDER BY c.id DESC";
			$this->_attributes = $this->_getList($sql);
		}
		
		if($payment_filter != "-1"){
			$db = JFactory::getDBO();
			$status_payment = "paid";
			switch($payment_filter){
				case "0" : {//paid
						$status_payment = "paid";
						break;
					}
				case "1" : {//unpaid
						$status_payment = "not_paid";
						break;
					}
			}
			if(isset($this->_attributes) && count($this->_attributes) > 0){
				foreach($this->_attributes as $key=>$value){
					$card_number = $value->id.";".$value->otid;
					$sql = "select `status` from #__ad_agency_order where `card_number`='".trim($card_number)."'";
					$db->setQuery($sql);
					$db->query();
					$result = $db->loadColumn();
					$result = $result["0"];
					
					if($result != $status_payment){
						unset($this->_attributes[$key]);
					}
				}
			}
		}
		
		return $this->_attributes;
	}

	function getCampaign() {
		if (empty ($this->_attribute)) {
			$this->_attribute =$this->getTable("adagencycampaigns");
			$this->_attribute->load($this->_id);
		}
		$data = JRequest::get('request');

		if (!$this->_attribute->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;

		}

		if (!$this->_attribute->check()) {
			$this->setError($item->getErrorMsg());
			return false;

		}
		
		if($this->_attribute->id == 0 || $this->_attribute->id == ""){
			$joomla_date = new JDate();
			$this->_attribute->start_date = $joomla_date->toSql();
		}
		
		return $this->_attribute;
	}

	function getPermForAdv($cid,$aid){
		if ($cid==0) {return true;}
		$db =  JFactory::getDBO();
		$sql = "SELECT id FROM #__ad_agency_campaign WHERE id='".intval($cid)."' AND aid='".intval($aid)."'";
		$db->setQuery($sql);
		$perm = $db->loadColumn();
		$perm = $perm["0"];
		
		if(isset($perm)&&($perm!=NULL)) { return true; } else { return false;}
	}

	function getAllPacks(){
		$db =  JFactory::getDBO();
		$query = 	"SELECT p . * , z.adparams
					FROM #__ad_agency_order_type AS p
					LEFT JOIN #__ad_agency_zone AS z ON p.location = z.zoneid";
		$db->setQuery($query);
		$result = $db->loadObjectList();
		return $result;
	}

	function getlistPackagesEdit () {
		$database =  JFactory::getDBO();
		$my =  JFactory::getUser();

		$database->setQuery("SELECT aid FROM #__ad_agency_advertis WHERE user_id=".intval($my->id));
		$aid = $database->loadColumn();
		$aid = $aid["0"];

		if (!$aid) return;
		$sql = "SELECT * FROM #__ad_agency_order_type AS p INNER JOIN #__ad_agency_order AS o WHERE p.tid = o.tid AND o.aid = ".intval($aid)." AND o.status='paid'";

		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		if(count($rows)<1) {
			$cids=$_GET['cid'];
			$cids = $cids[0];
			$sql = "SELECT `otid` FROM #__ad_agency_campaign WHERE id = ".intval($cids);
			$database->setQuery($sql);
			$otid = $database->loadColumn();
			$otid = $otid["0"];
			
			$sql = "SELECT * FROM #__ad_agency_order_type WHERE tid = ".intval($otid);
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
		}
		return $rows;

	}

	function getlistPackages () {
		$database = JFactory::getDBO();
		$my =  JFactory::getUser();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
		$database->setQuery($sql);
		$advid = $database->loadColumn();
		$advid = $advid["0"];
		
		$sql = "SELECT r.*,z.adparams
				FROM `#__ad_agency_order_type` AS r
				LEFT JOIN #__ad_agency_zone AS z
				ON r.location = z.zoneid
				WHERE r.visibility <>0
				AND r.published = 1
				AND r.tid NOT
				IN (
					SELECT p.tid
					FROM `#__ad_agency_order_type` AS p, `#__ad_agency_order` AS o
					WHERE p.visibility <>0
					AND p.cost = '0.00'
					AND hide_after =1
					AND p.tid = o.tid
					AND o.aid = ".intval($advid)."
				)
				ORDER BY r.ordering ASC
		";
		//echo $sql;die();
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		return $rows;
	}

	function getBuyedAvailablePacksForAid() {
		$db = JFactory::getDBO();
		$advertiser = $this->getCurrentAdvertiser();
		$aid = (int)$advertiser->aid;
		$sql = "SELECT * FROM #__ad_agency_order WHERE pack_id = '0' AND aid =".intval($aid)." AND `status` = 'paid'";
		$db->setQuery($sql);
		$rezultat = $db->loadResult();
		return $rezultat;
	}

	function getTimeFormat() {
		$database = JFactory::getDBO();
		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$database->setQuery($query);
		$params = $database->loadColumn();
		$params = $params["0"];
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "-1"; }
		return $params;
	}

	function getAdvInfo($id) {
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__ad_agency_advertis WHERE aid=".intval($id);
		$db->setQuery($query);
		$result = $db->loadObject();
		return $result;
	}

	function getBannerCount() {
		$db = JFactory::getDBO();
		$advertiser = $this->getCurrentAdvertiser();
		$aid = (int)$advertiser->aid;
		$sql = "SELECT count(*) FROM #__ad_agency_banners WHERE `advertiser_id`=".intval($aid);
		$db->setQuery($sql);
		$res = $db->loadColumn();
		$res = $res["0"];
		return $res;
	}

	function getCurrentAdvertiser() {
		$db =  JFactory::getDBO();
		$my =  JFactory::getUser();
		$sql = "SELECT * FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function formatime2($time,$option = 1){
		$date_time = explode(" ",$time);
		$date_time[0] = str_replace("/","-",$date_time[0]);
		$tdate = explode("-",$date_time[0]);
		if (($option == 1)||($option == 2)) {
			$aux=$tdate[0];
			$tdate[0]=$tdate[2];
			$tdate[2]=$aux;
		}
		elseif (($option == 3)||($option == 4)) {
			//mm-dd-yyyy
			$aux=$tdate[0];
			$tdate[0]=$tdate[2];
			$tdate[2]=$tdate[1];
			$tdate[1]=$aux;
		}
		$output = NULL;
		if(!isset($date_time[1])) {$date_time[1] = NULL;}
		$output = $tdate[0]."-".$tdate[1]."-".$tdate[2]." ".$date_time[1];
		return trim($output);
	}

	function getCountBannersPerAdv($id){
		$db = JFactory::getDBO();
		$sql = "SELECT COUNT(id) FROM #__ad_agency_banners WHERE advertiser_id = ".intval($id);
		$db->setQuery($sql);
		$result = $db->loadColumn();
		$result = $result["0"];
		return $result;
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
			return $res;
		} else {
			return NULL;
		}
	}

	function getCmpById($id){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_campaign WHERE id = ".intval($id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}

	function store(){
		$data = JRequest::get('post');
		$item = $this->getTable('adagencyCampaigns');
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$remove_action = JRequest::getVar("remove_action", "");
		
		if(isset($data["most_recent_available_date"])){
			$data["start_date"] = $data["most_recent_available_date"];
		}
		
		$update_status = 1;

		$otid = $data['otid'];
		$data['key'] = NULL;
		
		if(isset($data['banner'])) {
			$post_banner = $data['banner'];
		}
		$configs = $this->getInstance("adagencyConfig", "adagencyModel");
		$configs = $configs->getConfigs();
        $configs->params = @unserialize( $configs->params );

		$campnou=false;
		// Stuff for new campaigns
		if(!isset($data['id']) || ($data['id'] < 1)){
			$campnou=true;
			$data['key']=md5(rand(1000,9999));

            if(isset($configs->params['adslim'])){
				$data['params']['adslim'] = (int)$configs->params['adslim'];
            }
			else{
                $data['params']['adslim'] = 999;
            }

            $adslim = $data['params']['adslim'];
            $data['params'] = @serialize( $data['params']);

        // Stuff for existing campaigns
        }
		else{
			$current_camp = $this->getCmpById($data['id']);
            $current_camp->params = @unserialize($current_camp->params);

            if(isset($current_camp->params['adslim'])){
                $adslim = (int)$current_camp->params['adslim'];
            }
			else{
                $adslim = 999;
            }	
        }
		
		if(($campnou || $remove_action != "") && $data['otid'] > 0){
			$sql = "SELECT * FROM #__ad_agency_order_type WHERE tid=" .intval($data['otid']);
			$sqlz[] = $sql;
			$database->setQuery($sql);
			if(!$result = $database->query()) {				
				echo $database->stderr();
				return;
			}
		

		$rows = $database->loadObjectList();
		$package_row = $rows[0];
		

		$data['type'] = $package_row->type;
		$data['cost'] = $package_row->cost;
		
		unset($_SESSION["discount"]);
		unset($_SESSION["new_cost"]);
		unset($_SESSION["promocode"]);

		if(isset($data["promocode"]) && trim($data["promocode"]) != ""){
			$promo_code = trim($data["promocode"]);
			$sql = "select * from #__ad_agency_promocodes where `code`='".addslashes(trim($promo_code))."'";
			$db->setQuery($sql);
			$db->query();
			$promo_details = $db->loadAssocList();
			
			$now = time();
			
			if(($promo_details["0"]["codestart"] <= $now) && ($promo_details["0"]["codeend"] >= $now || $promo_details["0"]["codeend"] == 0)){
				$access = TRUE;
				if($promo_details["0"]["codelimit"] != "" && $promo_details["0"]["codelimit"] != "0" && ($promo_details["0"]["codelimit"] - $promo_details["0"]["used"] <= 0)){
					$access = FALSE;
				}
				if($access){
					if($promo_details["0"]["amount"] > 0){
						$amount = trim($promo_details["0"]["amount"]);
						$promotype = trim($promo_details["0"]["promotype"]);
						if($promotype == 0){
							$_SESSION["discount"] = $amount;
							$data["cost"] = $data["cost"] - $amount;
							$data["cost"] = round($data["cost"], 2);
							if($data["cost"] < 0){
								$data["cost"] = 0;
							}
						}
						else{
							$procent = ($data["cost"] * $amount)/100;
							$_SESSION["discount"] = $procent;
							$data["cost"] = $data["cost"] - $procent;
							$data["cost"] = round($data["cost"], 2);
						}
						$data["promocodeid"] = $promo_details["0"]["id"];
						$_SESSION["promocode"] = $promo_details["0"]["id"];
						$_SESSION["new_cost"] = $data["cost"];
					}
				}
			}

		}
		//$remove_action = JRequest::getVar("remove_action", "");
	
////here
		if($remove_action != ""){
			
			$strtdate = JRequest::getVar("start_date", "");
			$strtdate = strtotime($strtdate);
			$strtdate = date("Y-m-d H:i:s", $strtdate);
			
			$tmp_date_time = explode(" ", $strtdate, 2);
			if ($tmp_date_time[0]) {
				$tmp_date = explode("-", $tmp_date_time[0], 3);
			}
			else {
				$tmp_date[0] = 0;
				$tmp_date[1] = 0;
				$tmp_date[2] = 0;
			}

			if (isset($tmp_date_time[1])) {
				$tmp_time = explode(":", $tmp_date_time[1], 3);
			}
			else {
				$tmp_time[0] = 0;
				$tmp_time[1] = 0;
				$tmp_time[2] = 0;
			}
			
			$tmp = explode("|", $package_row->validity, 2);

			if ($tmp[1]=="day") {
				$validity = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2] + $tmp[0],$tmp_date[0]));
			} elseif ($tmp[1]=="week") {
				$validity = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2] + (7*$tmp[0]),$tmp_date[0]));
			} elseif ($tmp[1]=="month") {
				$validity = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1] + $tmp[0],$tmp_date[2],$tmp_date[0]));
			} elseif ($tmp[1]=="year") {
				$validity = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2],$tmp_date[0] + $tmp[0]));
			}
			$activities = explode(" - ", $current_camp->activities);
			
			if($activities['0'] == "Purchased(new)"){			
				$sql = "UPDATE #__ad_agency_campaign SET `start_date`='".trim($strtdate)."',`exp_notice`= 0, `validity` ='".$validity."', `approved`='P' WHERE `id`=".intval($current_camp->id);
				$db->setQuery($sql);
				$db->query();
				
				
			}

			$db = JFactory::getDBO();
			$item_id = JRequest::getInt('Itemid','0');
			$Itemid = "";
			if($item_id != 0){
				$Itemid = "&Itemid=".$item_id;
			}
			else{
				$Itemid = NULL;
			}
			$app = JFactory::getApplication("site");
			$otid = JRequest::getVar("otid", "0");
			if(isset($data["cost"]) && $data["cost"] != ""){
				$orderid = JRequest::getVar("orderid", "");
				$sql = "UPDATE #__ad_agency_order SET `cost`='".trim($data["cost"])."' WHERE `oid`=".intval($orderid);
				$db->setQuery($sql);
				$db->query();
			}
			$app->redirect("index.php?option=com_adagency&controller=adagencyOrders&task=order&tid=".intval($otid)."&orderid=".intval($orderid).$Itemid);
			
		}

		
		$sql4="UPDATE #__ad_agency_order SET `pack_id`='1' WHERE `tid`='".intval($package_row->tid)."' AND `notes`='".addslashes(trim($package_row->description))."' AND `cost`='".trim($package_row->cost)."'";
		$sqlz[] = $sql4;
		$database->setQuery($sql4);
		$database->query();
		
		if ($data['type'] == "fr" || $data['type'] == "in") {
			$start_date = $data['start_date'];
			$start_date = strtotime($start_date);
			$start_date = date("Y-m-d H:i:s", $start_date);
			
			$tmp_date_time = explode(" ", $start_date, 2);
			if ($tmp_date_time[0]) {
				$tmp_date = explode("-", $tmp_date_time[0], 3);
			}
			else {
				$tmp_date[0] = 0;
				$tmp_date[1] = 0;
				$tmp_date[2] = 0;
			}

			if (isset($tmp_date_time[1])) {
				$tmp_time = explode(":", $tmp_date_time[1], 3);
			}
			else {
				$tmp_time[0] = 0;
				$tmp_time[1] = 0;
				$tmp_time[2] = 0;
			}
			
			$tmp = explode("|", $package_row->validity, 2);

			if ($tmp[1]=="day") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2] + $tmp[0],$tmp_date[0]));
			} elseif ($tmp[1]=="week") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2] + (7*$tmp[0]),$tmp_date[0]));
			} elseif ($tmp[1]=="month") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1] + $tmp[0],$tmp_date[2],$tmp_date[0]));
			} elseif ($tmp[1]=="year") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2],$tmp_date[0] + $tmp[0]));
			}
		}
		else {
			$data['quantity'] = $package_row->quantity;
		}
	} else {
		$sql = "SELECT * FROM #__ad_agency_order_type WHERE tid=".intval($data['otid']);
		$sqlz[] = $sql;
		$database->setQuery($sql);
		if(!$result = $database->query()) {
			echo $database->stderr();
			return;
		}

		$rows = $database->loadObjectList();
		$package_row = $rows[0];
		$data['type'] = $package_row->type;
		$data['cost'] = $package_row->cost;
		$sql4 = "UPDATE #__ad_agency_order SET `pack_id`='1' WHERE `oid`='".intval($typp)."'";
		$sqlz[] = $sql4;
		$database->setQuery($sql4);
		$database->query();
		
		if ($data['type'] == "fr" || $data['type'] == "in") {
			$start_date = $data['start_date'];
			$start_date = strtotime($start_date);
			$start_date = date("Y-m-d H:i:s", $start_date);
			
			$tmp_date_time = explode(" ", $start_date, 2);
			if ($tmp_date_time[0]) {
				$tmp_date = explode("-", $tmp_date_time[0], 3);
			}
			else {
				$tmp_date[0] = 0;
				$tmp_date[1] = 0;
				$tmp_date[2] = 0;
			}

			if ($tmp_date_time[1]) {
				$tmp_time = explode(":", $tmp_date_time[1], 3);
			}
			else {
				$tmp_time[0] = 0;
				$tmp_time[1] = 0;
				$tmp_time[2] = 0;
			}

			$tmp = explode("|", $package_row->validity, 2);
			
			if ($tmp[1]=="day") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2] + $tmp[0],$tmp_date[0]));
			} elseif ($tmp[1]=="week") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2] + (7*$tmp[0]),$tmp_date[0]));
			} elseif ($tmp[1]=="month") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1] + $tmp[0],$tmp_date[2],$tmp_date[0]));
			} elseif ($tmp[1]=="year") {
				$data['validity'] = date("Y-m-d H:i:s", mktime($tmp_time[0],$tmp_time[1],$tmp_time[2],$tmp_date[1],$tmp_date[2],$tmp_date[0] + $tmp[0]));
			}

		}
		else {

		}

	}
		// Check settings for auto-approve ads [begin]

		// Get global setting
		$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='approved'";
		$sqlz[] = $query;
		$database->setQuery($query);
		$result = $database->loadRow();
		$aux_approved = $result[4];

		// Get Advertiser setting
		$info = $this->getAdvInfo($data['aid']);
		if(isset($info->apr_ads)&&($info->apr_cmp!='G')){
			$aux_approved = $info->apr_cmp;
		}
		elseif($info->apr_cmp == 'G'){
			unset($data['approved']);
		}
		
		if($info->apr_cmp == 'N'){
			$data['approved'] = "P";
		}
		elseif($info->apr_cmp == 'Y'){
			$data['approved'] = "Y";
		}

		if(($data['id']>0)&&($aux_approved == 'P')&&($update_status == 1)){
			// do nothing
		}
		elseif($data['id']<=0) {
			$sql = "SELECT cost FROM #__ad_agency_order_type WHERE tid = '".intval($data['otid'])."' ;";
			$db->setQuery($sql);
			$package_cost = $db->loadColumn();
			$package_cost = $package_cost["0"];
			
			if($package_cost != "0.00" && $data['cost'] != 0){
				$aux_approved = 'P';
				$data['status'] = "0";
			}
		}
		// Check settings for auto-approve ads [end]

		$aurorenew = JRequest::getVar("autorenewcmp", "0");
		$data["renewcmp"] = intval($aurorenew);
	
		$date_format = 'Y-m-d H:i:s';		
		$datee = new JDate($data["start_date"]); 
		$data["start_date"] = $datee->format($date_format);
		
		if (!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		if(!isset($data['id']) || $data['id']==0){
			$data['id'] = @mysql_insert_id();
		}
		
		if($data['id'] == 0){
			$ask = "SELECT `id` FROM `#__ad_agency_campaign` ORDER BY `id` DESC LIMIT 1 ";
			$sqlz[] = $ask;
			$db->setQuery( $ask );
			$id_selected = $db->loadColumn(); 
			$data['id'] = $id_selected["0"];
		}

		$sql_cbrw = "DELETE FROM `#__ad_agency_campaign_banner` WHERE `campaign_id` = ".intval($db->escape($data['id']));
		$sqlz[] = $sql_cbrw;
		$db->setQuery($sql_cbrw);
		$db->query();

		$sql = "SELECT z.adparams FROM #__ad_agency_campaign AS c LEFT JOIN #__ad_agency_order_type AS p ON c.otid = p.tid LEFT JOIN #__ad_agency_zone AS z ON p.location = z.zoneid WHERE c.id = ".intval($data['id'])." LIMIT 1";
		$sqlz[] = $sql;
		$db->setQuery($sql);
		$res = $db->loadColumn();
		$res = $res["0"];
		$zparams = @unserialize($res);

		if(isset($data["ad"]) && intval($data["ad"]) > 0){
			$temp = array("add"=>$data["ad"], "rw"=>100);
			$post_banner = array($data["ad"]=>$temp);
		}

		if(isset($post_banner))
		foreach ($post_banner as $key=>$val) {
			$_banner = NULL;
			$rw = intval($val['rw'])!=0?intval($val['rw']):100;

			// add
			if ( isset($val['add']) && ($adslim != 0) ) {
				$query = "INSERT INTO #__ad_agency_campaign_banner(campaign_id, banner_id, relative_weighting) SELECT {$data['id']} , {$key}, {$val['rw']} FROM #__ad_agency_banners WHERE id = ".intval($key)." AND advertiser_id = ".intval($data['aid']);
				$sqlz[] = $query;
				$database->setQuery($query);
				if (!$database->query()) {}

				$sql = "SELECT advertiser_id, media_type, image_url FROM #__ad_agency_banners WHERE id = ".intval($key);
				$sqlz[] = $sql;
				$db->setQuery($sql);
				$_banner = $db->loadObject();

				if(($_banner->media_type == 'TextLink')&&($_banner->image_url != NULL)&&(strlen($_banner->image_url)>4)) {
					if(isset($_banner->image_url)&&($_banner->image_url != NULL)) {
						$img_url = JPATH_BASE.DS.'images'.DS.'stories'.DS.$configs->imgfolder.DS.$_banner->advertiser_id.DS.$_banner->image_url;
						@list($width_orig, $height_orig, $type, $attr) = @getimagesize($img_url);

						if(isset($data['adzones'][$key])){
							$query = "SELECT z.adparams,z.textadparams FROM #__ad_agency_zone AS z WHERE z.zoneid = ".intval($data['adzones'][$key]);
							$db->setQuery($query);
							$res = $db->loadObject();
							$zparams = @unserialize($res->adparams);
							$paramz = @unserialize($res->textadparams);

							if(isset($paramz['mxsize'])&&(intval($paramz['mxsize'])>0)&&(isset($paramz['mxtype']))) {
								if((($paramz['mxtype'] == 'w')&&($paramz['mxsize'] < $zparams['width']))||($zparams['width'] == '')){
									//$ratio = $zparams['width']/$zparams['height'];$zparams['height'] = $zparams['width']/$ratio;
									$zparams['width'] = $paramz['mxsize'];
								} elseif((($paramz['mxtype'] == 'h')&&($paramz['mxsize'] < $zparams['height']))||($zparams['height'] == '')){
									//$ratio = $zparams['width']/$zparams['height'];$zparams['width'] = $ratio * $zparams['height'];
									$zparams['height'] = $paramz['mxsize'];
								}
							}
						}

						if(isset($zparams['width']) && isset($zparams['height']) && isset($width_orig) && ($width_orig != NULL) && isset($height_orig) && (($width_orig > $zparams['width'])||($height_orig > $zparams['height'])) ) {
							// make the thumb with the maximum size or the zone

							$thumb = $this->makeThumb($img_url, $zparams['width'], $zparams['height']);
							if($thumb != NULL) {
								$sql = "UPDATE `#__ad_agency_campaign_banner` SET `thumb` = '".trim($thumb)."' WHERE `campaign_id` =".intval($data['id'])." AND `banner_id` =".intval($key).";";
							} else {
								$sql = "UPDATE `#__ad_agency_campaign_banner` SET `thumb` = NULL WHERE `campaign_id` =".intval($data['id'])." AND `banner_id` =".intval($key).";";
							}
							$sqlz[] = $sql;
							$db->setQuery($sql);
							$db->query();

						}
					}
				}

                $adslim--;
			}

			if($this->bannerExist($key, $data['id'])){
				$sql = "UPDATE `#__ad_agency_campaign_banner` SET `zone` = '".intval($data['adzones'][$key])."' WHERE `campaign_id` =".intval($data['id'])." AND `banner_id` =".intval($key);
				$db->setQuery($sql);
				$db->query();
			}

			if (isset($val['del'])) {
				$sql = "DELETE FROM #__ad_agency_campaign_banner WHERE campaign_id = '".intval($data['id'])."'  AND banner_id = '".intval($key)."'";
				$sqlz[] = $sql;
				$db->setQuery($sql);
				$db->query();
			}

			// rw update
			if (!isset($val['del']) && !isset($val['add'])) {
				$query = "UPDATE #__ad_agency_campaign_banner SET relative_weighting = {$rw}  WHERE campaign_id=".intval($data['id'])." AND banner_id=".intval($key)." AND relative_weighting!=".intval($rw);
				$sqlz[] = $query;
				$database->setQuery($query);
				if (!$database->query()) {

				}
			}
		}

		$camp_row = $this->getCmpById($data['id']);
	
		if ($campnou) {
			// if package is buyable than we make the campaign in pending
			$sql = "SELECT cost FROM #__ad_agency_order_type WHERE tid = ".intval($otid);
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$package_cost = $database->loadColumn();
			$package_cost = $package_cost["0"];
			
			if($package_cost != "0.00" && $data['cost'] != 0){
				$sql = "UPDATE `#__ad_agency_campaign` SET `approved` = 'P' WHERE `id` = '".intval($camp_row->id)."'";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$database->query();
			}
	
			$sql = "SELECT a.user_id, a.company, a.telephone as phone, u.name, u.username, u.email FROM #__ad_agency_advertis AS a LEFT OUTER JOIN #__users as u on u.id=a.user_id WHERE a.aid=".intval($camp_row->aid)." GROUP BY a.aid";
			$sqlz[] = $sql;
			$database->setQuery($sql);
			$user = $database->loadObject();
			if(isset($user)&&($user!=NULL)){
				if(!isset($user->company)) {$user->company = "N/A";}
				if(!isset($user->phone)) {$user->phone = "N/A";}
				if(isset($camp_row->approved)&&($camp_row->approved == 'Y')) {
					$camp_row->approved = JText::_('NEWADAPPROVED');
				} elseif (isset($camp_row->approved)&&($camp_row->approved == 'P')) {
					$camp_row->approved = JText::_('VIEW_CAMPAIGN_PENDING');
				} elseif(isset($camp_row->approved)&&($camp_row->approved == 'N')) {
					$camp_row->approved = JText::_('VIEW_CAMPAIGN_REJECTED');
				}
	
				$sql = "SELECT description FROM #__ad_agency_order_type WHERE tid = ".intval($camp_row->otid)." LIMIT 1";
				$sqlz[] = $sql;
				$database->setQuery($sql);
				$description_selected = $database->loadColumn();
				$camp_row->otid = $description_selected["0"];
				
				$sql = "select `params` from #__ad_agency_settings";
				$database->setQuery($sql);
				$database->query();
				$email_params = $database->loadColumn();
				$email_params = @$email_params["0"];
				$email_params = unserialize($email_params);
				
				$subject=$configs->sbnewcmp;
				$message=$configs->bodynewcmp;
				$subject =str_replace('{name}',$user->name,$subject);
				$subject =str_replace('{email}',$user->email,$subject);
				$subject =str_replace('{phone}',$user->phone,$subject);
				$subject =str_replace('{campaign}',$camp_row->name,$subject);
				$subject =str_replace('{company}',$user->company,$subject);
				$subject =str_replace('{approve_campaign_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=manage&action=approve&key=".$data['key']."&cid=".$camp_row->id,$subject);
				$subject =str_replace('{decline_campaign_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=manage&action=decline&key=".$data['key']."&cid=".$camp_row->id,$subject);
				$subject =str_replace('{approval_status}',$camp_row->approved,$subject);
				$subject =str_replace('{package}',$camp_row->otid,$subject);
	
				$message =str_replace('{name}',$user->name,$message);
				$message =str_replace('{email}',$user->email,$message);
				$message =str_replace('{phone}',$user->phone,$message);
				$message =str_replace('{company}',$user->company,$message);
				$message =str_replace('{campaign}',$camp_row->name,$message);
				$message =str_replace('{package}',$camp_row->otid,$message);
				$message =str_replace('{approve_campaign_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=manage&action=approve&key=".$data['key']."&cid=".$camp_row->id,$message);
				$message =str_replace('{decline_campaign_url}',JURI::root()."index.php?option=com_adagency&controller=adagencyCampaigns&task=manage&action=decline&key=".$data['key']."&cid=".$camp_row->id,$message);
				$message =str_replace('{approval_status}',$camp_row->approved,$message);
				if($email_params["send_camp_added"] == 1){
					JFactory::getMailer()->sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject, $message, 1 );
				}
			}
			if(isset($_SESSION['LCC'])){
				$_SESSION['LCC'] = NULL;$_SESSION['LCC2']=NULL;
				unset($_SESSION['LCC']);unset($_SESSION['LCC2']);
			}
	
			$_SESSION['LCC'] = $camp_row->id;
			$_SESSION['LCC2'] = $otid;
	
			$item_id = JRequest::getInt('Itemid','0');
			if($item_id != 0) { $Itemid = "&Itemid=".$item_id; } else { $Itemid = NULL; }
	
	
			global $mainframe;
			
			$db = JFactory::getDBO();
			$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='renewcmp'";
			$db->setQuery($query);
			$result = $db->loadRow();
			$renewcmp = $result["4"];
			if($renewcmp == "0"){// No
				$_SESSION["aurorenew"] = "0";
			}
			elseif($renewcmp == "1"){// Yes
				$_SESSION["aurorenew"] = "1";
			}
			elseif($renewcmp == "2"){// Ask Advertisers
				$aurorenew = JRequest::getVar("autorenewcmp", "0");
				$_SESSION["aurorenew"] = $aurorenew;
			}
			
			$mainframe->redirect("index.php?option=com_adagency&controller=adagencyOrders&task=order&tid=".$data['otid'].$Itemid);
		}
		return true;
	}


    function savechangecb() {
        $db = JFactory::getDBO();
        $adv = $this->getCurrentAdvertiser();
        $data = JRequest::get('post');
        $data['campaignid'] = (int) $data['campaignid'];
        $deleted_banners = "(" . implode(',', $data['todel']) . ")";

        $sql = "DELETE FROM #__ad_agency_campaign_banner
                WHERE campaign_id = '".intval($data['campaignid'])."'
                AND banner_id IN " .trim($deleted_banners);
        $sqlz[] = $sql;
        $db->setQuery($sql);
        $db->query();

        // return the new count of banners that are assigned
        // to campaign
        $sql = "SELECT COUNT( banner_id )
                FROM #__ad_agency_campaign_banner
                WHERE campaign_id = ".intval($data['campaignid']);
        $sqlz[] = $sql;
        $db->setQuery($sql);
        $totalads = $db->loadColumn();
		$totalads = $totalads["0"];
        $vars = new stdClass();
        $vars->totalads = $totalads;
        $vars->cid = $data['campaignid'];

        return $vars;
    }

    function bannerExist($b_id, $c_id){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_campaign_banner where banner_id=".intval($b_id)." and campaign_id=".intval($c_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		$result = $result["0"];
		if($result == "0"){
			return false;
		}
		return true;
	}

    function getCampBanners($id) {
        $db = JFactory::getDBO();
        $sql ="
            SELECT b . *
            FROM #__ad_agency_campaign_banner AS cb
            JOIN #__ad_agency_banners AS b ON cb.banner_id = b.id
            WHERE cb.campaign_id = ".intval($id);
        $db->setQuery($sql);
        $res = $db->loadObjectList();
        return $res;
    }

	function getApprSts($advid){
		$db = JFactory::getDBO();
		$query = "SHOW columns FROM #__ad_agency_campaign WHERE field='approved'";
		$db->setQuery($query);
		$result = $db->loadRow();
		$aux_approved = $result[4];

		// Get Advertiser setting
		$info = $this->getAdvInfo($advid);
		if(isset($info->apr_ads)&&($info->apr_cmp!='G')){
			$aux_approved = $info->apr_cmp;
		}
		if($aux_approved == 'N') {
			$aux_approved = 'P';
		}
		return $aux_approved;
	}

	function makeThumb($file_url, $width, $height){
		$handle = new Upload($file_url);

		list($width_orig, $height_orig) = @getimagesize($file_url);
		if(!isset($width_orig)||($width_orig == NULL)) { return false; }
		$ratio_orig = $width_orig/$height_orig;
		if($width == '') { $width = $height*$ratio_orig; }
		elseif($height == '') { $height = $width/$ratio_orig; }
		if ($width/$height > $ratio_orig) {
		   $width = $height*$ratio_orig;
		} else {
		   $height = $width/$ratio_orig;
		}

		$check['width'] = (int)$width;
		$check['height'] = (int)$height;
		if(($check['width'] == 0)&&($check['height'] == 0)){
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
					$sel_zone = $db->loadColumn();
					@$sel_zone = $sel_zone["0"];
				} else {
					$sel_zone = NULL;
				}
				
				$disabled = "";
				if($ban->media_type == "jomsocial"){
					$disabled = 'disabled="disabled"';
				}
				
				$ban->zones = "<select class='w145' name='adzones[".$ban->id."]' id='adzones_".$ban->id."' ".$disabled.">";
				$ban->zones .= "<option value=''>".JText::_("ADAG_ZONE_FOR_AD")."</option>";
				if(is_array($zones))
				foreach($zones as $zone){
					$types = NULL; $b_with_size = false;
					if(is_array($zone->adparams))
					foreach($zone->adparams as $key=>$value){
						if($key == 'affiliate') { $types[] = $key; $b_with_size = true; }
						if($key == 'textad') { $types[] = $key; }
						if($key == 'standard') { $types[] = $key; $b_with_size = true; }
						if($key == 'flash') { $types[] = $key; $b_with_size = true; }
						if($key == 'popup') { $types[] = $key; }
						if($key == 'transition') { $types[] = $key; }
						if($key == 'floating') { $types[] = $key; }
					}
					if(is_array($types) && in_array($ban->media_type,$types)) {
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
		return $bans;
	}

	function manage($key,$action,$cid){
		global $mainframe;
		$db	= JFactory::getDBO();
		
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
		$sql = "SELECT approved FROM `#__ad_agency_campaign` WHERE `id`='".intval($cid)."' AND `key`='".trim($key)."' LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadColumn();
		$res = $res["0"];
		
		if(isset($res)&&($res!=NULL)){
			if($action == "approve"){
				$sql = "UPDATE `#__ad_agency_campaign` SET `approved` = 'Y' WHERE `id` ='".intval($cid)."';";
				$db->setQuery($sql);
				if($db->query()){
					echo "<img src='".JURI::root()."components/com_adagency/images/tick.png' />".JText::_('ADAG_CAMSG');
				}

				//send email notifications
				$sql = "SELECT c.name AS cname, u.name, u.username, u.email FROM #__ad_agency_campaign AS c LEFT JOIN #__ad_agency_advertis as a ON a.aid=c.aid LEFT JOIN #__users as u ON u.id=a.user_id WHERE c.id = '".intval($cid)."' LIMIT 1";
				$db->setQuery($sql);
				if(!$result = $db->query()) {
					echo $db->stderr();
					return;
				}
				$user = $db->loadObject();

				$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
				$db->setQuery($sql);
				$configs = $db->loadObject();

				$subject=$configs->sbcmpappv;
				$message=$configs->bodycmpappv;
				
				$subject =str_replace('{name}',$user->name,$subject);
				$subject =str_replace('{login}',$user->username,$subject);
				$subject =str_replace('{email}',$user->email,$subject);
				$subject =str_replace('{campaign}',$user->cname,$subject);
				$message =str_replace('{name}',$user->name,$message);
				$message =str_replace('{login}',$user->username,$message);
				$message =str_replace('{email}',$user->email,$message);
				$message =str_replace('{campaign}',$user->cname,$message);
				
				if($params["send_camp_app"] == 1){
					JFactory::getMailer()->sendMail( $configs->fromemail, $configs->fromname, $user->email, $subject, $message, 1 );
				}
				//send email notifications

			} elseif ($action == "decline"){
				$sql = "UPDATE `#__ad_agency_campaign` SET `approved` = 'N' WHERE `id` ='".intval($cid)."';";
				$db->setQuery($sql);
				if($db->query()){
					echo "<img src='".JURI::root()."components/com_adagency/images/publish_x.png' />".JText::_('ADAG_CDMSG');
				}
				//send email notifications
				$sql = "SELECT c.name AS cname, u.name, u.username, u.email FROM #__ad_agency_campaign AS c LEFT JOIN #__ad_agency_advertis as a ON a.aid=c.aid LEFT JOIN #__users as u ON u.id=a.user_id WHERE c.id = '".intval($cid)."' LIMIT 1";
				$db->setQuery($sql);
				if(!$result = $db->query()) {
					echo $db->stderr();
					return;
				}
				$user = $db->loadObject();

				$sql = "SELECT * FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
				$db->setQuery($sql);
				$configs = $db->loadObject();

				$subject=$configs->sbcmpdis;
				$message=$configs->bodycmpdis;
				$subject =str_replace('{name}',$user->name,$subject);
				$subject =str_replace('{login}',$user->username,$subject);
				$subject =str_replace('{email}',$user->email,$subject);
				$subject =str_replace('{campaign}',$user->cname,$subject);
				$message =str_replace('{name}',$user->name,$message);
				$message =str_replace('{login}',$user->username,$message);
				$message =str_replace('{email}',$user->email,$message);
				$message =str_replace('{campaign}',$user->cname,$message);
				
				if($params["send_camp_dis"] == 1){
					JFactory::getMailer()->sendMail( $configs->fromemail, $configs->fromname, $user->email, $subject, $message, 1 );
				}
				
				//send email notifications

			} else {
				$mainframe->redirect("index.php");
			}
		} else {
			$mainframe->redirect("index.php");
		}
	}

	function delete(){
		$user = JFactory::getUser();
		$remove_action = JRequest::getVar("remove_action", "");
		
		if($remove_action == ""){
			$cids = JRequest::getVar('cid', array(0), 'post', 'array');
			$item = $this->getTable('adagencyCampaigns');
			$database = JFactory::getDBO();
			foreach($cids as $cid){
				$query = "update #__ad_agency_campaign set `status` = '-1', `activities` = concat(activities, 'Deleted - ".date("Y-m-d H:i:s")."', ' - ".intval($user->id)."', ';') where id=".intval($cid);
				$database->setQuery($query);
				$database->query();
				
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
		else{
			$db = JFactory::getDBO();
			$database = JFactory::getDBO();
			
			$config = JFactory::getConfig();
			$siteOffset = $config->get('offset');
			$jnow = JFactory::getDate('now', $siteOffset);
			
			$sql = "select `id` from #__ad_agency_campaign where (`validity` <> '0000-00-00 00:00:00') and (`validity` < '".$jnow."')";
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadAssocList();
			
			if(isset($result) && count($result) > 0){
				foreach($result as $key_camp=>$value_camp){
					$query = "update #__ad_agency_campaign set `status` = '-1', `activities` = concat(activities, 'Deleted - ".date("Y-m-d H:i:s")."', ' - ".intval($user->id)."', ';') where id=".intval($value_camp["id"]);
					$db->setQuery($query);
					$db->query();
				
					$query = "DELETE FROM #__ad_agency_campaign_banner WHERE campaign_id = '".intval($value_camp["id"])."'";
					$db->setQuery($query);
					$db->query();
					
					$sql = "select * from #__ad_agency_statistics where `impressions` like '%\"campaign_id\":\"".intval($value_camp["id"])."\",%' OR `impressions` like '%\"campaign_id\":".intval($value_camp["id"]).",%' OR `click` like '%\"campaign_id\":\"".intval($value_camp["id"])."\",%' OR `click` like '%\"campaign_id\":".intval($value_camp["id"]).",%'";
					$database->setQuery($sql);
					$database->query();
					$statistics = $database->loadAssocList();
					
					if(isset($statistics) && count($statistics) > 0){
						foreach($statistics as $key=>$value){
							$id = $value["id"];
							$impressions = @json_decode($value["impressions"], true);
							$click = @json_decode($value["click"], true);
							
							foreach($impressions as $key_imp=>$value_imp){
								if($value_imp["campaign_id"] == intval($value_camp["id"])){
									unset($impressions[$key_imp]);
								}
							}
							
							foreach($click as $key_click=>$value_click){
								if($value_click["campaign_id"] == intval($value_camp["id"])){
									unset($click[$key_click]);
								}
							}
							
							$sql = "update #__ad_agency_statistics set `impressions`='".json_encode($impressions)."', `click`='".json_encode($click)."' where `id`=".intval($id);
							$database->setQuery($sql);
							$database->query();
						}
					}
				}
			}
			return true;
		}
	}

	function publish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyCampaigns');
		if ($task == 'approve'){
			$sql = "update #__ad_agency_campaign set approved='Y' where id in ('".implode("','", $cids)."')";
		} else {
			$sql = "update #__ad_agency_campaign set approved='N' where id in ('".implode("','", $cids)."')";

		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
	}

	function getcmplistAdvertisers () {
		if (empty ($this->_package)) {
			$db = JFactory::getDBO();
			$sql = "SELECT a.aid, a.company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY a.company ASC";
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
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$cmp = intval(JRequest::getVar('cid', "0"));
		$sql = "update #__ad_agency_campaign set status='0', `activities` = concat(activities, 'Paused - ".date("Y-m-d H:i:s")." - ".intval($user->id).";') where id='".intval($cmp)."'";
		$db->setQuery($sql);
			if (!$db->query() ){
				$this->setError($db->getErrorMsg());
				return false;
			}
		return true;
	}

	function unpause() {
		$user = JFactory::getUser();
		$db = JFactory::getDBO();
		$cmp = intval(JRequest::getVar('cid', "0"));
		$sql = "update #__ad_agency_campaign set status='1', `activities` = concat(activities, 'Active - ".date("Y-m-d H:i:s")." - ".intval($user->id).";') where id='".intval($cmp)."'";
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		
		return true;
	}

	function io() {

	}
	
	function getlistCampsAds(){
		$cid = JRequest::getVar("cid", "0");
		$db = JFactory::getDBO();
		$sql = "select `banner_id` from #__ad_agency_campaign_banner where `campaign_id`=".intval($cid);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function checkInventoryPackage(){
		$db = JFactory::getDBO();
		$package_id = JRequest::getVar("otid", "-1");
		if($package_id == -1 || $package_id == 0){
			$package_id = JRequest::getVar("pid", "0");
		}
		
		if(intval($package_id) != 0){// if package selected
			$sql = "select `type` from `#__ad_agency_order_type` where `tid`=".intval($package_id);
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadColumn();
			$type = $result["0"];
			
			if(trim($type) == "in"){// if package is inventory
				$offset = JFactory::getApplication()->getCfg('offset');
				$today = JFactory::getDate('now', $offset);
				$today_date = $today->toSql(true);
				$today_date = strtotime($today_date);
				
				$sql = "select z.* from #__ad_agency_zone z, #__ad_agency_package_zone pz where pz.`package_id`=".intval($package_id)." and pz.`zone_id`=z.`zoneid` and z.`inventory_zone`=1";
				$db->setQuery($sql);
				$db->query();
				$zones = $db->loadAssocList();
				
				$exist_slots = FALSE;
				
				if(isset($zones) && count($zones) > 0){
					$all_final_dates = array();
					foreach($zones as $key=>$zone){
						$slots = intval($zone["banners"]) * intval($zone["banners_cols"]);
						
						$sql = "select count(*) from #__ad_agency_campaign where `otid`=".intval($package_id)." and `status` <> -1 and `renewcmp` = 1";
						$db->setQuery($sql);
						$db->query();
						$total = $db->loadColumn();
						$total = $total["0"];
						
						$slots = $slots - $total;
						
						if($slots <= 0){
							continue;
						}
						else{
							$exist_slots = TRUE;
						}
						
						$sql = "select `id` from #__ad_agency_campaign where `otid`=".intval($package_id)." and `status` <> -1";
						$db->setQuery($sql);
						$db->query();
						$result = $db->loadColumn();
						$count = count($result);
						
						if(isset($result) && count($result) > 0){// check if campaign expired or not and check the most recent date
							$sql = "select `validity` from #__ad_agency_campaign where id in (".implode(", ", $result).") and `renewcmp` <> 1 order by `validity` asc";
							$db->setQuery($sql);
							$db->query();
							$validity = $db->loadAssocList();
							foreach($validity as $key=>$value){
								$date = $value["validity"];
								$date_time = strtotime($date);
								if($date_time > $today_date){
									$all_final_dates[] = $date_time;
									$slots --; // slot occupied
								}
							}
						}
					}
					
					if($exist_slots == FALSE){
						$_SESSION["most_recent_available_date"] = 'NO_SLOTS_AVAILABLE';
						return false;
					}
					
					asort($all_final_dates);
					$result_slot = $slots;
					
					if($result_slot < 0){
						$result_slot = $result_slot * -1;
					}
					elseif($result_slot > 0){
						$result_slot = 0;
					}
					
					@$most_recent_available_date = $all_final_dates[$result_slot];
					
					if(intval($slots) > 0){
						//$jnow = JFactory::getDate();
						//$most_recent_available_date = strtotime($jnow->toSql(true));
						$offset = JFactory::getApplication()->getCfg('offset');
						$today = JFactory::getDate('now', $offset);
						$most_recent_available_date = $today->toSql(true);
						$most_recent_available_date = strtotime($most_recent_available_date);
					}
					elseif(!isset($most_recent_available_date)){
						//$jnow = JFactory::getDate();
						//$most_recent_available_date = strtotime($jnow->toSql(true));
						$offset = JFactory::getApplication()->getCfg('offset');
						$today = JFactory::getDate('now', $offset);
						$most_recent_available_date = $today->toSql(true);
						$most_recent_available_date = strtotime($most_recent_available_date);
					}
					
					$configs = $this->getInstance("adagencyConfig", "adagencyModel");
					$configs = $configs->getConfigs();
					$configs->params = @unserialize($configs->params);
					$ymd = '%Y-%m-%d';
					if($configs->params['timeformat'] == 0){
						$ymd = "%Y-%m-%d %h:%m:%i";
					}
					elseif($configs->params['timeformat'] == 1){
						$ymd = "%m/%d/%Y %h:%m:%i";
					}
					elseif($configs->params['timeformat'] == 2){
						$ymd = "%d-%m-%Y %h:%m:%i";
					}
					elseif($configs->params['timeformat'] == 3){
						$ymd = "%Y-%m-%d";
					}
					elseif($configs->params['timeformat'] == 4){
						$ymd = "%m/%d/%Y";
					}
					elseif($configs->params['timeformat'] == 5){
						$ymd = "%d-%m-%Y";
					}
					
					if($most_recent_available_date < $today_date){
						$most_recent_available_date = $today_date;
					}
					
					$_SESSION["most_recent_available_date"] = date(str_replace(array("%", "h"), array("", "H"), $ymd), $most_recent_available_date);
					
					return false; // no empty slots
				}
			}
			else{ // if package is not inventory
				return true;
			}
		}
		else{// if not package selected
			return true;
		}
	}

};
?>
