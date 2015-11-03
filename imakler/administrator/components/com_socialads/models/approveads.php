<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport( 'joomla.application.component.view' );
jimport('joomla.filesystem.file');


class socialadsModelApproveads extends JModelLegacy
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
			$filter_order     = $mainframe->getUserStateFromRequest(  $option.'filter_order', 'filter_order', 'a.ad_id', 'cmd' );
			$filter_order_Dir = $mainframe->getUserStateFromRequest( $option.'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );
			$this->setState('filter_order', $filter_order);
			$this->setState('filter_order_Dir', $filter_order_Dir);


			// Get the pagination request variables
			$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
			$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

			$this->setState('limit', $limit);
			$this->setState('limitstart', $limitstart);

			$array = $input->get('cid',0,'array');
			$this->setId((int)$array[0]);
}

	function setId($id)
	{
				// Set id and wipe data
				$this->_id		= $id;
				$this->_data	= null;
	}
	function updatezone()
    {

    			$data = JRequest::get( 'post' );
				$id=$data['id'];
				$zone=$data['zone'];
				///////////////
				$query_lay	= "SELECT layout FROM #__ad_zone   where id=".$zone;
				$this->_db->setQuery($query_lay);
				$layout 	= $this->_db->loadresult();
				$layout1	= explode('|',$layout);

				$query = "UPDATE #__ad_data SET ad_zone =".$zone." ,layout='{$layout1['0']}' WHERE ad_id =".$id;

				$this->_db->setQuery($query);
    			$this->_db->execute();
    			return true;
    }

    function adzonename($ad_id,$zone_id)
    {
    			$db = JFactory::getDBO();
				$query = "SELECT zone_name FROM #__ad_zone as z, #__ad_data as d
								 WHERE z.id=d.ad_zone AND z.id=".$zone_id.
								 " AND d.ad_id=".$ad_id;

				$db->setQuery($query);
				$zone_name = $db->loadresult();
				return $zone_name;

    }
    function adzonelist()
    {

		$db = JFactory::getDBO();
		/*$query = "SELECT id,zone_name FROM #__ad_zone as z, #__ad_data as d
						 WHERE z.id=d.ad_zone";*/
		$query = "SELECT id,zone_name FROM #__ad_zone WHERE published=1";
		$db->setQuery($query);
		$zone_list = $db->loadObjectList();
		return $zone_list;

    }

	function store()
	{
				$data = JRequest::get( 'post' );
				$input=JFactory::getApplication()->input;
				$id=$data['id'];
				$status=$data['status'];
				$query = "UPDATE #__ad_data SET ad_approved =".$status." WHERE ad_id =".$id;
				$this->_db->setQuery($query);
				if($this->_db->execute())
				{
					jimport( 'joomla.utilities.utility' );

					$this->_db->setQuery("SELECT a.ad_creator, a.ad_title, a.ad_url2, u.name, u.email
											FROM #__ad_data AS a, #__users AS u
											WHERE a.ad_id=$id AND a.ad_creator=u.id");
					$result	= $this->_db->loadObject();

					global $mainframe;
					$mainframe = JFactory::getApplication();
					if($status==1)
					{
						$body	= JText::_('APPROVED');
						$subject = JText::_('APPROVEDAD');
					}
					else if($status==2)
					{
						$body	= JText::_('REJECTED_MAIL');
						$subject = JText::_('REJECTAD');
						$body	= str_replace('[REASON]', $input->get('reason','','STRING'), $body);
					}

					$body	= str_replace('[NAME]', $result->name, $body);
		  		$ad_title=($result->ad_title!= '') ? JText::_("PERIDIC_STATS_ADTIT").' <b>"'.$result->ad_title.'"</b>' : JText::_("PERIDIC_STATS_ADID").' : <b>'.$id.'</b>';
					$body	= str_replace('[ADTITLE]', $ad_title, $body);
					$body	= str_replace('[SITE]', JUri::root(), $body);
					$body	= str_replace('[SITENAME]', $mainframe->getCfg('sitename'), $body);

					$from = $mainframe->getCfg('mailfrom');
					$fromname = $mainframe->getCfg('fromname');
				  $recipient[] = $result->email;
				  $body = nl2br($body);
					$mode = 1;
					$cc = null;
					$bcc = null;
					$bcc = null;
					$attachment = null;
					$replyto = null;
					$replytoname = null;

					JFactory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
				}

				return true;
	}
    function getApproveAds()
	{
		$db=JFactory::getDBO();
		global $mainframe, $option;
		$mainframe = JFactory::getApplication();
		$input=JFactory::getApplication()->input;
 		$option = $input->get('option','','STRING');
 		$where = '';
		if (empty($this->_data))
		{
			$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order','a.ad_id',	'cmd' );
			$filter_order_Dir	= $mainframe->getUserStateFromRequest($option.'filter_order_Dir',	'filter_order_Dir',	'desc',	'word' );
			$query = $this->_buildQuery();
			$query = $query. ' ' . $where ;
			if ($filter_order)
			{
				$qry = "SHOW COLUMNS FROM #__ad_data";
				$db->setQuery($qry);
				$exists = $db->loadobjectlist();

				foreach($exists as $key=>$value){
					$allowed_fields[]='a.'.$value->Field;

				}

				if(in_array($filter_order,$allowed_fields))
				{
					 $query 	   .= " ORDER BY $filter_order $filter_order_Dir";
				}
			}
			else
			{
				$query 	   .= " ORDER BY a.`ad_id` DESC";
			}
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		return $this->_data;
  }



  function _buildQuery()
	{
		global $mainframe, $option,$approved;
		$mainframe = JFactory::getApplication();
		// Get the WHERE and ORDER BY clauses for the query
		$search = $mainframe->getUserStateFromRequest( $option.'search', 'search', '', 'string' );
		$search = JString::strtolower( $search );
		if($search=='3')
		{
			$db = JFactory::getDBO();
			$query = "SELECT id FROM #__ad_zone as z, #__ad_data as d
					 WHERE z.id=d.ad_zone group by id";
			$db->setQuery($query);
			$zone_list = $db->loadColumn();
			$where="";
			if($zone_list)
			{
				$zonelist1="'".implode("','",$zone_list)."'"			;
				$where="WHERE ad_zone NOT IN($zonelist1)";
			}
			$query = "SELECT a.*,c.campaign FROM #__ad_data as a LEFT JOIN #__ad_campaign as c ON a.camp_id = c.camp_id ". $where;
			return $query;
		}
		$where = $this->_buildContentWhere();
		$query = "SELECT a.*,c.campaign FROM #__ad_data as a LEFT JOIN #__ad_campaign as c ON a.camp_id = c.camp_id ". $where;


		return $query;
	}

	function _buildContentWhere()
	{
				global $mainframe, $option,$approved;
				$mainframe = JFactory::getApplication();
				$input=JFactory::getApplication()->input;
 				$option = $input->get('option','','STRING');
				$db=JFactory::getDBO();

				$filter_state = $mainframe->getUserStateFromRequest( $option.'filter_state', 'filter_state', '', 'string' );
				$search = $mainframe->getUserStateFromRequest( $option.'search', 'search', '', 'string' );
				$search = JString::strtolower( $search );
				//For Filter Based on Zone
				$search_zone = $mainframe->getUserStateFromRequest( $option.'search_zone', 'search_zone', '', 'string' );
				$search_zone = JString::strtolower( $search_zone);
				//For Filter Based on Zone

				$search_camp = $mainframe->getUserStateFromRequest( $option.'search_camp', 'search_camp', '', 'string' );


				$where = array();

				$query="SELECT id FROM #__users WHERE name='".$filter_state."'";
				$db->setQuery($query);
				$createid=$db->LoadResult();
				if($createid){
					$where[] = 'a.ad_creator = '.$this->_db->Quote($createid);
				}
				if(!$createid && $filter_state){
					$where[] = 'LOWER(a.ad_title) LIKE '.$this->_db->Quote('%'.$filter_state.'%').'
										OR a.ad_id = '.$this->_db->Quote($filter_state);
				}
				if($search_camp){
					$where[] = '  c.campaign="'.$search_camp.'"';
				}

				if($search_zone!=0 && $search!=-1 ){
					if($search !='')
						$where[] = ' (a.ad_approved = '.$this->_db->Quote($search).' AND ad_zone='.$search_zone .')';
					else
						$where[] = '  ad_zone='.$search_zone ;
				}
				else if($search=='0' || $search=='1' || $search=='2'){
					$where[] = ' (a.ad_approved = '.$this->_db->Quote($search) .')';
				}
				else if($search_zone!=0){
					$where[] = ' a.ad_zone = '.$this->_db->Quote($search_zone);
				}
				return $where = ( count( $where ) ? ' WHERE '. implode( ' AND ', $where ) : '' );
	}



  function getTotal()
  {
				global $mainframe, $option,$approved;
				$mainframe = JFactory::getApplication();
				$input=JFactory::getApplication()->input;
 				$option = $input->get('option','','STRING');
				$db = JFactory::getDBO();
				if (empty($this->_total))
				{
					$query = $this->_buildQuery();
					$filter_order		= $mainframe->getUserStateFromRequest( $option.'filter_order', 'filter_order','ad_id',	'cmd' );
					$filter_order_Dir	= $mainframe->getUserStateFromRequest( $option.'filter_order_Dir',	'filter_order_Dir',	'desc',	'word' );
					if ($filter_order)
					{
						$qry = "SHOW COLUMNS FROM #__ad_data";
						$db->setQuery($qry);
						$exists = $db->loadobjectlist();
						foreach($exists as $key=>$value)
						{
							$allowed_fields[]=$value->Field;
						}

						if(in_array($filter_order,$allowed_fields))
						{
							$query 	   .= " ORDER BY $filter_order $filter_order_Dir";
						}
						else
						{
							$query 	   .= " ORDER BY a.`ad_id` DESC";
						}
					}
					else
					{
						$query 	   .= " ORDER BY a.`ad_id` DESC";
					}
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
				//print_r($this->_pagination);
				return $this->_pagination;
  }

	/*count number of clicks == 1, impressions == 0*/
	function getAdtype($ad_id, $ad_type)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('COUNT(s.`display_type`) ')
		->from('`#__ad_stats` AS s')
		->where( ' s.`ad_id` ='.(int)$ad_id)
		->where( ' s.`display_type` ='.(int)$ad_type);
		$db->setQuery($query);
		$adtype = $db->loadresult();

		$query = $db->getQuery(true);
		if($ad_type === 1)
			$query->select('SUM(  a.`click` ) ');
		else
			$query->select('SUM(  a.`impression` ) ');
		$query->from('`#__ad_archive_stats` AS a')
		->where( 'a.`ad_id` ='.(int)$ad_id);
		$db->setQuery($query);
				$archive = $db->loadresult();
				return ($archive + $adtype);

/* //for Task #31607 increment ad stats in independent column against the ad
		if($ad_type === 1)
			$query->select('a.`ad_clicks`');
		else
			$query->select('a.`ad_impressions`');
		$query->from('`#__ad_data` AS a')
		->where( 'a.`ad_id` ='.(int)$ad_id);
		$db->setQuery($query);
		$count = $db->loadresult();
		return $count;
*/
	}

  function getIgnorecount($ad_id)
  {
  			$db = JFactory::getDBO();
  			$query = "SELECT COUNT(adid) FROM #__ad_ignore
  								WHERE adid=".$ad_id;
  			$db->setQuery($query);
				$ignorecount = $db->loadresult();

				return $ignorecount;
  }

	function deleteads($adid)
	{
		$db=JFactory::getDBO();
		$img_list=array();

		if(count($adid)>1)
		{
			$adid_str=implode(',',$adid);
			$query1 = "SELECT ad_image FROM #__ad_data WHERE ad_image<>'' AND "." ad_id IN (".$adid_str.")";
  			$db->setQuery($query1);
			$img_list = $db->loadObjectList();

		 	$query = "DELETE FROM #__ad_data where ad_id IN (".$adid_str.")";
			$db->setQuery( $query );
	            if (!$db->execute()) {
	                    $this->setError( $this->_db->getErrorMsg() );
	                    return false;
	            }

			$query ='';
			if(!$db->stderr){
				$query = "SHOW TABLES LIKE '#__ad_fields';";
				$db->setQuery($query);
				$fields = $db->loadResult();
				if($fields)
				{
					//delete social targeting of ads
					$query = "DELETE FROM #__ad_fields WHERE adfield_ad_id IN(".$adid_str.")";
					$db->setQuery($query);
					$db->execute();
				}
				//delete geo targeting of ads
				$query = "DELETE FROM #__ad_geo_target WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete stats of ads
				$query = "DELETE FROM #__ad_stats WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete ignores of ads
				$query = "DELETE FROM #__ad_ignore WHERE adid IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
				//delete payments of ad
				$query = "DELETE FROM #__ad_payment_info WHERE ad_id IN(".$adid_str.")";
				$db->setQuery($query);
				$db->execute();
			}
		}
		else
		{
			$query1 = "SELECT ad_image FROM #__ad_data WHERE ad_image<>'' AND  ad_id=".$adid[0];
			$db->setQuery($query1);
			$img_list = $db->loadObjectList();

			$query = "DELETE FROM #__ad_data where ad_id=$adid[0]";
			$db->setQuery( $query );
	            if (!$db->execute()) {
	                    $this->setError( $this->_db->getErrorMsg() );
	                    return false;
	            }

			$query ='';
			if(!$db->getErrorMsg()){
				$query = "SHOW TABLES LIKE '#__ad_fields';";
				$db->setQuery($query);
				$fields = $db->loadResult();
				if($fields)
				{
					//delete social targeting of ad
					$query = "DELETE FROM #__ad_fields WHERE adfield_ad_id=$adid[0]";
					$db->setQuery($query);
					$db->execute();
				}
				//delete geo targeting of ad
				$query = "DELETE FROM #__ad_geo_target WHERE ad_id=$adid[0]";
				$db->setQuery($query);
				$db->execute();
				//delete stats of ad
				$query = "DELETE FROM #__ad_stats WHERE ad_id=$adid[0]";
				$db->setQuery($query);
				$db->execute();
				//delete ignores of ads
				$query = "DELETE FROM #__ad_ignore WHERE adid=$adid[0]";
				$db->setQuery($query);
				$db->execute();
				//delete payments of ad
				$query = "DELETE FROM #__ad_payment_info WHERE ad_id=$adid[0]";
				$db->setQuery($query);
				$db->execute();
			}
		}

		if(!$img_list)
			return true;

		$count=0;
		foreach($img_list as $img_to_del)
		{
					$img_to_del=JPATH_SITE .DS.$img_to_del->ad_image;
					if($img_to_del){
						if(!JFile::delete($img_to_del))
						{
							echo JText::_('SA_IMG_DEL_SUC')."[".$img_to_del."]";
							echo "<br>";
						}
						else
						{
							$count++;
							echo "<br>";
							echo JText::_('SA_IMG_DEL_FAIL')."[".$img_to_del."]";
						}
					}
				echo "<br>";
				echo  JText::_('SA_IMG_DEL_COUNT')." ".$count;
		}

		 return true;
	}
	function getcampaign_dd(){

			$user=JFactory::getUser();
			$userid=$user->id;
			$query = "SELECT camp_id,campaign FROM #__ad_campaign ";
			$this->_db->setQuery($query);

			return $this->_db->loadobjectList();

		}

} // end class
