<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.html.parameter' );
jimport('joomla.filesystem.file');

JHtml::_('behavior.formvalidation');
JHtmlBehavior::framework();
JHTML::_('behavior.modal');
//JHtml::_('behavior.modal', 'a.modal');
JHTML::_('behavior.tooltip');


// vm:
global $mainframe;
$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();

$root_url	=	JURI::root();
$document->addScript($root_url.'components/com_socialads/js/fuelux2.3loader.min.js');
$document->addStyleSheet($root_url.'components/com_socialads/css/fuelux2.3.1.css');
$document->addStyleSheet($root_url.'components/com_socialads/css/sa_steps.css');
$document->addScript($root_url.'components/com_socialads/js/steps_backend.js');
$document->addScript($root_url.'components/com_socialads/js/buildad.js');
$document->addScript($root_url.'components/com_socialads/js/flowplayer-3.2.9.min.js');//added by manoj stable 2.7.5

if(file_exists(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."language".DS."default_language".DS."language.php"))
{


	global $_CB_framework, $_CB_database, $ueConfig, $mainframe;

	if (defined( 'JPATH_ADMINISTRATOR' ) )
	{

	if ( ! file_exists( JPATH_ADMINISTRATOR .'/components/com_comprofiler/plugin.foundation.php' ) )
	{
		echo 'CB not installed';
		return;
	}
	include_once( JPATH_ADMINISTRATOR .'/components/com_comprofiler/plugin.foundation.php' );

	}
	else
	{

	if (! file_exists( $mainframe->getCfg( 'absolute_path' ).'/administrator/components/com_comprofiler/plugin.foundation.php' ) )
	{
		echo 'CB not installed';
		return;
	}

	include_once( $mainframe->getCfg( 'absolute_path' ).'/administrator/components/com_comprofiler/plugin.foundation.php' );
	}
	require(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."language".DS."default_language".DS."language.php");

}




require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
$this->socialads_config=$socialads_config;
if($this->socialads_config['integration']== 1)
{
	if( file_exists(JPATH_ROOT.DS.'components'.DS.'com_community') ){
		/*load language file for plugin frontend*/
		$lang=JFactory::getLanguage();
		$lang->load('com_community',JPATH_SITE);
	}
}
$socialadshelper = new socialadshelper();
$init_balance = $socialadshelper->getbalance();
if($init_balance!=NULL && $init_balance !=1.00)    // HARDCODED FOR NOW.......
{
	$itemid	= $socialadshelper->getSocialadsItemid('payment');
	$not_msg	= JText::_('MIM_BALANCE');
	$not_msg	= str_replace('{clk_pay_link}','<a href="'.JRoute::_('index.php?option=com_socialads&view=payment&Itemid='.$itemid).'">'.JText::_('SA_CLKHERE').'</a>', $not_msg);
	JError::raiseNotice( 100, $not_msg );
}


$this->EST_HEAD=JText::_('ESTIMATED_REACH_HEAD');
$this->EST_END=JText::_('ESTIMATED_REACH_END');
$this->target_div=0;
if($this->socialads_config['geo_target']==1 or $this->socialads_config['context_target']==1)
{
	$this->target_div=1;
}

$this->input=JFactory::getApplication()->input;
$option = $this->input->get('option');
$sitename = $mainframe->getCfg('sitename');
$user = JFactory::getUser();
$ssession = JFactory::getSession();
$buildadsession = JFactory::getSession();
?>
<script type="text/javascript">
   var selected_pricing_mode= "<?php echo $this->socialads_config['select_campaign']; ?>";
</script>
<?php
$this->geodbfile = JPATH_SITE.DS."components".DS."com_socialads".DS."geo".DS."GeoLiteCity.dat";
if($this->socialads_config['geo_target'] && file_exists($this->geodbfile)) {
   ?>
	<!-- geo target start here -->
	<script src="<?php echo $root_url.'media/techjoomla_strapper/js/akeebajqui.js'?>"></script>
	<script src="<?php echo $root_url.'components/com_socialads/js/geo/geo.js'?>"></script>
	<link rel="stylesheet" href="<?php echo $root_url.'components/com_socialads/css/geo/geo.css' ?>">
	<!-- geo target end here -->
<?php
}
?>

<?php
//check whether user loggd in or not
if (!$user->id)
{
      ?>
   <div class="alert alert-block">
      <?php echo JText::_('BUILD_LOGIN'); ?>
   </div>
</div>
<!--techjoomla bootratp ends if not logged in-->
	<?php
	   if($this->socialads_config['sa_reg_show'])
	   {
		$itemid = $this->input->get('Itemid',0,'INT');
		$ssession->set('socialadsbackurl', $_SERVER["REQUEST_URI"]);
		$mainframe->redirect(JRoute::_('index.php?option=com_socialads&view=registration&Itemid='.$itemid,false));
	   }
	   return false;
 }

   //variable used for edit ad form adsumary page

if($this->ad_id)
{
	$this->edit_ad_adsumary=$this->ad_id;
}
else
{
	$this->edit_ad_adsumary=0;
}
$pluginlist = $buildadsession->get('addatapluginlist');
$upimg = $buildadsession->get('upimg');
$ad_image = $buildadsession->get('ad_image');
if($ad_image == '')
{
	$ad_image = $buildadsession->get('upimgcopy');
}

$juri = JURI::base();
$pos = strpos($ad_image, $juri);



if (isset($user->groups['8']) || isset($user->groups['7']) || isset($user->groups['Super Users']) || isset($user->groups['Administrator']) || $user->usertype == "Super Users" || isset($user->groups['Super Users']) || isset($user->groups['Administrator']) || $user->usertype == "Super Administrator" || $user->usertype == "Administrator" )
{
	$this->special_access = 1;
}
else
{
	$this->special_access = 0;
}
$this->multicheck=$this->datelow=$this->datehigh='';
$ad_zone_id	= $ad_type	=	$layout = $disabled = $display_reach = $display_reach_fun = '';
$ad_type="text_img";


$showBilltab = 1;
if(!empty($this->userbill))
{
	$showBilltab = 0;
}
if($this->ad_data)
{
   $ad_type	= $buildadsession->get('adtype');
   $ad_zone_id	= $buildadsession->get('adzone');
   $layout		= $buildadsession->get('layout');
}
else if($this->input->get('adtype') || $this->input->get('adzone') )
{
   $ad_type_val	= $this->input->get('adtype');
   $ad_type_val = str_replace("||",",",$ad_type_val);
   $ad_type_val = str_replace("|","",$ad_type_val);
   $ad_type_arry = explode (",",$ad_type_val);
   $ad_type = $ad_type_arry[0];
   $ad_zone_id	= $this->input->get('adzone');
   if ($this->socialads_config['frm_link'])
   {
		$disabled = 'disabled';
   }
}
if($this->socialads_config['display_reach'])
{
	$display_reach=' onchange=" calculatereach() "';
	$display_reach_fun="'onchange'=>'calculatereach()'";
}
if($this->edit_ad_adsumary)
{
	if($this->addata_for_adsumary_edit->ad_affiliate == '1'){
		$ad_type = 'affiliate';
	}else{
		$this->zone->ad_type = str_replace('||',',',$this->zone->ad_type);
		$this->zone->ad_type = str_replace('|','',$this->zone->ad_type);
		$ad_type1 = explode(",",$this->zone->ad_type);
		$ad_type = $ad_type1[0];
	}
}



   $js='



	function chkAdValid(stepId,unlimited_ad)
   {
		if(jQuery("#upload_area").find("div").children("[name=upimg]").val() != null)
			jQuery("#upimg").val(jQuery("#upload_area").find("div").children("[name=upimg]").val());
		//url validation starts here

	   if(document.getElementById("adtype").value == "text_img" || document.getElementById("adtype").value == "text" || document.getElementById("adtype").value == "img")
	   {
		var theurl=document.adsform.url2.value;
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

	   if(document.getElementById("adtype").value == "img" || document.getElementById("adtype").value == "text_img")
	   {
		if(document.getElementById("ad_image").value == "" && document.getElementById("upimg").value == "")
		{
				alert("'.JText::_("IMG_VALID").'");
				return false;
		}
	   }

		if(stepId=="ad-pricing" && unlimited_ad==0)
		{
			if(!checkreview(\''.$socialads_config['camp_currency_daily'].'\',\''.$socialads_config['mim_bid_value'].'\',\''.$socialads_config['select_campaign'].'\',\''.$socialads_config['charge'].'\',\''.JText::sprintf('COM_SOCIALADS_MORE_MINCHARGE',$socialads_config['charge'],$socialads_config['currency']).'\',\''.$socialads_config['currency'].'\',\''.JText::_('COM_SOCIALADS_VALID_DATE',true).'\',\''.JText::_('DATE_VALID1',true).'\',\''.JText::_('WRONGDATES',true).'\',\''.JText::_('INVALID',true).'\',\''.JText::_('CHKCONTEXTUAL',true).'\'))
				return false;
		}

		// for billing tab
		if(stepId=="ad-billing" && techjoomla.jQuery("#sa_BillForm").length)
		{
			var  sa_BillForm = document.sa_BillForm;
			if (document.formvalidator.isValid(sa_BillForm))
			{
				//return true;
			}
			else
			{
				return false;
			}
		}

		return true;
   }

   ';

   $js_key="
   function checkforalpha(el)
   {
   	var i =0 ;
   	for(i=0;i<el.value.length;i++){
   	  if((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123)) { alert('Please Enter Numerics'); el.value = el.value.substring(0,i); break;}

   	}
   }


   function calculatereach()
   {
   	var targetfields=jQuery('.ad-fields-inputbox').serializeArray();
   	//console.log(targetfields);
   	var estimated_reach=jQuery('#config_estimated_reach').val();

   	jQuery.ajax({
   	    type: 'POST',
   	    url: '?option=com_socialads&controller=buildad&task=calculatereach',
   	    data:  targetfields,
   	    dataType: 'json',
   	    success: function(data) {
   						if(data==null)
   						return;
   						var totalreach=''
   						var reach=parseInt(estimated_reach)+parseInt(data.reach);
           			if(parseInt(reach)==0)
   						totalreach=0
   						else
   						totalreach=reach
   				jQuery('#estimated_reach').html('".$this->EST_HEAD."'+totalreach+'".$this->EST_END."');
   	    }
   	});

   }
   ";

   $js_list="


   function selectapplist()
   {
    var plglist = document.getElementById('addatapluginlist').value;

   	var selectedlist = document.getElementById('addatapluginlist').value = '".$pluginlist."';

   	document.getElementById('destination_url').style.display='none';
   	document.getElementById('promotplugin').style.display='block';

   }
   ";

   $js_url="
	function sa_applycoupon(ck_val){
		if(jQuery('#sa_coupon_chk').is(':checked'))
		{
			if(jQuery('#totalamount').val() =='')
			{
				alert(\"".JText::_('SA_BUILDAD_TOTAL_SHOULDBE_VALID_VALUE')."\");

			}
			else if(jQuery('#sa_coupon_code').val() =='')
			{
				if(ck_val == 1)
					alert('".JText::_('ENTER_COP_COD')."');
				// commented BZ called on refresh
			}
			else
			{

				jQuery.ajax({
					url: '".$root_url."?option=com_socialads&task=sa_applycoupon&coupon_code='+document.getElementById('sa_coupon_code').value,
					type: 'GET',
					dataType: 'json',
					success: function(data) {
					amt=0;
					val=0;
					console.log(data);
					if(data != 0)
					{
						var subtotal = jQuery('#totalamount').val();
						if(data[0].val_type == 1)
								val = (data[0].value/100)* subtotal;
						else
								val = data[0].value;

						amt = round(subtotal- val);

						if(amt <= 0)
							amt=0;

							jQuery('.sa_cop_details').show();
							jQuery('#sa_cop_price').html( amt +'&nbsp;'+'".$socialads_config["currency"]."');

							jQuery('#sa_cop_afterprice').html( val +'&nbsp;'+'".$socialads_config["currency"]."')
							jQuery('#dis_cop').html( val+'&nbsp;'+'".$socialads_config["currency"]."');

							//jQuery('#dis_amt').show();


					}
					else
						alert('\"'+document.getElementById('sa_coupon_code').value +'\" ".JText::_('COP_EXISTS')."');


					}
				});

			}
		}
		}
   ";

   ?>
<script type="text/javascript">

	function getUserCampaign(userid)
	{
		jQuery.ajax({
			url:base_url+'?option=com_socialads&controller=campaign&task=getUserCampaign&userid='+userid+'&tmpl=component&format=raw',
			type:'POST',
			dataType:'json',
			success: function(data)
			{
				var op="<option value=''><?php echo JText::_('COM_SA_SELECT_CAMPAIGN') ?></option>";
				select=techjoomla.jQuery('#camp');
				select.find('option').remove().end();
				select.append(op);

				var option, index;

				for(index = 0; index < data.length; ++index)
				{
					var option=data[index];
					var op="<option value="  +option['camp_id']+  ">"  +option['campaign']+   '</option>'     ;
					techjoomla.jQuery('#camp').append(op);
				}
				jQuery("select").trigger("liszt:updated");
			},
			error: function()
			{
				console.log("Someting wen't wrong");
			}
		});
	}
</script>
<?php

   $document->addScriptDeclaration($js);
   $document->addScriptDeclaration($js_key);
   $document->addScriptDeclaration($js_list);
   $document->addScriptDeclaration($js_url);
   //javascript declaration ends here
   	$singleselect = array();
   if($this->Check_default_zone)
   {

		foreach($this->Check_default_zone as $published_zone)
		{
			if($published_zone=='text_img')
			{
				if(in_array('text_img',$this->socialads_config['ad_type_allow']))
				$singleselect[] = JHTML::_('select.option','text_img',  JText::_('AD_TYP_TXT_IMG'));

			}
			if($published_zone=='text')
			{
				if(in_array('text',$this->socialads_config['ad_type_allow']))
				$singleselect[] = JHTML::_('select.option','text',  JText::_('AD_TYP_TXT'));
			}
			if($published_zone=='img')
			{
				if(in_array('img',$this->socialads_config['ad_type_allow']))
				$singleselect[] = JHTML::_('select.option','img',  JText::_('AD_TYP_IMG'));
			}
			if($published_zone=='affiliate' && $this->special_access)
			{
				//if(in_array('img',$this->socialads_config['ad_type_allow']))
				$singleselect[] = JHTML::_('select.option','affiliate', JText::_('AD_TYP_AFFI'));
			}

		}

   }
   else
   {
   	if(in_array('text_img',$this->socialads_config['ad_type_allow']))
   	$singleselect[] = JHTML::_('select.option','text_img', 'Text & Image');
   }
   //	print_r($singleselect);die;
   /*
   if($this->special_access) {

   $singleselect[] = JHTML::_('select.option','affiliate', JText::_('AD_TYP_AFFI'));
   }
   */
   $this->assignRef('singleselect', $singleselect);

   $flds = array();
	if(isset($this->ad_fields))
	{
		foreach($this->ad_fields as $addfields)
		{
			foreach($addfields as $k=>$ad)
			{
				if($k && $ad)
				{
					if(!isset($flds[$k]))
					{
						$flds[$k]='';
					}
					if($flds[$k]==null)
					{
						$flds[$k] = $ad;
					}
					else
					{
						if(is_array($flds[$k]))
						{
							array_push($flds[$k],$ad);
						}
						else
						{
							$temp=$flds[$k];
							$flds[$k]=array();
							array_push($flds[$k],$temp);
							array_push($flds[$k],$ad);
						}
					}
				}
			}
		}
	}



   ?>

<!--techjoomla-bootstrap -->
<div class="techjoomla-bootstrap">

	<!--row-fluid-->
	<div class="row-fluid">

			<!-- main div starts here-->
			<div class="ad-form">
<!--
			<form method="post" name="myForm" id="adsform" enctype="multipart/form-data"
			class="form-horizontal form-validate" onsubmit="return validateForm();">  -->

			        <!--fuelux wizard-example -->
					<div class="fuelux wizard-example">

					<div class="sa_steps_parent row-fluid">
						<!--wizard-->
						<div id="MyWizard" class="wizard">

							<!--steps nav-->
							<?php $s=1; ?>

							<ol class="sa-steps-ol  steps clearfix span12" id="sa-steps">
							<!--<ul id="sa-steps" class="steps nav">-->
								<li id="ad-design" data-target="#step1" class="active">
									<span class="badge badge-info"><?php echo $s++; ?></span>
									<span class="hidden-phone hidden-tablet"><?php echo JText::_('DESIGN');?></span>
									<span class="chevron"></span>
								</li>


								<?php
								//Decide hide / or show geo targeting tab
								$showTargeting=1;
								if( !$this->socialads_config['geo_target'] && !($this->socialads_config['integration']!= 2) && !($this->socialads_config['context_target']))
								{
									$showTargeting=0;
								}

								if($showTargeting==1)
								{
								 ?>

									<li id="ad-targeting" data-target="#step2">
										<span class="badge"><?php echo $s++; ?></span>
										<span class="hidden-phone hidden-tablet"><?php echo JText::_('TARGETING'); ?></span>
										<span class="chevron"></span>
									</li>

								<?php
								} ?>

								<li id="ad-pricing" data-target="#step3" >
									<span class="badge"><?php echo $s++; ?></span>
									<span class="hidden-phone hidden-tablet"><?php echo JText::_('PRICING'); ?></span>
									<span class="chevron"></span>
								</li>
								<?php
								if($socialads_config['select_campaign'] == 1)
								{
									$sa_stpeNo=4;
									?>
									<li id="ad-review" data-target="#step<?php echo $sa_stpeNo ;?>" >
										<span class="badge"><?php echo $s++; ?></span>
										<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_SOCIALADS_REVIEW_AD')?></span>
										<span class="chevron"></span>
									</li>
									<?php
									$sa_stpeNo++;
								}
								else
								{
									$sa_stpeNo = 4;
									if(!empty($showBilltab))
									{	?>
										<li id="ad-billing" data-target="#step<?php echo $sa_stpeNo ;?>" >
											<span class="badge"><?php echo $s++; ?></span>
											<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_SOCIALADS_CKOUT_BILL_DETAILS')?></span>
											<span class="chevron"></span>
										</li>
										<?php
										$sa_stpeNo++;
									}
									else
									{ // already billing address is saved
										?>
										<input type="hidden" id="sa_hide_billTab" name="sa_hide_billTab" value="1" />
										<?php
									} ?>

									<li id="ad-summery" data-target="#step<?php echo $sa_stpeNo ;?>">
										<span class="badge"><?php echo $s++; ?></span>
										<span class="hidden-phone hidden-tablet"><?php echo JText::_('COM_SOCIALADS_CKOUT_ADS_SUMMERY')?></span>
										<span class="chevron"></span>
									</li>
									<?php
								}
								?>

							</ol>
							<!--steps nav-->

							<!--
							<div class="actions">
								<button type="button" class="btn btn-mini btn-prev"> <i class="icon-arrow-left"></i>Prev</button>
								<button type="button" class="btn btn-mini btn-next" data-last="Finish">Next<i class="icon-arrow-right"></i></button>
							</div>
							-->

						</div>
						<!--wizard-->
					</div>
						<!--tab-content step-content-->
						<div id="TabConetent" class="tab-content step-content">
						<form method="post" name="adsform" id="adsform" enctype="multipart/form-data" class="form-horizontal form-validate" onsubmit="return validateForm();">
							<!--step1-->
							<div class="tab-pane step-pane active" id="step1">
								<?php
									//echo $this->loadTemplate('design');
								?>

								<?php
								$socialadshelper = new socialadshelper();
								$billpath = $socialadshelper->getViewpath('buildad','default_design','ADMINISTRATOR','ADMINISTRATOR');

								ob_start();
									include($billpath);
									$html = ob_get_contents();
								ob_end_clean();

								echo $html;
								?>

							</div>
							<!--step1-->

							<!--step2-->
							<?php
							if($showTargeting==1)
							{
							 ?>
							<div class="tab-pane step-pane " id="step2">
								<?php
									//echo $this->loadTemplate('targeting');
								?>

								<?php
								$socialadshelper = new socialadshelper();
								$billpath = $socialadshelper->getViewpath('buildad','default_targeting','ADMINISTRATOR','ADMINISTRATOR');

								ob_start();
									include($billpath);
									$html = ob_get_contents();
								ob_end_clean();

								echo $html;
								?>
							</div>
							<?php
							}
							?>
							<!--step2-->

							<!--step3-->
							<div class="tab-pane step-pane" id="step3">

								<?php
								$socialadshelper = new socialadshelper();

								if($this->socialads_config['select_campaign']==0)
								{
									$billpath = $socialadshelper->getViewpath('buildad','default_pricing','ADMINISTRATOR','ADMINISTRATOR');
								}
								else
								{
									$billpath = $socialadshelper->getViewpath('buildad','default_camp','ADMINISTRATOR','ADMINISTRATOR');
								}

								ob_start();
									include($billpath);
									$html = ob_get_contents();
								ob_end_clean();

								echo $html;
								?>
							</div>
							<!--step3-->
						</form>
							<!--step4-->

							<?php


							if($socialads_config['select_campaign']==1)
							{
								$sa_stpeNo=4;
								?>
								<div class="tab-pane step-pane" id="step<?php echo $sa_stpeNo ;?>">
									<div id="adPreviewHtml">
									</div>
								</div>
								<?php
							}
							else
							{
								$sa_stpeNo = 4;
								?>
								<form method="post" name="sa_BillForm" id="sa_BillForm" class="form-horizontal form-validate" onsubmit="return validateForm();">
									<?php
								if(!empty($showBilltab))
								{
									?>
								<div class="tab-pane step-pane sa_build_ad_billing" id="step<?php echo $sa_stpeNo ;?>">
									<?php
										//echo $this->loadTemplate('pricing');
									?>

									<?php
									$socialadshelper = new socialadshelper();
									$billpath = $socialadshelper->getViewpath('buildad','default_billing','ADMINISTRATOR');
									ob_start();
										include($billpath);
										$html = ob_get_contents();
									ob_end_clean();

									echo $html;
									?>
								</div>
								<?php
									$sa_stpeNo++;
								}
								else
								{
									//  this field should be in the   <form name='adsform'
									// already billing address is saved
									?>
									<input type="hidden" id="sa_hide_billTab" name="sa_hide_billTab" value="1" />
									<?php
								}
								?>
								</form>

							<?php
							} ?>

							<!--step4-->

							<?php
							//if($socialads_config['select_campaign'] != 1)
							{
								?>
								<!--step5-->
								<div class="tab-pane step-pane" id="step<?php echo $sa_stpeNo ;?>">
									<!-- bill msg -->
									<div class="row-fluid ">
										<?php
										if( empty($showBilltab))
										{
										?>
											<div class="span12" id="sa_reomveMargin">
												<?php
													JHTML::_('behavior.modal');
													$catid=0;
													$socialadshelper = new socialadshelper();
													$itemid = $socialadshelper->getSocialadsItemid('buildad');

													$terms_link = JURI::root().substr(JRoute::_('index.php?option=com_socialads&view=buildad&layout=updatebill&tmpl=component&itemid='.$itemid),strlen(JURI::base(true))+1);
												?>
												<div class="alert alert-success " id="">
												  <?php echo JText::_( 'COM_SOCIALADS_BILL_INFO_ALREADY_STORED' ); ?>
													<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" href="<?php echo $terms_link;?>" class="modal">
														<strong><?php echo JText::_( 'COM_SOCIALADS_BILL_CLICK_HERE' ); ?></strong>
													</a>
													<?php echo JText::_( 'COM_SOCIALADS_UPDATE_BILLING_ADDRESS' ); ?>
												</div>

											</div>
										<?php } ?>
									</div>

									<div id="ad_reviewAndPayHTML">
									</div>

								</div>
								<!--step5-->
							<?php
							} ?>

						</div>
						<!--tab-content step-content-->

						<!--pull-right-->
						<div class="prev_next_wizard_actions">
							<div class="form-actions">
								<button id="btnWizardPrev" type="button" style="display:none" class="btn btn-primary pull-left" > <i class="icon-circle-arrow-left icon-white" ></i><?php echo JText::_('COM_SOCIALADS_PREV');?></button>
								<button id="btnWizardNext" type="button" class="btn btn-primary pull-right" data-last="Finish" onclick="return open_div(<?php echo ($this->target_div)?'1' : '0'; ?>);">
									<span><?php echo JText::_("COM_SOCIALADS_BTN_SAVEANDNEXT");?></span>
									<i class=" icon-circle-arrow-right  icon-white"></i>
								</button>

								<button id="sa_cancel" type="button" class="btn btn-danger pull-right" style="margin-right:1%;" onclick="cancelAd()"><?php echo JText::_('COM_SOCIALADS_CANCEL');?></button>

							</div>
						</div>
						<!--pull-right-->

					</div>
					<!--fuelux wizard-example -->
					<!--/div-->

					<input type="hidden" name="joomla_version" id="joomla_version" value="<?php echo JVERSION ?>" />

					<?php
					if($this->edit_ad_adsumary)
					{
					?>
						<input type="hidden" name="editview" id="editview" value="1" />
						<?php
					}
					else
					{	?>
						<input type="hidden" name="editview" id="editview" value="<?php echo ($this->input->get('frm','','STRING')=='editad')? '1' : '0'; ?>">
						<?php
					} ?>

					<div style="clear:both;"></div>



			</div>
			<!-- main div starts here-->
	</div>
	<!--row-fluid-->

</div>
<!--techjoomla-bootstrap -->

<script type="text/javascript">

	//javascript global variables declared herer
	var currency="<?php echo $socialads_config['currency']; ?>"
	var camp_currency_daily="<?php echo $socialads_config['camp_currency_daily']; ?>";
	var allowWholeAdEdit="<?php echo $this->allowWholeAdEdit; ?>";
	var root_url="<?php echo $root_url; ?>";
	var root_url2="<?php echo $root_url; ?>";
	var base_url="<?php echo JUri::base(); ?>";
	var selected_layout="<?php echo $this->addata_for_adsumary_edit->layout; ?>";
	var savennextbtn_text="<?php echo JText::_("COM_SOCIALADS_BTN_SAVEANDNEXT");?>";
	var savenexitbtn_text="<?php echo JText::_("COM_SOCIALADS_BTN_SAVEANDEXIT");?>";
	var showTargeting=parseInt("<?php echo $showTargeting; ?>");

	function cancelAd()
	{
		var r=confirm("<?php echo JText::_('COM_SOCIALADS_CANCEL_AD'); ?>");

		if (r==true)
		{
			window.location.assign(base_url+"?option=com_socialads&view=approveads");
		}
		else
		{
			return false;
		}

	}

	//@TODO implement price per click/impression like frontend
	function getzone_priceForInfo()
	{
		var a=0;
	}

	techjoomla.jQuery(document).ready(function(){

		var id = document.getElementById("ad_creator_id").value;
		//Get selected user social promoter plugins
		getPromoterPlugin(id);
		//Get selected user campaign
		getUserCampaign(id);
	});

</script>


<!--ajaxuload include here-->
<script type="text/javascript" src="<?php echo JURI::root(); ?>components/com_socialads/js/ajaxupload.js"></script>

</script>
