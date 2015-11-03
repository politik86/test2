<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
//require(JPATH_SITE.DS."components".DS."com_socialads".DS."views".DS."showad".DS."tmpl".DS."default.php");
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

$user =JFactory::getUser();
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

if ($socialads_config['select_campaign']=='0')
{
	?>
	<div class="alert alert-block">
	<?php echo JText::_('AD_NO_AUTH_SEE'); ?>
	</div>
	</div><!--techjoomla bootstrap ends if not logged in-->
	<?php
return false;
}

$singleselect = array();

			$singleselect[] = JHtml::_('select.option','0', JText::_('SA_NO'));
			$singleselect[] = JHtml::_('select.option','1', JText::_('SA_YES'));

?>
<script type="text/javascript">
	function ad_checkforZeroAndalpha(ele,allowedChar,msg)
	{
		if(ele.value  && ele.value != 0)
		{
			ad_checkforalpha(ele,allowedChar,msg)
		}
		else if(ele.value == 0)
		{
			alert("<?php echo JText::_('COM_SOCIALADS_MIN_AMT_SHOULD_GREATER_MSG'); ?>");
			ele.value='';
		}
	}

	function add_coupon_pay(coupon_code,value)
	{
		techjoomla.jQuery.ajax({
		url: '?option=com_socialads&controller=payment&task=add_payment&coupon_code='+coupon_code+'&value='+value,
		type: 'GET',
		dataType: 'json',
		success: function(data) {
			if(data != 0)
			{
				window.location = "?option=com_socialads&view=billing";
			}
		}
	});
}


	//function for validation on 'yes' 'no' of coupon
	function show_cop(rad,mim_bal)
	{
		var amt = document.getElementById('amount').value;
		if(amt=='' || isNaN(amt))
		{
			alert("<?php echo JText::_('COM_SOCIALADS_PAYMENT_ENTER_CORRECT_AMT'); ?>");
			document.getElementById('amount').focus();
			techjoomla.jQuery('input[name=coupon_chk]').val([0]);

			return false;
		}
		else if(parseInt(amt) < parseInt(mim_bal))
		{
			alert("<?php echo JText::_('COM_SOCIALADS_MIN_AMT_TO_PAY'); ?>"+mim_bal);
			document.getElementById('amount').focus();
			techjoomla.jQuery('input[name=coupon_chk]').val([0]);
			return false;
		}

		if(rad==1)
		{
			document.getElementById('cop_tr').style.display="block";
		}
		else
		{
			document.getElementById('cop_tr').style.display="none";
		}

	}


function round(n) {
	return Math.round(n*100+((n*1000)%10>4?1:0))/100;
}

function applycoupon()
{
	var cal = document.getElementById('amount').value;
	var coupon_code = document.getElementById('coupon_code').value;

	if(techjoomla.jQuery('#coupon_code').val() =='')
		alert("Enter Coupon Code");
	else
	{
	techjoomla.jQuery.ajax({
		url: '?option=com_socialads&task=getcoupon&coupon_code='+coupon_code,
		type: 'GET',
		dataType: 'json',
		success: function(data) {


		var amt=0;
		var val=0;
		if(data != 0)
		{
			if(data[0].val_type == 1)
					val = (data[0].value/100)*cal;
			else
					val = data[0].value;

			amt = round(cal - val);

			if(amt <= 0)
				amt=0;
				techjoomla.jQuery('#coupon_value').html(val+" <?php echo $socialads_config['currency'] ?>");
				techjoomla.jQuery('#coupon_discount').show();
				techjoomla.jQuery('#dis_amt').html(amt+" <?php echo $socialads_config['currency'] ?>");
				techjoomla.jQuery('#dis_amt1').show();

			if(amt == 0)
			{
				techjoomla.jQuery('#pay_gateway').hide();
				techjoomla.jQuery('#coupon').html('<input type="button" class="button btn btn-primary" id="add_coupon" value="<?php echo JText::_('SUBMIT'); ?>" onclick="add_coupon_pay(\''+coupon_code+'\',\''+val+'\')">');
				techjoomla.jQuery('#coupon_div').show();
			}
		}
		else
			//alert("\""+document.getElementById('coupon_code').value +" Coupon does not exists");
			alert(document.getElementById('coupon_code').value + " <?php echo JText::_('COM_SOCIALADS_COP_NOT_EXISTS'); ?>");
		},
		error: function(response)
		{
			// show ckout error msg
			//techjoomla.jQuery('#qtcShowCkoutErrorMsg').show();
			console.log(' ERRORRR' );
		}
	});
	}
}


function makepayment(pay_method)
{
	var cal = document.getElementById('amount').value;
	var cop_dis_opn_hide = 0;
	if(techjoomla.jQuery('#cop_tr').is(':visible') )
	{
		var cop_text = document.getElementById('coupon_code').value;
		if(techjoomla.jQuery('#dis_amt1').is(':hidden') && cop_text)
		{
			var cop_dis_opn_hide=1;
		}

	}

	if(cal=='' || isNaN(cal))
	{
		alert("Please enter correct amount");
		document.getElementById('amount').focus();
		return false;
	}
	techjoomla.jQuery.ajax({
			url: '?option=com_socialads&controller=payment&task=makepayment&processor='+pay_method+'&amount='+cal+'&cop='+document.getElementById('coupon_code').value+'&cop_dis_opn_hide='+cop_dis_opn_hide,
			type: 'GET',
			dataType: 'html',
			beforeSend: function()
			{
				var loadingMsg = "<?php echo JText::_( "SA_PAYMENT_GATEWAY_LOADING_MSG" ) ?>";
				var imgpath = "<?php echo JUri::root().'components/com_socialads/images/ajax.gif'; ?>";
				techjoomla.jQuery('#pay_gateway').after('<div class=\"com_socialad_ajax_loading\"><div class=\"com_socialad_ajax_loading_text\">'+loadingMsg+' ...</div><img class=\"com_socialad_ajax_loading_img\" src="'+imgpath+'"></div>');

			},
			complete: function() {
				techjoomla.jQuery('.com_socialad_ajax_loading').remove();

			},
			success: function(response)
			{
				var str_resp=response.toString();
				var aa=str_resp.search('coupon_discount_all');

				if(aa>-1)
				{
					window.location.href='index.php?option=com_socialads&view=payment';
				}
				techjoomla.jQuery('#html-container').html( response );
			}
	});
}

</script>



<div class="page-header">
	<h2><?php echo JText::_('MAKE_PAYMENT'); ?></h2>
</div>
<form name="adminForm" class="form-validate form-horizontal" id="hello" action="" method="post" class="form-validate" enctype="multipart/form-data">
	<?php
	$gatewayselect = array();
	foreach($this->gatewayplugin as $gateway)
	{
		if(!in_array($gateway->element,$socialads_config['gateways']))
		continue;
		$gatewayname = ucfirst(str_replace('plugpayment', '',$gateway->name));
		$gatewayselect[] = JHtml::_('select.option',$gateway->element, $gatewayname);
	}
	?>


	<div class="control-group">

			<label class="control-label"><span style="color:red" class="star">*&nbsp;</span><?php echo JText::_('AMOUNT'); ?> </label>
			<div class="controls">
				<div class="input-append ">
				<input id="amount" name="amount" type="text" onkeyup="ad_checkforZeroAndalpha(this,'46','<?php echo JText::_('ADS_ENTER_NUMERICS'); ?>')" class="input-mini required" value=""   ><span class="add-on"><?php echo $socialads_config['currency']; ?></span>
				</div>
			</div>

	</div>

	<div class="control-group">
		<label class="control-label"><?php echo JText::_('HAVE_COP');?></label>
		<?php if(JVERSION > 3.0){  ?>
		<div class="controls">
			<?php  } ?>
			<?php echo JHtml::_('select.radiolist', $singleselect, "coupon_chk", 'onchange="show_cop(this.value,\''.$socialads_config['camp_currency_pre'].'\')"', "value", "text",0);?>
		<?php if(JVERSION > 3.0){  ?> </div>  	<?php  } ?>
	</div>

	<div id="cop_tr" style="display:none" class="control-group">
		<div  class="controls">

		<input type="text" class="input-medium" id="coupon_code" name="cop" placeholder="<?php echo JText::_('CUPCODE'); ?>" value="" size= "10"/> <button type="button" class="btn btn-success" onclick="applycoupon()"><?php echo JText::_('APPLY');?></button>

		</div>
	</div>

	<div id="coupon_discount" style="display:none" class="control-group ">
			<label class="control-label"><?php echo JText::_('SA_DIS_COP');?></label>

			<div id="coupon_value" class="controls qtc_controls_text">

			</div>
	</div>

	<div id="dis_amt1" style="display:none" class="control-group ">
			<label class="control-label"><?php echo JText::_('FINAL_AMOUNT');?></label>

			<div id="dis_amt" class="controls qtc_controls_text">

			</div>
	</div>


	<div class="control-group" id="pay_gateway">


		<label class="control-label"><span style="color:red" class="star">*</span><?php echo JText::_('SELECT_GATEWAY');?></label>
		<?php if(JVERSION > 3.0){  ?>
		<div class="controls">
			<?php  } ?>
			<?php
			//$v="onclick=\"" .$makecal."\"";
		if(!empty($gatewayselect))
			echo JHtml::_('select.radiolist', $gatewayselect, "payment_gateway", 'onclick="makepayment(this.value)"', "value", "text");
			//$pg_list = JHtml::_('select.radiolist', $gateways, 'gateways', 'class="inputbox required" id="gateways"', 'id', 'name');
            //                   echo $pg_list;

		else
			echo JText::_('NO_GATEWAY_PLUG');
		?>

		<?php if(JVERSION > 3.0){  ?> </div>  	<?php  } ?>
	</div>
	<div class="control-group" id="coupon_div" style="display:none">



		<div class="controls" id="coupon">


		</div>
	</div>
	<!--<button id="buy" type="button" class="button" onclick="makepayment()"><?php echo JText::_('SHOWAD_BUY');?></button>-->
	<input type="hidden" name="arb_flag" id="arb_flag" value="<?php echo  ($this->sa_recuring == '1' || $socialads_config['recure_enforce'] == '1')? '1': '0'; ?>">
	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="controller" value="" />
	<input type="hidden" name="task" value="save" />


	<input type="hidden" name="arb_flag" id="arb_flag" value="<?php echo  ($this->sa_recuring == '1' || $socialads_config['recure_enforce'] == '1')? '1': '0'; ?>">
	<?php echo JHtml::_('form.token'); ?>
</form>
<div id="html-container" ></div>
</div>
