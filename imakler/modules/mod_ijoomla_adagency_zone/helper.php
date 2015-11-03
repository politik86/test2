<?php
/**
 * @copyright   (C) 2010 iJoomla, Inc. - All rights reserved.
 * @license  GNU General Public License, version 2 (http://www.gnu.org/licenses/gpl-2.0.html) * @author  iJoomla.com webmaster@ijoomla.com
 * @url   http://www.ijoomla.com/licensing/
 * the PHP code portions are distributed under the GPL license. If not otherwise stated, all images, manuals, cascading style sheets, and included JavaScript  *are NOT GPL, and are released under the IJOOMLA Proprietary Use License v1.0
 * More info at http://www.ijoomla.com/licensing/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class modAdAgencyZoneHelper{

	function getParams(&$params){
		//return the feed data structure for the template
		$my	= JFactory::getUser();
		$mosConfig_absolute_path =JPATH_BASE;
		$mosConfig_live_site     =JURI::base();
		$database                =JFactory :: getDBO();
		$db 		             =JFactory :: getDBO();
		$rotator_content=NULL;
		$script=NULL;
		$http_host = explode(':', $_SERVER['HTTP_HOST'] );
		$the_rot_type = '';
		$module = $params;
		$it_id_con = NULL;
		$adv_here_bottom = NULL;
		$JApp =JFactory::getApplication();
		$jnow = JFactory::getDate();

		if(intval(JVERSION) < 3){
			$jnow->setOffset($JApp->getCfg('offset'));
		}
		
		$real_ip = iJoomlaGetRealIpAddrModule();
		if($real_ip == null ||$real_ip == "" || $real_ip == " " ){
			$real_ip = "127.0.0.1";
		}
		if(strpos($real_ip, ",") !== FALSE){
			$real_ip = explode(",", $real_ip);
			$real_ip = $real_ip["0"];
		}
		
		$offset = JFactory::getApplication()->getCfg('offset');
		$today = JFactory::getDate('now', $offset);
		$time_interval = $today->toSql(true);

		$document =JFactory::getDocument();
		
		$sql = "select `params` from #__ad_agency_settings";
        $database->setQuery( $sql );
		$configs = $database->loadColumn();
	 	$configs = $configs['0'];
		@$configs = @unserialize($configs);
		
		if(!isset($configs['jquery_front']) || @$configs['jquery_front'] == "0"){
			$document->addScript( JURI::root()."components/com_adagency/includes/js/jquery.js" );
		}
		
		JHtml::_('jquery.framework');
		$document->addScript(JURI::root() . 'components/com_adagency/includes/js/domready.js');
		require_once($mosConfig_absolute_path."/components/com_adagency/helpers/helper.php");

		if( (!empty( $_SERVER['HTTPS'] ) && strtolower( $_SERVER['HTTPS'] ) != 'off' || isset( $http_host[1] ) && $http_host[1] == 443) && substr( $mosConfig_live_site, 0, 8 ) != 'https://' ) {
			$mosConfig_live_site1 = 'https://'.substr( $mosConfig_live_site, 7 );
		}
		else{
			$mosConfig_live_site1 = $mosConfig_live_site;
		}

		if(!class_exists('TableadagencyAds')){
			include_once($mosConfig_absolute_path."/components/com_adagency/tables/adagencyads.php");
		}
		require_once($mosConfig_absolute_path."/components/com_adagency/helpers/stats_count.php");
		require_once($mosConfig_absolute_path."/administrator/components/com_adagency/helpers/jomsocial.php");

		$sql = "SELECT * FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$globalSettings = $db->loadObject();

		$sql = "SELECT * FROM #__ad_agency_zone WHERE zoneid=".$module->id." LIMIT 1";
		$db->setQuery($sql);
		$zoneSettings = $db->loadObject();
		@$zoneSettings->adparams = @unserialize($zoneSettings->adparams);
		@$zoneSettings->textadparams = @unserialize($zoneSettings->textadparams);
		
		if(!isset($zoneSettings->adparams['width'])) {
			$zoneSettings->adparams['width'] = NULL;
		}
		if(!isset($zoneSettings->adparams['height'])) {
			$zoneSettings->adparams['height'] = NULL;
		}

		$obj = new stdClass();
		$obj->banner_id = $zoneSettings->defaultad;
		$defaultad[0] = $obj;

		$sql = "select `approved` from #__ad_agency_banners where `id`=".intval($zoneSettings->defaultad);
		$db->setQuery($sql);
		$db->query();
		$approved = $db->loadColumn();
		@$approved = $approved["0"];
		
		if($approved == "N"){
			$defaultad = array();
		}

		$limit_ip = $globalSettings->limit_ip;
		$settings = $globalSettings->lastsend;
		$ad_agency_folder = $globalSettings->imgfolder;

		$default = time();
		if(!isset($settings)||($settings == NULL)){
			$database->setQuery("INSERT INTO #__ad_agency_settings (`email_report`,`lastsend`) VALUES ('','$default')");
			$database->query();
		}

		if(!isset($settings)||($settings==NULL)){
			$settings=0;
		}
		
		$settings = strtotime(date("Y-m-d", $settings));
		$time = strtotime(date("Y-m-d", time()));
		
		if($settings < $time){
			$database->setQuery("SELECT `aid` FROM #__ad_agency_advertis WHERE `approved`='Y'");
			$aids = $database->loadObjectList();

			if(isset($aids)){
				foreach ($aids as $aid) {
					$database->setQuery("SELECT `lastreport`,`user_id`,`email_daily_report`,`email_weekly_report`,`email_month_report`,`email_campaign_expiration`,`weekreport`,`monthreport` FROM #__ad_agency_advertis WHERE `aid`=$aid->aid");
					$useradv = $database->loadObjectList();
					$newtime = time();
					
					$database->setQuery("UPDATE #__ad_agency_settings SET `lastsend`='".$newtime."'");
					$database->query();

					foreach($useradv as $users){
						if($users->email_daily_report == "Y"){
							if(($users->lastreport + 3600*24) < time()){
								$impression = impression($aid->aid,$users->lastreport);
								$clicks = clicks($aid->aid,$users->lastreport);
								sendreport($users->user_id, $clicks, $impression,$users->lastreport);
							}
						}
				
						if($users->email_weekly_report == "Y") {
							if (($users->weekreport + 3600*24*7) < time() ) {
								$impression = impression($aid->aid, $users->weekreport);
								$clicks = clicks($aid->aid, $users->weekreport);
								sendreport($users->user_id, $clicks, $impression,$users->weekreport);
							}
						}
						
						if ($users->email_month_report == "Y") {
							if (($users->monthreport + 3600*24*30) < time() ) {
								$impression = impression($aid->aid,$users->monthreport);
								$clicks = clicks($aid->aid,$users->monthreport);
								sendreport($users->user_id, $clicks, $impression,$users->monthreport);
							}
						}
						
						if ($users->email_campaign_expiration == "Y") {
							exp_notices($users->user_id);
						}
						else{
							exp_notices($users->user_id, false);
						}
					}//end foreach
				} //end foreach
			}

		}
		
		if(isset($_SERVER['HTTP_USER_AGENT'])&&checkbot($_SERVER['HTTP_USER_AGENT'])!=1){
			$valid_c = array();
			array_push( $valid_c, 0 );
			
			
			$config = JFactory::getConfig();
			$siteOffset = $config->get('offset');
			$jnow = JFactory::getDate('now', $siteOffset);
			
			$sql = "SELECT id FROM #__ad_agency_banners WHERE (media_type='Transition' OR media_type='Floating') AND ad_start_date <= '".$jnow."' AND (ad_end_date >= '".$jnow."' OR ad_end_date = '0000-00-00 00:00:00') AND `approved`='Y'";
			$database->setQuery($sql);						
			$ids = $database->loadColumn();
						
			if ( $database->getErrorMsg() ) {
				die( 'SQL error' );
			}

			foreach($ids as $sid){
				$cookie_name = "spl".md5($sid);
				if(isset($_COOKIE[$cookie_name])){
					array_push($valid_c, $sid);
				}
			}
						
			$valid_cookie = implode(",", $valid_c);
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
			$bannernr = $zoneSettings->banners;

			if(isset($_GET['Itemid'])) {
				$the_item_id = intval($_GET['Itemid']);
			}
			else{
				$the_item_id = NULL;
			}
			
			if(!isset($the_item_id)||($the_item_id==0)) {
				$the_item_id=NULL;
			}
			else {
				$it_id_con="&Itemid=".intval($the_item_id);
			}

			$bannernr_cols = $zoneSettings->banners_cols;
			$bannernr_rows = $bannernr;
			$rotator_info = $zoneSettings;

			if(isset($rotator_info)&&($rotator_info != NULL)) {
				// v.1.5.3 - adding "advertise here" link - start
				$target = '';

				if($rotator_info->link_taketo == 0){
					$link = JRoute::_('index.php?option=com_adagency&controller=adagencyPackages'.$it_id_con);
				}
				elseif($rotator_info->link_taketo == 1){
					$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=register'.$it_id_con);
				}
				elseif($rotator_info->link_taketo == 3){
					$link = JRoute::_('index.php?option=com_adagency&controller=adagencyAdvertisers&task=overview'.$it_id_con);
				}
				else{
					$link = $rotator_info->taketo_url;
					$target = 'target="_blank"';
				}

				$adv_here_top = '';
				$adv_here_bottom = '';
				$keywords		=  $document->getMetaData( 'keywords' );
				
				// Check if zone is showing ads by keywords and add sql condition
				$keyws = $zoneSettings->keywords;

				$sql_keywords2 = "";
				if(isset($keyws)&&($keyws==1)){
					$countkeys=0;
					$sql_keywords1 = "b.keywords, ";
					$sql_keywords2 .= " MATCH (b.`keywords`) AGAINST ('".trim(addslashes($keywords))."' IN BOOLEAN MODE) ";
				}
				else{
					$sql_keywords1 = NULL;
				}
		
				if ($sql_keywords2!="") {
					$sql_keywords2 = "AND (".$sql_keywords2.") ";
				}

				if($rotator_info->show_adv_link == 1){
					$adv_here_top = '';
					$adv_here_bottom = '<div class="adg_row adg_adv_link"><div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>'.JText::_('ADVERTISE_HERE').'</a></div></div>';
				}
				elseif($rotator_info->show_adv_link == 2){
					$adv_here_top = '<div class="adg_row adg_adv_link"><div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>'.JText::_('ADVERTISE_HERE').'</a></div></div>';
					$adv_here_bottom = '';
				}
				elseif($rotator_info->show_adv_link == 3)	{
					$adv_here_top = '<div class="adg_row adg_adv_link"><div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>'.JText::_('ADVERTISE_HERE').'</a></div></div>';
					$adv_here_bottom = '<div class="adg_row adg_adv_link"><div align="center" class="adv_here"><a class="adv_here_link" href="'.$link.'" '.$target.'>'.JText::_('ADVERTISE_HERE').'</a></div></div>';
				}

				$bannernr = ($bannernr > 0) ? $bannernr : 1;

				// v.1.5.3 - adding rows + cols for banners - start
				$bannernr = $bannernr * $bannernr_cols;
				// v.1.5.3 - adding rows + cols for banners - stop

				$offset = JFactory::getApplication()->getCfg('offset');
				$today = JFactory::getDate('now', $offset);
				$dateok = $today->toSql(true);
				$find_add = false;

				if($rotator_info->rotatebanners == 0){ // we don't have a ROTATOR
					if($rotator_info->rotaterandomize == 1){
						$order_by_in_static = "rw";
					}
					else{
						$order_by_in_static = "b.ordering";
					}
					
					$config = JFactory::getConfig();
					$siteOffset = $config->get('offset');
					$jnow = JFactory::getDate('now', $siteOffset);
					
					$sql = "SELECT t0.id, t0.aid, t0.type, b.zone, b.advertiser_id, b.approved,b.channel_id,".$sql_keywords1." cb.campaign_id, cb.banner_id, FLOOR(RAND() * relative_weighting) AS rw
							FROM #__ad_agency_campaign_banner AS cb
							LEFT OUTER JOIN #__ad_agency_banners AS b ON b.id = cb.banner_id
							LEFT JOIN #__ad_agency_campaign AS t0 ON cb.campaign_id = t0.id
							LEFT JOIN #__ad_agency_order_type AS p ON t0.otid = p.tid
							LEFT JOIN #__ad_agency_advertis AS a ON b.advertiser_id = a.aid
							WHERE a.approved = 'Y' AND cb.zone='".intval($module->id)."' AND b.approved='Y'
							AND ((t0.approved='Y') AND (t0.approved='Y' AND ('{$dateok}' > t0.start_date) AND ((t0.type IN ('cpm','pc') AND t0.quantity>0) OR (t0.type='fr' AND '".$jnow."' < t0.validity) OR (t0.type='in' AND '".$jnow."' < t0.validity) )))
							AND b.`approved`='Y'
							AND b.ad_start_date <= '".$jnow."'
							AND (b.ad_end_date >= '".$jnow."' OR b.ad_end_date = '0000-00-00 00:00:00')
							AND t0.status='1' AND cb.banner_id NOT IN (".$valid_cookie.") ".$sql_keywords2." ORDER BY ".$order_by_in_static." ASC";
					$database->setQuery($sql);
					$tst = $database->loadObjectList();
					$tbnrs = diversify($tst);
					$loaded_banners = getBanners($tbnrs,$database);
					$geocount = 0;

					$cids = array();
					if(isset($tbnrs)){
						foreach($tbnrs as $element){
							if(isset($element->channel_id)&&($element->channel_id != NULL)){
								$cids[] = $element->channel_id;
							}
						}
					}
		
					$cids = array_unique($cids);
					$inc = ",".implode(',',$cids);
			
					if($inc == ","){
						$inc = "";
					}

					$sql ="SELECT `channel_id` AS id,`type`,`logical`,`option`,`data` FROM #__ad_agency_channel_set WHERE channel_id IN (0".$inc.") ORDER BY id ASC";
					$database->setQuery($sql);
					$loaded_channels = $database->loadObjectList();

					$numrows=count($tbnrs);
					$jomSocial = new JomSocialTargeting();
		
					if(($numrows)||(isset($defaultad[0]->banner_id)&&($defaultad[0]->banner_id!=0))){
						if($numrows){
							$banners = $tbnrs;
							// start check add visibility ---------------------------------------
							$logged_user = JFactory::getUser();
							if(intval($logged_user->id) > 0){
								if(isset($banners) && count($banners) > 0 && $jomSocial->exists()){
									$temp = array();
									foreach($banners as $key=>$value){
										if($jomSocial->visible($value->banner_id)){
											$temp[] = $banners[$key];
										}
									}
									$banners = $temp;
								}
							}
							// stop check add visibility ----------------------------------------
						}
						else{
							$banners=$defaultad;
							$dfa_bool=true;
						}
			
						$cellpadding = $zoneSettings->cellpadding;

						if(count($banners) > $zoneSettings->banners_cols){
							$span = floor(12 / $zoneSettings->banners_cols);
						}
						else{
							if(count($banners) == 5){
								$span = "Special";
							}
							else{
								$span = 12;
								if(count($banners) > 0){
									$span = floor(12 / count($banners));
								}
							}
						}
						$output_this= '<div class="adg_row adg_banners"><div class="avd_display_block adg_table clearfix">';
						$i = 0;	
		
						for($rows_nr =0; $rows_nr < $zoneSettings->banners; $rows_nr++ ){
							$output_this.='<div class="adg_row adg_table_row ">';
							for($col_nr =0; $col_nr < $zoneSettings->banners_cols; $col_nr++ ){
								back_for_geo:
								if(!isset($banners[$i]->banner_id)){
									break;
								}
								else{
									$output_this.='<div style="padding:'.$cellpadding.'px;"  class="adg_cell adg_table_cell span'.$span.' "><div><div>'; $banner_id = $banners[$i]->banner_id;
								}
							
								if($banner_id) {
									if($numrows == 0){
										$banner_row = new TableadagencyAds($database);
										$banner_row->load( $banner_id );
									}
									else{
										$banner_row = loadBannerById($banner_id,$loaded_banners);
									}
				
									$banner_row->parameters = @unserialize($banner_row->parameters);
								
									if(($banner_row->channel_id != NULL)&&(intval($banner_row->channel_id) >0)&&(!isset($dfa_bool))){
										if(!geo(loadChannelById($banner_row->channel_id, $loaded_channels), $globalSettings->cityloc)){
											$i++;
											$output_this .= '</div></div></div>';
											goto back_for_geo;
											continue;
										}
									}
				
									if(!isset($dfa_bool)){
										ImpressionCalc($banners[$i], $banner_row,$real_ip,$limit_ip);
									}
									else{
										$link_dfa=$banner_row->target_url;
										$banners[$i]->aid=$banner_row->advertiser_id;
										ImpressionCalc($banners[$i], $banner_row,$real_ip,$limit_ip);
									}
				
									if(($zoneSettings->adparams['width'] == NULL && $zoneSettings->adparams['height'] == NULL) || (intval($zoneSettings->adparams['width']) == intval($banner_row->width) && intval($zoneSettings->adparams['height']) == intval($banner_row->height)) ){			
										if(!isset($link_dfa)&&!isset($dfa_bool)){
											$track_link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
										}
							
										switch ($banner_row->media_type) {
											case 'Standard':
												$max_width = $banner_row->width.'px';
												$max_height = $banner_row->height.'px';
												$imageurl=$mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url;
												
												if(!isset($link_dfa)){
													$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
												}
												else{
													$link=$link_dfa;
												}
		
												//border and color
												if(isset($banner_row->parameters['border']) && ($banner_row->parameters['border']>0)) {
													$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
												}
												else{
													$table_style="border: none;";
												}
												$bg_color="";
												
												if(isset($banner_row->parameters['bg_color'])&& ($banner_row->parameters['bg_color']!="")) {
													$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
												}
		
												//td padding
												$padding="";
												if(isset($banner_row->parameters['padding'])&& ($banner_row->parameters['padding']>0)) {
													$padding="padding: ".$banner_row->parameters['padding']."px;";
												}
								
												if(isset($banner_row->parameters['align'])) {
													if($banner_row->parameters['align'] !== '0') {
														$banner_row->parameters['align'] = 'align="'.$banner_row->parameters['align'].'"';
													}
													elseif ($banner_row->parameters['align'] === '0') {
														$banner_row->parameters['align'] = NULL;
													}
												}
												
												if(!isset($banner_row->parameters['target_window'])){
													$banner_row->parameters['target_window'] = "_blank";
												}
												
												if(!isset($banner_row->parameters['alt_text'])){
													$banner_row->parameters['alt_text'] = NULL;
												}
												
												if(!isset($banner_row->parameters['align'])){
													$banner_row->parameters['align'] = NULL;
												}
		
												$output_this.= '<div style="'.$padding.'" class="adv_standard_d"><div style="max-width:'.$max_width.'; max-height:'.$max_height.'; '.$bg_color.'"><div><div><a class="standard_adv_link" href="'. $link .'" target="'.$banner_row->parameters['target_window'].'"><img style="'.$table_style.'" class="standard_adv_img" src="'. $imageurl .'" border="0" title="'.$banner_row->parameters['alt_text'].'" alt="'.$banner_row->parameters['alt_text'].'" /></a></div></div></div></div>';
												break;
		
											case 'TextLink':
												$max_width = $banner_row->width.'px';
												$max_height = $banner_row->height.'px';
												$thumb = NULL;
												
												if(isset($banners[$i]->id) && isset($banner_row->id)){
													$sql = "SELECT thumb FROM #__ad_agency_campaign_banner WHERE campaign_id = ".$banners[$i]->id." AND banner_id = ".$banner_row->id." LIMIT 1";
													$db->setQuery($sql);
													$thumb = $db->loadColumn();
													$thumb = $thumb["0"];
													
													if(($thumb != NULL)&&(strlen($thumb)>4)) {
														$banner_row->image_url = $thumb;
													}
												}
												
												$img_siz = NULL;
												if (isset($banner_row->image_url)&&($banner_row->image_url!='')) {
													$txtimageurl=$mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url;
													$img_siz = @getimagesize($mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url);
													
													if(isset($img_siz[0])) {
														$img_siz[0]+=10; $img_siz = $img_siz[0]."px;";
													}
												}
								
												if($zoneSettings->textadparams['mxtype'] =="w"){
													$mindim = 'max-width:'.$zoneSettings->textadparams['mxsize'].'px !important;';
												}
												else{
													$mindim = 'max-height:'.$zoneSettings->textadparams['mxsize'].'px !important;';
												}
								
												$img_style = '';
												$br = '';
												
												if(isset($txtimageurl)&&($txtimageurl!=NULL)) {
													if(isset($zoneSettings->textadparams['ia'])) {
														if($zoneSettings->textadparams['ia'] == 'l') {
															$img_style = "float:left; padding: 5px;";
															$img_siz = "margin-left:".$img_siz;
														}
														elseif($zoneSettings->textadparams['ia'] == 'r'){
															$img_style = "float:right; padding: 5px;";
															$img_siz = "margin-right:".$img_siz;
														}
														else{
															$br = "<br />";
														}
													}
													$imagetxtcode='<img class="standard_adv_img" src="'. $txtimageurl .'" style="'.$mindim.' '.$img_style.'" border="0" title="'.$banner_row->parameters['img_alt'].'" alt="'.$banner_row->parameters['img_alt'].'" />';
												}
												else{
													$imagetxtcode='';
												}
		
												if(isset($zoneSettings->textadparams["wrap_img"])&&($zoneSettings->textadparams["wrap_img"] == '1')){
													$img_siz = NULL;
												}
												
												if(!isset($banner_row->parameters['border'])){
													$banner_row->parameters['border'] = NULL;
												}
												
												if(!isset($banner_row->parameters['bg_color'])){
													$banner_row->parameters['bg_color'] = NULL;
												}
												
												if(!isset($banner_row->parameters['title_color'])){
													$banner_row->parameters['title_color'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_weight'])){
													$banner_row->parameters['font_weight'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_weight_a'])){
													$banner_row->parameters['font_weight_a'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_weight_b'])){
													$banner_row->parameters['font_weight_b'] = NULL;
												}
												
												if(!isset($banner_row->parameters['padding'])){
													$banner_row->parameters['padding'] = NULL;
												}
												
												if(!isset($banner_row->parameters['align'])){
													$banner_row->parameters['align'] = NULL;
												}
												
												if(!isset($banner_row->parameters['target_window'])){
													$banner_row->parameters['target_window'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_family'])){
													$banner_row->parameters['font_family'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_family_a'])){
													$banner_row->parameters['font_family_a'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_family_b'])){
													$banner_row->parameters['font_family_b'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_size'])){
													$banner_row->parameters['font_size'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_size_a'])){
													$banner_row->parameters['font_size_a'] = NULL;
												}
												
												if(!isset($banner_row->parameters['font_size_b'])){
													$banner_row->parameters['font_size_b'] = NULL;
												}
		
												if(!isset($link_dfa)){
													$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
												}
												else{
													$link=$link_dfa;
												}
							
												if ($banner_row->parameters['border']>0) {
													$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
												}
												else {
													$table_style="border: none;";
												}
												
												$bg_color="";
												
												if ($banner_row->parameters['bg_color']!="") {
													$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
												}
												
												$title_color="";
												if($banner_row->parameters['title_color']!=""){
													$title_color='style="color: #'.$banner_row->parameters['title_color'].';"';
												}
												
												$body_color="";
												
												if(@$banner_row->parameters['body_color']!=""){
													$body_color="color: #".$banner_row->parameters['body_color'];
												}
												
												$action_color="";
												
												if(@$banner_row->parameters['action_color']!=""){
													$action_color='style="color: #'.$banner_row->parameters['action_color'].';"';
												}
												
												$underlined="";
												$font_weight="";
												$isUnderlined=strstr($banner_row->parameters['font_weight'],'underlined');
								
												if($isUnderlined=='underlined'){
													$str_length=strpos($banner_row->parameters['font_weight'],'underlined');
													$font_weight=substr($banner_row->parameters['font_weight'],0,$str_length);
													$underlined="text-decoration:underline;";
												}
												else{
													$font_weight=$banner_row->parameters['font_weight'];
												}
		
												$underlined_a="";
												$font_weight_a="";
												$isUnderlined_a=strstr($banner_row->parameters['font_weight_a'],'underlined');

												if($isUnderlined_a=='underlined'){
													$str_length_a=strpos($banner_row->parameters['font_weight_a'],'underlined');
													$font_weight_a=substr($banner_row->parameters['font_weight_a'],0,$str_length_a);
													$underlined_a="text-decoration:underline;";
												}
												else{
													$font_weight_a=$banner_row->parameters['font_weight_a'];
												}
		
												$underlined_b="";
												$font_weight_b="";
												$isUnderlined_b=strstr($banner_row->parameters['font_weight_b'],'underlined');
												
												if($isUnderlined_b=='underlined'){
													$str_length_b=strpos($banner_row->parameters['font_weight_b'],'underlined');
													$font_weight_b=substr($banner_row->parameters['font_weight_b'],0,$str_length_b);
													$underlined_b="text-decoration:underline;";
												}
												else{
													$font_weight_b=$banner_row->parameters['font_weight_b'];
												}
		
												$padding="";
												if ($banner_row->parameters['padding']>0){
													$padding="padding: ".$banner_row->parameters['padding']."px;";
												}
								
												$sizeparam='px';
												
												if(isset($zoneSettings->adparams['width'])&&($zoneSettings->adparams['width'] != NULL)){
													$banner_row->width = $zoneSettings->adparams['width'];
												}
												
												if(isset($zoneSettings->adparams['height'])&&($zoneSettings->adparams['height'] != NULL)){
													$banner_row->height = $zoneSettings->adparams['height'];
												}
												
												$width = ($banner_row->width > 0) ? ' max-width:'.$banner_row->width.'px;':'';
												$height = ($banner_row->height > 0) ? 'max-height:'.$banner_row->height.'px;':'';
		
								
												if(isset($banner_row->parameters['alt_text'])){
													$banner_row->parameters['alt_text'] = str_replace('<img', '<img onclick="window.location = \''.$link.'\';"', $banner_row->parameters['alt_text']);
												}
		
												$output_this.= '<div class="textlink_adv" align="'.$banner_row->parameters['align'].'" style="overflow: hidden; text-align:'.$banner_row->parameters['align'].'; '.$width.' '.$height.' '.$table_style.' '.$bg_color.' '.$padding.'">
														<a target="'. $banner_row->parameters['target_window'] .'" href="'. $link .'" '.$title_color.'>
															<span style="font-family: '. $banner_row->parameters['font_family'] .'; font-size: '. $banner_row->parameters['font_size'] .'px; font-weight: '. $font_weight.'; '.$underlined.'">
																<font face="'.$banner_row->parameters['font_family'].'">'.@$banner_row->parameters['alt_text_t'].'</font></span></a>
																<br /><div class="imgdiv2"><a target="'. $banner_row->parameters['target_window'] .'" href="'. $link .'" '.$title_color.'>'.$imagetxtcode.'</a></div>
														<div class="tbody" style="font-family: '. $banner_row->parameters['font_family_b'] .'; '.$img_siz.' font-size: '. $banner_row->parameters['font_size_b'] .'px; font-weight: '. $font_weight_b.';'.$underlined_b." ".$body_color.';">'.@$banner_row->parameters['alt_text'].'</div>
															<a class="textlink_adv_link" href="'. $link .'" target="'. $banner_row->parameters['target_window'] .'" '.$action_color.'>
																<div style="word-break: break-all; font-family: '. $banner_row->parameters['font_family_a'] .'; font-size: '. $banner_row->parameters['font_size_a'] .'px; font-weight: '. $font_weight_a .';'.$underlined_a.'">'.@$banner_row->parameters['alt_text_a'].'</div></a>
														</div>';
												$txtimageurl = NULL;
												break;
		
											case 'Flash':
												$max_width = $banner_row->width.'px';
												$max_height = $banner_row->height.'px';
												
												if(!isset($link_dfa)){
													$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
												}
												else{
													$link=$link_dfa;
												}
												
												if(isset($zoneSettings->adparams['width']) && ($zoneSettings->adparams['width'] != NULL)){
													$banner_row->width = $zoneSettings->adparams['width'];
												}
												
												if(isset($zoneSettings->adparams['height']) && ($zoneSettings->adparams['height'] != NULL)){
													$banner_row->height = $zoneSettings->adparams['height'];
												}
		
												//border and color
												if ($banner_row->parameters['border']>0) {
													$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
												}
												else {
													$table_style="border: none;";
												}
												
												$bg_color="";
												
												if ($banner_row->parameters['bg_color']!="") {
													$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
												}
		
												//td padding
												$padding="";
												if ($banner_row->parameters['padding']>0) {
													$padding="padding: ".$banner_row->parameters['padding']."px;";
												}
												
												///changes in adding flash objects
		
												$onevent = "onclick";
												if(isset($banner_row->parameters['target_window'])&&($banner_row->parameters['target_window'] == '_self')) {
													$js_open = 'document.location.href=\''.urldecode($link).'\';';
													$flash_target = '_self';
												}
												else{
													$flash_target = '_blank';
													$js_open = 'javascript:window.open(\''.urldecode($link).'\')';
												}
												if(isset($_SERVER['HTTP_USER_AGENT'])&&(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")>1)){
													$onevent = "onmousedown";
												}
		
												$flashurl = $mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->swf_url;
												
												/*$adflash = '<EMBED SRC="'.$flashurl.'" width=' . $banner_row->width . ' height='.$banner_row->height.' QUALITY="high" wmode="transparent" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>';
												$output_this.= '<div class="adv_flash" align="'.$banner_row->parameters['align'].'" style="'.$table_style.' '.$bg_color.' max-width:'.$banner_row->width.'px; max-height:'.$banner_row->height.'px; ">';
												$output_this.= '<div class="adv_flash_t" style="max-width:'.$max_width.'; max-height:'.$max_height.';"><div><div >';
												$output_this.= '<a href="' . $link . '" target="' . $flash_target . '"><img alt="" src="' . JURI::root() . 'components/com_adagency/images/trans.gif" style="height: ' . $banner_row->height . 'px; width: ' . $banner_row->width . 'px; float: left;"></a>';
												$output_this.= '</div></div></div>'.$adflash;
												$output_this.= '</div>';*/
												
												//---------------------------------------------------------------------
												$output_this .= '<div style="text-align:center; margin:0 auto; '.$table_style.' '.$bg_color.' max-width:'.$banner_row->width.'px; max-height:'.$banner_row->height.'px; " align="'.$banner_row->parameters['align'].'"><a href="'.$link.'" target="' . $flash_target . '" style="display:inline-block; width:100%;"><iframe src="'.JURI::root().'index.php?option=com_adagency&controller=adagencyAds&task=loadflash&url='.urlencode($flashurl).'&width='.$banner_row->width.'&height='.$banner_row->height.'&tmp=component&format=raw'.'" style="width:'.intval($banner_row->width).'px; height:'.intval($banner_row->height).'px; border:none; pointer-events: none;" scrolling="no" seamless="seamless"></iframe></a></div>';
												//---------------------------------------------------------------------
												
												break;
												
											case 'Advanced':
												$original_width = $zoneSettings->adparams['width'];
												$original_height = $zoneSettings->adparams['height'];
												$timestamp = mt_rand( 10000 , mt_getrandmax() );
												$toreplace = "ord=[timestamp]";
												$replacewith = "ord=".$timestamp;
												$max_width = $banner_row->width.'px';
												$max_height = $banner_row->height.'px';
												
												if(!isset($link_dfa)&&!isset($dfa_bool)){
													$link = JURI::root().'index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.$banners[$i]->id.'&bid='.$banner_row->id.'&aid='.$banners[$i]->aid;
												}
												else{
													$link = JURI::root().$link_dfa;
												}
												
												if(isset($zoneSettings->adparams['width'])&&isset($zoneSettings->adparams['height'])&&($zoneSettings->adparams['width'] != NULL)&&($zoneSettings->adparams['height'] != NULL)) {
													$zoneSettings->adparams['width']+= 5;$zoneSettings->adparams['height']+= 5;
													$style_adv = ' style="overflow:hidden; max-width:'.$zoneSettings->adparams['width'].'px; max-height:'.$zoneSettings->adparams['height'].'px;" ';
												}
												else{
													$style_adv = NULL;
												}
												
												if(!isset($banner_row->parameters['target_window'])){
													$banner_row->parameters['target_window'] = NULL;
												}
												
												if(strpos(" ".strtolower($banner_row->ad_code), "<a href") == 1){
													$banner_row->ad_code = str_replace($toreplace , $replacewith , $banner_row->ad_code);
													$output_this.= '<div class="adv_aff" '.$style_adv.'><div class="adv_advanced_t" style="max-width:'.$max_width.'; max-height:'.$max_height.';"><div><div>'.str_replace('ad_url"',$link.'" target="'.$banner_row->parameters['target_window'].'"',$banner_row->ad_code).'</div></div></div></div>';
												}
												elseif(strpos(" ".strtolower($banner_row->ad_code), "<iframe") == 1){
													$banner_row->ad_code = str_replace($toreplace , $replacewith , $banner_row->ad_code);
													
													$banner_row->ad_code = str_replace('<iframe ' , '<iframe style="pointer-events:none;" ' , $banner_row->ad_code);
													
													$output_this.= '<div class="adv_aff" '.$style_adv.'><div class="adv_advanced_t" style="max-width:'.$max_width.'; max-height:'.$max_height.';"><div><div><a style="display:block;" href="'. $link .'" target="'.$banner_row->parameters['target_window'].'">'.$banner_row->ad_code.'</a></div></div></div></div>';
												}
												else{
													$banner_row->ad_code = str_replace($toreplace , $replacewith , $banner_row->ad_code);
													$output_this.= '<div class="adv_aff" '.$style_adv.'><div class="adv_advanced_t" style="max-width:'.$max_width.'; max-height:'.$max_height.';"><div><div><div style="float:left; width: 100%; z-index: 10000;" onclick="javascript:countClicks(\''.$link.'\')">'.$banner_row->ad_code.'</div></div></div></div></div>';
												}
												
												$zoneSettings->adparams['width'] = $original_width;
												$zoneSettings->adparams['height'] = $original_height;					
												break;
											
											case 'Popup':
												$popupcode=str_replace('ad_url/','ad_url',$banner_row->ad_code);
								
												//html
												if ($banner_row->parameters["window_type"]=='popup') {
													$crt=1;
													$adcontent="";
													$string='ad_url"';
													$fisier=$popupcode;
													$pos1=strpos($fisier,$string);
													$poz=0;
													
													while ($pos1){
														if(!isset($link_dfa)){
															$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid).'&lid='.intval($crt);
														}
														else{
															$link=$link_dfa;
														}
														
														$cont=substr($fisier,$poz,$pos1+strlen($string));
														$adcontent.=str_replace('ad_url"',$link.'" target="_blank"',$cont);
														$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
														$crt++;
														$pos1=strpos($fisier,$string);
													}
		
													$adcontent.=$fisier;
													$adcode = str_replace('color="','color="#',$adcontent);
													$output_this.= $adcode;
												}
												else{
													$popup=str_replace('ad_url',$banner_row->target_url,$popupcode);
													$output_this.= $popup;
												}
												break;
							
											case 'Floating':
												$crt=1;
												$adcontent="";
												$string='ad_url"';
												$banner_row->ad_code = stripslashes($banner_row->ad_code);
												$fisier=str_replace('ad_url/','ad_url',$banner_row->ad_code);
												$pos1=strpos($fisier,$string);
												$poz=0;
												
												while ($pos1) {
													if(!isset($link_dfa)){
														$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid).'&lid='.intval($crt);
													}
													else{
														$link=$link_dfa;
													}
													
													$cont=substr($fisier,$poz,$pos1+strlen($string));
													$adcontent.=str_replace('ad_url"',$link.'" target="_blank"',$cont);
													$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
													$crt++;
													$pos1=strpos($fisier,$string);
												}
												
												$cookie_value = ".";
												$frequency = $banner_row->frequency;
												$cookie_name = "spl".md5( $banner_row->id );
												
												if ($frequency == "1" || $frequency == "7" || $frequency == "30" || $frequency == "365") {
							?>
													<script type="text/javascript">
														function SetCookie(cookieName,cookieValue,nDays) {
															var today = new Date();
															var expire = new Date();
															if (nDays==null || nDays==0) nDays=1;
															expire.setTime(today.getTime() + 3600000*24*nDays);
															document.cookie = cookieName+"="+escape(cookieValue)
															+ "; path=/; expires="+expire.toGMTString();
														}
													</script>
													<script type="text/javascript">SetCookie('<?php echo $cookie_name;?>', '<?php echo $cookie_value;?>','<?php echo $frequency;?>');</script>
							<?php 
												}
												
												$adcontent.=$fisier;
												$adcode = str_replace('color="','color="#',$adcontent);
												$adcode = str_replace("var ie=document.all;","try{ var ie=document.all; } catch(err){ }",$adcode);
												$output_this.= $adcode;
												break;
											
											case 'Transition':
												$crt=1;
												$adcontent="";
												$string='ad_url"';
												$banner_row->ad_code = stripslashes($banner_row->ad_code);
												$fisier=str_replace('ad_url/','ad_url',$banner_row->ad_code);
												$pos1=strpos($fisier,$string);
												$poz=0;
												
												while ($pos1) {
													if(!isset($link_dfa)){
														$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid).'&lid='.intval($crt);
													}
													else{
														$link=$link_dfa;
													}
													
													$cont=substr($fisier,$poz,$pos1+strlen($string));
													$adcontent.=str_replace('ad_url"',$link.'" target="_blank"',$cont);
													$fisier=substr($fisier,$pos1+strlen($string),strlen($fisier));
													$crt++;
													$pos1=strpos($fisier,$string);
												}
						
												$cookie_value = ".";
												$frequency = $banner_row->frequency;
												$cookie_name = "spl".md5( $banner_row->id );
												
												if ($frequency == "1" || $frequency == "7" || $frequency == "30" || $frequency == "365"){
										?>
													<script type="text/javascript">
														function SetCookie(cookieName,cookieValue,nDays) {
															var today = new Date();
															var expire = new Date();
															if (nDays==null || nDays==0) nDays=1;
															expire.setTime(today.getTime() + 3600000*24*nDays);
															document.cookie = cookieName+"="+escape(cookieValue)
															+ "; path=/; expires="+expire.toGMTString();
														}
													</script>
													<script type="text/javascript">SetCookie('<?php echo $cookie_name;?>', '<?php echo $cookie_value;?>','<?php echo $frequency;?>');</script>
								<?php  
												}
												$adcontent.=$fisier;
												$adcode = str_replace('color="','color="#',$adcontent);
												$output_this.= $adcode;
												break;
										}
									}
								}
								$output_this.='</div></div></div>';
								$i++;
								$find_add = true;
							}
							$output_this.='</div>';
						}
						$output_this.= '</div></div>';
						
						if(!$find_add){
							//$output_this.= '</div></div>';
						}
					} // andif - there are banners
				}
				else{// we have a rotator
					if(intval(JVERSION) >= 3){
						$dateok = $jnow->toSql(true);
					}
					else{
						$dateok = $jnow->toMySQL(true);
					}

					if($rotator_info->rotaterandomize == 1){
						$order_by_in_static = "rw";
					}
					else{
						$order_by_in_static = "b.ordering";
					}

					$config = JFactory::getConfig();
					$siteOffset = $config->get('offset');
					$jnow = JFactory::getDate('now', $siteOffset);

					$sql = "SELECT t0.*, b.zone, b.approved, b.ordering, ".$sql_keywords1." cb.campaign_id, cb.banner_id, FLOOR(RAND() * relative_weighting) AS rw
							FROM #__ad_agency_campaign_banner AS cb
							LEFT OUTER JOIN #__ad_agency_banners AS b ON b.id = cb.banner_id
							LEFT JOIN #__ad_agency_campaign AS t0 ON cb.campaign_id = t0.id
							LEFT JOIN #__ad_agency_order_type AS p ON t0.otid = p.tid
							LEFT JOIN #__ad_agency_advertis AS a ON b.advertiser_id = a.aid
							WHERE a.approved = 'Y' AND cb.zone='".$module->id."' AND b.approved='Y'
							AND b.ad_start_date <= '".$jnow."'
							AND b.`approved`='Y'
							AND (b.ad_end_date >= '".$jnow."' OR b.ad_end_date = '0000-00-00 00:00:00')
							AND ((t0.approved='Y') AND (t0.approved='Y' AND ('{$dateok}' > t0.start_date) AND ((t0.type IN ('cpm','pc') AND t0.quantity>0) OR (t0.type='fr' AND '".$jnow."' < t0.validity) OR (t0.type='in' AND '".$jnow."' < t0.validity) ))) AND t0.status='1' AND cb.banner_id NOT IN (".$valid_cookie.") AND (b.media_type = 'Standard' OR b.media_type = 'TextLink' OR b.media_type = 'Flash' OR b.media_type = 'Advanced' OR b.media_type = 'Transition') ".$sql_keywords2."
							GROUP BY b.id
							ORDER BY ".$order_by_in_static." ASC";
					$database->setQuery( $sql );
					$banners = diversify($database->loadObjectList());

                    if(is_array($banners) && count($banners) == 0){
                        if(isset($zoneSettings->defaultad) && intval($zoneSettings->defaultad) != 0){
                            $sql = "SELECT t0.*, b.zone, b.approved, b.ordering, ".$sql_keywords1." cb.campaign_id, cb.banner_id, FLOOR(RAND() * relative_weighting) AS rw
							FROM #__ad_agency_campaign_banner AS cb
							LEFT OUTER JOIN #__ad_agency_banners AS b ON b.id = cb.banner_id
							LEFT JOIN #__ad_agency_campaign AS t0 ON cb.campaign_id = t0.id
							LEFT JOIN #__ad_agency_order_type AS p ON t0.otid = p.tid
							LEFT JOIN #__ad_agency_advertis AS a ON b.advertiser_id = a.aid
							WHERE a.approved = 'Y' AND b.approved='Y'
							AND b.id = ".intval($zoneSettings->defaultad)."
							AND b.ad_start_date <= '".$jnow."'
							AND b.`approved`='Y'
							AND (b.ad_end_date >= '".$jnow."' OR b.ad_end_date = '0000-00-00 00:00:00')
							AND ((t0.approved='Y') AND (t0.approved='Y' AND ('{$dateok}' > t0.start_date) AND ((t0.type IN ('cpm','pc') AND t0.quantity>0) OR (t0.type='fr' AND '".$jnow."' < t0.validity) OR (t0.type='in' AND '".$jnow."' < t0.validity) ))) AND t0.status='1' AND cb.banner_id NOT IN (".$valid_cookie.") AND (b.media_type = 'Standard' OR b.media_type = 'TextLink' OR b.media_type = 'Flash' OR b.media_type = 'Advanced' OR b.media_type = 'Transition')
							GROUP BY b.id
							ORDER BY ".$order_by_in_static." ASC";
                            $database->setQuery( $sql );
                            $banners = diversify($database->loadObjectList());
                        }
                    }


					$no_of_ads_for_rotator = count( $banners );
					$loaded_banners = getBanners($banners,$database);

					$cids = array();
					if(isset($loaded_banners)) {
						foreach($loaded_banners as $element){
							if(isset($element->channel_id)&&($element->channel_id != NULL)) {
								$cids[] = $element->channel_id;
							}
						}
					}
					
					$cids = array_unique($cids);
					$inc = ",".implode(',',$cids);
		
					if($inc == ","){
						$inc = "";
					}

					$sql ="SELECT `channel_id` AS id,`type`,`logical`,`option`,`data` FROM #__ad_agency_channel_set WHERE channel_id IN (0".$inc.") ORDER BY id ASC";
					$database->setQuery($sql);
					$loaded_channels = $database->loadObjectList();
					
					$jomSocial = new JomSocialTargeting();
					$displayed_ids = array();
					
					if($no_of_ads_for_rotator>0){
						$the_rot_banners = '';
						$the_rot_advert = '';
						$the_rot_camp = '';
						$the_rot_banners_id = '';
						$geocount = 0;
						
						// start check add visibility ---------------------------------------
						$logged_user = JFactory::getUser();
						if(intval($logged_user->id) > 0){
							if(isset($banners) && count($banners) > 0 && $jomSocial->exists()){
								$temp = array();
								foreach($banners as $key=>$value){
									if($jomSocial->visible($value->banner_id)){
										$temp[] = $banners[$key];
									}
								}
								$banners = $temp;
							}
						}
						// stop check add visibility ----------------------------------------
						
						for ($i=0, $n=$no_of_ads_for_rotator; $i < $n; $i++) {
							$banner_id = $banners[$i]->banner_id;
							$banner_row = loadBannerById($banner_id, $loaded_banners);
							$banner_row->parameters = unserialize($banner_row->parameters);
							
							if(($banner_row->channel_id != NULL)&&(intval($banner_row->channel_id) >0)) {
								if(!geo(loadChannelById($banner_row->channel_id, $loaded_channels),$globalSettings->cityloc)){
									$no_of_ads_for_rotator--;
									continue;
								}
							}
				
							$geocount++;
							$the_advertiser = $banners[$i]->aid;
							$the_campaign = $banners[$i]->campaign_id;
							$the_type = $banners[$i]->type;

							if($geocount <= intval($bannernr_cols * $bannernr_rows)) {
								$displayed_ids[] = $banners[$i]->banner_id;
								ImpressionCalc($banners[$i], $banner_row, $real_ip, $limit_ip);
							}

							$track_link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid);
							
							if(($zoneSettings->adparams['width'] == NULL && $zoneSettings->adparams['height'] == NULL) || (intval($zoneSettings->adparams['width']) == intval($banner_row->width) && intval($zoneSettings->adparams['height']) == intval($banner_row->height)) ){			
								switch ($banner_row->media_type) {
									case 'Standard':
										$max_width = $banner_row->width.'px';
										$max_height = $banner_row->height.'px';
										$imageurl=$mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url;
										$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid);
										$link = JRoute::_($link);

										if(!isset($banner_row->parameters['border'])){
											$banner_row->parameters['border'] = NULL;
										}
										
										if(!isset($banner_row->parameters['bg_color'])){
											$banner_row->parameters['bg_color'] = NULL;
										}
										
										if(!isset($banner_row->parameters['padding'])){
											$banner_row->parameters['padding'] = NULL;
										}
										
										if(!isset($banner_row->parameters['align'])){
											$banner_row->parameters['align'] = NULL;
										}
										
										if(!isset($banner_row->parameters['target_window'])){
											$banner_row->parameters['target_window'] = NULL;
										}

										if(isset($banner_row->parameters['alt_text'])) {
											$banner_row->parameters['alt_text'] = str_replace(',','&#44;',$banner_row->parameters['alt_text']);
										}

										//border and color
										if ($banner_row->parameters['border']>0) {
											$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
										}
										else {
											$table_style="border: none;";
										}
										
										$bg_color="";
										if ($banner_row->parameters['bg_color']!="") {
											$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
										}

										//td padding
										$padding="";
										if ($banner_row->parameters['padding']>0) {
											$padding="padding: ".$banner_row->parameters['padding']."px;";
										}

										$one_banner = '<div style=\"'.$padding.'\" class=\"adv_standard_d\"><div style=\"max-width:'.$max_width.'; max-height:'.$max_height.';'.$bg_color.'\"><div><div><a href=\"'. $link .'\" target=\"'.$banner_row->parameters['target_window'].'\"><img style=\"'.$table_style.'\" src=\"'. $imageurl .'\" border=\"0\" title=\"'.$banner_row->parameters['alt_text'].'\" alt=\"'.$banner_row->parameters['alt_text'].'\" /></a></div></div></div></div>';
										break;
									
									case 'TextLink':
										$max_width = $banner_row->width.'px';
										$max_height = $banner_row->height.'px';
										$thumb = NULL;
										
										$sql = "SELECT thumb FROM #__ad_agency_campaign_banner WHERE campaign_id = ".intval($banners[$i]->id)." AND banner_id = ".intval($banner_row->id)." LIMIT 1";
										$db->setQuery($sql);
										$thumb = $db->loadColumn();
										$thumb = $thumb["0"];
						
										if(($thumb != NULL)&&(strlen($thumb)>4)) {
											$banner_row->image_url = $thumb;
										}
										
										if(isset($banner_row->parameters['img_alt'])) {
											$banner_row->parameters['img_alt'] = str_replace(',','&#44;',$banner_row->parameters['img_alt']);
										}

										$img_siz = NULL;
										$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid);
										
										if(isset($banner_row->image_url)&&($banner_row->image_url!='')){
											$txtimageurl=$mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url;
										}
										
										$link = JRoute::_($link);
										$img_style = '';
										$br = '';
										
										if(isset($txtimageurl)&&($txtimageurl != NULL)) {
											if(isset($zoneSettings->textadparams['ia'])) {
												$img_siz = @getimagesize($mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->image_url);
												if(isset($img_siz[0])){
													$img_siz[0]+=10; $img_siz = $img_siz[0]."px;";
												}
												
												if($zoneSettings->textadparams['ia'] == 'l'){
													$img_style = "float:left; padding: 5px;";$img_siz = "margin-left:".$img_siz;
												}
												elseif($zoneSettings->textadparams['ia'] == 'r'){
													$img_style = "float:right; padding: 5px;";$img_siz = "margin-right:".$img_siz;
												}
												else{
													$br = "<br />";
												}
											}
											
											if($zoneSettings->textadparams['mxtype'] =="w"){
												$mindim = 'width:'.$zoneSettings->textadparams['mxsize'].'px !important;';
											}
											else{
												$mindim = 'height:'.$zoneSettings->textadparams['mxsize'].'px !important;';
											}
											
											$imagetxtcode='<img class=\"standard_adv_img\" src=\"'. $txtimageurl .'\" style=\"'.$mindim.' '.$img_style.'\" border=\"0\" title=\"'.$banner_row->parameters['img_alt'].'\" alt=\"'.$banner_row->parameters['img_alt'].'\" />';
										}
										else{
											$imagetxtcode='';
										}

										//border and color
										if ($banner_row->parameters['border']>0) {
											$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
										}
										else{
											$table_style="border: none;";
										}
										
										$title_color="";
										if($banner_row->parameters['title_color']!=""){
											$title_color="style='color: #".$banner_row->parameters["title_color"].";'";
										}

										$bg_color="";
										if($banner_row->parameters['bg_color']!=""){
											$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
										}

										$underlined="";
										$font_weight="";
										$isUnderlined=strstr($banner_row->parameters['font_weight'],'underlined');
										
										if($isUnderlined=='underlined'){
											$str_length=strpos($banner_row->parameters['font_weight'],'underlined');
											$font_weight=substr($banner_row->parameters['font_weight'],0,$str_length);
											$underlined="text-decoration:underline;";
										}
										else{
											$font_weight=$banner_row->parameters['font_weight'];
										}

										$underlined_b="";
										$font_weight_b="";
										$isUnderlined_b=strstr($banner_row->parameters['font_weight_b'],'underlined');
										
										if($isUnderlined_b=='underlined'){
											$str_length_b=strpos($banner_row->parameters['font_weight_b'],'underlined');
											$font_weight_b=substr($banner_row->parameters['font_weight_b'],0,$str_length_b);
											$underlined_b="text-decoration:underline;";
										}
										else{
											$font_weight_b=$banner_row->parameters['font_weight_b'];
										}

										$body_color="";
										if($banner_row->parameters['body_color']!=""){
											$body_color="color: #".$banner_row->parameters['body_color'];
										}

										$action_color="";
										if($banner_row->parameters['action_color']!=""){
											$action_color="style='color: #".$banner_row->parameters["action_color"].";'";
										}
										
										$underlined_a="";
										$font_weight_a="";
										$isUnderlined_a=strstr($banner_row->parameters['font_weight_a'],'underlined');
										if($isUnderlined_a=='underlined'){
											$str_length_a=strpos($banner_row->parameters['font_weight_a'],'underlined');
											$font_weight_a=substr($banner_row->parameters['font_weight_a'],0,$str_length_a);
											$underlined_a="text-decoration:underline;";
										}
										else{
											$font_weight_a=$banner_row->parameters['font_weight_a'];
										}

										//td padding
										$padding="";
										if ($banner_row->parameters['padding']>0) {
											$padding="padding: ".$banner_row->parameters['padding']."px;";
										}

										$sizeparam='px';
										if(isset($zoneSettings->adparams['width']) && ($zoneSettings->adparams['width'] != NULL)){
											$banner_row->width = $zoneSettings->adparams['width'];
										}
										
										if(isset($zoneSettings->adparams['height']) && ($zoneSettings->adparams['height'] != NULL)){
											$banner_row->height = $zoneSettings->adparams['height'];
										}
										
										$width = ($banner_row->width > 0) ? 'width:'.$banner_row->width.'px;':'';
										$height = ($banner_row->height > 0) ? 'height:'.$banner_row->height.'px;':'';

										$banner_row->parameters['alt_text_a'] = addslashes($banner_row->parameters['alt_text_a']);
										$banner_row->parameters['alt_text'] = addslashes($banner_row->parameters['alt_text']);
										$banner_row->parameters['alt_text'] = str_replace(',','&#44;',$banner_row->parameters['alt_text']);

										if(isset($zoneSettings->textadparams["wrap_img"])&&($zoneSettings->textadparams["wrap_img"] == '1')){
											$img_siz = NULL;
										}
										
										$banner_row->parameters['alt_text']=preg_replace("/[\n\r]/"," ",$banner_row->parameters['alt_text']);
										
										$one_banner = '<div class="textlink_adv"  style=\"overflow: hidden; text-align:'.$banner_row->parameters['align'].'; '.$width.' '.$height.' '.$table_style.' '.$bg_color.' '.$padding.'\"><a target=\"'. $banner_row->parameters['target_window'] .'\" href=\"'. $link .'\" '.$title_color.'><span style=\"font-family: '. $banner_row->parameters['font_family'] .'; font-size: '. $banner_row->parameters['font_size'] .'px; font-weight: '. $font_weight.'; '.$underlined.'\"><font face=\"'.$banner_row->parameters['font_family'].'\"> '.$banner_row->parameters['alt_text_t'].'</font></span></a><br />'.$imagetxtcode.$br.'<div style=\"font-family: '. $banner_row->parameters['font_family_b'] .'; font-size: '. $banner_row->parameters['font_size_b'] .'px; font-weight: '. $font_weight_b.';'.$underlined_b.' '.$body_color.';'.$img_siz.';\">'.$banner_row->parameters['alt_text'].' </div><a href=\"'. $link .'\" target=\"'. $banner_row->parameters['target_window'] .'\" '.$action_color.'><div style=\"word-break: break-all; font-family: '. $banner_row->parameters['font_family_a'] .'; font-size: '. $banner_row->parameters['font_size_a'] .'px; font-weight: '. $font_weight_a .';'.$underlined_a.';\">'.$banner_row->parameters['alt_text_a'].'</div></a></div>';
										$txtimageurl = NULL;									
										break;

									case 'Flash':
										$max_width = $banner_row->width.'px';
										$max_height = $banner_row->height.'px';
										$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid);
										$link = JRoute::_($link);
										
										if(isset($zoneSettings->adparams['width'])&&($zoneSettings->adparams['width'] != NULL)){
											$banner_row->width = $zoneSettings->adparams['width'];
										}
										
										if(isset($zoneSettings->adparams['height'])&&($zoneSettings->adparams['height'] != NULL)){
											$banner_row->height = $zoneSettings->adparams['height'];
										}
										
										//border and color
										if ($banner_row->parameters['border']>0) {
											$table_style="border: solid ".$banner_row->parameters['border']."px #".$banner_row->parameters['border_color'].";";
										}
										else {
											$table_style="border: none;";
										}
										
										$bg_color="";
										if ($banner_row->parameters['bg_color']!="") {
											$bg_color="background-color: #".$banner_row->parameters['bg_color'].";";
										}

										//td padding
										$padding="";
										if ($banner_row->parameters['padding']>0) {
											$padding="padding: ".$banner_row->parameters['padding']."px;";
										}
						
										///changes in adding flash objects
										$flashurl = $mosConfig_live_site .'/images/stories/'.$ad_agency_folder.'/'.$banners[$i]->aid.'/'. $banner_row->swf_url;
										
										$adflash='<EMBED SRC=\"'.$flashurl.'\" width=' . $banner_row->width . ' height=' . $banner_row->height . ' QUALITY=\"high\" wmode=\"transparent\" TYPE=\"application/x-shockwave-flash\" PLUGINSPAGE=\"http://www.macromedia.com/go/getflashplayer\"></EMBED>';
										
										$one_banner = '';
										$onevent = "onclick";
										
										if(isset($banner_row->parameters['target_window'])&&($banner_row->parameters['target_window'] == '_self')) {
											$js_open = 'document.location.href=\''.urldecode($link).'\';';
											$flash_target = '_self';
										}
										else{
											$js_open = 'javascript:window.open(\''.urldecode($link).'\')';
											$flash_target = '_blank';
										}
										
										if(isset($_SERVER['HTTP_USER_AGENT'])&&(strpos($_SERVER['HTTP_USER_AGENT'],"MSIE")>1)){
											$onevent = "onmousedown";
										}
										
										/*$one_banner .= '<div style=\"text-align:center; margin:0 auto; '.$table_style.' '.$bg_color.' max-width:'.$banner_row->width.'px; max-height:'.$banner_row->height.'px; \" align=\"'.$banner_row->parameters['align'].'\">  ';
										$one_banner .= '<div style=\"position:fixed; max-width:'.$max_width.'; max-height:'.$max_height.'; background-color:transparent;cursor: pointer;\" cellpadding=\"0\" cellspacing=\"0\" width=\"'.$banner_row->width.'\" height=\"'.$banner_row->height.'\"><div><div valign=\"top\">';
										$one_banner .= '<a href=\"' . $link . '\" target=\"' . $flash_target . '\"><img alt=\"\" src=\"' . JURI::root() . 'components/com_adagency/images/trans.gif\" style=\"height: ' . $banner_row->height .  'px; width: ' . $banner_row->width .  'px; float: left;\"></a>';
										$one_banner .= '</div></div></div>'.$adflash. '</div>';*/
										
										//---------------------------------------------------------------------
										$one_banner .= '<div style=\"text-align:center; margin:0 auto; '.$table_style.' '.$bg_color.' max-width:'.$banner_row->width.'px; max-height:'.$banner_row->height.'px; \" align=\"'.$banner_row->parameters['align'].'\"><a href=\"'.$link.'\" target=\"' . $flash_target . '\" style=\"display:inline-block; width:100%;\"><iframe src=\"'.JURI::root().'index.php?option=com_adagency&controller=adagencyAds&task=loadflash&url='.urlencode($flashurl).'&width='.$banner_row->width.'&height='.$banner_row->height.'&tmp=component&format=raw'.'\" style=\"width:'.intval($banner_row->width).'px; height:'.intval($banner_row->height).'px; border:none; pointer-events: none;\" scrolling=\"no\" seamless=\"seamless\"></iframe></a></div>';
										//---------------------------------------------------------------------
										
										break;

									case 'Advanced':
										$max_width = $banner_row->width.'px';
										$max_height = $banner_row->height.'px';
										$link='index.php?option=com_adagency&controller=adagencyAds&task=click&cid='.intval($banners[$i]->id).'&bid='.intval($banner_row->id).'&aid='.intval($banners[$i]->aid);
										$link = JRoute::_($link);
										$style_adv = NULL;
										$original_width = $zoneSettings->adparams['width'];
										$original_height = $zoneSettings->adparams['height'];		

										if(!isset($banner_row->parameters['target_window'])){
											$banner_row->parameters['target_window'] = NULL;
										}
										
										if(isset($zoneSettings->adparams['width'])&&isset($zoneSettings->adparams['height'])&&($zoneSettings->adparams['width'] != NULL)&&($zoneSettings->adparams['height'] != NULL)) {
											$zoneSettings->adparams['width']+= 5;$zoneSettings->adparams['height']+= 5;
											$style_adv = ' style=\"overflow:hidden;margin:0 auto;width:'.$zoneSettings->adparams['width'].'px;height:'.$zoneSettings->adparams['height'].'px;\" ';
										}
										else{
											$style_adv = NULL;
										}
										
										if(preg_match('/ad_url/',$banner_row->ad_code)){
											$one_banner =  '<div class=\"adv_rt\" '.$style_adv.'><div><div><div>'.str_replace('ad_url',$link.'\" target=\"'.$banner_row->parameters['target_window'].'\"',str_replace('"', '\"', $banner_row->ad_code) ).'</div></div></div></div>';
										}
										else{
											$one_banner = '<div class=\"adv_rt\" '.$style_adv.'><div '.$style_adv.'  style=\"max-width:'.$max_width.'; max-height:'.$max_height.';\"><div><div><a href=\"'.$link.'\" target=\"'.$banner_row->parameters['target_window'].'\">'.str_replace('"', '\"', $banner_row->ad_code).'</a></div></div></div></div>';
										}

										$zoneSettings->adparams['width'] = $original_width;
										$zoneSettings->adparams['height'] = $original_height;
										break;
								} // endswitch
							}//end if
							
							$the_rot_banners = $the_rot_banners.'"'.$one_banner.'",';
							$the_rot_banners_id = $the_rot_banners_id.'"'.$banner_id.'",';
							$the_rot_advert = $the_rot_advert.'"'.$the_advertiser.'",';
							$the_rot_camp = $the_rot_camp.'"'.$the_campaign.'",';
							$the_rot_type = $the_rot_type.'"'.$the_type.'",';
						}

						$the_rot_banners = substr($the_rot_banners, 0, (strlen($the_rot_banners)-1) );
						$the_rot_banners_id = substr($the_rot_banners_id, 0, (strlen($the_rot_banners_id)-1) );
						$the_rot_advert = substr($the_rot_advert, 0, (strlen($the_rot_advert)-1) );
						$the_rot_camp = substr($the_rot_camp, 0, (strlen($the_rot_camp)-1) );
						$the_rot_type = substr($the_rot_type, 0, (strlen($the_rot_type)-1) );

						if($rotator_info->rotaterandomize == 1){
							$is_random = 'currentAd=Math.floor(Math.random()*'.$no_of_ads_for_rotator.')';
						}
						else{
							$is_random = '';
						}
						$document->addScript(JURI::base()."components/com_adagency/includes/js/ajax.js");

						$rotator_content.= '
							function rotator_display_count'.$module->id.'(banner_id, advertiser_id, campaign_id, type){
								var ajaxObjects = new Array();
								var ajaxIndex = ajaxObjects.length;
								ajaxObjects[ajaxIndex] = new sack();

								var url = "' . JURI::root()."index.php?option=com_adagency&controller=adagencyReports&task=rotator" . '&banner_id=" + banner_id + "&advertiser_id=" +advertiser_id+"&campaign_id=" +campaign_id+"&type=" +type;
								ajaxObjects[ajaxIndex].requestFile = url; // Specifying which file to get
								ajaxObjects[ajaxIndex].onCompletion = function(){};
								ajaxObjects[ajaxIndex].runAJAX(); // Execute AJAX function
							}
							';
							
						$rotator_content .='
							var imgCt'.$module->id.' = '.$geocount.';
							var banners'.$module->id.' = new Array('.str_replace('class="textlink_adv"', 'class=\"textlink_adv\"', $the_rot_banners).');
							var banners_ids'.$module->id.' = new Array('.$the_rot_banners_id.');
							var displayed_ids'.$module->id.' = new Array("'.implode('", "', $displayed_ids).'");
							var advertisers'.$module->id.' = new Array('.$the_rot_advert.');
							var campaigns'.$module->id.' = new Array('.$the_rot_camp.');
							var types'.$module->id.' = new Array('.$the_rot_type.');
							
							function cycle'.$module->id.'(position, curent){
								if(curent >= imgCt'.$module->id.'){
									curent=curent%imgCt'.$module->id.';
								}
								
								var next = curent+1;
								if(next >= imgCt'.$module->id.'){
									next = next % imgCt'.$module->id.';
								}
								
								document.getElementById("the'.$module->id.'_rotator"+position).innerHTML = banners'.$module->id.'[next];
								document.getElementById("the'.$module->id.'_rotator_aux"+position).innerHTML = banners'.$module->id.'[next];
								
								if(displayed_ids'.$module->id.'.indexOf(banners_ids'.$module->id.'[next]) == -1){
									rotator_display_count'.$module->id.'(banners_ids'.$module->id.'[next], advertisers'.$module->id.'[next], campaigns'.$module->id.'[next], types'.$module->id.'[next]);
									displayed_ids'.$module->id.'.push(banners_ids'.$module->id.'[next]);
								}
								
								curent += '.intval($zoneSettings->banners * $zoneSettings->banners_cols).';
								setTimeout("cycle'.$module->id.'("+position+","+next+")", '.$rotator_info->rotating_time.');
							}
							';
					}

					JHtml::_('behavior.framework');

					$to_rotate_now = NULL;
					if (!isset($document)){
						$document =JFactory::getDocument();
					}
					
					if (!isset($geocount)){
						$geocount = 0;
					}
					
					// added html comments for the w3c validator
					$document->addScriptDeclaration("<!--
					" . $rotator_content . "
					//-->
					");
					
					$new_rotator = '<div  class="adg_table clearfix">';
					
					if(!isset($the_rot_banners_id)){
						$the_rot_banners_id=NULL;
					}
					
					if(!isset($is_random)){
						$is_random=NULL;
					}
					
					if(!isset($the_rot_banners)){
						$the_rot_banners=NULL;
					}
					
					if(!isset($output_this)){
						$output_this=NULL;
					}
					
					$total = explode(",",$the_rot_banners_id);
					$start = 1;
					
					$display_banners=explode(",",$the_rot_banners);
					$displayed = array();

					if(count($banners) > $zoneSettings->banners_cols){
						$span = floor(12 / $zoneSettings->banners_cols);
					}
					else{
						if(count($banners) == 5){
							$span = "Special";
						}
						else{
							if(count($banners) != 0){
								$span = floor(12 / count($banners));
							}
						}
					}

					for($i=1;$i<=$bannernr_rows;$i++){
						$new_rotator .="<div class=\"adg_row adg_table_row\">";
						for($j=1;$j<=$bannernr_cols;$j++){
							if($start>count($total)){
								$start=1;
							}
		
							if($start >= $geocount+1){
								break;
							}
							
							if(!in_array($start,$displayed)){
								$displayed[] = $start;
								$new_rotator .= "<div class=\"adg_cell adg_table_cell span".$span." \"><div><div><div class=\"rotating_zone\" id=\"the".$module->id."_rotator".$start."\">".stripslashes(substr($display_banners[$start-1],1,-1))."</div><div id=\"the".$module->id."_rotator_aux".$start."\" style='display:none;'></div></div></div></div>";
								$to_rotate_now .= 'cycle'.$module->id.'('.$start.','.($start-1).');';
							}
							$start++;
						}
						$new_rotator .= "</div>";
					}
					
					$new_rotator .= "</div>";

					$output_this.= $new_rotator;
					if(count($loaded_banners)>=1){
						$document->addScriptDeclaration("DomReady.ready(function(){
								window.setTimeout(function(){
									".$to_rotate_now."
								}, ".$rotator_info->rotating_time.");
							});");
					}

				}
			}

			if(!isset($output_this)){
				$output_this='';
			}

			// Changed code for MetaMod compatibility
			global $ad_output1, $ad_output2;
			
			if (!is_array($ad_output1)){
				$ad_output1 = array();
			}
			
			if (!is_array($ad_output2)){
				$ad_output2 = array();
			}
			
			$ad_output1[$module->id] = $output_this;
			$ad_output2[$module->id] = $adv_here_top.$output_this.$adv_here_bottom;
	
			return $ad_output2[$module->id];
		}
		return NULL;
	}

	function formatime($time,$option = 1){
		$db =JFactory :: getDBO();
		$db->setQuery('SELECT params FROM #__ad_agency_settings ORDER BY id ASC LIMIT 1');
		$params = $db->loadColumn();
		$params = $params["0"];
		
		$params = @unserialize($params);
		$option = intval($params['timeformat']);
		$date_time = explode(" ",$time);
		$tdate = explode("-",$date_time[0]);
		$output = NULL;
		
		if(!isset($date_time[1])){
			$date_time[1] = NULL;
		}
		
		switch($option){
			case "1":
				$output = $tdate[2]."-".$tdate[1]."-".$tdate[0]." ".$date_time[1];
				break;
			case "2":
				$output = $tdate[2]."/".$tdate[1]."/".$tdate[0]." ".$date_time[1];
				break;
			case "3":
				$output = $tdate[1]."-".$tdate[2]."-".$tdate[0]." ".$date_time[1];
				break;
			case "4":
				$output = $tdate[1]."/".$tdate[2]."/".$tdate[0]." ".$date_time[1];
				break;
			case "5":
				$output = $time;
				break;
			case "6":
				$output = str_replace("-","/",$time);
				break;
			default:
				$output = $time;
				break;
		}
		return trim($output);
	}
}

function iJoomlaGetAge($Birthdate) {
	// Explode the date into meaningful variables
	list($BirthYear,$BirthMonth,$BirthDay) = explode("-", $Birthdate);
	// Find the differences
	$YearDiff = date("Y") - $BirthYear;
	$MonthDiff = date("m") - $BirthMonth;
	$DayDiff = date("d") - $BirthDay;
	// If the birthday has not occured this year
	if($MonthDiff < 0){
	  $YearDiff--;
	}
	return $YearDiff;
}

function ImpressionCalc($banners, $banner_row, $real_ip, $limit_ip){
	$database =JFactory :: getDBO();
	$class_helper = new adagencyAdminHelper;
	$campaingID = $banners->id;
	$bannerID = $banner_row->id;
	$advertiserID = $banner_row->advertiser_id;
	$time_interval = date("Y-m-d");
	$how_many = 0;
	$all_impressions = array();
	$ip_address = ip2long($real_ip);
	
	$sql = "select `impressions` from #__ad_agency_statistics where `entry_date`='".$time_interval."'";
	$database->setQuery($sql);
	
	if(!$database->query()) {
		echo $database->stderr();
		return;
	}
	else{
		$all_impressions = $database->loadColumn();
		$all_impressions = @$all_impressions["0"];
		
		if(isset($all_impressions)){
			$all_impressions = json_decode($all_impressions, true);
			
			if(!isset($all_impressions["0"])){
				if(isset($all_impressions)){
					$all_impressions = array("0"=>$all_impressions);
				}
				else{
					$temp = array("advertiser_id"=>"0", "campaign_id"=>"0", "banner_id"=>"0", "how_many"=>"0");
					$all_impressions = array("0"=>$temp);
				}
			}
			
			if(isset($all_impressions) && count($all_impressions) > 0){
				foreach($all_impressions as $key=>$value){
					if($value["campaign_id"] == intval($campaingID) && $value["banner_id"] == intval($bannerID)){
						$how_many = $value["how_many"];
						break;
					}
				}
			}
		}
	}

	if(ip2long($real_ip) != 0 && ip2long($real_ip) != NULL){
		// start limit impression count for a banner per ip ----------------------
		$sql = "select `ips_impressions` from #__ad_agency_ips where `entry_date`='".$time_interval."'";
		$database->setQuery($sql);
		$database->query();
		$all_ips = $database->loadColumn();
		
		if(is_array($all_ips) && count($all_ips) > 0){
			$all_ips = json_decode($all_ips["0"], true);
			$update = FALSE;
			
			if(isset($all_ips) && count($all_ips) > 0){
				foreach($all_ips as $key=>$value){
					if($value["ip"] == ip2long($real_ip) && $value["banner_id"] == intval($bannerID)){
						if($value["how_many"] < $limit_ip){
							$update = TRUE;
							$all_ips[$key]["how_many"] += 1;
							break;
						}
						else{
							// max limit impressions per IP for one ad per day
							return "";
						}
					}
				}
			}
			
			if(!$update){
				$new_ip_added = array("ip"=>ip2long($real_ip), "banner_id"=>intval($bannerID), "how_many"=>"1");
				$all_ips[] = $new_ip_added;
			}
			
			$sql = "update #__ad_agency_ips set `ips_impressions`='".json_encode($all_ips)."' where `entry_date`='".$time_interval."'";
			$database->setQuery($sql);
			$database->query();
		}
		else{
			$temp_ips1 = array("ip"=>ip2long($real_ip), "banner_id"=>intval($bannerID), "how_many"=>"1");
			$temp_ips2 = array("ip"=>'0000000000', "banner_id"=>"0", "how_many"=>"0");
			$temp_ips = array("0"=>$temp_ips1, "1"=>$temp_ips2);
			
			$sql = "insert into #__ad_agency_ips (`entry_date`, `ips_impressions`) values ('".$time_interval."', '".json_encode($temp_ips)."')";
			$database->setQuery($sql);
			$database->query();
		}
		// stop limit impression count for a banner per ip -----------------------
		
		if(isset($all_impressions) && count($all_impressions) > 0){
			if(isset($all_impressions) && count($all_impressions) > 0){
				$changed = false;
				foreach($all_impressions as $key=>$value){
					if($value["campaign_id"] == intval($campaingID) && $value["banner_id"] == intval($bannerID)){
						$all_impressions[$key]["how_many"] = $how_many + 1;
						$sql = "update #__ad_agency_statistics set `impressions`='".json_encode($all_impressions)."' where `entry_date`='".$time_interval."'";
						$database->setQuery($sql);
						$database->query();
						
						$changed = true;
						break;
					}
				}

				if(!$changed){
					$temp = array("advertiser_id"=>intval($advertiserID), "campaign_id"=>intval($campaingID), "banner_id"=>intval($bannerID), "how_many"=>"1");
					$all_impressions[] = $temp;
					$sql = "update #__ad_agency_statistics set `impressions`='".json_encode($all_impressions)."' where `entry_date`='".$time_interval."'";
					$database->setQuery($sql);
					$database->query();
				}
			}
		}
		else{
			$temp = array("advertiser_id"=>intval($advertiserID), "campaign_id"=>intval($campaingID), "banner_id"=>intval($bannerID), "how_many"=>"1");
			$sql = "insert into #__ad_agency_statistics (`entry_date`, `impressions`, `click`) values ('".$time_interval."', '".json_encode($temp)."', '')";
			$database->setQuery($sql);
			$database->query();
		}
	}
	
	if(('cpm'==$banners->type)&&($how_many<=$limit_ip)){
		$sql = "SELECT quantity FROM #__ad_agency_campaign WHERE id=".intval($campaingID);
		$database->setQuery($sql);
		if (!$database->query()) {
			echo $database->stderr();
			return;
		}
		$quantity = $database->loadColumn();
		$quantity = $quantity["0"];
		$quantity --;
		
		if($quantity < 0){
			$quantity = 0;
		}
		
		$sql = "UPDATE #__ad_agency_campaign SET quantity = '".$quantity."' WHERE id=".intval($campaingID);
		$database->setQuery($sql);
		if (!$database->query()) {
			echo $database->stderr();
			return;
		}

		if ($quantity == 0) {
			$jnow = JFactory::getDate();
			if(intval(JVERSION) >= 3){
				$nowdatetime = $jnow->toSql(true);
			}
			else{
				$nowdatetime = $jnow->toMySQL(true);
			}
			
			$sql = "UPDATE #__ad_agency_campaign SET validity = '$nowdatetime' WHERE id=".intval($campaingID);
			$database->setQuery($sql);
			if (!$database->query()) {
				echo $database->stderr();
				return;
			}
		}
	}
	
}


function getIPBlocklist(){
    $db = JFactory::getDBO();
	$sql = "select `blacklist` from #__ad_agency_settings";
	$db->setQuery($sql);
	$db->query();
	$blacklist = $db->loadColumn();
	$blacklist = @$blacklist["0"];
	return explode("||", $blacklist);
}

if(!function_exists("iJoomlaGetRealIpAddr")){
	function iJoomlaGetRealIpAddr(){
		if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
		{
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
		{
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		// check if isset REMOTE_ADDR and != empty
		elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
		{
			$ip = $_SERVER['REMOTE_ADDR'];
		// you're probably on localhost
		} else {
			$ip = "127.0.0.1";
		}
		return $ip;
	}
}

function iJoomlaGetRealIpAddrModule(){
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
		$ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
		$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
	// check if isset REMOTE_ADDR and != empty
    elseif(isset($_SERVER['REMOTE_ADDR']) && ($_SERVER['REMOTE_ADDR'] != '') && ($_SERVER['REMOTE_ADDR'] != NULL))
    {
    	$ip = $_SERVER['REMOTE_ADDR'];
	// you're probably on localhost
    } else {
		$ip = "127.0.0.1";
	}
    return $ip;
}

function diversify($sss){	
	if(!is_array($sss)) { return $sss; }
	$ary = array();$numring = 0;
	foreach($sss as $key=>$value){
		$ary[$numring] = $value->aid; $numring++;
	}

	$len = count($ary);
	$pos = array(); $pos2 = array();
	if($len>=2){
		for($q=0;$q<=$len-1;$q++){
			$pos[$q] = $q;
		}
		for($i=1;$i<=$len-2;$i++){
			//$haystack = array_slice($ary,$i+1,$len-$i-1,true);
			$haystack = array_slice($ary,0,$i,true);
			//echo "<pre>";var_dump($haystack);die();
			if(in_array($ary[$i],$haystack)){
				$found = false;
				for($j=$i+1;$j<=$len-1;$j++){
					if((!in_array($ary[$j],$haystack))&&(!$found)){
						$aux = $ary[$i];
						$ary[$i] = $ary[$j];
						$ary[$j] = $aux;

						$aux2 = $pos[$i];
						$pos[$i] = $pos[$j];
						$pos[$j] = $aux2;

						$found = true;
						break;
					}
				}
			}
		}
		for($i=1;$i<=$len-2;$i++){
				if($ary[$i-1] == $ary[$i]) {
					$found = false;
					for($j=$i+1;$j<=$len-1;$j++){
						if((!$found)&&($ary[$i] != $ary[$j])) {
							$found = true;
							$aux = $ary[$j];
							$ary[$j] = $ary[$i];
							$ary[$i] = $aux;

							$aux2 = $pos[$i];
							$pos[$i] = $pos[$j];
							$pos[$j] = $aux2;
							break;
						}
					}
				}
			}
		}

	foreach($pos as $key=>$val){
		$pos2[$key] = $sss[$val];
	}
	
	if(count($pos2)<=1) { return $sss; }
	return $pos2;
}

?>