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

class adagencyAdminViewadagencyTransition extends JViewLegacy {

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
		$helper = new adagencyAdminModeladagencyTransition();
		global $mainframe;
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$ad = $this->get('ad'); 
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
		if(isset($_SESSION['newest_adv'])&($advertiser_id=="")&&($_SESSION['newest_adv']!='')) {$advertiser_id = $_SESSION['newest_adv']+1;}
		if(isset($_SESSION['newest_adv'])) {unset($_SESSION['newest_adv']);}
		
		$configs = $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
		
		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyTransition")->getChannel($ad->id); } else { $channel = NULL; }

		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		if (!$isNew) { 
			$advertiser_id = $ad->advertiser_id;
			if(!is_array($ad->parameters))
				$ad->parameters = unserialize($ad->parameters);
		} else {
			$ad->parameters['ad_code']='';
		}
		JToolBarHelper::title(JText::_('AD_TRANSITION').":<small>[".$text."]</small>");
		if ($isNew) {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::cancel('cancel');

		} else {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::cancel ('cancel');
		}
		
		//================check if the banner is added to a campaign===============
		$sqla = "SELECT count(*) FROM #__ad_agency_campaign_banner WHERE `banner_id`=".intval($ad->id);
		$db->setQuery($sqla);
		$db->query();
		$added = $db->loadResult();
		$this->assign("added", $added);
		
		if ($isNew) $ad->approved='Y';
		
		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_SELECT_ADVERTISER'), 'aid', 'company' );	
	    $advertisersloaded = $helper->gettransitionlistAdvertisers();
	    $advertisers 	= array_merge( $advertisers, $advertisersloaded );
	    $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
			
	 	$sts_select = new StdClass;
		$sts_select->status = JText::_("ADAG_SEL_STS");
		$sts_select->value = '';
		$sts_approve = new StdClass;
		$sts_approve->status = JText::_("AD_APPROVED");
		$sts_approve->value = "Y";
		$sts_decline = new StdClass;
		$sts_decline->status = JText::_("ADAG_DECLINED");
		$sts_decline->value = "N";
		$sts_pending = new StdClass;
		$sts_pending->status = JText::_("ADAG_PENDING");
		$sts_pending->value = 'P';
		$statuses[] = $sts_select;$statuses[] = $sts_approve; $statuses[] = $sts_decline;$statuses[] = $sts_pending;					
		$lists['approved'] = JHTML::_('select.genericlist', $statuses,'approved','class="inputbox" size="1"','value','status',$ad->approved);
	    
	    // Padding  property
		$lists['padding'] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[padding]', 'class="inputbox"', @$ad->parameters['padding'] );
		// Border property
		$lists["border"] = JHTML::_('select.integerlist',   0, 25, 1, 'parameters[border]', 'class="inputbox"', @$ad->parameters['border']);


		// Channels list - begin
		if(isset($ad->channel_id)) {
			$default_channel = $ad->channel_id;
		} else {
			$default_channel = NULL;
		}

		$sql = "SELECT id,name FROM #__ad_agency_channels";
		$db->setQuery($sql);
		$the_channels = $db->loadObjectList();
		
		$channels[] = JHTML::_('select.option',  "0", ' - '.strtolower(JText::_('ADAG_NONE')).' - ', 'id', 'name' );
		$channels = array_merge( $channels, $the_channels );
		$lists['channel_id'] = JHTML::_( 'select.genericlist', $channels, 'channel_id', 'class="inputbox" size="1"','id', 'name', $default_channel);
		// Channels list - end	
		
		//Show Zone select
		if(($advertiser_id!='')&&($advertiser_id!=0)){
			if(!$isNew){
				$sql = "SELECT `id`, `name` FROM #__ad_agency_campaign WHERE aid = ".$advertiser_id;
				$db->setQuery($sql);
				$assoc_camps = $db->loadObjectList();
			} else { $assoc_camps = NULL; }
			$this->assign("assoc_camps",$assoc_camps);
			
			$sql="SELECT DISTINCT cb.campaign_id FROM #__ad_agency_banners AS b LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id=b.id WHERE b.advertiser_id=$advertiser_id AND b.id=".intval($ad->id);
			$db->setQuery($sql);
			$banners_camps = $db->loadColumn();
			$this->assign("banners_camps", (array)$banners_camps);		
					
			$sql1 = "SELECT DISTINCT tid FROM #__ad_agency_order WHERE aid=".$advertiser_id." "; 
			$db->setQuery($sql1);
			$the_advertiser_packages=$db->loadResultArray();
			
			$nothing="";
			if(isset($the_advertiser_packages))
			foreach($the_advertiser_packages as $pk){
				$query="SELECT zones_wildcard FROM #__ad_agency_order_type WHERE tid=".$pk." ";
				$db->setQuery($query);
				$rezult_wild=$db->loadResult();
				$rezult_wild=explode("|",$rezult_wild);
				//print_r($rezult_wild);
				$rezult_wild=implode(",",$rezult_wild);
				$nothing.=$rezult_wild." ";
			}
			$wildzones=substr(str_replace(" ",",",$nothing),0,-1);
			if($wildzones==false) {$wildzones="''";}
			if(strstr($wildzones,",,")) $wildzones="''";
			
			$the_advertiser_packages = @implode(",", $the_advertiser_packages);
			if ($the_advertiser_packages=="") { $notice_cond="-1";} else { $notice_cond="-1,"; }
			$sql2 = "SELECT DISTINCT zones FROM #__ad_agency_order_type WHERE tid IN (".$notice_cond.$the_advertiser_packages.") ";
			$db->setQuery($sql2);
			$packages_positions=$db->loadResultArray();
			@$packages_positions=implode("','",$packages_positions);
			// If we have one package that contains All Zones, then we don't need a condition
			$packages_positions=str_replace("|","','",$packages_positions);
			if (!preg_match("/All Zones/i", $packages_positions)) {
			$packages_positions="('".$packages_positions."')";
			$condition=" AND position IN ".$packages_positions." ";}
		if(!isset($condition)) { $condition="";}
		if($wildzones[strlen($wildzones)-1]==',') { $wildzones=substr($wildzones,0,strlen($wildzones)-1);}
		if(isset($wildzones[0]) && ($wildzones[0]==',')) { $wildzones=substr($wildzones,1,strlen($wildzones));}
		if(($wildzones=="")||($wildzones==",")) { $wildzones="''"; }
		$sql = "SELECT id, title FROM #__modules WHERE module='mod_ijoomla_adagency_zone' ".$condition." OR id IN (".$wildzones.") ORDER BY title ASC";
		$db->setQuery($sql);
		if (!$db->query()) {
			mosErrorAlert( $db->getErrorMsg() );
			return;
		}
		$the_zzones=$db->loadRowlist();
		$zone[] 	= JHTML::_('select.option',  "0", JText::_('AD_SELECT_ZONE'), 'id', 'title' );	
		$zone 	= array_merge( $zone, $db->loadObjectList() );
		$lists['zone_id'] = JHTML::_( 'select.genericlist', $zone, 'zone', 'class="inputbox" size="1"','id', 'title', $ad->zone);
		} else $no_advertiser_sel=JText::_('ADS_SEL_ADV');
		///////////////////
		if(isset($the_zzones)&&($the_zzones!= NULL)){
			$lists['zone_id']="<select id='zone' class='inputbox' size='1' name='zone'>
			<option value='0'>".JText::_("AD_SELECT_ZONE")."</option>";
			foreach($the_zzones as $value){
				if(isset($ad->zone)&&($ad->zone==$value[0])) { $current_selected="selected='selected'";} else {$current_selected=""; }
				$already[]=$value[0];
				$lists['zone_id'].="<option value='".$value[0]."' ".$current_selected.">".$value[1]."</option>";
			}
			
			$sql_allzones="SELECT z.zoneid, z.z_title
			FROM #__ad_agency_zone AS z
			LEFT JOIN #__modules AS m ON z.zoneid = m.id
			WHERE m.module = 'mod_ijoomla_adagency_zone'";
			$db->setQuery($sql_allzones);
			$all_existing_zones=$db->loadRowlist();
			
			if(isset($all_existing_zones)){
			foreach($all_existing_zones as $currentz){
				if(!in_array($currentz[0],$already)) { 
					if(isset($ad->zone)&&($ad->zone==$currentz[0])) { $current_selected="selected='selected'";} else {$current_selected=""; }
					$lists['zone_id'].="<option value='".$currentz[0]."' ".$current_selected." style='font-size: 12px; color: #FF0000;'>".$currentz[1]."</option>";
					}
			}
			}
			
			$lists['zone_id'].="</select>";
		} else {

			$lists['zone_id']="<select id='zone' class='inputbox' size='1' name='zone'>
			<option value='0'>".JText::_("AD_SELECT_ZONE")."</option>";
			$sql_allzones="SELECT z.zoneid, z.z_title
			FROM #__ad_agency_zone AS z
			LEFT JOIN #__modules AS m ON z.zoneid = m.id
			WHERE m.module = 'mod_ijoomla_adagency_zone'";
			$db->setQuery($sql_allzones);
			$all_existing_zones=$db->loadRowlist();
			
			if(isset($all_existing_zones)){
			foreach($all_existing_zones as $currentz){
				if(isset($ad->zone)&&($ad->zone==$currentz[0])) { $current_selected="selected='selected'";} else {$current_selected=""; }
				$lists['zone_id'].="<option value='".$currentz[0]."' ".$current_selected." style='font-size: 12px; color: #FF0000;'>".$currentz[1]."</option>";
			}
			}
			
			$lists['zone_id'].="</select>";
		}
		
		if(isset($no_advertiser_sel)){
			$lists['zone_id']=JText::_("AD_WARN_SEL_ADV");
		}
		////////////////////
		///===================select available campaigns============================	
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyTransition")->getCampsByAid($adv_id);
		} else $camps='';

		if($advertiser_id > 0) {
			$advt = $this->getModel("adagencyConfig")->getAdvById($advertiser_id);
		} else {
			$advt = NULL;
		}
		
		$exist_zone = $this->get('ExistsZone');
		//echo "<pre>";var_dump($exist_zone);die();
		
		if(!$exist_zone) {
			$no_zone = "<div id=\"system-message-container\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>
							<div class=\"alert alert-notice\">
								<p>".JText::_('ADAG_NO_ZONE_TYPE')."&nbsp;&nbsp; <a href='http://www.ijoomla.com/redirect/adagency/ad_support.htm' target='_blank'>".JText::_('AD_VIDEO')."<img src='components/com_adagency/images/icon_video.gif' alt='watch video'></a></p>
							</div>
						</div>";
		} else {
			$no_zone = NULL;
		}
	
		if((!is_array($camps)||(count($camps)<=0))&&($advt != NULL)&&($no_zone == NULL)){
			$no_zone = "<div id=\"system-message-container\"><button type=\"button\" class=\"close\" data-dismiss=\"alert\">×</button>
							<div class=\"alert alert-notice\">
								<p>".JText::_('ADAG_NO_CAMP_TYPE').'&nbsp;&nbsp; <a href="http://www.ijoomla.com/redirect/adagency/ad_support.htm" target="_blank">'.JText::_('AD_VIDEO')."<img src='components/com_adagency/images/icon_video.gif' alt='watch video'></a></p>
							</div>
						</div>";
		} 
		
		$query = "SELECT `params` FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadColumn();
		$params = unserialize($params["0"]);
		
		/*$campaigns_zones = $this->getModel("adagencyTransition")->getCampZones($camps);
		$czones = $this->getModel("adagencyTransition")->processCampZones($camps);
		$czones = $this->getModel("adagencyTransition")->createSelectBox($czones,$ad->id);*/
		
		$campaigns_zones = $this->getModel("adagencyTransition")->getCampZones($camps);
		$czones = $this->getModel("adagencyTransition")->processCampZones($camps);
		$czones_select = $this->getModel("adagencyTransition")->createSelectBox($czones, $ad->id, $ad);
		
        $camps = $this->getModel("adagencyTransition")->getCampsByAid($adv_id, 1);        
		
		$this->assign("campaigns_zones", $campaigns_zones);
		$this->assign("czones", $czones);
		$this->assign("czones_select",$czones_select);
		$this->assign("czones",$czones);	
		$this->assign("no_zone", $no_zone);
		$this->assign("advt", $advt);		
		$this->assign('advertiser_id',$advertiser_id);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);
		$this->assign("ad", $ad);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("params", $params);
		parent::display($tpl);
	}
}
?>