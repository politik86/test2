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

class adagencyModeladagencyAdvertisement extends JModelLegacy {
	
	function getJomsocialAds(){
		$db = JFactory::getDBO();
		$sql = "select c.`id` as campaign_id, c.`aid` as advertiser_id, c.`name` as campaign_name, c.`type` as campaign_type, c.`quantity` as campaign_quantity, c.`validity` as campaign_validity, c.`otid` as package_id, c.`approved` as campaign_approved, c.`status` as campaign_status, b.`id` as banner_id, b.`title` as banner_name, b.`image_url` as banner_avatar, b.`target_url` as url_to_promote, b.`image_content` as banner_image_content, b.`ad_headline` as banner_headline, b.`ad_text` as banner_text, b.`channel_id` as channel_id
				from #__ad_agency_campaign c, #__ad_agency_order_type ot, #__ad_agency_campaign_banner cb, #__ad_agency_banners b
				where c.`otid`=ot.`tid` and
					  c.`start_date` <= now() and
					  c.`approved`='Y' and
					  c.`status`='1' and 
					  c.`id`=cb.`campaign_id` and
					  cb.`banner_id`=b.`id` and
					  b.`media_type`='Jomsocial' and 
					  b.`approved`='Y'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		$jomsocial_ads_settings = $this->getJomsocialAdSettings();
		
		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$db->query();
		$globalSettings = $db->loadObject();
		
		if(isset($result) && count($result) > 0){
			$temp = array();
			$ad_ids = array();
			foreach($result as $key=>$value){
				if($jomsocial_ads_settings["js_ad_location"] == "geolocation"){
					// check geo location
					require_once(JPATH_SITE."/components/com_adagency/helpers/stats_count.php");
					require_once(JPATH_SITE."/administrator/components/com_adagency/helpers/jomsocial.php");
					$jomSocial = new JomSocialTargeting();
					
					$sql = "SELECT `channel_id` AS id, `type`, `logical`, `option`, `data` FROM #__ad_agency_channel_set WHERE channel_id=".intval($value["channel_id"])." ORDER BY id ASC";
					$db->setQuery($sql);
					$loaded_channels = $db->loadObjectList();
					
					// start check add visibility ---------------------------------------
					$logged_user = JFactory::getUser();
					if(intval($logged_user->id) > 0){
						if($jomSocial->exists()){
							if(!$jomSocial->visible($value["banner_id"])){
								continue;
							}
						}
					}
					// stop check add visibility ----------------------------------------
					
					// start check add geo location -------------------------------------
					if(($value["channel_id"] != NULL) && (intval($value["channel_id"]) >0)){
						if(!geo(loadChannelById(intval($value["channel_id"]), $loaded_channels), $globalSettings->cityloc)){
							continue;
						}
					}
					// stop check add geo location -------------------------------------
				}
				
				$value["ad_new_ad_language"] = JText::_("ADAG_CREATE_AN_AD");
				
				if($value["campaign_type"] == "cpm" || $value["campaign_type"] == "pc"){
					if($value["campaign_quantity"] > 0){
						if(!in_array($value["banner_id"], $ad_ids)){
							if(trim($value["banner_avatar"]) != ""){
								$value["banner_avatar"] = JURI::root()."images/stories/ad_agency/".intval($value["advertiser_id"])."/".$value["banner_avatar"];
							}
							else{
								$value["banner_avatar"] = JURI::root()."components/com_adagency/images/default_js_ad_avatar.png";
							}
							
							if(trim($value["banner_image_content"]) != ""){
								$value["banner_image_content"] = JURI::root()."images/stories/ad_agency/".intval($value["advertiser_id"])."/".$value["banner_image_content"];
							}
							
							$value["on_click_url"] = JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=click&cid=".intval($value["campaign_id"])."&bid=".intval($value["banner_id"])."&aid=".intval($value["advertiser_id"]);
							
							$url_to_promote = $value["url_to_promote"];
							$url_parsed = parse_url($url_to_promote);
							$value["short_url_to_promote"] = $url_parsed["host"];
							
							$temp[] = $value;
							$ad_ids[] = $value["banner_id"];
						}
					}
				}
				elseif($value["campaign_type"] == "fr" || $value["campaign_type"] == "in"){
					if(strtotime($value["campaign_validity"]) >= strtotime(date("Y-m-d")." 23:59:59")){
						if(!in_array($value["banner_id"], $ad_ids)){
							if(trim($value["banner_avatar"]) != ""){
								$value["banner_avatar"] = JURI::root()."images/stories/ad_agency/".intval($value["advertiser_id"])."/".$value["banner_avatar"];
							}
							else{
								$value["banner_avatar"] = JURI::root()."components/com_adagency/images/default_js_ad_avatar.png";
							}
							
							if(trim($value["banner_image_content"]) != ""){
								$value["banner_image_content"] = JURI::root()."images/stories/ad_agency/".intval($value["advertiser_id"])."/".$value["banner_image_content"];
							}
							
							$value["on_click_url"] = JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=click&cid=".intval($value["campaign_id"])."&bid=".intval($value["banner_id"])."&aid=".intval($value["advertiser_id"]);
							
							$url_to_promote = $value["url_to_promote"];
							$url_parsed = parse_url($url_to_promote);
							$value["short_url_to_promote"] = $url_parsed["host"];
							
							$temp[] = $value;
							$ad_ids[] = $value["banner_id"];
						}
					}
				}
			}
			$result = $temp;
		}
		
		return $result;
	}

	function getJomsocialAdSettings(){
		$db = JFactory::getDBO();
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadAssocList();
		$params = $params["0"]["params"];
		$params = unserialize($params);
		
		$itemid = JRequest::getVar("Itemid");
		$create_ad_link = JRoute::_("index.php?option=com_adagency&controller=adagencyJomsocial&task=edit&cid[0]=0&Itemid=".intval($itemid));
		
		$return = array();
		
		$return["js_ad_location"] = $params["js_ad_location"];
    	$return["target_audience_preview"] = $params["target_audience_preview"];
		$return["headline_limit"] = $params["headline_limit"];
		$return["content_limit"] = $params["content_limit"];
		$return["show_sponsored_stream_info"] = $params["show_sponsored_stream_info"];
		$return["show_create_ad_link"] = $params["show_create_ad_link"];
		$return["create_ad_link"] = $create_ad_link;
		$return["display_stream_ads"] = $params["display_stream_ads"];
		$return["display_stream_ads_every_value"] = $params["display_stream_ads_every_value"];
		$return["display_stream_ads_after_value"] = $params["display_stream_ads_after_value"];
		$return["js_stream_ads_on"] = $params["js_stream_ads_on"];
		
		return $return;
	}
	
	function increaseImpressions($advertiser_id, $campaign_id, $banner_id, $type){
		$database = JFactory::getDBO();
		$sql = "SELECT `limit_ip` FROM #__ad_agency_settings LIMIT 1";
		$database->setQuery($sql);
		$limit_ip = $database->loadResult();

		//reduce table size
		$time_interval = date("Y-m-d");

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

		$the_ipp = ip2long($ip);
		
		
		$sql = "select * from #__ad_agency_statistics where `entry_date`='".$time_interval."'";
		$database->setQuery($sql);
		$database->query();
		$result = $database->loadAssocList();
		
		$aid = $advertiser_id;
		$bid = $banner_id;
		$cid = $campaign_id;
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				if(isset($value["impressions"]) && trim($value["impressions"]) != ""){
					$impressions = json_decode($value["impressions"], true);
					if(isset($impressions) && count($impressions) > 0){
						$find = false;
						foreach($impressions as $key_impressions=>$impressions_value){
							if($impressions_value["advertiser_id"] == intval($aid) && $impressions_value["campaign_id"] == intval($cid) && $impressions_value["banner_id"] == intval($bid)){
								$find = true;
								$impressions[$key_impressions]["how_many"] ++;
							}
						}
						if(!$find){
							$temp2 = array("advertiser_id"=>intval($aid), "campaign_id"=>intval($cid), "banner_id"=>intval($bid), "how_many"=>"1");
							$impressions[] = $temp2;
						}
					}
					$result[$key]["impressions"] = json_encode($impressions);
				}
				else{
					$temp1 = array();
					$temp2 = array("advertiser_id"=>intval($aid), "campaign_id"=>intval($cid), "banner_id"=>intval($bid), "how_many"=>"1");
					$temp1[] = $temp2;
					$result[$key]["impressions"] = json_encode($temp1);
				}
			}
			
			$sql = "update #__ad_agency_statistics set `impressions`='".$result[$key]["impressions"]."' where `id`=".intval($result[$key]["id"]);
			$database->setQuery($sql);
			$database->query();
		}
		else{
			$temp1 = array();
			$temp2 = array("advertiser_id"=>intval($aid), "campaign_id"=>intval($cid), "banner_id"=>intval($bid), "how_many"=>"1");
			$temp1[] = $temp2;
			$impressions = json_encode($temp1);
			
			$sql = "insert into #__ad_agency_statistics (`entry_date`, `impressions`, `click`) values ('".$time_interval."', '".$impressions."', '')";
			$database->setQuery($sql);
			$database->query();
		}

		if('cpm'==$type){
			if($how_many <= $limit_ip) {
				$sql = "UPDATE #__ad_agency_campaign SET quantity = quantity-1 WHERE quantity > 0 AND id=".intval($campaign_id);
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
			}

			$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".intval($campaign_id);
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
			$quantity = $database->loadResult();
			
			if (($quantity == 0)&&($type!='fr')) {
				$nowdatetime = date("Y-m-d H:i:s");
				$sql = "UPDATE #__ad_agency_campaign SET validity = '".trim($nowdatetime)."' WHERE id=".intval($campaign_id);
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
			}			
		}
	}
};

function iJoomlaGetRealIpAddr(){
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
?>
