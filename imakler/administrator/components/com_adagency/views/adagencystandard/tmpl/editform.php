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

$czones = $this->czones;
$czones_select = $this->czones_select;
$ad = $this->ad;

$campaigns_zones = $this->campaigns_zones;
$no_zone = $this->no_zone;
$no_size = $this->no_size;
$_row=$this->ad;
$configs = $this->configs;
$current = $this->channel;
$added=$this->added;
$params_component = $this->params;

if (isset($this->banners_camps)){
	$banners_camps = $this->banners_camps;
}
else{
	$banners_camps = array();
}

$nullDate = 0;
$livesite = JURI::base();
if (!isset($type)) $type='cpm';
if (!isset($package->type)) @$package->type=$type;
foreach($realimgs as $k=>$v)
    $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
$realimgs = implode(",\n", $realimgs);
    $siz_sel = $this->size_selected;
//if(isset($_row->width)&&isset($_row->height)&&(($_row->width==0)||($_row->height==0))) {
    if(isset($siz_sel[0])&&isset($siz_sel[1])&&($siz_sel[0]>0)&&($siz_sel[1]>0)){
        $_row->width=$siz_sel[0];$_row->height=$siz_sel[1];
    }
//}

$advertiser = $this->advt;
//echo "<pre>";var_dump($advertiser->approved);echo "</pre><hr />";
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

$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
include_once("components/com_adagency/helpers/geo_fcs.php");
if (!is_array($banners_camps)) {
    $banners_camps = array();
}
?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $livesite; ?>components/com_adagency/js/jscolor.js"></script>
<?php
	include_once(JPATH_BASE."/components/com_adagency/includes/js/standard.php");
	include_once(JPATH_SITE."/components/com_adagency/includes/js/standard_geo.php");
?>

<?php if(!$no_size) { echo $no_zone; } ?>
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
				<a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69673997">
		            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
		            <?php echo JText::_("COM_ADAGENCY_VIDEO_STANDARD_SETTINGS"); ?>   
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
                    <textarea name="description" rows="2"><?php if ($_row->description!="") {echo $_row->description;} else {echo "";} ?></textarea>
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
                    <div id="to_be_replaced" style="float:left "><?php echo $lists['advertiser_id']; ?></div>
                    &nbsp;
                    <div style="float:left; padding-left:10px; ">
                    	<a rel="{handler: 'iframe', size: {x: 700, y: 450}}" href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=addadv&tmpl=component" class="modal2">
                        	Add advertiser
						</a>
					</div>
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
                    <?php echo JText::_('NEWADTARGET');?>
                    <span class="star">*</span>
                </label>
                <div class="controls">
                    <input class="formField" type="text" name="target_url" size="30" value="<?php if ($_row->target_url!="") {echo $_row->target_url;} else {echo "http://";} ?>" >
                    &nbsp;
					<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADTARGET_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('ADAG_KEYWORDS');?>
                </label>
                <div class="controls">
                    <input class="formField" type="text" name="keywords" size="30" maxlength="200" value="<?php if(isset($_row->keywords)) { echo $_row->keywords; }?>" />
                    &nbsp;
					<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_KEYWORDS_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                    
                    <br/>
                    <div class="alert alert-notice">
						<p><?php echo JText::_('ADAG_KEYEXP');?></p>
					</div>
                </div>
            </div>
            
            <div class="well well-minimized"><?php echo JText::_('NEWADIMAGE'); ?></div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADUPLOADIMG');?>
                </label>
                <div class="controls">
                    <input type="file" name="image_file" size="20" onchange="document.getElementById('button_upload').click();" />
                    <button style="display:none;" type="submit" id="button_upload" onclick="return UploadImage();" class="btn btn-primary"><i class="icon-upload icon-white"></i> Start Upload</button>
                    <input type="hidden" name="image_url" value="<?php if(isset($_row->image_url)) echo $_row->image_url;?>" />
                    
					<script  language="javascript" type="text/javascript">
						function getSelectedValue2( frmName, srcListName ) {
							var form = eval( 'document.' + frmName );
							var srcList = form[srcListName];
							//alert(srcList);

							i = srcList.selectedIndex;
							if (i != null && i > -1) {
								return srcList.options[i].value;
							} else {
								return null;
							}
						}
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
                    &nbsp;&nbsp;
					<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADUPLOADIMG_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADPREVIEW');?>
                </label>
                <div class="controls">
                    <?php
						if(!$_row->id){
					?>
							<div id="imgdiv" class="imgOutline" style="display: block; float:left;">
					<?php
								if(!$_row->image_url){
									echo '';
								}
								else{
									echo '<img src="'.$lists["image_directory"].$_row->image_url.'" name="imagelib" />';
								}
					?>
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
							<div id="imgdiv" class="imgOutline" style="display: block; float:left;">
								<img src="<?php echo $lists['image_directory'].$_row->image_url; ?>" name="imagelib" />
							</div>
					<?php
						}
					?>
	
				   <?php if (($_row->width>0)&&($_row->height>0)) {} else { ?>
					&nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADPREVIEW_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				   <?php } ?>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADSIZE');?>
                </label>
                <div class="controls">
					<?php if ($_row->width>0) {echo $_row->width; echo "<input type='hidden' name='width' value='".$_row->width."' />";} else {echo "";} ?> x <?php if ($_row->height>0) {echo $_row->height; echo "<input type='hidden' name='height' value='".$_row->height."' />";} else {echo "";} ?>
					<?php if($no_size) { echo $no_zone; } ?>
                    
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADSIZE_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
        </div>
        
        <div class="tab-pane" id="advanced">
        	<div class="well well-minimized"><?php echo JText::_('NEWADHTMLPROP');?></div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADALT');?>
                    <span class="star">*</span>
                </label>
                <div class="controls">
					<input class="formField" type="text" name="parameters[alt_text]" size="30" maxlength="200" value="<?php if (@$_row->parameters['alt_text']!="") {echo @$_row->parameters['alt_text'];} else {echo "";}?>">
                    
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADALT_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADBORDER');?>
                </label>
                <div class="controls">
					<?php echo $lists['border']; ?>
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADBORDER_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADPADDING');?>
                </label>
                <div class="controls">
					<?php echo $lists['padding']; ?>
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADPADDING_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADBCOLOR');?>
                </label>
                <div class="controls">
					<input type="text" size="7" class="color input-mini" value="<?php
                        if(isset($_row->parameters["border_color"])) { echo $_row->parameters["border_color"];} else { echo "000000"; }
                    ?>" name="parameters[border_color]" >
					
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADBCOLOR_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADBGCOLOR');?>
                </label>
                <div class="controls">
					<input type="text" size="7" class="color input-mini" value="<?php
                        if(isset($_row->parameters["bg_color"])) { echo $_row->parameters["bg_color"];} else { echo "FFFFFF"; }
                    ?>" name="parameters[bg_color]" >
					
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADBGCOLOR'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADALIGN');?>
                </label>
                <div class="controls">
					<?php echo $lists['align']; ?>
					
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADALIGN_TIP'); ?>" >
                    <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                </div>
            </div>
            
            <div class="control-group">
                <label class="control-label">
                    <?php echo JText::_('NEWADTARGWIN');?>
                </label>
                <div class="controls">
					<?php echo $lists['window']; ?>
					
                    &nbsp;
                    <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADTARGWIN_TIP'); ?>" >
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
            	if($this->advertiser_id == 0){
					echo "<span id='no_camps'>".JText::_('ADAG_SEL_CMPADV')."</span>";
				}
				elseif (count($camps)==0){
					echo "<span id='no_camps'>".JText::_('ADAG_NO_CAMPAIGNS')."</span><p />".JText::_('ADAG_NO_CAMP_2')."<p /><iframe src=\"http://player.vimeo.com/video/17354105\" width=\"700\" height=\"393\" frameborder=\"0\"></iframe>";
				}
				else{
					echo "<span id='no_camps'></span>";
				}
			
				if(isset($camps)&&($camps!=NULL)){
					$i=0;
			?>
            		<div class="well well-minimized"><?php echo JText::_('ADD_NEWADCMPS'); ?>
                        &nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADCMPS_TIP'); ?>" >
                        <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                    </div>
            
                    <table class="table table-striped table-bordered" width="100%" id="affiliateCampaigns">
                        <thead>
                            <th class="span1">
                            </th>
                            <th class="span4">
                                <?php echo JText::_("CONFIGCMP"); ?>
                            </th>
                            <th class="span4">
                                <?php echo JText::_("ADAG_ZONES_SIZES"); ?>
                            </th>
                            <th class="span3">
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
                                    <td class="check_camp span1">
                                        <input type="hidden" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>|no|" />
                                        <input class="formField camp<?php echo $camp->id; ?>" type="checkbox" <?php
                                        if(in_array($camp->id,$banners_camps)){
                                            echo 'checked="checked"';
                                        }
                                        ?>id="adv_cmp<?php echo $i;?>" name="adv_cmp[<?php echo $i;?>]" value="<?php echo $camp->id; ?>"  />
                                        <span class="lbl"></span>
                                    </td>
                                    
                                    <td class="span3">
                                        <label><?php echo $camp->name; ?></label>
                                    </td>
                                    
                                    <td class="span3">
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
                                    
                                    <td class="check_ad span5">
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
														
														if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["standard"])){
															if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
																$ok = TRUE;
																break;
															}
														}
														elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["standard"])){
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

<?php
	if(isset($_GET['act']) && ($_GET['act']=='new')){
?>
		<input type="hidden" name="act" value="new" />
<?php
	}
?>
    <input type="hidden" id="sendmail" name="sendmail" value="1" />
    <input type="hidden" id="initvalcamp" value="" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="media_type" value="Standard" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="controller" value="adagencyStandard" />
    <input type="hidden" id="numberoflims" value="1" />
    <input type="hidden" id="administrator" value="true" />
    <input type="hidden" name="time_format" id="time_format" value="<?php echo $format_value; ?>" />
    
    <?php
    	$camp_id = JRequest::getVar("camp_id", "0");
		if(intval($camp_id) != 0){
			echo '<input type="hidden" id="camp_id" name="camp_id" value="'.intval($camp_id).'" />';
		}
	?>

</form>