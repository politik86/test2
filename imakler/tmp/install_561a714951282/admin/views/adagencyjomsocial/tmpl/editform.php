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
return "";

JHtml::_('behavior.tooltip');
JHtml::_('behavior.calendar');
JHtml::_('behavior.modal');

$realimgs = "";
$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/css/uikit.almost-flat.css");
$document->addStyleSheet(JURI::root()."administrator/components/com_adagency/includes/css/ad-jomsocial.css");
$ad = $this->ad;
$ad = @$ad["0"];
$id = intval(@$ad["id"]);

$configs = $this->configs;
$current = $this->channel;
$camps = $this->camps;
$lists = $this->lists;
$advertiser = $this->advt;
$js_settings = $this->js_settings;

$headline_limit = "10";
$content_limit = "100";
$show_sponsored_stream_info = "1";
$show_create_ad_link = "1";

if(isset($js_settings["headline_limit"])){
	$headline_limit = intval($js_settings["headline_limit"]);
}
if(isset($js_settings["content_limit"])){
	$content_limit = intval($js_settings["content_limit"]);
}

if(isset($js_settings["show_sponsored_stream_info"])){
	$show_sponsored_stream_info = intval($js_settings["show_sponsored_stream_info"]);
}
if(isset($js_settings["show_create_ad_link"])){
	$show_create_ad_link = intval($js_settings["show_create_ad_link"]);
}

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
}
else{
	$advertiser_appr = NULL;
}

?>
<script type="text/javascript" language="javascript">
	ADAG = jQuery.noConflict();
</script>
<?php

include_once("components/com_adagency/helpers/geo_fcs.php");
include_once(JPATH_SITE."/components/com_adagency/includes/js/standard_geo.php");

$document->addScript("components/com_adagency/includes/js/ajaxupload.js");
?>

<script type="text/javascript" language="javascript">
	function changePromoteURL2(){
		target_url = document.getElementById("target_url").value;
		
		//------------------------------------------------------
		var message;
		var myRegExp =/^(?:(?:https?|ftp):\/\/)(?:\S+(?::\S*)?@)?(?:(?!10(?:\.\d{1,3}){3})(?!127(?:\.\d{1,3}){3})(?!169\.254(?:\.\d{1,3}){2})(?!192\.168(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)(?:\.(?:[a-z\u00a1-\uffff0-9]+-?)*[a-z\u00a1-\uffff0-9]+)*(?:\.(?:[a-z\u00a1-\uffff]{2,})))(?::\d{2,5})?(?:\/[^\s]*)?$/i;
		var urlToValidate = target_url;
		if(!myRegExp.test(urlToValidate)){
			alert("<?php echo JText::_("ADAG_NOT_A_VALID_URL"); ?>");
			return false;
		}
		//------------------------------------------------------
		
		if(!target_url.contains("http")){
			target_url = 'http://'+target_url;
		}
		
		var hostname = new URL(target_url).hostname;
		
		start = "http://";
		if(target_url.contains("https")){
			start = "https://";
		}
		document.getElementById("preview_promote_url").innerHTML = start + hostname;
	}
	
	function changePromoteURL(){
		timeoutID = window.setTimeout(changePromoteURL2, 500);
	}
	
	function cutText(element_id, max_value){
		if(element_id == "ad_headline"){
			length = document.getElementById("ad_headline").value.length;
			if(parseInt(max_value) - length >= 0){
				document.getElementById("head_left").innerHTML = max_value - length;
			}
			else{
				document.getElementById("ad_headline").value = document.getElementById("ad_headline").value.substring(0, max_value);
				document.getElementById("head_left").innerHTML = "0";
			}
			document.getElementById("preview_ad_headline").innerHTML = document.getElementById("ad_headline").value;
		}
		else if(element_id == "ad_text"){
			length = document.getElementById("ad_text").value.length;
			if(parseInt(max_value) - length >= 0){
				document.getElementById("content_left").innerHTML = max_value - length;
			}
			else{
				document.getElementById("ad_text").value = document.getElementById("ad_text").value.substring(0, max_value);
				document.getElementById("content_left").innerHTML = "0";
			}
			document.getElementById("preview_ad_text").innerHTML = document.getElementById("ad_text").value;
		}
	}
	
	function changeTarget(){
		values = document.getElementsByName("hidden_ids");
		url = "";
		audience = 0;
		total = 0;
		
		for(i=0; i<values.length; i++){
			element_id = values[i].value;
			element = document.getElementById("jomsocial_"+element_id);
			
			value = "";
			id = element_id;
			
			if(element.tagName.toLowerCase() == "input"){
				if(element.checked == true){
					value = element.value;
					url += "&target_"+id+"="+value;
				}
			}
			else if(element.tagName.toLowerCase() == "select"){
				value = element.value;
				url += "&target_"+id+"="+value;
			}
		}
		
		var url = "index.php?option=com_adagency&controller=adagencyJomsocial&task=target&format=raw&tmpl=component"+url;
		var req = new Request.HTML({
			method: 'get',
			url: url,
			async:false,
			data: { 'do' : '1' },
			onComplete: function(response){
				document.getElementById("ajax-result").empty().adopt(response);
				return_result = document.getElementById("ajax-result").innerHTML;
				temp = return_result.split("-");
				
				audience = temp[0];
				total = temp[1];
			}
		}).send();
		
		var myData = new Array(['<?php echo JText::_("ADAG_AUDIENCE"); ?>', parseInt(audience)], ['', parseInt(total)]);
		var colors = ['#FB9900', '#FACC00'];
		var myChart = new JSChart('basicpiechart', 'pie', null);
		myChart.setDataArray(myData);
		myChart.colorizePie(colors);
		myChart.setTitleColor('#857D7D');
		myChart.setPieUnitsColor('#9B9B9B');
		myChart.setPieValuesColor('#6A0000');
		myChart.setTitle('');
		myChart.setSize(250, 250);
		myChart.draw();
	}
	
	function timeToStamp(string_date){
		var form = document.adminForm;
		var time_format = form["time_format"].value;
		myDate = string_date.split(" ");
		myDate = myDate[0].split("-");
		
		if(myDate instanceof Array){
		}
		else{
			myDate = myDate[0].split("/");
		}
		var newDate = '';
		
		switch (time_format){
			case "0" :
				newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
				break;
			case "1" :
				newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
				break;
			case "2" :
				newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
				break;
			case "3" :
				newDate = myDate[1]+"/"+myDate[2]+"/"+myDate[0];
				break;
			case "4" :
				newDate = myDate[0]+"/"+myDate[1]+"/"+myDate[2];
				break;
			case "5" :
				newDate = myDate[1]+"/"+myDate[0]+"/"+myDate[2];
				break;
		}
		
		return newDate;
	}
	
	window.addEvent("domready", function(){
		jQuery("#target_url").focusout(function(){
			changePromoteURL();
		});
	});
	
	window.addEvent("domready", function(){
		new AjaxUpload("ajaxuploadavatar", {
			action: "index.php?option=com_adagency&controller=adagencyJomsocial&task=upload&tmpl=component&format=row&no_html=1&advertiser_id="+document.getElementById("advertiser_id").value,
			name: "image_file",
			multiple: false,
			onSubmit: function(id, fileName) {
				if(getSelectedValue2('adminForm','advertiser_id') < 1){
					alert("<?php echo JText::_('JS_SELECT_ADV');?>");
					return false;
				}
				jQuery('#onAjaxavatar').css('display', 'inline');
			},
			onComplete: function(file, response){
				if(eval(document.getElementById("image-image-url"))){
					document.getElementById("image-image-url").src = "<?php echo JURI::root(); ?>"+response;
				}
				
				if(eval(document.getElementById("preview-image-image-url"))){
					document.getElementById("preview-image-image-url").src = "<?php echo JURI::root(); ?>"+response;
				}
				
				if(eval(document.getElementById("image_url"))){
					var filename = response.split('/').pop();
					document.getElementById("image_url").value = filename;
				}
				
				if(eval(document.getElementById("onAjaxavatar"))){
					jQuery('#onAjaxavatar').hide();
				}
				
				if(eval(document.getElementById("delete-image-url"))){
					document.getElementById("delete-image-url").style.display = "inline-block";
				}
			}
		});
	});
	
	window.addEvent("domready", function(){
		new AjaxUpload("ajaxuploadcontent", {
			action: "index.php?option=com_adagency&controller=adagencyJomsocial&task=uploadImageContent&tmpl=component&format=row&no_html=1&advertiser_id="+document.getElementById("advertiser_id").value,
			name: "image_content_file",
			multiple: false,
			onSubmit: function(id, fileName){
				if(getSelectedValue2('adminForm','advertiser_id') < 1){
					alert("<?php echo JText::_('JS_SELECT_ADV');?>");
					return false;
				}
				jQuery('#onAjaxcontent').css('display', 'inline');
			},
			onComplete: function(file, response){
				if(eval(document.getElementById("image-image-content"))){
					document.getElementById("image-image-content").src = "<?php echo JURI::root(); ?>"+response;
				}
				
				if(eval(document.getElementById("preview-image-image-content"))){
					document.getElementById("preview-image-image-content").src = "<?php echo JURI::root(); ?>"+response;
				}
				
				if(eval(document.getElementById("image_content"))){
					var filename = response.split('/').pop();
					document.getElementById("image_content").value = filename;
				}
				
				if(eval(document.getElementById("onAjaxavatar"))){
					jQuery('#onAjaxcontent').hide();
				}
				
				if(eval(document.getElementById("delete-image-content"))){
					document.getElementById("delete-image-content").style.display = "inline-block";
				}
			}
		});
	});
	
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if((pressbutton=='save')||(pressbutton=='apply')){
			if(form['title'].value == ""){
				alert( "<?php echo JText::_("JS_INSERT_TITLE");?>" );
				return false;
			}
			else if(getSelectedValue2('adminForm','advertiser_id') < 1){
				alert( "<?php echo JText::_("JS_SELECT_ADV");?>" );
				return false;
			}
			else if(form['target_url'].value == ""){
				alert( "<?php echo JText::_("JS_INSERT_PROMOTE_URL");?>" );
				return false;
			}
			else{
				if(form['ad_end_date'].value != "Never" && form['ad_end_date'].value != ""){
					start_date = form['ad_start_date'].value;
					end_date = form['ad_end_date'].value;
							
					start_date = new Date(timeToStamp(start_date)).getTime();
					end_date = new Date(timeToStamp(end_date)).getTime();
							
					if(Date.parse(start_date) > Date.parse(end_date)){
						alert("<?php echo JText::_("ADAG_FINISH_DATE_AND_START_DATE"); ?>");
						return false;
					}
				}
						
				<?php
					if(isset($_GET['cid'][0])&&(intval($_GET['cid'][0])>0)) {
				?>
					if((document.getElementById("approved").value != 'P')&&(document.getElementById("initvalcamp").value != document.getElementById("approved").value)){
						if(document.getElementById("approved").value == 'Y'){
							var question = "<?php echo JText::_('ADAG_QUESTBANY');?>";
						}
						else if(document.getElementById("approved").value == 'N'){
							var question = "<?php echo JText::_('ADAG_QUESTBANN');?>";
						}

						var answer = confirm(question);
						if(answer){
						}
						else{
							document.getElementById("sendmail").value = 0;
						}
					}
				<?php
					}
				?>
				<?php
					if(!isset($configs->geoparams['allowgeo'])&&!isset($configs->geoparams['allowgeoexisting'])){
				?>
						submitform(pressbutton);
				<?php
					}
					else{
				?>
						if((ADAG('#geo_type1').prop('checked') == true)&&(!checkChannel(true))){
							return false;
						}
						sanitizeAndSubmit(pressbutton);
				<?php
					}
				?>
			}
		}
		else{
			submitform(pressbutton);
		}
		return true;
	}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="uk-form uk-form-horizontal form-horizontal"> 
	<div class="row-fluid">
		<div class="span12 pull-right">
			  <h2 class="pub-page-title">
					<?php
						if(!isset($ad["id"])){
							echo JText::_('ADAG_NEWAD');
						}
						else{
							echo JText::_('ADAG_EDITAD');
						}
					?>
			  </h2>
		</div>
	</div>
	
	<ul class="nav nav-tabs">
		<li class="active"><a href="#general" data-toggle="tab"><?php echo JText::_('VIEWCONFIGCATGENERAL'); ?></a></li>
		<li><a href="#properties" data-toggle="tab"><?php echo JText::_('ADAG_PROPERTIES'); ?></a></li>
		<li><a href="#location" data-toggle="tab"><?php echo JText::_('ADAG_LOCATION'); ?></a></li>
		<li><a href="#campaigns" data-toggle="tab"><?php echo JText::_('NEWADCMPS'); ?></a></li>
		<li><a href="#publishing" data-toggle="tab"><?php echo JText::_('VIEWORDERSPUBLISH'); ?></a></li>
	</ul>
	
	<div class="tab-content">
		<div class="tab-pane active" id="general">
			<div class="well well-minimized"><?php echo  JText::_('NEWADDETAILS'); ?></div>
			
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('NEWJSADTITLE'); ?>
					<span class="star">*</span>
				</label>
				<div class="controls">
					<input class="formField" type="text" name="title" size="30" value="<?php if (isset($ad["title"]) && $ad["title"] != "") {echo $ad["title"];} else {echo "";} ?>">
					&nbsp; 
					<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADTITLE_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('NEWADADVERTISER'); ?>
					<span class="star">*</span>
				</label>
				<div class="controls">
					<div  id="to_be_replaced" style="float:left "><?php echo $lists['advertiser_id']; ?></div>
					&nbsp;
					<div style="float:left; padding-left:10px; "><a rel="{handler: 'iframe', size: {x: 700, y: 450}}" href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=addadv&tmpl=component" class="modal">Add advertiser</a></div>
					<?php echo $advertiser_appr; ?>
					&nbsp; 
					<span class="editlinktip hasTip" title="<?php echo JText::_('NEWADADVERTISER_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				</div>
			</div>
			
			<div class="control-group">
				<label class="control-label">
					<?php echo JText::_('AD_STATUS'); ?>
				</label>
				<div class="controls">
					<?php echo $lists['approved']; ?>
					&nbsp; 
					<span class="editlinktip hasTip" title="<?php echo JText::_('AD_STATUS_TIP'); ?>" >
					<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
				</div>
			</div>
		</div>
		
		<div class="tab-pane ada-joms" id="properties">
			<div class="ada-joms-promote">
				<h3 class="ada-joms-promote-title"><?php echo JText::_("ADAG_URL_TO_PROMOTE"); ?> <span class="uk-text-danger">*</span></h3>
				<?php
					$target_url = "http://";
					if(isset($ad["target_url"]) && trim($ad["target_url"]) != ""){
						$target_url = $ad["target_url"];
					}
				?>
				<input type="text" name="target_url" id="target_url" value="<?php echo $target_url; ?>" class="input-xxlarge" onpaste="javascript:changePromoteURL();" />
			</div>
			
			<div class="ada-joms-box">
				<h4 class="ada-joms-box-title"><?php echo JText::_("ADAG_AD_IMAGES"); ?></h4>
				<ul class="uk-grid uk-grid-width-medium-1-2 uk-grid-small">
					<li>
						<div class="ada-joms-imagebox">
							<em>
								<?php echo JText::_("ADAG_AD_AVATAR_IMAGE"); ?>
							</em>
							<div class="ada-joms-image" id="div-image-url">
								<?php
									$img_path = "";
									if(!isset($ad["image_url"]) || trim(@$ad["image_url"]) == ""){
										$img_path = "components/com_adagency/images/ad_avatar.png";
									}
									else{
										$img_path = $lists['image_path'].$ad["image_url"];
									}
								?>
								
								<img src="<?php echo $img_path; ?>" id="image-image-url" alt="ad avatar image" title="ad avatar image">
								<?php
									$display = "none";
									if(isset($ad["image_url"]) && trim(@$ad["image_url"]) != ""){
										$display = "block";
									}
								?>
								
								<input type="hidden" name="image_url" id="image_url" value="<?php if(isset($ad["image_url"])){ echo $ad["image_url"]; }?>" />
							</div>
							<div class="ada-joms-box-opt">
								<div>
									<a href="#" id="delete-image-url" class="uk-button uk-button-danger" style="display:<?php echo $display; ?>;" onclick="javascript:deleteImage('div-image-url', 'image-image-url'); return false;">
										<?php echo JText::_("ADAG_DELETE"); ?>
									</a>
									<input class="uk-button uk-button-primary" type="button" name="ajaxuploadavatar" value="<?php echo JText::_("ADAG_UPLOAD"); ?>" id="ajaxuploadavatar"/>
									<div id="onAjaxavatar" style="display: none;font-weight: bold;margin-left: 20px;width: 300px;">
										<img src="<?php echo JURI::root(); ?>administrator/components/com_adagency/images/ajax-loader.gif" />
										<?php echo JText::_("ADAG_UPLOADING"); ?>
									</div>
									
									<script language="javascript" type="text/javascript">
										function deleteImage(div_id, image_id){
											if(image_id == "image-image-url"){
												document.getElementById("image-image-url").src = "components/com_adagency/images/ad_avatar.png";
												document.getElementById("preview-image-image-url").src = "components/com_adagency/images/ad_avatar.png";
												document.getElementById("delete-image-url").style.display = "none";
												document.adminForm.image_url.value = "";
											}
											else if(image_id == "image-image-content"){
												document.getElementById("image-image-content").src = "components/com_adagency/images/ad_content_image.png";
												document.getElementById("preview-image-image-content").src = "components/com_adagency/images/ad_content_image.png";
												document.getElementById("delete-image-content").style.display = "none";
												document.adminForm.image_content.value = "";
											}
										}
										
										function getSelectedValue2(frmName, srcListName){
											var form = eval('document.' + frmName);
											var srcList = form[srcListName];
											
											i = srcList.selectedIndex;
											if(i != null && i > -1){
												return srcList.options[i].value;
											}
											else{
												return null;
											}
										}
						
										function UploadImage(){
											if(getSelectedValue2('adminForm','advertiser_id') < 1){
												alert("<?php echo JText::_('JS_SELECT_ADV');?>");
											}
											else{
												var fileControl = document.adminForm.image_file;
												var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
												if(thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG"){
													alert('<?php echo JText::_('JS_INVALIDIMG');?>');
													return false;
												}
												
												if(fileControl.value){
													document.adminForm.task.value = 'upload';
													return true;
												}
												return false;
											}
											return false;
										}
									</script>
								</div>
							</div>
							<div class="ada-joms-box-info uk-text-muted uk-visible-large"">
								<?php echo JText::_("ADAG_SUGGESTED_SIZE_IMAGE_AVATAR"); ?>
							</div>
						</div>
					</li>
					
					<li>
						<div class="ada-joms-imagebox">
							<em>
								<?php echo JText::_("ADAG_AD_CONTENT_IMAGE"); ?>
							</em>
							<div class="ada-joms-image" id="div-image-content">
								<?php
									$img_path = "";
									if(!isset($ad["image_content"]) || trim(@$ad["image_content"]) == ""){
										$img_path = "components/com_adagency/images/ad_content_image.png";
									}
									else{
										$img_path = $lists['image_path'].$ad["image_content"];
									}
								?>
								
								<img src="<?php echo $img_path; ?>" id="image-image-content" alt="ad content image" title="ad content image" style="width:480px; height:270px;">
								
								 <?php
									$display = "none";
									if(isset($ad["image_content"]) && trim(@$ad["image_content"]) != ""){
										$display = "block";
									}
								?>
								
								<input type="hidden" id="image_content" name="image_content" value="<?php if(isset($ad["image_content"])){ echo $ad["image_content"]; }?>" />
							</div>
							<div class="ada-joms-box-opt">
								<div>
									<a href="#" id="delete-image-content" class="uk-button uk-button-danger" style="display:<?php echo $display; ?>;" onclick="javascript:deleteImage('div-image-content', 'image-image-content'); return false;">
										<?php echo JText::_("ADAG_DELETE"); ?>
									</a>
									<input class="uk-button uk-button-primary" type="button" name="ajaxuploadcontent" value="<?php echo JText::_("ADAG_UPLOAD"); ?>" id="ajaxuploadcontent"/>
									<div id="onAjaxcontent" style="display: none;font-weight: bold;margin-left: 20px;width: 300px;">
										<img src="<?php echo JURI::root(); ?>administrator/components/com_adagency/images/ajax-loader.gif" />
										<?php echo JText::_("ADAG_UPLOADING"); ?>
									</div>
									
									<script language="javascript" type="text/javascript">
										function UploadImageContent(){
											if(getSelectedValue2('adminForm','advertiser_id') < 1){
												alert("<?php echo JText::_('JS_SELECT_ADV');?>");
												return false;
											}
											else{
												var fileControl = document.adminForm.image_content_file;
												var thisext = fileControl.value.substr(fileControl.value.lastIndexOf('.'));
												if(thisext != ".jpeg" && thisext != ".jpg" && thisext != ".gif" && thisext != ".png" && thisext != ".JPEG" && thisext != ".JPG" && thisext != ".GIF" && thisext != ".PNG"){
													alert('<?php echo JText::_('JS_INVALIDIMG');?>');
													return false;
												}
												
												if(fileControl.value){
													document.adminForm.task.value = 'uploadImageContent';
													return true;
												}
												return false;
											}
											return false;
										}
									</script>
								</div>
							</div>
							<div class="ada-joms-box-info uk-text-muted uk-visible-large">
								<?php echo JText::_("ADAG_SUGGESTED_SIZE_IMAGE_CONTENT"); ?>
							</div>
						</div>
					</li>
				</ul>
			</div>
			
			<div class="ada-joms-box">
				<h4 class="ada-joms-box-title"><?php echo JText::_("ADAG_AD_HEADLINE_TEXT"); ?></h4>
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('ADAG_AD_HEADLINE');?>
					</label>
					<div class="controls" style="margin-left:180px !important;">
						<input type="text" name="ad_headline" id="ad_headline" value="<?php echo @$ad["ad_headline"]; ?>" class="input-large" onkeyup="javascript:cutText('ad_headline', '<?php echo intval($headline_limit); ?>');" />
						<?php
							$current_length = strlen(@$ad["ad_headline"]);
							echo '<span id="head_left" class="uk-text-primary">'.(intval($headline_limit) - intval($current_length)).'&nbsp;'.JText::_("ADAG_CHARACTERS_LEFT").'</span>';
						?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">
						<?php echo JText::_('ADAG_AD_TEXT');?>
					</label>
					<div class="controls" style="margin-left:180px !important;">
						<textarea name="ad_text" id="ad_text" style="width:90%; height:150px;" onkeyup="javascript:cutText('ad_text', '<?php echo intval($content_limit); ?>');"><?php echo @$ad["ad_text"]; ?></textarea>
						<?php
							$current_length = strlen(@$ad["ad_text"]);
							echo '<p class="uk-form-help-block uk-text-primary" id="content_left">'.(intval($content_limit) - intval($current_length)).'&nbsp;'.JText::_("ADAG_CHARACTERS_LEFT").'</p>';
						?>
					</div>
				</div>
			</div>
			
			<div class="ada-joms-preview">
				<h4 class="ada-joms-preview-title"><?php echo JText::_("ADAG_AD_PREVIEW"); ?></h4>
				<table class="uk-margin-remove">
					<tr>
						<td class="ada-joms-preview-avatar">
							<?php
								$img_path = "";
								if(!isset($ad["image_url"]) || trim(@$ad["image_url"]) == ""){
									$img_path = "components/com_adagency/images/ad_avatar.png";
								}
								else{
									$img_path = $lists['image_path'].$ad["image_url"];
								}
							?>
							<img src="<?php echo $img_path; ?>" id="preview-image-image-url" alt="ad avatar image" title="ad avatar image">
						</td>
						<td class="ada-joms-preview-content">
							<div id="preview_ad_headline" class="uk-h3">
								<?php echo @$ad["ad_headline"]; ?>
							</div>
							<div id="preview_promote_url" class="uk-text-primary ada-joms-preview-link">
								<?php
									$temp = parse_url(@$ad["target_url"]);
									if(isset($temp["host"])){
										echo $temp["scheme"]."://".$temp["host"];
									}
								?>
							</div>
							<div id="preview_ad_text">
								<?php echo @$ad["ad_text"]; ?>
							</div>
							<div id="preview_content_image">
								<?php
									$img_path = "";
									if(!isset($ad["image_content"]) || trim(@$ad["image_content"]) == ""){
										$img_path = "components/com_adagency/images/ad_content_image.png";
									}
									else{
										$img_path = $lists['image_path'].$ad["image_content"];
									}
								?>
								<img src="<?php echo $img_path; ?>" id="preview-image-image-content" alt="ad content image" title="ad content image" style="width:480px; height:270px;">
								<br/>
								<div style="float:left; width:50%;">
									<?php
										if($show_sponsored_stream_info == "1"){
											echo JText::_("ADAG_SPONSORED_STREAM");
										}
									?>
								</div>
								<div style="float:right; width:50%; text-align:right;">
									<?php
										if($show_create_ad_link == "1"){
											echo JText::_("ADAG_CREATE_AN_AD");
										}
									?>
								</div>
							</div>
						</td>
					</tr>
				</table>
			</div>
			
			<div class="ada-joms-box ada-joms-target">
				<div class="uk-grid">
					<?php
						if(isset($js_settings["target_audience_preview"]) && $js_settings["target_audience_preview"] == 0){ // No
							$width = "uk-width-medium-1-1"; // if targetting is OFF
						} else {
							$width = "uk-width-medium-2-3"; // ON
						}
					?>
					<div class="<?php echo $width; ?>">
						<h4 class="ada-joms-box-title"><?php echo JText::_("ADAG_TARGET_AUDIENCE"); ?></h4>
						<?php
							require_once(JPATH_BASE."/components/com_adagency/helpers/jomsocial_js_ad.php");
							$helper = new JomSocialTargetingJSAd();
							$helper->renderJsAd($id, "");
							
							$audience = 0;
							$total = 0;
						?>
					</div>
					<?php
						if(isset($js_settings["target_audience_preview"]) && $js_settings["target_audience_preview"] == 1){
					?>
					<div class="uk-width-medium-1-3">
						<h4 class="ada-joms-box-title"><?php echo JText::_("ADAG_TARGET_AUDIENCE_PREVIEW"); ?></h4>
						<script language="javascript" type="text/javascript" src="<?php echo JURI::root()."administrator/components/com_adagency/js/jscharts.js"; ?>"></script>
						<div id="basicpiechart" style="width:100%;">Loading...</div>
						<script type="text/javascript">
							var myData = new Array(['<?php echo JText::_("ADAG_AUDIENCE"); ?>', <?php echo intval($audience); ?>], ['', <?php echo intval($total); ?>]);
							var colors = ['#FB9900', '#FACC00'];
							var myChart = new JSChart('basicpiechart', 'pie', null);
							myChart.setDataArray(myData);
							myChart.colorizePie(colors);
							myChart.setTitleColor('#857D7D');
							myChart.setPieUnitsColor('#9B9B9B');
							myChart.setPieValuesColor('#6A0000');
							myChart.setTitle('');
							myChart.setSize(250, 250);
							myChart.draw();
						</script>
					</div>
					<?php
						}
					?>
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
							<th>
							</th>
							<th class="span11">
								<?php echo JText::_("CONFIGCMP"); ?>
							</th>
						</thead>
						<tbody>
						<?php
							if(isset($this->banners_camps)){
								$banners_camps = $this->banners_camps;
							}
							else{
								$banners_camps = array();
							}

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
						if(isset($ad["ad_start_date"])){
							$ad_start_date = $ad["ad_start_date"];
						}

						$ad_end_date = "0000-00-00 00:00:00";
						if(isset($ad["ad_end_date"])){
							$ad_end_date = $ad["ad_end_date"];
						}
						if($ad_end_date == "0000-00-00 00:00:00"){
							$ad_end_date = "Never";
						}
					
						$format_value = @$params_component["timeformat"];
						
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
					<?php echo JText::_('ADAG_START_PUBLISHING');?>
				</label>
				<div class="controls">
					<?php
						$calendar = JHtml::calendar(trim($ad_end_date), 'ad_end_date', 'ad_end_date', $format_string_2, ''); 
						if($ad_end_date == "Never"){
							$calendar = str_replace('value=""', 'value="'.trim($ad_end_date).'"', $calendar);
						}
						echo $calendar;
					?>
				</div>
			</div>
		</div>
	</div>
	
	<input type="hidden" id="initvalcamp" value="" />
	<input type="hidden" id="sendmail" name="sendmail" value="1" />
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="task" value="edit" />
	<input type="hidden" name="media_type" value="Jomsocial" />
	<input type="hidden" name="id" value="<?php echo intval($id); ?>" />
	<input type="hidden" name="cid[]" value="<?php echo intval($id); ?>" />
	<input type="hidden" name="controller" value="adagencyJomsocial" />
	<input type="hidden" name="time_format" id="time_format" value="<?php echo $format_value; ?>" />
	
	<?php
		$camp_id = JRequest::getVar("camp_id", "0");
		if(intval($camp_id) != 0){
			echo '<input type="hidden" id="camp_id" name="camp_id" value="'.intval($camp_id).'" />';
		}
	?>
	
	<div id="ajax-result" style="display:none;"></div>
	
	<?php
		if(intval($id) > 0){
	?>
			<script type="text/javascript" language="javascript">
				changeTarget();
			</script>
	<?php
		}
	?>
	
</form>