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


class adagencyModeladagencyPackage extends JModelLegacy {
	var $_packages;
	var $_package;
	var $_tid = null;
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

	function getConf(){
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadObject();
		return $res;
	}

	function getAid(){
		$database = JFactory::getDBO();
		$my = JFactory::getUser();
		$sql = "SELECT aid FROM #__ad_agency_advertis WHERE user_id='".intval($my->id)."'";
		$database->setQuery($sql);
	    $advertiserid = $database->loadResult();
		return $advertiserid;
	}

	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getListPackages();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_tid = $id;
		$this->_package = null;
	}

	function getShowZInfo(){
		$db = JFactory::getDBO();
		$sql = "SELECT `show` FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($sql);
		$shown = $db->loadResult();
		if(strpos(" ".$shown,"zinfo") > 0) {
			return true;
		} else {
			return false;
		}
	}

	function getlistPackages ($visibility=0) {
		if (empty ($this->_packages)) {
			$db = JFactory::getDBO();
			if($visibility==0)
			{
				//$sql = "SELECT * FROM #__ad_agency_order_type WHERE `published`=1 AND `visibility`<>0 ORDER BY ordering";
				$sql = "SELECT o.*,z.zoneid,z.banners,z.banners_cols,z.z_title,z.rotatebanners,z.adparams
						FROM #__ad_agency_order_type AS o
						LEFT JOIN #__ad_agency_zone AS z
						ON o.location = z.zoneid
						WHERE o.`published`=1 AND o.`visibility`<>0
						ORDER BY ordering";
			} else {
				$sql = "SELECT * FROM #__ad_agency_order_type WHERE `published`=1 ORDER BY ordering";
			}
			$db->setQuery($sql);
			$this->_total = $this->_getListCount($sql);
			$this->_packages = $this->_getList($sql);
		}

		return $this->_packages;
	}

	function getZonesForPacks($packs){
		$db = JFactory::getDBO();
		if(isset($packs)&&(is_array($packs))){
			foreach($packs as $pack){
				$sql = "SELECT m.title, m.id, z.banners as rows, z.banners_cols as cols, z.adparams, z.rotatebanners
						FROM #__ad_agency_package_zone AS pz
						LEFT JOIN #__modules AS m ON pz.zone_id = m.id
						LEFT JOIN #__ad_agency_zone AS z ON z.zoneid = m.id
						WHERE pz.package_id = ".intval($pack->tid);
				$db->setQuery($sql);
				$pack->location = $db->loadObjectList();
			}
		}
		return $packs;
	}

	function getPackage() {
		if (empty ($this->_package)) {
			$this->_package = $this->getTable("adagencyPackage");
			$this->_package->load($this->_tid);
			$data = JRequest::get('post');

			if (!$this->_package->bind($data)){
				$this->setError($item->getErrorMsg());
				return false;

			}

			if (!$this->_package->check()) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}
		return $this->_package;

	}

	function store () {
		$item = $this->getTable('adagencyPackage');
		$data = JRequest::get('post');
		$data['validity'] = ($data['amount']>0 && $data['duration']!="") ? $data['amount'] . "|" . $data['duration'] : "";
		if ($data['type']=='fr') $data['quantity']=0;
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

		return true;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('adagencyPackage');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}

		return true;
	}


	function publish () {
		$db =JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyPackage');
		if ($task == 'publish')
			$sql = "update #__ad_agency_order_type set published='1' where tid in ('".implode("','", $cids)."')";

		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return 1;
	}

	function unpublish () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$task = JRequest::getVar('task', '', 'post');
		$item = $this->getTable('adagencyPackage');
		if ($task == 'unpublish')
			$sql = "update #__ad_agency_order_type set published='0' where tid in ('".implode("','", $cids)."')";

		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
			return false;
		}
		return -1;
	}

	function getTemplate(){
		$db = JFactory::getDBO();
		$sql="SELECT template FROM #__template_styles WHERE client_id = '0' AND home = '1' ";
		$db->setQuery($sql);
		$default_template=$db->loadResult();
		return $default_template;
	}

	function getItemidLink(){
		$db = JFactory::getDBO();
		$get = JRequest::get('get');
		$sql = "SELECT m. * , n.link
			FROM #__modules_menu AS m
			LEFT JOIN #__menu AS n ON m.menuid = n.id
			WHERE n.access = 0
			AND n.published =1
			AND moduleid = ".intval($get['cid'])."
			ORDER BY menuid ASC
			LIMIT 1 ";
		$db->setQuery($sql);
		$res = $db->loadObject();
		if($res != NULL){
			return $res->link.'&Itemid='.intval($res->menuid);
		} else { return NULL; }
	}

	function getFreePermission($advertiserid,$packageid){
		if(!isset($advertiserid)||($advertiserid=='')||($advertiserid==0)) { return true; }

		$db = JFactory::getDBO();
		$sql="SELECT oid FROM #__ad_agency_order WHERE aid='".intval($advertiserid)."' AND payment_type='Free' AND tid='".intval($packageid)."' LIMIT 1";
		$db->setQuery($sql);
		$free_permission=$db->loadResult();
		$sql="SELECT hide_after FROM #__ad_agency_order_type WHERE tid='".intval($packageid)."'";
		$db->setQuery($sql);
		$hide_after = $db->loadResult();
		if(isset($free_permission)&&($hide_after==1)){ return false;}

		return true;
	}
	
	function checkInventoryPackage($package_id){
		$db = JFactory::getDBO();

		if(intval($package_id) != 0){// if package selected
			$sql = "select `type` from `#__ad_agency_order_type` where `tid`=".intval($package_id);
			$db->setQuery($sql);
			$db->query();
			$result = $db->loadColumn();
			$type = $result["0"];
			
			if(trim($type) == "in"){// if package is inventory
				$offset = JFactory::getApplication()->getCfg('offset');
				$today = JFactory::getDate('now', $offset);
				$today_date = $today->toUnix(true);
				
				$sql = "select z.* from #__ad_agency_zone z, #__ad_agency_package_zone pz where pz.`package_id`=".intval($package_id)." and pz.`zone_id`=z.`zoneid` and z.`inventory_zone`=1";
				$db->setQuery($sql);
				$db->query();
				$zones = $db->loadAssocList();
				
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
						return 'NO_SLOTS_AVAILABLE';
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
						$offset = JFactory::getApplication()->getCfg('offset');
						$today = JFactory::getDate('now', $offset);
						$most_recent_available_date = $today->toUnix(true);
					}
					elseif(!isset($most_recent_available_date)){
						$offset = JFactory::getApplication()->getCfg('offset');
						$today = JFactory::getDate('now', $offset);
						$most_recent_available_date = $today->toUnix(true);
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
					
					return date(str_replace(array("%", "h"), array("", "H"), $ymd), $most_recent_available_date);
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
