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
$campaigns_zones = $this->campaigns_zones;
$added = $this->added;
$params_component = $this->params;

$czones = $this->czones;
$czones_select = $this->czones_select;
$ad = $this->ad;

$configs = $this->configs;
$current = $this->channel;
$no_zone = $this->no_zone;
$no_size = $this->no_size;

$_row=$this->ad;
$nullDate = 0;
$livesite = JURI::base();
if (isset($this->banners_camps)) {
  $banners_camps = $this->banners_camps;
} else { $banners_camps = array(); }
if (!is_array($banners_camps)) { $banners_camps = array(); }
if (!isset($type)) $type='cpm';
if (!isset($package->type)) @$package->type=$type;
    foreach($realimgs as $k=>$v)
        $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
        $realimgs = implode(",\n", $realimgs);
require_once('components/com_adagency/helpers/ijPanes.php');


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

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
include_once("components/com_adagency/helpers/geo_fcs.php");
?>
<script language="JavaScript" type="text/javascript" src="<?php echo $livesite; ?>components/com_adagency/js/jscolor.js"></script>
<?php
	include_once(JPATH_BASE."/components/com_adagency/includes/js/flash.php");
	include_once(JPATH_SITE."/components/com_adagency/includes/js/flash_geo.php");	
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
				<a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69673988">
		            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
		            <?php echo JText::_("COM_ADAGENCY_VIDEO_FLASH_SETTINGS"); ?>   
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
                
                <div class="well well-minimized"><?php echo JText::_('NEWADIMAGE');?></div>
                
                <div class="control-group">
                    <label class="control-label">
                        <?php echo JText::_('NEWADSWFUPLOADIMG');?>
                    </label>
                    <div class="controls">
                        <input type="file" name="image_file" size="33" onchange="document.getElementById('upload-submit').click();">
                        <button style="display:none;" type="submit" id="upload-submit" onclick="return UploadImage();" class="btn btn-primary"><i class="icon-upload icon-white"></i> Start Upload</button>
                        <input type="hidden" name="swf_url" value="<?php echo $_row->swf_url;?>" />
                        <?php include(JPATH_BASE."/components/com_adagency/includes/js/flash_upl.php"); ?>
                        &nbsp; 
                        <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADUPLOADIMG_TIP2'); ?>" >
                        <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                    </div>
                </div>
                
                <div class="control-group">
                    <label class="control-label">
                        <?php echo JText::_('NEWADSWFPREVIEW');?>
                    </label>
                    <div class="controls">
                        <span id="swf_file">
						<?php
                            if ($_row->swf_url!=""){
                                $db = JFactory::getDBO();
                                $sql = "select `advertiser_id` from #__ad_agency_banners where `id`=".intval($_row->id);
                                $db->setQuery($sql);
                                $db->query();
                                $old_advertiser_id = $db->loadColumn();
                                $old_advertiser_id = $old_advertiser_id["0"];
                                $new_advertiser_id = $_row->advertiser_id;
                                
                                if($old_advertiser_id != $new_advertiser_id){
                                    // move image to new advertiser
                                    if(is_file(JPATH_SITE.DS."images".DS."stories".DS."ad_agency".DS.$old_advertiser_id.DS.$_row->swf_url)){
                                        $source = JPATH_SITE.DS."images".DS."stories".DS."ad_agency".DS.$old_advertiser_id.DS.$_row->swf_url;
                                        $destination = JPATH_SITE.DS."images".DS."stories".DS."ad_agency".DS.$new_advertiser_id.DS.$_row->swf_url;
                                        copy($source, $destination);
                                    }
                                }
                            
                        ?>
                            <OBJECT id="flash_ad_obj" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" ID="banner" <?php echo "WIDTH='" . $_row->width . "' HEIGHT='" . $_row->height . "'" ?>>
                            <PARAM NAME="movie" VALUE="<?php echo $lists['flash_directory'].$_row->swf_url; ?>?link=&window=_self">
                            <param name="wmode" value="transparent">
                            <PARAM NAME="quality" VALUE="high">
                            <EMBED id="flash_ad_embed" SRC="<?php echo $lists['flash_directory'].$_row->swf_url; ?>?link=&window=_self" <?php echo "WIDTH='" . $_row->width . "' HEIGHT='" . $_row->height . "'" ?> QUALITY="high" wmode="transparent" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer">
                            </EMBED>
                            </OBJECT>
                            <?php } ?>
                            </span>
    
                            <?php if ($_row->swf_url=="") { ?>
                            &nbsp; 
                            <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADPREVIEWFLASH_TIP'); ?>" >
                            <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                        <?php
                            }
                        ?>
                    </div>
                </div>
                
                <div class="control-group">
                	<label class="control-label">
						<?php echo JText::_('NEWADSIZE');?>
                        <span class="star">*</span>
                    </label>
                    <div class="controls">
                        <input class="formField"  type="text" name="width" id="width" size="3"	value="<?php if ($_row->width>0) {echo $_row->width;} else {echo "";} ?>" /> x
                        <input class="formField" type="text" name="height" id="height" size="3"	value="<?php if ($_row->height>0) {echo $_row->height;} else {echo "";} ?>" />
                        px
                        &nbsp;
                        <span class="editlinktip hasTip" title="<?php echo JText::_('NEWADSIZEFLASH_TIP'); ?>" >
                        <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
                        
                        <span class="before_warn"></span>
						<?php if($no_size) { echo $no_zone; } ?>
                        <br/><br/>
                        <div class="alert alert-notice">
                            <p><?php echo JText::_("ADAG_SELECT_CAMP_AFTER_RESIZE"); ?></p>
                        </div>
                        
                    </div>
            	</div>
                
            </div>
            
            <div class="tab-pane" id="advanced" width="100%">
                <div class="well well-minimized"><?php echo JText::_('NEWADHTMLPROP');?></div>
                
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
                            echo isset($_row->parameters['border_color']) ? $_row->parameters['border_color'] : '000000';
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
                            echo isset($_row->parameters['bg_color']) ? $_row->parameters['bg_color'] : 'FFFFFF';
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
                        <?php echo $lists['alignment'];?>
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
                        <?php echo $lists['window'];?>
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
					elseif(count($camps)==0){
						echo "<span id='no_camps'>".JText::_('ADAG_NO_CAMPAIGNS')."<p />".JText::_('ADAG_NO_CAMP_2')."<p /><iframe src=\"http://player.vimeo.com/video/17354105\" width=\"700\" height=\"393\" frameborder=\"0\"></iframe></span>";
					}
					else{
						echo "<span id='no_camps'></span>";
					}
				?>
                
				<?php if(isset($camps)&&($camps!=NULL)) { //this->assoc_camps
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
                        
                        <input type="hidden" id="flashMarker" />
                
                        <table class="table table-striped table-bordered" width="100%" id="flashCampaigns">
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
                                        
                                        <td style="width:40%" id="column<?php echo $i; ?>">
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
                                        
                                        <td align="left" class="check_ad" id="column_<?php echo $i; ?>">
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
															
															if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["flash"])){
																if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
																	$ok = TRUE;
																	break;
																}
															}
															elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["flash"])){
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
    <input type="hidden" id="sendmail" name="sendmail" value="1" />
    <input type="hidden" id="initvalcamp" value="" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="media_type" value="Flash" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="controller" value="adagencyFlash" />
    <input type="hidden" id="numberoflims" value="1" />
    <input type="hidden" id="administrator" value="true" />
    <input type="hidden" name="time_format" id="time_format" value="<?php echo $format_value; ?>" />
</form>