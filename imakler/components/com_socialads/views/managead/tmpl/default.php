<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
jimport('joomla.filesystem.file');
jimport( 'joomla.filesystem.folder');
$document =JFactory::getDocument();
$input=JFactory::getApplication()->input;
          //$post=$input->post;
          //$input->get
//$document->addStyleSheet(JUri::root().'components/com_socialads/css/socialads.css'); 	
$document->addScript(JUri::root().'components/com_socialads/js/managead.js'); 
$document->addScript(JUri::base().'components/com_socialads/js/flowplayer-3.2.9.min.js'); 	//added by manoj stable 2.7.5	

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

	require_once(JPATH_COMPONENT . DS . 'helper.php');
	$socialadshelper = new socialadshelper();
	$init_balance = $socialadshelper->getbalance();
	if($init_balance!=NULL && $init_balance !=1.00)    // HARDCODED FOR NOW.......
	{
		$itemid	= $socialadshelper->getSocialadsItemid('payment');
		$not_msg	= JText::_('MIM_BALANCE');
		$not_msg	= str_replace('{clk_pay_link}','<a href="'.JRoute::_('index.php?option=com_socialads&view=payment&Itemid='.$itemid).'">'.JText::_('SA_CLKHERE').'</a>', $not_msg);
		JError::raiseNotice( 100, $not_msg );
	}

if(file_exists(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."language".DS."default_language".DS."default_language.php")) {
	require(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."language".DS."default_language".DS."default_language.php");

	global $_CB_framework, $_CB_database, $ueConfig, $mainframe;
	if ( defined( 'JPATH_ADMINISTRATOR' ) ) {
		if ( ! file_exists( JPATH_ADMINISTRATOR .'/components/com_comprofiler/plugin.foundation.php' ) ) {
		echo 'CB not installed';
		return;
		}
		include_once( JPATH_ADMINISTRATOR .'/components/com_comprofiler/plugin.foundation.php' );
	}
	else
	{
		if ( ! file_exists( $mainframe->getCfg( 'absolute_path' ).'/administrator/components/com_comprofiler/plugin.foundation.php' ) ) {
		echo 'CB not installed';
		return;
		}
		include_once( $mainframe->getCfg( 'absolute_path' ).'/administrator/components/com_comprofiler/plugin.foundation.php' );
	}
}
$EST_HEAD=JText::_('ESTIMATED_REACH_HEAD');
$EST_END=JText::_('ESTIMATED_REACH_END');
$user = JFactory::getUser();
$datelow='';
?>
<div class="techjoomla-bootstrap">
<?php

$special_access=0;
if(isset($user->groups['8']) || isset($user->groups['7']) || isset($user->groups['Super Users']) || isset($user->groups['Administrator']) || $user->usertype == "Super Users" || isset($user->groups['Super Users']) || isset($user->groups['Administrator']) || $user->usertype == "Super Administrator" || $user->usertype == "Administrator" ){
	$special_access = 1;
}

if (!$user->id )
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('BUILD_LOGIN'); 
	$session = JFactory::getSession();   
	$session->set('socialadsbackurl', $_SERVER["REQUEST_URI"]);   
	$socialadsbackurl=$session->get('socialadsbackurl');
	?>
	</div>
	

			<a href='<?php 
				$msg=JText::_('LOGIN');
				$uri=$socialadsbackurl;
				$url=base64_encode($uri);
				echo 'index.php?option=com_users&view=login&return='.$url; ?>'>
				<div style="margin-left:auto;margin-right:auto;" class="control-group">
					<input id="LOGIN" class="btn btn-large btn-success validate" type="button" value="<?php echo JText::_('SIGN_UP'); ?>">
				</div>
			</a>

	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}



if(($special_access != 1) && !$this->adcheck) {
	?>

	<div class="alert alert-block">
	<?php echo JText::_('AD_NO_AUTH_SEE'); ?>
	</div>
</div>
	<?php
	return false;
}

$geodbfile = JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."GeoLiteCity.dat";
if($socialads_config['geo_target'] && JFile::exists($geodbfile)) {
?>

<!-- geo target start here -->
	<script src="<?php echo JUri::base().'media/techjoomla_strapper/js/akeebajqui.js'?>"></script>
	<script src="<?php echo JUri::root().'components/com_socialads/js/geo/geo.js'?>"></script>
	<link rel="stylesheet" href="<?php echo JUri::root().'components/com_socialads/css/geo/geo.css' ?>">
<!-- geo target end here -->

<?php
}
$layouts = explode ('|',$this->zone->layout); //split the layouts

		$singleselect = array();
		$singleselect[] = JHtml::_('select.option','img', JText::_('AD_TYP_IMG'));
		$singleselect[] = JHtml::_('select.option','text', JText::_('AD_TYP_TXT'));
		$singleselect[] = JHtml::_('select.option','text_img', JText::_('AD_TYP_TXT_IMG'));
	$singleselect[] = JHtml::_('select.option','affiliate', JText::_('AD_TYP_AFFI'));		
//print_r($this->guestad);
$display_reach='';
$display_reach_fun='';
if($socialads_config['display_reach'])	
{
$display_reach=' onchange=" calculatereach() "';
$display_reach_fun="'onchange'=>'calculatereach()'";
}
$js='
	function chkAdValid()
	{

		if(jQuery("#upload_area").find("div").children("[name=upimg]").val() != null)
			jQuery("#upimg").val(jQuery("#upload_area").find("div").children("[name=upimg]").val());
		//url validation starts here
		
	if(document.getElementById("adtype").value == "text_img" || document.getElementById("adtype").value == "text" || document.getElementById("adtype").value == "img")
	{		
		var theurl=document.myForm.url2.value;
		if(theurl == "")
		{
			alert("'.JText::_("URL_VALID").'");
			return false;
		}
		
		if(!theurl.match(/([A-Za-z0-9\.-]*)\.{0,1}([A-Za-z0-9-]{1,})(\.[A-Za-z]{2,})+/i))
		{
			alert("'.JText::_("URL_VALID").'");
			return false; 
		}//url validation ends here
	}
	if(document.getElementById("adtype").value == "text_img" || document.getElementById("adtype").value == "text" || document.getElementById("adtype").value == "affiliate")
	{
		//Title validation
		if(document.getElementById("eBann").value == "")
		{
			alert("'.JText::_("TITLE_VALID").'");
			return false;
		}
	}	
	if(document.getElementById("adtype").value == "text_img" || document.getElementById("adtype").value == "text" || document.getElementById("adtype").value == "affiliate")
	{	
		//Body text validation
		if(document.getElementById("eBann1").value == "")
		{
			alert("'.JText::_("BODY_VALID").'");
			return false;
		}		
	}
	if (document.getElementById("context_target_data[][keywordtargeting]"))
	{
		var contextual_keywords=document.getElementById("context_target_data[][keywordtargeting]").value.toString();
		if(document.getElementById("context_target").checked==true && (contextual_keywords==""))
		{
			alert("'.JText::_("CHKCONTEXTUAL").'");
			return false;
		}
	}
		return true;
	}

	function calculatereach()
	{
		var targetfields=jQuery(".ad-fields-inputbox").serializeArray();
		var estimated_reach=jQuery("#config_estimated_reach").val();
		jQuery.ajax({
		    type: "POST",
		    url: "?option=com_socialads&controller=buildad&task=calculatereach",
		    data:  targetfields,
		    dataType: "json",
		    success: function(data) {
							if(data==null)
							return;
							var totalreach=0
							var reach=parseInt(estimated_reach)+parseInt(data.reach);
         			if(parseInt(reach)==0)
							totalreach=0
							else
							totalreach=reach
					jQuery("#estimated_reach").html("'.$EST_HEAD.'"+totalreach+"'.$EST_END.'");
		    }
		});

	}

/*jQuery(document).ready(cmain);
		function cmain()
		{
			jQuery("#fixedElement").makeFloat({x:"current",y:"current"});
		}*/		
	';
	$document->addScriptDeclaration($js);	
	
?>	
<?php

if($this->addata->ad_affiliate == '1'){
$ad_typ = 'affiliate';
}else{
$this->zone->ad_type = str_replace('||',',',$this->zone->ad_type);
$this->zone->ad_type = str_replace('|','',$this->zone->ad_type);
$ad_type1 = explode(",",$this->zone->ad_type);
$ad_typ = $ad_type1[0];
}
?>	
<script>

jQuery(document).ready(function(){
							   

	changelayout(jQuery('#ad_layout_nm').val());
	var ad_typ = '<?php echo $ad_typ ?>';
	if( ad_typ != 'img'){
	toCount('eBann','max_tit1','{CHAR}',jQuery('#sBann').text(),jQuery('#max_tit').val(),jQuery('#eBann').val(),'');
	toCount1('eBann1','max_body1','{CHAR}',jQuery('#sBann1').text(),jQuery('#max_body').val(),jQuery('#eBann1').val(),'');
	}
	if( (document.getElementById('adtype').value == 'img' && <?php echo count($layouts) ?> == 1) || document.getElementById('adtype').value == 'affiliate')		/*for ad type= image and affiliate*/
	{
		jQuery("#layout_div").hide();
	}
	else
		jQuery("#layout_div").show();
			
		jQuery(".target").click(function(){
			show_hide_geo(this.id);
	  });
});

</script>
<script type="text/javascript" src="<?php echo JUri::root(); ?>components/com_socialads/js/ajaxupload.js"></script>
<style type="text/css">
	iframe
	 {
		display:none;
	 }
			
</style>
<?php
		//newly added for JS toolbar inclusion
		if(JFolder::exists(JPATH_SITE . DS .'components'. DS .'com_community') and $socialads_config['show_js_toolbar']==1)
		{					
			require_once( JPATH_ROOT . DS . 'components' . DS . 'com_community' . DS . 'libraries' . DS . 'toolbar.php');		
			$toolbar    = CFactory::getToolbar();	
			$tool = CToolbarLibrary::getInstance();
			
			?>
			<style>
			<!--
				div#proimport-wrap #community-wrap { margin: 0;padding: 0; }
				div#proimport-wrap #community-wrap { min-height: 45px !important; }
			-->
			</style>
			<div id="proimport-wrap">
				<div id="community-wrap">
					<?php	echo $tool->getHTML();	?>
				</div>
			</div>	<!-- end of proimport-wrap div -->
				<?php 
		}
		//eoc for JS toolbar inclusion
?>

<div class="ad-form">
<form method="post" name="myForm" id="adsform" enctype="multipart/form-data">
<fieldset class="sa_fieldset"> 
	<legend><?php echo JText::_('DESIGN');?></legend>

<!-- ad-details start here -->
<div class="ad-details" id="ad-details-id">

	<!--ad-dtl-space start here-->
	<div class="ad-dtl-space">

		<!-- ad-info start here -->
		<div class="ad-info sa_border_right">
	<!--zone manager -->

<div id="default_zone">
	<table>
		<tr>
			<td id="ad-form-span" class="sa_labels"><?php echo JText::_('ADTYPE');?> </td>
			<td ><?php echo JHtml::_('select.genericlist', $singleselect, "adtype", 'class="ad-type" size="1" onchange="Adchange()" disabled ', "value", "text", $ad_typ); ?> 
			<?php if($this->addata->ad_affiliate == '1'){?>
			<input type ="hidden" name="hadtype" id="hadtype" value="<?php echo $this->addata->ad_affiliate;  ?>"/>
			<?php } ?>
			</td>
		</tr>
		<tr>
			<td id="ad-form-span" class="sa_labels"><?php echo JText::_('ADZONE');?> </td>
			<td><select size="1" class="ad-zone" id="adzone" name="adzone" onchange="getZonesdata()" disabled>
				<option selected="selected" value="<?php echo $this->zone->id; ?>"><?php echo $this->zone->zone_name ?></option>
			</select>
			<input type ="hidden" name="ad_zone_id" id="ad_zone_id" value="<?php echo $this->zone->id;  ?>"/>
			</td>
		</tr>
	</table>
</div>
<!--<div class="componentheading" id="componentheading-id"><?php echo JText::_('DESIGN');?></div>-->

		<table>	
			<?php if($this->addata->ad_affiliate != '1'){?>
			<tr>
				<td><span id="ad-form-span"><?php echo JText::_('DEST_URL'); ?></span><span id="ad-form-spantxt"><?php echo JText::_('DEST_URL_OTHER'); ?></span>
          	    </td>
			</tr>
			<tr>
			<td id="ad-form-td">
					<?php echo JHtml::_('select.genericlist',  $this->url1, 'addata[][ad_url1]', 'class="inputbox" size="1"', 'value', 'text' ); ?>
					<input class="inputbox url" type="text" id="url2" name="addata[][ad_url2]" value="<?php echo $this->addata->ad_url2;?>" /></td>
			</tr>
			<?php } ?>
			
			
			
			<tr>
				<td>
					<span id="ad-form-span"><?php echo JText::_('TITLE'); ?></span>
				<?php if($this->addata->ad_affiliate != '1'){?>
					<span id ="max_tit1" ><?php echo $this->zone->max_title;?> </span>
					<span id="sBann" class="minitext"><?php echo JText::_('LEFT_CHAR'); ?></span>
					<input type ="hidden" name="max_tit" class="max_tit" id="max_tit" value="<?php echo $this->zone->max_title;?>"/>
				<?php } ?>
				</td>
			</tr>
			
					<tr>
				<td id="ad-form-td"><input class="inputbox" type="text" id="eBann" value="<?php echo $this->addata->ad_title;?>" name="addata[][ad_title]" maxlength="<?php echo $this->zone->max_title;?> " size="28"
onKeyUp="toCount('eBann','max_tit1','{CHAR}','<?php echo JText::_('LEFT_CHAR');?>',max_tit.value,this.value,event);" >
				
				<input type="hidden" name="hiddentitle"  value="">		
				</td>
			</tr>
		
			<?php if($ad_typ != 'img'){?>
			
			
			<tr>
				<td>
					<span id="ad-form-span"><?php echo JText::_('BODY_TEXT'); ?></span>
				<?php if($this->addata->ad_affiliate != '1'){?>					
					<span id ="max_body1" ><?php echo $this->zone->max_des; ?> </span>
					<span id="sBann1" class="minitext"><?php echo  JText::_('LEFT_CHAR');?></span>
					<input type ="hidden" name="max_body" class="max_body" id="max_body" value="<?php echo $this->zone->max_des; ?>"/>						
				<?php } ?>					
				</td>
			</tr>
			<tr>
				<td id="ad-form-td">
					<textarea id="eBann1" name="addata[][ad_body]"  rows="3" <?php echo ($this->addata->ad_affiliate != '1')? ' maxlength="'.$this->zone->max_des.'"':''; ?> <?php echo ($this->addata->ad_affiliate != '1')? 'size="28"' :''; ?>			  onKeyUp="toCount1('eBann1','max_body1','{CHAR}' ,'<?php echo JText::_('LEFT_CHAR');?>',max_body.value,this.value,event);"><?php echo stripslashes($this->addata->ad_body);?></textarea>
					<input type="hidden" name="hiddenbody"  value="<?php echo $this->addata->ad_body; ?> ">
				</td>
			</tr>
			<?php } ?>
			<?php if($this->addata->ad_affiliate != '1'){
			if($ad_typ != 'text'  ){ ?>
			<tr>
				<td><span id="ad-form-span"><?php echo JText::_('SA_IMG'); ?></span></td>
			</tr>
			<tr>	
			<!--ajax upload-->
				<td id="ad-form-td">
						<p><input type="file" name="ad_image" onchange="ajaxUpload(this.form,'&filename=ad_image','upload_area','File Uploading Please Wait...<br /><img src=\'<?php echo JUri::root();?>/components/com_socialads/images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' />','<img src=\'<?php echo JUri::root();?>components/com_socialads/images/error.gif\' width=\'16\' height=\'16\' border=\'0\' /> Error in Upload, check settings and path info in source code.'); return false;"/>
						<span id ='img_wid'><?php echo $this->zone->img_width; ?> px X  <?php echo $this->zone->img_height; ?> </span>px		
						</p>
						<div class="alert msg_support_type"><p><?php 
						echo JText::_('COM_SA_SUPPOERTED_FORMATS');
						if($socialads_config['allow_flash_ads']){
							echo ','.JText::_('COM_SA_SUPPOERTED_FORMATS_FLASH');
						}
						if($socialads_config['allow_vid_ads']){
							echo ','.JText::_('COM_SA_SUPPOERTED_FORMATS_VID');
						}
					?></p></div>
				</td>
			<!--ajax upload ends here-->			
			</tr>
			<?php }
			}
			?>		
	</table>
</div>

<!-- ad-info end here -->
<input type="hidden" name="upimg" id="upimg" class = 'abc' value="<?php echo JUri::root().$this->addata->ad_image; ?>">	
<?php if (isset($this->addata->ad_image)) { ?>
	<input type="hidden" name="upimgcopy" id="upimgcopy" value="<?php echo JUri::root().$this->addata->ad_image; ?>" />
<?php } else {?>
	<input type="hidden" name="upimgcopy" id="upimgcopy" value="" />
<?php } ?>

			
<div class="adpreview"  >
<!-- ad-preview start here -->

<?php if($this->addata->ad_affiliate != '1'){?> 
	<div id = "layout_div"><!-- for layouts-->
		<table>
		<tr>
			<td><span class="sa_labels"><?php echo JHtml::tooltip(JText::_('TOOLTIP_LAYOUT'), JText::_('LAYOUT'), '', JText::_('LAYOUT'));?></span>
			</td>
		</tr>
		<tr>
			<td>
			<span id = "layout1">
			<?php  
			foreach ($layouts as $lay)
			{
				$check = '';
				if($this->addata->layout == $lay){
					$check = 'checked';
				}	
			?>
				<span  class="layout_span">
				<input class="layout_radio" type="radio" name="layout" value="<?php echo $lay; ?>" <?php echo $check;?> onclick="changelayout(this.value)" >
					<?php if(JVERSION >= '1.6.0') { ?>
					<img class="layout_radio" src="<?php echo JUri::root(); ?>plugins/socialadslayout/plug_<?php echo $lay; ?>/plug_<?php echo $lay; ?>/layout.png" >
				<?php  
					}else{?>					
					<img class="layout_radio" src="<?php echo JUri::root(); ?>plugins/socialadslayout/plug_<?php echo $lay; ?>/layout.png" >
					<?php } ?>
				</span>
				<?php
			}?>
			</span>			

			<input type ="hidden" name="ad_layout_nm" id="ad_layout_nm" value="<?php echo  $this->addata->layout;  ?>"/>
			</td>
		</tr>
		</table>
	</div>
<!--end for layouts-->
	<?php } ?>	
<!--div ad-preview ends here-->

<?php if($this->addata->ad_affiliate != '1'){?> 
	<div style="margin:0px 0px 0px 5px;">
		<div><span class="sa_labels"><?php echo JText::_('BUILDAD_PREVIEW'); ?></span></div>
		<div class="ad-preview1" id="ad-preview"></div>
		<div style="clear:both;"></div>
	</div>
	<?php } ?>	
<div style="clear:both;"></div>

</div><!--ad-dtl-space ends here-->

</div><!-- ad-details end here -->
</fieldset> 
<?php 
	if(($this->addata->ad_alternative == 0 && $this->addata->ad_affiliate == 0) && ($socialads_config['geo_target'] || ($socialads_config['integration'] != 2) ||$socialads_config['context_target'])) { ?>	
<!--lowerdiv call here which continues lower view name as-Targetting -->
<div id="lowerdiv" style="display:block;">
	<!--for showing selection type of fields which is imported from jomsosial fields-->
	<fieldset class="sa_fieldset"> 
		<legend><?php echo JText::_('TARGETING');?></legend>
		<div class="alert"><span class="sa_labels1"><?php echo JText::_('TARGETING_MSG');?> </span></div>
		<!--<div class="componentheading" id="componentheading_field_id"><?php echo JText::_('TARGETING');?></div>-->	
	<!-- geo target start here -->
<?php if( $socialads_config['geo_target'] ){?>
<?php

 if(!empty($this->geo_target))
		$geo_dis = 'style="display:block;"';
	else
		$geo_dis = 'style="display:none;"';
if(!$this->geo_target['region'] && !$this->geo_target['city'])
$everywhere=1;
else
$everywhere=0;
?>		

		<div id="geo_target_space" class="target_space">
			<div>
				<span class="sa_h3_chkbox" ><input type="checkbox" name="geo_target" id="geo_target" class="target" <?php echo (!empty($this->geo_target))?'checked':''; ?> ></span><h3><?php echo JText::_('GEO_TARGETING');?></h3>
				<div style="clear:both;"></div>
			</div>
			<div id="geo_target_div" <?php echo $geo_dis; ?>>
				<div class="alert alert-info"> <span class="sa_labels1"><?php	 echo JText::_('GEO_TARGET_TIP'); ?> </span></div>
			<?php 
			
			if( JFile::exists($geodbfile) ){?>
				<table id="mapping-field-table">
				<?php 
				if(is_array($this->geo_target)){
				?>
<tr>
			 			<td class="ad-fields-lable"><?php echo JText::_("GEO_COUNTRY");?></td>
			 			<td>	
						<ul class='selections' id='selections_country'>
							<input type="text" class="geo_fields ad-fields-inputbox"  id="country" value="<?php echo (isset($this->geo_target['country']) ) ? $this->geo_target['country'] : '' ; ?>" />
							<input type="hidden" class="geo_fields_hidden" name="geo[country]" id="country_hidden" value="" />
							</ul>
						</td>
					</tr>
					<tr colspan="0">							
						<td></td>
						<td>
							<div id ="geo_others" style="display:none;">							
								
								<label class="saradioLabel radio" for="everywhere"><input type="radio" <?php echo ($everywhere ==1)?'checked="checked"' : ''; ?> value="everywhere" name="geo_type" id="everywhere" class="saradioLabel"><?php echo JText::_("SAGEOEVERY"); ?></label>
								<div <?php echo (in_array('byregion',$socialads_config['geo_opt']) )? '' :'style="display:none;"'; ?> >
									<label class="saradioLabel radio" for="byregion"><input type="radio" <?php echo (!empty($this->geo_target['region']))?'checked="checked"' : ''; ?> value="byregion" name="geo_type" id="byregion" class="saradioLabel"><?php echo JText::_("GEO_STATE"); ?></label>
									<ul style="display:none;" class="selections byregion_ul" id='selections_region' >
										<input type="text" class="geo_fields ad-fields-inputbox"  id="region" value="<?php echo (isset($this->geo_target['region']) ) ? $this->geo_target['region'] : '' ; ?>" />
										<input type="hidden" class="geo_fields_hidden" name="geo[region]" id="region_hidden" value="" />
									</ul>
								</div>
								<div <?php echo (in_array('bycity',$socialads_config['geo_opt']) )? '' :'style="display:none;"'; ?>>
									<label class="saradioLabel radio" for="bycity"><input type="radio" <?php echo (!empty($this->geo_target['city']))?'checked="checked"' : ''; ?> value="bycity" name="geo_type" id="bycity" class="saradioLabel"><?php echo JText::_("GEO_CITY"); ?></label>
									<ul style="display:none;" class="selections bycity_ul"  id='selections_city' >
										<input type="text" class="geo_fields ad-fields-inputbox"  id="city" value="<?php echo (isset($this->geo_target['city']) ) ? $this->geo_target['city'] : '' ; ?>" />
										<input type="hidden" class="geo_fields_hidden" name="geo[city]" id="city_hidden" value="" />
									</ul>
								</div>
							</div>
						</td>
					</tr>
				<?php	
				}
				?>
				</table>
		<?php	} /*geo db file chk*/
		else{ ?>
			<span class="sa_labels"><?php	 echo JText::_('GEO_NO_DB_FILE'); ?> </span>
		<?php	}	?> 
			</div><!-- geo_target_div end here -->
		</div>
		<div style="clear:both;"></div>		
<?php	} /*chk for geo target*/?>
<!-- geo target end here -->
	<?php 
	if(($socialads_config['integration'] != 2) ) 
	{ ?>

<?php
 if(!empty($this->ad_socialtarget))
		$social_dis = 'style="display:block;"';
	else
		$social_dis = 'style="display:none;"';
?>
<div	id="social_target_space" class="target_space">
			<div>
				<span class="sa_h3_chkbox" ><input type="checkbox" name="social_target" id="social_target" class="target" <?php echo (!empty($this->ad_socialtarget))?'checked':'' ; ?> ></span><h3><?php echo JText::_('SOCIAL_TARGETING');?></h3>
				<div style="clear:both;"></div>
			</div>
	<div id="social_target_div" <?php echo $social_dis; ?>>
		<div class="alert alert-info">
		<span class="sa_labels"><?php echo JText::_('MESSAGE1');?></span>
		</div>
		<!-- field_target starts here -->
		<div id="field_target">
			<!-- floatmain starts here -->
			<div id="floatmain" >

				<table id="mapping-field-table">	
		
			<?php
			//print_r($this->addata); die('asdasd'); 
			foreach($this->fields as $fields) 
			{
				if($fields->mapping_fieldtype!='targeting_plugin'){ 
			 ?>
				<tr>
		 			<td class="ad-fields-lable"><?php 
					 			if($socialads_config['integration'] == 0){
								$fields->mapping_label = htmlspecialchars( getLangDefinition( $fields->mapping_label));
								}
								else{
									$fields->mapping_label = JText::_("$fields->mapping_label");
								}
					 			
					 			echo $fields->mapping_label;?></td>
		 			<td>
						<?php
						
									?>
		 			   <!--Numeric Range-->
		 				<?php if($fields->mapping_fieldtype=="numericrange") 
							  { $lowvar = $fields->mapping_fieldname.'_low';
								$highvar = $fields->mapping_fieldname.'_high';
							 	if(isset($this->addata->$lowvar)) 
								{ 
									$grad_low=0;
									$grad_high=2030;
									if(strcmp($this->addata->$lowvar,$grad_low)==0)
										$this->addata->$lowvar = '';
									if( (strcmp($this->addata->$highvar,$grad_high)==0) || (strcmp($this->addata->$highvar,$grad_low)==0) )			
										$this->addata->$highvar = '';								
								?>
				 				<input type="textbox"  class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="<?php echo $this->addata->$lowvar; ?>" <?php echo $display_reach; ?> /> 
				 				<?php echo JText::_('SA_TO'); ?> 
				 				<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="<?php echo $this->addata->$highvar?>" <?php echo $display_reach; ?> />
		 						<?php } else { ?>
								<input type="textbox"  class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_low|numericrange|0'; ?>]" value="" <?php echo $display_reach; ?> /> 
				 				<?php echo JText::_('SA_TO'); ?> 
				 				<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php echo $fields->mapping_fieldname.'_high|numericrange|1'; ?>]" value="" <?php echo $display_reach; ?> />
							<?php } } ?>


		 				<!--Freetext-->
		 				<?php if($fields->mapping_fieldtype=="textbox") 
		 				{	$textvar = $fields->mapping_fieldname;
		 					if(isset($this->addata->$textvar)) 
		 					{ ?>
		 					<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php  echo $fields->mapping_fieldname; ?>]" value="<?php echo $this->addata->$textvar; ?>" <?php echo $display_reach; ?> /><?php }
		 					else
								{?>
		 					<input type="textbox" class="ad-fields-inputbox" name="mapdata[][<?php  echo $fields->mapping_fieldname; ?>]" value="" 
<?php echo $display_reach; ?> />
							<?php } 
						}?>
		 		 
		 				
		 				<!--Single Select-->
		 				<?php 	
							if($fields->mapping_fieldtype=="singleselect") 
							{	$singlevar = $fields->mapping_fieldname;
								if(isset($this->addata->$singlevar)) 
								{
									$singleselect = $fields->mapping_options;
									$singleselect = explode("\n",$singleselect);
									
									
									
									for($count=0;$count<count($singleselect); $count++)
													{
														
															$options[] = JHtml::_('select.option',$singleselect[$count], JText::_($singleselect[$count]),'value','text');
													}
									
									$s= array();
									$s[0]->value = '';
									$s[0]->text = JText::_('SINGSELECT');
									$options = array_merge($s, $options);	
									$mdata = str_replace('||',',',$this->addata->$singlevar);
									$mdata = str_replace('|','',$mdata);
									echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox" size="1" '.$display_reach,   'value', 'text', $mdata);
									
									$options= array();
								}
								else
								{
									$singleselect = $fields->mapping_options;
									$singleselect = explode("\n",$singleselect);
									for($count=0;$count<count($singleselect); $count++)
													{
														
															$options[] = JHtml::_('select.option',$singleselect[$count], JText::_($singleselect[$count]),'value','text');
													}
									
									$s= array();
									$s[0]->value = '';
									$s[0]->text = JText::_('SINGSELECT');
									$options = array_merge($s, $options);	
									
									echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox" size="1" '.$display_reach,   'value', 'text', '');	
									$options= array();
								}	
							}		
		 		 	     // Multiselect
		 		 			if($fields->mapping_fieldtype=="multiselect" )
							{ $multivar = $fields->mapping_fieldname;
								if(isset($this->addata->$multivar)) 
									{
										$multiselect = $fields->mapping_options;	  	
										$multiselect = explode("\n",$multiselect);
										$mdata = str_replace('||',',',$this->addata->$multivar);
										$mdata = str_replace('|','',$mdata);
										$multidata = explode(",",$mdata);
										for($cnt=0;$cnt<count($multiselect); $cnt++)
													{
														
															$options[] = JHtml::_('select.option',$multiselect[$cnt], JText::_($multiselect[$cnt]),'value','text');
													}
										if($cnt > 20)
										{	$size = '6';}
										else
											$size = '3';
										echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox" size="'.$size.'" multiple="true" '.$display_reach,  'value', 'text', $multidata);	
										
										$options= array();
									}
									else
									{
									$multiselect = $fields->mapping_options;	  						
									$multiselect = explode("\n",$multiselect);
									for($cnt=0;$cnt<count($multiselect); $cnt++)
													{
														
															$options[] = JHtml::_('select.option',$multiselect[$cnt], JText::_($multiselect[$cnt]),'value','text');
													}
									if($cnt > 20)
									{	$size = '6';}
									else
										$size = '3';	
									echo JHtml::_('select.genericlist', $options, 'mapdata[]['.$fields->mapping_fieldname.',select]', 'class="ad-fields-inputbox" size="'.$size.'" multiple="true"  '.$display_reach,   'value', 'text', '');	
									
									$options= array();}
							}
	
					     	//daterange
		 	 	  			 if($fields->mapping_fieldtype=="daterange") 
							{   $datelowvar = $fields->mapping_fieldname.'_low';
								$datehighvar = $fields->mapping_fieldname.'_high';
								
								if(isset($this->addata->$datelowvar)) 
								{ 
								$date_low = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,1910));
								$date_high = date('Y-m-d 00:00:00', mktime(0,0,0,01,1,2030));
								
								if(strcmp($this->addata->$datelowvar,$date_low)==0)
									$this->addata->$datelowvar = '';

								if(strcmp($this->addata->$datehighvar,$date_high)==0)
									$this->addata->$datehighvar = '';
		
		 						echo JHtml::_('calendar', $this->addata->$datelowvar, 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata[]['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
		 						echo JText::_('SA_TO');
		 						echo JHtml::_('calendar', $this->addata->$datehighvar, 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata[]['.$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
		 						}
		 						
		 						else 
		 						{
		 						echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_low|daterange|0]', 'mapdata[]['.$fields->mapping_fieldname.'_low]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox','onchange'=>'calculatereach()'));
		 						echo JText::_('SA_TO');
		 						echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.'_high|daterange|1]', 'mapdata[]['. 									$fields->mapping_fieldname.'_high]', '%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
		 						
		 						}
		 						if($datelow==null) { $datelow = $fields->mapping_fieldname; } else {  $datelow .= ','.$fields->mapping_fieldname; }
		 				   } 	
		 			     //date
		    				if($fields->mapping_fieldtype=="date") 
		    				{ $datevar = $fields->mapping_fieldname;
		    					if(isset($this->addata->$datevar)) 
		    					{
				 				 	echo JHtml::_('calendar', $this->addata->$datevar , 'mapdata[]['.$fields->mapping_fieldname.']', 'mapdata[]['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
		 					    } 				
		 					    else
		 					    {
				 					echo JHtml::_('calendar', '', 'mapdata[]['.$fields->mapping_fieldname.']', 
				 					'mapdata[]['.$fields->mapping_fieldname.']','%Y-%m-%d', array('class'=>'ad-fields-inputbox',$display_reach_fun));
		 					    } ?>
		 			  <?php }?>    	
				 	</td>		
		 		</tr>			     		
		 <?php }
			} ?>	
<?php
					
					JPluginHelper::importPlugin('socialadstargeting');		
					$dispatcher = JDispatcher::getInstance();
					$plgfields=$this->plgfields;
					$results = $dispatcher->trigger('onFrontendTargetingDisplay',array($plgfields)); 
					foreach($results as $value)					
					{	
						if(!empty($value))	
							foreach($value as $val)
							{
								?>
								<tr>	<td colspan="2">			
								<?php  echo $val;?>
								</td></tr>		
							<?php }	
					}		
										
		 			 ?>
				</table><!-- End fo table -->
			</div><!-- End fo floatmain div -->
			<div id="fixedElement"  >
					<table><tr><td id="estimated_reach"></td></tr></table>
			</div>
		</div><!-- End of field_target div -->	
	</div> <!--end of social_target div -->
</div> 

	<?php }
	?>
	
	<!-- context target start here -->
<?php if( $socialads_config['context_target'] ){
 

 if(isset($this->context_target))
		$context_dis = 'style="display:block;"';
	else
		$context_dis = 'style="display:none;"';
?>		
	<div style="clear:both;"></div>
		<div id="context_target_space" class="target_space">
			<div>
				<span class="sa_h3_chkbox" ><input type="checkbox" name="context_target"  id="context_target" class="target" <?php echo (isset($this->context_target_data_keywordtargeting))?'checked':''; ?> /></span><h3><?php echo JText::_('CONTEXT_TARGET');?></h3>
				<div style="clear:both;"></div>
			</div>		
			<div id="context_target_div" <?php echo $context_dis; ?>>
				<div class="alert alert-info"><span class="sa_labels1"><?php	 echo JText::_('CONTEXT_TARGET_TIP'); ?> </span></div>

				<table id="mapping-field-table">
					<tr>
			 			<td class="ad-fields-lable"><?php echo JText::_("CONTEXT_TARGET_INPUTBOX");?></td>
			 			<td>	
						<input type="text" name="context_target_data[keywordtargeting]" class="inputbox" id="context_target_data[][keywordtargeting]" value="<?php echo $this->context_target_data_keywordtargeting;?>" onchange="" />
						</td>
					</tr>
					
				</table>

		
			</div><!-- context_target_div end here -->
			<div style="clear:both;"></div>		
		</div>

<?php	} /*chk for context target*/?>
<!-- context target end here -->

<div style="clear:both;"></div>
</fieldset>

<div style="clear:both;"></div>
<div class="form-actions">
<input class="button btn btn-primary" type="submit" value="<?php echo JText::_('UPDATE_BUTTON');?>" name="review" id="review" onclick="return checkreview('<?php echo $datelow; ?>','<?php echo JText::_('DATE_VALI'); ?>');" />
</div>
</div><!--lowerdiv ends here-->
<?php		
	} //end for alternate ad if
	else if( ($this->addata->ad_alternative == 0 && $this->addata->ad_affiliate == 0) ){
	// display update button when no targeting is set
?>
<div style="clear:both;"></div>
<div class="form-actions">
<input class="button btn btn-primary" type="submit" value="<?php echo JText::_('UPDATE_BUTTON');?>" name="review" id="review" onclick="return checkreview('<?php echo $datelow; ?>','<?php echo JText::_('DATE_VALI'); ?>');" />
</div>
<?php	
	}
		
	if(($this->addata->ad_alternative == 1 || $this->addata->ad_affiliate == 1))  { ?>	
	<div class="form-actions">
	<input class="button btn btn-primary" type="submit" value="<?php echo JText::_('UPDATE_BUTTON');?>" name="review" id="altad" onclick="return altadjsm();" />
	</div>
<?php } ?>

		<input type="hidden" name="option" value="com_socialads" />
		<input type="hidden" name="view" value="managead" />
		<input type="hidden" name="task" value="update" />
		<input type="hidden" name="controller" value="managead" />
		<input type="hidden" name="editview" id="editview" value="1" />
<!--added by sagar -->
		<input type="hidden" name="joomla_version" id="joomla_version" value="<?php echo JVERSION ?>" />
		<input type="hidden" name="config_estimated_reach" id="config_estimated_reach" 
					value="<?php echo $socialads_config['estimated_reach'] ?>" />
		<!--added by sagar -->
</form>
</div>
</div> <!--techjoomla-bootstrap ends-->
<!--added by sagar -->
<?php
if($display_reach!='')
{
?>
<script>
	jQuery(document).ready(function(){
	calculatereach();
	});
</script>
<?php
}
?>
<script>
	jQuery(document).ready(function(){
		show_hide_geo("geo_target");
		show_hide_geo("social_target");
	});
</script>
<!--added by sagar -->


