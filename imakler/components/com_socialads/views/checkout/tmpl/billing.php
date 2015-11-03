<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die( 'Restricted access' );
//JHtml::_('behavior.formvalidation');
//jimport( 'joomla.form.formvalidator' );
JHtml::_('behavior.tooltip');
JHtmlBehavior::framework();
//$params = JComponentHelper::getParams( 'com_socialads' );
$baseurl=JRoute::_ (JUri::root().'index.php');
?>


<script type="text/javascript">
	var statebackup;

	function ads_generateState(countryId,Dbvalue,totalprice)
	{
		var country=techjoomla.jQuery('#'+countryId).val();
		if(country==undefined)
		{
			return (false);
		}
		techjoomla.jQuery.ajax({
			url: '?option=com_socialads&controller=checkout&task=loadState&country='+country+'&tmpl=component&format=raw',
			type: 'GET',
			dataType: 'json',
			success: function(data)
			{
				if(countryId=='country')
				{
					statebackup=data;
				}
				generateoption(data,countryId,Dbvalue);
			}
		});
	}

	function generateoption(data,countryId,Dbvalue)
	{
		var country=techjoomla.jQuery('#'+countryId).val();
		var options, index, select, option;

		// add empty option according to billing or shipping
		select = techjoomla.jQuery('#state');
		default_opt = "<?php echo JText::_('ADS_BILLIN_SELECT_STATE')?>";

		// REMOVE ALL STATE OPTIONS
		select.find('option').remove().end();

		// To give msg TASK  "please select country START"
		selected="selected=\"selected\"";
		var op='<option '+selected+' value="">'  +default_opt+   '</option>'     ;
		techjoomla.jQuery('#state').append(op);
		 // END OF msg TASK

		if(data)
		{
			options = data.options;
			for (index = 0; index < data.length; ++index)
			{
				var name=data[index];
				selected="";
				if(name==Dbvalue)
				{
					selected="selected=\"selected\"";
				}
				var op='<option '+selected+' value=\"'+data[index]+'\">'  +data[index]+   '</option>';

				techjoomla.jQuery('#state').append(op);
			}	 // end of for
		}
	}



	function adsPlaceOrder(stepno)
	{
		techjoomla.jQuery.ajax({
			url: '?option=com_socialads&controller=checkout&task=placeorder&tmpl=component&format=raw',
			type: 'GET',
			dataType: 'json',
			success: function(data)
			{
			}
		});
	}



</script>

	<div id="ads_mainwrapper" class="row-fluid form-horizontal">

		<legend id="qtc_billin" ><?php echo JText::_('ADS_BILLIN')?>&nbsp;<small><?php //echo JText::_('ADS_BILLIN_DESC')?></small>
		</legend>

		<div class="control-group">
			<label  for="fnam" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_FNAM')?></label>
			<div class="controls">
				<input id="fnam" class="input-medium bill inputbox required validate-name" type="text" value="<?php echo (isset($userbill->firstname))?$userbill->firstname:''; ?>" maxlength="250" size="32" name="bill[fnam]" title="<?php echo JText::_('ADS_BILLIN_FNAM_DESC')?>">
			</div>
		</div>

		<?php
		if($params->get( 'ads_middlenmae' )==1)
		{
		?>
		<div class="control-group">
			<label  for="fnam" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_MNAM')?></label>
			<div class="controls">
				<input id="mnam" class="input-medium bill inputbox required validate-name" type="text" value="<?php echo (isset($userbill->middlename))?$userbill->middlename:''; ?>" maxlength="250" size="32" name="bill[mnam]" title="<?php echo JText::_('ADS_BILLIN_MNAM_DESC')?>">
			</div>
		</div>
		<?php
		} ?>

		<div class="control-group">
			<label for="lnam" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_LNAM')?>	</label>
			<div class="controls">
				<input id="lnam" class="input-medium bill inputbox required validate-name" type="text" value="<?php echo (isset($userbill->lastname))?$userbill->lastname:''; ?>" maxlength="250" size="32" name="bill[lnam]" title="<?php echo JText::_('ADS_BILLIN_LNAM_DESC')?>">
			</div>
		</div>

		<div class="control-group">
			<label for="email1" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_EMAIL')?></label>
			<div class="controls"><input id="email1" class="input-medium bill inputbox required validate-email" type="text" value="<?php echo (isset($userbill->user_email))?$userbill->user_email:'' ; ?>" maxlength="250" size="32" name="bill[email1]"  title="<?php echo JText::_('ADS_BILLIN_EMAIL_DESC')?>">
			<!--			<span class="help-inline" id="billmail_msg"></span> -->
			</div>
		</div>

		<?php
		$enable_bill_vat=$params->get('enable_bill_vat');
		if($enable_bill_vat=="1")
		{
		 ?>
		<div class="control-group">
			<label for="vat_num"  class="control-label"><?php echo  JText::_('ADS_BILLIN_VAT_NUM')?></label>
			<div class="controls">
			  <input id="vat_num" class="input-small bill inputbox validate-integer" type="text" value="<?php echo (isset($userbill->vat_number))?$userbill->vat_number:''; ?>" size="32" name="bill[vat_num]" title="<?php echo JText::_('ADS_BILLIN_VAT_NUM_DESC')?>">
			</div>
		</div>
		<?php
		}

		$entered_numerics= "'".JText::_('QTC_ENTER_NUMERICS')."'";
		?>
		<div class="control-group">
			<label for="phon"  class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_PHON')?></label>
			<div class="controls">
			  <input id="phon" class="input-small bill inputbox required validate-integer" type="text" onkeyup="checkforalpha(this,43,<?php echo $entered_numerics; ?>);" maxlength="50" value="<?php echo (isset($userbill->phone))?$userbill->phone:''; ?>" size="32" name="bill[phon]" title="<?php echo JText::_('ADS_BILLIN_PHON_DESC')?>">
			</div>
		</div>
		<div class="control-group">
			<label for="addr"  class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_ADDR')?></label>
			<div class="controls">
			<textarea id="addr" class="input-medium bill inputbox required" name="bill[addr]"  maxlength="250" rows="3" title="<?php echo 		JText::_('ADS_BILLIN_ADDR_DESC')?>" ><?php echo (isset($userbill->address))?$userbill->address:''; ?></textarea>
				<p class="help-block"><?php echo JText::_('ADS_BILLIN_ADDR_HELP')?> </p>
			</div>
		</div>
		<div class="control-group">
			<label for="zip"  class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_ZIP')?></label>
			<div class="controls">
				<input id="zip"  class="input-small bill inputbox required " type="text" value="<?php echo (isset($userbill->zipcode))?$userbill->zipcode:''; ?>"  maxlength="20" size="32" name="bill[zip]" title="<?php echo JText::_('ADS_BILLIN_ZIP_DESC')?>">
			</div>
		</div>
		<div class="control-group">
			<label for="country"  class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_COUNTRY')?></label>
			<div class="controls">
			<?php
					$country 	= $this->country;
					$default	= ((isset($userbill->country_code))?$userbill->country_code:'');
					$options = array();
					$options[] = JHtml::_('select.option', "", JText::_('ADS_BILLIN_SELECT_COUNTRY'));

					foreach($country as $key=>$value)
					{
						$options[] = JHtml::_('select.option', $value, $value);
					}
					$taxval=0;
				echo $this->dropdown = JHtml::_('select.genericlist',$options,'bill[country]','class=" ads_ckout_select bill"  required="required" aria-invalid="false" size="1" onchange=\'ads_generateState(id,"",'.$taxval.')\' ','value','text',$default,'country');
			?>

			</div>
		</div>
		<div class="control-group">
			<label for="state" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_STATE')?></label>
			<div class="controls">
				<select name="bill[state]" id="state" class=" bill required">
					<option selected="selected" value="" ><?php echo JText::_('ADS_BILLIN_SELECT_STATE')?></option>
				</select>
			</div>
		</div>
		<div class="control-group">
			<label for="city" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_CITY')?></label>
			<div class="controls">
				<input id="city" class="input-medium bill inputbox required validate-name" type="text" value="<?php echo (isset($userbill->city))?$userbill->city:''; ?>" maxlength="250" size="32" name="bill[city]" title="<?php echo JText::_('ADS_BILLIN_CITY_DESC')?>">
			</div>
		</div>

	<button  type="button" onclick="adsPlaceOrder(4)" class="btn btn-mini btn-next" data-last="Finish">Place Order<i class="icon-arrow-right"></i></button>

	</div><!-- END qtc_mainwrapper  -->
