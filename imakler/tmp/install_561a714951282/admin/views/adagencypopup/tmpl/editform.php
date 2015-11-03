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

JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.modal');

$data = $this->data;
$realimgs = $this->realimgs;
$camps = $this->camps;
$lists = $this->lists;
$configs = $this->configs;
$current = $this->channel;
$campaigns_zones = $this->campaigns_zones;
$params_component = $this->params;

$czones = $this->czones;
$czones_select = $this->czones_select;
$ad = $this->ad;

$_row=$this->ad;
$added = $this->added;
$no_zone = $this->no_zone;
$nullDate = 0;
$editor1  =  JFactory::getEditor();
if (isset($this->banners_camps)) {
  $banners_camps = $this->banners_camps;
} else { $banners_camps = array(); }
if (!isset($_row->parameters['window_type'])) $_row->parameters['window_type']='popup';

if (!isset($_row->parameters['ad_code'])) $_row->parameters['ad_code']='';
foreach($realimgs as $k=>$v)
        $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
$realimgs = implode(",\n", $realimgs);
require_once('components/com_adagency/helpers/ijPanes.php');

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
$advertiser = $this->advt;


if(isset($advertiser->approved)&&($advertiser->approved != 'Y')){
    $advertiser_appr = "<div class=\"clearfix\"></div><br/>
						<div id=\"ajax_adv\">
							<div id=\"system-message-container\">
								<div class=\"alert alert-notice\">
									<p>".JText::_('ADAG_NOT_APPROVED_AD')."<br /<br />
										- <span id='approve_and_email' style='text-decoration:underline; cursor:pointer; color:#005580;'>".JText::_('ADAG_NOT_APPR_1')."</span><br />
										- <span id='approve_no_email' style='text-decoration:underline; cursor:pointer; color:#005580;'>".JText::_('ADAG_NOT_APPR_2')."</span>
										<br /><br />
										<span class='close_it' style='font-weight:bold; text-decoration: underline; cursor: pointer; color:#005580;' >".JText::_('ADAG_CLOSE')."</span>
										<input type='hidden' id='advertiser_aid' value='".$advertiser->aid."' />
									</p>
								</div>
							</div>
						</div>";
} else {
    $advertiser_appr = NULL;
}

include_once("components/com_adagency/helpers/geo_fcs.php");

?>
<?php echo $no_zone; ?>
<?php
	include_once(JPATH_BASE."/components/com_adagency/includes/js/popup.php");
	include_once(JPATH_SITE."/components/com_adagency/includes/js/popup_geo.php");
?>


<?php
	require_once(JPATH_BASE."/components/com_adagency/helpers/jomsocial.php");
	$helper = new JomSocialTargeting();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">
<div class="row-fluid">
      <div class="span12 pull-right">
           <h2 class="pub-page-title">
				<?php if(!isset($_row->id)) {echo JText::_('ADAG_NEWAD');} else {echo JText::_('ADAG_EDITAD');} ?>
			</h2>
			<a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69673991">
	            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
	            <?php echo JText::_("COM_ADAGENCY_VIDEO_POPUP_SETTINGS"); ?>   
	        </a>
      </div>
</div>

	
	<div class="row-fluid">
    	<ul class="nav nav-tabs">
            <li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('VIEWCONFIGCATGENERAL');?></a></li>
            <li><a href="#advanced" data-toggle="tab"><?php echo JText::_('ADAG_PROPERTIES');?></a></li>
            <li><a href="#location" data-toggle="tab"><?php echo JText::_('ADAG_LOCATION');?></a></li>
            <li><a href="#campaigns" data-toggle="tab"><?php echo JText::_('NEWADCMPS');?></a></li>
            <?php
				$helper->render($_row->id, "list");
			?>
            <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('VIEWORDERSPUBLISH');?></a></li>
        </ul>
        <div class="tab-content">
        	<div class="tab-pane active" id="general">
				<div class="well well-minimized"><?php echo  JText::_('NEWADDETAILS'); ?></div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_CHOOSEPOPUP');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<?php echo $lists['type'];?>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_CHOOSEPOPUP_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWADTITLE');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<input class="formField" type="text" name="title" size="30" value="<?php if ($_row->title!="") {echo $_row->title;} else {echo "";} ?>">
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADTITLE_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWADDESCTIPTION');?>
					</label>
					<div class="controls">
						<textarea name="description"><?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?></textarea>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADDESCTIPTION_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWADADVERTISER');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<div  id="to_be_replaced" style="float:left "><?php echo $lists['advertiser_id']; ?></div>
						&nbsp;
						<div style="float:left; padding-left:10px; "><a rel="{handler: 'iframe', size: {x: 700, y: 450}}" href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=addadv&tmpl=component" class="modal2">Add advertiser</a></div>
						<?php echo $advertiser_appr; ?>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADADVERTISER_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('AD_STATUS');?>
					</label>
					<div class="controls">
						<?php echo $lists['approved'];?>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('AD_STATUS_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('ADAG_KEYWORDS');?>
					</label>
					<div class="controls">
						<input class="formField" type="text" name="keywords" size="30" maxlength="200" value="<?php if(isset($_row->keywords)) { echo $_row->keywords; }?>" />&nbsp;
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_KEYWORDS_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						<br/>
						<div class="alert alert-notice">
							<p><?php echo JText::_('ADAG_KEYEXP');?></p>
						</div>
					</div>
				</div>
				
				<?php
					if('html'==$_row->parameters['popup_type']){
				?>
						<input class="formField" type="hidden" name="target_url" size="30" maxlength="200" value="URL" >
				<?php
					}
					elseif('webpage'==$_row->parameters['popup_type']){
				?>
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('JAS_TARGETURL_POPURL');?>
								<span class="star">*</span>
							</label>
							<div class="controls">
								<input class="formField" type="text" name="parameters[page_url]" size="30" maxlength="200" value="<?php echo (@$_row->parameters['page_url']) ? $_row->parameters['page_url'] : 'http://'; ?>">
								&nbsp; 
								<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_TARGETURL_POPURL_TIP'); ?>" >
								<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
							</div>
						</div>
				<?php
					}
					
					if('image'==$_row->parameters['popup_type']){
				?>
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('JAS_TARGETURL_POPIMG');?>
								<span class="star">*</span>
							</label>
							<div class="controls">
								<input class="formField" type="text" name="target_url" size="30" value="<?php if (isset($_row->target_url) && $_row->target_url!='URL') echo $_row->target_url; else echo 'http://'; ?>" >
								&nbsp; 
								<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADTARGET_TIP'); ?>" >
								<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
							</div>
						</div>
				<?php
					}
					
					if('image'==$_row->parameters['popup_type']){
				?>
						<div class="well well-minimized"><?php echo "Image"?></div>
				
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('JAS_UPLOADIMAGE');?>
							</label>
							<div class="controls">
								<input type="file" name="image_file" onchange="document.getElementById('button_upload').click();" size="33">
								<input type="submit" id="button_upload" style="display:none;" value="Upload" onclick="return UploadImage();">
								<input type="hidden" name="image_url" value="<?php if(isset($_row->image_url)) echo $_row->image_url;?>" />
								<script  language="javascript" type="text/javascript">
									function UploadImage() {

										if (getSelectedValue2('adminForm','advertiser_id') < 1) {
											alert( "<?php echo JText::_('JS_SELECT_ADV');?>" );
										}
										else {
											var fileControl = document.adminForm.image_file;
											var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
													if (thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG")
														{ alert('<?php echo JText::_('JS_INVALIDIMG');?>');
														  return false;
														}
											if (fileControl.value) {
												alert("<?php echo addslashes(JText::_("ADAGENCY_CHANGE_SIZE")); ?>");
												document.adminForm.task.value = 'upload';
												return true;
												//submitbutton('upload');
											}
											return false;
										}
										return false;
									}
								</script>
								<?php if(!isset($_row->image_url)) { ?>
								&nbsp; 
								<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADUPLOADIMG_TIP'); ?>" >
								<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
								<?php } ?>
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('JAS_IMGPREVIEW');?>
							</label>
							<div class="controls">
								<?php if (!isset($_row->image_url)) { //$_row->id?>
										<div id="imgdiv" style="display: block;">
											
										</div>
										<?php
											}
											else{
												$db = JFactory::getDBO();
												$sql = "select `advertiser_id` from #__ad_agency_banners where `id`=".intval($_row->id);
												$db->setQuery($sql);
												$db->query();
												$old_advertiser_id = $db->loadColumn();
												$old_advertiser_id = $old_advertiser_id["0"];
												$new_advertiser_id = $_row->advertiser_id;
												
												if($old_advertiser_id != $new_advertiser_id){
													// move image to new advertiser
													if(is_file(JPATH_SITE.DS."images".DS."stories".DS."ad_agency".DS.$old_advertiser_id.DS.$_row->image_url)){
														$source = JPATH_SITE.DS."images".DS."stories".DS."ad_agency".DS.$old_advertiser_id.DS.$_row->image_url;
														$destination = JPATH_SITE.DS."images".DS."stories".DS."ad_agency".DS.$new_advertiser_id.DS.$_row->image_url;
														copy($source, $destination);
													}
												}
										?>
										<div id="imgdiv" style="display: block;">
										<img src="<?php echo $lists['image_directory'].$_row->image_url; ?>" name="imagelib" style="height:200px;" />
										</div>
										<?php } ?>
								  <?php if (!isset($_row->image_url)) {	?>
											&nbsp; 
											<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADPREVIEW_TIP'); ?>" >
											<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
								  <?php } ?>
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('JAS_IMGSIZE');?>
								<span class="star">*</span>
							</label>
							<div class="controls">
								<?php
									if ($_row->image_url){
										$fileimg = JPATH_SITE.$lists['image_path'].$_row->image_url;
										$my_image = @getimagesize($fileimg);
										list($width, $height) = $my_image;
										$_row->width = $width;
										$_row->height = $height;
									}
								?>
								<input class="formField"  type="text" name="width" size="3"	value="<?php echo @$_row->width; ?>" /> x
								<input class="formField" type="text" name="height" size="3" value="<?php echo @$_row->height; ?>" />
								px
								&nbsp; 
								<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADSIZE_TIP'); ?>" >
								<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
							</div>
						</div>
				<?php
					}
					
					if ('html'==$_row->parameters['popup_type']) {
				?>
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('JAS_HTMLCONTENT');?>
								<span class="star">*</span>
							</label>
							<div class="controls">
								<?php
									if(isset($_row->parameters['html'])){
										$_row->parameters['html'] = stripslashes($_row->parameters['html']);
										echo $editor1->display( 'parameters[html]', html_entity_decode($_row->parameters['html']),'100%', '300px', '20', '60' );
									}
									else{
										echo $editor1->display( 'parameters[html]', '','100%', '300px', '20', '60' );
									}
								?>
							</div>
						</div>
						
						<div class="control-group">
							<label class="control-label">
								<?php echo JText::_('NEWADFREQV');?>
							</label>
							<div class="controls">
								<?php
									if(isset($_row->parameters['show_ad'])){
								?>
										<select name="parameters[show_ad]">
											<option value="<?php echo JText::_('NEWADEVERY_TIME_VALUE') ; ?>" <?php if ( $_row->parameters['show_ad'] == 0 ) echo "selected" ; ?>><?php echo JText::_('NEWADEVERY_TIME') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_DAY_VALUE') ; ?>" <?php if ( $_row->parameters['show_ad'] == 1 ) echo "selected" ; ?>><?php echo JText::_('NEWADONCE_A_DAY') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_WEEK_VALUE') ; ?>" <?php if ( $_row->parameters['show_ad'] == 7 ) echo "selected" ; ?>><?php echo JText::_('NEWADONCE_A_WEEK') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_MONTH_VALUE') ; ?>" <?php if ( $_row->parameters['show_ad'] == 30 ) echo "selected" ; ?>><?php echo JText::_('NEWADONCE_A_MONTH') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_YEAR_VALUE') ; ?>" <?php if ( $_row->parameters['show_ad'] == 365 ) echo "selected" ; ?>><?php echo JText::_('NEWADONCE_A_YEAR') ; ?></option>
										</select>
										&nbsp; 
										<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADFREQV_TIP'); ?>" >
										<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
								<?php
									}
									else{
								?>
										<select name="parameters[show_ad]">
											<option value="<?php echo JText::_('NEWADEVERY_TIME_VALUE') ; ?>" ><?php echo JText::_('NEWADEVERY_TIME') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_DAY_VALUE') ; ?>" ><?php echo JText::_('NEWADONCE_A_DAY') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_WEEK_VALUE') ; ?>" selected="selected" ><?php echo JText::_('NEWADONCE_A_WEEK') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_MONTH_VALUE') ; ?>" ><?php echo JText::_('NEWADONCE_A_MONTH') ; ?></option>
											<option value="<?php echo JText::_('NEWADONCE_A_YEAR_VALUE') ; ?>" ><?php echo JText::_('NEWADONCE_A_YEAR') ; ?></option>
										</select>
								<?php
									}
								?>
							</div>
						</div>
				<?php
					}
				?>
				
			</div>
			
			<div class="tab-pane" id="advanced">
				<div class="well well-minimized"><?php echo JText::_('JAS_HTMLPROP') ;?></div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WINDOWTYPE');?>
					</label>
					<div class="controls">
						<select class="formField" size="1" name="parameters[window_type]">
							<option value="popunder" <?php  if (isset($_row->parameters['window_type']) && $_row->parameters['window_type']=="popunder") { echo "selected"; } ?>><?php echo JText::_('JAS_POPUNDER'); ?></option>
							<option value="popup" <?php if (isset($_row->parameters['window_type']) && $_row->parameters['window_type']=="popup") { echo "selected"; } ?>><?php echo JText::_('JAS_POPUP'); ?></option>
						</select>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WINDOWTYPE_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WITHTOOLBAR');?>
					</label>
					<div class="controls">
						<select class="formField" size="1" name="parameters[toolbar]">
							<option value="0" <?php if (isset($_row->parameters['toolbar']) && $_row->parameters['toolbar']==0) { echo "selected"; } ?>><?php echo JText::_('JAS_NO'); ?></option>
							<option value="1" <?php if (isset($_row->parameters['toolbar']) && $_row->parameters['toolbar']==1) { echo "selected"; } ?>><?php echo JText::_('JAS_YES'); ?></option>
						</select>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WITHTOOLBAR_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WITHSTATUSBAR');?>
					</label>
					<div class="controls">
						<select class="formField" size="1" name="parameters[status]">
							<option value="0" <?php if (isset($_row->parameters['status']) && $_row->parameters['status']==0) { echo "selected"; } ?>><?php echo JText::_('JAS_NO'); ?></option>
							<option value="1" <?php if (isset($_row->parameters['status']) && $_row->parameters['status']==1) { echo "selected"; } ?>><?php echo JText::_('JAS_YES'); ?></option>
						</select>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WITHSTATUSBAR_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WITHMENUBAR');?>
					</label>
					<div class="controls">
						<select class="formField" size="1" name="parameters[menubar]">
							<option value="0" <?php if (isset($_row->parameters['menubar']) && $_row->parameters['menubar']==0) { echo "selected"; } ?>><?php echo JText::_('JAS_NO'); ?></option>
							<option value="1" <?php if (isset($_row->parameters['menubar']) && $_row->parameters['menubar']==1) { echo "selected"; } ?>><?php echo JText::_('JAS_YES'); ?></option>
						</select>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('CONFIGFROMNAME_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WITHSCROLLBAR');?>
					</label>
					<div class="controls">
						<select class="formField" size="1" name="parameters[scrollbars]">
							<option value="0" <?php if (isset($_row->parameters['scrollbars']) && $_row->parameters['scrollbars']==0) { echo "selected"; } ?>><?php echo JText::_('JAS_NO'); ?></option>
							<option value="1" <?php if (isset($_row->parameters['scrollbars']) && $_row->parameters['scrollbars']==1) { echo "selected"; } ?>><?php echo JText::_('JAS_YES'); ?></option>
						</select>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WITHSCROLLBAR_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WITHRESIZE');?>
					</label>
					<div class="controls">
						<select class="formField" size="1" name="parameters[resizable]">
							<option value="0" <?php if (isset($_row->parameters['resizable']) && $_row->parameters['resizable']==0) { echo "selected"; } ?>><?php echo JText::_('JAS_NO'); ?></option>
							<option value="1" <?php if (isset($_row->parameters['resizable']) && $_row->parameters['resizable']==1) { echo "selected"; } ?>><?php echo JText::_('JAS_YES'); ?></option>
						</select>
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WITHRESIZE_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WINDOWWIDTH');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<input type="text" class="formField" size="5" value="<?php if (isset($_row->parameters['window_width'])) echo $_row->parameters['window_width']; else echo '300'; ?>" name="parameters[window_width]">
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WINDOWWIDTH_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('JAS_WINDOWHEIGHT');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<input type="text" class="formField" size="5" value="<?php if (isset($_row->parameters['window_height'])) echo $_row->parameters['window_height']; else echo '300'; ?>" name="parameters[window_height]">
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('JAS_WINDOWHEIGHT_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					</div>
				</div>
				
			</div>
			
			<div class="tab-pane" id="location">
				<?php
                    require_once(JPATH_SITE.DS."administrator".DS."components".DS."com_adagency".DS."helpers".DS."createlocation.php");
                ?>
            </div>
			
			<div class="tab-pane" id="campaigns">
				<?php
					if($this->advertiser_id == 0) {
						echo "<span id='no_camps'>".JText::_('ADAG_SEL_CMPADV')."</span>";
					}
					elseif (count($camps)==0) {
						echo "<span id='no_camps'>".JText::_('ADAG_NO_CAMPAIGNS')."</span><p />".JText::_('ADAG_NO_CAMP_2')."<p /><iframe src=\"http://player.vimeo.com/video/17354105\" width=\"700\" height=\"393\" frameborder=\"0\"></iframe>";
					}
					else {
						echo "<span id='no_camps'></span>";
					}
				?>
				
				<?php if(isset($camps)&&($camps)) {//$this->assoc_camps
					$i=0;?>
						<table width="100%">
                            <tr>
                                <td align="left" width="100%" colspan="2" class="well well-minimized"><?php echo JText::_('ADD_NEWADCMPS'); ?>
                                    <span class="adag_tip">
                                        <img src="components/com_adagency/images/tooltip.png" border="0" />
                                        <span><?php echo JText::_('NEWADCMPS_TIP'); ?></span>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    
                        <br/><br/>
                
                        <table class="table table-striped table-bordered" width="100%" id="affiliateCampaigns">
                            <thead>
                                <th>
                                </th>
                                <th>
                                    <?php echo JText::_("CONFIGCMP"); ?>
                                </th>
                                <th>
                                    <?php echo JText::_("ADAG_ZONES_SIZES"); ?>
                                </th>
                                <th>
                                    <?php echo JText::_("ADAG_ON_WHICH_ZONE"); ?>
                                </th>
                            </thead>
                            <tbody>
                            <?php
                                $displayed = array();
                                foreach($camps as $camp){
                                    $displayed[] = $camp->id;
                                    $i++;
                            ?>
                                    <tr>
                                        <td class="check_camp">
                                            <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
                                            <input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
                                            if(in_array($camp->id,$banners_camps)){
                                                echo 'checked="checked"';
                                            }
                                            ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
                                            <span class="lbl"></span>
                                        </td>
                                        
                                        <td>
                                            <label><?php echo $camp->name; ?></label>
                                        </td>
                                        
                                        <td style="width:40%">
                                            <?php
												if(isset($czones) && count($czones) > 0){
													if(isset($czones[$camp->id])){
														foreach($czones[$camp->id] as $czone){
															$zone_width = $czone["width"];
															$zone_height = $czone["height"];
															$ad_width = $this->ad->width;
															$ad_height = $this->ad->height;
															
															if(trim($zone_width) != "" && trim($zone_height) != ""){
																if(trim($zone_width) < trim($ad_width) && trim($zone_height) < trim($ad_height)){
																	unset($campaigns_zones[$camp->id][$czone["zoneid"]]);
																}
															}
														}
													}
												}
                                                
                                                if(isset($campaigns_zones[$camp->id]) && count($campaigns_zones[$camp->id]) > 0){
                                                    echo implode("<br/>", $campaigns_zones[$camp->id]);
                                                }
                                            ?>
                                        </td>
                                        
                                        <td align="left" class="check_ad">
                                            <?php
                                                $ok = FALSE;
                                                if(isset($czones) && count($czones) > 0){
													if(isset($czones[$camp->id])){
														foreach($czones[$camp->id] as $czone){
															$zone_width = $czone["width"];
															$zone_height = $czone["height"];
															$ad_width = $ad->width;
															$ad_height = $ad->height;
															
															$params = $czone["adparams"];
															$params = unserialize($params);
															
															if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["popup"])){
																if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
																	$ok = TRUE;
																	break;
																}
															}
															elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["popup"])){
																$ok = TRUE;
																break;
															}
														}
													}
												}
                                            
                                                if($ok){
                                                    echo $czones_select[$camp->id];
                                                }
                                                else{
                                                    echo '<span class="label label-important">'.JText::_("ADAG_SIZE_OF_AD_UPLOADED")." (".$_row->width." x ".$_row->height." px) ".JText::_("ADAG_NOT_SUPPORTED_BY_THIS_CAMPAIGN").'</span>';
                                                }
                                            ?>
                                        </td>
                                    </tr>
                            <?php
                                }
                            ?>
                            </tbody>
                        </table>
				<?php
					}
				?>
			</div>
            
         
			
			<div class="tab-pane" id="publishing">
				
				<div class="well well-minimized"><?php echo JText::_("VIEWORDERSPUBLISH"); ?></div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('ADAG_START_PUBLISHING');?>
					</label>
					<div class="controls">
						<?php
							$format_string = "Y-m-d";
							$ad_start_date = date($format_string);
							if(isset($_row->ad_start_date)){
								$ad_start_date = $_row->ad_start_date;
							}
							
							$ad_end_date = "0000-00-00 00:00:00";
							if(isset($_row->ad_end_date)){
								$ad_end_date = $_row->ad_end_date;
							}
							if($ad_end_date == "0000-00-00 00:00:00"){
								$ad_end_date = "Never";
							}
						
							$format_value = $params_component["timeformat"];
							
							switch($format_value){
								case "0" : {
									$format_string = "Y-m-d H:i:s";
									$ad_start_date = date($format_string, strtotime($ad_start_date));
									$ad_end_date = $ad_end_date != "Never" ? date($format_string, strtotime($ad_end_date)) : "Never";
									break;
								}
								case "1" : {
									$format_string = "m/d/Y H:i:s";
									$ad_start_date = date($format_string, strtotime($ad_start_date));
									$ad_end_date = $ad_end_date != "Never" ? date($format_string, strtotime($ad_end_date)) : "Never";
									break;
								}
								case "2" : {
									$format_string = "d-m-Y H:i:s";
									$ad_start_date = date($format_string, strtotime($ad_start_date));
									$ad_end_date = $ad_end_date != "Never" ? date($format_string, strtotime($ad_end_date)) : "Never";
									break;
								}
								case "3" : {
									$format_string = "Y-m-d";
									$ad_start_date = date($format_string, strtotime($ad_start_date));
									$ad_end_date = $ad_end_date != "Never" ? date($format_string, strtotime($ad_end_date)) : "Never";
									break;
								}
								case "4" : {
									$format_string = "m/d/Y";
									$ad_start_date = date($format_string, strtotime($ad_start_date));
									$ad_end_date = $ad_end_date != "Never" ? date($format_string, strtotime($ad_end_date)) : "Never";
									break;
								}
								case "5" : {
									$format_string = "d-m-Y";
									$ad_start_date = date($format_string, strtotime($ad_start_date));
									$ad_end_date = $ad_end_date != "Never" ? date($format_string, strtotime($ad_end_date)) : "Never";
									break;
								}
							}
							
							$format_string_2 = str_replace ("-", "-%", $format_string);
							$format_string_2 = str_replace ("/", "/%", $format_string_2);
							$format_string_2 = "%".$format_string_2;
							$format_string_2 = str_replace("H:i:s", "%H:%M:%S", $format_string_2);
						
							echo JHtml::calendar(trim($ad_start_date), 'ad_start_date', 'ad_start_date', $format_string_2, '');
						?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('ADAG_FINISH_PUBLISHING');?>
					</label>
					<div class="controls">
						<?php
							if($ad_end_date == "Never"){
								$ad_end_date = "";
							}
							
							$calendar = JHtml::calendar(trim($ad_end_date), 'ad_end_date', 'ad_end_date', $format_string_2, ''); 
							
							if($ad_end_date == ""){
								$calendar = str_replace('value=""', 'value="Never"', $calendar);
							}
							echo $calendar;
						?>
					</div>
				</div>
				
			</div>
			   <?php
        		$helper->render($_row->id, "");
			?>
		</div>
	</div>
	
	<?php if (isset($_GET['act']) && ($_GET['act']=='new')) { ?>
	<input type="hidden" name="act" value="new" />
	<?php } ?>
	<input type="hidden" class="inputbox" checked="checked" value="onload" id="parameters[show_on]" name="parameters[show_on]"/>
	<?php if ($_row->id > 0) { ?>
	<input type="hidden" name="parameters[popup_type]" value="<?php echo $_row->parameters['popup_type'];?>" />
	<?php } ?>
	<input type="hidden" id="sendmail" name="sendmail" value="1" />
	<input type="hidden" id="initvalcamp" value="" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="media_type" value="Popup" />
	<input type="hidden" name="id" value="<?php echo $_row->id;?>" />
	<input type="hidden" name="controller" value="adagencyPopup" />
	<input type="hidden" id="numberoflims" value="1" />
	<input type="hidden" id="administrator" value="true" />
    <input type="hidden" name="time_format" id="time_format" value="<?php echo $format_value; ?>" />
</form>
