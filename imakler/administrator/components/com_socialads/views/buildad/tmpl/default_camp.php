<?php
// no direct access

jimport( 'joomla.html.parameter' );
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");
$input=JFactory::getApplication()->input;
          //$post=$input->post;
	$document = JFactory::getDocument();
	$singleselect = array();
	$singleselect[] = JHtml::_('select.option','value', JText::_('SELECT_OPTION'));

	foreach($socialads_config['pricing_opt'] as $k=> $v){
		if($v == 0)
			$singleselect[] = JHtml::_('select.option','0', JText::_('IMPR'));
		elseif($v == 1)
			$singleselect[] = JHtml::_('select.option','1', JText::_('CLICK'));
	}

			$campselect = array();
			$campselect[] = JHTML::_('select.option','0',JText::_('SELECT_CAMP'));

			foreach($this->camp_dd as $camp)
			{
				$campname = ucfirst(str_replace('plugpayment', '',$camp->campaign));
				//static options($arr, $optKey= 'value', $optText= 'text', $selected=null, $translate=false)
				$campselect[] = JHtml::_('select.option',$camp->camp_id, $campname);
			}

			$def = $this->cname;

			if($input->get('frm','','STRING'))
			{
				$def = $this->ad_camp;
				$bid = $this->bid_value;
			}

	$jszone = "	function getzone_price()
		{


					var click_price=0;
					var click_date=0;
					var click_imp=0;
					var a = document.getElementById('pricing_opt');
					var bid_val = document.getElementById('bid_div');
					var val = a.options[a.selectedIndex].value;

			if(".$socialads_config['zone_pricing']."!=0)
			{
				click_price		= document.getElementById('pric_click').value;

				click_imp		= document.getElementById('pric_imp').value;
			}
			else if(".$socialads_config['zone_pricing']."==0)
			{
				click_price	= ".$socialads_config['clicks_price'].";

				click_imp		= ".$socialads_config['impr_price'].";
			}


			jQuery('#click_span').html('".JText::_('RATE_PER_CLICK')."'+click_price+' ".$socialads_config['currency']." ');
			jQuery('#imps_span').html('".JText::_('RATE_PER_IMP')."'+click_imp+' ".$socialads_config['currency']." ');


				if(val==1)
				{
						document.getElementById('click').style.display='block';
						document.getElementById('imps').style.display='none';

				}
				else if(val==0)
				{
				document.getElementById('imps').style.display='block';
				document.getElementById('click').style.display='none';
				}
			}
			";

			$document->addScriptDeclaration($jszone);

?>

<script type="text/javascript">

		function new_camp(){
			//if click on new button reset the value of campaign select box to 0
			document.getElementById('camp').value="0";
			var a = document.getElementById("new_campaign");
			a.style.display="block";
		}


</script>
<div class="techjoomla-bootstrap">
	<fieldset class="sa_fieldset">
		<legend class="hidden-desktop"><?php echo JText::_('PRICING'); ?></legend>
	<div class="form-horizontal">



		<div class="control-group">

		<?php
			if ($this->special_access)
			{

				$publish1=$publish2='';
				if(!empty($this->addata_for_adsumary_edit->ad_noexpiry))
				{
					if($this->addata_for_adsumary_edit->ad_noexpiry)
					{
						$publish1='checked';
					}
					else
					{
						$publish2='checked';
					}
				}
				else
				{
					$publish2='checked';
				}
			?>

				<div class="unlimited_adtext alert alert-info"><?php echo JText::_('UNLIMITED_AD'); ?></div>

				<label class="control-label" for="type" title="<?php echo JText::_(''); ?>">
					<?php echo JText::_('COM_SA_UNLIMITED_AD'); ?>
				</label>

				<div id="review" class="controls input-append unlimited_yes_no">
					<input type="radio" name="unlimited_ad" id="unlimited_ad1" value="1" <?php echo $publish1;?> />
					<label class="first btn <?php echo $publish1_label;?>" for="unlimited_ad1"><?php echo JText::_('SA_YES');?></label>
					<input type="radio" name="unlimited_ad" id="unlimited_ad2" value="0" <?php echo  $publish2;?> />
					<label class="last btn <?php echo $publish2_label;?>" for="unlimited_ad2"><?php echo JText::_('SA_NO'); ?></label>
				</div>
			<?php
			}
			?>

	</div>


		<div class="control-group">

					<label class="control-label" for=""><?php echo JHTML::tooltip(JText::_('SELECT_CAMP_TOOLTIP'), JText::_('SELECT_CAMP'), '', JText::_('SELECT_CAMP'));?></label>
				 <div class="controls">
					<?php echo JHTML::_('select.genericlist', $campselect, "camp",'class="chzn-done" onchange="hideNewCampaign()"', "value", "text", $this->addata_for_adsumary_edit->camp_id);?>

					<?php if(empty($this->cname)) { ?>
							<button type="button" class="btn btn-primary" onclick="new_camp()"><?php echo JText::_('NEW');?></button>

					<?php } ?>
				</div>
		</div>

<?php //if edit ad-- show the campaign name and value box if stored earlier
	if(empty($this->cname) && $this->ad_camp && $this->ad_value)
	$show_new_campaign_box = 'style="display:block"';
	else
	$show_new_campaign_box = 'style="display:none"';

 ?>
		<div id="new_campaign" <?php echo $show_new_campaign_box; ?> class="control-group">
			<div class="controls">
				<div >

				<div class="form-inline">

								<input type="text" class="input-small" id="camp_name" name="camp_name" placeholder="<?php echo JText::_('CAMPAIGN_NAME');?>" value="<?php echo $this->ad_camp; ?>">

								<div class="input-append">
									<input type="text" class="input-mini" id="camp_amount" name="camp_amount" placeholder="<?php echo JText::_('DAILY_BUDGET');?>" value="<?php echo $this->ad_value;  ?>">
									<span class="add-on"><?php echo $socialads_config['currency'];?></span>
								</div>

				</div>
				</div>
			</div>
		</div>

		<div class="control-group">
			<?php

			?>
					<label class="control-label" for=""><?php echo JHTML::tooltip(JText::_('SELECT_METHOD_TOOLTIP'), JText::_('SELECT_METHOD'), '', JText::_('SELECT_METHOD'));?></label>
				<div class="controls">
							<?php echo JHTML::_('select.genericlist', $singleselect, "pricing_opt", 'class="chzn-done"  onchange=getzone_price()', "value", "text",$this->addata_for_adsumary_edit->ad_payment_type )?>
				</div>
				<div class="controls">
				<div id="click"  style="display:none"><p class="text-info"><span id="click_span"></span></p></div>
				<div id="imps"   style="display:none" ><p class="text-info"><span id="imps_span"></div>
				</div>
		</div>

		<?php /* if($socialads_config['bidding']==1) { ?>

		<div class="control-group" id="bid_div">
			<label class="control-label" for=""><?php echo JText::_('BID_VALUE');?></label>

			<div class="controls">
							<div class="input-append ">
								<input type="text" class="input-mini" id="bid_value" name="bid_value" value="<?php echo (JRequest::getVar('frm')=='editad')? $bid : ''; ?>" placeholder="<?php echo JText::_('VALUE');?>">
								<span class="add-on"><?php echo JText::_('USD'); ?></span>
							</div>
			</div>

		</div>

		<?php } */?>

	</div>
	</fieldset>

</div>











