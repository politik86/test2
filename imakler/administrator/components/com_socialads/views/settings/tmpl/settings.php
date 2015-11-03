<?php
defined('_JEXEC') or die('Restricted access');
jimport('joomla.html.pane');
jimport( 'joomla.form.formvalidator' );
$document = JFactory::getDocument();

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

$no="";
$cron_key='';
$yes="";
if(!empty($socialads_config['show_slab']))
$show_slab=1;
else
$show_slab=0;
if(!empty($socialads_config['priority_random']))
$priority_random=1;
else
$priority_random=0;


if(!empty($socialads_config['priority_random']))
$priority_random=1;
else
$priority_random=0;
	if( isset($socialads_config['gateways']) )
		$gateways =	$socialads_config['gateways'];
	else
		$gateways = '';

		$gatewayselect = array();
	foreach($this->gatewayplugin as $gateway)
	{
		$gatewayname = ucfirst(str_replace('plugpayment', '',$gateway->element));
		$gatewayselect[] = JHTML::_('select.option',$gateway->element, $gatewayname);
	}

if(!$socialads_config['cron_key'])
	$cron_key='a1z19';
else
	$cron_key= $socialads_config['cron_key'];
		if($socialads_config['article'])
		$article=$socialads_config['article'];
		else
		$article= 0;

$cbpath = JPATH_SITE.DS."administrator".DS."components".DS."com_comprofiler";
$jspath = JPATH_SITE.DS."administrator".DS."components".DS."com_community";
$espath = JPATH_SITE.DS."administrator".DS."components".DS."com_easysocial";

?>


<script type="text/javascript">

window.addEvent('domready', function(){
   document.formvalidator.setHandler('charector_only', function(value) {
      regex=/^[a-zA-Z\s]+$/;
      return regex.test(value);
   });
});


function migrateads()
{
	var migrate_sure=confirm('<?php echo JText::_('MIGRATE_AD'); ?>');
	if (migrate_sure==true)
	{
		var selected = jQuery(".camp_and_new:checked");
		var camp_or_old = selected.val();
		jQuery('#migrate_btn').hide();
		jQuery('#migrate_btn_for_old').hide();
		jQuery('#loader_image_div').show();

		jQuery.ajax({
			url: '?option=com_socialads&task=getmigration&camp_or_old='+camp_or_old,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				if(data == 1)
				{
					jQuery('#loader_image_div').hide();
					if(camp_or_old ==1)
						jQuery('#migration_status').show();
					else
						jQuery('#migration_status_for_old').show();
					alert('<?php echo JText::_('MIGRATE_CONFIRM_SAVE_POP'); ?>');
					Joomla.submitbutton('save');
				}
				else
				{
					jQuery('#loader_image_div').hide();
					jQuery('#migrate_error_div').show();
				}
			}
		});
	}
	else
	{
		return false;
	}
}

function show_bid_stat(value)
{
	if(value==1)
	{
		jQuery('#bid_row').show();
		jQuery('#static_val').hide();
	}
	else
	{
		jQuery('#bid_row').hide();
		jQuery('#static_val').show();
	}
}
function check_migrate(camp_or_old){
	var result;
	jQuery.ajax({
		url: '?option=com_socialads&task=getmigration&call=camp_hide&camp_or_old='+camp_or_old,
		type: 'GET',
		async: false,
		error: function(){
            return 0;
        },
		dataType: 'json',
		success: function(data) {
		if(data == 1)
		{
			if(camp_or_old == '1')
			jQuery('#migrate_div').show();
			else
			jQuery('#migrate_div_for_old').show();
			result =  1;
		}
		else
			result = 0;
		}
	});
return result;
}
function camp_hide(radiovalue,element)
{

	//if(obj.value == 1)
	//{
	var result = check_migrate(radiovalue);
	//}

	if(radiovalue == '1' || radiovalue ==1)
	{
	jQuery(element).show();
	jQuery('#migrate_div_for_old').hide();
	jQuery('#migrate_error_div').hide();
	jQuery("#configpricing_opt option[value='2']").remove();/*remove per day option */
	jQuery('.price2').hide();
	jQuery('.pay_per_ad').hide();
	}
	else
	{
	jQuery(element).hide();
	jQuery('#migrate_error_div').hide();
	jQuery('#migrate_div').hide();
	if (!jQuery("#configpricing_opt option[value='2']").length){
		if('true' == '<?php if( in_array('2',$socialads_config['pricing_opt']) ) echo 'true'; else 'false'; ?>')
			var day_sel = 'selected="selected"';
		else
			var day_sel = '';
		jQuery("#configpricing_opt").append('<option value="2" '+day_sel+' ><?php echo JText::_('SA_DAY') ?></option>');/*remove per day option */
		jQuery('.price2').show();
	}
	jQuery('.pay_per_ad').show();

	}
}


function toggleslab(obj)
{

	if(parseInt(obj.value)==1)
	{
		jQuery('.slab_tr_hide').show();

	}else
	  jQuery('.slab_tr_hide').hide();

}
function integra_change(intr){
	if( (intr.value =="0" && !'<?php echo JFolder::exists($cbpath); ?>' )
	|| (intr.value =="1" && !'<?php echo JFolder::exists($jspath); ?>' )
	|| (intr.value =="3" && !'<?php echo JFolder::exists($espath); ?>' ) ){
		alert('"'+jQuery("label[for='config[integration]"+intr.value+"']").text().trim() +'"<?php echo JText::sprintf('COM_SOCIALADS_SETTINGS_EXT_NOT_EXISTS','') ?>');
		jQuery('input[name=\"config[integration]\"]:first').attr('checked', true);
	}
}
  jQuery(document).ready(function(){
	//	jQuery
	var selected = jQuery(".check_autoplay:checked").val();
	if(selected==1)
	{
	jQuery('.autoplay_video').show();
	}
	/*jQuery('.slab_tr_hide').hide();*/
  var showslab="<?php echo $show_slab;?>";
	if(parseInt(showslab)==1)
	jQuery('.slab_tr_hide').show();
	else
  jQuery('.slab_tr_hide').hide();

   var article="<?php echo $article;?>";
	if(parseInt(article)==1)
	jQuery('.tncclass').show();
	else
  jQuery('.tncclass').hide();

  var priority_random="<?php echo $priority_random;?>";
	if(parseInt(priority_random)==1)
	jQuery('.priority_tr').show();
	else
	jQuery('.priority_tr').hide();

	hideshow("<?php echo $socialads_config['context_target'];?>",'.context_target_tr');
	hideshow("<?php echo $socialads_config['context_target_smartsearch'];?>",'.contextual_smartsearch_cron_tr');
	hideshow("<?php echo $socialads_config['context_target_keywordsearch'];?>",'#context_target_keywordsearch_id');

	jQuery("#configpricing_opt").change(function(){
		jQuery('option',this).each(function(){
			val = jQuery(this).val();
			if(jQuery(this).is(":selected")){
				jQuery('.price'+val).show();
			}
			else{
				jQuery('.price'+val).hide();
			}

		});
	});



 });

	function togglestate(obj,name1)
	{
		if(parseInt(obj.value)==1)
			jQuery('#'+name1).show();
		else
		  jQuery('#'+name1).hide();
	}
 //to hie show autoplay video tr


 function moveUpItem(){
  jQuery('#configpriority option:selected').each(function(){
			jQuery(this).insertBefore(jQuery(this).prev());
  });

  jQuery('#configpriority option').each(function(){
	jQuery(this).attr('selected', 'selected');
  });
 }

 function moveDownItem(){
  jQuery('#configpriority option:selected').each(function(){
   	jQuery(this).insertAfter(jQuery(this).next());

  });
  jQuery('#configpriority option').each(function(){
	jQuery(this).attr('selected', 'selected');
  });
 }
</script>
<?php
$model = $this->getModel( 'settings' );
if($socialads_config['select_campaign']==1)
{
	$ads2_camp=$model->migrateads_camp('camp_hide');
}
else
{
	$ads2_old=$model->migrateads_old('camp_hide');//check if migrating camp_budget to old
}
if(JVERSION >= '1.6.0')
	$js_key="Joomla.submitbutton = function(task){ ";
	else
	$js_key="function submitbutton( task ){";

	$js_key.="

var pay_mode = jQuery('input:radio[name =\"config[select_campaign]\"]:checked').val();
jQuery.when(check_migrate(pay_mode)).done(function(mode_result){
	if(mode_result == 1  ){
		alert('".JText::_('SA_MUST_MIGRATE')."');
	}
	else{
		var validateflag = document.formvalidator.isValid(document.adminForm);
		if(validateflag)";
			if(JVERSION >= '1.6.0')
			$js_key.="Joomla.submitform(task);";
			else
			$js_key.="document.adminForm.submit();";
		$js_key.="else
		return false;
	}
});
}


function hideshow1(obj,element){

if(obj.value == '1' || obj.value ==1)
	jQuery(element).show();
else
	jQuery(element).hide();


}
   function geo_clk(){
		if( !(jQuery('#byreg').is(':checked')) &&  !(jQuery('#bycity').is(':checked')) ){
		 jQuery('#byreg').attr('checked', true);
		 jQuery('#bycity').attr('checked', true);

		}
   }
	function autoup()
	{
		var r=confirm('".JText::_('Are you sure?')."');
		if (r==true){
			jQuery('#geo_db').html('<div>".JText::_('SAGEO_CITY_WAIT')."</div><img src =\'".JURI::root()."components/com_socialads/images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' />');

			jQuery.ajax({
			url: '?option=com_socialads&controller=settings&task=getgeodb&pkey'+$cron_key,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
					var msg ='';
					alert(data.downmsg);
					if(data.downmsg !=1)
						msg = '".JText::_('SAGEO_CITY_NO_DOWN')." '+data.downmsg;
					else if(data.readmsg !=1)
						msg = '".JText::_('SAGEO_CITY_NO_READ')."';
					else
						msg = '".JText::_('SAGEO_CITY_DONE')."';

					jQuery('#geo_db').html(msg);
			}
			});
		}
	}

	function updategeodb()
	{
		var r=confirm('".JText::_('SAGEO_DB_SURE')."');
		if (r==true){
			jQuery('#geo_database').html('<div>".JText::_('SAGEO_DB_WAIT')."</div><img src =\'".JURI::root()."components/com_socialads/images/loader_light_blue.gif\' width=\'128\' height=\'15\' border=\'0\' />');

			jQuery.ajax({
			url: '?option=com_socialads&controller=settings&task=populategeoDB&pkey'+$cron_key,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
			//alert('here');

					msg = data.displaymsg;
					jQuery('#geo_database').html(msg);
			}
			});
		}
	}
	/* this function allow only numberic and specified char (at 0th position)
	// ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
				(code 46 for dot/full stop .)
		@param el :: html element
		@param allowed_ascii::ascii code that shold allow

	*/
	function checkforalpha(el,allowed_ascii)
	{
		allowed_ascii= (typeof allowed_ascii === 'undefined') ? '' : allowed_ascii;
		var i =0 ;
		for(i=0;i<el.value.length;i++){
			if(el.value=='0'){
				alert('".JText::_('COM_SOCIALADS_ZERO_VALUE_VALI_MSG')."');
				el.value = el.value.substring(0,i); break;
			}
		 if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 )){
				if(allowed_ascii !=el.value.charCodeAt(i) ){
					alert('".JText::_('COM_SOCIALADS_NUMONLY_VALUE_VALI_MSG')."'); el.value = el.value.substring(0,i); break;
				}
			}
		}
	}

	function checkforalphabets(el,aa)
	{
	 if(jQuery('input[name=\"config['+aa+']\"]:checked').val()=='1') {

		var i =0 ;
		for(i=0;i<el.value.length;i++){
		  if((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123)) {
			   alert('Please Enter Numerics'); el.value = el.value.substring(0,i); break;
		  }
		}
		}
	}
	function checkfornum(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
		   if(el.value.charCodeAt(i) > 47 && el.value.charCodeAt(i) < 58) {
			   alert('Numerics Not Allowed'); el.value = el.value.substring(0,i); break;
			}
		}
	}

window.addEvent('domready', function(){
      document.formvalidator.setHandler('name', function (value) {
				if(value<=0){
				  alert( 'Value must be greater than 0' );
				return false;
				}
				else if(value == ' '){
        	alert('Field should not be blank ');
        	return false;
        }
				else{
				return true;
				}
				}
        );
   });

	";
	$document->addScriptDeclaration($js_key);


?>

<?php
	$singleselect = array();
	$singleselect[] = JHTML::_('select.option','1', JText::_('CLICK'));
	$singleselect[] = JHTML::_('select.option','0', JText::_('IMPR'));
	$singleselect[] = JHTML::_('select.option','2', JText::_('SA_DAY'));
//	$singleselect[] = JHTML::_('select.option','3', JText::_('SA_MONTH'));

//option 3..for easysocial added by aniket
	$import = array(2=>JText::_('NO_SOCIAL'),0=>JText::_('SA_CB'), 1=>JText::_('SA_JS') , 3=>JText::_('COM_SOCIALADS_SA_ES') );
	$options= array();
		foreach($import as $key=>$value) {
			$options[] = JHTML::_('select.option', $key, $value);
		}


	$singleselect_ad = array();
	$singleselect_ad['0'] = JHTML::_('select.option','text_img', JText::_('AD_TYP_TXT_IMG'));
	$singleselect_ad['1'] = JHTML::_('select.option','text',JText::_('AD_TYP_TXT'));
	$singleselect_ad['2'] = JHTML::_('select.option','img', JText::_('AD_TYP_IMG'));

	$singleselect_prio = array();
if(empty($socialads_config['priority_random']))
{
	$singleselect_prio['0'] = JHTML::_('select.option','0',JText::_('SA_SOCIAL'));
	$singleselect_prio['1'] = JHTML::_('select.option','1', JText::_('SA_GEO'));
	$singleselect_prio['2'] = JHTML::_('select.option','2', JText::_('SA_CONTEXT'));
}
else
{
	//$socialads_config['priority']);die;


	$i=0;
	foreach($socialads_config['priority'] as $key=>$value) {

		if($value==0)
		$valuestr=JText::_('SA_SOCIAL');
		else if($value==1)
		$valuestr=JText::_('SA_GEO');
		else if($value==2)
		$valuestr=JText::_('SA_CONTEXT');
	//	echo $value;
	//echo "---";
	//	echo $valuestr;
	//		echo "</br>";
		$singleselect_prio[] = JHTML::_('select.option',$value,$valuestr);
		$i++;

	}
	/*$singleselect_prio['0'] = JHTML::_('select.option','0',JText::_('SA_SOCIAL'));
	$singleselect_prio['1'] = JHTML::_('select.option','1', JText::_('SA_GEO'));
	$singleselect_prio['2'] = JHTML::_('select.option','2', JText::_('SA_CONTEXT'));*/
}




					$display_reach= 1;
					if($socialads_config['display_reach']==0)
						$display_reach = 0;
					else
					$display_reach = 1;

				$import_reach = array(0=>JText::_('I_NO'), 1=>JText::_('I_YES') );
				$options_reach= array();
				foreach($import_reach as $key_reach=>$value_reach) {
				 	 $options_reach[] = JHTML::_('select.option', $key_reach, $value_reach);
					 }

					$display_reg = 1;
					if($socialads_config['sa_reg_show']==0)
						$display_reg = 0;
					else
					$display_reg = 1;

				$sa_reg= array(0=>JText::_('I_NO'), 1=>JText::_('I_YES') );
				$options_reg= array();
				foreach($sa_reg as $key_reg=>$value_reg) {
				 	 $options_reg[] = JHTML::_('select.option', $key_reg, $value_reg);
					 }
?>
<div class="techjoomla-bootstrap">
<form method="POST" name="adminForm" id="adminForm" class="form-validate" action="">
	<?php
// @ sice version 3.0 Jhtmlsidebar for menu
	if(JVERSION>=3.0):
		 if (!empty( $this->sidebar)) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10 socialads_settings">
		<?php else : ?>
			<div id="j-main-container" class="socialads_settings">
		<?php endif;
	endif;
	?>
	<input type="hidden" name="check" value="post"/>
	<div class="tabbable <?php echo (JVERSION < 3.0) ? 'socialads_settings' : '' ;?>">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general_config" data-toggle="tab"><?php echo JText::_('COM_SOCIALADS_TARGET_SET');?></a></li>
			<li><a href="#ad_pricing_config" data-toggle="tab"><?php echo JText::_('COM_SOCIALADS_PRICING_SET');?></a></li>
			<li><a href="#ad_specific_config" data-toggle="tab"><?php echo JText::_('COM_SOCIALADS_AD_SET');?></a></li>
			<li><a href="#ad_other_config" data-toggle="tab"><?php echo JText::_('COM_SOCIALADS_OTHER_SET');?></a></li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="general_config">
				<fieldset class="form-horizontal"><legend><?php echo JText::_('INT_WIT');?></legend>
					<div class="control-group">
							<label class="control-label"><?php echo JHTML::tooltip(JText::_('IMPORT_TOOLTIP'), JText::_('IMPORT'), '', JText::_('IMPORT'));?></label>

							<?php
									$value= '';
									if($socialads_config['integration']==0)
										$value = 0;
									else if($socialads_config['integration']==1)
										$value = 1;
									else if($socialads_config['integration']==2)
										$value = 2;
									else if($socialads_config['integration']==3)
										$value = 3;
							?>
							<?php echo (JVERSION < 3.0) ? "<div class='controls'>" : ' ' ;?>
								<?php echo $radiolist = JHTML::_('select.radiolist', $options, 'config[integration]', 'class="inputbox fieldlist sa_setting_radio"  onchange="integra_change(this)" ', 'value', 'text', $socialads_config['integration']);?>
							<?php echo (JVERSION < 3.0) ? "</div>" : ' '; ?>
					</div>

					<?php
					if(file_exists(JPATH_SITE.DS."administrator".DS."components".DS."com_community"))
					{
						//$parser		= JFactory::getXML('Simple');

						// Load the local XML file first to get the local version
						$xml		=JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_community' . DS . 'community.xml';

						$xml = JFactory::getXML($xml);
						$jsversion=(string)$xml->version;
						if($jsversion >= 2.4 ){
						?>
						<div class="control-group" style="display:block">
						<?php }else{
						?>
						<div class="control-group" style="display:none;">
						<?php }
						?>
							<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_SHOW_JS_TOOLBAR'), JText::_('SHOW_JS_TOOLBAR'), '',
									JText::_('SHOW_JS_TOOLBAR'));
							?>
							</label>

							<div id="show_js_toolbar" class="input-append yes_no_toggle">
								<input type="radio" name="config[show_js_toolbar]" id="show_js_toolbar1" value="1" <?php echo ($socialads_config['show_js_toolbar'] == 1 )? 'checked="checked"' :'';?> />
								<label class="first btn <?php echo ($socialads_config['show_js_toolbar']  == 1 )? 'btn-success' :'';?>" for="show_js_toolbar1"><?php echo JText::_('SA_YES');?></label>
								<input type="radio" name="config[show_js_toolbar]" id="show_js_toolbar0" value="0" <?php echo ($socialads_config['show_js_toolbar'] == 0)? 'checked="checked"' :'';?>  />
								<label class="last btn <?php echo ($socialads_config['show_js_toolbar']  == 0 )? 'btn-danger' :'';?>" for="show_js_toolbar0"><?php echo JText::_('SA_NO'); ?></label>
							</div>
				</div>
				<?php

			}
			?>
			</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('SAPRIO');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SAPRIO_TOOLTIP'), JText::_('SAPRIO'), '', JText::_('SAPRIO'));?></label>

							<?php
								if($socialads_config['priority_random']!=1)
								$socialads_config['priority_random']=0;
							?>
						<div id="priority_random" class="input-append yes_no_toggle">
							<input type="radio" name="config[priority_random]" id="priority_random1" value="1" <?php echo ($socialads_config['priority_random'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['priority_random']  == 1 )? 'btn-success' :'';?>" for="priority_random1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[priority_random]" id="priority_random0" value="0" <?php echo ($socialads_config['priority_random'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['priority_random']  == 0 )? 'btn-danger' :'';?>" for="priority_random0"><?php echo JText::_('SA_NO'); ?></label>
						</div>

					</div>
					<div class="control-group priority_tr" >
						<label class="control-label" for="configpriority"><?php echo JHTML::tooltip(JText::_('SAPRIOU_TARGET'), JText::_('SAPRIOU'), '', JText::_('SAPRIOU'));?></label>
						<?php echo JHTML::_('select.genericlist',$singleselect_prio, "config[priority][]", 'class="inputbox chzn-done required" data-chosen="sa" multiple="multiple" ', "value", "text", $singleselect_prio ); ?>
						<input type="button" class="btn btn-success" value="Move Up" id="mup" onclick="moveUpItem()">
						<input type="button" class="btn btn-warning" value="Move Down" id="mdown" onclick="moveDownItem()">
					</div>

				</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('SAGEO');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SAGEOEN_TOOLTIP'), JText::_('SAGEOEN'), '', JText::_('SAGEOEN'));?></label>

						<div id="geo_target" class="input-append yes_no_toggle">
							<input type="radio" name="config[geo_target]" id="geo_target1" value="1" <?php echo ($socialads_config['geo_target'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['geo_target']  == 1 )? 'btn-success' :'';?>" for="geo_target1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[geo_target]" id="geo_target0" value="0" <?php echo ($socialads_config['geo_target'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['geo_target']  == 0 )? 'btn-danger' :'';?>" for="geo_target0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
						<?php
						if(!$this->geotablepresent)
						{?>
					<div class="control-group geo_target_tr">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SAGEO_DB_TOOLTIP'), JText::_('SAGEO_DB'), '', JText::_('SAGEO_DB'));?></label>
						<div class="controls">
									<div id="geo_database">
									<?php	echo '<div class="install_fail">'.JText::_('SAGEO_INSTL_NOT_COMPLETE').'</div>';
										?>
										<div>
											<input type="button" class="btn btn-primary" name="geo_db_btn" id="geo_db_btn" onclick="updategeodb()" value="<?php echo JText::_('SAGEO_INSTL_LINK');?>">
										</div>
								 </div>

						</div>
					</div>
						<?php
						}
						?>
						<?php
						$dis = '';
						if($socialads_config['geo_target'] == 0)
							$dis = 'style="display:none;"';
						?>

						<div class="control-group geo_target_tr" <?php echo $dis; ?> >
								<label class="control-label"><?php echo JHTML::tooltip(JText::_('SAGEO_CITY_TOOLTIP'), JText::_('SAGEO_CITY'), '', JText::_('SAGEO_CITY'));?></label>
								<div class="controls">
									<?php
									$geodbfile = JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."GeoLiteCity.dat";
									if(!(JFile::exists($geodbfile)) ){
									?>
									<div class="alert alert-error"><i><font><?php echo JText::_('SA_MISSING_MSG')." ".JText::_('SAGEO_CITY_ERROR_MSG'); ?></font></i></div>
									<?php }
									else{
									?>
									<div class="alert alert-success"><i><font ><?php echo JText::_('SA_PRESENT_MSG')." ".JText::_('SAGEO_CITY_PRESENT_MSG'); ?></font></i></div>
									<?php }
									?>
								<!-- <div id="geo_db"><input type="button" onclick="autoup()" value="<?php //echo JText::_('SAGEO_UPDATE');?>"></div>
								<br> -->
								<div class="alert alert-info" id="geo_db_inst" >
								<a href="http://geolite.maxmind.com/download/geoip/database/GeoLiteCity.dat.gz" target="_blank" ><?php echo JText::_('SAGEO_DOWN_FILE');?></a>
									<div>
									<?php echo JText::_('SAGEO_FILE').' "your_domain/components/com_socialads/geo"';?>
									</div>
								</div>
							</div>
						</div>
						<div class="control-group geo_target_tr" <?php echo $dis; ?> >
								<label class="control-label"><?php echo JHTML::tooltip(JText::_('SAGEO_OPT_TOOLTIP'), JText::_('SAGEO_OPT'), '', JText::_('SAGEO_OPT'));?></label>
								<div class="controls">
									<input type="checkbox" name="config[geo_opt][]" value="byregion" id="byreg"  onchange="geo_clk()" <?php echo (in_array('byregion',$socialads_config['geo_opt']) )?'checked' :''; ?> ><?php echo JHTML::tooltip(JText::_('SAGEOREGION_TOOLTIP'), JText::_('SAGEOREGION'), '', JText::_('SAGEOREGION'));?>
									<input type="checkbox" name="config[geo_opt][]" value="bycity" id="bycity"  onchange="geo_clk()" <?php echo (in_array('bycity',$socialads_config['geo_opt']) )?'checked' :''; ?> ><?php echo JHTML::tooltip(JText::_('SAGEOCITY_TOOLTIP'), JText::_('SAGEOCITY'), '', JText::_('SAGEOCITY'));?>
								</div>
						</div>
				<!-- 		<tr class="geo_target_tr" <?php //echo $dis; ?> >
								<td  width="25%"><?php //echo JHTML::tooltip(JText::_('SAGEO_CITY_CRON_TOOLTIP'), JText::_('SAGEO_CITY_CRON'), '', JText::_('SAGEO_CITY_CRON'));?></td>
								<td  class="setting-td" ><?php //echo JRoute::_(JURI::base().'/index.php?option=com_socialads&tmpl=component&controller=settings&task=getgeodb&pkey='.$cron_key)?>
								</td>
						</tr>
				-->
					</fieldset>
			<?php
					$dis = '';
					if($socialads_config['context_target'] == 0)
						$disc_context = 'style="display:none;"';
						else
						$disc_context = '';

						$searchkeyword_tr_config=$disc_context;
						$smart_searchkeyword_tr_config=$disc_context;
						if($socialads_config['context_target'] ==1)
						{
							if($socialads_config['context_target_keywordsearch']==0)
							$searchkeyword_tr_config='style="display:none;"';
							else
							$searchkeyword_tr_config='';

							if($socialads_config['context_target_smartsearch']==0)
							$smart_searchkeyword_tr_config='style="display:none;"';
							else
							$smart_searchkeyword_tr_config='';
						}
					?>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('CONTEXTUAL_TARGET');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('ENBLE_CONTEXT_TARGET_DESC'), JText::_('CONTEXT_TARGET'), '', JText::_('CONTEXT_TARGET'));?></label>
						<div id="context_target" class="input-append yes_no_toggle">
							<input type="radio" name="config[context_target]" id="context_target1" value="1" <?php echo ($socialads_config['context_target'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['context_target']  == 1 )? 'btn-success' :'';?>" for="context_target1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[context_target]" id="context_target0" value="0" <?php echo ($socialads_config['context_target'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['context_target']  == 0 )? 'btn-danger' :'';?>" for="context_target0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>

					<div class="control-group context_target_tr"	<?php echo $disc_context;?> >
						<label class="control-label" for="config[context_target_keywordsearch]" ><?php echo JHTML::tooltip(JText::_('ENBLE_CONTEXT_TARGET_SEARCH_DESC'), JText::_('ENBLE_CONTEXT_TARGET_SEARCH'), '', JText::_('ENBLE_CONTEXT_TARGET_SEARCH'));?></label>
						<div id="context_target_keywordsearch" class="input-append yes_no_toggle">
							<input type="radio" name="config[context_target_keywordsearch]" id="context_target_keywordsearch1" value="1" <?php echo ($socialads_config['context_target_keywordsearch'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['context_target_keywordsearch']  == 1 )? 'btn-success' :'';?>" for="context_target_keywordsearch1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[context_target_keywordsearch]" id="context_target_keywordsearch0" value="0" <?php echo ($socialads_config['context_target_keywordsearch'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['context_target_keywordsearch']  == 0 )? 'btn-danger' :'';?>" for="context_target_keywordsearch0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>

					<div class="control-group "	<?php echo $searchkeyword_tr_config;?> id="context_target_keywordsearch_id">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('CONTEXT_TARGET_PARAM_DESC'), JText::_('CONTEXT_TARGET_PARAM'), '', JText::_('CONTEXT_TARGET_PARAM'));?></label>
						<div class="controls"><textarea name="config[context_target_param]" id="config[context_target_param]" rows="6" cols="100" class="inputbox required"><?php echo trim($socialads_config['context_target_param']); ?></textarea>
						</div>
					</div>
					<div class="control-group context_target_tr"	<?php echo $disc_context;?> >
						<label class="control-label"  for="config[context_target_metasearch]"><?php echo JHTML::tooltip(JText::_('ENBLE_CONTEXT_TARGET_META_DESC'), JText::_('ENBLE_CONTEXT_TARGET_META'), '', JText::_('ENBLE_CONTEXT_TARGET_META'));?></label>
						<div id="context_target_metasearch" class="input-append yes_no_toggle">
							<input type="radio" name="config[context_target_metasearch]" id="context_target_metasearch1" value="1" <?php echo ($socialads_config['context_target_metasearch'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['context_target_metasearch']  == 1 )? 'btn-success' :'';?>" for="context_target_metasearch1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[context_target_metasearch]" id="context_target_metasearch0" value="0" <?php echo ($socialads_config['context_target_metasearch'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['context_target_metasearch']  == 0 )? 'btn-danger' :'';?>" for="context_target_metasearch0"><?php echo JText::_('SA_NO'); ?></label>
						</div>

					</div>
						<?php

						if(JVERSION >= '2.5.0')
						{

						?>



						<!--tr	class="context_target_tr"	<?php //echo $disc_context;?>>
						<td  width="25%"><?php //echo JHTML::tooltip(JText::_('ENBLE_CONTEXT_TARGET_DEBUG_DESC'), JText::_('ENBLE_CONTEXT_TARGET_DEBUG'), '', JText::_('ENBLE_CONTEXT_TARGET_DEBUG'));?>
						<label for="config[context_target]" ></label></td>
						<td class="setting-td">
						<?php
							//echo JHTML::_( 'select.booleanlist', 'config[context_target_debug]', 'class="selarticle sa_setting_radio"',$socialads_config['context_target_debug'], JText::_('I_YES'), JText::_('I_NO') );
						?>
						</td>
						</tr-->
						<div class="control-group context_target_tr"	<?php echo $disc_context;?>>
							<label class="control-label" for="config[context_target_smartsearch]" ><?php echo JHTML::tooltip(JText::_('ENBLE_CONTEXT_TARGET_SPECIAL_DESC'), JText::_('ENBLE_CONTEXT_TARGET_SPECIAL'), '', JText::_('ENBLE_CONTEXT_TARGET_SPECIAL'));?></label>
							<div id="context_target_smartsearch" class="input-append yes_no_toggle">
								<input type="radio" name="config[context_target_smartsearch]" id="context_target_smartsearch1" value="1" <?php echo ($socialads_config['context_target_smartsearch'] == 1 )? 'checked="checked"' :'';?> />
								<label class="first btn <?php echo ($socialads_config['context_target_smartsearch']  == 1 )? 'btn-success' :'';?>" for="context_target_smartsearch1"><?php echo JText::_('SA_YES');?></label>
								<input type="radio" name="config[context_target_smartsearch]" id="context_target_smartsearch0" value="0" <?php echo ($socialads_config['context_target_smartsearch'] == 0)? 'checked="checked"' :'';?>  />
								<label class="last btn <?php echo ($socialads_config['context_target_smartsearch']  == 0 )? 'btn-danger' :'';?>" for="context_target_smartsearch0"><?php echo JText::_('SA_NO'); ?></label>
							</div>

						</div>
						<div class="control-group  contextual_smartsearch_cron_tr" 	 <?php echo $smart_searchkeyword_tr_config?> >
							<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_CONTEXT_SEARCH_INDEX_CRON_URL'), JText::_('CONTEXT_SEARCH_INDEX_CRON_URL'), '', JText::_('CONTEXT_SEARCH_INDEX_CRON_URL'));?></label>
							<div class="controls">
								<?php echo JRoute::_(JURI::root().'index.php?option=com_socialads&controller=indexer&task=makeIndexing&tmpl=component&indexlimitstart=0&indexlimit='.$socialads_config['contextual_smartsearch_cron_batchsize'].'&pkey='.$cron_key)?>
							</div>
						</div>

						<div class="control-group context_target_tr contextual_smartsearch_cron_tr" 	 <?php echo $smart_searchkeyword_tr_config;?> >
							<label class="control-label" for="config[contextual_smartsearch_cron_batchsize]"> <?php echo JHTML::tooltip(JText::_('CONTEXT_SEARCH_INDEX_CRON_BATCH'), JText::_('CONTEXT_SEARCH_INDEX_CRON_BATCH_DESC'), '', JText::_('CONTEXT_SEARCH_INDEX_CRON_BATCH'));?></label>
							<div class="controls">
								<input type="text" id="contextual_smartsearch_cron_batchsize" name="config[contextual_smartsearch_cron_batchsize]"  width="50%" value="<?php echo $socialads_config['contextual_smartsearch_cron_batchsize']; ?>" class="inputbox" />
							</div>
						</div>
						<?php
						}
						?>
					</fieldset>
				</div><!--general config-->
			<!--ANIKET.....new....price change model-->
			<div  class="tab-pane" id="ad_pricing_config">
				<?php  $singleselect_prio = array();

				$singleselect_price_model['0'] = JHTML::_('select.option','1',JHTML::tooltip(JText::_('HOVER_CAMP_BUDGET'), JText::_('CAMMP_BUDGET'), '', JText::_('CAMMP_BUDGET')));
				$singleselect_price_model['1'] = JHTML::_('select.option','0', JHTML::tooltip(JText::_('HOVER_STD_PRICING'), JText::_('STD_CLICK_IMP'), '', JText::_('STD_CLICK_IMP')));


				if($socialads_config['select_campaign'] == 0)
						$select_campaign = 'style="display:none;"';
						else
						$select_campaign = '';

			 ?>


			<fieldset class="form-horizontal"><legend><?php echo JText::_('PRICE_CHANGE');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JText::_('CHOOSE_METHOD');?></label>

						<div id="select_campaign" class="input-append yes_no_toggle">
							<input class="camp_and_new" type="radio" name="config[select_campaign]" id="select_campaign1" value="1" <?php echo ($socialads_config['select_campaign'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['select_campaign']  == 1 )? 'btn-success' :'';?>" for="select_campaign1"><?php echo JText::_('CAMMP_BUDGET');?></label>
							<input class="camp_and_new" type="radio" name="config[select_campaign]" id="select_campaign0" value="0" <?php echo ($socialads_config['select_campaign'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['select_campaign']  == 0 )? 'btn-danger' :'';?>" for="select_campaign0"><?php echo JText::_('STD_CLICK_IMP'); ?></label>
						</div>
						<div style="margin-top:5px" class="controls">
								<div id="migrate_div"  style="<?php echo ($socialads_config['select_campaign']==1 && $ads2_camp)? '' :'display:none'; ?>"  >
									<div id="migrate_btn">
										<div class="alert alert-error"><?php echo JText::_('MIGRATE_NOTICE'); ?></div>
										<input type="button" class="btn btn-danger"  width="50%" onclick="migrateads()" value="<?php echo JText::_('MIGRATE');?>" />
									</div>
									<div class="alert alert-info" id="migration_status" style="display:none">
										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::sprintf('STEP1',$socialads_config['camp_currency_daily'],$socialads_config['currency']);?></span>	</div>
										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::sprintf('STEP2',$socialads_config['currency']);?></span>	</div>
										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::_('STEP3');?></span>		</div>
										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::_('STEP4');?></span>		</div>
									</div>
								</div>
								<div id="loader_image_div" style="display:none">
									 <div class="alert alert-warning"><?php echo JText::_('COM_SA_PLS_WAIT'); ?> </div>
									 <img src="<?php echo JURI::root();?>components/com_socialads/images/loader_light_blue.gif" width='128' height='15' border='0' />
								</div>
								<div id="migrate_div_for_old"  style="<?php echo ($socialads_config['select_campaign']==0 && $ads2_old)? '' :'display:none'; ?>"  >
									<div id="migrate_btn_for_old">
										<div class="alert alert-error"><?php echo JText::_('MIGRATE_NOTICE_FOR_OLD'); ?></div>
										<input type="button" class="btn btn-danger"  width="50%" onclick="migrateads()" value="<?php echo JText::_('MIGRATE_OLD');?>" />
									</div>

									<div class="alert alert-info" id="migration_status_for_old" style="display:none">

										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::_('OLD_STEP1');?></span>	</div>
										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::_('OLD_STEP2');?></span>	</div>
										<div class="completed_old_migrate" ><span class="image_span" ><img class="image"  src="<?php echo JURI::root().'/components/com_socialads/images/confirm.png' ?>" > <?php echo JText::_('OLD_STEP3');?></span>		</div>
									</div>
								</div>
								<div id="migrate_error_div" class="alert alert-warning" style="display:none"  ></div>
						</div>
					</div>


					<div class="control-group camp_price" <?php echo $select_campaign ;  ?>  >
						<label class="control-label"><?php echo JText::_('MIN_PRE_BAL');?></label>
						<div class="controls"><input type="text" id="camp_price_pre" name="config[camp_currency_pre]"  width="50%" value="<?php echo $socialads_config['camp_currency_pre']; ?>" class="inputbox required"  Onkeyup= "checkforalpha(this,46);" maxlength="10" />
						</div>
					</div>

					<div class="control-group camp_price" <?php echo $select_campaign ;  ?>  >
						<label class="control-label"><?php echo JText::_('MIN_DAILY_BYDGET');?></label>
						<div class="controls"><input type="text" id="camp_price_daily" name="config[camp_currency_daily]"  width="50%" value="<?php echo $socialads_config['camp_currency_daily']; ?>" class="inputbox required"  Onkeyup= "checkforalpha(this,46);" maxlength="10" />
						</div>
					</div>

					<div class="control-group">
						<label class="control-label" for="configpricing_opt"><?php echo JHTML::tooltip(JText::_('SELECT_PRICING_TOOLTIP'), JText::_('SELECT_PRICING'), '', JText::_('SELECT_PRICING'));?></label>

							<?php echo JHTML::_('select.genericlist', $singleselect, "config[pricing_opt][]", ' class="inputbox required" multiple="multiple" ', "value", "text", $socialads_config['pricing_opt'] )?>

					</div>
			</fieldset>



				<!--bidding part added-->
				<?php  $singleselect_prio = array();

				//$singleselect_price_rate['0'] = JHTML::_('select.option','1',JText::_('BIDDING'));
				$singleselect_price_rate['0'] = JHTML::_('select.option','0', JText::_('STATIC_BIDDING'));

			 ?>

			<fieldset class="form-horizontal"><legend><?php echo JText::_('PRIC_RATES');?></legend>

				<div class="control-group">
					<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_ENABLE_ZONE_PRICING'), JText::_('ENABLE_ZONE_PRICING'), '', JText::_('ENABLE_ZONE_PRICING'));?></label>
					<div id="zone_pricing" class="input-append yes_no_toggle">
							<input type="radio" name="config[zone_pricing]" id="zone_pricing1" value="1" <?php echo ($socialads_config['zone_pricing'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['zone_pricing']  == 1 )? 'btn-success' :'';?>" for="zone_pricing1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[zone_pricing]" id="zone_pricing0" value="0" <?php echo ($socialads_config['zone_pricing'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['zone_pricing']  == 0 )? 'btn-danger' :'';?>" for="zone_pricing0"><?php echo JText::_('SA_NO'); ?></label>
					</div>
				</div>
				<div class="control-group price1">
					<label class="control-label" for="config[clicks_price]"><?php echo JHTML::tooltip(JText::_('PRICE_DAY'), JText::_('CLICKS_PRICE'), '', JText::_('CLICKS_PRICE'));?></label>
					<div class="controls">
						<input type="text" name="config[clicks_price]" id="config[clicks_price]" width="50%" value="<?php echo $socialads_config['clicks_price']; ?>" class="inputbox required  validate-numeric"  Onkeyup= "checkforalpha(this,46);" maxlength="10" />
					</div>
				</div>
				<div class="control-group price0">
					<label class="control-label" for="config[impr_price]"><?php echo JHTML::tooltip(JText::_('PRICE_IMPR'), JText::_('IMPR_PRICE'), '', JText::_('IMPR_PRICE'));?></label>
						<div class="controls">
							<input type="text" name="config[impr_price]" id="config[impr_price]" width="50%" value="<?php echo $socialads_config['impr_price']; ?>" class="inputbox required validate-numeric" Onkeyup= "checkforalpha(this,46);" maxlength="10" />
					</div>
				</div>
				<div class="control-group price2">
					<label class="control-label" for="config[date_price]" > <?php echo JHTML::tooltip(JText::_('PRICE_TOOLTIP'), JText::_('DATE_PRICE'), '', JText::_('DATE_PRICE'));?></label>
					<div class="controls"><input type="text" name="config[date_price]" id="config[date_price]" width="50%" value="<?php echo $socialads_config['date_price']; ?>" class=" inputbox required validate-numeric" Onkeyup= "checkforalpha(this,46);" maxlength="10" />
					</div>
				</div>
					<!--added by sagar -->
				<div class="control-group price2" id="slab_tr_radio" >
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SHOWSLABS_TOOLTIP'), JText::_('SHOWSLABS'), '', JText::_('SHOWSLABS'));?></label>
						<div id="show_slab" class="input-append yes_no_toggle">
							<input type="radio" name="config[show_slab]" id="show_slab1" value="1" <?php echo ($socialads_config['show_slab'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['show_slab']  == 1 )? 'btn-success' :'';?>" for="show_slab1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[show_slab]" id="show_slab0" value="0" <?php echo ($socialads_config['show_slab'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['show_slab']  == 0 )? 'btn-danger' :'';?>" for="show_slab0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
				</div>
				<div class="control-group slab_tr_hide">
					<label class="control-label"><?php echo JHTML::tooltip(JText::_('SHOWSLABS_PER_DAY_TOOLTIP'), JText::_('SHOWSLABS_PER_DAY'), '', JText::_('SHOWSLABS_PER_DAY'));?></label>
						<div id="show_per_day_opt" class="input-append yes_no_toggle">
							<input type="radio" name="config[show_per_day_opt]" id="show_per_day_opt1" value="1" <?php echo ($socialads_config['show_per_day_opt'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['show_per_day_opt']  == 1 )? 'btn-success' :'';?>" for="show_per_day_opt1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[show_per_day_opt]" id="show_per_day_opt0" value="0" <?php echo ($socialads_config['show_per_day_opt'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['show_per_day_opt']  == 0 )? 'btn-danger' :'';?>" for="show_per_day_opt0"><?php echo JText::_('SA_NO'); ?></label>
						</div>

				</div>
				<div class="control-group slab_tr_hide" id="slab_tr_row" >
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SLABS_TOOLTIP'), JText::_('SLABS'), '', JText::_('SLABS'));?></label>
						<div class="controls">

							<table border="0" width="50" class="adminlist table table-striped" style="width:60px;">
								<tbody>
									<tr>
										<td width="2%" align="center" class="title">
											<?php echo JHTML::tooltip(JText::_('SLABS_TITLE_TOOLTIP'), JText::_('SLABS_TITLE'), '', JText::_('SLABS_TITLE'));?>
										</td>
										<td width="2%" align="center" class="title">
											<?php echo JHTML::tooltip(JText::_('SLABS_DURATION_TOOLTIP'), JText::_('SLABS_DURATION'), '', JText::_('SLABS_DURATION'));?>
										</td>
										<td width="2%" align="center" class="title">
											<?php echo JHTML::tooltip(JText::_('SLABS_PRICE_TOOLTIP'), JText::_('SLABS_PRICE'), '', JText::_('SLABS_PRICE'));?>
										</td>
									</tr>
									<?php  $i = 0;
									foreach ($socialads_config['slab'] as $slab)
									{
									?>
										<tr >
											<td  width=""><input type="text" name="config[slab][<?php echo $i;?>][label]"  width="50%" value="<?php echo $socialads_config['slab'][$i]['label']; ?>" class="inputbox required" maxlength="20" /></td>
											<td class="setting-td" align="center" width="10px">
												<input type="text" name="config[slab][<?php echo $i;?>][duration]" width="50%" value="<?php echo $socialads_config['slab'][$i]['duration']; ?>" class=" inputbox required" maxlength="5" Onkeyup= "checkforalpha(this);"  />
											</td>
											<td class="setting-td">
												<input type="text" name="config[slab][<?php echo $i;?>][price]"  width="50%" value="<?php echo $socialads_config['slab'][$i]['price']; ?>" class=" inputbox validate-name" Onkeyup= "checkforalpha(this,46);" maxlength="10" />

											</td>
										</tr>
									<?php
										$i++;
									}
									?>

								</tbody>
							</table>
						</div>
				</div>

					<!--added by sagar -->
			</fieldset>

			<fieldset class="form-horizontal"> <legend><?php echo JText::_('COM_SOCIALADS_PRIC_THRES');?></legend>
				<div class="control-group">
					<label class="control-label" for="config[charge]"><?php echo JHTML::tooltip(JText::_('MIN_CHARGE'), JText::_('CHARGE'), '', JText::_('CHARGE'));?></label>
					<div class="controls">
						<input type="text" id="charge" name="config[charge]"  width="50%" value="<?php echo $socialads_config['charge']; ?>" class=" inputbox required validate-name" Onkeyup= "checkforalpha(this,46);"  maxlength="10" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="config[balance]"><?php echo JHTML::tooltip(JText::_('LOW_BAL'), JText::_('BALANCE'), '', JText::_('BALANCE'));?></label>
					<div class="controls">
						<input type="text" id="balance" name="config[balance]"  width="50%" value="<?php echo $socialads_config['balance']; ?>" class="required validate-name" Onkeyup= "checkforalpha(this);" maxlength="3" />
					</div>
				</div>
			</fieldset>
			<fieldset class="form-horizontal"><legend><?php echo JText::_('PAYMENT_CONFIG');?></legend>
				<div class="control-group">
					<label class="control-label" for="configgateways"><?php echo JHTML::tooltip(JText::_('COM_SOCIALADS_SELECT_GATEWAY_DES'), JText::_('SELECT_GATEWAY'), '', JText::_('SELECT_GATEWAY'));?></label>

						<?php
					if(!empty($gatewayselect))
						echo JHTML::_('select.genericlist', $gatewayselect, "config[gateways][]", ' multiple size="5" class="required" ', "value", "text", $gateways );
					else{

						echo "<div class='controls'>" .JText::_('NO_GATEWAY_PLUG')."</div>";
					}
					?>


				</div>
				<div class="control-group">
						<label class="control-label" for="config[currency]" ><?php echo JHTML::tooltip(JText::_('CURRENCY'), JText::_('CURRENCY_HEAD'), '', JText::_('CURRENCY'));?></label>
						<div class="controls"><input type="text" id="currency" name="config[currency]"  width="50%" value="<?php echo $socialads_config['currency']; ?>" class="inputbox required validate-charector_only" Onkeyup= "checkfornum(this);" maxlength="4" /></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo JHTML::tooltip(JText::_('ARTICLE_DESC'), JText::_('ENABLE_ARTICLE'), '', JText::_('ENABLE_ARTICLE'));?></label>
					<div id="article" class="input-append yes_no_toggle">
						<input type="radio" name="config[article]" id="article1" value="1" <?php echo ($socialads_config['article'] == 1 )? 'checked="checked"' :'';?> />
						<label class="first btn <?php echo ($socialads_config['article']  == 1 )? 'btn-success' :'';?>" for="article1"><?php echo JText::_('SA_YES');?></label>
						<input type="radio" name="config[article]" id="article0" value="0" <?php echo ($socialads_config['article'] == 0)? 'checked="checked"' :'';?>  />
						<label class="last btn <?php echo ($socialads_config['article']  == 0 )? 'btn-danger' :'';?>" for="article0"><?php echo JText::_('SA_NO'); ?></label>
					</div>
				</div>
				<div class="control-group tncclass" style="display:none;" >
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('T_C_TOOLTIP'), JText::_('SA_T_C'), '', JText::_('T_CID'));?></label>
						<div class="controls"><input type="text" name="config[tnc]" id="config[tnc]" width="50%" value="<?php echo $socialads_config['tnc']; ?>"  Onkeyup="checkforalphabets(this,'article');" /></div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo JHTML::tooltip(JText::_('ENFORE_RECURR_DESC'), JText::_('ENFORE_RECURR'), '', JText::_('ENFORE_RECURR'));?></label>
					<div id="recure_enforce" class="input-append yes_no_toggle">
						<input type="radio" name="config[recure_enforce]" id="recure_enforce1" value="1" <?php echo ($socialads_config['recure_enforce'] == 1 )? 'checked="checked"' :'';?> />
						<label class="first btn <?php echo ($socialads_config['recure_enforce']  == 1 )? 'btn-success' :'';?>" for="recure_enforce1"><?php echo JText::_('SA_YES');?></label>
						<input type="radio" name="config[recure_enforce]" id="recure_enforce0" value="0" <?php echo ($socialads_config['recure_enforce'] == 0)? 'checked="checked"' :'';?>  />
						<label class="last btn <?php echo ($socialads_config['recure_enforce']  == 0 )? 'btn-danger' :'';?>" for="recure_enforce0"><?php echo JText::_('SA_NO'); ?></label>
					</div>
				</div>
			</fieldset>
		</div>
			<div class="tab-pane" id="ad_specific_config">
				<fieldset class="form-horizontal"><legend><?php echo JText::_('ADS_SET');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_SELECT_AD_TYPE'), JText::_('SELECT_AD_TYPE'), '', JText::_('SELECT_AD_TYPE'));?></label>
						<?php echo JHTML::_('select.genericlist', $singleselect_ad, "config[ad_type_allow][]", ' multiple size="3"  ', "value", "text", $socialads_config['ad_type_allow'] )?>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('AD_SITE_HEAD'), JText::_('AD_SITE'), '', JText::_('AD_SITE'));?></label>
						<div id="ad_site" class="input-append yes_no_toggle">
							<input type="radio" name="config[ad_site]" id="ad_site1" value="1" <?php echo ($socialads_config['ad_site'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['ad_site']  == 1 )? 'btn-success' :'';?>" for="ad_site1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[ad_site]" id="ad_site0" value="0" <?php echo ($socialads_config['ad_site'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['ad_site']  == 0 )? 'btn-danger' :'';?>" for="ad_site0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('OWN_ADS_DESC'), JText::_('OWN_ADS'), '', JText::_('OWN_ADS'));?></label>
						<div id="own_ad" class="input-append yes_no_toggle">
							<input type="radio" name="config[own_ad]" id="own_ad1" value="1" <?php echo ($socialads_config['own_ad'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['own_ad']  == 1 )? 'btn-success' :'';?>" for="own_ad1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[own_ad]" id="own_ad0" value="0" <?php echo ($socialads_config['own_ad'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['own_ad']  == 0 )? 'btn-danger' :'';?>" for="own_ad0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="config[sa_reg_show]"><?php echo JHTML::tooltip(JText::_('DESC_SA_REGISTRATION_SHOW'), JText::_('SA_REGISTRATION_SHOW'), '', JText::_('SA_REGISTRATION_SHOW'));?></label>
						<div id="sa_reg_show" class="input-append yes_no_toggle">
							<input type="radio" name="config[sa_reg_show]" id="sa_reg_show1" value="1" <?php echo ($display_reg == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($display_reg == 1 )? 'btn-success' :'';?>" for="sa_reg_show1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[sa_reg_show]" id="sa_reg_show0" value="0" <?php echo ($display_reg == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($display_reg == 0 )? 'btn-danger' :'';?>" for="sa_reg_show0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_FRM_LINK'), JText::_('FRM_LINK'), '', JText::_('FRM_LINK'));?></label>
						<div id="frm_link" class="input-append yes_no_toggle">
							<input type="radio" name="config[frm_link]" id="frm_link1" value="1" <?php echo ($socialads_config['frm_link'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['frm_link']  == 1 )? 'btn-success' :'';?>" for="frm_link1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[frm_link]" id="frm_link0" value="0" <?php echo ($socialads_config['frm_link'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['frm_link']  == 0 )? 'btn-danger' :'';?>" for="frm_link0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_DISPLAY_REACH'), JText::_('DISPLAY_REACH'), '', JText::_('DISPLAY_REACH'));?></label>
						<div id="display_reach" class="input-append yes_no_toggle">
							<input type="radio" name="config[display_reach]" id="display_reach1" value="1" <?php echo ($display_reach == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($display_reach == 1 )? 'btn-success' :'';?>" for="display_reach1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[display_reach]" id="display_reach0" value="0" <?php echo ($display_reach == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($display_reach == 0 )? 'btn-danger' :'';?>" for="display_reach0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
						<!--added by sagar -->
					<div class="control-group">
						<label for="config[estimated_reach]" class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_ESTIMATED_REACH'), JText::_('ESTIMATED_REACH'), '', JText::_('ESTIMATED_REACH'));?></label>
						<div class="controls">
							<input type="text" id="estimated_reach" name="config[estimated_reach]"  width="50%" value="<?php echo $socialads_config['estimated_reach']; ?>" class="inputbox required validate" Onkeyup= "checkforalpha(this);"   maxlength="5" />
						</div>
					</div>
				</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('MEDIA_SET');?></legend>
					<div class="control-group">
						<label for="config[image_size]" class="control-label"><?php echo JHTML::tooltip(JText::_('IMAGE_SIZE_TOOLTIP'),JText::_('IMAGE_SIZE_HEAD'), '', JText::_('IMAGE_SIZE')); ?></label>
						<div class="controls">
							<input type="text" id="image_size" name="config[image_size]"  width="50%" value="<?php echo $socialads_config['image_size']; ?>" class="inputbox required validate-name" Onkeyup= "checkforalpha(this);" maxlength="10" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('FLASH_ADS_DESC'), JText::_('FLASH_ADS'), '', JText::_('FLASH_ADS'));?></label>
						<div id="allow_flash_ads" class="input-append yes_no_toggle">
							<input type="radio" name="config[allow_flash_ads]" id="allow_flash_ads1" value="1" <?php echo ($socialads_config['allow_flash_ads'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['allow_flash_ads']  == 1 )? 'btn-success' :'';?>" for="allow_flash_ads1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[allow_flash_ads]" id="allow_flash_ads0" value="0" <?php echo ($socialads_config['allow_flash_ads'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['allow_flash_ads']  == 0 )? 'btn-danger' :'';?>" for="allow_flash_ads0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('VIDEO_ADS_DESC'), JText::_('VIDEO_ADS'), '', JText::_('VIDEO_ADS'));?></label>
						<div id="allow_vid_ads" class="input-append yes_no_toggle">
							<input type="radio" name="config[allow_vid_ads]" id="allow_vid_ads1" value="1" <?php echo ($socialads_config['allow_vid_ads'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['allow_vid_ads']  == 1 )? 'btn-success' :'';?>" for="allow_vid_ads1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[allow_vid_ads]" id="allow_vid_ads0" value="0" <?php echo ($socialads_config['allow_vid_ads'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['allow_vid_ads']  == 0 )? 'btn-danger' :'';?>" for="allow_vid_ads0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>


					<div class="control-group autoplay_video" style=" <?php echo ($socialads_config['allow_vid_ads'] == 1 )? 'display:block' :'display:none';?>">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('VIDEO_ADS_DESC_AUTOPLAY'), JText::_('VIDEO_ADS_AUTOPLAY'), '', JText::_('VIDEO_ADS_AUTOPLAY'));?></label>
						<div id="allow_vid_ads_autoplay" class="input-append yes_no_toggle">
							<input type="radio" name="config[allow_vid_ads_autoplay]" id="allow_vid_ads_autoplay1" value="1" <?php echo ($socialads_config['allow_vid_ads_autoplay'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['allow_vid_ads_autoplay']  == 1 )? 'btn-success' :'';?>" for="allow_vid_ads_autoplay1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[allow_vid_ads_autoplay]" id="allow_vid_ads_autoplay0" value="0" <?php echo ($socialads_config['allow_vid_ads_autoplay'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['allow_vid_ads_autoplay']  == 0 )? 'btn-danger' :'';?>" for="allow_vid_ads_autoplay0"><?php echo JText::_('SA_NO'); ?></label>
						</div>

					</div>
				</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_SOCIALADS_MODERAT_FRAUD');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('ADMIN_TEXT'), JText::_('ADMIN_HEAD'), '', JText::_('APPROVAL'));?></label>
						<div id="approval" class="input-append yes_no_toggle">
							<input type="radio" name="config[approval]" id="approval1" value="1" <?php echo ($socialads_config['approval'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['approval'] == 1 )? 'btn-success' :'';?>" for="approval1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[approval]" id="approval0" value="0" <?php echo ($socialads_config['approval'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['approval'] == 0 )? 'btn-danger' :'';?>" for="approval0"><?php echo JText::_('SA_NO'); ?></label>
						</div>

					</div>
					<!--
					<div class="control-group">
						<label class="control-label"><?php //echo JHTML::tooltip(JText::_('ALL_APPR_DESC'), JText::_('ALL_APPR'), '', JText::_('ALL_APPR'));?></label>
						<div id="all_approval" class="input-append yes_no_toggle">
							<input type="radio" name="config[all_approval]" id="all_approval1" value="1" <?php //echo ($socialads_config['all_approval'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php //echo ($socialads_config['all_approval'] == 1 )? 'btn-success' :'';?>" for="all_approval1"><?php //echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[all_approval]" id="all_approval0" value="0" <?php //echo ($socialads_config['all_approval'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php //echo ($socialads_config['all_approval'] == 0 )? 'btn-danger' :'';?>" for="all_approval0"><?php //echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					-->
					<div class="control-group">
						<label class="control-label" for="config[timeimpressions]" ><?php echo JHTML::tooltip(JText::_('TIME_INTERVAL_IMPR'), JText::_('TIME_INTERVAL_IMPR_HEAD'), '', JText::_('TIME_IMPRESSIONS')); ?></label>
						<div class="controls">
							<input type="text" id="timeimpressions" name="config[timeimpressions]" width="50%" value="<?php echo $socialads_config['timeimpressions']; ?>" class=" inputbox required validate-name" Onkeyup= "checkforalpha(this);" maxlength="5" />
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="config[timeclicks]" ><?php echo JHTML::tooltip(JText::_('TIME_INTERVAL'), JText::_('TIME_INTERVAL_HEAD'), '', JText::_('TIME_CLICKS'));?></label>
						<div class="controls">
							<input type="text" id="timeclicks" name="config[timeclicks]"  width="50%" value="<?php echo $socialads_config['timeclicks']; ?>" class="inputbox required validate-name" Onkeyup= "checkforalpha(this);" maxlength="5" />
						</div>
					</div>
				</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_SA_SOCIAL_ENGAGEMENT');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('AD_IGNORE'), JText::_('AD_IGNORE_HEAD'), '', JText::_('IGNORE'));?></label>
						<div id="ignore" class="input-append yes_no_toggle">
							<input type="radio" name="config[ignore]" id="ignore1" value="1" <?php echo ($socialads_config['ignore'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['ignore']  == 1 )? 'btn-success' :'';?>" for="ignore1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[ignore]" id="ignore0" value="0" <?php echo ($socialads_config['ignore'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['ignore']  == 0 )? 'btn-danger' :'';?>" for="ignore0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('USER_FEEDBACK'), JText::_('USER_FEEDBACK_HEAD'), '', JText::_('FEEDBACK'));?></label>
						<div id="feedback" class="input-append yes_no_toggle">
							<input type="radio" name="config[feedback]" id="feedback1" value="1" <?php echo ($socialads_config['feedback'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['feedback']  == 1 )? 'btn-success' :'';?>" for="feedback1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[feedback]" id="feedback0" value="0" <?php echo ($socialads_config['feedback'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['feedback']  == 0 )? 'btn-danger' :'';?>" for="feedback0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('AD_IGNORE_AFF'), JText::_('AD_IGNORE_HEAD_AFF'), '', JText::_('IGNORE_AFF'));?></label>
						<div id="ignore_affiliate" class="input-append yes_no_toggle">
							<input type="radio" name="config[ignore_affiliate]" id="ignore_affiliate1" value="1" <?php echo ($socialads_config['ignore_affiliate'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['ignore_affiliate']  == 1 )? 'btn-success' :'';?>" for="ignore_affiliate1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[ignore_affiliate]" id="ignore_affiliate0" value="0" <?php echo ($socialads_config['ignore_affiliate'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['ignore_affiliate']  == 0 )? 'btn-danger' :'';?>" for="ignore_affiliate0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('COM_SA_ENABLE_JBOLO'), JText::_('COM_SA_ENABLE_JBOLO'), '', JText::_('COM_SA_ENABLE_JBOLO'));?></label>
						<div id="se_jbolo" class="input-append yes_no_toggle">
							<input type="radio" name="config[se_jbolo]" id="se_jbolo1" value="1" <?php echo ($socialads_config['se_jbolo'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['se_jbolo']  == 1 )? 'btn-success' :'';?>" for="se_jbolo1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[se_jbolo]" id="se_jbolo0" value="0" <?php echo ($socialads_config['se_jbolo'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['se_jbolo']  == 0 )? 'btn-danger' :'';?>" for="se_jbolo0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('COM_SA_ENABLE_ADDTHIS'), JText::_('COM_SA_ENABLE_ADDTHIS'), '', JText::_('COM_SA_ENABLE_ADDTHIS'));?></label>
						<div id="se_addthis" class="input-append yes_no_toggle">
							<input type="radio" name="config[se_addthis]" id="se_addthis1" value="1" <?php echo ($socialads_config['se_addthis'] == 1 )? 'checked="checked"' :'';?> onclick="togglestate(this,'addthis_pub_class');" />
							<label class="first btn <?php echo ($socialads_config['se_addthis']  == 1 )? 'btn-success' :'';?>" for="se_addthis1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[se_addthis]" id="se_addthis0" value="0" <?php echo ($socialads_config['se_addthis'] == 0)? 'checked="checked"' :'';?>  onclick="togglestate(this,'addthis_pub_class');" />
							<label class="last btn <?php echo ($socialads_config['se_addthis']  == 0 )? 'btn-danger' :'';?>" for="se_addthis0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group" id="addthis_pub_class" style="display:none;" >
						<label class="control-label" for="config[sa_addthis_pub]" ><?php echo JHTML::tooltip(JText::_('COM_SA_ENABLE_ADDTHIS_PUBLISHED_ID'), JText::_('COM_SA_ENABLE_ADDTHIS_PUBLISHED_ID_HEAD'), '', JText::_('COM_SA_ENABLE_ADDTHIS_PUBLISHED_ID'));?></label>
						<div class="controls">
							<input type="text" id="sa_addthis_pub" name="config[sa_addthis_pub]"  width="50%" value="<?php echo $socialads_config['sa_addthis_pub']; ?>" class="inputbox" />
						</div>
					</div>
				</fieldset>

			</div><!-- ad_specific_config -->
			<div  class="tab-pane" id="ad_other_config">

				<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_SOCIALADS_CACHE_SETINGS');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SA_ENABLE_CACHING_DESC'), JText::_('SA_ENABLE_CACHING'), '', JText::_('SA_ENABLE_CACHING'));?></label>
						<div id="enable_caching" class="input-append yes_no_toggle">
							<input type="radio" name="config[enable_caching]" id="enable_caching1" value="1" <?php echo ($socialads_config['enable_caching'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['enable_caching'] == 1 )? 'btn-success' :'';?>" for="enable_caching1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[enable_caching]" id="enable_caching0" value="0" <?php echo ($socialads_config['enable_caching'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['enable_caching'] == 0 )? 'btn-danger' :'';?>" for="enable_caching0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">

						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SA_CACHE_TIME_DESC'), JText::_('SA_CACHE_TIME'), '', JText::_('SA_CACHE_TIME'));?></label>
						<div class="controls">
							<input type="text" id="cache_time" name="config[cache_time]"  width="50%" value="<?php echo $socialads_config['cache_time']; ?>" class="inputbox required validate-name" Onkeyup= "checkforalpha(this);" maxlength="5" />
						</div>
					</div>
				</fieldset>

				<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_SOCIALADS_SYS_SETINGS');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SA_LOAD_BOOTSTRAP_DESC'), JText::_('SA_LOAD_BOOTSTRAP'), '', JText::_('SA_LOAD_BOOTSTRAP'));?></label>
						<div id="load_bootstrap" class="input-append yes_no_toggle">
							<input type="radio" name="config[load_bootstrap]" id="load_bootstrap1" value="1" <?php echo ($socialads_config['load_bootstrap'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['load_bootstrap'] == 1 )? 'btn-success' :'';?>" for="load_bootstrap1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[load_bootstrap]" id="load_bootstrap0" value="0" <?php echo ($socialads_config['load_bootstrap'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['load_bootstrap'] == 0 )? 'btn-danger' :'';?>" for="load_bootstrap0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('SA_LOAD_JQUI_DESC'), JText::_('SA_LOAD_JQUI'), '', JText::_('SA_LOAD_JQUI'));?></label>
						<div id="load_jqui" class="input-append yes_no_toggle">
							<input type="radio" name="config[load_jqui]" id="load_jqui1" value="1" <?php echo ($socialads_config['load_jqui'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['load_jqui'] == 1 )? 'btn-success' :'';?>" for="load_jqui1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[load_jqui]" id="load_jqui0" value="0" <?php echo ($socialads_config['load_jqui'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['load_jqui'] == 0 )? 'btn-danger' :'';?>" for="load_jqui0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
				</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_SOCIALADS_CRONJOBS_SETINGS');?></legend>
					<div class="control-group">
						<label class="control-label" for="config[estimated_reach]" ><?php echo JHTML::tooltip(JText::_('DESC_CRON_KEY'), JText::_('CRON_KEY'), '', JText::_('CRON_KEY'));?></label>
						<div class="controls"><input type="text" id="cron_key" name="config[cron_key]"  width="50%" value="<?php if(!$socialads_config['cron_key']) echo 'a1z19'; else echo $socialads_config['cron_key']; ?>" class="inputbox required validate" /></div>
					</div>
				<?php  if(!$socialads_config['cron_key']) $cron_key='a1z19'; else $cron_key= $socialads_config['cron_key']; ?>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_SIN_CRON_URL'), JText::_('SIN_CRON_URL'), '', JText::_('SIN_CRON_URL'));?></label>
						<div class="controls"><?php echo JRoute::_(JURI::root().'index.php?option=com_socialads&tmpl=component&task=sa_allfunc_cron&pkey='.$cron_key)?></div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_CLEAN_IMAGES_CRON'), JText::_('CLEAN_IMAGES_CRON'), '', JText::_('CLEAN_IMAGES_CRON'));?></label>
						<div class="controls"><?php echo JRoute::_(JURI::root().'index.php?option=com_socialads&tmpl=component&task=removeimages&pkey='.$cron_key);?></div>
					</div>
				</fieldset>
				<fieldset class="form-horizontal"><legend><?php echo JText::_('COM_SOCIALADS_STATS_CONFG');?></legend>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('ARCH_STATS_DESC'), JText::_('ARCH_STATS'), '', JText::_('ARCH_STATS'));?></label>
						<div id="arch_stats" class="input-append yes_no_toggle">
							<input type="radio" name="config[arch_stats]" id="arch_stats1" value="1" <?php echo ($socialads_config['arch_stats'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['arch_stats']  == 1 )? 'btn-success' :'';?>" for="arch_stats1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[arch_stats]" id="arch_stats0" value="0" <?php echo ($socialads_config['arch_stats'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['arch_stats']  == 0 )? 'btn-danger' :'';?>" for="arch_stats0"><?php echo JText::_('SA_NO'); ?></label>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('ARCH_DAYS_STATS_DESC'), JText::_('ARCH_DAYS_STATS'), '', JText::_('ARCH_DAYS_STATS'));?>
						</label>
						<div class="controls">
							<input type="text" id="" name="config[arch_stats_day]"  width="50%" value="<?php echo $socialads_config['arch_stats_day']; ?>" class="inputbox required validate-name" Onkeyup= "checkforalpha(this);" maxlength="5" /><?php echo JText::_('SA_DAYS'); ?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('WEEK_STATS_DESC'), JText::_('WEEK_STATS'), '', JText::_('WEEK_STATS'));?></label>
						<div id="week_mail" class="input-append yes_no_toggle">
							<input type="radio" name="config[week_mail]" id="week_mail1" value="1" <?php echo ($socialads_config['week_mail'] == 1 )? 'checked="checked"' :'';?> />
							<label class="first btn <?php echo ($socialads_config['week_mail'] == 1 )? 'btn-success' :'';?>" for="week_mail1"><?php echo JText::_('SA_YES');?></label>
							<input type="radio" name="config[week_mail]" id="week_mail0" value="0" <?php echo ($socialads_config['week_mail'] == 0)? 'checked="checked"' :'';?>  />
							<label class="last btn <?php echo ($socialads_config['week_mail'] == 0 )? 'btn-danger' :'';?>" for="week_mail0"><?php echo JText::_('SA_NO'); ?></label>
						</div>

					</div>
					<div class="control-group">
						<label class="control-label"><?php echo JHTML::tooltip(JText::_('DESC_INTRO_MSG'), JText::_('INTRO_MSG'), '', JText::_('INTRO_MSG'));?></label>
						<div class="controls">
							<textarea name="config[intro_msg]" id="config[intro_msg]" rows="5" cols="30" class="inputbox required"><?php echo trim($socialads_config['intro_msg']); ?></textarea>
						</div>
					</div>
				</fieldset>

			</div><!--other-->
		</div><!--tab-content-->
	</div><!--tabbable-->
	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="settings" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div><!-- techjoomla-bootstrap -->
