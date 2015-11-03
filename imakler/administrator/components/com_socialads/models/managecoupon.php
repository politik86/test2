<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();
jimport('joomla.application.component.model');
class socialadsModelManagecoupon extends JModelLegacy
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


		$query 	 = "SELECT * from #__ad_coupon as a";
		return $query;
	}
	function deletecoupon($zoneid)
	{

		if(count($zoneid)>1)
		{
			$newzone=implode(',',$zoneid);
			$db=JFactory::getDBO();
			 	$query = "DELETE FROM #__ad_coupon where id IN (".$newzone.")";
				$db->setQuery( $query );
		            if (!$db->execute()) {
		                    echo $this->setError( $this->_db->getErrorMsg() );
		                    return false;
		            }
		}
		else
		{
				$db=JFactory::getDBO();
				$query = "DELETE FROM #__ad_coupon where id=$zoneid[0]";
				$db->setQuery( $query );
		            if (!$db->execute()) {
		                    echo $this->setError( $this->_db->getErrorMsg() );
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
		$query="SELECT id FROM #__ad_data LEFT JOIN #__ad_coupon ON ad_coupon=id WHERE id=$selzoneid";
		$db->setQuery($query);
		$createid=$db->loadresult();
		return $createid;
	}
	function getZoneaddatacount($selzoneid)
	{
		$db=JFactory::getDBO();
		$query="SELECT count(id) FROM #__ad_data AS d LEFT JOIN #__ad_coupon AS z ON ad_coupon=id WHERE id=$selzoneid AND d.ad_published=1 	";
		$db->setQuery($query);
		$createid=$db->loadresult();
		return $createid;
	}
	function _buildContentWhere()
	{
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$db=JFactory::getDBO();

		$search = $mainframe->getUserStateFromRequest( $option.'search', 'search','', 'string' );
		$where = array();
		if(trim($search)!='')
		{
			/*$query="SELECT id FROM #__ad_coupon WHERE name LIKE '%".$search."%'";
			$db->setQuery($query);
			$createid=$db->LoadResult();
			*/
			//if($createid)
			{
				$where[] = "name LIKE '%".$search."%'";
			}
			return $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
		}
		else
			return '';

	}

	function getManagecoupon()
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

		if ($filter_order)
		{
			$qry = "SHOW COLUMNS FROM #__ad_coupon";
			$db->setQuery($qry);
		 	$exists = $db->loadobjectlist();

			foreach($exists as $key=>$value)
			{
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
			$query = "SELECT * from #__ad_coupon where id=$zoneid";
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
				$query = "UPDATE  #__ad_coupon SET published=$state where id=".$id;
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
		$data 				= JRequest::get( 'post' );

		$db	= JFactory::getDBO();
		$row1 = new stdClass;
		$coupon_id=$data['id1'];
		$row1->name 			= $data['coupon_name'];
		$row1->published 		= $data['published'];
		$row1->code 			= $db->escape(trim($data['code']));
		$row1->value 			= $data['value'];
		$row1->val_type 		= $data['val_type'];
		$row1->max_use	 		= $data['max_use'];
		$row1->max_per_user 	= $data['max_per_user'];
		$row1->description		= $data['description'];
		$row1->params 			= $data['params'];
		$row1->from_date		= $data['from_date'];
		$row1->exp_date 		= $data['exp_date'];


		if($coupon_id)
		{



			$qry = "SELECT `id` FROM #__ad_coupon WHERE `id` = '{$coupon_id}'";
			$db->setQuery($qry);
		 	$exists = $db->loadResult();

			// Store the web link table to the database


				if ($exists) {
					$row1->id = $coupon_id;

					$db->updateObject('#__ad_coupon', $row1, 'id');

				}

		}
	else{

			$db->insertObject('#__ad_coupon', $row1, 'id');

		}

		return true;


	}//function store ends

	function getcode($code)
	{
			$db	= JFactory::getDBO();

			$qry = "SELECT `id` FROM #__ad_coupon WHERE `code` = ".$db->quote($db->escape(trim($code))); //die('modek');
			$db->setQuery($qry);
		 	$exists = $db->loadResult();
		 	if($exists)
		 	return 1;
		 	else
		 	return 0;

	}


	function getselectcode($code,$id)
	{
			$db	= JFactory::getDBO();

			$qry = "SELECT `code` FROM #__ad_coupon WHERE id<>'{$id}' AND `code` = ".$db->quote($db->escape(trim($code)));
			$db->setQuery($qry);
		 	$exists = $db->loadResult();
		 	if($exists)
		 	return 1;
		 	else
		 	return 0;

	}


}
