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

function AdagencyBuildRoute(&$query) {
	$segments = array();
	
	
	if(isset($query['view'])){
	  $segments[] = $query['view'];
	  $controller = $query['view'];
	  unset($query['view']);
	 }
	 elseif(isset($query['controller'])){
	  $segments[] = $query['controller'];
	  $controller = $query['controller'];
	  unset($query['controller']);
	 }
 
 
	if (isset($query['controller'])) {
		$segments[] = $query['controller'];
		unset($query['controller']);
	} 
    if (isset($query['task'])) {
		$segments[] = $query['task'];
        unset($query['task']);
	} 
    if (isset($query['cid'])) {
		if (is_array($query['cid'])) {
			$segments[] = $query['cid'][0];
		} else {
			$segments[] = $query['cid'];
		}
		unset($query['cid']);
	} 
	if (isset($query['aid'])) {
		if (is_array($query['aid'])) {
			$segments[] = $query['aid'][0];
		} else {
			$segments[] = $query['aid'];
		}
		unset($query['aid']);
	} 
    if (isset($query['bid'])) {
		if (is_array($query['bid'])) {
			$segments[] = $query['bid'][0];
		} else {
			$segments[] = $query['bid'];
		}
		unset($query['bid']);
	} 

    if (isset($query['tid'])) {
		if (is_array($query['tid'])) {
			$segments[] = $query['tid'][0];
		} else {
			$segments[] = $query['tid'];
		}
		unset($query['tid']);
	} 
	if (isset($query['tmpl'])) {
		if (is_array($query['tmpl'])) {
			$segments[] = $query['tmpl'][0];
		} else {
			$segments[] = $query['tmpl'];
		}
		unset($query['tmpl']);
    } 	
    if (isset($query['adid'])) {
		if (is_array($query['adid'])) {
			$segments[] = $query['adid'][0];
		} else {
			$segments[] = $query['adid'];
		}
		unset($query['adid']);
	} 	

	return $segments;
}

function AdagencyParseRoute($segments) {

	$vars = array();
	$vars['controller'] = isset($segments[0])?$segments[0]:null;
	$vars['task'] = isset($segments[1])?$segments[1]:null;
	
	if( isset($segments[5])){
		$vars['tid'] = $segments[5];
	}
	else{ 
		if($vars['task']=='order'){
			$vars['tid'] = isset($segments[2]) ? $segments[2] : null;	
		}
	}
	
	//task preview adds
	if ($vars['controller']=='adagencyAds'){
		if($vars['task']=='preview') {
			$vars['tmpl'] = isset($segments[2])?$segments[2]:null;
			$vars['adid'] = isset($segments[3])?$segments[3]:null;	
		}
		else if($vars['task']=='click') {
			$vars['cid'] = isset($segments[2])?$segments[2]:null;
            $vars['aid'] = isset($segments[3])?$segments[3]:null;
			$vars['bid'] = isset($segments[4])?$segments[4]:null;			
		}
	}
	
	//task preview packages
	if($vars['controller']=="adagencyPackages"){
        if($vars['task'] == 'packs') {
            $vars['tmpl'] = isset($segments[2])?$segments[2]:null;
        } else {
            $vars['cid'] = isset($segments[2])?$segments[2]:null;
        }
	}
	if($vars['controller']=='adagencyCampaigns'){
		$vars['cid'] = isset($segments[2])?$segments[2]:null;
	}
	
	if(($vars['controller']=='adagencyStandard' || $vars['controller']=='adagencyAdcode' 
        || $vars['controller']=='adagencyPopup' || $vars['controller']=='adagencyFlash' 
        || $vars['controller']=='adagencyTextlink' || $vars['controller']=='adagencyTransition' 
        || $vars['controller']=='adagencyFloating' || $vars['controller']=='adagencyJomsocial') && $vars['task']=='edit' )
    {
		$vars['cid'] = isset($segments[2])?$segments[2]:null;
	}
	
	if($vars['controller']=='adagencyAdvertisers') {
		$vars['cid'] = isset($segments[2]) ? $segments[2] : null;
	}
	
	if($vars['controller']=='adagencyOrders') {
		if(count($segments) == 3){
			$vars['task'] = $segments["1"];
			$vars['tid'] = $segments["2"];
		}
	}
	
	$itemid = JRequest::getVar("Itemid", "0");
	if(intval($itemid) != 0){
		$vars["Itemid"] = intval($itemid);
	}
	
	return $vars;
}
?>