<?php
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.html.parameter' );
jimport('joomla.filesystem.file');

class remoteAdRetriever extends adRetriever
{
	function __construct($userid=0,$extra=0)
	{
		//$this->_my = ($user == 0) ? (JFactory::getUser()) : (JFactory::getUser($user));
		if($userid==0)
			$this->_my = JFactory::getUser();
		else if($userid== -1)
			$this->_my->id = 0;
		else if($userid== 1){
			$this->_my = new stdClass;
			$this->_my->id = 1;
		}
		$this->_fromemail =  $extra;
	}
	function get($paramindex,$default)
	{
		$session = JFactory::getSession();
		$userData =  $session->get('userData',array());
		if(empty($userData['ads_params'][$paramindex]))
			return $default;
		else
			return $userData['ads_params'][$paramindex];
	}
	function getParam($params,$paramindex)
	{
		return $params['ads_params'][$paramindex];
	}
	function getCBData(){
		return $this->getSocial_params();
	}
	function getJSData(){
		return $this->getSocial_params();
	}
	function getESData(){
		return $this->getSocial_params();
	}
	function getSocial_params(){
		$db = JFactory::getDBO();
		$query = "SELECT * FROM #__ad_fields_mapping ORDER BY mapping_id";
		$db->setQuery($query);
		$mapdata = $db->loadObjectlist();
		//dont go inside if mapdata is empty
		if(!empty($mapdata)){
			$i=0;
			$session = JFactory::getSession();
			$userData =  $session->get('userData',array());
			$col_value =  $userData['social_params'];

			$result = array();
			foreach($mapdata as $key=>$map)
			{
				if(!empty($col_value[$map->mapping_fieldname])){
					// get the field values of the above mapping field names
					$str=$map->mapping_fieldname;
					$result[$i] = new stdClass;
					$result[$i]->value = $col_value[$map->mapping_fieldname];
					$result[$i]->mapping_fieldtype = $map->mapping_fieldtype;
					$result[$i]->mapping_fieldname = $map->mapping_fieldname;
					$result[$i]->mapping_match = $map->mapping_match;
					$i++;
				}
			}
			return $result;
		}
	}
	function getContextData($params,$adRetriever)
	{
		$session = JFactory::getSession();
		$userData =  $session->get('userData',array());

		if(!empty($userData['context_params']['keys'])){
			$keywords=explode( ",", $userData['context_params']['keys']);
			return $keywords;
		}
		return	array();
	}
}
