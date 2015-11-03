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

class adagencyViewadagencyTransition extends JViewLegacy {
	
	function scandir_php4($dir)
	{
  		$files = array();
  		if ($handle = @opendir($dir))
  	{
    	while (false !== ($file = readdir($handle)))
      	array_push($files, $file);
    	closedir($handle);
  	}
  		return $files;
	}
	
	function editForm($tpl = null) { 
		global $mainframe;
		$size_selected = NULL;
		$helper = new adagencyModeladagencyTransition();
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$ad = $this->get('ad'); 
		$currentAdvertiser = $this->get('CurrentAdvertiser');
		$advertiser_id = (int)$currentAdvertiser->aid;

		$configs = $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
		$itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
		//check for valid id of the banner
		if ($ad->id!=0) {
		if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
		if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Transition")) die('This banner id is not a Transition banner');
		}
		//check for valid id of the banner
		
		$isNew = ($ad->id < 1);
		if (!$isNew) {
			$ad->parameters = unserialize($ad->parameters);
			
		}	
				
		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );	
	    $advertisersloaded = $helper->gettransitionlistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
	    
	    // Padding  property
		$lists['padding'] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[padding]', 'class="inputbox"', @$ad->parameters['padding'] );
		// Border property
		$lists["border"] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[border]', 'class="inputbox"', @$ad->parameters['border']);
		
		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyTransition")->getCampsByAid($adv_id);
		} else { $camps=''; }	
		
		$these_campaigns = $this->getModel("adagencyTransition")->getSelectedCamps($advertiser_id, $ad->id);

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyTransition")->getChannel($ad->id); } else { $channel = NULL; }
		
		/*$czones = $this->getModel("adagencyTransition")->processCampZones($camps);
		$czones = $this->getModel("adagencyTransition")->createSelectBox($czones,$ad->id);*/
		
		$czones = $this->getModel("adagencyTransition")->processCampZones($camps);			
		@$ad->width=$size_selected['0'];			
		@$ad->height=$size_selected['1'];
        $czones_select = $this->getModel("adagencyTransition")->createSelectBox($czones, $ad->id, $ad);
		$campaigns_zones = $this->getModel("adagencyTransition")->getCampZones($camps);
		
		$itemid_cpn = $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

        $camps = $this->getModel("adagencyTransition")->getCampsByAid($adv_id, 1);
        
		$this->assign("czones",$czones);
		$this->assign("czones_select",$czones_select);
		$this->assign("campaigns_zones", $campaigns_zones);
        $this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);		
		$this->assign("ad", $ad);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("advertiser_id", $advertiser_id);
		$this->assign("these_campaigns", $these_campaigns);

		parent::display($tpl);
	}
}
?>