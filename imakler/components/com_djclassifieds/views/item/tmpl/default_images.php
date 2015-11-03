<?php
/**
* @version		2.0
* @package		DJ Classifieds
* @subpackage	DJ Classifieds Component
* @copyright	Copyright (C) 2010 DJ-Extensions.com LTD, All rights reserved.
* @license		http://www.gnu.org/licenses GNU/GPL
* @autor url    http://design-joomla.eu
* @autor email  contact@design-joomla.eu
* @Developer    Lukasz Ciastek - lukasz.ciastek@design-joomla.eu
* 
* 
* DJ Classifieds is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
*
* DJ Classifieds is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with DJ Classifieds. If not, see <http://www.gnu.org/licenses/>.
* 
*/

defined ('_JEXEC') or die('Restricted access');
$par = JComponentHelper::getParams( 'com_djclassifieds' );
$app = JFactory::getApplication();
$item = $this->item;
$main_img_width = $par->get('gallery_width','200')-4;
$thumbs_limit = $par->get('gallery_thumbs_in_row','3');
$thumb_width = round(($par->get('gallery_width','200')/$thumbs_limit)-14);
?>

	<div class="images_wrap" style="width:<?php echo $par->get('gallery_width','200');?>px"><div class="images">
				<div class="djc_images">
					<div class="djc_mainimage">
						<a id="djc_mainimagelink" rel="djc_lb_0" title="<?php echo $item->name; ?>" href="<?php echo JURI::base().$this->item_images[0]->thumb_b; ?>">
							<img width="<?php echo $main_img_width;?>px" id="djc_mainimage" alt="<?php echo $item->name; ?>" src="<?php echo JURI::base().$this->item_images[0]->thumb_b;?>" />
						</a>
					</div>
					<?php
					if (count($this->item_images) > 1) { ?>
						<div class="djc_thumbnails" id="djc_thumbnails">
						<?php foreach($this->item_images as $img){
								/*if($ii>0 && $ii%3==0){
									echo '<div style="clear:both"></div>';
								}*/
							 ?>
							<div class="djc_thumbnail">
								<a rel="<?php echo JURI::base().$img->thumb_b;?>" title="<?php echo $img->caption; ?>" href="<?php echo JURI::base().$img->thumb_b;?>">
									<img width="<?php echo $thumb_width;?>px" alt="<?php echo $img->caption; ?>" src="<?php echo JURI::base().$img->thumb_s;?>" />
								</a>
							</div>
							<?php } ?>
							<div style="clear:both"></div>
						</div>
					<?php } ?>
					<?php for($ii=0; $ii<count($this->item_images);$ii++ ){ ?>
						<a id="djc_lb_<?php echo $ii; ?>" rel="lightbox-djitem" title="<?php echo $this->item_images[$ii]->caption;?>" href="<?php echo JURI::base().$this->item_images[$ii]->thumb_b;?>" style="display: none;"></a>
					<?php } ?>
				</div>
	
	
			</div></div>			
	<script type="text/javascript">
		this.DJCFImageSwitcher = function (){
			var mainimagelink = document.id('djc_mainimagelink');
			var mainimage = document.id('djc_mainimage');
			var thumbs = document.id('djc_thumbnails') ? document.id('djc_thumbnails').getElements('img') : null;
			var thumblinks = document.id('djc_thumbnails') ? document.id('djc_thumbnails').getElements('a') : null;
			
			if(mainimagelink && mainimage) {
				mainimagelink.removeEvents('click').addEvent('click', function(evt) {
					var rel = mainimagelink.rel;
					document.id(rel).fireEvent('click', document.id(rel));
		
					if(!/android|iphone|ipod|series60|symbian|windows ce|blackberry/i.test(navigator.userAgent)) {
						return false;
					}
					return true;
				});
			}
			
			if (!mainimage || !mainimagelink || !thumblinks || !thumbs) return false;
			
			thumblinks.each(function(thumblink,index){
				var fx = new Fx.Tween(mainimage, {link: 'cancel', duration: 200});
		
				thumblink.addEvent('click',function(event){
					event.preventDefault();
					//new Event(element).stop();
					/*
					mainimage.onload = function() {
						fx.start('opacity',0,1);
					};
					*/
					var img = new Image();
					img.onload = function() {
						fx.start('opacity',0,1);
					};
					
					fx.start('opacity',1,0).chain(function(){
						mainimagelink.href = thumblink.href;
						mainimagelink.title = thumblink.title;
						mainimagelink.rel = 'djc_lb_'+index;
						img.src = thumblink.rel;
						mainimage.src = img.src;
						mainimage.alt = thumblink.title;
					});
					return false;
				});
			});
		}; 
								 
		window.addEvent('load', function(){	
			var img_width = document.id('dj-classifieds').getElement('.djc_images').getSize().x;
			var dj_item = document.id('dj-classifieds').getElement('.dj-item-in').getSize().x;
			var general_det = dj_item-img_width-19; 
			if(general_det<150){
				general_det='100%';
			}		
			document.id('dj-classifieds').getElement('.general_det').setStyle('width',general_det) ; 		
		});
		window.addEvent('domready', function(){		
			DJCFImageSwitcher();
		});
	</script>				