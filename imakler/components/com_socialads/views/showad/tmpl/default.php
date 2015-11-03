<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
JHtml::_('behavior.modal', 'a.modal');
JHtml::_('behavior.formvalidation');
$document = JFactory::getDocument();
//$document->addStyleSheet(JUri::base().'components/com_socialads/css/socialads.css');
$document->addStyleSheet(JUri::base().'components/com_socialads/css/helper.css');
$document->addScript(JUri::base().'components/com_socialads/js/flowplayer-3.2.9.min.js');//added by manoj stable 2.7.5
$document->addScript(JUri::base().'components/com_socialads/js/socialads.js');
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
require_once JPATH_COMPONENT . DS . 'helper.php';
global $mainframe;
$mainframe = JFactory::getApplication();
$sitename = $mainframe->getCfg('sitename');
$user = JFactory::getUser();

$response['msg'] = JText::_('DETAILS_SAVE');
if($socialads_config['approval']==1)
{
	$response['msg'] .='<br>'.JText::_('AD_REVIEW');
}
?>
<div class="techjoomla-bootstrap">
<?php
if (!$user->id)
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('BUILD_LOGIN'); ?>
	</div>

	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
	return false;
}
	$buildadsession = JFactory::getSession();

		$plugin = JPluginHelper::getPlugin('payment',$buildadsession->get('ad_gateway'));
	if(!isset($plugin))
	{
		$pluginParams = json_decode($plugin->params);
		$arb_support = $pluginParams->arb_support;
	}
	 $ad_image = $buildadsession->get('ad_image');
	/////Values are taken when zone pricing Enabled
	//$payhtml = $model->getpayHTML($order['order_info'][0]->processor,$order_id);
	// added by VM
	JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
	$model = new socialadsModelShowad();
	// VM : end

	if($socialads_config['zone_pricing']=='1')
	{
		$buildadsession = JFactory::getSession();
		$zoneid=$buildadsession->get('adzone');
		//print_r($zoneid); die('sdf');
		$zonedata = $model->getzonedata($zoneid);
		$socialads_config['clicks_price']	= $zonedata['0']->per_click;
		$socialads_config['date_price']		= $zonedata['0']->per_day;
		$socialads_config['impr_price']		= $zonedata['0']->per_imp;
	}


	//Added by sagar hardcoded for now
if($socialads_config['select_campaign']==0)
{
	/////Values are taken when zone pricing Enabled
	if($this->chargeoption == 1)
	{
		$cal = $this->ad_totaldisplay * $socialads_config['clicks_price'];
	}
	else if($this->chargeoption >= 2)
	{
		if($this->chargeoption > 2)
		{
			if($socialads_config['show_slab'])
			{
				foreach($socialads_config['slab'] as $slab)
				{

					if(!empty($slab['price']) and ($slab['duration']==$this->chargeoption))
					{
						$socialads_config['date_price']=$slabprice=$slab['price'];
						$slablabel=$slab['label'];
						break;
					}

				}

				if($this->sa_recuring == '1')
				{
					$cal = 	$socialads_config['date_price'];
				}
				else{
					$cal = $this->ad_totaldays * $socialads_config['date_price'];
				}
			}
		}
		else
		  $cal = $this->ad_totaldays * $socialads_config['date_price'];
	}
	else  if($this->chargeoption == 0)
	{
		 $cal = $this->ad_totaldisplay * $socialads_config['impr_price'];
	}
	else{
		 $cal = $buildadsession->get('totalamount','');
	}

	if (!isset($this->ad_points))
	{
		$buildadsession->set('totalamount',$cal);

	}
	else
	{
		$cal= $cal * $this->ad_jconver;
		$socialads_config["currency"] = JText::_('POINT');
	}

	if($cal == 0)
	{	$msg = JText::_('TOT_AMT_MSG');
		$link = JRoute::_(JUri::base().'index.php?option=com_socialads&view=buildad');
		$mainframe->redirect($link, $msg);
		return true;
	}
	$article_id=$socialads_config['article'];


//javascript for submit form and submit button
$js = "

function show_cop(){

	if(jQuery('#coupon_chk').is(':checked'))
		jQuery('#cop_tr').show();
	else
	{
		jQuery('#cop_tr').hide();
		jQuery('#coupon_code').val('');
		jQuery('#dis_cop').html('<td >".JText::_('SA_DIS_COP')."&nbsp;&nbsp;&nbsp;'+ 0+'&nbsp;'+'".$socialads_config["currency"]."'+'</td>');
		jQuery('#dis_amt').html('<td >".JText::_('SHOWAD_NET_AMT_PAY')."&nbsp;&nbsp;&nbsp;".$cal."&nbsp;".$socialads_config["currency"]."'+'</td>');
	}
}

function applycoupon(){
if(jQuery('#coupon_chk').is(':checked'))
{
	if(jQuery('#coupon_code').val() =='')
		alert('".JText::_('ENTER_COP_COD')."');
	else
	{
	jQuery.ajax({
		url: '?option=com_socialads&task=getcoupon&coupon_code='+document.getElementById('coupon_code').value,
		type: 'GET',
		dataType: 'json',
		success: function(data) {
		amt=0;
		val=0;
		if(data != 0)
		{
			if(data[0].val_type == 1)
					val = (data[0].value/100)*".$cal.";
			else
					val = data[0].value;

			amt = round(".$cal."- val);
			if(amt <= 0)
				amt=0;

				jQuery('#dis_cop').show();
				jQuery('#dis_amt').html('<td>".JText::_('SHOWAD_NET_AMT_PAY')."&nbsp;&nbsp;&nbsp;'+ amt+'&nbsp;'+'".$socialads_config["currency"]."'+'</td>');
				jQuery('#dis_cop').html('<td >".JText::_('SA_DIS_COP')."&nbsp;&nbsp;&nbsp;'+ val+'&nbsp;'+'".$socialads_config["currency"]."'+'</td>');
				jQuery('#dis_amt').show();


		}
		else
			alert('\"'+document.getElementById('coupon_code').value +'\" ".JText::_('COP_EXISTS')."');


		}
	});
	}
}
}
function round(n) {
	return Math.round(n*100+((n*1000)%10>4?1:0))/100;
}
	function makealert(jpoints,amt){
		if((".$article_id.") == 0)
		{
			data=jpoints-amt;
			alert('".JText::_('CUR_POINT')." '+data);
			submitbutton('makepayment');
		}
		else
		{
			if(document.getElementById('chk_tnc').checked == true){
				data=jpoints-amt;
				alert('".JText::_('CUR_POINT')." '+data);
				submitbutton('makepayment');
				}
			else
				alert('".JText::_('AGREE')."');
			return false;
		}

	}
	function submitbutton(pressbutton) {
		var form = document.adminForm;

		if(pressbutton)
		{
			 	if(pressbutton!='makepayment')
				 	{
						submitform(pressbutton);
						return;
					}
				else if(pressbutton == 'makepayment')
					{
					  if(".$article_id." == 1){
				 		if(document.getElementById('chk_tnc').checked == true)
						{
							submitform(pressbutton);
							return;
						}
						else{
						alert('".JText::_('AGREE')."');
						return false;
						}
					}
					else {
					submitform(pressbutton);
					return;
					}
				}
		}
		else {
		submitform(pressbutton);
		return;
		}


	}//submitbutton() ends


	function submitform(pressbutton){
		 if (pressbutton) {
		 	document.adminForm.task.value = pressbutton;
		 }
		 if (typeof document.adminForm.onsubmit == 'function') {
		 	document.adminForm.onsubmit();
		 }
		 	document.adminForm.submit();
	} //submitform() ends

	function use_arb()
	{

		if(jQuery('#arbchk').is(':checked'))
			jQuery('#arb_flag').val('1');
		else
			jQuery('#arb_flag').val('');
	}



	function makepayment(){


	var cop_dis_opn_hide = 0;
				if(jQuery('#cop_tr').is(':visible') ) {

						var cop_text = document.getElementById('coupon_code').value;
						if(jQuery('#dis_cop').is(':hidden') && cop_text)
						{
							var cop_dis_opn_hide=1;
						}

				}
	if(".$article_id." == 1){
				 		if(document.getElementById('chk_tnc').checked != true)
						{
						alert('".JText::_('AGREE')."');
						return false;
						}
					}
	jQuery('#edit').attr('disabled','disabled');
	jQuery('#draft').attr('disabled', 'disabled');
	jQuery('#buy').attr('disabled', 'disabled');
	jQuery('#html-container').html('<div><span>".JText::_('PLEASE_WAIT_RE')."</span> <img src = \'".JUri::base()."components/com_socialads/images/loader_light_blue.gif\'   /></div>');
	 		jQuery.ajax({
					url: '?option=com_socialads&controller=showad&task=makepayment&arb_flag='+document.getElementById('arb_flag').value+'&cop='+document.getElementById('coupon_code').value+'&cop_dis_opn_hide='+cop_dis_opn_hide,
					type: 'GET',
					dataType: 'html',
					success: function(response)
					{
						var str_resp=response.toString();
						var aa=str_resp.search('coupon_discount_all');

						if(aa>-1)
						{
							window.location='".JRoute::_('index.php?option=com_socialads&view=managead')."';

						}
						jQuery('#html-container').html( response );




					}
			});
	}
	";

	$document->addScriptDeclaration($js);
}
?>
<?php
		//newly added for JS toolbar inclusion
		if(file_exists(JPATH_SITE . DS .'components'. DS .'com_community') and $socialads_config['show_js_toolbar']==1)
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
<!--form strts here for showing preview of an ad
<form action="" method="post" enctype="multipart/form-data" name="adminForm" > -->
	<!-- <div class="componentheading page-header"><h2><?php //echo JText::_('SHOWAD_ADPREVIEW')?></h2></div> -->
	<div class="review">
	<div><?php echo $this->preview; ?></div>
	<?php if($socialads_config['select_campaign']==0)
	{

		?>


	<table class="well table table-bordered">
		<tr>

				<?php
				$adMoreTr = 0;
				$td_key = '';
				$td_value = '';
				if($this->chargeoption==0)
				{
					//echo JText::_('SHOWAD_ADMODE_IMP');
					$td_key = JText::_('SHOWAD_ADMODE_IMP_KEY');
					$td_value = JText::_('SHOWAD_ADMODE_IMP_VALUE');
				}
				elseif($this->chargeoption==1)
				{
					//echo JText::_('SHOWAD_ADMODE_CLK');
					$td_key = JText::_('SHOWAD_ADMODE_CLK_KEY');
					$td_value = JText::_('SHOWAD_ADMODE_CLK_VALUE');
				}
				elseif($this->chargeoption>=2)
				{
					if($this->chargeoption==2)
					{
						//echo JText::_('SHOWAD_ADMODE_DATE');
						$td_key = JText::_('SHOWAD_ADMODE_DATE_KEY');
						$td_value = JText::_('SHOWAD_ADMODE_DATE_VALUE');
					}
					else if($this->chargeoption>2)
					{
						$td_key = JText::_('SHOWAD_ADMODE_CUSTOM_KEY');
						$td_value = $slablabel;

						if($this->sa_recuring != '1')
						{
							$adMoreTr = 1;
						?>
						<!--	 </td>
							</tr>
							<tr>
								<td class="">  -->
						<?php
							//echo JText::_('SA_RENEW_NO_RECURR').' '.$slablabel.' : '.$this->ad_totaldays;
						}
					}

				}

			?>
			<td class="" width="30%"><?php echo $td_key; ?></td>
			<td class="" width="30%"><?php echo $td_value; ?></td>
		</tr>

		<?php
		if($adMoreTr==1)
		{
		?>
			<tr>
				<td class=""><?php echo JText::_('SA_RENEW_NO_RECURR').' '.$slablabel ; ?></td>
				<td class=""><?php echo $this->ad_totaldays; ?></td>
			</tr>
		<?php
		}
		?>

		<tr>

				<?php

				$ad_chargeOpKey = JText::_('PRICE_DAY');
				$ad_chargeOpValue =  $socialads_config['date_price'] .' '. $socialads_config["currency"];
				if($this->chargeoption<2)
				{
					$ad_chargeOpKey = JText::_('SHOWAD_NUMBER_CLICKS');
					$ad_chargeOpValue =  $this->ad_totaldisplay;

				} ?>
			<td class=""><?php echo $ad_chargeOpKey; ?></td>
			<td class=""><?php echo $ad_chargeOpValue; ?></td>
		</tr>

		<tr>
		<?php
		if (!isset($this->ad_points))
		{?>
			<?php
			if($this->chargeoption == 1)
			{
				$ad_chargeOpKey = JText::_('SHOWAD_TOTAL_AMT');
				$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
			}
			else if($this->chargeoption == 0)
			{
				$ad_chargeOpKey = JText::_('SHOWAD_TOTAL_AMT');
				$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
			}
			else if($this->chargeoption >= 2)
			{
				$ad_chargeOpKey = JText::_('SHOWAD_TOTAL_AMT');
				$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
				$makecal= "makepayment();";
			}
			?>
			<td class=""><?php echo $ad_chargeOpKey; ?></td>
			<td class=""><?php echo $ad_chargeOpValue; ?></td>
		</tr>
		<?php
		} ?>
		<?php
		if (isset($this->ad_points))
		{ ?>
		<tr>
			<td class=""><?php echo JText::_('POINTS');; ?></td>
			<td class=""><?php echo $cal; ?></td>
		</tr>
		<?php $makecal='makepayment();';
		}?>

		<tr id= "dis_cop" style="display:none">
			<td class=""><?php echo JText::_('SA_DIS_COP'); ?></td>
			<td class=""><?php echo  '0'; ?>&nbsp;<?php echo $socialads_config["currency"]; ?></td>
		</tr>

		<tr>
			<td class="">
				<label class="checkbox"><input type = 'checkbox' id = "coupon_chk" name = "copchk" value="" size= "10" onchange='show_cop()' /> <?php echo JText::_('HAVE_COP');?> </label>
			</td>
			<td class="">
				<span id = "cop_tr" style="display:none;">
					<span class=""><?php echo JText::_('CUPCODE');?> <input type = "text" class="input-small" id = "coupon_code" name = "cop" value="" size= "10"/> <button type="button" class="btn btn-primary" onclick="applycoupon()"><?php echo JText::_('APPLY');?></button>
					</span>
			</td>

		</tr>
		<tr id= "dis_amt">
			<td class=""><?php echo JText::_('SHOWAD_NET_AMT_PAY'); ?></td>
			<td class=""><?php echo $cal;?>&nbsp;<?php echo $socialads_config["currency"]; ?></td>
		</tr>
		<?php
			//added by VM: only if not its content
		if(empty($ads_calledFromCkout))
		{
		?>
		<tr>
			<td class=""><?php echo JText::_('GATEWAY');; ?></td>
			<td class=""><?php echo $cal; ?> &nbsp; <?php if(!empty($this->gateway))	echo $this->ad_gateway; else echo $buildadsession->get('ad_gateway'); ?></td>
		</tr>
		<?php
		}
		?>
		<?php
		if(($this->chargeoption>2)	)
		{

		?>

		<?php
				$checked='';
				$enforceaarb=0;
				$arbstyle="display:block;";
				$arbstylelabel="display:none;";
				$checked="checked='true'";
			if($this->arb_enforce)
			{
				$checked="checked='true'";
				$enforceaarb=1;
				if(!$arb_support)
				{
					$arbstyle="display:none;";
					$arbstylelabel="display:block;";
				}
				else
				{
				$arbstyle="display:block;";
				$arbstylelabel="display:none;";
				}

			}
			else
			{
				if($arb_support)
				{
					$arbstyle="display:block;";

				}
			}

			if($this->sa_recuring == '1' &&  $socialads_config['recure_enforce'] != '1')
			{
			//if($socialads_config['recure_enforce']){
				?>
				<tr	id="arb_chk" >
					<td class=""><?php echo JText::_('PAYMENT_TYPE_ARB'); ?></td>
					<td class=""><?php echo $cal .' '. JText::_('PAYMENT_TYPE_ARB_LABEL');?> </td>
				</tr>
			<?php
			}
			?>

			<?php
		} ?>
		<!--if article is set at by admin then allowed to show the following box-->
		<?php
		if($socialads_config['article'] == 1)
		{	?>
		<tr style="display:block;">
			<?php
		}
		else
		{ 	?>
		<tr style="display:none;">
		<?php
		} ?>
				<td colspan='2'>
					<input type="checkbox" name=chk_tnc id="chk_tnc" />
						<?php $link = JUri::root().'index.php?option=com_content&tmpl=component&view=article&id='.$socialads_config['tnc'];?>
						<a rel="{handler: 'iframe', size: {x: 500, y: 300}}" href="<?php echo $link; ?>" class="modal">
								<?php echo JText::sprintf('SA_TNC', $sitename); ?>
						</a>
				</td>
		</tr>
	</table>

		<div class="form-actions">
		<?php
		//added by VM: only if not its content

		if(empty($ads_calledFromCkout))
		{
		?>
			<button id="buy" type="button" class="btn btn-success" onclick="<?php echo $makecal; ?>"><?php echo JText::_('SHOWAD_BUY');?></button>
		<?php
		}
		?>
			<button id="edit" type="button" class="btn btn-warning" onclick="submitbutton('editad');"><?php echo JText::_('SHOWAD_EDIT');?></button>
			<button id="draft" type="button" class="btn btn-danger" onclick="submitbutton('draft');"><?php echo JText::_('SHOWAD_DRAFT');?></button>
		</div>


	<?php
	}
	else
	{    ?>
		<div>
	 <?php echo $this->loadTemplate('showads_camp');  ?>
		</div>

	<?php
	}//if else ends ?>

	</div><!--div ends here-->
<input type="hidden" name="arb_flag" id="arb_flag" value="<?php echo  ($this->sa_recuring == '1' || $socialads_config['recure_enforce'] == '1')? '1': '0'; ?>">
		<input type="hidden" name="option" value="com_socialads" />
		<input type="hidden" name="view" value="showad" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="showad" />


<!--</form>form ends here for showing preview of an ad-->
<div id="html-container"></div>
<div style="clear:both;"></div>
</div><!--techjoomla-bootstrap ends-->
