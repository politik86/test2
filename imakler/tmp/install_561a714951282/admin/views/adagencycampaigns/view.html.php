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

class adagencyAdminViewadagencyCampaigns extends JViewLegacy {

    function display ($tpl =  null ) {
        JToolBarHelper::title(JText::_('ADAGENCY_CAMPAIGNS_MANAGER'), 'generic.png');
        JToolBarHelper::publishList('publish', JText::_("AD_APPROVE"));
        JToolBarHelper::unpublishList('unpublish', JText::_("AD_DECLINE"));
		JToolBarHelper::addNew();
		JToolBarHelper::editList();	
			
        JToolBarHelper::deleteList();
        $camps = $this->get('listCampaigns');
		
		$all_camps = $this->get('AllCamps');
        $camps = $this->_models['adagencycampaigns']->getZonesForCamps($camps);

        $pack_array = array();
		$zone_array = array();
        
		$lists['packs'] = "<select name='selpack1' id='selpack' onchange='document.adminForm.submit();'><option value='0'>-- ".JText::_('ADAG_NONE')." --</option>";
        $lists['zones'] = "<select name='selzone1' id='selzone' onchange='document.adminForm.submit();'><option value='0'>-- ".JText::_('ADAG_NONE')." --</option>";
        if(isset($all_camps)&&($all_camps!=NULL)&&(is_array($all_camps))){
			foreach($all_camps as $camp){
                $selected_pack = NULL; $selected_zone = NULL;
                if(!in_array($camp->package_id,$pack_array)) {
                    if(isset($_SESSION['selpack1']) && ($_SESSION['selpack1'] == $camp->package_id)) { $selected_pack = " selected='selected' "; }
                    $lists['packs'] .= "<option value='".$camp->package_id."' ".$selected_pack.">".$camp->description."</option>";
                }
                
				$allzones = $camp->allzones;
				if(isset($allzones))
				foreach($allzones as $key_all=>$zone){ 
					if(!in_array($zone->zoneid, $zone_array) && intval($zone->zoneid) != 0){
						$selected_zone = "";
						if(isset($_SESSION['selzone1']) && ($_SESSION['selzone1'] == $zone->zoneid)) { $selected_zone = " selected='selected' "; }
						$lists['zones'] .= "<option value='".$zone->zoneid."' ".$selected_zone.">".$zone->z_title."</option>";
						$zone_array[] = $zone->zoneid;
					}
				}
				$pack_array[] = $camp->package_id;
            }
        }
        $lists['packs'] .= "</select>";
        $lists['zones'] .= "</select>";

        //$current_selected=NULL;
        $db = JFactory::getDBO();

        //select advertisers drop-down
        $javascript = 'onchange="document.adminForm.submit();"';
        $advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_AGENCY_ALL_ADV'), 'aid', 'company' );

        $sql = "SELECT a.aid, b.name as company, a.user_id FROM #__ad_agency_advertis as a, #__users as b WHERE a.user_id = b.id ORDER BY company ASC";
        $db->setQuery($sql);
        $advertisersloaded = $db->loadObjectList();

        $advertisers 	= array_merge( $advertisers, $advertisersloaded );

        if(isset($_SESSION['advertiser_id']))
            $advertiser_id = $_SESSION['advertiser_id'];
        else
            $advertiser_id = 0;

        $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);
        //end select advertisers drop-down

        $query = "SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
        $db->setQuery($query);
        $params = $db->loadResult();
        $params = @unserialize($params);
        if(isset($params['timeformat'])){
            $params = $params['timeformat'];
        } else { $params = "-1"; }

        $pagination = $this->get( 'Pagination' );

		$configs = $this->_models['adagencyconfig']->getConfigs();
		$configs->params = @unserialize($configs->params);

		$this->assign("configs", $configs);
        $this->assignRef('params', $params);
        $this->assignRef('camps', $camps);
        $this->assignRef('lists', $lists);
        $this->assignRef('pagination', $pagination);
        parent::display($tpl);
    }

    function approve( &$row, $i, $prefix='' )
    {
        //$imgP = "components/com_adagency/images/pending.gif";
		$icon_class = "fa fa-clock-o";
        if($row->approved=='Y') {
            //$img = 'templates/bluestork/images/admin/'.$imgY;
            $task = "pending";
            $alt = JText::_('Approve');
            $action = JText::_('ADAG_CHTPEN');
			$icon_class = "fa fa-check";
        } elseif ($row->approved=='N') {
            //$img = 'templates/bluestork/images/admin/'.$imgX;
            $task = "approve";
            $alt = JText::_('Unapprove');
            $action = JText::_('Approve item');
			$icon_class = "fa fa-ban";
        } elseif ($row->approved=='P') {
            //$img = $imgP;
            $task = "unapprove";
            $alt = JText::_("ADAG_PENDING");
            $action = "Unnapprove Item";
			$icon_class = "fa fa-clock-o";
        } else {return false;}

        //$href = '<img src="'. $img .'" border="0" alt="'. $alt .'" />';
		$href = '<i class="'.$icon_class.'"></i>';
		
        return $href;
    }

    function time_difference($start_datetime, $end_datetime){
        // Splits the dates into parts, to be reformatted for mktime.
        $start_datetime = explode(" ", $start_datetime, 2);
        $end_datetime = explode(" ", $end_datetime, 2);

        $first_date_ex = explode("-",$start_datetime[0]);
        $first_time_ex = explode(":",$start_datetime[1]);
        $second_date_ex = explode("-",$end_datetime[0]);
        $second_time_ex = explode(":",$end_datetime[1]);

        // makes the dates and times into unix timestamps.
        $first_unix  = mktime($first_time_ex[0], $first_time_ex[1], $first_time_ex[2], $first_date_ex[1], $first_date_ex[2], $first_date_ex[0]);
        $second_unix  = mktime($second_time_ex[0], $second_time_ex[1], $second_time_ex[2], $second_date_ex[1], $second_date_ex[2], $second_date_ex[0]);

        // Gets the difference between the two unix timestamps.
        $timediff = $second_unix-$first_unix;

        // Works out the days, hours, mins and secs.
        $days=intval($timediff/86400);
        $remain=$timediff%86400;
        $hours=intval($remain/3600);
        $remain=$remain%3600;
        $mins=intval($remain/60);
        $secs=$remain%60;

        // Returns a pre-formatted string. Can be chagned to an array.
        $ARR = array();
        $ARR['days'] = $days;
        $ARR['hours'] = $hours;
        $ARR['mins'] = $mins;

        return $ARR;
    }


	function getOrderDetails($campaign_id, $package_id){
		$db = JFactory::getDBO();
		$sql = "select * from #__ad_agency_order where `card_number`='".intval($campaign_id).";".intval($package_id)."'";
		$db->setquery($sql);
		$db->query();
		$result = $db->loadAssocList();
		return $result;
	}

    function editForm($tpl = null) {
    	$helper = new adagencyAdminModeladagencyCampaigns();
		$helperView = new adagencyAdminViewadagencyCampaigns();
        $current_selected = NULL;
        $db = JFactory::getDBO();
        $camp = $this->get('Campaign');
        $data = JRequest::get('request');
		
        $isNew = ($camp->id < 1);
        if (!isset($camp->id)) { $camp->id = 0; }
        
		//$jnow = JFactory::getDate();
		$offset = JFactory::getApplication()->getCfg('offset');
		$jnow = JFactory::getDate('now', $offset);
        
		$JApp = JFactory::getApplication();
					
		$JApp->getCfg('offset');
        if($camp->id==0) {          
			$camp->start_date = $jnow->toSql(true);
        }
        $text = $isNew?JText::_('New'):JText::_('Edit');
        $advertiser_id = JRequest::getVar('advertiser_id', '');


        $aid=JRequest::getVar('aid', '', 'request');
        $task = JRequest::getVar('task', '', 'get');
        JToolBarHelper::title(JText::_('AD_NEW_CAMPAIGN').":<small>[".$text."]</small>");
        JToolBarHelper::save();
        if ($isNew) {
            JToolBarHelper::cancel();

        } else {
            JToolBarHelper::cancel ('cancel', 'Close');
        }

        $configs = $this->_models['adagencyconfig']->getConfigs();
        $configs->params = @unserialize($configs->params);
        $configs->payment = @unserialize($configs->payment);
        if(!isset($configs->params['timeformat'])){ $configs->params['timeformat'] = -1; }

        if ($aid!=NULL) {$adv_condition=" AND a.aid=".$aid;}
        if ($isNew==0) {$rows = $helper->getlistPackages();}
        elseif (($isNew==1)&&(($aid==NULL)||($aid==0))) { $lists['package']=JText::_('AD_WARN_SEL_ADV'); }
        elseif (($isNew==1)&&(($aid!=NULL)||($aid!=0)))
        {
            if (!isset($adv_condition)) { $adv_condition="";}
            $sql_pack = "SELECT DISTINCT a.notes AS description, b.tid
                        FROM #__ad_agency_order AS a, #__ad_agency_order_type AS b
                        WHERE a.notes = b.description " . $adv_condition . " GROUP BY description";
            $db->setQuery($sql_pack);
            // $rows=$this->getModel()->_getList($sql_pack);
            $rows = $db->loadObjectList();
            $cond2=NULL;
            $lists['package']="<select id='otid' class='inputbox' size='1' name='otid' onchange='submitbutton(\"edit\");'>
                <option value='0'>".JText::_("AD_SELECT_PACKAGE")."</option>";
            if((isset($rows))&&($rows!= NULL)){
                foreach($rows as $value){
                    $cond2.=",".$value->tid;
                    if(isset($data['otid'])&&($data['otid'] == $value->tid)) { $current_selected = "selected='selected'";} else { $current_selected = NULL; }
                    $lists['package'].="<option value='".$value->tid."' ".$current_selected.">".$value->description."</option>";
                }
            }

            $sql_pack2 = "SELECT tid,description FROM #__ad_agency_order_type
                         WHERE tid NOT IN (-1" . $cond2 . ") ";
            $db->setQuery($sql_pack2);
            // $rows2=$this->getModel()->_getList($sql_pack2);
            $rows2 = $db->loadObjectList();
            if ((isset($rows2))&&($rows2!= NULL)) {
                foreach($rows2 as $value){
                    if(isset($data['otid'])&&($data['otid'] == $value->tid)) {
                        $current_selected = "selected='selected'";
                    } else { $current_selected = NULL; }
                    $lists['package'].="<option value='".$value->tid."' ".$current_selected." style='font-size: 12px; color: #FF0000;'>".$value->description."</option>";
                }
            }
            $lists['package'].="</select>";
        }
        if(isset($rows)){
            for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                $row = &$rows[$i];
                if ($row->tid == $camp->otid) {
                    $package_row = $row;
                }
            }
        }

        if ($advertiser_id > 0) $camp->aid = $advertiser_id;
        $creat=false;
        $b_with_size = false;
        $with_size = NULL;
        $types = NULL;

        //echo "<pre>";var_dump($camp);die();
        if((isset($camp->id)&&($camp->id>0))||(isset($data['otid']))) {
            $types = array("'a'","'b'");
            $rem_pack = false;
            if(!isset($package_row->type)||($package_row->type == NULL)) {
                $rem_pack = true;
                $rows = $helper->getlistPackages();
                if(isset($rows)){
                    for ($i=0, $n=count( $rows ); $i < $n; $i++) {
                        $row = &$rows[$i];
                        if ($row->tid == $data['otid']) {
                            $package_row = $row;
                        }
                    }
                }
            }
            @$package_row->allzones =  $this->_models['adagencycampaigns']->getZonesForPack($package_row->tid);
            if(is_array($package_row->allzones)){
                foreach($package_row->allzones as $element){
                    $b_with_size = false;
                    $element->adparams = @unserialize($element->adparams);
                    $types = array("'a'","'b'");
                    if(isset($element->adparams)&&is_array($element->adparams)){
                        foreach($element->adparams as $key=>$value) {
                            if($key == 'affiliate') { $types[] = "'Advanced'"; $b_with_size = true; }
                            if($key == 'textad') { $types[] = "'TextLink'"; }
                            if($key == 'standard') { $types[] = "'Standard'"; $b_with_size = true; }
                            if($key == 'flash') { $types[] = "'Flash'"; $b_with_size = true; }
                            if($key == 'popup') { $types[] = "'Popup'"; }
                            if($key == 'transition') { $types[] = "'Transition'"; }
                            if($key == 'floating') { $types[] = "'Floating'"; }
                        }
						$types[] = "'Jomsocial'";
						
                        if($b_with_size == true) {
                            if(isset($element->adparams['width'])&&($element->adparams['width'] != '')) {
                                $with_size = " AND b.width ='".$element->adparams['width']."' AND b.height='".$element->adparams['height']."'";
                            } else {
                                $with_size = NULL;
                            }
                        } else {
                            $with_size = NULL;
                        }

                        $types2[] = "(b.media_type IN (".implode(',',$types).")".$with_size.")";
                    }
                }
                if(is_array(@$types2)) { @$types2 = " AND (".implode(" OR ",@$types2).")"; }
            }
            if($rem_pack) { $package_row = NULL; }
        } else {
            $types2 = NULL;
        }

		if ($camp->aid <=0) {
            if (isset($_POST['id']) && $_POST['id']!=0) {
                $camp = $this->get('Campaign');
                $camp->aid = $advertiser_id;
            } else {
                $camp->aid = 0;
                $camp->start_date = $jnow->toSql(true);
            }
            $creat=true;
        } else {
            $advertiser_id = $camp->aid;
            $creat=false;
        }

        if(isset($advertiser_id) && ($advertiser_id > 0)) {
            $advt = $helper->getAdvById($advertiser_id);
        } else {
            $advt = NULL;
        }

        //echo "<pre>";var_dump($advt);echo "</pre><hr />";//die();
        if ($camp->id > 0 && $camp->aid > 0) {
            $disable_advertiser = " disabled='disabled' readonly='readonly' ";
        } else {
            $disable_advertiser = NULL;
        }
        $javascript = 'onchange="submitbutton(\'edit\');"';
        $advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_SELECT_ADVERTISER'), 'aid', 'company' );
        $advertisersloaded = $helper->getcmplistAdvertisers();
        $advertisers 	= array_merge( $advertisers, $advertisersloaded );
        $lists['advertiser_id']  =  JHTML::_( 'select.genericlist', $advertisers, 'aid', $disable_advertiser . ' class="inputbox" size="1"'.$javascript,'aid', 'company', $advertiser_id);

        // Create status list
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

        if ($isNew) { $status_selected = 'Y'; } else { $status_selected = $camp->approved; }

        $lists['approved'] = JHTML::_('select.genericlist', $statuses,'approved','class="inputbox" size="1"','value','status',$status_selected);

        ///////////////////////////////////////////////////////////////////////////////////////////
    if((!isset($advertiser_id))||($advertiser_id=='')) {$advertiser_id=0;}

    $database = JFactory::getDBO();

    if ($creat) {
        $sql	= "SELECT id, title, media_type, parameters, width, height , approved, '0' as relative_weighting FROM #__ad_agency_banners AS b WHERE b.advertiser_id=$camp->aid ".$types2;
        $database->setQuery($sql);
    } else {
        $sql="SELECT DISTINCT b.id, b.title, b.media_type, b.parameters, b.width, b.height, b.approved, cb.relative_weighting
              FROM #__ad_agency_banners AS b
              LEFT OUTER JOIN #__ad_agency_campaign_banner as cb on cb.campaign_id=$camp->id AND cb.banner_id=b.id WHERE b.advertiser_id=$camp->aid ".@$types2;
        $database->setQuery($sql);
    }

	$ban_row = $database->loadObjectList();
	
    for ($i=0, $n=count( $ban_row ); $i < $n; $i++) {
                $ban_row[$i]->parameters = unserialize($ban_row[$i]->parameters);
                if ($ban_row[$i]->media_type == "Popup" ) {

                }
                elseif ($ban_row[$i]->media_type == "Transition" || $ban_row[$i]->media_type == "Floating" || $ban_row[$i]->media_type == "Advanced" || ($ban_row[$i]->media_type == "Popup" && $ban_row[$i]->parameters['popup_type'] == "HTML")) {
                    if (@preg_match("/ad_url/", $ban_row[$i]->parameters['ad_code'])) {
                        $ban_row[$i]->display = "yes";
                    }
                    else {
                        $ban_row[$i]->display = "no";
                    }
                }
                else {
                    $ban_row[$i]->display = "yes";
                }
                unset($ban_row[$i]->parameters);
    }

        if((!isset($package_row->tid))&&(isset($data['otid']))){
            $package_row2 = $this->_models['adagencycampaigns']->getPackById($data['otid']);
            @$package_row2->allzones = $this->_models['adagencycampaigns']->getZonesForPack($package_row2->tid);
        }

        if(isset($ban_row)&&(isset($package_row->allzones)||isset($package_row2->allzones))){
            $ban_row =  $this->_models['adagencycampaigns']->updateMediaType($ban_row);
            if(isset($package_row->allzones)) {
                $ban_row = $this->_models['adagencycampaigns']->updateZoneList($ban_row,$package_row->allzones,$camp->id);
            } else {
                if(is_array($package_row2->allzones)){
                    foreach($package_row2->allzones as $element) { $element->adparams = @unserialize($element->adparams); }
                }
                $ban_row = $this->_models['adagencycampaigns']->updateZoneList($ban_row,$package_row2->allzones,$camp->id);
            }
        }

        ///////////////////////////////////////////////////////////////////////////////////
        if (!isset($package_row->type))  {
            @$package_row->type='';
        }

        if ($package_row->type == "cpm") {
            $package_row->details = $package_row->quantity ."&nbsp;".JText::_("AD_CAMP_IMP");
        }
        if ($package_row->type == "pc") {
            $package_row->details = $package_row->quantity . "&nbsp;".JText::_("AD_CAMP_CLK");
        }
        if ($package_row->type == "fr" || $package_row->type == "in") {
            $tmp_validity = explode("|", $package_row->validity, 2);
			
         	if($tmp_validity[0] == "1"){
				  $package_row->details = $tmp_validity[0] . " " . JText::_("ADAG_".strtoupper($tmp_validity[1]));}
			else{
				  $package_row->details = $tmp_validity[0] . " " . JText::_("ADAG_".strtoupper($tmp_validity[1]."s"));
			}
			
            //$now_datetime = date("Y-m-d H:i:s");
			$now_datetime = $jnow->toSql(true);
			
            if ($now_datetime > $camp->start_date) {
                //CONTINUE
            }
            else {
                $now_datetime = $camp->start_date;
            }

            if ($now_datetime > $camp->validity) {
                $camp->expired = true;
            }
            else {
                $camp->expired = false;

                //get time difference as days, hours, mins
                $camp->time_left = $helperView->time_difference($now_datetime, $camp->validity);
            }
        }

        //
		$now_datetime = $jnow->toSql(true);
		
        if(intval($camp->id) > 0){
        	$stats = array();
			
			$sql = "select * from #__ad_agency_statistics where `impressions` like '%\"advertiser_id\":".intval($camp->aid).",%' OR `impressions` like '%\"advertiser_id\":\"".intval($camp->aid)."\",%' OR `click` like '%\"advertiser_id\":".intval($camp->aid).",%' OR `click` like '%\"advertiser_id\":\"".intval($camp->aid)."\",%' OR `impressions` like '%\"campaign_id\":".intval($camp->id).",%' OR `impressions` like '%\"campaign_id\":\"".intval($camp->id)."\",%' OR `click` like '%\"campaign_id\":".intval($camp->id).",%' OR `click` like '%\"campaign_id\":\"".intval($camp->id)."\",%'";
			$database->setQuery($sql);
			$database->query();
			$result = $database->loadAssocList();
			
			if(isset($result) && count($result) > 0){
				$nr_imp = 0;
				$nr_click = 0;
				foreach($result as $key=>$value){
					$impressions = @json_decode($value["impressions"], true);
					$click = @json_decode($value["click"], true);
					
					if(isset($impressions) && count($impressions) > 0){
						if(!isset($impressions["0"])){
							$impressions = array("0"=>$impressions);
						}
						
						foreach($impressions as $key_imp=>$value_imp){
							if($value_imp["advertiser_id"] == intval($camp->aid) && $value_imp["campaign_id"] == intval($camp->id)){
								$nr_imp += $value_imp["how_many"];
							}
						}
					}
					
					if(isset($click) && count($click) > 0){
						if(!isset($click["0"])){
							$click = array("0"=>$click);
						}
						
						foreach($click as $key_click=>$value_click){
							if($value_click["advertiser_id"] == intval($camp->aid) && $value_click["campaign_id"] == intval($camp->id)){
								$nr_click += $value_click["how_many"];
							}
						}
					}
				}
				$stats["impressions"] = $nr_imp;
				$stats["click"] = $nr_click;
				if(intval($nr_imp) != 0){
					$nr = $nr_click / $nr_imp * 100;
				}
				$stats["click_rate"] = number_format($nr, 2, '.', ' ');
			}
			
            if ($camp->validity == "0000-00-00 00:00:00" || $now_datetime < $camp->validity || $camp->default == "Y") {
                //CONTINUE
            }
            else {
                $now_datetime = $camp->validity;
            }

            //get time difference as days, hours, mins
            //echo "***".$camp->start_date."***";die();
            $duration_stats = $helperView->time_difference($camp->start_date, $now_datetime);
            $stats = array_merge($stats, $duration_stats);

        }

        if (!isset($stats)) $stats='';
        if(isset($camp->params)) {
            $camp->params = @unserialize($camp->params);
        }
		
		$camps_ads = $this->get('listCampsAds');
		
        $this->assign('advertiser_id',$advertiser_id);
        $this->assign("advt", $advt);
        $this->assign("camp", $camp);
        $this->assign("stats", $stats);
        $this->assign("package_row", $package_row);
        $this->assign("configs", $configs);
        $this->assign("lists", $lists);
        $this->assign("task", $task);
        $this->assign("ban_row", $ban_row);
		$this->assign("camps_ads", $camps_ads);

        parent::display($tpl);
    }

}

?>
