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

class adagencyViewadagencyAdcode extends JViewLegacy {

	function display ($tpl =  null ) {

		$orders = $this->get('listPackages');
		$this->assignRef('packages', $orders);
		$pagination = $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);

		parent::display($tpl);

	}

	function editForm($tpl = null) {
		global $mainframe;
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$ad = $this->get('ad');
		
		$my = JFactory::getUser();
		$size_selected = NULL;
		$configs = $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);

        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
        $itemid_cpn = $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

		$advertiser = $this->getModel("adagencyAdcode")->getCurrentAdvertiser();
		$advertiser_id = (int)$advertiser->aid;

		$camps2 = NULL;

		//check for valid id of the banner
		if ($ad->id!=0) {
		if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
		if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Advanced")) die('This banner id is not an Ad Code banner');
		}
		//check for valid id of the banner
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		///===================select available campaigns============================
		$adv_id = $advertiser_id;

		if ($adv_id) {
			$camps = $this->getModel("adagencyAdcode")->getCampsByAid($adv_id);
		} else { $camps=''; }

		if(isset($camps)&&(is_array($camps)))
		/*
		foreach ($camps as &$camp){
			if( (!isset($camp->adparams['width'])) || (!isset($camp->adparams['height'])) || ($camp->adparams['width'] == '') || ($camp->adparams['height'] == '') ) {
				$camps2[] = $camp;
			} elseif((!isset($ad->width))||($ad->width != $camp->adparams['width'])||(!isset($camp->adparams['height']))||(!isset($ad->height))||($ad->height != $camp->adparams['height'])) {
				//@unset($camp);
				$camp = NULL;
			} else { $camps2[] = $camp; }
		}
		$camps = $camps2;
		*/
		$these_campaigns = $this->getModel("adagencyAdcode")->getSelectedCamps($advertiser_id, $ad->id);

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyAdcode")->getChannel($ad->id); } else { $channel = NULL; }

		/*$czones = $this->getModel("adagencyAdcode")->processCampZones($camps);
		$czones = $this->getModel("adagencyAdcode")->createSelectBox($czones,$ad->id);*/
		
		$czones = $this->getModel("adagencyAdcode")->processCampZones($camps);
		//$ad->width = $size_selected['0'];
		//$ad->height = $size_selected['1'];
        
		$czones_select = $this->getModel("adagencyAdcode")->createSelectBox($czones, $ad->id, $ad);
		$campaigns_zones = $this->getModel("adagencyAdcode")->getCampZones($camps);

        $camps = $this->getModel("adagencyAdcode")->getCampsByAid($adv_id, 1);
        if (!isset($czones) || empty($czones)) {
            $camps = array();
        }

		$this->assign("czones",$czones);
		$this->assign("czones_select",$czones_select);
		$this->assign("campaigns_zones", $campaigns_zones);
        $this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
        $this->assign("ad", $ad);
        $this->assign("channel",$channel);
        $this->assign("configs", $configs);
        $this->assign("data", $data);
        $this->assign("camps", $camps);
        $this->assign("advertiser_id", $advertiser_id);
        $this->assign("these_campaigns", $these_campaigns);

        parent::display($tpl);
    }

}

?>
