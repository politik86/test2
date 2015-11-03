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

jimport('joomla.utilities.date');

class adagencyAdminModeladagencyPromocodes extends JModelLegacy {

	protected $_context = 'com_adagency.adagencyPromocodes';
	var $_valid_promos;
	var $_promos;
	var $_promo;
	var $_id = null;
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

    function getPagination(){		$this->_total = null;
		// Lets load the content if it doesn't already exist
		if (empty($this->_pagination))	{
			jimport('joomla.html.pagination');
			if (!$this->_total) $this->getlistPromos();
			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );
		}
		return $this->_pagination;
	}

	function setId($id) {
		$this->_id = $id;
		$this->_promo = null;
	}

	protected function getListQuery(){
        $db = JFactory::getDBO();
		$session = JFactory::getSession();
		$promosearch = JRequest::getVar("promosearch", "");
		$and = " where 1=1 ";
		if(trim($promosearch) != ""){
			$and .= " and (title like '%".trim($promosearch)."%' or code like '%".trim($promosearch)."%') ";
		}
		
		$active_promocodes = JRequest::getVar("active_promocodes", "0");
		if($active_promocodes == 1){
			$today =  time();
			$and .= " and (`codestart`<='".$today."' and (`codeend`>='".$today."' OR `codeend` = 0)) and (`codelimit` <> `used` OR `codelimit` = 0)";
		}
		
		$query = "select * from #__ad_agency_promocodes ".$and." order by ordering asc";
		return $query;
	}
	
	function getItems(){
		$config = new JConfig();	
		$app = JFactory::getApplication('administrator');
		$limistart = $app->getUserStateFromRequest($this->_context.'.list.start', 'limitstart');
		$limit = $app->getUserStateFromRequest($this->_context.'.list.limit', 'limit', $config->list_limit);
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query = $this->getListQuery();
		
		$db->setQuery($query);
		$db->query();
		$result	= $db->loadObjectList();
		$this->total=count($result);
		$db->setQuery($query, $limistart, $limit);
		$db->query();
		$result	= $db->loadObjectList();
		return $result;
	}

	function getlistPromos () {
		if(empty ($this->_promos)){
			$promosearch = JRequest::getVar("promosearch", "");
			$and = "";
			if(trim($promosearch) != ""){
				$and .= " where (title like '%".trim($promosearch)."%' or code like '%".trim($promosearch)."%') ";
			}
		
			$sql = "select * from #__ad_agency_promocodes ".$and." order by id desc";
			$this->_total = $this->_getListCount($sql);
			if ($this->getState('limitstart') > $this->_total) $this->setState('limitstart', 0);
			if ($this->getState('limitstart') > 0 && $this->getState('limit') == 0)  $this->setState('limitstart', 0);			
			$this->_promos = $this->_getList($sql);
		}
		return $this->_promos;
	}
	
	function getlistPromosValid () {
		if (empty ($this->_valid_promos)) {
		
			$sql = "select * from #__ad_agency_promocodes order by id desc";			
			$this->_db->setQuery( $sql ); 			
			$promos = $this->_db->loadObjectList();
			
			$nullDate = 0;
			
			$promos_valid = array();
			
			foreach($promos as $promo) {
			
				$published = $promo->published;
				$timestart = $promo->codestart;
				$timeend = $promo->codeend;
				$limit = $promo->codelimit;
				$used = $promo->used;			
				$now = time();				
				
				$promo_status = true;
				
				if ( $now <= $timestart && $published == "1") {
					$promo_status = true;
				} else if ($limit > 0 && $used >= $limit) {
					$promo_status = true;
				} else if ( ( $now <= $timeend || $timeend == $nullDate ) && $published == "1" ) {
					$promo_status = true;
				} else if ( $now > $timeend && $published == "1" && $timeend != $nullDate) {
					$promo_status = true;
				} elseif ( $published == "0" ) {
					$promo_status = false;
				} else {
					$promo_status = false;
				}
				
				if ($promo_status)
					$this->_valid_promos[] = $promo;			
			}									
		}
		
		return $this->_valid_promos;
	}

	function getPromo() {
		if (empty ($this->_promo)) {
			$this->_promo = $this->getTable("adagencyPromocodes");
			$this->_promo->load($this->_id);
		}
		return $this->_promo;
	}

	function store () {
		$item = $this->getTable('adagencyPromocodes');
		$data = JRequest::get('post');
		
		$data["codestart"] = strtotime($data["codestart"]);
		$data["codeend"] = strtotime($data["codeend"]);
		
		if(!$item->bind($data)){
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->check()) {
			$this->setError($item->getErrorMsg());
			return false;
		}

		if (!$item->store()) {
			return false;
		}
		
		return true;
	}

	function delete () {
		$cids = JRequest::getVar('cid', array(0), 'post', 'array');
		$item = $this->getTable('adagencyPromocodes');
		foreach ($cids as $cid) {
			if (!$item->delete($cid)) {
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
		$item = $this->getTable('adagencyPromocodes');
		$res = 0;
		if ($task == 'publish'){
			$res = 1;
			$sql = "update #__ad_agency_promocodes set published='1' where id in ('".implode("','", $cids)."')";
		} else {
			$res = -1;
			$sql = "update #__ad_agency_promocodes set published='0' where id in ('".implode("','", $cids)."')";
		}
		$db->setQuery($sql);
		if (!$db->query() ){
			$this->setError($db->getErrorMsg());
		}
		return $res;
	}
	
	function saveorder($idArray = null, $lft_array = null){
		// Get an instance of the table object.
		$table = $this->getTable("adagencyPromocodes");

		if(!$table->saveorder($idArray, $lft_array)){
			$this->setError($table->getError());
			return false;
		}
		// Clean the cache
		$this->cleanCache();
		return true;
	}

}

?>