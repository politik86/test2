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



class adagencyAdminModeladagencyAds extends JModelLegacy {

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

		

		if(JRequest::getVar("limitstart") == JRequest::getVar("old_limit")){

			JRequest::setVar("limitstart", "0");		

			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');

		}

		

		$this->setState('limit', $limit); // Set the limit variable for query later on

		$this->setState('limitstart', $limitstart);

	}



	function setId($id) {

		$this->_id = $id;

		$this->_license = null;

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



	function rememberChannel(){

		$data = JRequest::get('post');

		//echo "<pre>";var_dump($data);die();

		if(isset($data['geo_type'])&&($data['geo_type'] == 1)){

			if(isset($data['limitation'])&&($data['limitation'] != '')) {

				$temp = NULL;

				$region_city_exist = false;

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

		} elseif(isset($data['geo_type'])&&($data['geo_type'] == 2)){

			if(isset($data['limitation_existing'])&&($data['limitation_existing'] != 0)) {

				$_SESSION['channelz2'] = $data['limitation_existing'];

		//		echo "2";die();

			}

		}

		

		return true;

	}



	function getChannelInfo(){

		$JPATH_BASE = str_replace('administrator','',JPATH_BASE); 

		require_once($JPATH_BASE.'/components/com_adagency/helpers/geoipotherdata.php');

		require_once($JPATH_BASE.'/components/com_adagency/helpers/geoipregionvars.php');		

		if (!function_exists('json_encode')) {

			require_once($JPATH_BASE.DS.'components'.DS.'com_adagency'.DS.'helpers'.DS.'jsonwrapper.php');

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

						foreach($element->data as &$val){

							if($counterREG == 0){

								$aux = $val;

								$val = $HELPER_GEOIP_COUNTRIES[$val];

							} else {

								$val = $GEOIP_REGION_NAME[$aux][$val];

							}

							$counterREG++;

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



	function getlistAds () {

		$limit_cond=NULL;

		$db = JFactory::getDBO();

		if (empty ($this->_licenses)) {

			if(isset($_GET['cid'][0]) && $_GET['cid'][0] > 0)	

				{

					$and_filter = ' AND cb.campaign_id = '.intval($_GET['cid'][0]).' ';

				}

			else

					$and_filter = '';

					

		if(isset($_GET['apr'])&&($_GET['apr']==true)) { $and_filter.= " AND b.approved = 'P' "; }			

		/* adding the search condition - start */			

		if(isset($_POST['search_text']))

		{

			$_SESSION['search_text'] = trim($_POST['search_text']);

			$search_text = trim($_POST['search_text']);

		}

		elseif(isset($_SESSION['search_text']))

			$search_text = trim($_SESSION['search_text']);

			

		if(isset($search_text) && $search_text!=''){

			$and_filter = $and_filter." AND (b.id LIKE '%".addslashes($search_text)."%' OR b.title LIKE '%".addslashes($search_text)."%' OR b.description LIKE '%".addslashes($search_text)."%')"; }								

		/* adding the search condition - stop */		

		

		/* adding the advertiser select condition - start */			

		/*if(isset($_REQUEST['advertiser_id'])){
			$advertiser = intval($_REQUEST['advertiser_id']);
			$_SESSION['advertiser_id'] = intval($_REQUEST['advertiser_id']);
		}
		elseif(isset($_SESSION['advertiser_id'])){
			$advertiser = $_SESSION['advertiser_id'];
		}
		
		if(isset($advertiser) && $advertiser != '0'){
			$and_filter = $and_filter." AND b.advertiser_id = '".$advertiser."' ";
		}*/

		/* adding the advertiser select condition - stop */

		
		$advertisers_select = JRequest::getVar("advertisers_select", "0");
		if(intval($advertisers_select) != 0){
			$and_filter = $and_filter." AND b.advertiser_id = '".$advertisers_select."' ";
		}

		/* adding the type select condition - start */			

		if(isset($_POST['type_select']))

			{

				$type_select = trim($_POST['type_select']);

				$_SESSION['type_select'] = trim($_POST['type_select']);

			}	

		elseif(isset($_SESSION['type_select']))	

			$type_select = $_SESSION['type_select'];

			

		if(isset($type_select) && $type_select!='all' && $type_select!=""){

				$and_filter = $and_filter." AND b.media_type = '".$type_select."' "; }			

		/* adding the advertiser select condition - stop */		

		

		/* adding the zone select condition - start */			

		$zone_id = JRequest::getVar('zone_id', "0");

		if(isset($zone_id) && intval($zone_id) != 0){

			$zone = $zone_id;

			$_SESSION['zone_id'] = $zone_id;

		}

		else{

			$_SESSION['zone_id'] = 0;

			$zone = 0;

		}

		

		if(isset($zone) && $zone!='0'){

				$and_filter = $and_filter." AND m.id = '".$zone."' "; }			

		/* adding the zone select condition - stop */	

		

		/* adding the status select condition - start */			

		if(isset($_REQUEST['status_select']))

			{

				$status_select = JRequest::getVar("status_select", "YA");

				$_SESSION['status_select'] = JRequest::getVar("status_select", "YA");

			}	

		elseif(isset($_SESSION['status_select']))	

			$status_select = JRequest::getVar("status_select", "YA");

		if(isset($status_select) && $status_select!='YA' && $status_select!=""){

				$and_filter = $and_filter." AND b.approved = '".$status_select."' "; }			

		/* adding the status select condition - stop */			
		
		$camp_id = JRequest::getVar("camp_id", "0");
		if(intval($camp_id) > 0){
			$and_filter .= " AND cb.`campaign_id`=".intval($camp_id);
		}
		
		$sql = "	SELECT b . * , camp.id campaign_id, camp.name campaign_name, a.aid AS advertiser_id2, a.company AS advertiser, concat( width, 'x', height ) AS size_type, m.id mid, m.title zone_name

					FROM #__ad_agency_banners AS b

					LEFT OUTER JOIN #__ad_agency_advertis AS a ON b.advertiser_id = a.aid

					LEFT JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id = b.id

					LEFT JOIN #__ad_agency_campaign AS camp ON camp.id = cb.campaign_id

					LEFT JOIN #__ad_agency_order_type AS p ON camp.otid = p.tid

					LEFT JOIN #__modules AS m ON m.id = cb.zone

					WHERE 1=1 " . $and_filter . "

					GROUP BY b.id

                    ORDER BY b.ordering ASC , b.id DESC

				";

		$limitstart=$this->getState('limitstart');

		$limit=$this->getState('limit');

			

		if($limit!=0){

			$limit_cond=" LIMIT ".$limitstart.",".$limit." ";

		} else {

			$limit_cond = NULL;

		}

		$this->_licenses = $this->_getList($sql.$limit_cond);

		$this->_total = $this->_getListCount($sql);

		$rows = $this->_licenses;

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
					if(!isset($impressions["0"])){
						$impressions = array("0"=>$impressions);
					}
					
					foreach($impressions as $key_imp=>$value_imp){
						$banner_id = @$value_imp["banner_id"];
						if(in_array($banner_id, $ids_array)){
							@$temp[$banner_id]["impressions"] += $value_imp["how_many"];
						}
					}
				}
				
				if(isset($click) && is_array($click) && count($click) > 0){
					if(!isset($click["0"])){
						$click = array("0"=>$click);
					}
					
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
					
					$click_rate = "0.0%";
					
					if($temp[$key]["impressions"] != 0){
						$click_rate = @$temp[$key]["click"] / $temp[$key]["impressions"] * 100;
						$click_rate = number_format($click_rate, 2, '.', ' ')."%";
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

					@$rows[$k]->impressions = @$row2->impressions;

					@$rows[$k]->click = @$row2->click;

					@$rows[$k]->click_rate = @$row2->click_rate;

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

			

			$this->_license = $this->_getList($sql);

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

	

	function copy () {

		$db =  JFactory::getDBO();

		$cid	= JRequest::getVar( 'cid', array(), 'post', 'array' );

		$n		= count( $cid );

		if ($n == 0) {

			return JError::raiseWarning( 500, JText::_( 'No items selected' ) );

		}

		

		foreach ($cid as $id)

		{

			$row = $this->getTable('adagencyAds');

			$db = JFactory::getDBO();

			// load the row from the db table

			$row->load( (int) $id );

			

			$row->title 		= $row->title.' copy';

			$row->id 			= 0;



			if (!$row->check()) {

				return JError::raiseWarning( 500, $row->getError() );

			}

			if (!$row->store()) {

				return JError::raiseWarning( 500, $row->getError() );

			}

			$row->checkin();

			unset($row);

			

			$sql = "SELECT id FROM #__ad_agency_banners ORDER BY id DESC LIMIT 1";

			$db->setQuery($sql);

			$currentid = $db->loadColumn();

			$currentid = $currentid["0"];

			

			$sql = "SELECT `campaign_id`, `zone` FROM #__ad_agency_campaign_banner WHERE banner_id = '".intval($id)."'";

			$db->setQuery($sql);

			$result = $db->loadAssocList();

						

			// Copy the channel settings as well here - BEGIN

			$sql = "SELECT * FROM #__ad_agency_channels WHERE banner_id = ".intval($id)." LIMIT 1";

			$db->setQuery($sql);

			$sqlz[] = $sql;

			$chan = $db->loadObject();

			

			if(isset($chan->id)){

				$sql = "INSERT INTO `#__ad_agency_channels` (`name` ,`banner_id` ,`advertiser_id` ,`public` ,`created` ,`created_by` ,`from`

			)VALUES ('".stripslashes($chan->name)."', '".intval($currentid)."', '".intval($chan->advertiser_id)."', '".stripslashes($chan->public)."',  NOW() , '".stripslashes($chan->created_by)."', '".stripslashes($chan->from)."');";

				$sqlz[] = $sql;

				$db->setQuery($sql);

				$db->query();

				

				$sql = "SELECT id FROM `#__ad_agency_channels` ORDER BY id DESC LIMIT 1";

				$sqlz[] = $sql;

				$db->setQuery($sql);

				$new_channel_id = $db->loadColumn();

				$new_channel_id = $new_channel_id["0"];

				

				$sql = "SELECT * FROM `#__ad_agency_channel_set` WHERE channel_id = ".intval($chan->id)." LIMIT 1";

				$sqlz[] = $sql;

				$db->setQuery($sql);

				$channel_sets = $db->loadObject();

				

				if(($channel_sets != NULL)&&($new_channel_id != NULL)) {

					$sql = "INSERT INTO `#__ad_agency_channel_set` (`channel_id` ,`type` ,`logical` ,`option` ,`data`) VALUES (

					'".intval($new_channel_id)."', '".stripslashes($channel_sets->type)."', '".stripslashes($channel_sets->logical)."', '".stripslashes($channel_sets->option)."', '".stripslashes($channel_sets->data)."');";

					$sqlz[] = $sql;

					$db->setQuery($sql);

					$db->query();

				}

			}

			// Copy the channel settings as well here - END

			foreach($result as $key=>$element) {

				$sql = "INSERT INTO `#__ad_agency_campaign_banner` (`campaign_id` ,`banner_id` ,`relative_weighting`, `zone`) VALUES ('".intval($element["campaign_id"])."', '".intval($currentid)."', '100', '".stripslashes($element["zone"])."');";

				$db->setQuery($sql);

				$db->query();

			}

		}

		

		return 1;

	}

	

	function delete(){
		$database =  JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$item = $this->getTable('adagencyAds');

		foreach ($cids as $cid) {
			if (!$item->delete($cid)){
				$this->setError($item->getErrorMsg());
				return false;
			}

			$sql = "select * from #__ad_agency_statistics where `impressions` like '%\"banner_id\":\"".intval($cid)."\",%' OR `impressions` like '%\"banner_id\":".intval($cid).",%' OR `click` like '%\"banner_id\":\"".intval($cid)."\",%' OR `click` like '%\"banner_id\":".intval($cid).",%'";
			$database->setQuery($sql);
			$database->query();
			$statistics = $database->loadAssocList();
			
			if(isset($statistics) && count($statistics) > 0){
				foreach($statistics as $key=>$value){
					$id = $value["id"];
					$impressions = @json_decode($value["impressions"], true);
					$click = @json_decode($value["click"], true);
					
					foreach($impressions as $key_imp=>$value_imp){
						if($value_imp["banner_id"] == intval($cid)){
							unset($impressions[$key_imp]);
						}
					}
					
					foreach($click as $key_click=>$value_click){
						if($value_click["banner_id"] == intval($cid)){
							unset($click[$key_click]);
						}
					}
					
					$sql = "update #__ad_agency_statistics set `impressions`='".json_encode($impressions)."', `click`='".json_encode($click)."' where `id`=".intval($id);
					$database->setQuery($sql);
					$database->query();
				}
			}

			$query = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id = '".intval($cid)."'";
			$database->setQuery( $query );
			if (!$database->query()){
				mosErrorAlert( $database->getErrorMsg() );
				exit;
			}
		}

		return true;

	}



	function publish () {

		$db = JFactory::getDBO();
		
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");

		$configs = $configs->getConfigs();

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$task = JRequest::getVar('task', '', 'post');

		$item = $this->getTable('adagencyAds');

		if ($task == 'publish'){

			$sql = "update #__ad_agency_banners set approved='Y' where id in ('".implode("','", $cids)."')";

			$ret = 1;

		} else {

			$ret = -1;

			$sql = "update #__ad_agency_banners set approved='N' where id in ('".implode("','", $cids)."')";

		}

		$db->setQuery($sql);

		if (!$db->query() ){

			$this->setError($db->getErrorMsg());

			return false;

		}

	

		//send email notifications

			$sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id IN ('".implode("','", $cids)."') GROUP BY b.id";

			$db->setQuery($sql);



			if(!$result = $db->query()) {

				echo $db->stderr();

				return;

			}

			$user = $db->loadObjectList();

	        foreach ( $user as $value) {			
				$ok_send_email = 1;
				if ($task == 'publish'){
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

		return $ret;

	}

	function saveorder($idArray = null, $lft_array = null){

		// Get an instance of the table object.

		$table = $this->getTable("adagencyAds");



		if(!$table->saveorder($idArray, $lft_array)){

			$this->setError($table->getError());

			return false;

		}

		// Clean the cache

		$this->cleanCache();

		return true;

	}



	function approve ($cid,$task) {

		$db = JFactory::getDBO();
		
		$sql = "select `params` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$params = $db->loadColumn();
		$params = @$params["0"];
		$params = unserialize($params);
		
		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");

		$configs = $configs->getConfigs();

		//$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		//$task = JRequest::getVar('task', '', 'post');

		$item = $this->getTable('adagencyAds');

		$sql = "update #__ad_agency_banners set approved='Y' where id = '".intval($cid)."' ";

		$db->setQuery($sql);

		if (!$db->query() ){

			$this->setError($db->getErrorMsg());

			return false;

		}



		//send email notifications

		$sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id = '".intval($cid)."' GROUP BY b.id";

		$db->setQuery($sql);

		if(!$result = $db->query()) {

			echo $db->stderr();

			return;

		}

		$user = $db->loadObjectList();

		foreach ( $user as $value){
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

	

	// get advertisers name by advertiser id

	function getAdvByAID($aid){

		$db = JFactory::getDBO();

		$sql = "SELECT u.name FROM `#__users` AS u, `#__ad_agency_advertis` AS a WHERE u.id = a.user_id AND a.aid= ".intval($aid);

		$db->setQuery($sql);

		$result = $db->loadResult();

		return $result;

	}

	

	function pending () {

		$db = JFactory::getDBO();

		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");

		$configs = $configs->getConfigs();

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$task = JRequest::getVar('task', '', 'post');

		$item = $this->getTable('adagencyAds');

		$sql = "UPDATE #__ad_agency_banners SET approved='P' WHERE id in ('".implode("','", $cids)."')";

		$db->setQuery($sql);

		if (!$db->query() ){

			$this->setError($db->getErrorMsg());

			return false;

		}

		return 0;

	}



	function unapprove () {

		$db = JFactory::getDBO();

		$configs = $this->getInstance("adagencyConfig", "adagencyAdminModel");

		$configs = $configs->getConfigs();

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$task = JRequest::getVar('task', '', 'post');

		$item = $this->getTable('adagencyAds');

		$sql = "update #__ad_agency_banners set approved='N' where id in ('".implode("','", $cids)."')";

		$db->setQuery($sql);

		if (!$db->query() ){

			$this->setError($db->getErrorMsg());

			return false;

		}

		//send email notifications

			$sql = "SELECT b.title, u.name, u.username, u.email FROM #__ad_agency_banners AS b LEFT JOIN #__ad_agency_advertis as a ON a.aid=b.advertiser_id LEFT JOIN #__users as u ON u.id=a.user_id WHERE b.id IN ('".implode("','", $cids)."') GROUP BY b.id";

			$db->setQuery($sql);

			if(!$result = $db->query()) {

				echo $db->stderr();

				return;

			}

			$user = $db->loadObjectList();

	        foreach ( $user as $value) {			

					$subject=$configs->sbaddisap;

					$message=$configs->bodyaddisap;

				$subject =str_replace('{name}',$value->name,$subject);

				$subject =str_replace('{login}',$value->username,$subject);

				$subject =str_replace('{email}',$value->email,$subject);

				$subject =str_replace('{banner}',$value->title,$subject);

				$message =str_replace('{name}',$value->name,$message);

				$message =str_replace('{login}',$value->username,$message);

				$message =str_replace('{email}',$value->email,$message);

				$message =str_replace('{banner}',$value->title,$message);

				// mail publish campaign				

				JFactory::getMailer()->sendMail($configs->fromemail, $configs->fromname, $value->email, $subject, $message, 1);				

			}

		//send email notifications

		return -1;

	}

};

?>