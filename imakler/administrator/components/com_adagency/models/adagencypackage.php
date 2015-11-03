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



class adagencyAdminModeladagencyPackage extends JModelLegacy {

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

		

		if(JRequest::getVar("limitstart") == JRequest::getVar("old_limit")){

			JRequest::setVar("limitstart", "0");		

			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');

			$limitstart = $mainframe->getUserStateFromRequest($option.'limitstart', 'limitstart', 0, 'int');

		}

		

		$this->setState('limit', $limit); // Set the limit variable for query later on

		$this->setState('limitstart', $limitstart);

	}



	function getThePositions($clientid) {

		if (empty ($this->_position)) {

			$db = JFactory::getDBO();



			// extracting the positions from template - start

			$query = 'SELECT template'.

				' FROM #__template_styles' .

				' WHERE client_id=0';

			// ADAGENCYONEFIVE-97 - stop

			$db->setQuery( $query );

			$templateDir = $db->loadResult();



			$client		= JApplicationHelper::getClientInfo(JRequest::getVar('client', '0', '', 'int'));

			$tBaseDir	= JPath::clean($client->path.DS.'templates');



			if(!is_file($tBaseDir.DS.$templateDir.DS.'templateDetails.xml')) {

				return false;

			}



			//$xml = JFactory::getFeedParser('Simple');

			

			if (!JFactory::getXML($tBaseDir.DS.$templateDir.DS.'templateDetails.xml')) {

				unset($xml);

				return false;

			}



			$xml = JFactory::getXML($tBaseDir.DS.$templateDir.DS.'templateDetails.xml');

			

			$element_array = $xml->positions->position;

			if(isset($element_array))

			foreach($element_array as $key=>$element)

				$xml_positions[] = $element;



			// ADAGENCYONEFIVE-97 - start

			// displaying all positions regardless the client_id

			$query = 'SELECT DISTINCT(position)'.

				' FROM #__modules' .

				' WHERE 1 = 1 ORDER BY position ASC';

			// ADAGENCYONEFIVE-97 - stop

			$db->setQuery( $query );

			$db_positions = $db->loadResultArray();



			$all_positions = $xml_positions;

			if(isset($db_positions))

			foreach($db_positions as $one_position)

				if(!@in_array($one_position, $all_positions))

					$all_positions[] = $one_position;



			@sort($all_positions);



			$this->_position = $all_positions;

			$this->_position = (is_array($this->_position)) ? $this->_position : array();

		}

		return $this->_position;



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



	function getlistPackages () {

		$data = JRequest::get('post');

		$db = JFactory::getDBO();

		$and_filter="WHERE o.tid>0";

		/* adding the search condition for Packages  - start */

		if(isset($data['search_package']))

		{

			$_SESSION['search_package'] = $data['search_package'];

			$search_package = $data['search_package'];

		}

		elseif(isset($_SESSION['search_package']))

			$search_package = $db->escape($_SESSION['search_package']);

		if(isset($search_package) && $search_package!=''){

			$and_filter = $and_filter." AND (description like '%".$search_package."%' OR tid like '%".$search_package."%' OR pack_description LIKE '%".$search_package."%')"; }

		/* adding the search condition for Packages - stop */



		/* adding the type condition for Packages  - start */

		if(isset($data['type_package']))

		{

			$_SESSION['type_package'] = $data['type_package'];

			$type_package = $data['type_package'];

		}

		elseif(isset($_SESSION['type_package']))

			$type_package = $db->escape($_SESSION['type_package']);

		if(isset($type_package) && $type_package!='' && (in_array($type_package, array("cpm","pc","fr", "in"))) ){

			$and_filter = $and_filter." AND (type='".$type_package."')"; }

		/* adding the type condition for Packages - stop */



		/* adding the zone select condition - start */

		if(isset($data['package_zone']))

			{

				$zone = $data['package_zone'];

				$_SESSION['package_zone'] = $data['package_zone'];

			}

		elseif(isset($_SESSION['package_zone']))

			$zone = $_SESSION['package_zone'];

		if(isset($zone) && $zone!='All Zones'){

				if($zone != '0') {	$and_filter = $and_filter." AND pz.zone_id = ".$zone." ";}

		 }

		/* adding the advertiser select condition - stop */



		/* adding  status package select condition - start */

		if(isset($data['package_status']))

			{

				$package_status = $data['package_status'];

				$_SESSION['package_status'] = $data['package_status'];

			}

		elseif(isset($_SESSION['package_status']))

			$package_status = $_SESSION['package_status'];

		if(isset($package_status) && $package_status!='-1'){

				$and_filter = $and_filter." AND published='".$package_status."'";

		}

		/* adding status package select condition - stop */

        $long_session = $this->getState('limit');

		if (empty ($this->_packages)) {

            $db = JFactory::getDBO();

            if (isset($data['limit'])&&($data['limit']!='')&&($data['limit']!=0)&&($data['limitstart']!=0)&&($data['limitstart']!=''))

            {

                $and_limit=" LIMIT ".$data['limitstart'].",".$data['limit'];

            }

            elseif (isset($_data['limit'])&&($data['limit']!='')&&($data['limit']!=0))

            {

                $and_limit=" LIMIT ".$data['limit'];

            }

            elseif (isset($_SESSION['limit'])&&($_SESSION['limit']!='')&&($_SESSION['limit']!=0))

            {

                $and_limit=" LIMIT ".$_SESSION['limit'];

            }

            elseif ($long_session)

            {

                $and_limit=" LIMIT ".$long_session;

            }



            if (JRequest::getInt('newzone', '0', 'get')) {

                $and_limit = NULL;

                //$and_filter = NULL;

				$newzone = JRequest::getInt('newzone', '0', 'get');

				$sql = "select `inventory_zone` from #__ad_agency_zone where `zoneid`=".intval($newzone);

				$db->setQuery($sql);

				$db->query();

				$inventory_zone = $db->loadColumn();

				$inventory_zone = $inventory_zone["0"];

				

				if($inventory_zone == 0){

					$and_filter = " where o.`type` <> 'in' ";

				}

				else{

					$and_filter = " where o.`type` = 'in' ";

				}

            }



			// packages for current page

			if (!isset($and_limit)) { $and_limit="";}

            $sql_base = "SELECT o.*, pz.zone_id FROM #__ad_agency_order_type AS o

                LEFT JOIN #__ad_agency_package_zone AS pz

                ON o.tid = pz.package_id ".$and_filter."

                GROUP BY o.tid ORDER BY o.ordering";

				

			$sql = $sql_base.$and_limit ;

			$sql2 = "SELECT COUNT(*) FROM (".$sql_base.") AS alz";

			$db->setQuery($sql2);

			$this->_total = $db->loadResult();

			$this->_packages = $this->_getList($sql);

		}



		return $this->_packages;

	}



	function getZonesForPacks($packs){

		$db = JFactory::getDBO();

		if(isset($packs)&&(is_array($packs))){

			foreach($packs as $pack){

				if(isset($pack->tid)){

					$sql = "SELECT pz.zone_id, m.title

							FROM #__ad_agency_package_zone AS pz

							LEFT JOIN #__modules AS m ON pz.zone_id = m.id

							WHERE pz.package_id =".intval($pack->tid);

					$db->setQuery($sql);

					$pack->location =  $db->loadObjectList();

				}

			}



			//echo "<pre>";var_dump($packs);die();

			return $packs;

		} else {

			return NULL;

		}

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



	function getZones(){

		$db = JFactory::getDBO();

		$and = "";

		$type = JRequest::getVar("type", "");

		

		if(trim($type) == ""){

			$cid = JRequest::getVar("cid", array(), "get", "array");

			if(isset($cid["0"]) && intval($cid["0"]) != 0){

				$sql = "SELECT `type` from #__ad_agency_order_type where `tid`=".intval($cid["0"]);

				$db->setQuery($sql);

				$result = $db->loadColumn();

				$type = $result["0"];

			}

		}

		

		if(trim($type) == "in"){

			$and = " where `inventory_zone`=1";

		}

		else{

			$and = " where `inventory_zone`=0";

		}

		$sql = "SELECT zoneid, z_title FROM #__ad_agency_zone".$and;

		$db->setQuery($sql);

		$res = $db->loadObjectList();

		return $res;

	}



	function getZonesByPackId($id){

		$db = JFactory::getDBO();

		$sql = "SELECT zone_id FROM #__ad_agency_package_zone WHERE package_id = ".intval($id);

		$db->setQuery($sql);

		$res = $db->loadResultArray();

		$res = $db->loadColumn();

		

		return $res;

	}



	function getZoneById($id){

		$db = JFactory::getDBO();

		$sql = "SELECT zoneid, z_title FROM #__ad_agency_zone WHERE zoneid = ".intval($id)." ORDER BY z_title ASC";

		$db->setQuery($sql);

		$res = $db->loadObject();

		return $res;

	}



    function zonePacks() {
        $db = JFactory::getDBO();
        $data = JRequest::get('data');
	
        $sql = "DELETE FROM #__ad_agency_package_zone WHERE zone_id = '{$data['nz']}' ";
        $sqlz[] = $sql;
        $db->setQuery($sql);
        $db->query();

        if(is_array($data['cid'])){
            foreach ($data['cid'] as $element) {
                $sql = "INSERT INTO `#__ad_agency_package_zone` (`package_id` , `zone_id`) VALUES ('$element', '{$data['nz']}');";
                $sqlz[] = $sql;
                $db->setQuery($sql);
                $db->query();
            }
        }
        return true;
    }



	function store () {

		$db = JFactory::getDBO();

		$item = $this->getTable('adagencyPackage');

		$data = JRequest::get('post');
		
		$data['pack_description'] = JRequest::getVar('pack_description', '', 'post', 'string', JREQUEST_ALLOWRAW);

		

		if(isset($data['amount']) && isset($data['duration'])){

			$data['validity'] = ($data['amount']>0 && $data['duration']!="") ? $data['amount'] . "|" . $data['duration'] : "";

		}

		

		if ($data['type']=='fr' || $data['type']=='in') $data['quantity']=0;



		//get new zones



		$newzones=array();

		$thezones='';

		if ($data['selzone']==1) $newzones[]='All Zones';

		 else {

			$allzones = $data['allzones'];

			$zones = explode('|',$allzones);

			foreach ($zones as $zone)

				if (isset($data[$zone]))

				    $newzones[]=$zone;

		 }



		if(!isset($data['hide_after'])){$data['hide_after']=0;}

		$data['visibility']=$data['visible'];

		

		if (!$item->bind($data)){

			$this->setError($item->getErrorMsg());

			return false;

		}



		if (!$item->check()) {

			$this->setError($item->getErrorMsg());

			return false;

		}



		$item->zones = implode('|',$newzones);

		if (!$item->store()) {

			$this->setError($item->getErrorMsg());

			return false;



		}



		if(isset($data['tid'])){

			// if we are dealing with a new package get its id

			if(intval($data['tid'])<=0) {

				$sql = "SELECT tid FROM #__ad_agency_order_type ORDER BY tid DESC LIMIT 1";

				$db->setQuery($sql);

				$data['tid'] = $db->loadResult();

			}

			if(intval($data['tid'])>0) {

				$sql = "DELETE FROM #__ad_agency_package_zone WHERE package_id = ".intval($data['tid']);

				$db->setQuery($sql);

				$db->query($sql);

				if((isset($data['packz']))&&(is_array($data['packz']))) {

					foreach($data['packz'] as $element){

						$sql = "INSERT INTO `#__ad_agency_package_zone` (`package_id` ,`zone_id`) VALUES ('".intval($data['tid'])."', '".intval($element)."');";

						$db->setQuery($sql);

						$db->query();

						//$sqlz[] = $sql;

					}

				}

			}

		}



		return true;



	}



	function delete () {

		$cids = JRequest::getVar('cid', array(0), 'post', 'array');

		$item = $this->getTable('adagencyPackage');

		$db = JFactory::getDBO();

		

		foreach($cids as $cid){

			$date = JFactory::getDate();

			$current_date = $date->toSql();

			$sql = "select count(*) from #__ad_agency_campaign where `otid`=".intval($cid)." and `approved`='Y' and `start_date`<='".$current_date."' and (`validity` >= '".$current_date."' or `validity`='0000-00-00 00:00:00')";

			$db->setQuery($sql);

			$db->query();

			$count = $db->loadColumn();

			if(intval($count["0"]) > 0){

				$app = JFactory::getApplication("admin");

				$app->redirect("index.php?option=com_adagency&controller=adagencyPackages", JText::_("ADAG_PACKAGE_ASSIGNED_TO_VALID_CAMPAIGN"), 'error');

			}

			

			if(!$item->delete($cid)) {

				$this->setError($item->getErrorMsg());

				return false;

			}

		}



		return true;

	}



	function publish () {

		$db = JFactory::getDBO();

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



	/*function saveorder() {

		$db =& JFactory::getDBO();

		$data = JRequest::get('post');

		$task = JRequest::getVar('task', '', 'post');

		$i=0;

		foreach ($data['pack'] as $key => $id) {

			$keys[$i] = intval($key);

			$orderings[$i] = intval($id);

			$i++;

		}

		$i--;

		if ($task == 'saveorder'){

 		 for($j=$i;$j>=0;$j--){

			$sql = "update #__ad_agency_order_type set ordering='".$orderings[$j]."' where tid = '".$keys[$j]."' ";

			$sql2 .= "<br />".$sql;

			$db->setQuery($sql);

			if (!$db->query()){

				$this->setError($db->getErrorMsg());

				return false;

				}

		 }

		}

		return "New order saved";

	}*/



	function saveorder($idArray = null, $lft_array = null){

		// Get an instance of the table object.

		$table = $this->getTable("adagencyPackage");



		if(!$table->saveorder($idArray, $lft_array)){

			$this->setError($table->getError());

			return false;

		}

		// Clean the cache

		$this->cleanCache();

		return true;

	}



};

?>

