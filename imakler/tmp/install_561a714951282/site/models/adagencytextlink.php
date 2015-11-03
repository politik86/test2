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



jimport ("joomla.aplication.component.model");

if(!class_exists('upload')){

	require_once('administrator/components/com_adagency/helpers/class.upload.php');

}



class adagencyModeladagencyTextlink extends JModelLegacy {

	var $_licenses;

	var $_license;

	var $_id = null;

	var $_total = 0;

	var $_pagination = null;



	function __construct () {
		parent::__construct();

		$cids = JRequest::getVar('cid', 0, '', 'array');

		$this->setId((int)$cids[0]);

		global $mainframe, $option;

		// Get the pagination request variables

		$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );

		$limitstart = $mainframe->getUserStateFromRequest( $option.'limitstart', 'limitstart', 0, 'int' );

		$this->setState('limit', $limit); // Set the limit variable for query later on

		$this->setState('limitstart', $limitstart);

	}



	function setId($id) {

		$this->_id = $id;

		$this->_license = null;

	}



	function getPagination(){

		// Lets load the content if it doesn't already exist

		if (empty($this->_pagination))	{

			jimport('joomla.html.pagination');

			if (!$this->_total) $this->getListAds();

			$this->_pagination = new JPagination( $this->_total, $this->getState('limitstart'), $this->getState('limit') );

		}

		return $this->_pagination;

	}

	function getad() {

		if (empty ($this->_package)) {

			$this->_package = $this->getTable("adagencyAds");

			$this->_package->load($this->_id);

			$data = JRequest::get('post');

			if (!$this->_package->bind($data)){

				$this->setError($item->getErrorMsg());

				return false;



			}

			if (!$this->_package->check()) {

				$this->setError($item->getErrorMsg());

				return false;

			}

		}

		return $this->_package;

	}



	function getWH($id){

		$db = JFactory::getDBO();

		$sql = "SELECT width, height FROM #__ad_agency_banners WHERE id='".intval($id)."'";

		$db->setQuery($sql);

		return $db->loadObject();

	}



	function getPrevZones(){

		$db = JFactory::getDBO();

		$sql = "SELECT zoneid, z_title, textadparams FROM `#__ad_agency_zone` WHERE `adparams` LIKE '%textad%'";

		$db->setQuery($sql);

		$result = $db->loadObjectList();

		//echo "<pre>";var_dump($result);die();

		return $result;

	}



	function getImageInfo($imagepath){

		return @getimagesize($imagepath);

	}



	function getAdvInfo($id){

		$db = JFactory::getDBO();

		$query = "SELECT * FROM #__ad_agency_advertis WHERE aid=".intval($id);

		$db->setQuery($query);

		$result = $db->loadObject();

		return $result;

	}



	function getCurrentAdvertiser(){

		$db = JFactory::getDBO();

		$my = JFactory::getUser();

		$sql = "SELECT * FROM #__ad_agency_advertis WHERE user_id = ".intval($my->id);

		$db->setQuery($sql);

		$result = $db->loadObject();

		return $result;

	}



	function getAdById($id){

		$db = JFactory::getDBO();

		$sql = "SELECT * FROM #__ad_agency_banners WHERE id = ".intval($id);

		$db->setQuery($sql);

		$result = $db->loadObject();

		return $result;

	}



	function getChannel($bid) {

		$db = JFactory::getDBO();

		if(isset($bid)) {

			$sql = "SELECT * FROM `#__ad_agency_channels` WHERE banner_id =".intval($bid)." LIMIT 1";

			$db->setQuery($sql);

			$result = $db->loadObject();

			if(!isset($result->id)) { return NULL; }

			$sql = "SELECT s.type, s.option, s.logical, s.data FROM `#__ad_agency_channels` AS c, `#__ad_agency_channel_set` AS s WHERE c.id = ".intval($result->id)." AND c.id = s.channel_id ORDER BY s.id ASC";

			$db->setQuery($sql);

			$result->sets = $db->loadObjectList();



			//echo "<pre>";var_dump($result);die();

		} else {

			$result = NULL;

		}

		return $result;

	}



	function processCampZones($camps){

		$zones = array();

		if(isset($camps)&&(is_array($camps))){

			foreach($camps as $camp){

				if(!isset($zones[$camp->id])) { $zones[$camp->id] = $this->getAllZonesForCampByCampId($camps,$camp->id); }

			}

		}

		return $zones;

	}



	function getAllZonesForCampByCampId($camps, $camp_id){

        $resp = array();

        if(isset($camps)&&(is_array($camps))){

            $i = 1;

            foreach($camps as $camp){

                if($camp->id == $camp_id) {

                    $db = JFactory::getDBO();

					$sql = "SELECT z.`zoneid`, z.`z_title`, z.`adparams` FROM `#__ad_agency_zone` z, `#__ad_agency_campaign` c, `#__ad_agency_package_zone` pz WHERE pz.zone_id=z.zoneid and c.otid=pz.package_id and c.id=".intval($camp_id);

					$db->setQuery($sql);

					$db->query();

					$result = $db->loadAssocList();

					

					if(isset($result) && count($result) > 0){

						foreach($result as $zone){

							$params = $zone["adparams"];

							$params = unserialize($params);

							$zone["width"] = $params["width"];

							$zone["height"] = $params["height"];

							$zone["zoneid"] = $zone["zoneid"];

							$zone["z_title"] = $zone["z_title"];

						

							$resp[] = $zone;

						}

					}

					

                    $i++;

                }

            }

        }

        return $resp;

    }



	function createSelectBox($czones, $ad_id, $ad){

        $db = JFactory::getDBO();

        $select = array();

        if(isset($czones)&&(is_array($czones))){

			$first = TRUE;

			foreach($czones as $key=>$value){

				if($first){

					$czones[$key] = array();	

				}

				

				$sql = "SELECT z.`zoneid`, m.`title` as z_title, z.`adparams` FROM `#__ad_agency_zone` z, `#__ad_agency_campaign` c, `#__ad_agency_package_zone` pz, `#__modules` m WHERE pz.zone_id=z.zoneid and c.otid=pz.package_id and c.id=".intval($key)." and m.`id`=z.`zoneid`";

				$db->setQuery($sql);

				$db->query();

				$result = $db->loadAssocList();

				

				if(isset($result) && count($result) > 0){

					foreach($result as $res){

						$czones[$key][] = $res;

					}

				}

			}

			

            foreach($czones as $key=>$value){

                $sql = "SELECT `zone` FROM #__ad_agency_campaign_banner WHERE banner_id = ".intval($ad_id)." AND campaign_id = ".intval($key);

				$db->setQuery($sql);

				$db->query();

                $sel_zone = $db->loadColumn();

				@$sel_zone = $sel_zone["0"];

				

                $select[$key] = "<select name='czones[".$key."]' id='czones_".$key."' class='w145'>";

                $select[$key] .= "<option value='0'>".JText::_('ADAG_ZONE_FOR_AD')."</option>";

                if(is_array($value)){

                    foreach($value as $val){

						$disabled = "";

						$params = $val["adparams"];

						$params = unserialize($params);

						$zone_width = $params["width"];

						$zone_height = $params["height"];

						

						/*if(trim($zone_width) != "" && trim($zone_height) != ""){

							if(trim($zone_width) != $ad->width && trim($zone_height) != $ad->height){

								$disabled = 'disabled="disabled"';

							}

						}*/

						

						if(!isset($params["textad"])){

							$disabled = 'disabled="disabled"';

						}

						

                        if($sel_zone == $val['zoneid']){

							$this_selected = " selected='selected' ";

						}

						else{

							$this_selected = NULL;

						}

						

						if($disabled == ""){

                        	$select[$key] .= "<option value='".$val['zoneid']."' ".$this_selected." ".$disabled.">".$val['z_title']."</option>";

						}

                    }

                }

                $select[$key] .= "</select>";

            }

        }

        return $select;

    }

	function getCampsByAid($adv_id, $distinct = 0) {

		$db = JFactory::getDBO();

		$sqls = "

			SELECT c.id, c.name, c.params, z.*

			FROM #__ad_agency_campaign AS c

			JOIN #__ad_agency_order_type AS o

			ON c.otid = o.tid

			JOIN #__ad_agency_package_zone AS pz

			ON pz.package_id = o.tid

			JOIN #__ad_agency_zone AS z

			ON z.zoneid = pz.zone_id

			WHERE c.aid = ".intval($adv_id)." AND z.adparams LIKE '%textad%'
			AND c.status != -1

		";

        if ($distinct == 1) { $sqls .= "GROUP BY c.id"; }

        //echo $sqls;die();

		$db->setQuery($sqls);

		$camps = $db->loadObjectList();

		foreach($camps as &$element) {

			$element->adparams = @unserialize($element->adparams);

            $element->params = @unserialize($element->params);



            // Select total banners for each campaign

            $sql = "SELECT COUNT( campaign_id )

                       FROM `#__ad_agency_campaign_banner`

                       WHERE `campaign_id` = " . $element->id;

            $db->setQuery($sql);

            $element->totalbanners = $db->loadResult();

		}

		//echo "<pre>";var_dump($camps);die();

		return $camps;

	}



	function getSelectedCamps($advertiser_id,$adid){

		$db = JFactory::getDBO();

        $advertiser_id = (int) $advertiser_id;

        $adid = (int) $adid;

		$sql="SELECT DISTINCT cb.campaign_id FROM #__ad_agency_banners AS b LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id=b.id WHERE b.advertiser_id=".intval($advertiser_id)." AND b.id=".intval($adid);

		$db->setQuery($sql);

		$these_campaigns = $db->loadColumn();

		

		return $these_campaigns;

	}



	function delete_geo($bid, $aid) {

		$db = JFactory::getDBO();

		$temp = NULL;

		if(isset($bid)&&isset($aid)) {

			$sql0 = 'SELECT id FROM #__ad_agency_channels WHERE banner_id = "'.intval($bid).'" AND advertiser_id = "'.intval($aid).'" LIMIT 1';

			$db->setQuery($sql0);

			$id_to_del = $db->loadResult();



			if(isset($id_to_del)&&($id_to_del != NULL)) {

				/*$sql1 = 'DELETE FROM #__ad_agency_channels WHERE id = "'.$id_to_del.'"';

				$db->setQuery($sql1);

				if(!$db->query()) { return false; }*/



				$sql2 = 'DELETE FROM #__ad_agency_channel_set WHERE channel_id = "'.intval($id_to_del).'"';

				$db->setQuery($sql2);

				if(!$db->query()) { return false; }

			}

		}



		if(isset($id_to_del)) { return $id_to_del; } else { return -1; }

	}



	function store_geo($bid = NULL){

		require_once('components/com_adagency/helpers/channel_fcs.php');

	}



	function store () {

		$database = JFactory::getDBO();

		$db = JFactory::getDBO();

		$item = $this->getTable('adagencyAds');

        $adsModel = $this->getInstance("adagencyAds", "adagencyModel");

		$configs = $this->getInstance("adagencyConfig", "adagencyModel");

		$configs = $configs->getConfigs();

		$notify = JRequest::getInt('id');

		$changed = "new";

		$advid = $this->getCurrentAdvertiser();

		if(isset($advid->aid)) { $advid = $advid->aid; } else { $advid = 0;}

		$data = JRequest::get('post');

		if(isset($data['width'])&&(isset($data['height']))) {

			$data['width']=NULL;$data['height']=NULL;

			unset($data['width']);unset($data['height']);

		}

		//echo "<pre>";var_dump($data);die();

		if($data['id']==0){

			$data['created'] = date("Y-m-d");

			$data['key'] = md5(rand(1000,9999));

			$data['ad_start_date'] = $data['created'];

		} else {

			$data['key'] = NULL;

		}

		//echo "<pre>";var_dump($data);die();

		if(isset($data['adv_cmp'])) {$adv_cmp=$data['adv_cmp'];}

		$res = true;



		// Check settings for auto-approve ads [begin]

		$advertiser = $this->getCurrentAdvertiser();

		if(isset($advertiser->apr_ads)&&($advertiser->apr_ads!='G')){

			$approved = $advertiser->apr_ads;

		} else {

			$sql = "SHOW COLUMNS FROM `#__ad_agency_banners` WHERE field = 'approved'";

			$db->setQuery($sql);

			$res = $db->loadObject();

			$approved = $res->Default;

		}

		if($approved == 'N') { $approved = 'P'; }

		$data['approved'] = $approved;

		// Check settings for auto-approve ads [end]



		// if the ad is not new then ->

		if(isset($data['id'])&&($data['id']>0)){

			$ex_ad = $this->getAdById($data['id']);

			$ex_ad->parameters = @unserialize($ex_ad->parameters);

			// if the ad was approved or declined and the new status would be 'pending', make sure changes occured to it!

			if(($ex_ad->approved != 'P')&&($data['approved'] == 'P')){

				// if no change has been made, let the status be as it was

				if(($ex_ad->title == $data['title'])&&($ex_ad->description == $data['description'])&&($ex_ad->target_url == $data['target_url'])&&($ex_ad->image_url == $data['image_url'])&&(stripslashes(@$ex_ad->parameters['alt_text_t']) == $data['linktitle'])&&(stripslashes(@$ex_ad->parameters['alt_text']) == $data['linktext'])&&(stripslashes(@$ex_ad->parameters['alt_text_a']) == $data['linkaction'])) {

					//&&(stripslashes(@$ex_ad->parameters['img_alt']) == $data['parameters']['img_alt'])

					$data['approved'] = $ex_ad->approved;

				}

				else{

					//it was changed, and sent email to administrator

					$notify = "0";

					$changed = "changed";

				}

			}

		}



		if(isset($ex_ad->parameters)){

			foreach($data['parameters']	as $key=>$value){

				$ex_ad->parameters[$key] = $value;

			}

			$data['parameters'] = $ex_ad->parameters;

		}



		if(!isset($data['parameters']['img_alt'])||($data['parameters']['img_alt'] == '')) {

			$data['parameters']['img_alt'] = $data['target_url'];

		}



		//echo "<pre>";var_dump($data);die();



		$data['parameters'] = serialize($data['parameters']);
		
		$parameters = $data["parameters"];
		$parameters = unserialize($parameters);
		if(!isset($parameters["target_window"])){
			$parameters["target_window"] = "_blank";
			$data["parameters"] = serialize($parameters);
		}
		
		$data["linktext"] = str_replace("\r\n", " ", $data["linktext"]);
  		$data["parameters"]["alt_text"] = str_replace("\r\n", " ", $data["parameters"]["alt_text"]);


		if (!$item->bind($data)){

			$res = false;

		}

		if (!$item->check()) {

			$res = false;

		}

		if (!$item->store()) {

			$res = false;

		}

		if (!isset($data['id']) || $data['id']==0) $data['id'] = mysql_insert_id();

		$idi = $data['id'];

		if ($idi==0) {

			$ask = "SELECT `id` FROM `#__ad_agency_banners` ORDER BY `id` DESC LIMIT 1 ";

			$db->setQuery( $ask );

			$idi = $db->loadResult();

		}

		if(isset($idi)) {

			$this->store_geo($idi);

		}

		require_once(JPATH_BASE . "/administrator/components/com_adagency/helpers/jomsocial.php");

		JomSocialTargeting::save($idi);

		

		$query = "DELETE FROM #__ad_agency_campaign_banner WHERE banner_id=".intval($idi);

		$db->setQuery($query);

		$db->query();



		if(isset($data['image_url'])&&($data['image_url'] != NULL)) {

			$img_url = JPATH_BASE.DS.'images'.DS.'stories'.DS.$configs->imgfolder.DS.$data['advertiser_id'].DS.$data['image_url'];

			//echo $img_url."<br />";

			@list($width_orig, $height_orig, $type, $attr) = @getimagesize($img_url);

			/*echo "Width original: ";var_dump($width_orig)."<br />";

			echo "Height original: ".$height_orig."<br />";

			echo "Type: ".$type."<br />";

			echo "Attr: ".$attr."<br />";

			echo "<pre>";var_dump($adv_cmp);echo "<hr />";//die();

			//die();*/

		}



		$fls = array();



        $campLimInfo = $adsModel->getCampLimInfo();

		//echo "<pre>";var_dump($campLimInfo);echo "<hr />";//die();



		if(isset($adv_cmp)){

			foreach ($adv_cmp as $val) {



                $zparams = NULL;$thumb = NULL;

                if(strstr($val,"|no|")) { $yes_no=0;} else { $yes_no=1; }

                $val = str_replace("|yes|","",$val);

                $val = str_replace("|no|","",$val);



				if ( ($val) && (

                    ( !isset($campLimInfo[$val])) || ( $campLimInfo[$val]['adslim'] > $campLimInfo[$val]['occurences'] )

                ) ) {

					if($yes_no == 1) {

                        $query = "INSERT INTO `#__ad_agency_campaign_banner` (`campaign_id` ,`banner_id` ,`relative_weighting` ,`thumb`, `zone`)

                                        VALUES ('".intval($val)."', '".intval($idi)."', '100', NULL, ".intval($data['czones'][$val]).");";

						//$query = "INSERT INTO #__ad_agency_campaign_banner VALUES ({$val},{$idi},100)";

						$db->setQuery($query);

						$db->query();



						$query = "SELECT z.adparams,z.textadparams FROM #__ad_agency_zone AS z WHERE z.zoneid = ".intval($data['czones'][$val]);

						$db->setQuery($query);

						$res = $db->loadObject();

						$zparams = @unserialize($res->adparams);

						$ex_ad->parameters = @unserialize($res->textadparams);



						if(isset($ex_ad->parameters['mxsize'])&&(intval($ex_ad->parameters['mxsize'])>0)&&(isset($ex_ad->parameters['mxtype']))) {

							if((($ex_ad->parameters['mxtype'] == 'w')&&($ex_ad->parameters['mxsize'] < $zparams['width']))||($zparams['width'] == '')){

								//$ratio = $zparams['width']/$zparams['height'];$zparams['height'] = $zparams['width']/$ratio;

								$zparams['width'] = $ex_ad->parameters['mxsize'];

							} elseif((($ex_ad->parameters['mxtype'] == 'h')&&($ex_ad->parameters['mxsize'] < $ex_ad->parameters['height']))||($zparams['height'] == '')){

								//$ratio = $zparams['width']/$zparams['height'];$zparams['width'] = $ratio * $zparams['height'];

								$zparams['height'] = $ex_ad->parameters['mxsize'];

							}



							if(isset($zparams['width']) && isset($zparams['height']) && isset($width_orig) && ($width_orig != NULL) && isset($height_orig) && (($width_orig > $zparams['width'])||($height_orig > $zparams['height'])) ) {

								// make the thumb with the maximum size or the zone

								//echo "Campaign id: ".$val."<br />";

								$thumb = $this->makeThumb($img_url, $zparams['width'], $zparams['height']);

								if(($thumb != NULL)&&($thumb != false)) {

									$sql = "UPDATE `#__ad_agency_campaign_banner` SET `thumb` = '".trim($thumb)."' WHERE `campaign_id` =".intval($val)." AND `banner_id` =".intval($idi).";";

								} else {

									$sql = "UPDATE `#__ad_agency_campaign_banner` SET `thumb` = NULL WHERE `campaign_id` =".intval($val)." AND `banner_id` =".intval($idi).";";

								}

								$db->setQuery($sql);

								$db->query();



								$fls[] = $thumb;

							}

						}

					}

				}

			}

		}

		//echo "<hr />";

		//var_dump($fls);

		//die();



		// Send email to administrator

		if ($notify=='0') {

			$sql = "SELECT a.user_id, a.company, a.telephone AS phone, u.name, u.username, u.email FROM #__ad_agency_advertis AS a LEFT OUTER JOIN #__users AS u ON u.id=a.user_id	WHERE a.aid=".intval($advid)." GROUP BY a.aid";

			$db->setQuery($sql);

			$user = $db->loadObject();

			if(isset($user)&&($user!=NULL)){

				$sql = "select `params` from #__ad_agency_settings";
				$db->setQuery($sql);
				$db->query();
				$email_params = $db->loadColumn();
				$email_params = @$email_params["0"];
				$email_params = unserialize($email_params);

				$ok_send_email = 0;
                $subject = "";
                $message = "";
                if($changed == "new"){
                	$subject = $configs->sbnewad;
	                $message = $configs->bodynewad;
                    $ok_send_email = $email_params["send_ban_added"];
                }
                else{
                	$subject = $configs->sbadchanged;
                	$message = $configs->boadchanged;
					$ok_send_email = $email_params["send_ad_modified"];
				}
				

				if(!isset($user->company)||($user->company=="")){$user->company = "N/A";}

				if(!isset($user->phone)||($user->phone=="")){$user->phone = "N/A";}



				$current_ad = $this->getAdById($idi);

				if(isset($current_ad->approved)&&($current_ad->approved == "Y")) { $status = JText::_("NEWADAPPROVED");} else { $status = JText::_("ADAG_PENDING");}



				$epreview = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&no_html=1&adid=".$idi."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&adid=".$idi."</a>";

				$eapprove = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=approve&cid=".$idi."&key=".$data['key']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=approve&cid=".$idi."&key=".$data['key']."</a>";

				$edecline = "<a href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=decline&cid=".$idi."&key=".$data['key']."' target='_blank'>".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=manage&action=decline&cid=".$idi."&key=".$data['key']."</a>";



				$subject =str_replace('{name}',$user->name,$subject);

				$subject =str_replace('{login}',$user->username,$subject);

				$subject =str_replace('{email}',$user->email,$subject);

				$subject =str_replace('{banner}',$data['title'],$subject);

				$subject =str_replace('{company}',$user->company,$subject);

				$subject =str_replace('{phone}',$user->phone,$subject);

				$subject =str_replace('{approval_status}',$status,$subject);

				$subject =str_replace('{banner_preview_url}',$epreview,$subject);

				$subject =str_replace('{approve_banner_url}',$eapprove,$subject);

				$subject =str_replace('{decline_banner_url}',$edecline,$subject);



				$message =str_replace('{name}',$user->name,$message);

				$message =str_replace('{login}',$user->username,$message);

				$message =str_replace('{email}',$user->email,$message);

				$message =str_replace('{banner}',$data['title'],$message);

				$message =str_replace('{company}',$user->company,$message);

				$message =str_replace('{phone}',$user->phone,$message);

				$message =str_replace('{approval_status}',$status,$message);

				$message =str_replace('{banner_preview_url}',$epreview,$message);

				$message =str_replace('{approve_banner_url}',$eapprove,$message);

				$message =str_replace('{decline_banner_url}',$edecline,$message);
				if($ok_send_email == 1){
					JFactory::getMailer()->sendMail( $configs->fromemail, $configs->fromname, $configs->adminemail, $subject, $message, 1 );
				}
							

			}

		}

		// END - Send email to administrator



		$sql = "SELECT approved FROM #__ad_agency_banners WHERE id = ".intval($idi);

		$db->setQuery($sql);

		$approved = $db->loadResult();



		$isWizzard = $this->isWizzard();



		$item_id = JRequest::getInt('Itemid','0');

		if($item_id != 0) { $Itemid = "&Itemid=".intval($item_id); } else { $Itemid = NULL; }



		$link = "index.php?option=com_adagency&controller=adagencyAds&p=1".$Itemid;

		$msg = JText::_('AD_BANNERSAVED');

		global $mainframe;

		if($isWizzard && $approved=='P'){

			$mainframe->redirect($link,$msg);

		}



		return $res;



	}





	function makeThumb($file_url, $width, $height){

		$handle = new Upload($file_url);



		list($width_orig, $height_orig) = @getimagesize($file_url);

		if(!isset($width_orig)||($width_orig == NULL)) { return false; }

		$ratio_orig = $width_orig/$height_orig;

		if($width == '') { $width = $height*$ratio_orig; }

		elseif($height == '') { $height = $width/$ratio_orig; }

		if ($width/$height > $ratio_orig) {

		   $width = $height*$ratio_orig;

		} else {

		   $height = $width/$ratio_orig;

		}



		$check['width'] = (int)$width;

		$check['height'] = (int)$height;

		if(($check['width'] == 0)&&($check['height'] == 0)){

			return NULL;

		}



		$width = number_format($width,0);

		$height = number_format($height,0);



		$pieces = explode(DS,$file_url);

		$filename = explode('.',$pieces[count($pieces)-1]);

		$pieces[count($pieces)-1] = '';

		$pieces = implode(DS,$pieces);

		$dir_dest = $pieces;



		if ($handle->uploaded) {

			$handle->image_resize			= true;

			$handle->image_x				= $width;

			$handle->image_y				= $height;

			$handle->file_new_name_body		= $filename[0].'_w'.$width.'_h'.$height;

			$handle->file_overwrite			= true;

			$handle->jpeg_quality 			= 100;



			$handle->Process($dir_dest);

			if ($handle->processed) {

				return $handle->file_dst_name;

			}  else {

				return false;

			}

		} else {

			return false;

		}

	}



	function gettextlistAdvertisers () {

		if (empty ($this->_package)) {

			$db = JFactory::getDBO();

			$sql = "SELECT a.aid, a.company, a.user_id FROM #__ad_agency_advertis AS a, #__users AS b WHERE a.user_id = b.id ORDER BY a.company ASC";

			$db->setQuery($sql);

			if (!$db->query()) {

				echo $db->stderr();

				return;

			}

			$this->_package = $db->loadObjectList();



		}

		return $this->_package;

	}



	function isWizzard(){

		$db = JFactory::getDBO();

		$sql = "SELECT `show` FROM #__ad_agency_settings ORDER BY id LIMIT 1";

		$db->setQuery($sql);

		$shown = $db->loadResult();

		$shown = explode(";",$shown);

		$isWizzard = false;

		foreach($shown as $element){

			if($element == "wizzard") { $isWizzard = true; }

		}

		return $isWizzard;

	}

	

	function getCampZones($campaigns){

		$return = array();

		if(isset($campaigns) && count($campaigns) > 0){

			$db = JFactory::getDBO();

			if(isset($campaigns) && $campaigns != "" && count($campaigns) > 0)

			foreach($campaigns as $key=>$campaign){

				$sql = "SELECT z.`zoneid`, z.`z_title`, z.`adparams` FROM `#__ad_agency_zone` z, `#__ad_agency_campaign` c, `#__ad_agency_package_zone` pz WHERE pz.zone_id=z.zoneid and c.otid=pz.package_id and c.id=".intval($campaign->id);

				$db->setQuery($sql);

				$db->query($sql);

				$zones = $db->loadAssocList();

				if(isset($zones) && count($zones) > 0){

					foreach($zones as $key_zone=>$zone){

						$params = $zone["adparams"];

						$params = unserialize($params);

						$zone["width"] = $params["width"];

						$zone["height"] = $params["height"];

						

						if(!isset($params["textad"])){

							continue;

						}

						

						$temp = $zone["z_title"]." ";

						

						$temp .= "(".JText::_("ADAG_ANYSIZE").")";

						/*if(intval($zone["width"]) == 0){

							$temp .= "(".JText::_("ADAG_ANYSIZE").")";

						}

						else{

							$temp .= "(".intval($zone["width"])." x ".intval($zone["height"])." px)";

						}*/

						

						$return[intval($campaign->id)][$zone["zoneid"]] = $temp;

					}

				}

				else{

					$return[intval($campaign->id)] = array();

				}

			}

			return $return;

		}

		else{

	 		return $return;

		}

	}



};

?>

