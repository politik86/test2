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

class adagencyAdminViewadagencyJomsocial extends JViewLegacy {
	
	function editForm($tpl = null){
		$model = $this->getModel("adagencyJomsocial");
		$ad = $model->getAdDetails();
		$db = JFactory::getDBO();
		
		$isNew = intval(@$ad["0"]["id"]) < 1;
		$text = $isNew ? JText::_('New') : JText::_('Edit');
		
		JToolBarHelper::title(JText::_('VIEWTREEADDJOMSOCIAL').":<small>[".$text."]</small>");
		/*JToolBarHelper::apply('apply');
		JToolBarHelper::save('save');
		JToolBarHelper::cancel('cancel');*/
		
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'post');
		$advt = "";
		$model_config = $this->getModel("adagencyConfig");
		
		if(intval(@$ad["0"]["id"]) > 0){
			$advt = $model_config->getAdvById(intval(@$ad["0"]["advertiser_id"]));
		}
		else{
			$advt = NULL;
		}
		$this->advt = $advt;
		
		$approved = 'Y';
		if(!$isNew){
			$approved = @$ad["0"]["approved"];
		}
		
		if(isset($_SESSION["approved"]) && trim($_SESSION["approved"]) != ""){
			$approved = trim($_SESSION["approved"]);
		}
		
		$helper = new adagencyAdminModeladagencyJomsocial();
		
		$javascript = 'onchange="submitbutton(\'edit\');"';
		$advertisers[] = JHTML::_('select.option',  "0", JText::_('AD_SELECT_ADVERTISER'), 'aid', 'company' );
	    $advertisersloaded = $helper->getstandardlistAdvertisers();
	    $advertisers = array_merge($advertisers, $advertisersloaded);
		
		if(isset($ad["0"]["advertiser_id"]) && intval($ad["0"]["advertiser_id"]) != 0){
			$advertiser_id = intval($ad["0"]["advertiser_id"]);
		}
	    $lists['advertiser_id'] = JHTML::_( 'select.genericlist', $advertisers, 'advertiser_id', 'class="inputbox" size="1"'.$javascript, 'aid', 'company', $advertiser_id);
		
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
		$statuses[] = $sts_select;
		$statuses[] = $sts_approve;
		$statuses[] = $sts_decline;
		$statuses[] = $sts_pending;
		$lists['approved'] = JHTML::_('select.genericlist', $statuses, 'approved', 'class="inputbox" size="1"', 'value', 'status', $approved);
		$this->js_settings = $this->get("JomSocialSettings");
		
		$sql = "SELECT `imgfolder` FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$db->query();
		$imgfolder = $db->loadResult();
		
		$directory 	= "/images/stories/".$imgfolder."/{$advertiser_id}";
		$absolutepath = JPATH_SITE;
		$image_folder = "{$absolutepath}{$directory}";
		$lists['image_path'] = "../images/stories/".$imgfolder."/{$advertiser_id}/";
		if(!is_dir($image_folder)){
			@mkdir($image_folder, 0755);
			@chmod($mosConfig_absolute_path."/images/stories/".$imgfolder, 0755);
		}
		
		if(isset($this->uploaded_file)){
			$ad["0"]["image_url"] = $this->uploaded_file;
		}
		
		if(isset($this->uploaded_file_content)){
			$ad["0"]["image_content"] = $this->uploaded_file_content;
		}
		
		$this->lists = $lists;
		$this->ad = $ad;
		
		$adv_id = $advertiser_id;
		if($adv_id){
			$camps = $this->getModel("adagencyJomsocial")->getCampsByAid($adv_id);
		}
		else{
			$camps='';
		}
		
		$this->assign('advertiser_id', $advertiser_id);
		$this->assign("camps", $camps);
		
		$sql="SELECT DISTINCT cb.campaign_id FROM #__ad_agency_banners AS b LEFT OUTER JOIN #__ad_agency_campaign_banner AS cb ON cb.banner_id=b.id WHERE b.advertiser_id=".intval($advertiser_id)." AND b.id=".intval(intval(@$ad["0"]["id"]));
		
		$db->setQuery($sql);
		$db->query();
		$banners_camps = $db->loadColumn();
		$this->assign("banners_camps", (array)$banners_camps);
		
		$configs = $model_config->getConf();
		$configs->geoparams = @unserialize($configs->geoparams);
		$this->assign("configs", $configs);
		
		if(isset($ad["0"]["id"])&&(intval($ad["0"]["id"]) != 0)){
			$channel = $model->getChannel($ad["0"]["id"]);
		}
		else{
			$channel = NULL;
		}
		
		if(isset($_SESSION['channelz'])){
			$channel = new stdClass();
			$channel->sets[0] = $_SESSION['channelz'];
			unset($_SESSION['channelz']);
		}
		if(isset($_SESSION['channelz2'])){
			$channel = NULL;
			$ad["0"]["channel_id"] = $_SESSION['channelz2'];
			unset($_SESSION['channelz2']);
		}

		$this->assign("channel",$channel);
		
		parent::display($tpl);
	}
	
	function uploadbannerimage() {
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'get');
		if(!$advertiser_id){
			die('return');
		}
		
		//get the image folder
		$sql = "SELECT `imgfolder` FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$db->query();
		$imgfolder = $db->loadResult();

		$targetPath = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/';
		$failed = '0';
		if(isset($_FILES['image_file'])){
			$filename = time();
			$filename2 = $_FILES['image_file']['name'];
			
			if($filename2){
				$filenameParts = explode('.', $filename2);
				$extension = '';
				
				if(count($filenameParts) > 1){
					$extension = array_pop($filenameParts);
				}
				
				$extension = strtolower($extension);
				if(!in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))){
					$text = strip_tags( addslashes( nl2br( "The image must be gif, png, jpg, jpeg." )));
					echo "<script>alert('$text');</script>";
					$failed=1;
				}
				
				if($failed != 1){
					$filename .= '.'.$extension;
					if(!move_uploaded_file($_FILES['image_file']['tmp_name'], $targetPath.$filename) || !chmod($targetPath.$filename, 0644)){
						$text = strip_tags(addslashes(nl2br("Upload of ".$filename2." failed.")));
						echo "<script>alert('$text'); </script>";
					}
					else{
						$img_size = getimagesize(JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename);
						$width_old = $img_size["0"];
						$height_old = $img_size["1"];
						$width = 100;
						$height = 100;
						
						if($height_old < $height){ 
							$width = $height_old;
							$height = $height_old;
						}
						
						$source = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename;
						$destination = JPATH_SITE.DS.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename;;
						$this->cropImage($source, $destination, $filename, $width, $height, $imgfolder, $advertiser_id);
						
						return '/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename;
					}
				}
			 }
		}
	}
	
	function uploadbannerimagecontent(){
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$advertiser_id = JRequest::getVar('advertiser_id', '', 'get');
		
		if(!$advertiser_id){
			die('return');
		}
		
		//get the image folder
		$sql = "SELECT `imgfolder` FROM #__ad_agency_settings LIMIT 1";
		$db->setQuery($sql);
		$db->query();
		$imgfolder = $db->loadResult();

		$targetPath = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/';
		$failed = '0';
		if(isset($_FILES['image_content_file'])){
			$filename = time();
			$filename2 = $_FILES['image_content_file']['name'];
			
			if($filename2){
				$filenameParts = explode('.', $filename2);
				$extension = '';
				
				if(count($filenameParts) > 1){
					$extension = array_pop($filenameParts);
				}
				
				$extension = strtolower($extension);
				
				if(!in_array($extension, array('jpg', 'jpeg', 'gif', 'png'))){
					$text = strip_tags( addslashes( nl2br( "The image must be gif, png, jpg, jpeg." )));
					echo "<script>alert('$text');</script>";
					$failed=1;
				}
				
				if($failed != 1){
					$filename .= '.'.$extension;
					if(!move_uploaded_file($_FILES['image_content_file']['tmp_name'], $targetPath.$filename) || !chmod($targetPath.$filename, 0644)){
						$text = strip_tags(addslashes(nl2br("Upload of ".$filename2." failed.")));
						echo "<script>alert('$text'); </script>";
					}
					else{
						$img_size = getimagesize(JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename);
						$width_old = $img_size["0"];
						$height_old = $img_size["1"];
						$width = 0;
						$height = 0;
						
						if($width_old > 480){ 
							//proportional by width
							$raport = $width_old / $height_old;
							$width = 480;
							$height = intval(480/$raport);				
						}
						else{
							$width = $width_old;
							$height = $height_old;					
						}
						
						$sql = "select `params` from #__ad_agency_settings";
						$db->setQuery($sql);
						$db->query();
						$params = $db->loadColumn();
						$params = @$params["0"];
						$params = unserialize($params);
						if(!isset($params["image_content_height"]) || intval($params["image_content_height"]) == 0){
							$params["image_content_height"] = 270;
						}
						
						if($height > $params["image_content_height"]){
							echo "<script>alert('".JText::_("ADAG_HEIGHT_TOO_BIG")." ".$params["image_content_height"]." px!'); </script>";
							die();
						}
						
						return '/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename;
					}
				}
			 }
		}
	}
	
	function cropImage($source_image, $new_image, $image_name, $resizedWidth, $resizedHeight, $imgfolder, $advertiser_id){
		$img_size = getimagesize($source_image);
		$width_old = $img_size["0"];
		$height_old = $img_size["1"];
		$width = 0;
		$height = 0;
		$new_width = 100;
		
		if($width_old > $new_width){ 
			//proportional by width
			$raport = $width_old / $height_old;
			$width = $new_width;
			$height = intval($new_width/$raport);
			
			while($height < 100){
				$new_width ++;
				$raport = $width_old / $height_old;
				$width = $new_width;
				$height = intval($new_width/$raport);
			}
		}
		else{
			$width = $width_old;
			$height = $height_old;					
		}
		
		$filename = $this->create_thumbnails($imgfolder, $source_image, $width, $height, $width_old, $height_old, $advertiser_id);
		$source_image = JPATH_SITE.'/images/stories/'.$imgfolder.'/'.$advertiser_id.'/'.$filename;
		
		$source_image = str_replace(" ", "%20", $source_image);
		$img_size = @getimagesize($source_image);
		$img_wide = $img_size["0"];
		$img_high = $img_size["1"];
		
		
		
		$dst_x = 0;
		$dst_y = 0;
		$src_x = ($img_wide - $resizedWidth)/2; // Crop Start X
		$src_y = ($img_high - $resizedHeight)/2; // Crop Srart Y
		$dst_w = $resizedWidth; // Thumb width
		$dst_h = $resizedHeight; // Thumb height
		$src_w = $resizedWidth; //$src_x + $dst_w;
		$src_h = $resizedHeight; //$src_y + $dst_h;
		
		$image_name_array = explode(".", $image_name);
	   	$ext = $image_name_array[count($image_name_array) - 1];
		$ext = strtolower($ext);
		switch($ext){
			case "jpg":				
				$dst_image = @imagecreatetruecolor($dst_w, $dst_h);
				$src_image = @imagecreatefromjpeg($source_image);
				@imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
				@imagejpeg($dst_image, $new_image);
				break;
			case "jpeg":				
				$dst_image = @imagecreatetruecolor($dst_w, $dst_h);
				$src_image = @imagecreatefromjpeg($source_image);
				@imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
				@imagejpeg($dst_image, $new_image);
				break;
			case "gif":
				$dst_image = @imagecreatetruecolor($dst_w, $dst_h);
				$src_image = @imagecreatefromgif($source_image);
				@imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
				@imagegif($dst_image, $new_image);
				break;
			case "png":
				$dst_image = @imagecreatetruecolor($dst_w, $dst_h);
				$src_image = @imagecreatefrompng($source_image);
				@imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
				@imagepng($dst_image, $new_image);
				break;
        }
	}
	
	function create_thumbnails($gallery_folder, $images, $width, $height, $width_old, $height_old, $advertiser_id){
		if($images == ""){
			return;
		}
		
		$images = trim($images);
		$get_path = explode('/', $images);
		$nr = (count($get_path) - 1);		
		$photo_name = $get_path[$nr];
		unset($get_path[$nr]);
		$path = implode("/", $get_path);
	
		$mosConfig_absolute_path = JPATH_ROOT;
		$mosConfig_live_site = JURI :: base();

		$width_2 = $width;
		$height_2 = $height;
		
		$gdimg = null;
		$pic = explode(".", $photo_name);
		$ext = $pic[count($pic)-1]; 
		$ext = strtolower($ext);

		switch($ext){
			case "jpg":				
				$gdimg = @imagecreatefromjpeg($images);
				break;
			case "jpeg":				
				$gdimg = @imagecreatefromjpeg($images);
				break;
			case "gif": 
				$gdimg = @imagecreatefromgif($images);
				break;
			case "png":
				$gdimg = @imagecreatefrompng($images);
				break;
		}

		//create image regenerated
		if($ext == "png"){
			$image_p = @imagecreatetruecolor($width_2, $height_2);
			@imagealphablending($image_p, false);
			@imagesavealpha($image_p, true);
			$source = @imagecreatefrompng($images);
			@imagealphablending($source, true);
			@imagecopyresampled($image_p, $source, 0, 0, 0, 0, $width_2, $height_2, $width_old, $height_old);
		}
		elseif($ext != 'gif'){
			$image_p = @imagecreatetruecolor($width_2, $height_2);
			$trans = @imagecolorallocate($image_p, 0,0,0);
			@imagecolortransparent($image_p, $trans);
			@imagecopyresampled($image_p, $gdimg, 0, 0, 0, 0, $width_2, $height_2, $width_old, $height_old);
		}
		else{ 	
			$image_p = @imagecreate($width_2, $height_2);
			$trans = @imagecolorallocate($image_p,0,0,0);
			@imagecolortransparent($image_p,$trans);
			@imagecopyresized($image_p, $gdimg, 0, 0, 0, 0, $width_2, $height_2, $width_old, $height_old);				
		}
		
		$name = rand(1,50).$photo_name;
		
		$upload_th = "";
		
		if($ext == "jpg" || $ext == "JPG"){
			$upload_th = @imagejpeg($image_p, JPATH_ROOT.DS.'images/stories'.DS.$gallery_folder.DS.$advertiser_id.DS.$name, 100);
		}
		if($ext == "jpeg" || $ext == "JPEG"){
			$upload_th = @imagejpeg($image_p, JPATH_ROOT.DS.'images/stories'.DS.$gallery_folder.DS.$advertiser_id.DS.$name, 100);			
		}
		if($ext == "gif" || $ext == "GIF"){
			$upload_th = @imagegif($image_p, JPATH_ROOT.DS.'images/stories'.DS.$gallery_folder.DS.$advertiser_id.DS.$name, 100); 
		}	
		if($ext == "png" || $ext == "PNG"){
			$upload_th = @imagepng($image_p, JPATH_ROOT.DS.'images/stories'.DS.$gallery_folder.DS.$advertiser_id.DS.$name);
		}
		
		if($upload_th){
			unlink($images);
			return $name;
		}	
		else{
			return $images;
		}
	}
}
?>
