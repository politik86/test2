<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');


class socialadsModelAdorders extends JModelLegacy
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
		// Get the pagination request variables
		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );
		$this->setState('limit', $limit); // Set the limit variable for query later on
		$this->setState('limitstart', $limitstart);
	}

	function _buildQuery()
	{
		$db=JFactory::getDBO();
		$input=JFactory::getApplication()->input;
		// Get the WHERE and ORDER BY clauses for the query
	  $where = $this->_buildContentWhere();

		$query = "SELECT d.ad_id, d.ad_title, d.ad_payment_type,d.ad_startdate, d.ad_enddate, i.processor,i.payee_id, i.ad_credits_qty, i.cdate, i.ad_amount, i.ad_coupon,i.status,i.id FROM #__ad_data AS d RIGHT JOIN #__ad_payment_info AS i ON d.ad_id = i.ad_id". $where;
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$option = $input->get('option','','STRING');
		$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order','ad_id','cmd' );
		$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'filter_order_Dir',	'filter_order_Dir',	'desc',	'word' );
		if ($filter_order) {

		$qry = "SHOW COLUMNS FROM #__ad_data";
		$db->setQuery($qry);
	 	$exists = $db->loadobjectlist();
		foreach($exists as $key=>$value){
				$allowed_fields[]=$value->Field;
			}
		$qry1 = "SHOW COLUMNS FROM #__ad_payment_info";
		$db->setQuery($qry1);
	 	$exists1 = $db->loadobjectlist();
		foreach($exists1 as $key1=>$value1){
				$allowed_fields[]=$value1->Field;
			}
				if(in_array($filter_order,$allowed_fields))
				{
					 $query 	   .= " ORDER BY i.$filter_order $filter_order_Dir";
				}
				else
				{
					$query 	   .= " ORDER BY d.ad_id DESC";
				}
		}
		return $query;
	}

function _buildContentWhere()
	{
		$input=JFactory::getApplication()->input;
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();

		$db=JFactory::getDBO();

		//For Filter Based on Text
		$filter_state = $mainframe->getUserStateFromRequest( $option.'search_list', 'search_list', '', 'string' );

		//For Filter Based on Status
		$search = $mainframe->getUserStateFromRequest( $option.'search_select', 'search_select','', 'string' );

		//For Filter Based on Payment status
		$search_pay = $mainframe->getUserStateFromRequest( $option.'search_pay', 'search_pay','', 'string' );

		//For Filter Based on Gateway
		$search_gateway ='';
		$search_gateway = $mainframe->getUserStateFromRequest( $option.'search_gateway', 'search_gateway', '', 'string' );
		$search_gateway = JString::strtolower( $search_gateway);


		//For Filter Based on Gateway


		$where = array();

		$query="SELECT id FROM #__users WHERE username='".$filter_state."'";
		$db->setQuery($query);
		$createid=$db->LoadResult();

		if($search_gateway)
			 $where[] = " (i.processor LIKE '".$search_gateway."')";

		if($createid){
			$where[] = 'ad_creator = '.$this->_db->Quote($createid);
		}
		else if(!$createid && $filter_state)
		{
			$where[] = ' LOWER(ad_title) LIKE '.$this->_db->Quote('%'.$filter_state.'%').'
			OR id = '.$this->_db->Quote($filter_state);
		}
		else if($search=='P' || $search=='C' || $search=='RF' || $search=='E'){

				$where[] = 'status LIKE '.$this->_db->Quote($search);
		}

		if($search_pay==2)
		{
				$where[] = ' i.ad_id != 0 ' ;
		}

		return $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
	}

	function getAdOrders()
	{

		if (empty($this->_data))
		{
		$query = $this->_buildQuery();
		//print_r($query);
		$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		}

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
		// Lets load the content if it doesn’t already exist
		if (empty($this->_pagination))
		{
		jimport('joomla.html.pagination');
		$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
  }

	/* function store ends*/
	function store()
	{

		$data = JRequest::get( 'post' );

		$id=$data['id'];
		$status=$data['status'];
		//require_once(JPATH_SITE.DS."components".DS."com_socialads".DS."helper.php");  // require when we call from backend
		$socialadshelper = new socialadshelper;

		if($status=='RF')
		{
			$query = "UPDATE #__ad_payment_info SET status ='RF' WHERE id =".$id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
				return 2;
			}
			$socialadshelper->new_pay_mail($id);
			return 3;
		}
		elseif($status=='E')
		{
			$query = "UPDATE #__ad_payment_info SET status ='E' WHERE id =".$id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
				return 2;
			}
			$socialadshelper->new_pay_mail($id);
			return 3;
		}
		elseif($status=='C')
		{
			$query = "SELECT * FROM #__ad_payment_info WHERE id =".$id;
			$this->_db->setQuery($query);
			$result = $this->_db->loadObject();

			$query = "UPDATE #__ad_payment_info SET status ='C' WHERE id =".$id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
				return 2;
			}

			//entry for transaction table
			$query = "SELECT ad_id FROM #__ad_payment_info WHERE id = ".$id;
			$this->_db->setQuery($query);
			$ad = $this->_db->loadresult();

			JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
			$socialadsModelpayment = new socialadsModelpayment();
			if(empty($ad))
			{
				// add wallet
				$comment = 'ADS_PAYMENT';
				$transc = $socialadsModelpayment->add_transc($result->ad_original_amt,$id,$comment);
				$sendmail=$socialadsModelpayment->SendOrderMAil($id,$data['search_gateway'],$payPerAd=0);
			}
			else
			{
				// pay per ad
				$sendmail=$socialadsModelpayment->SendOrderMAil($id,$data['search_gateway'],$payPerAd=1);
			}

			require_once(JPATH_SITE.'/components/com_socialads/helper.php');
			$adid=$result->ad_id;
			$qryad= "SELECT ad_payment_type FROM #__ad_data WHERE ad_id =".$adid;
			$this->_db->setQuery($qryad);
			$ad_payment_type=$this->_db->loadResult();

			if($ad_payment_type!=2)
			{
			$query = "UPDATE #__ad_data SET ad_credits = ad_credits + $result->ad_credits_qty, ad_credits_balance = ad_credits_balance + $result->ad_credits_qty WHERE ad_id=".$result->ad_id;
			$this->_db->setQuery($query);
			$this->_db->execute();
			}
			//added by sagar for date type ads




			if(empty($subscriptiondata[0]->subscription_id) and ($ad_payment_type==2))
			{
				socialadshelper::adddays($adid,$result->ad_credits_qty);
			}
			//added by sagar for date type ads
		}
		else
		{
			$query = "UPDATE #__ad_payment_info SET status ='P' WHERE id =".$id;
			$this->_db->setQuery($query);
			if(!$this->_db->execute())
			{
				return 2;
			}
		}
		return 1;
	}//function store ends

function gatewaylist()
	{
			$db = JFactory::getDBO();
			$query = "SELECT DISTINCT(`processor`) FROM #__ad_payment_info";
			$db->setQuery($query);
			$gatewaylist = $db->loadObjectList();
			if(!$gatewaylist)
			return 0;
			else
			return $gatewaylist;

	}

}
