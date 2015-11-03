<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtmlBehavior::framework();

// load config file
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

// fetch ad detail
if(empty($order_id))
{
	?>
	<div class="well" >
		<div class="alert alert-error">
			<span ><?php echo JText::_('SA_UNABLE_TO_TRACK_ORDER_ID'); ?> </span>
		</div>
	</div>
	<?php
	return false;
}

$socialadshelper = new socialadshelper();
$adDetail = $socialadshelper->getOrderAndAdDetail($order_id);
//var_dump($adDetail); die;
$this->chargeoption = $adDetail['ad_payment_type'];
$this->ad_totaldisplay = $adDetail['ad_credits'];  // no of clicks or impression



// VM:: hv to add and code for jomsical points ( we are looking later for jomscial points)
$gatwayName = 'bycheck';
$plugin = JPluginHelper::getPlugin( 'payment',$gatwayName);

if( 0 && $socialads_config['select_campaign']==0)
{
	$pluginParams = json_decode( $plugin->params );
	$this->assignRef( 'ad_gateway', $pluginParams->plugin_name);
		//added by sagar//
	$arb_enforce='';
	$this->assignRef( 'arb_enforce', $pluginParams->arb_enforce);
	$arb_enforce='';
	$this->assignRef( 'arb_support', $pluginParams->arb_support);
	//end added by sagar//
	$points1=0;
	if(isset($pluginParams->points))
	{
		if($pluginParams->points=='point')
		{
			$points1=1;
			//$points1=$this->get('JomSocialPoints');
			$this->assignRef( 'ad_points', $points1);
			$this->assignRef( 'ad_jconver',$pluginParams->conversion);
		}
	}
}//if ends

// get ad preview
// get payment HTML
//JLoader::import('showad',JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');

$showadmodel = new socialadsModelShowad();

//$preview = $showadmodel->getAds($adDetail['ad_id']);
//$this->preview = $preview ;

// getting payment list START
$selected_gateways = $socialads_config['gateways'];

//getting GETWAYS
$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin('payment');

if(  !is_array($selected_gateways) )
{
	$gateway_param[] = $selected_gateways;
}
else
{
	$gateway_param = $selected_gateways;
}

if(!empty($gateway_param))
{
	$gateways = $dispatcher->trigger('onTP_GetInfo',array($gateway_param));
}
$this->ad_gateways = $gateways;
// getting payment list END


// get SELECTED paymen plugin html
JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
$paymodel = new socialadsModelpayment();
$selectedGateway = !empty($adDetail['processor']) ? $adDetail['processor'] :(!empty($this->ad_gateways) ? $this->ad_gateways[0]->id : '');

if(empty($dontLoadPayDetails))
{  // in invoice dont load payment releated things
	//$payhtml = $paymodel->getHTML($selectedGateway,$order_id);   // vm: i think,  no need of this line
}

if(empty($sa_displayblocks))
	$sa_displayblocks=array('invoiceDetail'=>1,'billingDetail'=>1,'adsDetail'=>1);

?>

<!--techjoomla-bootstrap -->
<div class="techjoomla-bootstrap ad_reviewAdmainContainer" >
<?php echo ""?>
	<!-- Order detail block-->
	<?php
	if($sa_displayblocks['invoiceDetail']==1)
	{
		?>
	<div class=" row-fluid" style="width: 80%;">
		<div class="" >
			<h4 style="background-color:#cccccc"><?php echo JText::_('ADS_INVOICE_DETAILS');?></h4>
			<table class="table table-condensed adminlist" >
				<tr>
					<td><?php echo JText::_('ADS_INVOICE_ID');?></td>
					<td><?php echo $displayOrderid = sprintf("%05s", $order_id); //$orderDetails->order_id;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_INVOICE_DATE');?></td>
					<td><?php echo $orderDetails->mdate;?></td>
				</tr>
				<?php
					if(!empty($billinfo->vat_number)){
				?>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_VAT_NUM');?></td>
					<td><?php echo $orderDetails->vat_number;?></td>
				</tr>
				<?php } ?>
				<tr>
					<td><?php echo JText::_('ADS_INVOICE_AMOUNT');?></td>
					<td><?php echo $orderDetails->ad_amount;?></td>
				</tr>
				<!--
				<tr>
					<td><?php //echo JText::_('ADS_INVOICE_EMAIL');?></td>
					<td><?php //echo $orderDetails->zipcode;?></td>
				</tr> -->
				<tr>
					<td><?php echo JText::_('ADS_INVOICE_STATUS');?></td>
					<td><?php echo $orderstatus;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_INVOICE_PREPROCESSOR');?></td>
					<td><?php $gtway= !empty($adDetail['processor']) ? $adDetail['processor']:'' ;

						$plugin = JPluginHelper::getPlugin( 'payment',$gtway);
						$pluginParams = json_decode( $plugin->params );
						//$this->assignRef( 'ad_gateway', $pluginParams->plugin_name);
						echo $pluginParams->plugin_name;
						//echo ucfirst($gtway); ?>
					</td>
				</tr>

			</table>

		</div> <!-- END OF SPAN6-->
	</div>

	<?php
	}
		?>
	<!-- END Order detail block </div>
	<div class=" row-fluid ">-->

	<div class=" row-fluid " >
		<?php
		if($sa_displayblocks['billingDetail']==1)
		{
		?>
		<div class="" style="float:left;width:40%;">
			<h4 style="background-color:#cccccc"><?php echo JText::_('ADS_BILLING_INFO');?></h4>
			<table class="table table-condensed adminlist" >
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_FNAM');?></td>
					<td><?php echo $billinfo->firstname;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_LNAM');?></td>
					<td><?php echo $billinfo->lastname;?></td>
				</tr>
				<?php
					if(!empty($billinfo->vat_number)){
				?>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_VAT_NUM');?></td>
					<td><?php echo $billinfo->vat_number;?></td>
				</tr>
				<?php } ?>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_ADDR');?></td>
					<td><?php echo $billinfo->address;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_ZIP');?></td>
					<td><?php echo $billinfo->zipcode;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_COUNTRY');?></td>
					<td><?php echo $billinfo->country_code;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_STATE');?></td>
					<td><?php echo $billinfo->state_code;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_CITY');?></td>
					<td><?php echo $billinfo->city;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_PHON');?></td>
					<td><?php echo $billinfo->phone;?></td>
				</tr>
				<tr>
					<td><?php echo JText::_('ADS_BILLIN_EMAIL');?></td>
					<td><?php echo $billinfo->user_email;?></td>
				</tr>
			</table>

		</div> <!-- END OF SPAN6-->

		<?php
		}
		?>
		<?php
		if($sa_displayblocks['adsDetail']==1)
		{
		?>
		<!--ad detai start -->
		<div class="" style="float:left;width:40%;">
			<h4 style="background-color:#cccccc"><?php echo JText::_('ADS_INVOICE_AD_DETAIL');?></h4>
			<table class=" table table-bordered">
			<tr>

				<?php
				$adMoreTr = 0;
				$td_key = '';
				$td_value = '';

				// click , impression or date
				if($this->chargeoption	== 0)
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
				elseif($this->chargeoption >= 2)
				{
					if($this->chargeoption == 2)
					{
						//echo JText::_('SHOWAD_ADMODE_DATE');
						$td_key = JText::_('SHOWAD_ADMODE_DATE_KEY');
						$td_value = JText::_('SHOWAD_ADMODE_DATE_VALUE');
					}
					else if($this->chargeoption > 2)
					{
						$td_key = JText::_('SHOWAD_ADMODE_CUSTOM_KEY');
						$td_value = $slablabel;

						// @TODO we have to supprt recurring payment ( skipped for now) ( Ask DJ for how to do)
						/*
						if($adDetail['sa_recuring'] != '1')
						{
							$adMoreTr = 1;
						}
						*/
					}

				}

				?>
				<td class="" width="30%"><?php echo $td_key; ?></td>
				<td class="" width="30%"><?php echo $td_value; ?></td>
			</tr>

			<tr>
				<?php

				$ad_chargeOpKey = JText::_('PRICE_DAY');
				$ad_chargeOpValue =  $socialads_config['date_price'] .' '. $socialads_config["currency"];
				if($this->chargeoption < 2)
				{
					$ad_chargeOpKey = JText::_('SHOWAD_NUMBER_CLICKS');
					$ad_chargeOpValue =  $this->ad_totaldisplay;  // no of clicks or impression

				} ?>
				<td class=""><?php echo $ad_chargeOpKey; ?></td>
				<td class=""><?php echo $ad_chargeOpValue; ?></td>
			</tr>



			<tr>
			<?php
			// jomsocial points
			if (!isset($this->ad_points))
			{?>
				<?php
				$ad_chargeOpKey = JText::_('SHOWAD_TOTAL_AMT');
				$ad_chargeOpValue =  $adDetail['ad_original_amt'] .' '.$socialads_config["currency"];

				// charge option- click, impression, or day
				/*if($this->chargeoption == 1)
				{
					$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
				}
				else if($this->chargeoption == 0)
				{
					$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
				}
				else if($this->chargeoption >= 2)
				{
					$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
					$makecal= "makepayment();";
				}*/
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
				<td class=""><?php echo $adDetail['ad_original_amt']; ?></td>
			</tr>
			<?php $makecal='makepayment();';
			}?>

		<?php
			$cop_dis = 0;
			if(!empty($adDetail['ad_coupon']))
			{
				// get payment HTML
				JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
				$showadmodel = new socialadsModelShowad();
				$adcop = $showadmodel->getcoupon($adDetail['ad_coupon']);

				if($adcop)
				{
					if($adcop[0]->val_type == 1) 		//discount rate
					{
						$cop_dis = ($adcop[0]->value/100) * $adDetail['ad_original_amt'];
					}
					else
						$cop_dis = $adcop[0]->value;
				}
				else
				{
					$cop_dis = 0;
				}
			}

			$discountedPrice = $adDetail['ad_original_amt'] - $cop_dis;
			?>


			<!-- coupon discount display:block-->
			<tr id= "" style="">
				<td class=""><?php echo JText::_('SA_DIS_COP'); ?></td>
				<td class=""><?php echo  $cop_dis; ?>&nbsp;<?php echo $socialads_config["currency"]; ?></td>
			</tr>

			<?php $discountAmt = 450; ?>

			<!-- tax amount -->
			<tr id= "ad_tax" style="">
				<td class=""><?php echo JText::sprintf('SA_TAX_AMT');//,$tax[0]); ?></td>
				<td class=""><?php echo  $adDetail['ad_tax']	; ?>&nbsp;<?php echo $socialads_config["currency"]; ?></td>
			</tr>

			<!-- NET TOTAL AMOUNT after tax and coupon-->
			<tr id= "">
				<td class=""><?php echo JText::_('SHOWAD_NET_AMT_PAY'); ?></td>
				<td class=""><?php echo $adDetail['ad_amount'];?>&nbsp;<?php echo $socialads_config["currency"]; ?>
				</td>
			</tr>
		</table>

		</div>
		<!-- ad detail end -->
		<?php
		}
		?>

	</div>


</div>
