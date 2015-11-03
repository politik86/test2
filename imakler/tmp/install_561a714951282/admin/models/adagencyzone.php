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

if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);



class adagencyAdminModeladagencyZone extends JModelLegacy {

	var $_promos;

	var $_promo;

	var $_id = null;



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



	function getPagination(){

		// Lets load the content if it doesn't already exist

		if (empty($this->_pagination))	{

			jimport('joomla.html.pagination');

			if (!$this->_total) $this->getlistZones();

			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );

		}

		return $this->_pagination;

	}



	function setId($id) {

		$this->_id = $id;

		$this->_promo = null;

	}



	function getApprovedAds(){

		$db = JFactory::getDBO();

		$sql="SELECT id,title FROM `#__ad_agency_banners` WHERE approved='Y'";

		$db->setQuery($sql);

		$result=$db->loadObjectList();

		return $result;

	}



	function getZoneById($id){

		$db = JFactory::getDBO();

		$sql="SELECT z.*, m.published FROM `#__ad_agency_zone` AS z, `#__modules` AS m WHERE zoneid='".intval($id)."' AND z.zoneid = m.id LIMIT 1";

		$db->setQuery($sql);

		$result=$db->loadObjectList();

		return $result;

	}



	function getLastZoneId(){

		$db = JFactory::getDBO();

		$sql="SELECT zoneid FROM `#__ad_agency_zone` ORDER BY zoneid DESC LIMIT 1";

		$db->setQuery($sql);

		$result=$db->loadResult();

		return $result;

	}

	

	function duplicate(){
		$db = JFactory::getDBO();

		foreach ($_POST['cid'] as $element){
			$item = $this->getTable('adagencyZone');
			$modul = $this->getTable('adagencyModules');
			// Saving the module - begin
			$current = $this->getZoneById($element);
			$data = $current[0];
			$data->z_title.=" copy";
			$data->title=$data->z_title;
			$data->zoneid=NULL;
			$data->showtitle=$data->show_title;
			$data->params=$data->suffix;
			$data->module = 'mod_ijoomla_adagency_zone';
			$data->published = 0;
			$data->position = $data->z_position;
			$zone = $data;

			if (!$modul->bind($data)){
				$this->setError($modul->getErrorMsg());
				return false;
			}
			
			if (!$modul->check()) {
				$this->setError($modul->getErrorMsg());
				return false;
			}

			if (!$modul->store()) {
				$this->setError($modul->getErrorMsg());
				return false;
			}

			// Saving the module - End
			// Saving the zone - Begin

			$zone->zoneid = mysql_insert_id();

			if ($zone->zoneid==0) {
				$ask = "SELECT `id` FROM `#__modules` ORDER BY `id` DESC LIMIT 1 ";
				$db->setQuery( $ask );
				$zone->zoneid = $db->loadResult();
			}

			$query = "INSERT INTO #__ad_agency_zone ( `zoneid` , `banners` , `banners_cols`, `z_title` , `z_ordering` , `z_position` , `show_title` , `suffix`, `rotatebanners`, `rotaterandomize`, `rotating_time`, `show_adv_link`, `link_taketo`, `taketo_url`, `cellpadding`, `inventory_zone`, `adparams`, `textadparams`, `defaultad`) VALUES ({$zone->zoneid}, {$zone->banners}, {$zone->banners_cols}, '{$zone->z_title}', {$zone->z_ordering}, '{$zone->z_position}', {$zone->showtitle}, '{$zone->suffix}', '{$zone->rotatebanners}', '{$zone->rotaterandomize}', '{$zone->rotating_time}', '{$zone->show_adv_link}', '{$zone->link_taketo}', '{$zone->taketo_url}', '{$zone->cellpadding}', ".intval($zone->inventory_zone).", '".$zone->adparams."', '".$zone->textadparams."', '{$zone->defaultad}')";
			
			$db->setQuery($query);
			$db->query();
			// Saving the zone - End
			// Saving the menu assignment - Begin

			$query = "SELECT menuid FROM `#__modules_menu` WHERE `moduleid` = ".intval($element);
			$db->setQuery($query);
			$results = $db->loadColumn();
			$cond = array();

			foreach($results as $key){
				$cond[].="('".$zone->zoneid."','{$key}')";
			}

			if(count($cond) > 0){
				$cond = implode(", ",$cond);
				$query = "INSERT INTO `#__modules_menu` (`moduleid` ,`menuid`) VALUES ".$cond.";";
				$db->setQuery($query);
				$db->query();
			}
			// Saving the menu assignment - End



			//	Saving the wildcard packages also - Begin

			$query = "SELECT tid, zones_wildcard FROM `#__ad_agency_order_type` WHERE

			`zones_wildcard` LIKE '%|".$element."' OR

			`zones_wildcard` LIKE '%|".$element."|%' OR

			`zones_wildcard` LIKE '".$element."|%' OR

			`zones_wildcard`='".$element."' ";

			$db->setQuery($query);

			$wildcard_list = $db->loadObjectList();



			if(isset($wildcard_list)){

				foreach ($wildcard_list as $current){

					$sql="UPDATE `#__ad_agency_order_type` SET `zones_wildcard` = '".$current->zones_wildcard."|".$zone->zoneid."' WHERE `tid` =".intval($current->tid).";";

					$db->setQuery($sql);

					$db->query();

				}

			}

			$data = NULL;

			//	Saving the wildcard packages also - End

		}

		return true;

	}



	function getlistZones () {

		if (empty ($this->_promos)) {

		$and_filter="";

		$limit_cond=NULL;

		$db = JFactory::getDBO();



		/* search by zone(module) name - start */

		$P_search_zone=JRequest::getVar('search_zone');

		if(isset($P_search_zone))

		{

			$_SESSION['search_zone'] = $P_search_zone;

			$search_zone = $P_search_zone;

		}

		elseif(isset($_SESSION['search_zone']))

			$search_zone = $_SESSION['search_zone'];

		if(isset($search_zone) && $search_zone!=''){

			$and_filter.=" AND (m.title LIKE '%".$search_zone."%' OR m.id LIKE '%".$search_zone."%')"; }

		/* search by zone(module) name - stop */



		/* search by zone(module) position - start */

		$P_module_position = JRequest::getVar('module_position');

		if(isset($P_module_position)){

			$_SESSION['module_position'] = $P_module_position;

			$search_position = $P_module_position;

		}

		else

			if(isset($_SESSION['module_position']))

				$search_position = $_SESSION['module_position'];

		if (isset($search_position) && $search_position!='' && $search_position!='0' && $search_position!= trim(JText::_('AD_ZONES_ALL_POS')) ){

			$and_filter.=" AND (m.position='" . trim($search_position) ."')";

		}

		

		$active_zones = JRequest::getVar("active_zones", "0");

		if($active_zones == 1){

			$and_filter.=" AND (m.published='1') ";

		}

		

		/* search by zone(module) position - stop */

			$where[] = "m.module='mod_ijoomla_adagency_zone'";

			$sql ="SELECT m.*, MIN(mm.menuid) AS pages, z.*

                      FROM #__modules AS m

                      LEFT JOIN #__modules_menu AS mm

                      ON mm.moduleid = m.id

                      LEFT JOIN #__ad_agency_zone AS z

                      ON z.zoneid = m.id

                      WHERE m.module='mod_ijoomla_adagency_zone'

                      ".$and_filter."

                      GROUP BY m.id ORDER BY z.ordering ASC";



			$limitstart=$this->getState('limitstart');

			$limit=$this->getState('limit');



			if($limit!=0){

				$limit_cond=" LIMIT ".$limitstart.",".$limit." ";

			}



			$this->_total = $this->_getListCount($sql);

			$this->_promos = $this->_getList($sql.$limit_cond);

		}

		return $this->_promos;

	}



	function getZone() {

		if (empty ($this->_promo)) {

			$this->_promo = $this->getTable("adagencyZone");

			$this->_promo->load($this->_id);

		}

		return $this->_promo;

	}



	function cleanup() {

		$db =  JFactory::getDBO();

		$sql = "SELECT id FROM `#__modules` WHERE module = 'mod_ijoomla_adagency_zone' AND id NOT IN

				(SELECT zoneid FROM `#__ad_agency_zone`) ORDER BY id DESC";

		$db->setQuery($sql);

		$res = $db->loadColumn();

		

		$res[count($res)] = '0';

		$cond = implode(',',$res);

		//echo "<pre>";var_dump($cond);die();

		$sql = "DELETE FROM `#__modules` WHERE id IN (".$cond.")";

		$db->setQuery($sql);

		$db->query();

		return true;

	}





	function getTheModule($moduleid) {

		if (empty ($this->_module)) {

			$sql = "SELECT * FROM #__modules WHERE id ='".intval($moduleid)."'";

			$this->_module = $this->_getList($sql);

		}

		return $this->_module;

	}



	function getZonePacks2Select($param){

		if (empty ($this->_selpack)) {

			$sql = "SELECT tid,description,zones_wildcard FROM #__ad_agency_order_type WHERE zones NOT LIKE '%".$param."%' AND zones NOT LIKE '%All Zones%'";

			$this->_selpack = $this->_getList($sql);

		}

		return $this->_selpack;

	}



	function getZonePacks2SelectALL(){

		if (empty ($this->_selpack)) {

			$sql = "SELECT tid,description,zones_wildcard FROM #__ad_agency_order_type";

			$this->_selpack = $this->_getList($sql);

		}

		return $this->_selpack;

	}

	

	function getTemplatePositions(){

		if (empty ($this->_position)) {

			$db = JFactory::getDBO();

			// extracting the positions from template - start

			$query = 'SELECT `template` FROM `#__template_styles` WHERE `client_id` = 0 AND `home` = 1';

			// ADAGENCYONEFIVE-97 - stop

			$db->setQuery( $query );

			$templateDir = $db->loadResult();



			$client		= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

			$tBaseDir	= JPath::clean($client->path.DS.'templates');



            // echo $tBaseDir.DS.$templateDir.'templateDetails.xml';die();

            // echo "<pre>";var_dump($query);die();



			if(!is_file($tBaseDir.DS.$templateDir.DS.'templateDetails.xml')) {

				return false;

			}

			$xml = JFactory::getXML($tBaseDir.DS.$templateDir.DS.'templateDetails.xml');

			$element_array = $xml->positions;

			$params = new JForm($element_array);

			$params2 = $params->getName("name");

			$params2 = (array)$params2;

			$xml_positions = $params2["position"];

			sort($xml_positions);



			$this->_position = $xml_positions;

			$this->_position = (is_array($this->_position)) ? $this->_position : array();

		}

		

		return $this->_position;

	}

	function getThePositions($clientid = null) {
		if (empty ($this->_position)) {
			$db = JFactory::getDBO();
			// extracting the positions from template - start
			$query = 'SELECT `template` FROM `#__template_styles` WHERE `client_id` = 0 AND `home` = 1';
			// ADAGENCYONEFIVE-97 - stop
			$db->setQuery($query);
			$templateDir = $db->loadResult();
			$client		= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));
			$tBaseDir	= JPath::clean($client->path.DS.'templates');
            // echo $tBaseDir.DS.$templateDir.'templateDetails.xml';die();
			if(!is_file($tBaseDir.DS.$templateDir.DS.'templateDetails.xml')) {
				return false;
			}

			$xml = JFactory::getXML($tBaseDir.DS.$templateDir.DS.'templateDetails.xml');
			$element_array = $xml->positions;
			$params = new JForm($element_array);
			$params2 = $params->getName("name");
			$params2 = (array)$params2;
			$xml_positions = $params2["position"];
			
			// ADAGENCYONEFIVE-97 - start
			// displaying all positions regardless the client_id
			$query = 'SELECT DISTINCT(position)'.
				' FROM #__modules' .
				' WHERE 1 = 1 ORDER BY position ASC';
			// ADAGENCYONEFIVE-97 - stop

			$db->setQuery( $query );
			$db_positions = $db->loadColumn();
			$all_positions = $xml_positions;

			if(!is_array($all_positions)){
				$all_positions = array($all_positions);
			}

			if(isset($db_positions))
			
			foreach($db_positions as $one_position){
				if(trim($one_position) != ""){
					if(!in_array($one_position, $all_positions)){
						$all_positions[] = $one_position;
					}
				}
			}
			
			sort($all_positions);
			$this->_position = $all_positions;
			$this->_position = (is_array($this->_position)) ? $this->_position : array();
		}
		return $this->_position;
	}

	function getLockZone($zoneid){

		$db = JFactory::getDBO();

        $sql = "SELECT COUNT(cb.campaign_id)

                FROM #__ad_agency_package_zone AS pz

                JOIN #__ad_agency_campaign AS c ON pz.package_id = c.otid

                JOIN #__ad_agency_campaign_banner AS cb ON c.id = cb.campaign_id

                JOIN #__ad_agency_banners AS b ON cb.banner_id = b.id

                WHERE cb.zone = pz.zone_id AND pz.zone_id =" . intval($zoneid);

		$db->setQuery($sql);

		$existing = $db->loadColumn();

		$existing = $existing["0"];

		

		if($existing > 0){

			return true;

		}

		else{

			return false;

		}

	}



	function store () {

		$db = JFactory::getDBO();

		$item = $this->getTable('adagencyZone');

		$modul = $this->getTable('adagencyModules');

		$data = JRequest::get('post', JREQUEST_ALLOWRAW);

		

        // if the zone is new remember that

        if ( !isset($data['zoneid']) || ($data['zoneid'] <= 0) ) {

            $isNew = TRUE;

        } else {

            $isNew = FALSE;

        }



		// Determine wheather there was a change

		// to the text ad maximum width/height setting

		$trigger_thumb_resize = false;

		if(isset($data['zoneid']) && ($data['zoneid'] != 0) && isset($data['textadparams'])) {

			$sql = "SELECT textadparams FROM #__ad_agency_zone WHERE zoneid = ".$data['zoneid'];

			$db->setQuery($sql);

			$old_tsettings = @unserialize($db->loadResult());
			
			
			if(!isset($old_tsettings->mxsize) || !isset($old_tsettings->mxtype) || ($old_tsettings->mxsize != $data['textadparams']['mxsize']) || ($old_tsettings->mxtype != $data['textadparams']['mxtype'])) {

				$trigger_thumb_resize = true;



				if(!isset($data['adparams']['width']) || ($data['adparams']['width'] == '') || ($data['adparams']['width'] == 0)) {

					$ad_w = $data['textadparams']['mxsize'];

				} else { $ad_w = $data['adparams']['width']; }

				if(!isset($data['adparams']['height']) || ($data['adparams']['height'] == '') || ($data['adparams']['height'] == 0)) {

					$ad_h = $data['textadparams']['mxsize'];

				} else { $ad_h = $data['textadparams']['mxsize']; }



				if($data['textadparams']['mxtype'] == 'w') {

					$new_width = min($data['textadparams']['mxsize'], $ad_w);

					$new_height = '';

				} else {

					$new_width = '';

					$new_height = min($data['textadparams']['mxsize'], $ad_h);

				}

			}

		}



		if(isset($data["inventory_zone"]) && $data["inventory_zone"] == 1){// zone is inventory and delete zone from packages that are not inventory

			$sql = "select `tid` from #__ad_agency_order_type where `type` <> 'in'";

			$db->setQuery($sql);

			$db->query();

			$tid = $db->loadColumn();

			if(isset($tid) && count($tid) > 0){

				$tid = implode(",", $tid);

				$sql = "delete from #__ad_agency_package_zone where `package_id` in (".$tid.") and `zone_id`=".intval($data["zoneid"]);

				$db->setQuery($sql);

				$db->query();

			}

		}

		else{// zone is not inventory and delete zone from packages that are inventory

			$sql = "select `tid` from #__ad_agency_order_type where `type` = 'in'";

			$db->setQuery($sql);

			$db->query();

			$tid = $db->loadColumn();

			if(isset($tid) && count($tid) > 0){

				$tid = implode(",", $tid);

				$sql = "delete from #__ad_agency_package_zone where `package_id` in (".$tid.") and `zone_id`=".intval($data["zoneid"]);

				$db->setQuery($sql);

				$db->query();

			}

		}

		

		if(isset($data['bx'])&&($data['bx']=='2')) { $data['adparams']['textad']= '1'; }

		if(isset($data['adparams']['width']) && isset($data['adparams']['height'])){

			$data['textadparams']['width'] = $data['adparams']['width'];

			$data['textadparams']['height'] = $data['adparams']['height'];

		}

		$data['adparams'] = @serialize($data['adparams']);



		$zona = array();

		$zona['z_title'] = $data['title'];

		$zona['banners'] = $data['banners'];

		$zona['banners_cols'] = $data['banners_cols'];

		if (isset($data['zoneid'])) $zona['zoneid'] = $data['zoneid'];

		$zona['showtitle'] = $data['showtitle'];

		$zona['z_ordering'] = $data['ordering'];

		$zona['z_position'] = $data['position'];



		$zona['rotatebanners'] = $data['rotatebanners'];

		$zona['rotaterandomize'] = $data['rotaterandomize'];

		$zona['rotating_time'] = $data['rotating_time'];

		$zona['defaultad'] = $data['defaultad'];



		$zona['show_adv_link'] = $data['show_adv_link'];

		$zona['link_taketo'] = $data['link_taketo'];

		$zona['cellpadding'] = $data['cellpadding'];

		$zona['adparams'] = $data['adparams'];

		$zona['keywords'] = $data['showkeyws'];

		$zona['ignorestyle'] = $data['ignorestyle'];

		$zona['textadparams'] = @serialize($data['textadparams']);

		

		$zona['zone_text_below'] = $data['zone_text_below'];

		$zona['zone_content_visibility'] = $data['zone_content_visibility'];

		$zona['zone_content_location'] = $data['zone_content_location'];

		

		$zona['inventory_zone'] = $data['inventory_zone'];



		if(isset($data['taketo_url']))

			$zona['taketo_url'] = $data['taketo_url'];

		else

			$zona['taketo_url'] = 'http://';

		/*$zona['suffix'] = 'moduleclass_sfx='.$data['params'];

		$data['params'] = $zona['suffix'];*/

		
	//'moduleclass_sfx' => $data['params'], 
		////replaced with:
	//echo"<pre>";
	//print_r($data);
	//echo"</pre>";
		//die();
		
		$arr = array('moduleclass_sfx' => $data['suffix'], 'cache' => $data['cache']);
		$data['params'] = json_encode($arr);
		
	
		///

		

		if (isset($data['zoneid'])) {

			$data['id'] = $data['zoneid'];

			$zonid = $data['zoneid'];

		}

		$data['module'] = 'mod_ijoomla_adagency_zone';

		

		if(!isset($data["publish_up"])){

			$joomla_date = Jfactory::getDate();

			$data["publish_up"] = $joomla_date->toSql();

		}

		

		if (!$modul->bind($data)){

			$this->setError($modul->getErrorMsg());

			return false;

		}



		if (!$modul->check()) {

			$this->setError($modul->getErrorMsg());

			return false;

		}



		if (!$modul->store()) {

			$this->setError($modul->getErrorMsg());

			return false;

		}

		if ($zona['zoneid']=="") {

			$zona['zoneid'] = mysql_insert_id();

			if ($zona['zoneid']==0) {

				$ask = "SELECT `id` FROM `#__modules` ORDER BY `id` DESC LIMIT 1 ";

				$db->setQuery( $ask );

				$zona['zoneid'] = $db->loadResult();

			}

			if(!isset($zona['z_ordering'])) { $zona['z_ordering'] = 0; }

			$query = "INSERT INTO #__ad_agency_zone ( `zoneid` , `banners`, `banners_cols` , `z_title` , `z_ordering` , `z_position` , `show_title` , `suffix`, `rotatebanners`, `rotaterandomize`, `rotating_time`, `show_adv_link`, `link_taketo`, `taketo_url`, `cellpadding`, `defaultad`, `keywords`, `adparams`, `ignorestyle`, `textadparams`, `inventory_zone`) VALUES ({$zona['zoneid']}, {$zona['banners']}, {$zona['banners_cols']}, '{$zona['z_title']}', {$zona['z_ordering']}, '{$zona['z_position']}', {$zona['showtitle']}, '{$zona['suffix']}', '{$zona['rotatebanners']}', '{$zona['rotaterandomize']}', '{$zona['rotating_time']}', '{$zona['show_adv_link']}', '{$zona['link_taketo']}', '{$zona['taketo_url']}', '{$zona['cellpadding']}', '{$zone['defaultad']}', '{$zone['keywords']}', '{$zona['adparams']}', '{$zona['ignorestyle']}','{$zona['textadparams']}', ".intval($zona["inventory_zone"]).")";

			$db->setQuery( $query );

			$db->query();

		} else {

			

			if (!$item->bind($zona)){

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

		}



		$menus = JRequest::getVar( 'menus', '', 'post', 'word' );

		$selections = JRequest::getVar( 'selections', array(), 'post', 'array' );

		JArrayHelper::toInteger($selections);

		// delete old module to menu item associations

		if (!isset($data['id']) || ($data['id']==0)) $data['id'] = $zona['zoneid'];

			$query = 'DELETE FROM #__modules_menu'

			. ' WHERE moduleid = '.(int) $data['id']

			;

			$db->setQuery( $query );

			if (!$db->query()) {

				return JError::raiseWarning( 500, $row->getError() );

			}



			// check needed to stop a module being assigned to `All`

			// and other menu items resulting in a module being displayed twice

			if ( $menus == 'all' ) {

				// assign new module to `all` menu item associations

				$query = 'INSERT INTO #__modules_menu'

				. ' SET moduleid = '.(int) $data['id'].' , menuid = 0'

				;

				$db->setQuery( $query );

				if (!$db->query()) {

					return JError::raiseWarning( 500, $row->getError() );

				}

			}

			else

			{

				foreach ($selections as $menuid)

				{

					// this check for the blank spaces in the select box that have been added for cosmetic reasons

					if ( (int) $menuid >= 0 ) {

						// assign new module to menu item associations

						$query = 'INSERT INTO #__modules_menu'

						. ' SET moduleid = '.(int) $data['id'] .', menuid = '.(int) $menuid

						;

						$db->setQuery( $query );

						if (!$db->query()) {

							return JError::raiseWarning( 500, $row->getError() );

						}

					}

				}

			}



		if($trigger_thumb_resize) {

			//echo "<pre>";var_dump($new_width);echo "<hr />";var_dump($new_height);die();

			$sql = "SELECT imgfolder FROM #__ad_agency_settings LIMIT 1";

			$db->setQuery($sql);

			$imgfolder = $db->loadResult();

			$JPATH_BASE = str_replace('administrator','',JPATH_BASE);

			$sql = "SELECT cb . * , b.advertiser_id, b.image_url

					FROM #__ad_agency_package_zone AS pz

					LEFT JOIN #__ad_agency_campaign AS c ON pz.package_id = c.otid

					LEFT JOIN #__ad_agency_campaign_banner AS cb ON c.id = cb.campaign_id

					LEFT JOIN #__ad_agency_banners AS b ON cb.banner_id = b.id

					WHERE pz.zone_id =".$data['id'];

			$db->setQuery($sql);

			$elements = $db->loadObjectList();



			//echo "<pre>";var_dump($elements);die();



			foreach($elements as $element){

				$img_url = $JPATH_BASE.'images'.DS.'stories'.DS.$imgfolder.DS.$element->advertiser_id.DS.$element->image_url;

				$thumb = $this->makeThumb($img_url, $new_width, $new_height);

				if($thumb != NULL) {

					$sql = "UPDATE `#__ad_agency_campaign_banner` SET `thumb` = '".$thumb."' WHERE `campaign_id` ='".$element->campaign_id."' AND `banner_id` ='".$element->banner_id."';";

				} else {

					$sql = "UPDATE `#__ad_agency_campaign_banner` SET `thumb` = NULL WHERE `campaign_id` ='".$element->campaign_id."' AND `banner_id` ='".$element->banner_id."';";

				}

				$sss[] = $sql;

				$db->setQuery($sql);

				$db->query();

			}

		}



        $sql = "SELECT package_id FROM #__ad_agency_package_zone WHERE zone_id = {$data['id']} LIMIT 1";

        $db->setQuery($sql);

        $has_packages = $db->loadResult();



        $session = JFactory::getSession();

        if ($isNew) {

            $session->set("newzone-{$data['id']}", "1", 'adag');

        } elseif ($has_packages) {

            $session->clear("newzone-{$data['id']}", 'adag');

        }



        // echo "<pre>";var_dump($data);die();



        return $data['id'];

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



	function delete () {

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$database =   JFactory::getDBO();

		$item =  $this->getTable('adagencyZone');



		$modul =  $this->getTable('adagencyModules');

		foreach ($cids as $cid) {

			if (!$item->delete($cid)) {

				$this->setError($item->getErrorMsg());

				return false;

			}

			if (!$modul->delete($cid)) {

				$this->setError($modul->getErrorMsg());

				return false;

			}

			$query = "DELETE FROM #__modules_menu WHERE moduleid ='".intval($cid)."'";

			$database->setQuery( $query );

			$database->query();

		}

		return true;

	}



	function publish () {

		$db =  JFactory::getDBO();

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$task = JRequest::getVar('task', '', 'post');

		$item = $this->getTable('adagencyZone');

		$res = 0;

		if ($task == 'publish'){

			$res = 1;

			$sql = "UPDATE #__modules SET published='1' WHERE id IN ('".implode("','", $cids)."')";

		} else {

			$res = -1;

			$sql = "UPDATE #__modules SET published='0' WHERE id IN ('".implode("','", $cids)."')";



		}

		$db->setQuery($sql);

		if (!$db->query() ){

			$this->setError($db->getErrorMsg());

		}

		return $res;

	}

	

	function saveorder($idArray = null, $lft_array = null){

		// Get an instance of the table object.

		$table = $this->getTable("adagencyZone");



		if(!$table->saveorder($idArray, $lft_array)){

			$this->setError($table->getError());

			return false;

		}

		// Clean the cache

		$this->cleanCache();

		return true;

	}



	function getZonesAds(){

		$zones_ads = array();

		

		$db = JFactory::getDBO();

		$sql = "select distinct(`zone`) from `#__ad_agency_campaign_banner`";

		$db->setQuery($sql);

		$db->query();

		$zones = $db->loadAssocList("zone");

		

		if(isset($zones) && count($zones) > 0){

			foreach($zones as $key=>$zone){

				$sql = "SELECT b . * , camp.id campaign_id, camp.name campaign_name, a.aid AS advertiser_id2, a.company AS advertiser, concat( width, 'x', height ) AS size_type, m.id mid, m.title zone_name FROM #__ad_agency_banners AS b LEFT OUTER JOIN #__ad_agency_advertis AS a ON b.advertiser_id = a.aid LEFT JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id = b.id LEFT JOIN #__ad_agency_campaign AS camp ON camp.id = cb.campaign_id LEFT JOIN #__ad_agency_order_type AS p ON camp.otid = p.tid LEFT JOIN #__modules AS m ON m.id = cb.zone WHERE 1=1 AND m.id = '".intval($key)."' GROUP BY b.id ORDER BY b.ordering ASC , b.id DESC";

				$db->setQuery($sql);

				$db->query();

				$result = $this->_getListCount($sql);

				$zones_ads[$key]["total"] = intval($result);

			}

		}

		

		return $zones_ads;

	}



};

?>

