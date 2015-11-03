<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport('joomla.application.component.model');
jimport( 'joomla.application.component.view' );


class socialadsModelIgnoreads extends JModelLegacy
{
  var $_data;
  var $_total = null;
 	var $_pagination = null;
 	
  function __construct()
  {
        parent::__construct();
 
        global $mainframe, $option;
 				$mainframe = JFactory::getApplication();
 				$input=JFactory::getApplication()->input;
 				$option = $input->get('option','','STRING');
        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $input->get('limitstart',0,'INT');
 
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
 
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
  }
	
  function getData() 
  {
        // if data hasn't already been obtained, load it
        if (empty($this->_data)) {
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit')); 
        }
        return $this->_data;
  }

  function _buildQuery()
	{
		$input=JFactory::getApplication()->input;
		$adid 		= $input->get('adid',0,'INT'); 
		$freetxt 	= $input->get('ignore_search');
		
		if($adid)
			$where[] = "i.adid = $adid ";
		if($freetxt)
			$where[] = "(u.name LIKE '%$freetxt%' OR i.ad_feedback LIKE '%$freetxt%')";
	
		// Build the where clause for events query
		$where = (count($where) ? ' WHERE '.implode(' AND ', $where) : '');
		
		$query = "SELECT u.name, i.* 
							FROM #__ad_ignore AS i
							LEFT JOIN #__users AS u ON u.id = i.userid 
							$where";
							
		return $query;
	}
        
  function getTotal()
  {
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);    
        }
        return $this->_total;
  }


  function getPagination()
  {
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
  }

} // end class
