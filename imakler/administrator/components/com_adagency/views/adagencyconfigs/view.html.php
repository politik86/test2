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

jimport ("joomla.application.component.view");
require_once('components/com_adagency/helpers/legacy.php');

class adagencyAdminViewadagencyConfigs extends JViewLegacy {
	function display ($tpl =  null ) {
		
		$db =  JFactory::getDBO();
		$configs = $this->get('Configs');
		$configs->params = @unserialize($configs->params);
		$configs->payment = @unserialize($configs->payment);
		require_once( JPATH_COMPONENT.DS.'helpers'.DS.'sajax.php' );
		JToolBarHelper::title(JText::_('Settings'));
		
		$task2 = JRequest::getVar("task2", "");
		if($task2 != "" && $task2 != "content" && $task2 != "payments" && $task2 != "email" && $task2 != "overview" && $task2 != "registration" && $task2 != "approvals" && $task2 != "jomsocial"){
			JToolBarHelper::apply('apply', 'Apply');
			JToolBarHelper::save();
		}
		JToolBarHelper::cancel ('cancel', 'Cancel');

		global $mainframe;
		$agmailfrom =  $mainframe->getCfg( 'mailfrom' );	
		$agfromname =  $mainframe->getCfg( 'fromname' );	
		$agfromemail =  $mainframe->getCfg( 'fromemail' );

        $arr[] = JHTML::_('select.option', 999, JText::_('ADAG_UNLIMITED'));
        for ($i=1; $i <= 100; $i++) {
            $arr[] = JHTML::_('select.option',  $i, $i );
        }

        isset($configs->params['adslim']) ? $selected = $configs->params['adslim'] : $selected = NULL;
        $adslim = JHTML::_('select.genericlist',   $arr, 'params[adslim]', '', 'value', 'text', $selected );       
        
		if (isset($configs->show)){
			$show=explode(";",$configs->show);
		} else {$show = NULL;}
		
		if (isset($configs->mandatory)){
			$mandatory=explode(";",$configs->mandatory);
		} else {$mandatory = NULL;}
		
		if(count($show)>=2){ unset($show[count($show)-1]);}
		if(count($mandatory)>=2){ unset($mandatory[count($mandatory)-1]);}
		
		$configs->show = $show;
		$configs->mandatory = $mandatory;

	
		if(!isset($configs->params['click_limit'])){$configs->params['click_limit'] = "10";}; 
		if(!isset($configs->params['jquery_front'])){$configs->params['jquery_front'] = "0";};
		if(!isset($configs->params['jquery_back'])){$configs->params['jquery_back'] = "0";};

		$this->assign("configs", $configs);		
		$this->assign("agmailfrom", $agmailfrom);
		$this->assign("agfromname", $agfromname);
		$this->assign("agfromemail", $agfromemail);

		$data = JRequest::get('get');
		if(isset($data['task2'])) {
			if($data['task2']=='general') { $startOffset=0; }
			elseif ($data['task2']=='payments') { $startOffset=1; }
			elseif ($data['task2']=='email') { $startOffset=2; }
			elseif ($data['task2']=='content') { $startOffset=3; }
			elseif ($data['task2']=='overview') { $startOffset=4; }
			elseif ($data['task2']=='registration') { $startOffset=5;}
			elseif ($data['task2']=='approvals') { $startOffset=6; }
			elseif ($data['task2']=='jomsocial') { $startOffset=7; }
		} else {
			$startOffset=0; 
		}
		
		$this->assign("startOffset", $startOffset);	

		$agmailfrom=str_replace("'","''",$agmailfrom);
		$agfromname=str_replace("'","''",$agfromname);
		$agfromemail=str_replace("'","''",$agfromemail);

		if ($configs->adminemail=="") { 
			$query="UPDATE #__ad_agency_settings SET `adminemail` = '".$agmailfrom."'";
			$db->setQuery($query);
			if(!$db->query()) {die($db->stderr());}
		}
		if ($configs->fromemail=="") {
			$query="UPDATE #__ad_agency_settings SET `fromemail` = '".$agmailfrom."'";
			$db->setQuery($query);
			if(!$db->query()) {die($db->stderr());}
		}
		if ($configs->fromname=="") {  
			$query="UPDATE #__ad_agency_settings SET `fromname` = '".$agfromname."'";
			$db->setQuery($query);
			if(!$db->query()) {die($db->stderr());}
		} 
		
		$respathfe = JPATH_ROOT.DS."language".DS."en-GB".DS."en-GB.com_adagency.ini";
		$respathbe = JPATH_ROOT.DS."administrator".DS."language".DS."en-GB".DS."en-GB.com_adagency.ini";
		
		$langfe = implode ("", file ($respathfe));
		$langbe = implode ("", file ($respathbe));
		
		$this->assign("langfe", $langfe);
		$this->assign("langbe", $langbe);
		
		
	$configs = $this->_models['adagencyconfig']->getConfigs();
	//var_dump($configs);die();
	$database =  JFactory::getDBO();
	
	$query = "select distinct (currency_name), currency_full from #__ad_agency_currencies";
    $database->setQuery($query);
    $currs = $database->loadObjectList();
	
	$query = "select distinct (plugname) from #__ad_agency_currencies as dc, #__ad_agency_plugins as dp where dp.published=1 and dp.name=dc.plugname";
    $database->setQuery($query);
    $normal_plugs = $database->loadObjectList ();
	
	$default_currency = $configs->currencydef;
    $plugs = array ();
	
    $currency_list = '<select disabled="disabled" name="currencydef">';
    foreach ($currs as $i => $v){
        $query = "select dc.plugname from #__ad_agency_currencies as dc, #__ad_agency_plugins as dp where dc.currency_name='".$v->currency_name."' and dp.published=1 and dp.name=dc.plugname";
        $database->setQuery($query);
        $plugs[$v->currency_name][] = $database->loadObjectList();
		
        $currency_list .= '<option  value="'.$v->currency_name.'" ';
    	if($v->currency_name == $default_currency){
            $currency_list .= 'selected';
		}
        $currency_list .= '>'.$v->currency_full.'</option>';
    }
    $currency_list .= '</select>';
    
	   $query = "select name from #__ad_agency_plugins where `def`='default'";
	    $database->setQuery($query);
	    $defaultplug = $database->loadResult();
	
	    $this->assign('currency_list', $currency_list);
		$approvals = $this->_models['adagencyconfig']->getApprovals();
		$plugin_data =  $this->_models['adagencyplugin']->BackPluginHandler();

		$isJomSocialStreamAd = $this->_models['adagencyconfig']->isJomSocialStreamAd();
		$isJomSocial = $this->_models['adagencyconfig']->isJomSocial();
		
		if($isJomSocial){
		    require_once(JPATH_BASE."/components/com_adagency/helpers/jomsocial.php");
			$helperJomSocial = new JomSocialTargeting();
		    $jomFields = $helperJomSocial->getFields();
			$jomFields = $helperJomSocial->getOptsParents($jomFields);
		}
		else{
			$jomFields = NULL;	
		}
		
        $this->assign('isJomSocialStreamAd', $isJomSocialStreamAd);
		$this->assign('isJomSocial', $isJomSocial);
        $this->assign('jomFields', $jomFields);
        $this->assign('adslim', $adslim);
		$this->assign('approvals', $approvals);
    	$this->assign('plugin_data', $plugin_data);    
    	$this->assign('plugs', $plugs);    
    	$this->assign('default_currency', $default_currency);    
    	$this->assign('normal_plugs', $normal_plugs);    
    	$this->assign('defaultplug', $defaultplug);    
		parent::display($tpl);
	}
    
}

?>