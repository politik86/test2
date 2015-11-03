<?php

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtmlBehavior::framework();

// load config file
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

// fetch ad detail
if (empty($order_id))
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
$adDetail = $socialadshelper->getOrderAndAdDetail($order_id,1);

$this->chargeoption = $adDetail['ad_payment_type'];
//$this->ad_totaldisplay = $adDetail['ad_credits'];  // no of clicks or impression
$this->ad_totaldisplay = $adDetail['ad_credits_qty'];  // no of clicks or impression


// VM:: hv to add and code for jomsical points ( we are looking later for jomscial points)
$gatwayName = 'bycheck';
$plugin = JPluginHelper::getPlugin( 'payment',$gatwayName);

if (0 && $socialads_config['select_campaign']==0)
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
	if (isset($pluginParams->points))
	{
		if ($pluginParams->points=='point')
		{
			$points1=1;
			//$points1=$this->get('JomSocialPoints');
			$this->assignRef( 'ad_points', $points1);
			$this->assignRef( 'ad_jconver',$pluginParams->conversion);
		}
	}
}
// If ends

// Get Showad model
JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
$showadmodel = new socialadsModelShowad();

// Get ad preview
$preview = $showadmodel->getAds($adDetail['ad_id']);
$this->preview = $preview ;

// Getting selected payment gateway list form component config
$selected_gateways = $socialads_config['gateways'];

// Getting GETWAYS
$dispatcher = JDispatcher::getInstance();
JPluginHelper::importPlugin('payment');

if (!is_array($selected_gateways))
{
	$gateway_param[] = $selected_gateways;
}
else
{
	$gateway_param = $selected_gateways;
}

if (!empty($gateway_param))
{
	$gateways = $dispatcher->trigger('onTP_GetInfo',array($gateway_param));
}
$this->ad_gateways = $gateways;
// Getting payment list END


// Get payment model
JLoader::import('payment', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
$paymodel = new socialadsModelpayment();
//$selectedGateway = !empty($adDetail['processor']) ? $adDetail['processor'] :(!empty($this->ad_gateways) ? $this->ad_gateways[0]->id : '');
//$payhtml = $paymodel->getHTML($selectedGateway,$order_id,1);
?>

<!--techjoomla-bootstrap -->
<div class="techjoomla-bootstrap ad_reviewAdmainContainer" >
	<fieldset class="sa_fieldset">
			<legend class="hidden-desktop"><?php echo JText::_('COM_SOCIALADS_CKOUT_ADS_SUMMERY'); ?></legend>
<?php echo ""?>

	<!-- for ad detail and preview -->
	<div class=" row-fluid show-grid">
		<!--ad detai start -->
		<div class="span6 well">
			<h4><?php echo JText::_('ADS_PAYMENT_REVIEW');?></h4>
			<div class="table-responsive">
			<table class=" table table-bordered">
				<tr>

				<?php
				$adMoreTr = 0;
				$td_key = '';
				$td_value = '';
				/*
				// click , impression or date
				if ($this->chargeoption	==0)
				{
					//echo JText::_('SHOWAD_ADMODE_IMP');
					$td_key = JText::_('SHOWAD_ADMODE_IMP_KEY');
					$td_value = JText::_('SHOWAD_ADMODE_IMP_VALUE');
				}
				elseif ($this->chargeoption==1)
				{
					//echo JText::_('SHOWAD_ADMODE_CLK');
					$td_key = JText::_('SHOWAD_ADMODE_CLK_KEY');
					$td_value = JText::_('SHOWAD_ADMODE_CLK_VALUE');
				}
				elseif ($this->chargeoption >= 2)
				{
					if ($this->chargeoption == 2)
					{
						//echo JText::_('SHOWAD_ADMODE_DATE');
						$td_key = JText::_('SHOWAD_ADMODE_DATE_KEY');
						$td_value = JText::_('SHOWAD_ADMODE_DATE_VALUE');
					}
					else if ($this->chargeoption > 2)
					{
						$td_key = JText::_('SHOWAD_ADMODE_CUSTOM_KEY');
						$td_value = $slablabel;

						// @TODO we have to supprt recurring payment ( skipped for now) ( Ask DJ for how to do)

						//if ($adDetail['sa_recuring'] != '1')
						//{
							//$adMoreTr = 1;
						//}
						//
					}

				}
				*/
				$slabDetails = array();
				if ($this->chargeoption > 2)
				{
					$slabDetails = $socialadshelper->getSlabDetails($this->chargeoption);
					$td_key = JText::_('SHOWAD_ADMODE_DEFAULT_KEY');
					$td_value = $slabDetails['label'];
				}
				switch($this->chargeoption)
				{
					case 0:  // impresssion
							$td_key = JText::_('SHOWAD_ADMODE_IMP_KEY');
							$td_value = JText::_('SHOWAD_ADMODE_IMP_VALUE');
					break;

					case 1:  // click
							$td_key = JText::_('SHOWAD_ADMODE_CLK_KEY');
							$td_value = JText::_('SHOWAD_ADMODE_CLK_VALUE');
					break;

					case 2:  //  date
							$td_key = JText::_('SHOWAD_ADMODE_DATE_KEY');
							$td_value = JText::_('SHOWAD_ADMODE_DATE_VALUE');
					break;

					case 7:
							$td_key = JText::_('SHOWAD_ADMODE_WEEK_KEY');
							$td_value = JText::_('SHOWAD_ADMODE_WEEK_VALUE');
					break;
					case 30:
							$td_key = JText::_('SHOWAD_ADMODE_MONTH_KEY');
							$td_value = JText::_('SHOWAD_ADMODE_MONTH_VALUE');
					break;

				/*	default:  // for slab
					$slabDetails = $socialadshelper->getSlabDetails($this->chargeoption);

						$td_key = JText::_('SHOWAD_ADMODE_DEFAULT_KEY');
						$td_value = $slabDetails['label'];*/


				}

				?>
				<td class="" width="30%"><?php echo $td_key; ?></td>
				<td class="" width="30%"><?php echo $td_value; ?></td>
			</tr>


			<?php
			$ad_chargeOpKey = JText::_('PRICE_DAY');
			$ad_chargeOpValue =  $socialads_config['date_price'] .' '. $socialads_config["currency"];
			if ($this->chargeoption < 2)
			{
				$ad_chargeOpKey =  ($this->chargeoption == 1) ?JText::_('SA_SHOWAD_NUMBER_CLICKS') : JText::_('SA_SHOWAD_NUMBER_IMPRESSION');
				$ad_chargeOpValue =  $this->ad_totaldisplay;  // no of clicks or impression
				?>
				<tr>
					<td class=""><?php echo $ad_chargeOpKey; ?></td>
					<td class=""><?php echo $ad_chargeOpValue; ?></td>
				</tr>
				<?php
			}
			elseif ($this->chargeoption == 2) // if days then show day count
			{
				$ad_dayOpKey = JText::_('SHOWAD_NUMBER_DAYS');
				$ad_dayOpValue =  $this->ad_totaldisplay;  // no of days

			?>
				<tr>
					<td class=""><?php echo $ad_dayOpKey; ?></td>
					<td class=""><?php echo $ad_dayOpValue; ?></td>
				</tr>
			<?php

			}
			else
			{
				?>
				<tr>
					<td class=""><?php echo JText::sprintf('SHOWAD_ADMODE_DEFAULT_SLAB_KEY',$slabDetails['label']); ?></td>
					<td class=""><?php echo $slabDetails['price'] .' '. $socialads_config["currency"]; ?></td>
				</tr>
				<?php
			}
			?>



			<tr>
			<?php
			// jomsocial points
			if (!isset($this->ad_points))
			{?>
				<?php
				$ad_chargeOpKey = JText::_('SHOWAD_TOTAL_AMT');
				$ad_chargeOpValue =  $adDetail['ad_original_amt'] .' '.$socialads_config["currency"];

				// charge option- click, impression, or day
				/*if ($this->chargeoption == 1)
				{
					$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
				}
				else if ($this->chargeoption == 0)
				{
					$ad_chargeOpValue =  $cal .' '.$socialads_config["currency"];
				}
				else if ($this->chargeoption >= 2)
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
			if (!empty($adDetail['ad_coupon']))
			{
				// get payment HTML
				JLoader::import('showad', JPATH_SITE.DS.'components'.DS.'com_socialads'.DS.'models');
				$showadmodel = new socialadsModelShowad();
				$adcop = $showadmodel->getcoupon($adDetail['ad_coupon']);

				if ($adcop)
				{
					if ($adcop[0]->val_type == 1) 		//discount rate
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
			<tr id= "dis_cop" style="">
				<td class=""><?php echo JText::_('SA_DIS_COP'); ?></td>
				<td class=""><?php echo  $cop_dis; ?>&nbsp;<?php echo $socialads_config["currency"]; ?></td>
			</tr>

			<?php $discountAmt = 450; ?>

			<!-- tax amount -->
			<tr id= "ad_tax" style="">
				<td class=""><?php echo JText::sprintf('SA_TAX_AMT',$tax[0]); ?></td>
				<td class=""><?php echo  $adDetail['ad_tax']	; ?>&nbsp;<?php echo $socialads_config["currency"]; ?></td>
			</tr>

			<!-- NET TOTAL AMOUNT after tax and coupon-->
			<tr id= "dis_amt">
				<td class=""><?php echo JText::_('SHOWAD_NET_AMT_PAY'); ?></td>
				<td class=""><?php echo $adDetail['ad_amount'];?>&nbsp;<?php echo $socialads_config["currency"]; ?>
				</td>
			</tr>
		</table>
		</div>		<!-- table-responsive -->
		</div> 		<!-- ad detail end -->

		<div class="span6" style="height: 310px;">
			<?php echo $this->preview; ?>
		</div>
	</div>

	<!-- show payment option start -->
	<div class="row-fluid">
		<div class="paymentHTMLWrapper well">

			<?php
			$paymentListStyle = '' ;
			$mainframe = JFactory::getApplication();
			if (!$mainframe->isAdmin() && $socialads_config['article'] == 1 && !empty($socialads_config['tnc']))
			{
				$paymentListStyle = 'display:none' ;
				?>
			<!-- TERMS AND CONDITION -->
			<div class="control-group">

				<input class="inputbox sa_terms_checkbox_style" type="checkbox" name="sa_accpt_terms" id="sa_termsCondCk" size="30" aria-invalid="false" onClick="paymentList_showHide()" >&nbsp;&nbsp;<?php  echo JText::_( 'COM_SOCIALADS_ACCEPT' ); ?>
				<?php
					//$termslink = "'".JRoute::_('index.php?option=com_content&tmpl=component&view=article&id='.$socialads_config['tnc'])."','_blank'";
					//onClick="window.open(<?php echo $termslink;
				?>
				<a href="<?php echo JUri::root().'index.php?option=com_content&tmpl=component&view=article&id='.$socialads_config['tnc'] ; ?>" class="" target="_blank">
					<span class="hasTip" title="<?php echo JText::_( 'COM_SOCIALADS_TERMS_CONDITION' ); ?>">
							<?php  echo JText::_( 'COM_SOCIALADS_TERMS_CONDITION' ); ?>
					</span>
				</a>
			</div>
			<br/>
			<?php
			}
			?>

			<?php
			if (!empty($adDetail['ad_amount']))
			{
			?>
				<div class="" id="sa_paymentlistWrapper" style="<?php echo $paymentListStyle?>">
				<div class="control-group " id="sa_paymentGatewayList">
					<?php
					$default = "";
					$lable = JText::_( 'ADS_SEL_GATEWAY' );
					$gateway_div_style=1;
					if (!empty($this->ad_gateways)) //if only one geteway then keep it as selected
					{
						$default = $this->ad_gateways[0]->id; // id and value is same
					}
					if (!empty($this->ad_gateways) && count($this->ad_gateways)==1) //if only one geteway then keep it as selected
					{
						$default=$this->ad_gateways[0]->id; // id and value is same
						$lable=JText::_( 'ADS_SEL_GATEWAY' );
						$gateway_div_style=1;  // to show payment radio btn even if only one payment gateway
					}
					?>

					<label for="" class="control-label"><h4><?php echo $lable ?> </h4></label>
					<div class="controls" style="<?php echo ($gateway_div_style==1)?"" : "display:none;" ?>">
						<?php
						if (empty($this->ad_gateways))
							echo JText::_( 'NO_PAYMENT_GATEWAY' );
						else
						{
							$default = ''; // removed selected gateway 26993
							$imgpath = JUri::root()."components/com_socialads/images/ajax.gif";
							$ad_fun = 'onChange=ad_gatewayHtml(this.value,'.$order_id.',1,"'.JText::_( "SA_PAYMENT_GATEWAY_LOADING_MSG" ).'","'.$imgpath.'")';
							$pg_list = JHtml::_('select.radiolist', $this->ad_gateways, 'ad_gateways', 'class="inputbox required" '.$ad_fun.'  ', 'id', 'name',$default,false);
							echo $pg_list;
						}
						?>
					</div>
					<?php
					if (empty($gateway_div_style))
					{
						?>
							<div class="controls qtc_left_top">
							<?php echo 	$this->ad_gateways[0]->name; // id and value is same ?>
							</div>
						<?php
					}
					?>
				</div> <!-- END OF control-group-->
				<!-- show payment hmtl form-->
				<div id="ad_payHtmlDiv">
				<?php
				/*// removed selected gateway 26993
					if (!empty($this->ad_gateways))
					{
						echo  $payhtml[0];
					}*/
				?>
				</div>
			</div>
			<?php
			}
			else
			{
				?>
				<div id="ad_payHtmlDiv">
				<form method="post" name="sa_freePlaceOrder" class="" id="sa_freePlaceOrder">
				<div class="techjoomla-bootstrap" >


					<input type="hidden" name="option" value="com_socialads">
					<input type="hidden" name="controller" value="buildad" />
					<input type="hidden" id="task" name="task" value="sa_processFreeOrder">
					<input type="hidden" name="order_id" value="<?php echo $order_id; ?>">

					<div class="form-actions " >
						<input type="submit" class="btn btn-success btn-large" value="<?php echo JText::_('SA_CONFORM_ORDER'); ?>">
					</div >


				</div>
				</form>
				</div>

				<?php

			}
			?>
		</div> <!-- end of paymentHTMLWrapper-->
	</div>
	<!-- show payment option end -->
	</fieldset>
</div>
