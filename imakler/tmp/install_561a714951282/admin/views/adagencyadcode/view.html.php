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

class adagencyAdminViewadagencyAdcode extends JViewLegacy {

	function display ($tpl =  null ) {
		JToolBarHelper::title(JText::_('VIEWTREEADDADCODE'), 'generic.png');
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::addNewX('edit','New');
		JToolBarHelper::editListX();
		JToolBarHelper::deleteList(JText::_('AGENCYCONFIRMDEL'));
		$orders = $this->get('listPackages');
		$this->assignRef('packages', $orders);
		$pagination =  $this->get( 'Pagination' );
		$this->assignRef('pagination', $pagination);
		parent::display($tpl);
	}

	function editForm($tpl = null) {
		$helper = new adagencyAdminModeladagencyAdcode();
		global $mainframe;
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$ad = $this->get('ad');
		$camps2 = NULL;
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
		if(isset($_SESSION['newest_adv'])&($advertiser_id=="")&&($_SESSION['newest_adv']!='')) {$advertiser_id = $_SESSION['newest_adv']+1;}
		if(isset($_SESSION['newest_adv'])) {unset($_SESSION['newest_adv']);}

		$configs = $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyAdcode")->getChannel($ad->id); } else { $channel = NULL; }
		//echo "<pre>";var_dump($channel);die();
		$this->assign("channel",$channel);

		if (!$advertiser_id) $advertiser_id = $ad->advertiser_id;
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('AD_NEW'):JText::_('AD_EDIT');
		if (!$isNew) {
			//if ($ad->approved=='N') $ad->approved='0'; else $ad->approved='1';
			$ad->parameters = @unserialize($ad->parameters);
		}
		if ($isNew) $ad->approved='Y';

		JToolBarHelper::title(JText::_('VIEWTREEADDADCODE').":<small>[".$text."]</small>");
		if ($isNew) {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::cancel('cancel');

		} else {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::cancel ('cancel');
		}
		$this->assign("ad", $ad);
		if (!isset($ad->parameters['target_window'])) $ad->parameters['target_window']='_blank';
		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_SELECT_ADVERTISER'), 'aid', 'company' );
	    $advertisersloaded = $helper->getadcodelistAdvertisers();
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

		// Window option
		$window[] 	= JHTML::_('select.option', '_blank', JText::_('AD_OPENNEWWINDOW'), 'value', 'option' );
		$window[] 	= JHTML::_('select.option', '_self', JText::_('AD_OPENSAMEWINDOW'), 'value', 'option' );
		$lists['window'] = JHTML::_( 'select.genericlist', $window, 'parameters[target_window]', 'class="inputbox" size="1"  id="show_hide_box"','value', 'option', $ad->parameters['target_window']);

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
		//Show zones available for advertiser
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
				$rezult_wild=implode(",",$rezult_wild);
				$nothing.=$rezult_wild." ";
			}
			$wildzones=substr(str_replace(" ",",",$nothing),0,-1);
			if($wildzones==false) {$wildzones="''";}
			if(strstr($wildzones,",,")) $wildzones="''";

			@$the_advertiser_packages=implode(",",$the_advertiser_packages);
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
		if($wildzones[0]==',') { $wildzones=substr($wildzones,1,strlen($wildzones));}
		if($wildzones[strlen($wildzones)-1]==',') { $wildzones=substr($wildzones,0,strlen($wildzones)-1);}
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
		//END Show Zone select
		///////////////////////////////////////////////////////////////////////////////
		if((isset($the_zzones))&&($the_zzones!= NULL)){
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
		//////////////////////////////////////////////////
		///==============select available campaigns================
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyAdcode")->getCampsByAid($adv_id);
		} else $camps='';

		if(isset($camps)&&(is_array($camps)))
		foreach ($camps as &$camp){
			if( (!isset($camp->adparams['width'])) || (!isset($camp->adparams['height'])) || ($camp->adparams['width'] == '') || ($camp->adparams['height'] == '') ) {
				$camps2[] = $camp;
			} elseif((!isset($ad->width))||($ad->width != $camp->adparams['width'])||(!isset($camp->adparams['height']))||(!isset($ad->height))||($ad->height != $camp->adparams['height'])) {
				//@unset($camp);
				$camp = NULL;
			} else { $camps2[] = $camp; }
		}
		$camps = $camps2;

		//=========check if the banner is added to a campaign=========
		$sqla = "SELECT count(*) FROM #__ad_agency_campaign_banner WHERE `banner_id`='".$ad->id."'";
		$db->setQuery($sqla);
		$db->query();
		$added = $db->loadResult();

		if($advertiser_id > 0) {
			$advt = $this->getModel("adagencyConfig")->getAdvById($advertiser_id);
		} else {
			$advt = NULL;
		}

		$exist_zone = $this->get('ExistsZone');
		//echo "<pre>";var_dump($exist_zone);die();
		if(!$exist_zone) {
			$no_zone = '<div id="system-message-container">
							<button type="button" class="close" data-dismiss="alert">×</button>
							<div class="alert alert-notice">
								<p>'.JText::_('ADAG_NO_ZONE_TYPE').".&nbsp;&nbsp; <a href='http://www.ijoomla.com/redirect/adagency/ad_support.htm' target='_blank'>".JText::_('AD_VIDEO')."<img src='components/com_adagency/images/icon_video.gif' alt='watch video'></a></p>
							</div>
						</div>";
		} else {
			$no_zone = "";
			if(isset($ad->width) && isset($ad->height) && (intval($ad->width)>0) && (intval($ad->height)>0)){
				$exist_zone_wh = $this->_models["adagencyadcode"]->getExistsZoneWH($ad->width,$ad->height);
				if(!$exist_zone_wh){
					$no_size = true;
					$no_zone = '<div id="system-message-container"><button type="button" class="close" data-dismiss="alert">×</button>
									<div class="alert alert-notice">
										<p>'.JText::_('ADAG_NO_ZONE_SIZE').".&nbsp;&nbsp; <a href='http://www.ijoomla.com/redirect/adagency/ad_support.htm' target='_blank'>".JText::_('AD_VIDEO')."<img src='components/com_adagency/images/icon_video.gif' alt='watch video'></a></p>
									</div>
								</div>";
				}
			}
			$exist_pack = $this->get('ExistsAffiliateCampaign');
			if((!$exist_pack)&&($no_zone == "")) {
				$no_zone = '<div id="system-message-container"><button type="button" class="close" data-dismiss="alert">×</button>
								<div class="alert alert-notice">
									<p>'.JText::_('ADAG_NO_CAMP_TYPE').".&nbsp;&nbsp; <a href='http://www.ijoomla.com/redirect/adagency/ad_support.htm' target='_blank'>".JText::_('AD_VIDEO')."<img src='components/com_adagency/images/icon_video.gif' alt='watch video'></a></p>
								</div>
							</div>";
			} else {

			}
		}
		
		$query = "SELECT `params` FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadColumn();
		$params = unserialize($params["0"]);

		/*$campaigns_zones = $this->getModel("adagencyAdcode")->getCampZones($camps);
		$czones = $this->getModel("adagencyAdcode")->processCampZones($camps);
		$czones = $this->getModel("adagencyAdcode")->createSelectBox($czones,$ad->id);*/
		
		$campaigns_zones = $this->getModel("adagencyAdcode")->getCampZones($camps);
		$czones = $this->getModel("adagencyAdcode")->processCampZones($camps);
		$czones_select = $this->getModel("adagencyAdcode")->createSelectBox($czones, $ad->id, $ad);
		
		$camps = $this->getModel("adagencyAdcode")->getCampsByAid($adv_id, 1);

		$this->assign("campaigns_zones", $campaigns_zones);
		$this->assign("czones", $czones);
		$this->assign("czones_select",$czones_select);
		$this->assign("no_zone", $no_zone);
		$this->assign("advt", $advt);
		$this->assign("configs", $configs);
		$this->assign("added", $added);
		$this->assign('advertiser_id',$advertiser_id);
		$this->assign("configs", $configs);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("params", $params);

		parent::display($tpl);
	}

}

?>
