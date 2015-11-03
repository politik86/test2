<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
class socialadsModelManagezone extends JModelLegacy
{
 	var $_data;
 	var $_total = null;
 	var $_pagination = null;

	/* Constructor that retrieves the ID from the request*/
	function __construct()
	{
		parent::__construct();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
 		$option = $input->get('option','','STRING');
		// Get the pagination request variables
		$limit 				= $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart 		= $input->get('limitstart', 0,'INT');
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order','',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'desc',	'word' );
		$this->setState('filter_order', $filter_order); // Set the limit variable for query later on
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function _buildQuery()
	{
		// Get the WHERE and ORDER BY clauses for the query


		$query 	 = "SELECT * from #__ad_zone as a";
		return $query;
	}
	function deletezone($zoneid)
	{
		if(count($zoneid)>1)
		{
			$newzone=implode(',',$zoneid);
			$db=JFactory::getDBO();
			$query = "DELETE FROM #__ad_zone where id IN (".$newzone.")";
			$db->setQuery( $query );
			if (!$db->execute()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
			}
		}
		else
		{
			$db=JFactory::getDBO();
			$query = "DELETE FROM #__ad_zone where id=$zoneid[0]";
			$db->setQuery( $query );
			if (!$db->execute()) {
					$this->setError( $this->_db->getErrorMsg() );
					return false;
			}


		}
		return true;
	}
	function getZoneaddata()
	{
		$db=JFactory::getDBO();
		$input=JFactory::getApplication()->input;
		$selzoneid = $input->get('selzoneid',0,'INT');
		$query="SELECT id FROM #__ad_data LEFT JOIN #__ad_zone ON ad_zone=id WHERE id=$selzoneid";
		$db->setQuery($query);
		$createid=$db->loadresult();
		return $createid;
	}
	function getZoneaddatacount($selzoneid)
	{
		$db=JFactory::getDBO();
		$query="SELECT count(id) FROM #__ad_data AS d LEFT JOIN #__ad_zone AS z ON ad_zone=id WHERE id=$selzoneid AND d.ad_published=1 	";
		$db->setQuery($query);
		$createid=$db->loadresult();
		return $createid;
	}
	function getZoneamodule(){
		$db=JFactory::getDBO();
		$query="SELECT params FROM #__modules WHERE published = 1 AND module LIKE '%mod_socialads%'";
		$db->setQuery($query);
		$params=$db->loadObjectList();
		$module = array();

		foreach($params as $params)
		{
			$params1 = str_replace('"','',$params->params);
			if(JVERSION >= '1.6.0')
				$single= explode(",", $params1);
			else
				$single= explode("\n", $params1);
			foreach ($single as $single)
			{
				if(JVERSION >= '1.6.0')
					$name= explode(":", $single);
				else
					$name = explode("=", $single);

				if($name[0] == 'zone')
					$module[] = $name[1];
			}
		}
		return $module;
	}
	function _buildContentWhere()
	{
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$option = $input->get('option','','STRING');
		$db=JFactory::getDBO();
		$view = $input->get('view','','STRING');
		$search = $mainframe->getUserStateFromRequest($option.$view.'search', 'search', '', 'string');
		$search = JString::strtolower($search);
		$where = array();
		if(trim($search)!='')
		{
		$query="SELECT id FROM #__ad_zone WHERE zone_name LIKE '%".$search."%'";
		$db->setQuery($query);
		$createid=$db->LoadResult();

		if($createid){	$where[] = "zone_name LIKE '%".$search."%'";}
		return $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		}
		else
		return '';

	}

	function getManagezone()
	{
		$db=JFactory::getDBO();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
		$option = $input->get('option','','STRING');
		$query 		= $this->_buildQuery();
		$query 	   .= $this->_buildContentWhere();
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order','',	'cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'desc',	'word' );

		if ($filter_order) {
			$qry = "SHOW COLUMNS FROM #__ad_zone";
			$db->setQuery($qry);
		 	$exists = $db->loadobjectlist();
			foreach($exists as $key=>$value){
					$allowed_fields[]=$value->Field;
				}

			if(in_array($filter_order,$allowed_fields))
			$query 	   .= " ORDER BY $filter_order $filter_order_Dir";
		}

		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		return $this->_data;
  	}

  	function Editlist($zoneid)
	{

        	unset($this->_data);
			$query = "SELECT * from #__ad_zone where id=$zoneid";
			$this->_data = $this->_getList($query);
			return $this->_data;
  	}

   function getTotal()
   {
		// Lets load the content if it doesn’t already exist
		if (empty($this->_total))
		{
		$query = $this->_buildQuery();
		$this->_total = $this->_getListCount($query);
		}

		return $this->_total;
   }

  function getPagination()
  {

		if (empty($this->_pagination))
		{
		jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}


		return $this->_pagination;
  }
	function setItemState( $items, $state )
	{
		$db=JFactory::getDBO();

		if(is_array($items))
		{
			$row =& $this->getTable();

			foreach ($items as $id)
			{

				$db=JFactory::getDBO();
				$query = "UPDATE  #__ad_zone SET published=$state where id=".$id;
				$db->setQuery( $query );
				if (!$db->execute()) {
						$this->setError( $this->_db->getErrorMsg() );
						return false;
				}



			}
		}

		// clean cache


		return true;
	}
	/* function store ends*/
	function store()
	{
		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
		$data 				= JRequest::get( 'post' );
		$data['layout']		= str_replace(',','|', $data['layout']);

		if($data['add_type']==1)
		{
			$data['img_width']		= 0;
			$data['img_height']		= 0;
		}
		else if($data['add_type']==2)
		{
			$data['max_title']		= 0;
			$data['max_des']		= 0;
		}

		$db	= JFactory::getDBO();
		$row1 = new stdClass;
		$zone_id=$data['zone_id'];
		$row1->zone_name 	= $data['zone_name'];
		$row1->published 	= $data['published'];
		$row1->zone_type 	= $data['zone_type'];
		$row1->max_title 	= $data['max_title'];
		$row1->max_des 		= $data['max_des'];
		$row1->img_width 	= $data['img_width'];
		$row1->img_height 	= $data['img_height'];
		$row1->per_click 	= isset($data['per_click']) ?$data['per_click'] : $socialads_config['clicks_price'];
		$row1->per_imp		= isset($data['per_imp']) ?$data['per_imp'] : $socialads_config['impr_price'];
		$row1->per_day 		= isset($data['per_day']) ?$data['per_day'] : $socialads_config['date_price'];
		$row1->num_ads		= $data['num_ads'];
		$row1->layout 		= $data['layout'];

		if($data['affiliate']==1)
		{
			$row1->ad_type 		= "|".$data['add_type']."||affiliate|";
		}
		else
		{
			$row1->ad_type 		= "|".$data['add_type']."|";
		}


		if($zone_id)
		{

			$qry = "SELECT `id` FROM #__ad_zone WHERE `id` = '{$zone_id}'";
			$db->setQuery($qry);
		 	$exists = $db->loadResult();

			$row1->id = $zone_id;
			// Store the web link table to the database



					if($row1->ad_type=="text")
					{
						$row1->img_width 	= 0;
						$row1->img_height 	= 0;
					}
					else if($row1->ad_type=="img")
					{
						$row1->max_title 	= 0;
						$row1->max_des 		= 0;
					}

				if ($exists) {
					$db->updateObject('#__ad_zone', $row1, 'id');
				}

		}
	else{
			$db->insertObject('#__ad_zone', $row1, 'id');

		}
//die;
		return true;


	}//function store ends


}
