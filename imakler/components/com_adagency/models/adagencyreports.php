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

class adagencyModeladagencyReports extends JModelLegacy {
	var $_total = 0;
	var $_pagination = null;

	function __construct () {
		parent::__construct();
		$cids = JRequest::getVar('cid', 0, '', 'array');
		$this->setId((int)$cids[0]);
		global $mainframe, $option;
		
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		if(JRequest::getVar("limitstart") == JRequest::getVar("old_limit")){
			JRequest::setVar("limitstart", "0");		
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');
		}

		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if(empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;
		$this->_installpath = JPATH_COMPONENT.DS."plugins".DS;
		$this->_plugin = null;
	}

	function getlistLanguages () {
		if (empty ($this->_plugins)) {
			$sql = "select * from #__adagency_languages";
			$this->_languages = $this->_getList($sql);			
		}
		return $this->_languages;
	}	

	function getLanguage () {
		$db = JFactory::getDBO();
		$sql = "select * from #__adagency_languages where id=".intval($this->_id);
		$db->setQuery($sql);
		$lang = $db->loadObjectList();
		$lang = $lang[0];
		$file = $lang->fefilename;
		$code = $lang->name;

		$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$file;
		$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS.$file;
		$task = JRequest::getVar("task", "", "request");
		if ($task == "editFE") $lang->path = $respathfe;
		else $lang->path = $respathbe;
		$lang->data = implode ("", file ($lang->path));
		$lang->type = $task;
		return $lang;
	}
	
	function store () {
		$db = JFactory::getDBO();
		$id = JRequest::getVar("id", "", "request");
		if (!$id) return false;
		$sql = "select * from #__adagency_languages where id=".intval($id);
		$db->setQuery($sql);
		$lang = $db->loadObjectList();
		$lang = $lang[0];
		$file = $lang->fefilename;
		$code = $lang->name;
		$respathfe = JPATH_ROOT.DS."language".DS.$code.DS.$file;
		$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS.$file;
		$type = JRequest::getVar("type", "", "request");
		if ($type == "editFE") $path = $respathfe;
		else $path = $respathbe;
		$text = JRequest::getVar("langfiledata", "", "post");
		$f = fopen ($path, "w");
		fwrite ($f, $text);
		fclose ($f);
		return true;
	}
	
	function rotator() {
		$database = JFactory::getDBO();
		
		$banner_id = JRequest::getInt('banner_id');
		$advertiser_id = JRequest::getInt('advertiser_id');
		$campaign_id = JRequest::getInt('campaign_id');
		$type = JRequest::getVar('type');

		$sql = "SELECT limit_ip FROM #__ad_agency_settings LIMIT 1";
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
		
		// start limit impression count for a banner per ip ----------------------
		$sql = "select `ips_impressions` from #__ad_agency_ips where `entry_date`='".$time_interval."'";
		$database->setQuery($sql);
		$database->query();
		$all_ips = $database->loadColumn();
		$how_many = 0;
		
		if(is_array($all_ips) && count($all_ips) > 0){
			$all_ips = json_decode($all_ips["0"], true);
			$update = FALSE;
			
			foreach($all_ips as $key=>$value){
				if($value["ip"] == $the_ipp && $value["banner_id"] == intval($banner_id)){
					if($value["how_many"] < $limit_ip){
						$update = TRUE;
						$all_ips[$key]["how_many"] += 1;
						$how_many = $all_ips[$key]["how_many"];
						break;
					}
					else{
						// max limit impressions per IP for one ad per day
						return ;
					}
				}
			}
			
			if(!$update){
				$new_ip_added = array("ip"=>$the_ipp, "banner_id"=>intval($banner_id), "how_many"=>"1");
				$all_ips[] = $new_ip_added;
			}
			
			$sql = "update #__ad_agency_ips set `ips_impressions`='".json_encode($all_ips)."' where `entry_date`='".$time_interval."'";
			$database->setQuery($sql);
			$database->query();
		}
		else{
			$temp_ips1 = array("ip"=>$the_ipp, "banner_id"=>intval($banner_id), "how_many"=>"1");
			$temp_ips2 = array("ip"=>'0000000000', "banner_id"=>"0", "how_many"=>"0");
			$temp_ips = array("0"=>$temp_ips1, "1"=>$temp_ips2);
			
			$sql = "insert into #__ad_agency_ips (`entry_date`, `ips_impressions`) values ('".$time_interval."', '".json_encode($temp_ips)."')";
			$database->setQuery($sql);
			$database->query();
		}
		// stop limit impression count for a banner per ip -----------------------
		
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
						
						if(!isset($impressions["0"])){
							$impressions = array("0"=>$impressions);
						}
						
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

		if ('cpm'==$type) {
			$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".intval($campaign_id);
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
			$quantity = $database->loadResult();
			$quantity -- ;
			
			if($quantity < 0){
				$quantity = 0;
			}
			
			if($how_many <= $limit_ip) {
				$sql = "UPDATE #__ad_agency_campaign SET quantity = '".$quantity."' WHERE quantity > 0 AND id=".intval($campaign_id);
				$database->setQuery($sql);
				if (!$database->query()) {
					echo $database->stderr();
					return;
				}
			}

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
	
	function copyLangFile ($path, $type, $code, $file) {
		$respath = "";
		if ($type == "fe") {
			$respath = JPATH_ROOT.DS."language".DS.$code.DS;
		} elseif ($type == "be") {
			$respath = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
		}
		if (!file_exists($respath) ) return -2;
		if (file_exists($respath.$file)) return -1;
		JFile::copy($path, $respath.$file, '');	
		return 1;
	}

	function installLanguage($path, $language_file = '') {
		$db = JFactory::getDBO();
		$language_file = trim ($language_file);
		if (strlen($language_file) < 1) return JText::_('MODLANGNOUPLOAD');
		$ext = substr ($language_file, strrpos($language_file, ".") + 1);
		if ($ext != 'zip') return JText::_('MODLANGNOZIP');
		jimport('joomla.filesystem.archive');	
		if (!JArchive::extract($path.$language_file, $path)) {
			return JText::_('MODLANGERREXTRACT');
		}
		$dir = opendir ($path);
		if (!file_exists($path."install")) return JText::_("MODLANGMISSINSTALL");
		$install = parse_ini_file($path."install");
		if (count ($install) < 1) return JText::_("MODLANGCORRUPTEDINSTALL");
		$lang_code = explode (" ", $install['langcode']);
	    foreach ($lang_code as $code ) {
			$be = 0;
			$fe = 0;

			$fe_path = $path."fe".DS.$code.DS.$code.".com_adagency.ini";
			$lang_file = $code.".com_adagency.ini";
			$be_path = $path."be".DS.$code.DS.$code.".com_adagency.ini";
			if (!file_exists($fe_path)) $fe = 0; else $fe = 1;
			if (!file_exists($be_path)) $be = 0; else $be = 1;
			if ($be && $fe ) {
				$query = "select count(*) from #__adagency_languages where fefilename='".trim($lang_file)."' or befilename='".trim($lang_file)."'";
  				$db->setQuery($query);
		   		$isthere = $db->loadResult();
				if ($isthere) {
					return JText::_('MODLANGALLREDYEXIST');// 
				} else {
				        $fe = 0;
					$be = 0;
					$fe = $this->copyLangFile($fe_path, "fe", $code, $lang_file);
					if ($fe) {
						$be = $this->copyLangFile($be_path, "be", $code, $lang_file);		
					}
					if (!$fe || !$be) {
						return JText::_("MODLANGCOPYERR");
					} else if ($fe == -1 || $be == -1) {
						return JText::_("MODLANGCANTCOPY");
					} else if ($fe < 0 || $be < 0) {
						return JText::_("MODLANGFOLDERNOTEXITST");
					} else {
						$sql = "insert into #__adagency_languages(`name`, `fefilename`, `befilename`) values ('".trim($code)."', '".trim($lang_file)."', '".trim($lang_file)."')";
		  				$db->setQuery($sql);
				   		$db->query();
					}
				} 
			} else {
				return JText::_("MODLANGMISSLANGFILE");
			}
		}
		$install_path = $this->_installpath;
      		JFile::copy ($path.$install['filename'], $install_path.$install['filename']);      
       	return JText::_("MODLANGSUCCESSFUL");
	}

	function upload() {
		$table_entry =& $this->getTable ("adagencyPlugin");
		jimport('joomla.filesystem.file');
		$file = JRequest::getVar('langfile', array(), 'files');	
		$install_path = JPATH_ROOT.DS."tmp".DS."adagencylanguage".DS;
		Jfolder::create ($install_path);
		if (JFile::copy($file['tmp_name'], $install_path.$file['name'], '')) {
			$res = $this->installLanguage($install_path, $file['name']);
			JFolder::delete ($install_path);
		} else {
			$res = JText::_('MODLANGCOPYERR');
		}
		
		return $res;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		jimport('joomla.filesystem.file');
		$db = JFactory::getDBO();

		foreach ($cids as $cid) {
			$sql = "select name,fefilename from #__adagency_languages where id=".intval($cid);
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			$file = $tmp[0]->fefilename;
			$code = $tmp[0]->name;
			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS;
			$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
			$menufile = str_replace (".ini", ".menu.ini", $file);
			if ((JFile::delete($respathfe.$file)) && (JFile::delete($respathfe.$menufile))
				&& (JFile::delete($respathbe.$file)) && (JFile::delete($respathbe.$menufile))) {
	
				$sql = "delete from #__adagency_languages where id=".intval($cid);
				$db->setQuery($sql);
				$db->query();
			}
		}
		return true;
	}
	
	function getreportsAdvertisers () {
		if(empty ($this->_package)){
			$db = JFactory::getDBO();
			$sql = "SELECT a.aid, a.company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY a.company ASC";
			$db->setQuery($sql);
			if(!$db->query()) {
				echo $db->stderr();
				return;
			}
			$this->_package = $db->loadObjectList();
		}
		return $this->_package;
	}
	
	function getNumberOfActiveAds(){
		$db = JFactory::getDBO();
		
		$campaigns_stats = JRequest::getVar("campaigns", "0");
		$all_campaigns = $this->getAllCampaignsByAdvTo();		
		$camp_id = 0;
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$sql = "select `aid` from #__ad_agency_advertis where `user_id`=".intval($advertisers);
		$db->setQuery($sql);
		$db->query();
		$advertisers = $db->loadColumn();
		$advertisers = @$advertisers["0"];
		
		if(intval($campaigns_stats) != 0){
			$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns_stats);
			$db->setQuery($sql);
			$db->query();
			$aid = $db->loadColumn();
			$aid = @$aid["0"];
			
			if(intval($aid) == intval($advertisers)){
				$camp_id = $campaigns_stats;
			}
			else{
				$temp = @array_shift(array_slice($all_campaigns, 0, 1));
				$camp_id = $temp["id"];
			}
		}
		else{
			$temp = @array_shift(array_slice($all_campaigns, 0, 1));
			$camp_id = $temp["id"];
		}
		
		$sql = "select count(*) from #__ad_agency_banners b, #__ad_agency_campaign_banner cb where b.`approved`='Y' and cb.`banner_id`=b.`id` and cb.`campaign_id`=".intval($camp_id);
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		return $count;
	}
	
	function getNumberOfInactiveAds(){
		$db = JFactory::getDBO();
		
		$campaigns_stats = JRequest::getVar("campaigns", "0");
		$all_campaigns = $this->getAllCampaignsByAdvTo();		
		$camp_id = 0;
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$sql = "select `aid` from #__ad_agency_advertis where `user_id`=".intval($advertisers);
		$db->setQuery($sql);
		$db->query();
		$advertisers = $db->loadColumn();
		$advertisers = @$advertisers["0"];
		
		if(intval($campaigns_stats) != 0){
			$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns_stats);
			$db->setQuery($sql);
			$db->query();
			$aid = $db->loadColumn();
			$aid = @$aid["0"];
			
			if(intval($aid) == intval($advertisers)){
				$camp_id = $campaigns_stats;
			}
			else{
				$temp = @array_shift(array_slice($all_campaigns, 0, 1));
				$camp_id = $temp["id"];
			}
		}
		else{
			$temp = @array_shift(array_slice($all_campaigns, 0, 1));
			$camp_id = $temp["id"];
		}
		
		$sql = "select count(*) from #__ad_agency_banners b, #__ad_agency_campaign_banner cb where b.`approved`='N' and cb.`banner_id`=b.`id` and cb.`campaign_id`=".intval($camp_id);
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		return $count;
	}
	
	function getHighestClickRatioAd(){
		$db = JFactory::getDBO();
		$return = array();
		
		$sql = "select `click` from #__ad_agency_statistics where `click` <> ''";
		$db->setQuery($sql);
		$db->query();
		$clicks = $db->loadAssocList();
		
		$task = JRequest::getVar("task", "");
		$adv_id = JRequest::getVar("advertisers", "0");
		$camp_id = "";
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$sql = "select `aid` from #__ad_agency_advertis where `user_id`=".intval($advertisers);
		$db->setQuery($sql);
		$db->query();
		$advertisers = $db->loadColumn();
		$advertisers = @$advertisers["0"];
		
		$campaigns_stats = JRequest::getVar("campaigns", "0");
		
		$all_campaigns = $this->getAllCampaignsByAdvTo();
		$camp_id = 0;
		
		if(intval($advertisers) == 0){
			$temp = @array_shift(array_slice($all_campaigns, 0, 1));
			$camp_id = $temp["id"];
		}
		else{
			if(intval($campaigns_stats) != 0){
				$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns_stats);
				$db->setQuery($sql);
				$db->query();
				$aid = $db->loadColumn();
				$aid = @$aid["0"];
				
				if(intval($aid) == intval($advertisers)){
					$campaigns_stats = $campaigns_stats;
				}
				else{
					$temp = @array_shift(array_slice($all_campaigns, 0, 1));
					$campaigns_stats = $temp["id"];
				}
				
				$adv_id = $aid;
				
				$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns_stats);
				$db->setQuery($sql);
				$db->query();
				$aid = $db->loadColumn();
				$aid = @$aid["0"];
				if(intval($aid) == intval($advertisers)){
					$camp_id = $campaigns_stats;
				}
				else{
					$temp = @array_shift(array_slice($all_campaigns, 0, 1));
					$camp_id = $temp["id"];
				}
			}
			else{
				$temp = @array_shift(array_slice($all_campaigns, 0, 1));
				$camp_id = $temp["id"];
			}
		}
		
		if(isset($clicks) && count($clicks) > 0){
			$banner_id = 0;
			$max = 0;
			foreach($clicks as $key=>$value){
				$value = json_decode($value["click"], true);
				
				if(isset($value) && count($value) > 0){
					foreach($value as $key_click=>$value_click){
						if(intval($adv_id) != 0 && $value_click["advertiser_id"] != intval($adv_id)){
							continue;
						}
						
						if(intval($camp_id) != 0 && $value_click["campaign_id"] != intval($camp_id)){
							continue;
						}
						
						if($value_click["how_many"] > $max){
							$max = $value_click["how_many"];
							$banner_id = $value_click["banner_id"];
						}
					}
				}
			}
			
			if(intval($banner_id) != 0){
				$sql = "select `id`, `title`, `media_type` from #__ad_agency_banners where `id`=".intval($banner_id);
				$db->setQuery($sql);
				$db->query();
				$return = $db->loadAssocList();
			}
		}
		return $return;
	}
	
	function getLowestClickRatioAd(){
		$db = JFactory::getDBO();
		$return = array();
		
		$sql = "select `click` from #__ad_agency_statistics where `click` <> ''";
		$db->setQuery($sql);
		$db->query();
		$clicks = $db->loadAssocList();
		
		$task = JRequest::getVar("task", "");
		$adv_id = JRequest::getVar("advertisers", "0");
		$camp_id = "";
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$sql = "select `aid` from #__ad_agency_advertis where `user_id`=".intval($advertisers);
		$db->setQuery($sql);
		$db->query();
		$advertisers = $db->loadColumn();
		$advertisers = @$advertisers["0"];
		
		$campaigns_stats = JRequest::getVar("campaigns", "0");
		
		$all_campaigns = $this->getAllCampaignsByAdvTo();
		$camp_id = 0;
		
		if(intval($advertisers) == 0){
			$temp = @array_shift(array_slice($all_campaigns, 0, 1));
			$camp_id = $temp["id"];
		}
		else{
			if(intval($campaigns_stats) != 0){
				$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns_stats);
				$db->setQuery($sql);
				$db->query();
				$aid = $db->loadColumn();
				$aid = @$aid["0"];
				
				if(intval($aid) == intval($advertisers)){
					$campaigns_stats = $campaigns_stats;
				}
				else{
					$temp = @array_shift(array_slice($all_campaigns, 0, 1));
					$campaigns_stats = $temp["id"];
				}
				
				$adv_id = $aid;
				
				$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns_stats);
				$db->setQuery($sql);
				$db->query();
				$aid = $db->loadColumn();
				$aid = @$aid["0"];
				if(intval($aid) == intval($advertisers)){
					$camp_id = $campaigns_stats;
				}
				else{
					$temp = @array_shift(array_slice($all_campaigns, 0, 1));
					$camp_id = $temp["id"];
				}
			}
			else{
				$temp = @array_shift(array_slice($all_campaigns, 0, 1));
				$camp_id = $temp["id"];
			}
		}
		
		if(isset($clicks) && count($clicks) > 0){
			$banner_id = 0;
			$max = 0;
			foreach($clicks as $key=>$value){
				$value = json_decode($value["click"], true);
				
				if(isset($value) && count($value) > 0){
					foreach($value as $key_click=>$value_click){
						if(intval($adv_id) != 0 && $value_click["advertiser_id"] != intval($adv_id)){
							continue;
						}
						
						if(intval($camp_id) != 0 && $value_click["campaign_id"] != intval($camp_id)){
							continue;
						}
						
						if($max == 0){
							$max = $value_click["how_many"];
							$banner_id = $value_click["banner_id"];
						}
						
						if($value_click["how_many"] < $max){
							$max = $value_click["how_many"];
							$banner_id = $value_click["banner_id"];
						}
					}
				}
			}
			
			if(intval($banner_id) != 0){
				$sql = "select `id`, `title`, `media_type` from #__ad_agency_banners where `id`=".intval($banner_id);
				$db->setQuery($sql);
				$db->query();
				$return = $db->loadAssocList();
			}
		}
		return $return;
	}
	
	function getAllAdvertisers(){
		$db = JFactory::getDBO();
		
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$sql = "select a.`aid`, a.`user_id`, u.`name` from #__ad_agency_advertis a, #__users u where a.`user_id`=u.`id` and u.`id`=".intval($user_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('aid');
		return $result;
	}
	
	function getAllCampaigns(){
		$db = JFactory::getDBO();
		
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$sql = "select c.`id`, c.`name`, o.`cost` from #__ad_agency_campaign c, #__ad_agency_order o, #__ad_agency_advertis a where c.`otid`=o.`tid` and a.`aid`=o.`aid` and a.`aid`=c.`aid` and a.`user_id`=".intval($user_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('id');
		return $result;
	}
	
	function getAllCampaignsByAdv(){
		$db = JFactory::getDBO();
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$all_advertisers = $this->getAllAdvertisers();
		$adv_id = 0;
		
		if(intval($advertisers) != 0){
			$adv_id = intval($advertisers);
		}
		elseif(isset($all_advertisers) && count($all_advertisers) > 0){
			$temp = @array_shift(array_slice($all_advertisers, 0, 1));
			$adv_id = $temp["user_id"];
		}
		
		$sql = "select c.`id`, c.`name` from #__ad_agency_campaign c, #__ad_agency_advertis a where a.`aid`=c.`aid` and a.`user_id`=".intval($adv_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('id');
		return $result;
	}
	
	function getAllAds(){
		$db = JFactory::getDBO();
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$all_advertisers = $this->getAllAdvertisers();
		$adv_id = 0;
		
		if(intval($advertisers) != 0){
			$adv_id = intval($advertisers);
		}
		elseif(isset($all_advertisers) && count($all_advertisers) > 0){
			$temp = @array_shift(array_slice($all_advertisers, 0, 1));
			$adv_id = $temp["user_id"];
		}
		
		$campaigns = JRequest::getVar("campaigns", "0");
		$all_campaigns = $this->getAllCampaignsByAdvTo();
		$camp_id = 0;
		
		if(intval($campaigns) != 0){
			$sql = "select a.`user_id` from #__ad_agency_campaign c, #__ad_agency_advertis a where a.`aid`=c.`aid` and c.`id`=".intval($campaigns);
			$db->setQuery($sql);
			$db->query();
			$aid = $db->loadColumn();
			$aid = @$aid["0"];
			
			if(intval($aid) == intval($advertisers)){
				$camp_id = intval($campaigns);
			}
			else{
				$temp = @array_shift(array_slice($all_campaigns, 0, 1));
				$camp_id = $temp["id"];
			}
		}
		elseif(isset($all_campaigns) && count($all_campaigns) > 0){
			$temp = @array_shift(array_slice($all_campaigns, 0, 1));
			$camp_id = $temp["id"];
		}
		
		$sql = "select b.`id`, b.`title` from #__ad_agency_banners b, #__ad_agency_campaign c, #__ad_agency_campaign_banner cb, #__ad_agency_advertis a where b.`id`=cb.`banner_id` and cb.`campaign_id`=c.`id` and b.`advertiser_id`=a.`aid` and c.`aid`=a.`aid` and a.`user_id`=".intval($adv_id)." and c.`id`=".intval($camp_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('id');
		
		return $result;
	}
	
	function getTableCmpContent(){
		$db = JFactory::getDBO();
		$return = array();
		$for_return = array();
		
		$date_range = JRequest::getVar("date_range", "this_week");
		$advertisers = JRequest::getVar("advertisers", "0");
		$all_advertisers = $this->getAllAdvertisers();
		
		if(intval($advertisers) == 0){
			if(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$advertisers = $temp["aid"];
			}
		}
		
		$campaigns = JRequest::getVar("campaigns", "0");
		$ad_name = JRequest::getVar("ad_name", "0");
		
		$start_date = "";
		$stop_date = "";
		
		if($date_range == "this_week"){
			$start_date = date("Y-m-d", strtotime('monday this week'));
			$stop_date = date("Y-m-d", strtotime("sunday this week"));
		}
		elseif($date_range == "last_week"){
			$start_date = date("Y-m-d", strtotime('monday last week'));
			$stop_date = date("Y-m-d", strtotime("sunday last week"));
		}
		elseif($date_range == "last_month"){
			$start_date = date('Y-m-d', strtotime('first day of last month'));
			$stop_date = date('Y-m-d', strtotime('last day of last month'));
		}
		elseif($date_range == "this_month"){
			$start_date = date('Y-m-d', strtotime('first day of this month'));
			$stop_date = date('Y-m-d', strtotime('last day of this month'));
		}
		
		$start_date_request = JRequest::getVar("start_date", "");
		$quick_range = JRequest::getVar("quick-range", "");
		if($start_date_request != "" && $quick_range == ""){
			$start_date = $start_date_request;
		}
		
		$stop_date_request = JRequest::getVar("stop_date", "");
		$quick_range = JRequest::getVar("quick-range", "");
		if($stop_date_request != "" && $quick_range == ""){
			$stop_date = $stop_date_request;
		}
		
		$sql = "select `entry_date`, `impressions`, `click` from #__ad_agency_statistics order by `entry_date` asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		$all_campaigns = $this->getAllCampaignsByAdv(); //$this->getAllCampaigns();
		$aid = JRequest::getVar("advertisers", "0");
		
		if(intval($aid) == 0){
			$all_advertisers = $this->getAllAdvertisers();
			if(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$aid = intval($temp["aid"]);
			}
		}
		else{
			$sql = "select `aid` from #__ad_agency_advertis where `user_id`=".intval($aid);
			$db->setQuery($sql);
			$db->query();
			$aid = $db->loadColumn();
			$aid = @$aid["0"];
		}
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$impressions = json_decode($value["impressions"], true);
				$click = json_decode($value["click"], true);
				
				if(isset($impressions) && count($impressions) > 0){
					foreach($impressions as $imp_key=>$imp_value){
						if(@$imp_value["advertiser_id"] == $aid && intval(@$imp_value["campaign_id"]) > 0){
							if(isset($return[$imp_value["advertiser_id"]."-".$imp_value["campaign_id"]]["impressions"])){
								$return[$imp_value["advertiser_id"]."-".$imp_value["campaign_id"]]["impressions"] += $imp_value["how_many"];
							}
							else{
								$return[$imp_value["advertiser_id"]."-".$imp_value["campaign_id"]]["impressions"] = $imp_value["how_many"];
							}
						}
					}
				}
				
				if(isset($click) && count($click) > 0){
					foreach($click as $click_key=>$click_value){
						if($click_value["advertiser_id"] == $aid && intval($click_value["campaign_id"]) > 0){
							if(isset($return[$click_value["advertiser_id"]."-".$click_value["campaign_id"]]["click"])){
								$return[$click_value["advertiser_id"]."-".$click_value["campaign_id"]]["click"] += $click_value["how_many"];
							}
							else{
								$return[$click_value["advertiser_id"]."-".$click_value["campaign_id"]]["click"] = $click_value["how_many"];
							}
						}
					}
				}
			}
		}
		
		$for_return = array();
		
		if(isset($return) && count($return) > 0){
			$i = 0;
			
			foreach($return as $key=>$value){
				$impressions = 0;
				$click = 0;
				
				$temp = explode("-", $key);
				$advertiser = $temp["0"];
				$campaign = $temp["1"];
				
				if(!isset($all_campaigns[$campaign])){
					continue;
				}
				
				if(isset($value["impressions"])){
					$impressions = intval($value["impressions"]);
				}
				
				if(isset($value["click"])){
					$click = intval($value["click"]);
				}
				
				$for_return[$i]["advertiser"] = $all_advertisers[$advertiser]["name"];
				$for_return[$i]["campaign"] = $all_campaigns[$campaign]["name"];
				$for_return[$i]["advertiser_id"] = $advertiser;
				$for_return[$i]["campaign_id"] = $campaign;
				@$for_return[$i]["cost"] = $all_campaigns[$campaign]["cost"];
				$for_return[$i]["impressions"] = $impressions;
				$for_return[$i]["click"] = $click;
				
				if(intval($click) != 0 && intval($impressions) != 0){
					$nr = intval($click) / intval($impressions);
					$for_return[$i]["click_ratio"] = number_format($nr, 2, '.', '');
				}
				else{
					$for_return[$i]["click_ratio"] = "0.00";
				}
				
				$configs = $this->getConfigs();
				$params_conf = unserialize($configs["0"]["params"]);
				$currency_poz = 0;
				if(isset($params_conf['currency_price'])){
					$currency_poz = $params_conf['currency_price'];
				}
				$currency = $configs["0"]["currencydef"];
				
				if(intval($click) != 0){
					@$cpc = $all_campaigns[$campaign]["cost"] / intval($click);
					$cpc = number_format($cpc, 2, '.', '');
					
					if($currency_poz == 0){ // Before Price
						$for_return[$i]["cpc"] = JText::_("ADAG_C_".$currency)." ".$cpc;
					}
					else{ // After Price 
						$for_return[$i]["cpc"] = $cpc." ".JText::_("ADAG_C_".$currency);
					}
				}
				else{
					if($currency_poz == 0){ // Before Price
						$for_return[$i]["cpc"] = JText::_("ADAG_C_".$currency)." 0.00";
					}
					else{ // After Price 
						$for_return[$i]["cpc"] = "0.00 ".JText::_("ADAG_C_".$currency);
					}
				}
				
				
				
				if(intval($impressions) != 0){
					@$cpi = $all_campaigns[$campaign]["cost"] / intval($impressions);
					$cpi = number_format($cpi, 2, '.', '');
					
					if($currency_poz == 0){ // Before Price
						$for_return[$i]["cpi"] = JText::_("ADAG_C_".$currency)." ".$cpi;
					}
					else{ // After Price 
						$for_return[$i]["cpi"] = $cpi." ".JText::_("ADAG_C_".$currency);
					}
				}
				else{
					if($currency_poz == 0){ // Before Price
						$for_return[$i]["cpi"] = JText::_("ADAG_C_".$currency)." 0.00";
					}
					else{ // After Price 
						$for_return[$i]["cpi"] = "0.00 ".JText::_("ADAG_C_".$currency);
					}
				}
				
				$i++;
			}
		}
		
		$this->_total = count($for_return);
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		
		if($limitstart <= count($for_return) && intval($limit) > 0){
			$for_return = array_slice($for_return, $limitstart, $limit);
		}
		
		return $for_return;
	}
	
	function getMinReportDate(){
		$db = JFactory::getDBO();
		$sql = "select min(`entry_date`) from #__ad_agency_statistics";
		$db->setQuery($sql);
		$db->query();
		$min = $db->loadColumn();
		$min = @$min["0"];
		return $min;
	}
	
	function getMaxReportDate(){
		return date("Y-m-d");
	}
	
	function getAllCampaignsByAdvTo(){
		$db = JFactory::getDBO();
		$advertisers = JRequest::getVar("advertisers", "0");
		$and = "";
		
		if(intval($advertisers) != 0){
			$and .= " and `aid`=".intval($advertisers);
		}
		else{
			$task = JRequest::getVar("task", "");
			if($task != "overview"){
				$all_advertisers = $this->getAllAdvertisers();
				
				if(isset($all_advertisers) && count($all_advertisers) > 0){
					$temp = @array_shift(array_slice($all_advertisers, 0, 1));
					$and .= " and `aid`=".intval($temp["aid"]);
				}
			}
		}
		
		$sql = "select `id`, `name` from #__ad_agency_campaign where 1=1 ".$and;
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('id');
		return $result;
	}
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		return $result;
	}
	
	function campaignsCSV(){
		$data = "";
		$header = array("Advertiser", "Campaign", "Campaign cost", "Impressions", "Clicks", "Click Ratio", "CPC", "CPI");
		$content = $this->getTableCmpContent();
		
		$data .= implode(",", $header);
		$data .= "\n";
		
		$cid = JRequest::getVar("cid", array(), "post", "array");
		
		if(isset($content) && count($content) > 0){
			foreach($content as $key=>$value){
				if(is_array($cid) && count($cid) > 0 && !in_array($value["campaign_id"], $cid)){
					continue;
				}
			
				$data .= $value["advertiser"].",";
				$data .= $value["campaign"].",";
				$data .= $value["cost"].",";
				$data .= $value["impressions"].",";
				$data .= $value["click"].",";
				$data .= $value["click_ratio"].",";
				$data .= $value["cpc"].",";
				$data .= $value["cpi"]."\n";
			}
		}
		
		$csv_filename = "campaigns.csv";
		$size_in_bytes = strlen($data);
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);
		exit();
	}
	
	function campaignsPDF(){
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'tcpdf_include.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		$header = array("Advertiser", "Campaign", "Campaign cost", "Impressions", "Clicks", "Click Ratio", "CPC", "CPI");
		$content = $this->getTableCmpContent();
		
		$file_content  = "<table border=\"1\" cellpadding=\"5\" cellpadding=\"5\">\n";
		$file_content .= "<tr>\n";
		foreach($header as $key=>$value){
			$file_content .= "<th>".$value."</th>";
		}
		$file_content .= "</tr>\n";
		
		$cid = JRequest::getVar("cid", array(), "post", "array");
		
		if(isset($content) && count($content) > 0){
			foreach($content as $key=>$value){
				if(is_array($cid) && count($cid) > 0 && !in_array($value["campaign_id"], $cid)){
					continue;
				}
				
				$file_content .= "<tr>\n";
				
				$file_content .= "<td>".$value["advertiser"]."</td>";
				$file_content .= "<td>".$value["campaign"]."</td>";
				$file_content .= "<td>".$value["cost"]."</td>";
				$file_content .= "<td>".$value["impressions"]."</td>";
				$file_content .= "<td>".$value["click"]."</td>";
				$file_content .= "<td>".$value["click_ratio"]."</td>";
				$file_content .= "<td>".$value["cpc"]."</td>";
				$file_content .= "<td>".$value["cpi"]."</td>";
				
				$file_content .= "</tr>\n";
			}
		}
		
		$file_content .= "</table>";
		ob_end_clean();
		$pdf_filename = "campaigns.pdf";
		$pdf->AddPage();
		$pdf->writeHTMLCell(0, 0, '', '', $file_content, 0, 1, 0, true, '', true);
		$pdf->Output($pdf_filename, 'FD');
		
		exit();
	}
	
};
?>