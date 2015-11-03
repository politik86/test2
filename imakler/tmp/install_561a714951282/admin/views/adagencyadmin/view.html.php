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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyAdmin extends JViewLegacy {

function display ($tpl =  null ) {
	JToolBarHelper::title(JText::_('ADAGENCY_CONTROL_PANEL'), 'generic.png');
	
	$installer = JRequest::getVar("installer", "");
	if($installer == 1){
		$this->installAllModules();
	}
	
	$migrate = JRequest::getVar("migrate", "");
	if($migrate == 1){
		$this->migrateStatistics();
		return true;
	}
	
	$database = JFactory :: getDBO();	
	$get_data = JRequest::get('get');
	$query = "SELECT * FROM #__ad_agency_zone where zoneid<>'0'";
	$database->setQuery( $query );
	$pols = $database->loadObjectList();
	if ($database->getErrorNum()) {
		echo $database->stderr();
		return false;
	}
	foreach ($pols as $pol) {	
		$query = "SELECT * FROM #__modules WHERE `id`=$pol->zoneid ";
			$database->setQuery( $query );
			$modules = $database->loadObjectList();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
			
		if (empty($modules))   {
			$query = "INSERT INTO #__modules (`id`,`title`,`ordering`,`position`,`published`,`module`,`showtitle`,`params`) values ('{$pol->zoneid}','".addslashes(trim($pol->z_title))."','{$pol->z_ordering}', '{$pol->z_position}', '1', 'mod_ijoomla_adagency_zone','{$pol->show_title}','{$pol->suffix}')";
			$database->setQuery( $query );
			$database->query();
			if ($database->getErrorNum()) {
				echo $database->stderr();
				return false;
			}
				$xid = @mysql_insert_id();
			if($xid!=0){
				//update the old zoneid from banner table
				$query = "UPDATE #__ad_agency_banners SET `zone`='{$xid}' WHERE `zone`=$pol->zoneid";
				$database->setQuery( $query );
				$database->query();
				if ($database->getErrorNum()) {
					echo $database->stderr();
					return false;
				}	
				//update the old ids from zone table	
				$query = "UPDATE #__ad_agency_zone SET `zoneid`='{$xid}' WHERE `zoneid`=$pol->zoneid";
				$database->setQuery( $query );
				$database->query();
				if ($database->getErrorNum()) {
					echo $database->stderr();
					return false;
				}
				$query = "INSERT INTO #__modules_menu SET moduleid = $xid, menuid = 0";
				$database->setQuery( $query );
				$database->query();	
			}
		}
	}
		//get the total number of unnapproved advertisers		
		$sql111	= "SELECT count( aid )
			FROM `#__ad_agency_advertis`
			WHERE `approved` = 'P'
			AND `user_id`
			IN (
				SELECT id
				FROM #__users
			)";
		$database->setQuery($sql111);
	    $total_a = $database->loadResult();		
		//get the total number of unnapproved banners		
		$database->setQuery("SELECT count(*) FROM #__ad_agency_banners WHERE approved = 'P'");
		$total_b = $database->loadResult();
		//get the total number of unnapproved campaigns			
		$database->setQuery("SELECT count(*) FROM #__ad_agency_campaign WHERE approved = 'P'");
		$total_c = $database->loadResult();		
		//get the total number of orders
		$datta=date('Y-m-d');	
		$database->setQuery("SELECT count(*) FROM #__ad_agency_order WHERE order_date='$datta' AND `status`='paid'");
		$total_o = $database->loadResult();		
		//get the total revenue
		$database->setQuery("SELECT cost FROM #__ad_agency_order WHERE order_date='$datta'  and status='paid'");
		$orderss = $database->LoadObjectlist();		
		$total_r=0;
		foreach ($orderss as $ordd)
		{
		  $total_r=$total_r + $ordd->cost;;
		}			
		
		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$database->setQuery($sql);
		$configs = $database->loadObject();
		$configs->geoparams = @unserialize($configs->geoparams);
		//echo "<pre>";var_dump($configs);die();
		
		$geo_not_set = NULL;
		if(isset($configs->geoparams['allowgeo']) || isset($configs->geoparams['allowgeoexisting'])) {
			if(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs->countryloc."/country-AD.txt")) { $geo_not_set = "<div style='padding: 3px; border:1px solid #FF0000; background-color: #FFFFCC; width: 515px;'>".str_replace("settings page","<a href='index.php?option=com_adagency&controller=adagencyGeo&task=settings'>settings page</a>",JText::_('ADAG_GEO_NOT_SET'))."</div>"; }
			if(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs->cityloc)||(!strpos($configs->cityloc,'.dat'))) { $geo_not_set = "<div style='padding: 3px; border:1px solid #FF0000; background-color: #FFFFCC; width: 515px;'>".str_replace("settings page","<a href='index.php?option=com_adagency&controller=adagencyGeo&task=settings'>settings page</a>",JText::_('ADAG_GEO_NOT_SET'))."</div>"; }
			if(!file_exists(str_replace("administrator","",JPATH_BASE)."/".$configs->codeloc."/areacode.txt")) { $geo_not_set = "<div style='padding: 3px; border:1px solid #FF0000; background-color: #FFFFCC; width: 515px;'>".str_replace("settings page","<a href='index.php?option=com_adagency&controller=adagencyGeo&task=settings'>settings page</a>",JText::_('ADAG_GEO_NOT_SET'))."</div>"; }
		}
		//echo "<pre>";var_dump($geo_not_set);die();
		
		$database->setQuery("SELECT `currencydef` FROM #__ad_agency_settings WHERE 1");
		$currencydef = trim($database->loadResult()," ");
		
		if(isset($get_data['final_up'])&&($get_data['final_up'] == '1')){
			$upgrade_set = "<div style='padding: 3px; border:1px solid #FF0000; background-color: #FFFFCC; width: 515px; font-size: 16px; font-weight: bold;'>".JText::_('ADAG_UPG_COMPLETE')."</div>";
		} else {
			$upgrade_set = NULL;
		}

		$this->assign("upgrade_set", $upgrade_set);	
		$this->assign("geo_not_set", $geo_not_set);
		$this->assign("currencydef", $currencydef);	
		$this->assign("total_a", $total_a);
		$this->assign("total_b", $total_b);
		$this->assign("total_c", $total_c);
		$this->assign("total_o", $total_o);
		$this->assign("total_r", $total_r);
		
		parent::display($tpl);

	}
	
	function getRevenue(){
		$db = JFactory::getDBO();
		$sql = "SELECT sum(`cost`) as revenue, `currency` FROM `#__ad_agency_order` WHERE `status`='paid' and `payment_type` <> 'Free' group by `currency`";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getOrders(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as orders FROM `#__ad_agency_order` WHERE `status`='paid'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getPendingAds(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__ad_agency_banners` WHERE `approved`='P'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getPendingAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__ad_agency_advertis` WHERE `approved`='P'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getPendingCampaigns(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__ad_agency_campaign` WHERE `approved`='P'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getRecentOrders(){
		$db = JFactory::getDBO();
		$sql = "SELECT o.*, ot.description, u.name, u.id as user_id FROM `#__ad_agency_order` o, `#__ad_agency_order_type` ot, #__users u, #__ad_agency_advertis ad WHERE o.`status`='paid' and ot.tid=o.tid and ad.aid=o.aid and ad.user_id = u.id order by `order_date` desc limit 0,5";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getActiveAdvertisers(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__ad_agency_advertis` WHERE `approved`='Y'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getActiveCampaigns(){
		$db = JFactory::getDBO();
		
		$config = JFactory::getConfig();
		$siteOffset = $config->get('offset');
		$jnow = JFactory::getDate('now', $siteOffset);
		
		$sql = "SELECT count(*) as total FROM `#__ad_agency_campaign` WHERE (`validity` >= '".$jnow."' OR `validity` = '0000-00-00 00:00:00') and `approved` = 'Y'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getActiveAds(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__ad_agency_banners` WHERE `approved`='Y'";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getActivePromoCodes(){
		$today =  time();
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__ad_agency_promocodes` WHERE `codestart`<='".$today."' and (`codeend`>='".$today."' OR `codeend` = 0) and (`codelimit` <> `used` OR `codelimit` = 0)";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getActiveZones(){
		$db = JFactory::getDBO();
		$sql = "SELECT count(*) as total FROM `#__modules` m, `#__ad_agency_zone` z where z.zoneid=m.id and m.published=1";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadColumn();
		return $result;
	}
	
	function getUsedPromo(){
		$db = JFactory::getDBO();
		$sql = "SELECT p.`id`, p.`title`, count(*) as total FROM `#__ad_agency_promocodes` p, `#__ad_agency_order` o WHERE p.id=o.promocodeid group by p.id limit 0, 7";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}
	
	function getCTR(){
		$db = JFactory::getDBO();
		
		$sql = "select * from #__ad_agency_statistics";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		$temp = array();
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$impressions = array();
				$click = array();
				
				$impressions = @json_decode($value["impressions"], true);
				$click = @json_decode($value["click"], true);
				
				if(isset($impressions) && count($impressions) > 0){
					$nr_imp = 0;
					foreach($impressions as $key_imp=>$value_imp){
						$banner_id = @$value_imp["banner_id"];
						$advertiser_id = @$value_imp["advertiser_id"];
						$campaign_id = @$value_imp["campaign_id"];
						
						if(intval($banner_id) != 0 && intval($advertiser_id) != 0 && intval($campaign_id) != 0){
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["banner_id"] = $banner_id;
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["advertiser_id"] = $advertiser_id;
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["campaign_id"] = $campaign_id;
							
							$nr_imp += @$value_imp["how_many"];
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["impressions"] = $nr_imp;
						}
					}
				}
				
				if(isset($click) && count($click) > 0){
					$nr_click = 0;
					foreach($click as $key_click=>$value_click){
						$banner_id = $value_click["banner_id"];
						$advertiser_id = $value_click["advertiser_id"];
						$campaign_id = $value_click["campaign_id"];
						
						if(intval($banner_id) != 0 && intval($advertiser_id) != 0 && intval($campaign_id) != 0){
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["banner_id"] = $banner_id;
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["advertiser_id"] = $advertiser_id;
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["campaign_id"] = $campaign_id;
							
							$nr_click += $value_click["how_many"];
							$temp[$banner_id."-".$advertiser_id."-".$campaign_id]["click"] = $nr_click;
						}
					}
				}
			}
			
			if(isset($temp) && count($temp) > 0){
				foreach($temp as $key=>$value){
					$sql = "select `media_type`, `title`, `width`, `height` from #__ad_agency_banners where `id`=".intval($value["banner_id"]);
					$db->setQuery($sql);
					$db->query();
					$banner = $db->loadAssocList();
					
					$temp[$key]["media_type"] = $banner["0"]["media_type"];
					$temp[$key]["title"] = $banner["0"]["title"];
					$temp[$key]["width"] = $banner["0"]["width"];
					$temp[$key]["height"] = $banner["0"]["height"];
					
					$sql = "select u.`name`, a.`user_id` from #__users u, #__ad_agency_advertis a where u.`id`=a.`user_id` and a.`aid`=".intval($value["advertiser_id"]);
					$db->setQuery($sql);
					$db->query();
					$user = $db->loadAssocList();
					
					$temp[$key]["name"] = @$user["0"]["name"];
					$temp[$key]["user_id"] = @$user["0"]["user_id"];
					
					if(!isset($value["impressions"])){
						$value["impressions"] = 0;
					}
					
					if(!isset($value["click"])){
						$value["click"] = 0;
					}
					
					$nr = 0;
					if(intval($value["impressions"]) > 0){
						$nr = $value["click"] / $value["impressions"] * 100;
					}
					$nr = number_format($nr, 2, '.', ' ');
					$temp[$key]["click_rate"] = $nr." %";
				}
			}
			
			$result = $temp;
		}
		
		$temp = array();
		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$temp[$key] = $value["click_rate"];
			}
		}
		arsort($temp);
		$temp = array_slice($temp, 0, 7);
		
		$return = array();
		foreach($temp as $key=>$value){
			$return[] = $result[$key];
		}
		
		return $return;
	}
	
	function getOrdersByYear($year){
		$db = JFactory::getDBO();
		$sql = "SELECT max(s.total) as max_amount from (
						SELECT MONTH(`order_date`) AS period,
	       				SUM(cost) AS total,
	       				date_format(`order_date`, '%m') AS month
						FROM `#__ad_agency_order`
						WHERE `status`='paid' and `order_date` like '".$year."-%'
						GROUP BY period) s";
		$db->setQuery($sql);
		$db->query();
		$max_amout = $db->loadColumn();
		$max_amout = $max_amout["0"];
		
		if($max_amout < 5){
			$max_amout = 5;
		}
		elseif($max_amout < 10){
			$max_amout = 10;
		}
		elseif($max_amout < 100){
			$max_amout = 100;
		}
		elseif($max_amout <500){
			$max_amout = 500;
		}
		elseif($max_amout < 1000){
			$max_amout = 1000;
		}
		elseif($max_amout < 5000){
			$max_amout = 5000;
		}
		elseif($max_amout < 10000){
			$max_amout = 10000;
		}
		elseif($max_amout < 50000){
			$max_amout = 50000;
		}
		elseif($max_amout < 100000){
			$max_amout = 100000;
		}
		elseif($max_amout < 500000){
			$max_amout = 500000;
		}
		elseif($max_amout < 1000000){
			$max_amout = 1000000;
		}
		
		$return["max_amout"] = $max_amout;
		
		$sql = "SELECT
					MONTH(`order_date`) AS period,
					SUM(cost) AS total,
					date_format(`order_date`, '%m') AS month
				FROM `#__ad_agency_order`
				WHERE `status`='paid'
				GROUP BY period";
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList("month");
		$return["orders"] = $result;
		
		return $return;
	}

	function avg(){
		$db = JFactory::getDBO();
		$licenses = NULL;
		
		$sql = "	SELECT b . * , camp.id campaign_id, camp.name campaign_name, a.aid AS advertiser_id2, a.company AS advertiser, concat( width, 'x', height ) AS size_type, m.id mid, m.title zone_name
					FROM #__ad_agency_banners AS b
					LEFT OUTER JOIN #__ad_agency_advertis AS a ON b.advertiser_id = a.aid
					LEFT JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id = b.id
					LEFT JOIN #__ad_agency_campaign AS camp ON camp.id = cb.campaign_id
					LEFT JOIN #__ad_agency_order_type AS p ON camp.otid = p.tid
					LEFT JOIN #__modules AS m ON m.id = cb.zone
					WHERE 1=1 
					GROUP BY b.id
                    ORDER BY b.ordering ASC , b.id DESC";
		$db->setQuery($sql);
		$db->query();
		$licenses = $db->loadObjectList();
		
		$rows = $licenses;
		$ids = array();
		
		if(is_array($rows)){
			foreach($rows as $row){
				array_push($ids, $row->id);
			}
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
				$impressions = array();
				$click = array();
				
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
						$banner_id = @$value_click["banner_id"];
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
					if(intval($temp[$key]["impressions"]) > 0){
						$click_rate = @$temp[$key]["click"] / $temp[$key]["impressions"] * 100;
					}
					$click_rate = number_format($click_rate, 2, '.', ' ');
					
					$temp2->click_rate = $click_rate;
					
					$temp[$key] = $temp2;
				}
				$licenses = $temp;
			}
			else{
				$licenses = (object)array();
			}
		}
		
		$rows2 = $licenses;
		if(isset($rows2) && count($rows2) > 0){
			foreach($rows2 as $row2){
				foreach($rows as $k=>$row){
					if(@$row2->banner_id == $row->id){
						$rows[$k]->impressions = $row2->impressions;
						$rows[$k]->click = $row2->click;
						$rows[$k]->click_rate = $row2->click_rate;
						$sqlz	= "SELECT count(c.id) FROM #__ad_agency_campaign c INNER JOIN #__ad_agency_campaign_banner cb on c.id = cb.campaign_id WHERE banner_id = {$row->id}";
						$db->setQuery($sqlz);
						$db->query();
						$licenses = $db->loadObjectList();
					}
				}
			}
			$licenses = $rows;
		}
			
		if(!isset($licenses) || count($licenses) == 0){
			return "0.00 %";
		}
		else{
			$total = 0;
			foreach($licenses as $key=>$license){
				if(isset($license->click_rate)){
					$total += $license->click_rate;
				}
			}
			if($total > 0){
				$avg = $total / count($licenses);
				return number_format($avg, 2, '.', ' ')." %";
			}
			else{
				return "0.00 %";
			}
		}
	}
	
	function installAllModules(){
		jimport('joomla.filesystem.folder');
		$modules = array();
		$sourceModules	= JPATH_ROOT . '/administrator/components/com_adagency/all_modules';

		$listModules = JFolder::files($sourceModules);
		
		foreach($listModules as $row){
			$modules[] = $sourceModules."/".$row;
		}
		
		jimport('joomla.installer.installer');
		jimport('joomla.installer.helper');

		$app = JFactory::getApplication();

		foreach($modules as $module){
			$package   = JInstallerHelper::unpack($module);
			$installer = JInstaller::getInstance();

			if(!$installer->install($package['dir'])){
				// There was an error installing the package
			}

			// Cleanup the install files
			if (!is_file($package['packagefile'])){
				$package['packagefile'] = $app->getCfg('tmp_path').'/'.$package['packagefile'];
			}
			JInstallerHelper::cleanupInstall('', $package['extractdir']);
		}
		
		// check if need to migrate stats
		$db = JFactory::getDBO();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_statistics` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `entry_date` date NOT NULL DEFAULT '0000-00-00',
				  `impressions` longtext,
				  `click` longtext,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "CREATE TABLE IF NOT EXISTS `#__ad_agency_ips` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `entry_date` date NOT NULL DEFAULT '0000-00-00',
				  `ips_impressions` longtext,
				  `ips_clicks` longtext NOT NULL,
				  PRIMARY KEY (`id`)
				) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
		$db->setQuery($sql);
		$db->query();
		
		$sql = "select count(*) from #__ad_agency_statistics";
		$db->setQuery($sql);
		$db->query();
		$count = $db->loadColumn();
		$count = @$count["0"];
		
		if($count == 0){
			echo '<script type="text/jscript" language="javascript">
					window.location.href = "'.JURI::root().'administrator/index.php?option=com_adagency&migrate=1&start=0";
				  </script>';
		}
		else{
			echo '<script type="text/jscript" language="javascript">
					window.location.href = "'.JURI::root().'administrator/index.php?option=com_adagency";
				  </script>';
		}
	}
	
	function alreadyExists($element, $all_rows, $type){
		$return = array("exists"=>FALSE, "position"=>"0", "element"=>$element);
		
		if(isset($all_rows) && count($all_rows) > 0){
			foreach($all_rows[$type] as $key=>$value){
				if($element["advertiser_id"] == $value["advertiser_id"] && $element["campaign_id"] == $value["campaign_id"]){
					$element["how_many"] += $value["how_many"];
					$return = array("exists"=>TRUE, "position"=>$key, "element"=>$element);
					return $return;
				}
			}
		}
		
		return $return;
	}
	
	function migrateStatistics(){
		$db = JFactory::getDBO();
		$limit = 10000;
		
		echo '<div class="alert alert-info">
				'.JText::_("ADAGENCY_MIGRATING").'
			  </div>
		
			  <div class="progress progress-info progress-striped">
				<div class="bar" style="width: 100%;"></div>
			  </div>';
		
		$start = JRequest::getVar("start", "0");
		
		$sql = "select * from #__ad_agency_stat order by `entry_date` asc Limit ".$start.",".$limit;
		$db->setQuery($sql);
		$db->query();
		$result = $db->loadAssocList();
		
		$all_rows = array();

		if(isset($result) && count($result) > 0){
			foreach($result as $key=>$value){
				$date = $value["entry_date"];
				$date = strtotime($date);
				$date = date("Y-m-d", $date);
				
				if($value["type"] == "impressions"){
					unset($value["entry_date"]);
					unset($value["type"]);
					
					$temp = $this->alreadyExists($value, $all_rows[$date], 'impressions');
					
					if($temp["exists"] === TRUE){
						$all_rows[$date]["impressions"][$temp["position"]] = $temp["element"];
					}
					else{
						$all_rows[$date]["impressions"][] = $value;
					}
				}
				else{
					unset($value["entry_date"]);
					unset($value["type"]);
					
					$temp = $this->alreadyExists($value, $all_rows[$date], 'click');
					
					if($temp["exists"] === TRUE){
						$all_rows[$date]["click"][$temp["position"]] = $temp["element"];
					}
					else{
						$all_rows[$date]["click"][] = $value;
					}
				}
			}
		}
		
		if(isset($all_rows) && count($all_rows) > 0){
			foreach($all_rows as $key=>$value){
				$sql = "select * from #__ad_agency_statistics where `entry_date`='".trim($key)."'";
				$db->setQuery($sql);
				$db->query();
				$statistics = $db->loadAssocList();
				
				$impressions = array();
				$click = array();
				
				if(isset($statistics) && count($statistics) > 0){
					if(isset($value["impressions"])){
						$old_impressions = $value["impressions"];
						$new_impressions = array();
						
						if(isset($statistics["0"]["impressions"]) && trim($statistics["0"]["impressions"]) != ""  && trim($statistics["0"]["impressions"]) != "[]"){
							$new_impressions = json_decode($statistics["0"]["impressions"], true);
						}
						
						if(count($new_impressions) == 0){
							$new_impressions = $old_impressions;
						}
						else{
							foreach($old_impressions as $old_key=>$old_value){
								$find = false;
								foreach($new_impressions as $new_key=>$new_value){
									if($old_value["advertiser_id"] == $new_value["advertiser_id"] && $old_value["campaign_id"] == $new_value["campaign_id"] && $old_value["banner_id"] == $new_value["banner_id"]){
										$new_impressions[$new_key]["how_many"] = intval($new_value["how_many"]) + intval($old_value["how_many"]);
										$find = true;
									}
								}
								if(!$find){
									$new_impressions[] = $old_value;
								}
							}
						}
						$impressions = $new_impressions;
					}
					
					if(isset($value["click"])){
						$old_click = $value["click"];
						$new_click = array();
						
						if(isset($statistics["0"]["click"]) && trim($statistics["0"]["click"]) != "" && trim($statistics["0"]["click"]) != "[]"){
							$new_click = json_decode($statistics["0"]["click"], true);
						}
						
						if(count($new_click) == 0){
							$new_click = $old_click;
						}
						else{
							foreach($old_click as $old_key=>$old_value){
								$find = false;
								foreach($new_click as $new_key=>$new_value){
									if($old_value["advertiser_id"] == $new_value["advertiser_id"] && $old_value["campaign_id"] == $new_value["campaign_id"] && $old_value["banner_id"] == $new_value["banner_id"]){
										$new_click[$new_key]["how_many"] = intval($new_value["how_many"]) + intval($old_value["how_many"]);
										$find = true;
									}
								}
								if(!$find){
									$new_click[] = $old_value;
								}
							}
						}
						$click = $new_click;
					}
					
					$update_id = $statistics["0"]["id"];
					
					if(count($impressions) > 0){
						$sql = "update #__ad_agency_statistics set `impressions`='".json_encode($impressions)."' where `id`=".intval($update_id);
						$db->setQuery($sql);
						$db->query();
					}
					
					if(count($click) > 0){
						$sql = "update #__ad_agency_statistics set `click`='".json_encode($click)."' where `id`=".intval($update_id);
						$db->setQuery($sql);
						$db->query();
					}
				}
				else{
					if(isset($value["impressions"]) && trim($value["impressions"]) != "null"){
						$impressions = $value["impressions"];
					}
					
					if(isset($value["click"]) && trim($value["click"]) != "null"){
						$click = $value["click"];
					}
					
					$impressions = @json_encode($value["impressions"]);
					$click = @json_encode($value["click"]);
					
					if(trim($impressions) == "" || trim($impressions) == "null"){
						$impressions = "[]";
					}
					
					if(trim($click) == "" || trim($click) == "null"){
						$click = "[]";
					}
					
					$sql = "insert into #__ad_agency_statistics (`entry_date`, `impressions`, `click`) values ('".$key."', '".addslashes(trim($impressions))."', '".addslashes(trim($click))."')";
					$db->setQuery($sql);
					$db->query();
				}
			}
			
			echo '<script type="text/jscript" language="javascript">
					window.location.href = "'.JURI::root().'administrator/index.php?option=com_adagency&migrate=1&start='.($start + $limit + 1).'";
			  	  </script>';
		}
		else{
			// it's over
			echo '<script type="text/jscript" language="javascript">
					window.location.href = "'.JURI::root().'administrator/index.php?option=com_adagency";
				  </script>';
		}
	}
	
}

?>