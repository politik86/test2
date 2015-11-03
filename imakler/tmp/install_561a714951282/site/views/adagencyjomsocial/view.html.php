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

class adagencyViewadagencyJomsocial extends JViewLegacy {
	
	function scandir_php4($dir) {
        $files = array();
        if ($handle = @opendir($dir)) {
            while (false !== ($file = readdir($handle))) {
                array_push($files, $file);
            }
            closedir($handle);
        }
        return $files;
    }
	
	function editForm($tpl = null) {
		global $mainframe;
		$size_selected = NULL;
        $data = JRequest::get('post');
        $db = JFactory::getDBO();
        $js_model = $this->getModel("adagencyJomsocial");
		$ad = $js_model->getad();
		
        $my = JFactory::getUser();
        $d_advertiser = $this->getModel("adagencyConfig")->getCurrentAdvertiser();
        $advertiser_id = (int)$d_advertiser->aid;
        $configs = $this->getModel("adagencyConfig")->getConf();
        $configs->geoparams = @unserialize($configs->geoparams);
        $itemid = $this->getModel("adagencyConfig")->getItemid('adagencyads');
		
        //check for valid id of the banner
        if($ad->id != 0) {
            if ($ad->advertiser_id!=$advertiser_id) die('You may edit only your banners');
            if (($ad->advertiser_id==$advertiser_id) && ($ad->media_type!="Jomsocial")) die('This banner id is not a JomSocial banner');
        }
        //check for valid id of the banner

        //get the image folder
        $imgfolder = $configs->imgfolder;
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
        if (!$isNew) $ad->parameters = @unserialize($ad->parameters);

        // Imagelist
        $javascript = 'onchange="changeDisplayImage();"';
        $directory = "images/stories/".$imgfolder."/{$advertiser_id}";
        $livesite = JURI::base();
        $absolutepath = JPATH_SITE;
        $image_folder = "{$absolutepath}{$directory}";
        $lists['image_path'] = "images/stories/".$imgfolder."/{$advertiser_id}/";
        if (!is_dir($image_folder)) {
            @mkdir($image_folder, 0755);
            @chmod($mosConfig_absolute_path."/images/stories/".$imgfolder, 0755);}
        $javascript = 'onchange="changeDisplayImage();"';
        
		$lists['image_directory'] = "images/stories/".$imgfolder."/".$advertiser_id."/";

        if(isset($ad->image_url)&&($ad->image_url!="")){
            $size_selected = @getimagesize($lists['image_directory'].$ad->image_url);
        } else {
            $size_selected = NULL;
        }
        $director=$image_folder;
        $imgs = $this->scandir_php4($director);
        $realimgs = array();
        foreach($imgs as $img)
            if(is_file($director."/".$img))
            {
                $props = @getimagesize($director."/".$img);
                if($props === false) continue;
                array_push($realimgs, array("width"=>$props[0],"height"=>$props[1],"name"=>"'".addslashes($img)."'"));
            }

        ///===================select available campaigns============================
        $adv_id = $advertiser_id;
        if($adv_id){
			$camps = $this->getModel("adagencyJomsocial")->getCampsByAid($adv_id);
        }
		else{
			$camps='';
		}
		
		$these_campaigns = $this->getModel("adagencyJomsocial")->getSelectedCamps($advertiser_id, $ad->id);
		
        $ad->width = $size_selected['0'];
		$ad->height = $size_selected['1'];	
        
        if (isset($ad->id)&&($ad->id != 0)) { $channel = $this->getModel("adagencyJomsocial")->getChannel($ad->id); } else { $channel = NULL; }
        if (isset($_SESSION['channelz'])) {
            $channel = new stdClass();
            $channel->sets[0] = $_SESSION['channelz'];
            unset($_SESSION['channelz']);
        }
        if (isset($_SESSION['channelz2'])) {
            $channel = NULL;
            $ad->channel_id = $_SESSION['channelz2'];
            unset($_SESSION['channelz2']);
        }

        $camps = $this->getModel("adagencyJomsocial")->getCampsByAid($adv_id, 1);
        $itemid_cpn = $this->getModel("adagencyConfig")->getItemid('adagencycpanel');

		$js_settings = $this->getModel("adagencyJomsocial")->getJomSocialSettings();
		
        $this->assign("js_settings", $js_settings);
		$this->assign("itemid", $itemid);
        $this->assign("itemid_cpn", $itemid_cpn);
        $this->assign ("size_selected",$size_selected);
        $this->assign("channel",$channel);
        $this->assign("configs", $configs);
        $this->assign("ad", $ad);
        $this->assign("lists", $lists);
        $this->assign("data", $data);
        $this->assign("camps", $camps);
        $this->assign("realimgs", $realimgs);
        $this->assign("advertiser_id", $advertiser_id);
		$this->assign("these_campaigns", $these_campaigns);
        
        parent::display($tpl);
    }
	
	function uploadbannerimage() {
		$database = JFactory::getDBO();
		$db = JFactory::getDBO();
		$advertiser_id = $this->getModel("adagencyAds")->getCurrentAdvertiser()->aid;
        if(!$advertiser_id){
			die('- Access denied! Not an advertiser! -');
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
		$advertiser_id = $this->getModel("adagencyAds")->getCurrentAdvertiser()->aid;
        if(!$advertiser_id){
			die('- Access denied! Not an advertiser! -');
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
