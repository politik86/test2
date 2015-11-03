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

class adagencyAdminViewadagencyFlash extends JViewLegacy {

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

	function listswfs( $name, $active = NULL, $javascript = NULL, $directory = NULL )
	{
		if ( !$directory ) {
			$directory = '/images/stories/';
		}

		if ( !$javascript ) {
			$javascript = "onchange=\"javascript:if (document.forms.adminForm." . $name . ".options[selectedIndex].value!='') {document.imagelib.src='..$directory' + document.forms.adminForm." . $name . ".options[selectedIndex].value} else {document.imagelib.src='../images/blank.png'}\"";
		}

		jimport( 'joomla.filesystem.folder' );
		$imageFiles = JFolder::files( JPATH_SITE.DS.$directory );
		$images 	= array(  JHTML::_('select.option',  '', '- '. JText::_( 'Select Image' ) .' -' ) );
		foreach ( $imageFiles as $file ) {
			if ( eregi( "swf|SWF", $file ) ) {
				$images[] = JHTML::_('select.option',  $file );
			}
		}
		$images = JHTML::_('select.genericlist',  $images, $name, 'class="inputbox" size="1" '. $javascript, 'value', 'text', $active );

		return $images;
	}

	function uploadflash() {
	global $failed;
	$db = JFactory::getDBO();
 	$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
	$test = $this->getModel("adagencyAds")->rememberChannel();

	if (!$advertiser_id) return;
	//get the image folder
		$sqla = "SELECT `imgfolder` FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sqla);
		$db->query();
		$imgfolder = $db->loadResult();
	//end image folder
	$targetPath = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/';
	$failed = '0';
	if (isset($_FILES['image_file'])) {
		$filename = time();
		$filename2 = $_FILES['image_file']['name'];
		if ($filename2) {
			$filenameParts = explode('.', $filename2);
			$extension = '';
			if (count($filenameParts) > 1)
				$extension = array_pop($filenameParts);
			$extension = strtolower($extension);
			if (!in_array($extension, array('swf'))) {
				$text = strip_tags( addslashes( nl2br( "The image must be swf." )));
				echo "<script>alert('$text');</script>";
				$failed=1;
			}
		$filename.='.'.$extension;
		if ($failed != 1) {
			if (!move_uploaded_file ($_FILES['image_file']['tmp_name'],$targetPath.$filename) || !chmod($targetPath.$filename, 0644)) {
				$text = strip_tags( addslashes( nl2br( "Upload of ".$filename2." failed." )));
				echo "<script>alert('$text'); </script>";
			} else {
				//$text = strip_tags( addslashes( nl2br( "Upload of ".$filename2." was successful." )));
				//echo "<script>alert('$text'); </scr ipt>";
				return $filename;
			}
		}
	  }
	}
	return $filename;
}

	function editForm($tpl = null) {
		$helper = new adagencyAdminModeladagencyFlash();
		$helperView = new adagencyAdminViewadagencyFlash();
		global $mainframe;
		$data = JRequest::get('post');
		$db = JFactory::getDBO();
		$ad = $this->get('ad');
		
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
		if(isset($_SESSION['newest_adv'])&($advertiser_id=="")&&($_SESSION['newest_adv']!='')) {$advertiser_id = $_SESSION['newest_adv']+1;}
		if(isset($_SESSION['newest_adv'])) {unset($_SESSION['newest_adv']);}
		$configs = $this->getModel("adagencyConfig")->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
		//echo "<pre>";var_dump($configs);die();

		if(isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyFlash")->getChannel($ad->id); } else { $channel = NULL; }
		//echo "<pre>";var_dump($channel);die();
		if(isset($_SESSION['channelz'])){
			$channel = new stdClass();
			$channel->sets[0] = $_SESSION['channelz'];
			//echo "<pre>";var_dump($channel);echo "</pre><hr />";//die();
			unset($_SESSION['channelz']);
		}
		if(isset($_SESSION['channelz2'])){
			$channel = NULL;
			$ad->channel_id = $_SESSION['channelz2'];
			unset($_SESSION['channelz2']);
		}

		$imgfolder = $configs->imgfolder;
		///////////////////////////////////////////////////////////
		$adddv=$advertiser_id;
		$directory = "/images/stories/".$imgfolder."/{$adddv}";
		$absolutepath = JPATH_SITE;
		$image_folder = "{$absolutepath}{$directory}";
		if (!is_dir($image_folder)) {
			@mkdir($image_folder, 0755);
			@chmod($mosConfig_absolute_path."/images/stories/".$imgfolder, 0755);
		}
		///////////////////////////////////////////////////////////
		//get the image folder
		if (intval($advertiser_id)>0) {
				$imagepath = str_replace("/administrator","",JPATH_BASE);
				$imagepath = $imagepath."/images/stories/";
				$newimgfolder = $imgfolder."/".$advertiser_id;
			if ( !is_dir ( $imagepath.$newimgfolder ) ) {
		       @mkdir ( $imagepath."/".$newimgfolder );
		       @chmod ( $imagepath."/".$newimgfolder, 0755 ); }
		     else {
		       @chmod ( $imagepath."/".$newimgfolder, 0755 );
		    }
		}
		//end image folder
		$isNew = ($ad->id < 1);
		$text = $isNew?JText::_('New'):JText::_('Edit');
		if (!$isNew) {
			$advertiser_id = $ad->advertiser_id;
			if(!is_array($ad->parameters)) {$ad->parameters = unserialize($ad->parameters);}
			if (!isset($ad->parameters['align'])) $ad->parameters['align']='';
			if (!isset($ad->parameters['target_window'])) $ad->parameters['target_window']='';
		} else {
			$ad->parameters['align']='';
			$ad->parameters['target_window']='';
		}
		JToolBarHelper::title(JText::_('AD_FLASH').":<small>[".$text."]</small>");
		if ($isNew) {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::cancel('cancel');

		} else {
			JToolBarHelper::apply('apply');
			JToolBarHelper::save('save');
			JToolBarHelper::cancel ('cancel');
		}
		if ($isNew) $ad->approved='Y';

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

		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_SELECT_ADVERTISER'), 'aid', 'company' );
	    $advertisersloaded = $helper->getflashlistAdvertisers();
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

		// Alignment options :
		$alignments[] = JHTML::_('select.option',  "0", JText::_('ADSELPOSITION'), 'value', 'option' );
		$alignments[] = JHTML::_('select.option',  "left", JText::_('AD_LEFT'), 'value', 'option' );
		$alignments[] = JHTML::_('select.option',  "center", JText::_('AD_CENTER'), 'value', 'option' );
		$alignments[] = JHTML::_('select.option',  "right", JText::_('AD_RIGHT'), 'value', 'option' );
		$lists['alignment']  =  JHTML::_( 'select.genericlist', $alignments, 'parameters[align]', 'class="inputbox" size="1"','value', 'option', $ad->parameters['align']);


		// Window option
		$window[] 	= JHTML::_('select.option', '_blank', JText::_('AD_OPENNEWWINDOW'), 'value', 'option' );
		$window[] 	= JHTML::_('select.option', '_self', JText::_('AD_OPENSAMEWINDOW'), 'value', 'option' );
		$lists['window'] = JHTML::_( 'select.genericlist', $window, 'parameters[target_window]', 'class="inputbox" size="1"  id="show_hide_box"','value', 'option', $ad->parameters['target_window']);

	    // Imagelist
		$javascript 	= 'onchange="changeDisplayImage();"';
		$directory 	= "images/stories/".$imgfolder."/{$advertiser_id}";
		// $livesite = $mainframe->getSiteURL();
		$absolutepath = JPATH_SITE;
		$image_folder = "{$absolutepath}{$directory}";
		$lists['image_path'] = "/images/stories/".$imgfolder."/{$advertiser_id}/";
		//if (isset($_FILES['image_file'])) $ad->swf_url = $_FILES['image_file']['name'];
		if(isset($this->uploaded_file)) { $ad->swf_url = $this->uploaded_file;$ad->image_url = $this->uploaded_file;}
		//$lists['image_url']	= JHTML::_('list.images',  'image', $ad->swf_url, $javascript, $directory );
		$director=$image_folder;
		$imgs=$helperView->scandir_php4($director);
		$realimgs = array();
		foreach($imgs as $img)
			if(is_file($director."/".$img))
			{
				$props = @getimagesize($director."/".$img);
				if($props === false) continue;
				array_push($realimgs, array("width"=>$props[0],"height"=>$props[1],"name"=>"'".addslashes($img)."'"));
			}

		////startflash

		$javascript = 'onchange="changeDisplayFlash();"';
		//$lists['swf_url'] = adagencyAdminViewadagencyFlash::listswfs('swf_url', $ad->swf_url, $javascript, $directory );

		global $mainframe;
		// $mainframe->getSiteURL();
		$lists['flash_directory'] = JURI::root() . "/images/stories/".$imgfolder."/{$ad->advertiser_id}/";
		///endflash

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

			$the_advertiser_packages = @implode(",",$the_advertiser_packages);
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
		} else $no_advertiser_sel=JText::_('ADS_SEL_ADV');//'-please select advertiser-';
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
		///////////////////////////////////////////////////////////////////////////////
		///===================select available campaigns============================
		$adv_id = $advertiser_id;
		if ($adv_id) {
			$camps = $this->getModel("adagencyFlash")->getCampsByAid($adv_id);
		} else $camps = '';

		//echo "<pre>";var_dump($camps);die();
		$camps2 = array();
		if(isset($camps)&&(is_array($camps)))
		foreach ($camps as &$camp){
			//$camp->adparams = @unserialize($camp->adparams);
			//echo "<pre>";var_dump($camp);die();

			if( (!isset($camp->adparams['width'])) || (!isset($camp->adparams['height'])) || ($camp->adparams['width'] == '') || ($camp->adparams['height'] == '') ) {
				$camps2[] = $camp;
			} elseif((!isset($ad->width))||($ad->width != $camp->adparams['width'])||(!isset($ad->height))||($ad->height != $camp->adparams['height'])) {
				//@unset($camp);
				$camp = NULL;
			} else {
				$camps2[] = $camp;
			}
		}
		$camps = $camps2;
		//echo "<pre>";var_dump($camps);die();

		//================check if the banner is added to a campaign===============
		$sqla = "SELECT count(*) FROM #__ad_agency_campaign_banner WHERE `banner_id`=".intval($ad->id);
		$db->setQuery($sqla);
		$added = $db->loadResult();

		if($advertiser_id > 0) {
			$advt = $this->getModel("adagencyConfig")->getAdvById($advertiser_id);
		} else {
			$advt = NULL;
		}

		$no_size = false;
		$exist_zone = $this->get('ExistsZone');
		//echo "<pre>";var_dump($exist_zone);die();
		if(!$exist_zone) {
			$no_zone = '<div id="system-message-container"><button type="button" class="close" data-dismiss="alert">×</button>
							<div class="alert alert-notice">
								<p>'.JText::_('ADAG_NO_ZONE_TYPE').'&nbsp;&nbsp; <a href="http://www.ijoomla.com/redirect/adagency/ad_support.htm" target="_blank">'.JText::_('AD_VIDEO').'<img src="components/com_adagency/images/icon_video.gif" alt="watch video"></a></p>
							</div>
						</div>';
		} else {
			$no_zone = "";
			if(isset($ad->width) && isset($ad->height) && (intval($ad->width)>0) && (intval($ad->height)>0)){
				$exist_zone_wh = $this->_models["adagencyflash"]->getExistsZoneWH($ad->width,$ad->height);
				if(!$exist_zone_wh){
					$no_size = true;
					$no_zone = '<div id="system-message-container"><button type="button" class="close" data-dismiss="alert">×</button>
									<div class="alert alert-notice">
										<p>'.JText::_('ADAG_NO_ZONE_SIZE').'&nbsp;&nbsp; <a href="http://www.ijoomla.com/redirect/adagency/ad_support.htm" target="_blank">'.JText::_('AD_VIDEO').'<img src="components/com_adagency/images/icon_video.gif" alt="watch video"></a></p>
									</div>
								</div>';
				}
			}
			$exist_pack = $this->get('ExistsFlashCampaign');
			if((!$exist_pack)&&($no_zone == "")) {
				$no_zone = '<div id="system-message-container"><button type="button" class="close" data-dismiss="alert">×</button>
								<div class="alert alert-notice">
									<p>'.JText::_('ADAG_NO_CAMP_TYPE').'&nbsp;&nbsp; <a href="http://www.ijoomla.com/redirect/adagency/ad_support.htm" target="_blank">'.JText::_('AD_VIDEO').'<img src="components/com_adagency/images/icon_video.gif" alt="watch video"></a></p>
								</div>
							</div>';
			} else {

			}
		}
		
		$query = "SELECT `params` FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadColumn();
		$params = unserialize($params["0"]);

		/*$campaigns_zones = $this->getModel("adagencyFlash")->getCampZones($camps);
		$czones = $this->getModel("adagencyFlash")->processCampZones($camps);
		$czones = $this->getModel("adagencyFlash")->createSelectBox($czones, $ad->id);*/
		
		$campaigns_zones = $this->getModel("adagencyFlash")->getCampZones($camps);
		$czones = $this->getModel("adagencyFlash")->processCampZones($camps);
		$czones_select = $this->getModel("adagencyFlash")->createSelectBox($czones, $ad->id, $ad);
		
		$camps = $this->getModel("adagencyFlash")->getCampsByAid($adv_id, 1);

		$this->assign("campaigns_zones", $campaigns_zones);
		$this->assign("czones", $czones);
		$this->assign("czones_select",$czones_select);
		$this->assign("no_size", $no_size);
		$this->assign("no_zone", $no_zone);
		$this->assign("advt", $advt);
		$this->assign("channel",$channel);
		$this->assign("configs", $configs);
		$this->assign("ad", $ad);
		$this->assign("added", $added);
		$this->assign('advertiser_id',$advertiser_id);
		$this->assign("lists", $lists);
		$this->assign("data", $data);
		$this->assign("camps", $camps);
		$this->assign("realimgs", $realimgs);
		$this->assign("params", $params);

		parent::display($tpl);
	}

}

?>
