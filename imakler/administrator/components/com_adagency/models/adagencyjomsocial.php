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

class adagencyAdminModeladagencyJomsocial extends JModelLegacy {
	var $_licenses;
	var $_license;
	var $_id = null;
	var $_total = 0;
	var $_pagination = null;

	function __construct(){
		parent::__construct();
	}

	function getAdDetails(){
		$cid = JRequest::getVar("cid", array(), "get", "array");
		$id = @$cid["0"];
		
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_banners where `id`=".intval($id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		if(intval($id) == 0){
			if(isset($_SESSION["title"]) && trim($_SESSION["title"]) != ""){
				$result["0"]["title"] = trim($_SESSION["title"]);
			}
			
			if(isset($_SESSION["advertiser_id"]) && trim($_SESSION["advertiser_id"]) != ""){
				$result["0"]["advertiser_id"] = trim($_SESSION["advertiser_id"]);
			}
			
			if(isset($_SESSION["approved"]) && trim($_SESSION["approved"]) != ""){
				$result["0"]["approved"] = trim($_SESSION["approved"]);
			}
			
			if(isset($_SESSION["target_url"]) && trim($_SESSION["target_url"]) != ""){
				$result["0"]["target_url"] = trim($_SESSION["target_url"]);
			}
			
			if(isset($_SESSION["image_content"]) && trim($_SESSION["image_content"]) != ""){
				$result["0"]["image_content"] = trim($_SESSION["image_content"]);
			}
			
			if(isset($_SESSION["ad_headline"]) && trim($_SESSION["ad_headline"]) != ""){
				$result["0"]["ad_headline"] = trim($_SESSION["ad_headline"]);
			}
			
			if(isset($_SESSION["ad_text"]) && trim($_SESSION["ad_text"]) != ""){
				$result["0"]["ad_text"] = trim($_SESSION["ad_text"]);
			}
			
			if(isset($_SESSION["ad_start_date"]) && trim($_SESSION["ad_start_date"]) != ""){
				$result["0"]["ad_start_date"] = trim($_SESSION["ad_start_date"]);
			}
			
			if(isset($_SESSION["ad_end_date"]) && trim($_SESSION["ad_end_date"]) != ""){
				$result["0"]["ad_end_date"] = trim($_SESSION["ad_end_date"]);
			}
			
			if(isset($_SESSION["image_url"]) && trim($_SESSION["image_url"]) != ""){
				$result["0"]["image_url"] = trim($_SESSION["image_url"]);
			}
			
			if(isset($_SESSION["image_content"]) && trim($_SESSION["image_content"]) != ""){
				$result["0"]["image_content"] = trim($_SESSION["image_content"]);
			}
		}
		
		return $result;
	}
	
	function getstandardlistAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY company ASC";
		$db->setQuery($sql);
		if (!$db->query()) {
			echo $db->stderr();
			return;
		}
		$this->_package = $db->loadObjectList();
		
		return $this->_package;
	}
	
	function getJomSocialSettings(){
		$db = JFactory::getDBO();
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = unserialize(@$params["0"]);
		return $params;
	}
	
	function getCampsByAid($adv_id, $distinct = 0) {
		$db = JFactory::getDBO();
		$sqls = "
			SELECT c.id, c.name, c.params 
			FROM #__ad_agency_campaign AS c
			JOIN #__ad_agency_order_type AS o
			ON c.otid = o.tid 
			WHERE c.aid = ".intval($adv_id)." AND c.status != -1
		";
        if($distinct == 1){
			$sqls .= "GROUP BY c.id";
		}
		
		$db->setQuery($sqls);
		$camps = $db->loadObjectList();
		
        if(is_array($camps)){
			foreach($camps as &$element) {
				$element->adparams = @unserialize($element->adparams);
				$element->params = @unserialize($element->params);
				/*// Select total banners for each campaign
				$sql = "SELECT COUNT( campaign_id )
						FROM `#__ad_agency_campaign_banner`
						WHERE `campaign_id` = ".$element->id;
				$db->setQuery($sql);
				$element->totalbanners = $db->loadResult();*/
			}
		}
		return $camps;
	}
	
	function getUIDbyAID($aid){
		$db = JFactory::getDBO();
		$sql = 'SELECT user_id FROM `#__ad_agency_advertis` WHERE aid = "'.intval($aid).'" LIMIT 1';
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	function delete_geo($bid, $aid) {
		$db = JFactory::getDBO();
		$temp = NULL;
		if(isset($bid)&&isset($aid)) {
			$sql0 = 'SELECT id FROM #__ad_agency_channels WHERE banner_id = "'.intval($bid).'" AND advertiser_id = "'.intval($aid).'" LIMIT 1';
			$db->setQuery($sql0);
			$id_to_del = $db->loadResult();

			if(isset($id_to_del)&&($id_to_del != NULL)){
				$sql2 = 'DELETE FROM #__ad_agency_channel_set WHERE channel_id = "'.intval($id_to_del).'"';
				$db->setQuery($sql2);
				if(!$db->query()) { return false; }
			}
		}

		if(isset($id_to_del)){
			return $id_to_del;
		}
		else{
			return -1;
		}
	}
	
	function store_geo($bid = NULL){
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'channel_fcs.php');
	}
	
	function last_ad($type){
		$db = JFactory::getDBO();
		$sql = "select id from #__ad_agency_banners WHERE media_type='".$type."' GROUP BY id DESC LIMIT 1";
		$db->setQuery($sql);
		return $db->loadResult();
	}
	
	function getAdById($id){
		$db =  JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_banners WHERE id =".intval($id);
		$db->setQuery($sql);
		$result = $db->loadObject();
		return $result;
	}
	
	function getChannel($bid) {
		$db = JFactory::getDBO();
		if(isset($bid)) {
			$sql = "SELECT * FROM `#__ad_agency_channels` WHERE banner_id =".intval($bid)." LIMIT 1";
			$db->setQuery($sql);
			$result = $db->loadObject();
			if(!isset($result->id)) { return NULL; }
			$sql = "SELECT s.type, s.option, s.logical, s.data FROM `#__ad_agency_channels` AS c, `#__ad_agency_channel_set` AS s WHERE c.id = ".intval($result->id)." AND c.id = s.channel_id ORDER BY s.id ASC";
			$db->setQuery($sql);
			$result->sets = $db->loadObjectList();
		} else {
			$result = NULL;
		}
		return $result;
	}
	
	function store(){
       JLoader::import('adagencyads', JPATH_SITE.DS.'components'.DS.'com_adagency'.DS.'models');
		$front_ads_model = JModelLegacy::getInstance('AdagencyAds', 'AdagencyModel');
		       
		$item = $this->getTable('adagencyAds');
		$database = JFactory::getDBO();
		$data = JRequest::get('post');

		$sendmail = $data['sendmail'];
		$data['key'] = '';
		$isNew = false;
		
		if($data['id']==0){
			$data['created'] = date("Y-m-d");
			$isNew = true;
		}
		else{
			$current_banner = $this->getAdById($data['id']);
		}
		
		// Prepare the keywords, trim them
		if(isset($data['keywords'])&&($data['keywords']!='')){
			$data['keywords'] = implode(',', array_map('trim', explode(',',$data['keywords'])));
		}

		$data['parameters'] = @serialize($data['parameters']);
		
		if(trim($data['ad_start_date']) != ""){
			$data['ad_start_date'] = date("Y-m-d H:i:s", strtotime($data['ad_start_date']));
		}
		else{
			$data['ad_start_date'] = date("Y-m-d H:i:s");
		}
		
		if(trim($data['ad_end_date']) != "Never" && trim($data['ad_end_date']) != ""){
			$data['ad_end_date'] = date("Y-m-d H:i:s", strtotime($data['ad_end_date']));
		}
		
		if(intval($data["id"]) == 0){
			$sql = "select max(`ordering`) from #__ad_agency_banners";
			$database->setQuery($sql);
			$database->query();
			$max_ordering = $database->loadColumn();
			$max_ordering = $max_ordering["0"];
			$data["ordering"] = $max_ordering + 1;
		}
		
		$target_url = $data["target_url"];
		if(strpos(" ".$target_url, "http") === FALSE){
			$data["target_url"] = "http://".$data["target_url"];
		}
		
		if(!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;
		}
		
		if(!$item->check()){
			$this->setError($item->getErrorMsg());
			return false;
		}
		
		if(!$item->store()){
			$this->setError($item->getErrorMsg());
			return false;
		}
		
		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
		$configs = $configs->getConfigs();
		$cids = intval($data['id']);
		$db =  JFactory::getDBO();

		if(!isset($data['id']) || $data['id']==0){
			$data['id'] = mysql_insert_id();
		}
		
		$idi = $data['id'];
		
		if($idi==0){
			$ask = "SELECT `id` FROM `#__ad_agency_banners` ORDER BY `id` DESC LIMIT 1 ";
			$db->setQuery($ask);
			$idi = $db->loadResult();
		}

		if(isset($idi)){
			$this->store_geo($idi);
			require_once(JPATH_BASE . '/components/com_adagency/helpers/jomsocial.php');
			$helper = new JomSocialTargeting();
			$helper->save($idi);			
		}

		$sql = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id = '".intval($idi)."'";
		$db->setQuery($sql);
		$db->query();

        $campLimInfo = $front_ads_model->getCampLimInfo($data['advertiser_id']);
		
		if(isset($data['adv_cmp'])){
			foreach($data['adv_cmp'] as $val){
                if(($val) && (
                    ( !isset($campLimInfo[$val])) || ( $campLimInfo[$val]['adslim'] > $campLimInfo[$val]['occurences'] )
                ) ) {
					if(strpos(" ".$val, '|no|') === FALSE){
						$query = "INSERT INTO `#__ad_agency_campaign_banner` (`campaign_id`, `banner_id`, `relative_weighting`) VALUES ('".intval($val)."', '".intval($idi)."', '100');";
						$db->setQuery($query);
						$db->query();
					}
				}
			}
		}

		if(($sendmail)&&(isset($current_banner)&&($current_banner->approved!=$data['approved'])&&($data['approved']!='P'))) {
			$this->sendmail($current_banner->id,$data['approved']);
		}

		return true;
	}
	
	function sendmail ($cid,$task) {
		$db = JFactory::getDBO();
		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");
		$configs = $configs->getConfigs();
		$item = $this->getTable('adagencyAds');

		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);

		//send email notifications
		$sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id = '".intval($cid)."' GROUP BY b.id";
		$db->setQuery($sql);
		if(!$result = $db->query()) {
			echo $db->stderr();
			return;
		}
		$user = $db->loadObjectList();
		foreach ( $user as $value) {
			$ok_send_email = 1;
			if($task == 'Y') {
				$subject=$configs->sbadappv;
				$message=$configs->bodyadappv;
				$ok_send_email = $params["send_ban_app"];
			}
			else{
				$subject=$configs->sbaddisap;
				$message=$configs->bodyaddisap;
				$ok_send_email = $params["send_ban_dis"];
			}

			$subject =str_replace('{name}',$value->name,$subject);
			$subject =str_replace('{login}',$value->username,$subject);
			$subject =str_replace('{email}',$value->email,$subject);
			$subject =str_replace('{banner}',$value->title,$subject);
			$message =str_replace('{name}',$value->name,$message);
			$message =str_replace('{login}',$value->username,$message);
			$message =str_replace('{email}',$value->email,$message);
			$message =str_replace('{banner}',$value->title,$message);
			// mail publish campaign				
			if($ok_send_email == 1){
				JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $value->email, $subject, $message, 1);
			}
		}
		//send email notifications
		return 1;
	}
};
?>
