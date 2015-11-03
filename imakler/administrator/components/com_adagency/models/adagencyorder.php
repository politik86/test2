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

class adagencyAdminModeladagencyOrder extends JModelLegacy {
	var $_orders;
	var $_order;
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


	function getPagination(){
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getListOrders();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;
		$this->_order = null;
	}


	function getlistOrders () {
		$db = JFactory::getDBO();
		$and_filter="WHERE 1=1";
		/* adding the search condition for Orders  - start */			
		if(isset($_POST['search_order']))
		{
			$_SESSION['search_order'] = addslashes(trim($_POST['search_order']));
			$search_order = addslashes(trim($_POST['search_order']));
		}
		elseif(isset($_SESSION['search_order']))
			$search_order = addslashes(trim($_SESSION['search_order']));
			
		if(isset($search_order) && $search_order!=''){
			$and_filter = $and_filter." AND (o.oid='".$search_order."' OR a.company LIKE '%".$search_order."%' OR o.order_date='".$search_order."' OR o.notes LIKE '%".$search_order."%' OR u.username LIKE '%".$search_order."%' OR u.name LIKE '%".$search_order."%')"; }								
		/* adding the search condition for Orders - stop */		
		
		/* adding the advertiser select condition - start */			
		if(isset($_POST['advertiser_id']))
			{
				$advertiser = intval($_POST['advertiser_id']);
				$_SESSION['advertiser_id'] = intval($_POST['advertiser_id']);
			}	
		elseif(isset($_SESSION['advertiser_id']))	
			$advertiser = $_SESSION['advertiser_id'];
		if(isset($advertiser) && $advertiser!='0'){
				$and_filter = $and_filter." AND a.aid = '".$advertiser."' "; }			
		/* adding the advertiser select condition - stop */
		
		/* adding the package select condition - start */			
		if(isset($_REQUEST['package_id']))
			{
				$package = intval($_REQUEST['package_id']);
				$_SESSION['package_id'] = intval($_REQUEST['package_id']);
			}	
		elseif(isset($_SESSION['package_id']))	
			$package = $_SESSION['package_id'];
			
		if(isset($package) && $package!='0'){
				$and_filter = $and_filter." AND ot.tid = '".$package."' "; }			
		/* adding the package select condition - stop */
		
		
		/* adding the payment method select condition - start */			
		if(isset($_POST['payment_method'])){
				$payment_method = trim($_POST['payment_method']);
				$_SESSION['payment_method'] = trim($_POST['payment_method']);
			}	
		else if(isset($_SESSION['payment_method']))	
			$payment_method = trim($_SESSION['payment_method']);
			
		if(isset($payment_method) && $payment_method!='' && $payment_method!='all methods' ){
			$and_filter = $and_filter." AND o.payment_type='".$payment_method."' "; 
		}			
		/* adding the payment method select condition - stop */
		
		/* adding the payment status select condition - start */			
		if(isset($_REQUEST['order_status'])){
				$order_status = $_REQUEST['order_status'];
				$_SESSION['order_status'] = $_REQUEST['order_status'];
			}	
		else if(isset($_SESSION['order_status']))	
			$order_status =  $_SESSION['order_status'];
		if(isset($order_status) && $order_status!=-1){
			if($order_status==0) $order_status="paid";
				else if($order_status==1) $order_status="not_paid";
			$and_filter = $and_filter." AND o.status='".$order_status."' "; 
		}			
		/* adding the payment status select condition - stop */
		
		if (empty ($this->_orders)) {
			$db = JFactory::getDBO();
			//$sql = "select * from #__ad_agency_order";
			$sql = "SELECT o.*, IF( o.payment_type = 'twocheckout', '2CO', o.payment_type ) AS alias, a.user_id, ot.description,u.username, u.name as company FROM #__ad_agency_order AS o LEFT JOIN #__ad_agency_advertis AS a ON a.aid=o.aid LEFT JOIN #__ad_agency_order_type AS ot ON ot.tid=o.tid LEFT JOIN #__users AS u ON u.id=a.user_id ".$and_filter." ORDER BY o.oid DESC";
			
			$limitstart=$this->getState('limitstart');
			$limit=$this->getState('limit');
				
			if($limit!=0){
				$limit_cond=" LIMIT ".$limitstart.",".$limit." ";
			} else {
				$limit_cond = NULL;
			}
			
			$this->_total = $this->_getListCount($sql);
			$this->_orders = $this->_getList($sql.$limit_cond);
		}
		return $this->_orders;
	}	

	function getOrder() {
		if (empty ($this->_order)) {
			$this->_order =& $this->getTable("adagencyOrder");
			$this->_order->load($this->_id);
		}
		return $this->_order;
	}

	function store(){
		$item = $this->getTable('adagencyOrder');
		$data = JRequest::get('post');
		
		if (!$item->bind($data)){
			return false;
		}

		if (!$item->check()) {
			return false;
		}

		if (!$item->store()) {
			return false;
 		}
		return true;

	}	
	
	function storenew(){
		$db = JFactory::getDBO();
		$item = $this->getTable('adagencyOrder');
		$data = JRequest::get('post');
		
		$sql = "select `currencydef` from #__ad_agency_settings";
		$db->setQuery($sql);
		$db->query();
		$currencydef = $db->loadColumn();
		$currencydef = trim($currencydef["0"]," ");
		$data["currency"] = $currencydef;
		
		$promo_code = $data["promocode"];
		if(isset($promo_code) && trim($promo_code) != ""){
			$sql = "select * from #__ad_agency_promocodes where `code`='".addslashes(trim($promo_code))."'";
			$db->setQuery($sql);
			$db->query();
			$promo_details = $db->loadAssocList();
			if($promo_details["0"]["amount"] > 0){
				$amount = trim($promo_details["0"]["amount"]);
				$promotype = trim($promo_details["0"]["promotype"]);
				if($promotype == 0){
					$data["cost"] = $data["cost"] - $amount;
					if($data["cost"] < 0){
						$data["cost"] = 0;
					}
				}
				else{
					$procent = ($data["cost"] * $amount)/100;
					$data["cost"] = $data["cost"] - $procent;
				}
				$data["promocodeid"] = $promo_details["0"]["id"];
			}
		}
		
		$data['pay_date'] = date("Y-m-d H:i:s", strtotime($data['pay_date']));
		
		if (!$item->bind($data)){
			return false;
		}

		$db = JFactory::getDBO();
		$sql = " SELECT * FROM #__ad_agency_order_type WHERE `tid` = '".intval($data['package_id'])."' ";
		$db->setQuery($sql);	
		$package = $db->loadObject();	
		
		$item->aid = $data['advertiser_id'];
		$item->tid = $data['package_id'];
		$item->order_date = $data['pay_date'];
		$item->payment_type = $data['payment_method'];
		$item->notes = $package->description;
		$item->status = 'paid';
		$item->type = $package->type;
		$item->quantity = $package->quantity;
		$item->cost = $data['cost'];

		if (!$item->check()) {
			return false;
		}
		
		if (!$item->store()) {
			return false;
		}
		return true;
	}		

	function formatime2($time,$option = 1){
		$date_time = explode(" ",$time);
		$date_time[0] = str_replace("/","-",$date_time[0]);
		$tdate = explode("-",$date_time[0]); 		
		if (($option == 1)||($option == 2)||($option == 7)||($option == 8)) { 
			$aux=$tdate[0];
			$tdate[0]=$tdate[2];
			$tdate[2]=$aux;
		}
		elseif (($option == 3)||($option == 4)||($option == 9)||($option == 10)) { 
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

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'array');
		$item = $this->getTable('adagencyOrder'); 
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}
		return true;
	}
	
	function remove () {
		$cids = JRequest::getVar('cid', array(0), 'get', 'array');
		$item = $this->getTable('adagencyOrder'); 
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
				$this->setError($item->getErrorMsg());
				return false;
			}
		}
		return true;
	}
	
	function confirm () {
		$db = JFactory::getDBO();
		$cids = JRequest::getVar('cid', array(0), 'get', 'array');
		$item = $this->getTable('adagencyOrder');
		foreach ($cids as $cid) {
			$sql = "update #__ad_agency_order set status='paid' where oid in ('".implode("','", $cids)."')";
			$db->setQuery($sql);
			if (!$db->query() ){
				$this->setError($db->getErrorMsg());
				return false;
			}
		}
		return true;
	}

	function saveorder($idArray = null, $lft_array = null){
		// Get an instance of the table object.
		$table = $this->getTable("adagencyOrder");

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