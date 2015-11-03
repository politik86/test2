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

class adagencyViewadagencyReports extends JViewLegacy {
	
	function display ($tpl =  null ) {
		$number_of_active_ads = $this->get("NumberOfActiveAds");
		$number_of_inactive_ads = $this->get("NumberOfInactiveAds");
		$highest_click_ratio_ad = $this->get("HighestClickRatioAd");
		$lowest_click_ratio_ad = $this->get("LowestClickRatioAd");
		$all_advertisers = $this->get("AllAdvertisers");
		$all_campaigns = $this->get("AllCampaigns");
		$all_campaigns_by_adv = $this->get("AllCampaignsByAdv");
		$all_ads = $this->get("AllAds");
		$table_cmp_content = $this->get("TableCmpContent");
		$min = $this->get("MinReportDate");
		$max = $this->get("MaxReportDate");
		
		$pagination = $this->get('Pagination');
		$this->assignRef('pagination', $pagination);
		
		$this->assign("number_of_active_ads", $number_of_active_ads);
		$this->assign("number_of_inactive_ads", $number_of_inactive_ads);
		$this->assign("highest_click_ratio_ad", $highest_click_ratio_ad);
		$this->assign("lowest_click_ratio_ad", $lowest_click_ratio_ad);
		$this->assign("all_advertisers", $all_advertisers);
		$this->assign("all_campaigns", $all_campaigns);
		$this->assign("all_campaigns_by_adv", $all_campaigns_by_adv);
		$this->assign("all_ads", $all_ads);
		$this->assign("table_cmp_content", $table_cmp_content);
		$this->assign("min", $min);
		$this->assign("max", $max);
		
		parent::display($tpl);
	}
	
	function editForm($tpl = null) { 
		global $mainframe;
		$helper = new adagencyAdminModeladagencyAdcode();
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$ad = $this->get('ad'); 
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
		if (!$advertiser_id) $advertiser_id = $ad->advertiser_id;
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');

		JToolBarHelper::title(JText::_('VIEWTREEADDADCODE').":<small>[".$text."]</small>");
		if ($isNew) {
			JToolBarHelper::cancel();
			JToolBarHelper::save('save', 'Save');

		} else {
			JToolBarHelper::cancel ('cancel', 'Close');
			JToolBarHelper::save('save', 'Save');

		}
		$this->assign("ad", $ad);

		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('select advertiser'), 'aid', 'company' );	
	    $advertisersloaded = $helper->getadcodelistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
			
	    $lists['approved'] 		= JHTML::_('select.booleanlist',  'approved', '', $ad->approved );
	    		
		// Window option
		$window[] 	= JHTML::_('select.option', '_blank', JText::_('open in new window'), 'value', 'option' );
		$window[] 	= JHTML::_('select.option', '_self', JText::_('open in the same window'), 'value', 'option' );
		$lists['window'] = JHTML::_( 'select.genericlist', $window, 'parameters[target_window]', 'class="inputbox" size="1"  id="show_hide_box"','value', 'option', $ad->parameters['target_window']);
				
		//Show Zone select
		$sql	= "SELECT id, title FROM #__modules WHERE module='mod_ijoomla_adagency_zone' ORDER BY title ASC";
		$db->setQuery($sql);
		if (!$db->query()) {
			mosErrorAlert( $db->getErrorMsg() );
			return;
		}
		$zone[] 	= JHTML::_('select.option',  "0", JText::_('select zone'), 'id', 'title' );	
		$zone 	= array_merge( $zone, $db->loadObjectList() );
		$lists['zone_id'] = JHTML::_( 'select.genericlist', $zone, 'zone', 'class="inputbox" size="1"','id', 'title', $ad->zone);
		
		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
		$sqls = "SELECT `id`,`name` FROM #__ad_agency_campaign WHERE `aid`=".intval($adv_id);
		$db->setQuery($sqls);
			if (!$db->query()) {
			mosErrorAlert( $db->getErrorMsg() );
			return;
			}
			$camps = $db->loadObjectList();
		} else $camps='';
		
				
		$query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadResult();
		$params = @unserialize($params);
		if(isset($params['timeformat'])){
			$params = $params['timeformat'];
		} else { $params = "-1"; }
		
		$this->assign("params", $params);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);

		parent::display($tpl);
	}
}
?>