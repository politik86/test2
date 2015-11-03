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
require_once("components/com_adagency/helpers/helper.php");

class adagencyAdminModeladagencyReports extends JModelLegacy {
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

	function formatime2($time, $option = 1){
		if($time == "Never"){
			return "Never";
		}

		if(is_null($time)){
			$joomla_date = JFactory::getDate();
			$time = $joomla_date->toSql();
		}

		$time = str_replace("/", "-", $time);
		$date_time = explode(" ",$time);
		$tdate = explode("-", $date_time["0"]);
		$output = NULL;

		if(!isset($date_time["1"])){
			$date_time["1"] = NULL;
		}

		switch($option){
			case "0":
				$output = $tdate["0"]."-".$tdate["1"]."-".$tdate["2"];
				break;
			case "1":
				$output = $tdate["1"]."/".$tdate["2"]."/".$tdate["0"];
				break;
			case "2":
				$output = $tdate["2"]."-".$tdate["1"]."-".$tdate["0"];
				break;
			case "3":	
				$output = $tdate["0"]."-".$tdate["1"]."-".$tdate["2"];
				break;
			case "4":
				$output = $tdate["1"]."/".$tdate["2"]."/".$tdate["0"];
				break;
			case "5":
				$output = $tdate["2"]."-".$tdate["1"]."-".$tdate["0"];
				break;
			default:
				$output = $time;
				break;
		}
		return $output;
	}

	function getLanguage () {
		$db = JFactory::getDBO();
		$sql = "select * from #__adagency_languages where id=".$this->_id;
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

		$sql = "select * from #__adagency_languages where id=".$id;
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

		$text = $_POST["langfiledata"];
		$f = fopen ($path, "w");
		fwrite ($f, $text);
		fclose ($f);
		return true;
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
				$query = "select count(*) from #__adagency_languages where fefilename='".$lang_file."' or befilename='".$lang_file."'";
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
						$sql = "insert into #__adagency_languages(`name`, `fefilename`, `befilename`) values ('".$code."', '".$lang_file."', '".$lang_file."')";
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
		$table_entry = $this->getTable ("adagencyPlugin");
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
			$sql = "select name,fefilename from #__adagency_languages where id=".$cid;
			$db->setQuery($sql);
			$tmp = $db->loadObjectList();
			$file = $tmp[0]->fefilename;
			$code = $tmp[0]->name;
			$respathfe = JPATH_ROOT.DS."language".DS.$code.DS;
			$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS.$code.DS;
			$menufile = str_replace (".ini", ".menu.ini", $file);

			if ((JFile::delete($respathfe.$file)) && (JFile::delete($respathfe.$menufile))
				&& (JFile::delete($respathbe.$file)) && (JFile::delete($respathbe.$menufile))) {
				$sql = "delete from #__adagency_languages where id=".$cid;
				$db->setQuery($sql);
				$db->query();
			}
		}
		return true;
	}

	function getreportsAdvertisers () {
		if (empty ($this->_package)) {
			$db = JFactory::getDBO();
			$sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY company ASC";
			$db->setQuery($sql);
			
			if (!$db->query()) {
				echo $db->stderr();
				return;
			}
			$this->_package = $db->loadObjectList();
		}
		return $this->_package;
	}
	
	function getNumberOfActiveAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_advertis a, #__users u where a.`approved`='Y' and a.`user_id`=u.`id`";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		return $count;
	}
	
	function getNumberOfInactiveAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "select count(*) from #__ad_agency_advertis a, #__users u where a.`approved`='N' and a.`user_id`=u.`id`";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		return $count;
	}
	
	function getNumberOfActiveCampaigns(){
		$db = JFactory::getDBO();
		
		$task = JRequest::getVar("task", "");
		$where = "";
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$where = " where `aid`=".intval($adv_id);
		}
		
		$sql = "select * from #__ad_agency_campaign".$where;
		$db->setQuery($sql);
		$db->query();
		$campaigns = $db->loadAssocList();
		$count = 0;
		
		$offset = JFactory::getApplication()->getCfg('offset');
		$jnow = JFactory::getDate('now', $offset);
		$current_date = $jnow->toSql(true);
		
		if(isset($campaigns) && count($campaigns) > 0){
			for($i = 0; $i < count($campaigns); $i++){
                $camp = $campaigns[$i];
				
                $expired=0;
				if(($camp["type"]=="cpm"  || $camp["type"]=="pc") && $camp["quantity"] < 1){
					$expired=1;
				}
				
				if($camp["type"]=="fr" || $camp["type"]=="in"){
					$datan = date("Y-m-d H:i:s");
					if($datan > $camp["validity"] && $camp["validity"] != "0000-00-00 00:00:00"){
						$expired=1;
					}
				}
				
				if($expired == 1 && $camp["status"] != "-1"){
					// do nothing
				}
				elseif((strtotime($camp["start_date"]) > strtotime($current_date)) && $camp["status"] != "-1"){
					// do nothing
				}
				elseif($camp["status"] == "1"){
					$count++;
				}
				elseif($camp["status"] == "0"){
					// do nothing
				}
				elseif($camp["status"] == "-1"){
					// do nothing
				}
			}
		}
		else{
			return "0";
		}
		
		return $count;
	}
	
	function getNumberOfInactiveCampaigns(){
		$db = JFactory::getDBO();
		
		$task = JRequest::getVar("task", "");
		$where = "";
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$where = " where `aid`=".intval($adv_id);
		}
		
		$sql = "select * from #__ad_agency_campaign".$where;
		$db->setQuery($sql);
		$db->query();
		$campaigns = $db->loadAssocList();
		$count = 0;
		
		$offset = JFactory::getApplication()->getCfg('offset');
		$jnow = JFactory::getDate('now', $offset);
		$current_date = $jnow->toSql(true);
		
		if(isset($campaigns) && count($campaigns) > 0){
			for($i = 0; $i < count($campaigns); $i++){
                $camp = $campaigns[$i];
				
                $expired=0;
				if(($camp["type"]=="cpm"  || $camp["type"]=="pc") && $camp["quantity"] < 1){
					$expired=1;
				}
				
				if($camp["type"]=="fr" || $camp["type"]=="in"){
					$datan = date("Y-m-d H:i:s");
					if($datan > $camp["validity"] && $camp["validity"] != "0000-00-00 00:00:00"){
						$expired=1;
					}
				}
				
				if($expired == 1 && $camp["status"] != "-1"){
					$count++;
					continue;
				}
				elseif((strtotime($camp["start_date"]) > strtotime($current_date)) && $camp["status"] != "-1"){
					$count++;
					continue;
				}
				elseif($camp["status"] == "1"){
					// do nothing
				}
				elseif($camp["status"] == "0"){
					$count++;
					continue;
				}
				elseif($camp["status"] == "-1"){
					$count++;
					continue;
				}
			}
		}
		else{
			return "0";
		}
		
		return $count;
	}
	
	function getNumberOfActiveAds(){
		$db = JFactory::getDBO();
		
		$task = JRequest::getVar("task", "");
		$and = "";
		
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$and .= " and `advertiser_id`=".intval($adv_id);
		}
		elseif($task == "campaigns"){
			$advertisers = JRequest::getVar("advertisers", "0");
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
			
			$sql = "select count(*) from #__ad_agency_banners b, #__ad_agency_campaign_banner cb where b.`approved`='Y' and cb.`banner_id`=b.`id` and cb.`campaign_id`=".intval($camp_id);
			$db->setQuery($sql);
			$db->query();
			$count = $db->loadColumn();
			$count = @$count["0"];
			return $count;
		}
		
		$sql = "select count(*) from #__ad_agency_banners where `approved`='Y'".$and;
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		return $count;
	}
	
	function getNumberOfInactiveAds(){
		$db = JFactory::getDBO();
		
		$task = JRequest::getVar("task", "");
		$and = "";
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$and .= " and `advertiser_id`=".intval($adv_id);
		}
		elseif($task == "campaigns"){
			$advertisers = JRequest::getVar("advertisers", "0");
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
			
			$sql = "select count(*) from #__ad_agency_banners b, #__ad_agency_campaign_banner cb where b.`approved`='N' and cb.`banner_id`=b.`id` and cb.`campaign_id`=".intval($camp_id);
			$db->setQuery($sql);
			$db->query();
			$count = $db->loadColumn();
			$count = @$count["0"];
			return $count;
		}
		
		$sql = "select count(*) from #__ad_agency_banners where `approved`='N'".$and;
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		return $count;
	}
	
	function getRevenueEarnedLastMonth(){
		$start = mktime(0, 0, 0, date("m")-1, 1, date("Y"));
		$stop = mktime(0, 0, 0, date("m")-1, 31, date("Y"));
		
		$start = date("Y-m-d H:i:s", $start);
		$stop = date("Y-m-d H:i:s", $stop);
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$all_advertisers = $this->getAllAdvertisers();
		$task = JRequest::getVar("task", "");
		$adv_id = 0;
		$and = "";
		
		if($task == "advertisers"){
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$and = " and `aid`=".intval($adv_id);
		}
		elseif($task == "overview"){
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
				$and = " and `aid`=".intval($adv_id);
			}
		}
		
		$db = JFactory::getDBO();
		$sql = "select sum(`cost`) as total from #__ad_agency_order where `order_date` >= '".$start."' and `order_date` <= '".$stop."'".$and;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadColumn();
		$total = @$total["0"];
		
		return $total;
	}
	
	function getRevenueEarnedThisMonth(){
		$start = mktime(0, 0, 0, date("m"), 1, date("Y"));
		$stop = mktime(0, 0, 0, date("m"), 31, date("Y"));
		
		$start = date("Y-m-d H:i:s", $start);
		$stop = date("Y-m-d H:i:s", $stop);
		
		$advertisers = JRequest::getVar("advertisers", "0");
		$all_advertisers = $this->getAllAdvertisers();
		$task = JRequest::getVar("task", "");
		$adv_id = 0;
		$and = "";
		
		if($task == "advertisers"){
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$and = " and `aid`=".intval($adv_id);
		}
		elseif($task == "overview"){
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
				$and = " and `aid`=".intval($adv_id);
			}
		}
		
		$db = JFactory::getDBO();
		$sql = "select sum(`cost`) as total from #__ad_agency_order where `order_date` >= '".$start."' and `order_date` <= '".$stop."'".$and;
		$db->setQuery($sql);
		$db->query();
		$total = $db->loadColumn();
		$total = @$total["0"];
		
		return $total;
	}
	
	function getMostPayingAdvertiser(){
		$db = JFactory::getDBO();
		$sql = "select sum(o.`cost`) as total, a.`aid`, u.`id`, u.`name` from #__ad_agency_order o, #__users u, #__ad_agency_advertis a where u.`id`=a.`user_id` and a.`aid`=o.`aid` group by u.`id`, u.`name`, a.`aid` order by total desc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			return $result["0"];
		}
		else{
			return array();
		}
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
		
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
		}
		elseif($task == "campaigns"){
			$advertisers = JRequest::getVar("advertisers", "0");
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
		
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
		}
		elseif($task == "campaigns"){
			$advertisers = JRequest::getVar("advertisers", "0");
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
	
	function getMostSuccessfulCampaign(){
		$db = JFactory::getDBO();
		$return = array();
		
		$sql = "select `click` from #__ad_agency_statistics where `click` <> ''";
		$db->setQuery($sql);
		$db->query();
		$clicks = $db->loadAssocList();
		
		$task = JRequest::getVar("task", "");
		$and = "";
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$and .= " and o.`aid`=".intval($adv_id);
		}
		
		$sql = "select c.`id`, o.`cost` from #__ad_agency_campaign c, #__ad_agency_order o where c.`otid`=o.`tid` ".$and;
		$db->setQuery($sql);
		$db->query();
		$campaigns = $db->loadAssocList("id");
		
		$campaign_id = 0;
		
		if(isset($clicks) && count($clicks) > 0 && isset($campaigns) && count($campaigns) > 0){
			$max = 0;
			foreach($clicks as $key=>$value){
				$click = json_decode($value["click"], true);
				
				if(is_array($click) && count($click) > 0)
				foreach($click as $key_click=>$value_click){
					$campaignid = $value_click["campaign_id"];
				
					if(isset($campaigns[$campaignid])){
						$total_cost = $campaigns[$campaignid]["cost"];
						$total_clicks = $value_click["how_many"];
						
						if(intval($total_clicks) > 0){
							$cpc = $total_cost / $total_clicks;
							$total_cost = $cpc * $total_clicks;
							
							if($total_cost > $max){
								$campaign_id = $campaignid;
								$max = $total_cost;
							}
						}
					}
				}
			}
		}
		
		if(intval($campaign_id) > 0){
			$sql = "select `id`, `name` from #__ad_agency_campaign where `id`=".intval($campaign_id);
			$db->setQuery($sql);
			$db->query();
			$return = $db->loadAssocList();
		}
		
		return $return;
	}
	
	function getLeastSuccessfulCampaign(){
		$db = JFactory::getDBO();
		$return = array();
		
		$sql = "select `click` from #__ad_agency_statistics where `click` <> ''";
		$db->setQuery($sql);
		$db->query();
		$clicks = $db->loadAssocList();
		
		$task = JRequest::getVar("task", "");
		$and = "";
		if($task == "advertisers"){
			$advertisers = JRequest::getVar("advertisers", "0");
			$all_advertisers = $this->getAllAdvertisers();
			$adv_id = 0;
			
			if(intval($advertisers) != 0){
				$adv_id = intval($advertisers);
			}
			elseif(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$adv_id = $temp["aid"];
			}
			$and .= " and o.`aid`=".intval($adv_id);
		}
		
		$sql = "select c.`id`, o.`cost` from #__ad_agency_campaign c, #__ad_agency_order o where c.`otid`=o.`tid` ".$and;
		$db->setQuery($sql);
		$db->query();
		$campaigns = $db->loadAssocList("id");
		
		$campaign_id = 0;
		
		if(isset($clicks) && count($clicks) > 0 && isset($campaigns) && count($campaigns) > 0){
			$max = 0;
			foreach($clicks as $key=>$value){
				$click = json_decode($value["click"], true);
				
				if(isset($click) && count($click) > 0)
				foreach($click as $key_click=>$value_click){
					$campaignid = $value_click["campaign_id"];
				
					if(isset($campaigns[$campaignid])){
						$total_cost = $campaigns[$campaignid]["cost"];
						$total_clicks = $value_click["how_many"];
						
						if(intval($total_clicks) > 0){
							$cpc = $total_cost / $total_clicks;
							$total_cost = $cpc * $total_clicks;
							
							if($max == 0){
								$max = $total_cost;
							}
							
							if($total_cost <= $max){
								$campaign_id = $campaignid;
								$max = $total_cost;
							}
						}
					}
				}
			}
		}
		
		if(intval($campaign_id) > 0){
			$sql = "select `id`, `name` from #__ad_agency_campaign where `id`=".intval($campaign_id);
			$db->setQuery($sql);
			$db->query();
			$return = $db->loadAssocList();
		}
		
		return $return;
	}
	
	function getCurrency(){
		$db = JFactory::getDBO();
		$sql = "select `currencydef` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$currencydef = $db->loadColumn();
		$currencydef = @$currencydef["0"];

		return $currencydef;
	}
	
	function getAllAdvertisers(){
		$db = JFactory::getDBO();
		
		$sql = "select a.`aid`, a.`user_id`, u.`name` from #__ad_agency_advertis a, #__users u where a.`user_id`=u.`id`";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('aid');
		return $result;
	}
	
	function getAllCampaigns(){
		$db = JFactory::getDBO();
		
		$sql = "select c.`id`, c.`name`, c.`cost` as campaign_cost, o.`cost` as order_cost from #__ad_agency_campaign c left outer join #__ad_agency_order o on c.`otid`=o.`tid`";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('id');
		return $result;
	}
	
	function getAllAdsType(){
		$db = JFactory::getDBO();
		$sql = "select `id`, `title`, `media_type` from #__ad_agency_banners";
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
			$adv_id = $temp["aid"];
		}
		
		$campaigns = JRequest::getVar("campaigns", "0");
		$all_campaigns = $this->getAllCampaignsByAdvTo();
		$camp_id = 0;
		
		if(intval($campaigns) != 0){
			$sql = "select `aid` from #__ad_agency_campaign where `id`=".intval($campaigns);
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
		
		$sql = "select b.`id`, b.`title` from #__ad_agency_banners b, #__ad_agency_campaign c, #__ad_agency_campaign_banner cb where b.`id`=cb.`banner_id` and cb.`campaign_id`=c.`id` and b.`advertiser_id`=".intval($adv_id)." and c.`id`=".intval($camp_id);
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
			$adv_id = $temp["aid"];
		}
		
		$sql = "select `id`, `name` from #__ad_agency_campaign where `aid`=".intval($adv_id);
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList('id');
		return $result;
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
	
	function getTableContent(){
		$db = JFactory::getDBO();
		$return = array();
		
		$date_range = JRequest::getVar("date_range", "this_week");
		$advertisers = JRequest::getVar("advertisers", "0");
		$campaigns = JRequest::getVar("campaigns", "0");
		
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
		
		$sql = "select `entry_date`, `impressions`, `click` from #__ad_agency_statistics where `entry_date` >= '".$start_date."' and `entry_date` <= '".$stop_date."' order by `entry_date` asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$impressions = json_decode($value["impressions"], true);
				$click = json_decode($value["click"], true);
				
				if(isset($impressions) && count($impressions) > 0){
					foreach($impressions as $imp_key=>$imp_value){
						if($advertisers != 0 && $imp_value["advertiser_id"] != $advertisers){
							continue;
						}
						
						if(intval($imp_value["advertiser_id"]) <= 0){
							continue;
						}
						
						if($campaigns != 0 && $imp_value["campaign_id"] != $campaigns){
							continue;
						}
						
						if(intval($imp_value["campaign_id"]) <= 0){
							continue;
						}
						
						if(isset($return[$imp_value["advertiser_id"]."-".$imp_value["campaign_id"]]["impressions"])){
							$return[$imp_value["advertiser_id"]."-".$imp_value["campaign_id"]]["impressions"] += $imp_value["how_many"];
						}
						else{
							$return[$imp_value["advertiser_id"]."-".$imp_value["campaign_id"]]["impressions"] = $imp_value["how_many"];
						}
					}
				}
				
				if(isset($click) && count($click) > 0){
					foreach($click as $click_key=>$click_value){
						if($advertisers != 0 && $click_value["advertiser_id"] != $advertisers){
							continue;
						}
						
						if($campaigns != 0 && $click_value["campaign_id"] != $campaigns){
							continue;
						}
						
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
		
		$this->_total = count($return);
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		
		if($limitstart <= count($return) && intval($limit) > 0){
			$return = array_slice($return, $limitstart, $limit);
		}

		return $return;
	}
	
	function overviewCSV(){
		$data = "";
		$header = array("Advertiser", "Campaign", "Impressions", "Clicks", "Click Ratio");
		$content = $this->getTableContent();
		
		$all_advertisers = $this->getAllAdvertisers();
		$all_campaigns = $this->getAllCampaigns();
		
		$data .= implode(",", $header);
		$data .= "\n";
		
		$cid = JRequest::getVar("cid", array(), "post", "array");
		
		if(isset($content) && count($content) > 0){
			foreach($content as $key=>$value){
				$tmp_key = explode("-", $key);
				$adv_id = $tmp_key["0"];
				$cmp_id = $tmp_key["1"];
				$impression = $value["impressions"];
				$click = @$value["click"];
				
				if(isset($all_advertisers[$adv_id]["name"]) && isset($all_campaigns[$cmp_id]["name"])){
					if(is_array($cid) && count($cid) > 0 && !in_array($adv_id."-".$cmp_id, $cid)){
						continue;
					}
					
					$data .= $all_advertisers[$adv_id]["name"].",";
					$data .= $all_campaigns[$cmp_id]["name"].",";
					$data .= $impression.",";
					$data .= $click.",";
					
					$nr = 0;
					if(intval($impression) != 0){
						$nr = $click / $impression * 100;
					}
					$data .= number_format($nr, 3, '.', '')."%"."\n";
				}
			}
		}
		
		$csv_filename = "overview.csv";
		$size_in_bytes = strlen($data);
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);
		exit();
	}
	
	function overviewPDF(){
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'tcpdf_include.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	
		$data = "";
		$header = array("Advertiser", "Campaign", "Impressions", "Clicks", "Click Ratio");
		$content = $this->getTableContent();
		$all_advertisers = $this->getAllAdvertisers();
		$all_campaigns = $this->getAllCampaigns();
		
		$file_content  = "<table border=\"1\" cellpadding=\"5\" cellpadding=\"5\">\n";
		$file_content .= "<tr>\n";
		foreach($header as $key=>$value){
			$file_content .= "<th>".$value."</th>";
		}
		$file_content .= "</tr>\n";
		
		$cid = JRequest::getVar("cid", array(), "post", "array");
		
		if(isset($content) && count($content) > 0){
			foreach($content as $key=>$value){
				$tmp_key = explode("-", $key);
				$adv_id = $tmp_key["0"];
				$cmp_id = $tmp_key["1"];
				$impression = $value["impressions"];
				$click = $value["click"];
				
				if(isset($all_advertisers[$adv_id]["name"]) && isset($all_campaigns[$cmp_id]["name"])){
					if(is_array($cid) && count($cid) > 0 && !in_array($adv_id."-".$cmp_id, $cid)){
						continue;
					}
					
					$file_content .= "<tr>\n";
					
					$file_content .= "<td>".$all_advertisers[$adv_id]["name"]."</td>";
					$file_content .= "<td>".$all_campaigns[$cmp_id]["name"]."</td>";
					$file_content .= "<td>".$impression."</td>";
					$file_content .= "<td>".$click."</td>";
					$nr = 0;
					if(intval($impression) != 0){
						$nr = $click / $impression * 100;
					}
					$file_content .= "<td>".number_format($nr, 3, '.', '')."%</td>";
					
					$file_content .= "</tr>\n";
				}
			}
		}
		
		$file_content .= "</table>";
		ob_end_clean();
		$pdf_filename = "overview.pdf";
		$pdf->AddPage();
		$pdf->writeHTMLCell(0, 0, '', '', $file_content, 0, 1, 0, true, '', true);
		$pdf->Output($pdf_filename, 'FD');
		
		exit();
	}
	
	function getTableAdvContent(){
		$db = JFactory::getDBO();
		$return = array();
		
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
		$ad_type = JRequest::getVar("ad_type", "0");
		
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
		
		$sql = "select `entry_date`, `impressions`, `click` from #__ad_agency_statistics where `entry_date` >= '".$start_date."' and `entry_date` <= '".$stop_date."' order by `entry_date` asc";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		$all_ads = $this->getAllAdsType();
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$impressions = json_decode($value["impressions"], true);
				$click = json_decode($value["click"], true);
				
				if(isset($impressions) && count($impressions) > 0){
					foreach($impressions as $imp_key=>$imp_value){
						if($advertisers != 0 && $imp_value["advertiser_id"] != $advertisers){
							continue;
						}
						
						if($campaigns != 0 && $imp_value["campaign_id"] != $campaigns){
							continue;
						}
						
						if($imp_value["campaign_id"] == 0){
							continue;
						}
						
						if(trim($ad_type) != "0" && $all_ads[$imp_value["banner_id"]]["media_type"] != $ad_type){
							continue;
						}
						
						if(isset($return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["impressions"])){
							$return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["impressions"] += $imp_value["how_many"];
						}
						else{
							$return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["impressions"] = $imp_value["how_many"];
						}
						
						$return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["date"] = $value["entry_date"];
						$return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["type"] = $all_ads[$imp_value["banner_id"]]["media_type"];
						$return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["ad_name"] = $all_ads[$imp_value["banner_id"]]["title"];
						$return[$imp_value["campaign_id"]."-".$imp_value["banner_id"]]["ad_id"] = $imp_value["banner_id"];
					}
				}
				
				if(isset($click) && count($click) > 0){
					foreach($click as $click_key=>$click_value){
						if($advertisers != 0 && $click_value["advertiser_id"] != $advertisers){
							continue;
						}
						
						if($campaigns != 0 && $click_value["campaign_id"] != $campaigns){
							continue;
						}
						
						if($click_value["campaign_id"] == 0){
							continue;
						}
						
						if(trim($ad_type) != "0" && $all_ads[$click_value["banner_id"]]["media_type"] != $ad_type){
							continue;
						}
						
						if(isset($return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["click"])){
							$return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["click"] += $click_value["how_many"];
						}
						else{
							$return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["click"] = $click_value["how_many"];
						}
						
						$return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["date"] = $value["entry_date"];
						$return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["type"] = $all_ads[$click_value["banner_id"]]["media_type"];
						$return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["ad_name"] = $all_ads[$click_value["banner_id"]]["title"];
						$return[$click_value["campaign_id"]."-".$click_value["banner_id"]]["ad_id"] = $click_value["banner_id"];
					}
				}
			}
		}
		
		$this->_total = count($return);
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');
		
		if($limitstart <= count($return) && intval($limit) > 0){
			$return = array_slice($return, $limitstart, $limit);
		}
		
		return $return;
	}
	
	function advertisersCSV(){
		$data = "";
		$header = array("Date", "Campaign", "Ad type", "Ad name", "Impressions", "Clicks", "Click Ratio");
		$content = $this->getTableAdvContent();
		$all_advertisers = $this->getAllAdvertisers();
		$all_campaigns = $this->getAllCampaigns();
		
		$data .= implode(",", $header);
		$data .= "\n";
		
		$cid = JRequest::getVar("cid", array(), "post", "array");
		
		if(isset($content) && count($content) > 0){
			foreach($content as $key=>$value){
				$tmp_key = explode("-", $key);
				$camp_id = $tmp_key["0"];
				$ad_id = $tmp_key["1"];
				$impression = $value["impressions"];
				$click = @$value["click"];
				$type = $value["type"];
				$ad_name = $value["ad_name"];
				$date = $value["date"];
				
				if(is_array($cid) && count($cid) > 0 && !in_array($camp_id."-".$ad_id, $cid)){
					continue;
				}
				
				$data .= $date.",";
				$data .= $all_campaigns[$camp_id]["name"].",";
				$data .= $type.",";
				$data .= $ad_name.",";
				$data .= $impression.",";
				$data .= $click.",";
				
				$nr = 0;
				if(intval($impression) != 0){
					$nr = $click / $impression * 100;
				}
				$data .= number_format($nr, 2, '.', '')."%"."\n";
			}
		}
		
		$csv_filename = "advertisers.csv";
		$size_in_bytes = strlen($data);
		header("Content-Type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=".$csv_filename);
		header("Pragma: no-cache");
		header("Expires: 0");
		echo utf8_decode($data);
		exit();
	}
	
	function advertisersPDF(){
		require_once(JPATH_SITE.DS.'administrator'.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'tcpdf_include.php');
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		
		
		$header = array("Date", "Campaign", "Ad type", "Ad name", "Impressions", "Clicks", "Click Ratio");
		$content = $this->getTableAdvContent();
		$all_advertisers = $this->getAllAdvertisers();
		$all_campaigns = $this->getAllCampaigns();
		
		$file_content  = "<table border=\"1\" cellpadding=\"5\" cellpadding=\"5\">\n";
		$file_content .= "<tr>\n";
		foreach($header as $key=>$value){
			$file_content .= "<th>".$value."</th>";
		}
		$file_content .= "</tr>\n";
		
		$cid = JRequest::getVar("cid", array(), "post", "array");
		
		if(isset($content) && count($content) > 0){
			foreach($content as $key=>$value){
				$tmp_key = explode("-", $key);
				$camp_id = $tmp_key["0"];
				$ad_id = $tmp_key["1"];
				$impression = $value["impressions"];
				$click = $value["click"];
				$type = $value["type"];
				$ad_name = $value["ad_name"];
				
				if(is_array($cid) && count($cid) > 0 && !in_array($camp_id."-".$ad_id, $cid)){
					continue;
				}
				
				$file_content .= "<tr>\n";
				
				$file_content .= "<td>".$value["date"]."</td>";
				$file_content .= "<td>".$all_campaigns[$camp_id]["name"]."</td>";
				$file_content .= "<td>".$type."</td>";
				$file_content .= "<td>".$ad_name."</td>";
				$file_content .= "<td>".$impression."</td>";
				$file_content .= "<td>".$click."</td>";
				
				$nr = 0;
				if(intval($impression) != 0){
					$nr = $click / $impression * 100;
				}
				$file_content .= "<td>".number_format($nr, 2, '.', '')."%</td>";
				
				$file_content .= "</tr>\n";
			}
		}
		
		$file_content .= "</table>";
		ob_end_clean();
		$pdf_filename = "advertisers.pdf";
		$pdf->AddPage();
		$pdf->writeHTMLCell(0, 0, '', '', $file_content, 0, 1, 0, true, '', true);
		$pdf->Output($pdf_filename, 'FD');
		
		exit();
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
		
		$all_campaigns = $this->getAllCampaigns();
		
		$aid = JRequest::getVar("advertisers", "0");
		
		if(intval($aid) == 0){
			$all_advertisers = $this->getAllAdvertisers();
			if(isset($all_advertisers) && count($all_advertisers) > 0){
				$temp = @array_shift(array_slice($all_advertisers, 0, 1));
				$aid = intval($temp["aid"]);
			}
		}
		
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$impressions = json_decode($value["impressions"], true);
				$click = json_decode($value["click"], true);
				
				if(isset($impressions) && count($impressions) > 0){
					if(!isset($impressions["0"])){
						$impressions = array("0"=>$impressions);
					}
				
					foreach($impressions as $imp_key=>$imp_value){
						if($imp_value["advertiser_id"] == $aid && intval($imp_value["campaign_id"]) > 0){
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
					if(!isset($click["0"])){
						$click = array("0"=>$click);
					}
					
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
				
				$cost = $all_campaigns[$campaign]["order_cost"];
				if(trim($cost) == ""){
					$cost = $all_campaigns[$campaign]["campaign_cost"];
				}
				
				$for_return[$i]["advertiser"] = $all_advertisers[$advertiser]["name"];
				$for_return[$i]["campaign"] = $all_campaigns[$campaign]["name"];
				$for_return[$i]["advertiser_id"] = $advertiser;
				$for_return[$i]["campaign_id"] = $campaign;
				$for_return[$i]["cost"] = $cost;
				$for_return[$i]["impressions"] = $impressions;
				$for_return[$i]["click"] = $click;
				
				if(intval($click) != 0 && intval($impressions) != 0){
					$nr = intval($click) / intval($impressions) * 100;
					$for_return[$i]["click_ratio"] = number_format($nr, 2, '.', '')."%";
				}
				else{
					$for_return[$i]["click_ratio"] = "0.00"."%";
				}
				
				$configs = $this->getConfigs();
				$params_conf = unserialize($configs["0"]["params"]);
				$currency_poz = 0;
				if(isset($params_conf['currency_price'])){
					$currency_poz = $params_conf['currency_price'];
				}
				$currency = $configs["0"]["currencydef"];
				
				if(intval($click) != 0){
					$cpc = $cost / intval($click);
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
					$cpi = $cost / intval($impressions);
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
	
	function getConfigs(){
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		return $result;
	}
	
};

?>