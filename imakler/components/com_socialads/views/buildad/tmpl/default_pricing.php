
<?php
if($this->socialads_config['select_campaign']==0)
{

		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::root().'components/com_socialads/css/helper.css');
		$document->addScript(JUri::root().'components/com_socialads/js/helper.js');

		require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

		JPluginHelper::importPlugin( 'payment' );
		$dispatcher = JDispatcher::getInstance();
		$newvar = JPluginHelper::getPlugin( 'payment' );
		$selectbox = array();
		$re_selectbox = array();
		$selectbox_all = array();
		$i=0;
		$payment_flag=1;
		$gatewaylabel=JText::_("SELECT");
		if(empty($newvar))
		{
			$selectbox[] = JHtml::_('select.option','', JText::_('CHKGATEWAY'));
			$payment_flag=0;
		}
		else
		{
			$payment_flag=1;
			$default_selected_gateway=0;

			foreach ($newvar as $myparam)
			{

				if(!in_array($myparam->name,$socialads_config['gateways']))
				continue;
				$plugin = JPluginHelper::getPlugin( 'payment',$myparam->name);
				//echo $plugin;
				$gateway_style="";
				if(count($newvar)==1)
				{
					$default_selected_gateway=1;
					$gateway_style="style=display:none";
					$gatewaylabel=JText::_("SELECT_GATEWY_DEFAULT");
				}

				$pluginParams = json_decode( $plugin->params );

				$selectbox[] = JHtml::_('select.option',$myparam->name, $pluginParams->plugin_name);

				if(!$pluginParams->arb_support){
					$re_selectbox[$i]['value'] = $myparam->name;
					$re_selectbox[$i++]['name'] =  $pluginParams->plugin_name;
				}

			}
		}

		$re_selectbox_json = json_encode($re_selectbox);

		//selectlist
		$singleselect = array();
		$slabs_json ='';
		foreach($socialads_config['pricing_opt'] as $k=> $v){
			if($v == 0)
				$singleselect[] = JHtml::_('select.option','0', JText::_('IMPR'));
			elseif($v == 1)
				$singleselect[] = JHtml::_('select.option','1', JText::_('CLICK'));
			elseif($v==2)
			{
				if(empty($socialads_config['show_slab']))
				{
					$singleselect[] = JHtml::_('select.option','2', JText::_('SA_DAY'));
				}
				else
				{
					if($socialads_config['show_per_day_opt']==1)
						$singleselect[] = JHtml::_('select.option','2', JText::_('SA_DAY'));
					foreach($socialads_config['slab'] as $slab)
					{
						if(!empty($slab['price']))
						{
							$singleselect[] = JHtml::_('select.option',$slab['duration'], $slab['label']);
						}
					}
					$slabs_json = json_encode($socialads_config['slab']);
				}
			}
		}
		$buildadsession = JFactory::getSession();
		$ad_chargeoption = $ad_totaldisplay = $ad_totalamount = $ad_gateway = $ad_currency = $ad_rate = $ad_daterangefrom =	$ad_daterangeto = $ad_totaldays = $ad_chargeoption_day = '';
		  $ad_totaldisplay =   $this->pricingData->ad_credits_qty;
	if($showimpclick == 0)
	{
		//session variable for chargoption
		$ad_chargeoption =  $this->pricingData->ad_payment_type;

		//session variable for daterange
		$ad_daterangefrom = $this->pricingData->ad_startdate;
		$ad_daterangeto = $buildadsession->get('dateto');
		$ad_calenderid = $buildadsession->get('priceperdate');
		$ad_totaldays = $this->pricingData->ad_credits_qty;
			$sa_recuring = $buildadsession->get('sa_recuring');
		//session variable for totladisplay


		//session variable for totalamount
		$ad_totalamount = $this->pricingData->ad_original_amt;

		//session variable for gateway selectbox
		$ad_gateway = $buildadsession->get('ad_gateway');
		$ad_currency = $buildadsession->get('ad_currency');
		$ad_rate = $buildadsession->get('ad_rate');
	}
	else if($showimpclick == 1)
	{
		$ad_chargeoption =  $buildadsession->get('ad_chargeoption');
		$ad_chargeoption_day = $buildadsession->get('ad_chargeoption_day');
	}

	 	$u_points=$buildadsession->get('user_points');
	//Extra code for zone pricing
 		if(!$socialads_config['clicks_price'])
  			$socialads_config['clicks_price']=0;
		if(!$socialads_config['date_price'])
			$socialads_config['date_price']=0;
		if(!$socialads_config['impr_price'])
			$socialads_config['impr_price']=0;
		$recurring_gateway=socialadshelper::getRecurringGateways();

		if(!$recurring_gateway)
		$recurring_gateway='';

		if(!isset($u_points)){
			$u_points=-1;
		}

		$js = "

		var jpoints=$u_points;
		var amt1=0;
		var click_price=0;
		var pric_day=0;
		var click_imp=0;
	function caltotal()
	{
		getzone_priceForInfo();

		if(techjoomla.jQuery('#totaldisplay').length)
		{
			var el= document.getElementById('totaldisplay');
			var result=ad_checkforalpha(el,'',valid_msg);

			if(!result)
			{
				caltotal();
			}
		}

		if(techjoomla.jQuery('#totaldays').length)
		{
			var el= document.getElementById('totaldays');
			var result=ad_checkforalpha(el,'',valid_msg);

			if(!result)
			{
				caltotal();
			}
		}

		var chargeoptionsel=document.getElementById('chargeoption').value;

		var totaldisplay=document.getElementById('totaldisplay').value;
		var gateway = document.getElementById('gateway').value;
		var recurring_gateway = '".$recurring_gateway."';
		var re_jsondata = '".$re_selectbox_json."';
		var re_select = jQuery.parseJSON(re_jsondata);

		if (jQuery('#sa_recuring').is(':checked'))
		{
			document.getElementById('total_days_label').innerHTML = '".JText::_("SA_RENEW_RECURR")."'+' '+jQuery('#chargeoption option:selected').text();
		}
		else
		{
			document.getElementById('total_days_label').innerHTML = '".JText::_("SA_RENEW_NO_RECURR")."'+' '+jQuery('#chargeoption option:selected').text();
		}
		if(recurring_gateway.search(gateway)==-1)
		{

			if(document.getElementById('sa_recuring').checked==true)
			{
				document.getElementById('sa_recuring').checked=false;
			}

		}
		else
		{
			 if(".$socialads_config['recure_enforce']."==1)
			  document.getElementById('sa_recuring').checked=true;
		}



		if(!gateway)
			return;


	 	if(document.getElementById('chargeoption').value == '1')
	 	{
			document.getElementById('total_days').style.display = 'none';
			document.getElementById('priceperdate').style.display = 'none';
			document.getElementById('priceperclick').style.display = 'block';

			if(totaldisplay=='')
			{
 				document.getElementById('ad_totalamount').innerHTML = '';
 				document.getElementById('currency').innerHTML='';
 		  }
 		  else{
				amt1=round(totaldisplay * click_price);

	 			if(jpoints<0){
					document.getElementById('ad_totalamount').innerHTML= amt1;
					document.getElementById('totalamount').value = amt1;
					document.getElementById('currency').innerHTML='".$socialads_config['currency']."';
		 			document.getElementById('hcurrency').value= '".$socialads_config['currency']."';
					document.getElementById('hrate').value='';
				}
			}
	 	}
	 	else if(document.getElementById('chargeoption').value == '0')
	 	{
			document.getElementById('total_days').style.display = 'none';
			document.getElementById('priceperdate').style.display = 'none';
			document.getElementById('priceperclick').style.display = 'block';

	 		if(totaldisplay=='')
	 		{
	 			document.getElementById('ad_totalamount').innerHTML='';
	 			document.getElementById('currency').innerHTML='';
	 		}
	 		else {
	 			amt1=round(totaldisplay *  click_imp);
	 			if(jpoints<0){
		 			document.getElementById('ad_totalamount').innerHTML = amt1;
		 			document.getElementById('totalamount').value =  amt1;
		 			document.getElementById('currency').innerHTML='".$socialads_config['currency']."';
		 			document.getElementById('hcurrency').value= '".$socialads_config['currency']."';
					document.getElementById('hrate').value='';
				}
	 		}
	 	}
	 	else if(document.getElementById('chargeoption').value == '2')
	 	{
			document.getElementById('total_days').style.display = '';
			document.getElementById('priceperclick').style.display = 'none';
			/*added by sagar feb9*/

			document.getElementById('total_days_label').innerHTML = '".JText::_("TOTAL_DAYS_FOR_RENEWAL")."';
			document.getElementById('sa_recuring_div').style.display = 'none';

			var ad_chargeoption_day = 0;
			if(document.getElementById('ad_chargeoption_day') )
				ad_chargeoption_day = document.getElementById('ad_chargeoption_day').value;

			if(ad_chargeoption_day)
			{
				if(document.getElementById('totaldays').value==' ' )
				{
					document.getElementById('ad_totalamount').innerHTML='';
					document.getElementById('currency').innerHTML='';
				}
				else
				{
					var daycount=document.getElementById('totaldays').value;

					document.getElementById('ad_totaldays').value = daycount;
					amt1=round(daycount *  pric_day);
					if(jpoints<0){
				 		document.getElementById('ad_totalamount').innerHTML = amt1;
				 		document.getElementById('totalamount').value =  amt1;
				 		document.getElementById('currency').innerHTML='".$socialads_config['currency']."';
				 		document.getElementById('hcurrency').value= '".$socialads_config['currency']."';
						document.getElementById('hrate').value='';
						}
				}
			}
			 else
			{
				document.getElementById('priceperdate').style.display = '';
				var daterangefrom = document.getElementById('datefrom').value;

				if(daterangefrom==' '  || document.getElementById('totaldays').value == '')
				{
					document.getElementById('ad_totalamount').innerHTML='';
					document.getElementById('currency').innerHTML='';
				}
				else
				{
						var daycount;
				 		daycount=document.getElementById('totaldays').value;

						document.getElementById('ad_totaldays').value = daycount;
				 	  amt1=round(daycount *  pric_day);
				 		document.getElementById('ad_totaldays').value = daycount;
				 		document.getElementById('totaldays').innerHTML = daycount;
				 		if(jpoints<0){
					 		document.getElementById('ad_totalamount').innerHTML = amt1;
					 		document.getElementById('totalamount').value =  amt1;
					 		document.getElementById('currency').innerHTML='".$socialads_config['currency']."';
					 		document.getElementById('hcurrency').value= '".$socialads_config['currency']."';
							document.getElementById('hrate').value='';
						}
				}
		 	}
	 }
	 else
	 {
			document.getElementById('priceperdate').style.display = '';

			document.getElementById('total_days').style.display = '';
			document.getElementById('priceperclick').style.display = 'none';
			/*added by sagar feb9*/

			if(".$socialads_config['recure_enforce']."==0)
			{
				if(recurring_gateway.search(gateway)==-1)
    		{

       			document.getElementById('sa_recuring_div').style.display=	'none'
        }
        else
				document.getElementById('sa_recuring_div').style.display = '';
			}
			else	if(recurring_gateway.search(gateway)==-1)
    		{

       			document.getElementById('sa_recuring_div').style.display=	'none'
        }


			var daterangefrom = document.getElementById('datefrom').value;
			if(daterangefrom==' ' || document.getElementById('totaldays').value == '')
	 		{
	 			document.getElementById('ad_totalamount').innerHTML='';
	 			document.getElementById('currency').innerHTML='';
	 		}
			else
			{
				var jsondata = '".$slabs_json."';
				var slab = jQuery.parseJSON(jsondata);

				for (i=0;i<slab.length;i++)
				{
					if(parseInt(slab[i].duration)==parseInt(chargeoptionsel))
					{
						amt1=slab[i].price;
						daycount=slab[i].duration;
						break;
					}
				}

				daycount=document.getElementById('totaldays').value;

				if(document.getElementById('sa_recuring').checked==false){
					amt1=round(daycount *  amt1);
				}

				document.getElementById('totalamtspan').innerHTML = '".JText::_("TOTAL")."'
				if(document.getElementById('sa_recuring').checked==true)
    			{
    				chargeselected=document.getElementById('chargeoption').value;
    				if(parseInt(chargeselected)>2)
    					document.getElementById('totalamtspan').innerHTML = '".JText::_("TOTAL_SLAB")."'+' '+jQuery('#chargeoption option:selected').text();

    			}


			 		document.getElementById('ad_totaldays').value = daycount;

			 		if(jpoints<0){
				 		document.getElementById('ad_totalamount').innerHTML = amt1;
				 		document.getElementById('totalamount').value =  amt1;
				 		document.getElementById('currency').innerHTML='".$socialads_config['currency']."';
				 		document.getElementById('hcurrency').value= '".$socialads_config['currency']."';
						document.getElementById('hrate').value='';
					}
		 	}
	 }
	 	if( (!(jpoints<0)) && (gateway!= '') ){
	 		calpoints();
	 	}

	 	// check for coupon
	 	sa_applycoupon(0);

	}


	function removeoption(select_opt){
		for (i=0;i<select_opt.length;i++)
		{
			jQuery('#gateway option[value=\"'+select_opt[i].value+'\"]').remove();
		}
	}

	function addoption(select_opt){
		for (i=0;i<select_opt.length;i++)
		{
		alert(valuecurrentgatewayliststr);
		var	result=valuecurrentgatewayliststr.search(select_opt[i].value);
			if(result==-1)
			{
				jQuery('#gateway').append(jQuery('<option/>', {
				value: select_opt[i].value,
				text: select_opt[i].name
				}));
			}
		}

	}

	function round(n) {
		return Math.round(n*100+((n*1000)%10>4?1:0))/100;
	}

	function calpoints()
	{
		var amt=document.getElementById('totalamount').value;
	 	var totaldisplay=document.getElementById('totaldisplay').value;
		if(!document.getElementById('gateway').value)
			return;
	 	document.getElementById('jpoints').value =jpoints;
		var chargeoption=document.getElementById('chargeoption').value;
		if(totaldisplay=='' && chargeoption != '2') {
			if(parseInt(chargeoption)<='2')
			{
				document.getElementById('ad_totalamount').innerHTML='';
				document.getElementById('currency').innerHTML='';
			}
		}
		else{
	 	document.getElementById('rate').innerHTML='';
			caltotal();
		}
	}

		jQuery(function() {
		var totaldisplay=document.getElementById('totaldisplay').value;

		jQuery('#sa_recuring').change(function(){
			var re_jsondata = '".$re_selectbox_json."';
			var re_select = jQuery.parseJSON(re_jsondata);
			chargeselected=document.getElementById('chargeoption').value;
			if(document.getElementById('chargeoption').value > '2'){
				if (jQuery('#sa_recuring').is(':checked'))
				{
					document.getElementById('total_days_label').innerHTML = '".JText::_("SA_RENEW_RECURR")."'+' '+jQuery('#chargeoption option:selected').text();

    				if(parseInt(chargeselected)>2)
    					document.getElementById('totalamtspan').innerHTML = '".JText::_("TOTAL_SLAB")."'+' '+jQuery('#chargeoption option:selected').text();
					//removeoption(re_select);
				}
				else
				{
					document.getElementById('total_days_label').innerHTML = '".JText::_("SA_RENEW_NO_RECURR")."'+' '+jQuery('#chargeoption option:selected').text();
    					document.getElementById('totalamtspan').innerHTML = '".JText::_("TOTAL")."'
					//addoption(re_select);
				}
			}
	  });
	  jQuery('#sa_recuring').change();
		if(document.getElementById('editview').value=='1'){
			calpoints();
		}
	});
	";


	$document->addScriptDeclaration($js);

    // Load the calendar behavior
    //JHtml::_('behavior.calendar');
	  $articlelink= JRoute::_('index.php?option=com_content&tmpl=component&view=article&id='.$socialads_config['tnc']);
//bottom div starts here

?>

<div id="bottomdiv" style="display:block;">
	<fieldset class="sa_fieldset">
		<legend class="hidden-desktop"><?php echo JText::_('PRICING'); ?></legend>
		<div class="form-horizontal buildad_pricing_tab">
				<?php
				if ($this->special_access)
				{

					$publish1=$publish2=$publish1_label=$publish2_label='';
					if(!empty($this->addata_for_adsumary_edit->ad_noexpiry))
					{
						if($this->addata_for_adsumary_edit->ad_noexpiry)
						{
							$publish1='checked';
							$publish1_label	=' btn-sucess ';
						}
						else
						{
							$publish2='checked';
							$publish2_label	=	'btn-danger';
						}
					}
					else
					{
						$publish2='checked';
						$publish2_label	=	'btn-danger';
					}
				?>
			<div class="control-group">
				<div class="unlimited_adtext alert alert-info"><?php echo JText::_('UNLIMITED_AD');?></div>

				<label class="control-label" for="type" title="">
					<?php echo JText::_('COM_SA_UNLIMITED_AD');?>
				</label>

				<div id="review" class="controls input-append unlimited_yes_no">
					<input type="radio" name="unlimited_ad" id="unlimited_ad1" value="1" <?php echo $publish1;?> />
					<label class="first btn <?php echo $publish1_label;?>" for="unlimited_ad1"><?php echo JText::_('SA_YES');?></label>
					<input type="radio" name="unlimited_ad" id="unlimited_ad2" value="0" <?php echo  $publish2;?> />
					<label class="last btn <?php echo $publish2_label;?>" for="unlimited_ad2"><?php echo JText::_('SA_NO'); ?></label>
				</div>
			</div>
			<?php	} ?>

			<div class="control-group">
				<?php
					if($showimpclick == 0)
					{
				?>

				<label class="ad-price-lable control-label" ><?php echo JText::_("ADS_CHARGED");?></label>

				<div class = "controls ">
					<?php echo JHtml::_('select.genericlist', $singleselect, "chargeoption", 'class="ad-pricing"  onchange="caltotal()" title="'.JText::_("COM_SOCIALADS_ADS_TO_BE_CHARGED_MSG") .'"', "value", "text", $ad_chargeoption);?>
					<span class=" "  >
						<img border="0" title="<?php echo JText::_("COM_SOCIALADS_ADS_TO_BE_CHARGED_MSG") ?>" alt="" src=<?php echo $root_url."components/com_socialads/images/tooltip.png "; ?>" >
					</span>
				</div>
				<?php
					}
					else
					{
				?>
						<div class="controls"><input type="hidden" name="chargeoption" id="chargeoption" value="<?php echo $ad_chargeoption;?>"><input type="hidden" name="chargeoption_day" id="chargeoption_day" value="<?php echo $ad_chargeoption_day;?>"></div>


				<?php
					}
				?>
				<div class = "controls ">
					<div id="sa_click"  style="display:none">
						<p class="text-info">
							&nbsp;<span id="sa_click_span"></span></p>
					</div>
					<div id="sa_imps" style="display:none" >
						<p class="text-info">
							&nbsp;<span id="sa_imps_span"></span>
						</p>
					</div>
					<div id="sa_pric_day" style="display:none" >
						<p class="text-info">
							&nbsp;<span id="sa_pric_day_span"></span>
						</p>
					</div>
					<div id="sa_price_slab" style="display:none" >
						<p class="text-info">
							&nbsp;<span id="sa_price_slab_span"></span>
						</p>
					</div>

				</div>
			</div>
				<?php
				if($ad_chargeoption == 2 && $ad_chargeoption_day == ''   )
				{
						$display_style	=	"display:display";
				}
				else{
					if($ad_chargeoption_day)
						$display_style	=	"";//display:none";
					else if((count($socialads_config['pricing_opt'])==1 && $socialads_config['pricing_opt'][0] == 2))
						$display_style	=	"display:display";
					else
						$display_style	=	"display:none";
				}
				?>

			<div class="control-group" id="priceperdate" style="<?php echo $display_style;?>">
				<?php
				if( $ad_daterangefrom && isset($ad_daterangefrom)  )
				{
					$checked = '';
					if($sa_recuring == '1')
						$checked = 'checked="checked"';
					?>
					<label class="ad-price-lable control-label" ><?php echo JText::_("FROM");?></label>
					<div class="ad-price-lable controls">
						<?php echo JHtml::_("calendar", $ad_daterangefrom , "datefrom", "datefrom", "%Y-%m-%d", 'class="ad-pricing", onchange="caltotal()"');?>
						<div id="sa_recuring_div" style="display:none">
							<input type="checkbox" maxlength="5" name="sa_recuring" class="ad-pricing" id="sa_recuring" <?php echo $checked;?> value="1" onchange="caltotal()" />
							<?php echo JText::_("SA_AUTO_RENEW");?>
						</div>
					</div>
				<?php
				}
				else
				{
					$re_chked = '';
					if($socialads_config['recure_enforce'])
						$re_chked = 'checked="checked"';
									//echo "=====2=====";
					?>
						<label class="ad-price-lable control-label" width="40%"><?php echo JText::_("FROM");?></label>
						<div class="ad-price-lable controls">
							<?php echo JHtml::_("calendar", " ", "datefrom", "datefrom", "%Y-%m-%d", 'class="ad-pricing", onchange="caltotal()"');?>
							<div id="sa_recuring_div" style="display:none">
								<input type="checkbox" maxlength="5" name="sa_recuring" class="ad-pricing" id="sa_recuring" <?php echo $re_chked;?> value="1" onchange="caltotal()" />
									<?php echo JText::_("SA_AUTO_RENEW");?>
							</div>
						</div>
				<?php
				}
				?>
			</div>
			<?php

			if($ad_chargeoption == 2 && $ad_chargeoption_day )
				$date_dis = 'display:block';
			else if((count($socialads_config['pricing_opt'])==1 && $socialads_config['pricing_opt'][0] == 2))
				$date_dis = 'display:block';
			else
				$date_dis = 'display:none';

			?>
			<div class="control-group" id="total_days" style="<?php echo $date_dis;?>">

				<label class="ad-price-lable control-label"  width="40%"><div id="total_days_label"><?php JText::_("TOTAL_DAYS_FOR_RENEWAL");?>:</div> </label>
				<div class="controls">
					<input type="text" maxlength="5" name="totaldays" class="ad-pricing" id="totaldays" value="<?php echo $ad_totaldays;?>" onchange="caltotal()" />
					<input type="hidden" name="ad_totaldays" id="ad_totaldays" value="<?php echo  $ad_totaldays;?>" />
				</div>
			</div>

		<?php
			if($ad_chargeoption == 2 || (count($socialads_config['pricing_opt'])==1 && $socialads_config['pricing_opt'][0] == 2)){
				?>
				<div id="priceperclick" class = "control-group" style="display:none">
			<?php
			}
			else{
				?>
			<div id="priceperclick" class = "control-group" style="display:block">
			<?php
			}
			?>
				<label class="ad-price-lable control-label"><?php echo JText::_("CLICKS-IMPRESSIONS");?></label>
				<div class="controls">
					<input type="text" maxlength="7" name="totaldisplay" class="ad-pricing cal_text" id="totaldisplay" value="<?php echo $ad_totaldisplay;?>" onchange="caltotal()"  />
				</div>
			</div>

		<div class = "control-group">

				<label class="ad-price-lable control-label"><span id="totalamtspan" name="totalamtspan"><?php echo JText::_("TOTAL");?></span></label>
				<div class="controls cal_text">
					<span id="ad_totalamount" name="ad_totalamount" class="ad_pricing" ><?php echo $ad_totalamount;?></span>
					<input type="hidden" name="totalamount" id="totalamount" value="<?php echo $ad_totalamount;?>" onchange="caltotal()" />
					<input type="hidden" name="jpoints" id="jpoints"  value="<?php echo $u_points;?>" />

					<span id="currency" name="currency" class="ad_pricing " ><?php echo $ad_currency;?></span>
					<input type="hidden" name="h_currency" id="hcurrency"  value="<?php echo $ad_currency;?>" />
				</div>
		</div>
			<div id= "dis_amt" style="display:none;">
			</div>


		<!-- Remove payment gateway html -->

		<div class="control-group" style="display:none">
			<label class="ad-price-lable control-label"><?php echo $gatewaylabel;?></label>
			<?php
			if($payment_flag)
			{
				?>
				<div class="controls">
				<?php
				if($default_selected_gateway) //show only default gateway
				{ ?>
						<span><?php echo $pluginParams->plugin_name;?></span>

				<?php } ?>
				<span id="gateway_div" <?php echo $gateway_style;?>>
						<?php echo JHtml::_('select.genericlist', $selectbox , "gateway", 'class="ad-pricing" size="1" onchange="calpoints()" ', "value", "text", $ad_gateway);?>
				</span>
				</div>
			<?php
			}
			else
			{
			?>
				<div class="controls">
				<span><?php echo JText::_('CHKGATEWAY');?><input type="hidden" name="gateway" id="gateway"  value="" /></span>
				</div>
			<?php
			}
			?>
				<div class="controls">
						<div  id="rate" name="rate" class="ad_pricing" ><?php echo $ad_rate;?></div>
						<input type="hidden" name="h_rate" id="hrate"  value="<?php echo $ad_rate;?>" />
				</div>
		</div>


		<!-- Vm:coupon releated-->
		<div class="sa_hideForUnlimitedads" >

			<div class="control-group">
				<label class="control-label" for="sa_coupon_chk" title="<?php echo JText::_('SA_DO_U_HV_A_COUPON');?>">
					<?php echo JText::_('SA_DO_U_HV_A_COUPON');?>
				</label>
				<div class="controls">
					<input type="checkbox" id="sa_coupon_chk" autocomplete="off" name="coupon_chk" value="" size="10" onchange="show_cop()">
					&nbsp;
					<span id="sa_cop_tr"  style="display:none;" >

						<input class="input-small focused" autocomplete="off" id="sa_coupon_code" name="sa_cop" value="" type="text" placeholder="<?php echo JText::_("SA_COUPON_PLACEHOLDER") ?>">
						<input type="button" class="btn btn-success" onclick="sa_applycoupon(1)" value="<?php echo JText::_("SA_COUPON_APPLY") ?>">
					</span>
				</div>
			</div>

		<div class="control-group sa_cop_details " style="display:none;" >
			<label class="control-label"><?php echo JText::_("SA_COUPON_PRICE") ?></label>
			<div class="controls" id="sa_cop_afterprice">


			</div>
		</div>

		<div class="control-group sa_cop_details " style="display:none;" >
			<label class="control-label"> <?php echo JText::_("SA_AFTER_COUPON_PRICE") ?></label>
			<div class="controls" id="sa_cop_price">


			</div>
		</div>
		</div>
		<!-- Vm:End coupon releated-->

	</div>

		<div id="ad-pricing-error" class="alert alert-error" style="display:none;">
			<?php echo JText::_('COM_SA_SOMETHING_WENT_WRONG_AD_PRICING'); ?>
		</div>

	</fieldset>
</div><!--close bottomdiv-->
<?php
//bottomdiv ends here
}
/*else
{
	?>
		<!---
		<div id="camp_dis" style="display:block;">
			<?php echo $this->loadTemplate('camp');  ?>
		</div>
		-->
	<?php
} */ ?>

<?php
// vm: for add more credit

$addCreditAttr = '';
if(!empty($this->editableSteps) && $this->editableSteps['pricing']==0)
{
	$addCreditAttr = "disabled='disabled'";
}
?>

<script type="text/javascript">

	var addCreditAttr = "<?php echo $addCreditAttr; ?>";

	if(addCreditAttr)
	{
		jQuery("#chargeoption").attr("disabled","disabled");
			//jQuery("#totaldisplay").attr("disabled","disabled");
			//jQuery("#datefrom").attr("disabled","disabled");
	}

</script>
