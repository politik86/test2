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
$configs = $this->configs;
$current = $this->channel;
$camps = $this->camps;
$lists = $this->lists;
$_row=$this->ad;

$czones = $this->czones;
$czones_select = $this->czones_select;
$ad = $this->ad;
$params_component = $this->params;

$campaigns_zones = $this->campaigns_zones;
$nullDate = 0;
$livesite = JURI::base();
$width2 = NULL;$height2 = NULL;
$realimgs = $this->realimgs;
$max_chars = $this->max_chars;
$no_zone = $this->no_zone;
$helper = new adagencyAdminModeladagencyTextlink();
if (isset($this->banners_camps)) {
  $banners_camps = $this->banners_camps;
} else { $banners_camps = array(); }
if (!isset($type)) $type='cpm';
if (!isset($package->type)) @$package->type=$type;
if (!isset($_row->parameters['title_color'])) $_row->parameters['title_color'] = '0066CC';
if (!isset($_row->parameters['body_color'])) $_row->parameters['body_color'] = '000000';
if (!isset($_row->parameters['action_color'])) $_row->parameters['action_color'] = '0066CC';
if (!isset($_row->parameters['border_color'])) $_row->parameters['border_color'] = '000000';
if (!isset($_row->parameters['bg_color'])) $_row->parameters['bg_color'] = 'FFFFFF';
if (!isset($_row->parameters['sizeparam'])) $_row->parameters['sizeparam'] = 0;
if (!isset($_row->parameters['alt_text'])||($_row->parameters['alt_text']=='')) {
    $alts = $helper->getAltsAd($_row->id);
    if(isset($alts['alt_text'])) $_row->parameters['alt_text'] = $alts['alt_text'];
    if(isset($alts['alt_text_t'])) $_row->parameters['alt_text_t'] = $alts['alt_text_t'];
    if(isset($alts['alt_text_a'])) $_row->parameters['alt_text_a'] = $alts['alt_text_a'];
}
foreach($realimgs as $k=>$v)
    $realimgs[$k] = "[{$v["width"]},{$v["height"]},{$v["name"]}]";
$realimgs = implode(",\n", $realimgs);
//echo "<pre>";var_dump($_row);die();
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_adagency/css/joomla16.css');
$document->addScript('components/com_adagency/js/serialize_unserialize.js');
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");
include_once("components/com_adagency/helpers/geo_fcs.php");
require_once('components/com_adagency/helpers/ijPanes.php');


if(!isset($_row->parameters['border_color'])) { $_row->parameters['border_color'] = NULL; }
if(!isset($_row->parameters['bg_color'])) { $_row->parameters['bg_color'] = NULL; }
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

?>
	<script language="JavaScript" type="text/javascript" src="<?php echo $livesite; ?>components/com_adagency/js/jscolor.js"></script>
<?php
	include_once(JPATH_BASE."/components/com_adagency/includes/js/textlink.php");
	include_once(JPATH_SITE."/components/com_adagency/includes/js/textlink_geo.php");
?>
<?php echo $no_zone; ?>


<?php
	require_once(JPATH_BASE."/components/com_adagency/helpers/jomsocial.php");
	$helperJomSocial = new JomSocialTargeting();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="form-horizontal">	
<div class="row-fluid">
    <div class="span12 pull-right">
          <h2 class="pub-page-title">
				<?php if(!isset($_row->id)) {echo JText::_('ADAG_NEWAD');} else {echo JText::_('ADAG_EDITAD');} ?>
		  </h2>
		  <a class="modal adagency-video-manager pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69673998">
            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
            <?php echo JText::_("COM_ADAGENCY_VIDEO_TEXT_SETTINGS"); ?>   
        </a>
    </div>
</div>

	<?php
    	$after_upload_image = JRequest::getVar("after_upload_image", "false");
		$general_active = 'class="active"';
		$advanced_active = '';
		if($after_upload_image == "true"){
			$general_active = '';
			$advanced_active = 'class="active"';
		}
	?>
	<div class="row-fluid">
    	<ul class="nav nav-tabs">
            <li <?php echo $general_active; ?> ><a href="#general" data-toggle="tab"><?php echo JText::_('VIEWCONFIGCATGENERAL');?></a></li>
            <li <?php echo $advanced_active; ?> ><a href="#advanced" data-toggle="tab"><?php echo JText::_('ADAG_PROPERTIES');?></a></li>
            <li><a href="#location" data-toggle="tab"><?php echo JText::_('ADAG_LOCATION');?></a></li>
            <li><a href="#campaigns" data-toggle="tab"><?php echo JText::_('NEWADCMPS');?></a></li>
            <?php
				$helperJomSocial->render($_row->id, "list");
			?>
            <li><a href="#publishing" data-toggle="tab"><?php echo JText::_('VIEWORDERSPUBLISH');?></a></li>
        </ul>
        <div class="tab-content">
        	<div class="tab-pane <?php if(trim($general_active) != ""){echo 'active';} ?>" id="general">
				<div class="well well-minimized"><?php echo  JText::_('NEWADDETAILS');?></div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('AD_TEXTLINK_NAME');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<input class="formField" type="text" name="title" size="30" value="<?php echo $_row->title;?>">
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
						<textarea name="description"><?php echo $_row->description;?></textarea>
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
						<?php echo JText::_('NEWADTARGETURL');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<input class="formField" type="text" name="target_url" size="30" value="<?php if (!$_row->target_url) echo 'http://'; else echo $_row->target_url;?>" >
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
				
            </div>
            
            <div class="tab-pane <?php if(trim($advanced_active) != ""){echo 'active';} ?>" id="advanced">
            	<div class="well well-minimized">
					<?php echo JText::_('NEWADHTMLPROP');?>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWAD_LINKTEXT_TITLE');?>
						<span class="star">*</span>
					</label>
					<div class="controls">
						<input class="formField" type="text" id="clinktitle" onkeyup="javascript:changeLinkTitle()" name="linktitle" size="29" value="<?php if(isset($_POST['linktitle'])) { echo stripslashes($_POST['linktitle']); } else {echo stripslashes(@$_row->parameters['alt_text_t']);} ?>" />
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADTITLE_TIP3'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
							<tr align="left">
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTFAM'); ?>:
								<br/><?php echo $lists['font_family']; ?></td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTSIZE'); ?>:
								<br/><?php echo $lists['font_size']; ?></td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTCOLOR'); ?>
								<br/>
								<input type="text" size="7" class="color input-mini" value="<?php echo $_row->parameters['title_color'];?>" name="parameters[title_color]" >
								</td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTWEIGHT'); ?>:
								<br/><?php echo $lists['font_weight']; ?></td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWAD_LINKTEXT_BODY');?>
					</label>
					<div class="controls">
						<input class="formField" name="parameters[alt_text]" type="hidden"/>
						<input class="formField" name="parameters[alt_text_t]" type="hidden"/>
						<input class="formField" name="parameters[alt_text_a]" type="hidden"/>
						
						<textarea name="linktext" id="linktext" wrap="physical" cols="28" rows="8" onkeyup="var lng=<?php echo $max_chars;?>; if (document.adminForm.linktext.value.length > lng) document.adminForm.linktext.value=document.adminForm.linktext.value.substring(0,lng); else document.adminForm.nume.value = lng-document.adminForm.linktext.value.length; changeBody();"><?php if(isset($_POST['linktext'])) { echo stripslashes($_POST['linktext']); } else { echo stripslashes(@$_row->parameters['alt_text']);}?></textarea>
                        
                        <script type="text/javascript" language="javascript">
							function pasteIntercept(evt) {
								setTimeout(function(){
									var lng=<?php echo $max_chars;?>;
									if(document.adminForm.linktext.value.length > lng){
										document.adminForm.linktext.value=document.adminForm.linktext.value.substring(0,lng);
									}
									else{
										document.adminForm.nume.value = lng-document.adminForm.linktext.value.length;
									}
									changeBody();
								}, 0);
							}
							document.getElementById("linktext").addEventListener("paste", pasteIntercept, false);
						</script>
                        
						&nbsp;
						<span class="editlinktip hasTip" title="<?php echo JText::_('TEXTADBODY_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						<br /><?php echo 'characters left';?>:
						<input type="text" size="4" readonly="" style="color:#FF0000; font-weight:bold; background-color:transparent;border:0px solid white" value="<?php echo ($max_chars - strlen(stripslashes(@$_row->parameters['alt_text']))) ;?>" name="nume" id="nume" />
						<br/>
						<table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
							<tr align="left">
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTFAM'); ?>:
								<br/><?php echo $lists['font_family_b']; ?></td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTSIZE'); ?>:
								<br/><?php echo $lists['font_size_b']; ?></td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTCOLOR'); ?>
								<br/>
								<input type="text" size="7" class="color input-mini" value="<?php echo $_row->parameters['body_color'];?>" name="parameters[body_color]" >
								</td>
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTWEIGHT'); ?>:
								<br/><?php echo $lists['font_weight_b']; ?></td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWAD_LINKTEXT_ACTIONTEXT');?>
					</label>
					<div class="controls">
						<input class="formField" id="clinkaction" onkeyup="changeAction()" type="text" name="linkaction" size="29" value="<?php if(isset($_POST['linkaction'])) { echo stripslashes($_POST['linkaction']); } else { echo stripslashes(@$_row->parameters['alt_text_a']);} ?>" />
						&nbsp;
						<span class="editlinktip hasTip" title="<?php echo JText::_('TEXTADACTIONLINK_TIP'); ?>" >
						<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						<br />
						<table width="100%" cellpadding="0" cellspacing="0" border="0" align="left">
							<tr align="left">
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTFAM'); ?>:
								<br/><?php echo $lists['font_family_a']; ?></td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTSIZE'); ?>:
								<br/><?php echo $lists['font_size_a']; ?></td>
								
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTCOLOR'); ?>
								<br/><input type="text" size="7" class="color input-mini" value="<?php echo $_row->parameters['action_color'];?>" name="parameters[action_color]" >
								</td>
								<td width="10%" align="left"><?php echo JText::_('NEWADFONTWEIGHT'); ?>:
								<br/><?php echo $lists['font_weight_a']; ?></td>
							</tr>
						</table>
					</div>
				</div>
				
				<div class="well well-minimized"><?php echo  JText::_('ADAG_TEXTPROPS');?></div>
				
				<div class="control-group">
					<label class="control-label">
						
					<div class="controls">
						<div>
							<?php echo JText::_('ADSELZONE'); ?>:&nbsp;<select id="zoneId" onchange="callZoneSettings();return false;"><option value="0">--------</option><?php echo $lists['prevzones'];?></select>
						</div>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('VIEWADPREVIEW');?>
					</label>
					<div class="controls">
						<?php if (!$_row->id) { ?>
						<div id="textlink">
							<a id="tlink">
								<span id="ttitle">&nbsp;</span>
							</a>
							<br />
							<div id="imgdiv2" <?php if(!isset($_row->image_url)||($_row->image_url=='')){ echo 'style="display:none"';} ?>>
								<a id="tlink2">
									<img src="<?php
										$style = "";
									if(isset($_row->image_url)&&($_row->image_url!='')){
										echo $lists["image_directory"].$_row->image_url;
										$image_size = getimagesize($lists["image_directory"].$_row->image_url);
										if(isset($image_size["1"]) && $image_size["1"] > 200){
											$style = 'style="height:200px;"';
										}
									} else {
										echo "images/blank.png";
									}?>" name="imagelib" <?php echo $style; ?> id="rt_image" />
								</a>
							</div>
							<div id="tbody">
								<span id="ttbody">&nbsp;</span>
							</div>
							<div id="taction">
								<a id="tlink2">
									<span id="ttaction">&nbsp;</span>
								</a>
							</div>
						</div>
							<?php } else { ?>
							<div id="textlink" style="overflow:hidden;margin-left:25%;margin-top:5%;
							<?php
								if(isset($_row->parameters['border'])&&($_row->parameters['border']!='')){
									echo "border: ".$_row->parameters['border']."px solid #".$_row->parameters['border_color']."; ";
								}
								if(isset($_row->width)&&($_row->width!="")&&($_row->width!=0)){
									echo " width: ".$_row->width;
									if(isset($_row->parameters['sizeparam'])&&($_row->parameters['sizeparam']!=0)) {
										echo "%; ";
									} else { echo "px; ";}
								}
								if(isset($_row->height)&&($_row->height!="")&&($_row->height!=0)){
									echo " height: ".$_row->height;
									if(isset($_row->parameters['sizeparam'])&&($_row->parameters['sizeparam']!=0)) {
										echo "%; ";
									} else { echo "px; ";}
								}
								if(isset($_row->parameters['bg_color'])&&($_row->parameters['bg_color']!="")){
									echo " background-color: #".$_row->parameters['bg_color']."; ";
								}
								if(isset($_row->parameters['padding'])&&($_row->parameters['padding']!="")){
									echo " padding: ".$_row->parameters['padding']."px; ";
								}
							?>">
							<?php
								if(isset($_row->parameters['alt_text_t'])&&($_row->parameters['alt_text_t']!="")){
									if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
										echo "<a id='tlink' href='".$_row->target_url."' ";
										if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
											echo " target='".$_row->parameters['target_window']."' ";
										}
										if(isset($_row->parameters['title_color'])&&($_row->parameters['title_color']!="")){
											echo " style='color: ".$_row->parameters['title_color']."' ";
										}
										echo ">";
									}
									echo "<span id='ttitle' style='";
									if(isset($_row->parameters['font_size'])&&($_row->parameters['font_size']!="")){
										echo "font-size: ".$_row->parameters['font_size']."px; ";
									}
									if(isset($_row->parameters['font_family'])&&($_row->parameters['font_family']!="")){
										echo "font-family: ".$_row->parameters['font_family']."; ";
									}
									if(isset($_row->parameters['font_weight'])&&($_row->parameters['font_weight']!="")){
										echo "font-weight: ".$_row->parameters['font_weight']."; ";
									}
									if(isset($_row->parameters['title_color'])&&($_row->parameters['title_color']!="")){
										echo "color: #".$_row->parameters['title_color']."; ";
									}
									echo "'>";
									echo $_row->parameters['alt_text_t']."</span><br />";
									if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
										echo "</a>";
									}
								}
							?>
							<div id="imgdiv2" <?php if(!isset($_row->image_url)||($_row->image_url=='')){ echo 'style="display:none"';} ?>>
							<?php
								if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
									$outputz="<a id='tlink2' href='".$_row->target_url."' ";
									if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
											$outputz.="target='".$_row->parameters['target_window']."' ";
									}
									$outputz.=">";
									echo $outputz;
								}?>
								
								<?php
									$style = "";
									if(isset($_row->image_url)&&($_row->image_url!='')) {
										$image_size = getimagesize($lists["image_directory"].$_row->image_url);
										if(isset($image_size["1"]) && $image_size["1"] > 200){
											$style = 'style="height:200px;"';
										}
									}
								?>
								
								<?php if(isset($_row->image_url)&&($_row->image_url!='')) { ?><img src="<?php echo $lists['image_directory'].$_row->image_url; ?>" name="imagelib" <?php echo $style; ?> id="rt_image" /><?php } ?>					 							<?php
								if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
									echo "</a>";
								}
							?>
							</div>
							<div id="tbody">
								<?php
									if(isset($_row->parameters['alt_text'])&&($_row->parameters['alt_text']!="")){
										echo "<span id='ttbody' style='";
										if(isset($_row->parameters['font_family_b'])&&($_row->parameters['font_family_b']!="")){
											$output_b = "font-family: ".$_row->parameters['font_family_b']."; ";
										}
										if(isset($_row->parameters['font_size_b'])&&($_row->parameters['font_size_b']!="")){
											$output_b.= "font-size: ".$_row->parameters['font_size_b']."px; ";
										}
										if(isset($_row->parameters['font_weight_b'])&&($_row->parameters['font_weight_b']!="")){
											$output_b.= "font-weight: ".$_row->parameters['font_weight_b']."; ";
										}
										if(isset($_row->parameters['body_color'])&&($_row->parameters['body_color']!="")){
											$output_b.= "color: #".$_row->parameters['body_color']."; ";
										}
										echo $output_b;
										echo "'>";
										echo $_row->parameters['alt_text'];
										echo "</span>";
									}
								?>
							</div>
							<div id="taction">
								<?php
									if(isset($_row->parameters['alt_text_a'])&&($_row->parameters['alt_text_a']!="")){
										if(isset($_row->target_url)&&($_row->target_url!="")&&($_row->target_url!="http://")){
											$outputs="<a id='tlink2' href='".$_row->target_url."' ";
											if(isset($_row->parameters['target_window'])&&($_row->parameters['target_window']!="")){
												$outputs.="target='".$_row->parameters['target_window']."' ";
											}
											$outputs.=">";
											echo $outputs;
										}
										echo "<span id='ttaction' style='";
										if(isset($_row->parameters['font_family_a'])&&($_row->parameters['font_family_a']!="")){
											$output_a = "font-family: ".$_row->parameters['font_family_a']."; ";
										}
										if(isset($_row->parameters['font_size_a'])&&($_row->parameters['font_size_a']!="")){
											$output_a.= "font-size: ".$_row->parameters['font_size_a']."px; ";
										}
										if(isset($_row->parameters['font_weight_a'])&&($_row->parameters['font_weight_a']!="")){
											$output_a.= "font-weight: ".$_row->parameters['font_weight_a']."; ";
										}
										if(isset($_row->parameters['action_color'])&&($_row->parameters['action_color']!="")){
											$output_a.= "color: #".$_row->parameters['action_color']."; ";
										}
										echo $output_a;
										echo "'>";
										echo $_row->parameters['alt_text_a'];
										echo "</span></a>";
									}
								?>
							</div>
						</div>
						<?php } ?>
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
						<input type="text" size="7" class="color input-mini" value="<?php echo $_row->parameters['border_color'];?>" name="parameters[border_color]">
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
						<input type="text" size="7" class="color input-mini" value="<?php echo $_row->parameters['bg_color'];?>" name="parameters[bg_color]">
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADBCOLOR_TIP'); ?>" >
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
				
				<div class="well well-minimized"><?php echo JText::_('ADAG_IMGPROPS'); ?></div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWADUPLOADIMG');?>
					</label>
					<div class="controls">
						<input type="file" name="image_file" size="20" onchange="document.getElementById('upload-submit').click();">
						<button style="display:none;" type="submit" id="upload-submit" onclick="return UploadImage();" class="btn btn-primary"><i class="icon-upload icon-white"></i> Start Upload</button>
						
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
								<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADUPLOADIMG_TIP'); ?>" >
								<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
						<?php } ?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWADPREVIEW');?>
					</label>
					<div class="controls">
						<?php if (!$_row->id) { ?>
							<div id="imgdiv" style="display: block;">
							<?php	if (!$_row->image_url){
										echo '';
									}
									elseif(isset($_row->image_url)&&($_row->image_url!='')){
										$image_size = getimagesize($lists["image_directory"].$_row->image_url);
										if(isset($image_size["1"]) && $image_size["1"] > 200){
											$style = 'style="height:200px;"';
										}
										echo '<img src="'.$lists["image_directory"].$_row->image_url.'" '.$style.' name="imagelib" />';
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
							<div id="imgdiv" style="display: block;">
							<?php
								if(isset($_row->image_url)&&($_row->image_url!='')){
									$image_size = getimagesize($lists["image_directory"].$_row->image_url);
									if(isset($image_size["1"]) && $image_size["1"] > 200){
										$style = 'style="height:200px;"';
									}
							?>
									<img src="<?php echo $lists['image_directory'].$_row->image_url; ?>" <?php echo $style; ?> name="imagelib" />
							<?php
								}
							?>
							</div>
							<?php } ?>
		
					  <?php if (!isset($_row->image_url)) {	?>
							<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADPREVIEW_TIP'); ?>" >
							<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
					  <?php  }
							if(isset($_row->image_url)&&($_row->image_url!='')) {
								echo '<p /><a href="#" id="remimg">'.JText::_("ADAG_REMIMG").'</a>';
							}
					  ?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('NEWADALT');?>
					</label>
					<div class="controls">
						<input class="formField" type="text" name="parameters[img_alt]" size="29" value="<?php if (isset($_row->parameters['img_alt'])&&($_row->parameters['img_alt']!="")) {echo $_row->parameters['img_alt'];} else {echo "";}?>">
						&nbsp; 
						<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADALT_TIP'); ?>" >
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
				?>

				<?php
                	if(isset($camps)&&($camps!=NULL)) { //this->assoc_camps
						$i=0;
				?>
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
															
															if(trim($zone_width) != "" && trim($zone_height) != "" && isset($params["textad"])){
																if(trim($zone_width) == trim($ad_width) && trim($zone_height) == trim($ad_height)){
																	$ok = TRUE;
																	break;
																}
															}
															elseif(trim($zone_width) == "" && trim($zone_height) == "" && isset($params["textad"])){
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
				$helperJomSocial->render($_row->id, "");
			?>
            
            
		</div>
	</div>

	<?php if (isset($_GET['act']) && ($_GET['act']=='new')) { ?>
    <input type="hidden" name="act" value="new" />
    <?php } ?>
    <input type="hidden" id="sendmail" name="sendmail" value="1" />
    <input type="hidden" id="initvalcamp" value="" />
    <input type="hidden" name="controller" value="adagencyTextlink" />
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="edit" />
    <input type="hidden" name="id" value="<?php echo $_row->id;?>" />
    <input type="hidden" name="media_type" value="TextLink" />
    <input type="hidden" id="numberoflims" value="1" />
    <input type="hidden" id="administrator" value="true" />
    <input type="hidden" name="time_format" id="time_format" value="<?php echo $format_value; ?>" />
    <?php echo $lists['hidden_zones']; ?>

</form>
