<?php
/**
 *  @package    Social Ads
 *  @copyright  Copyright (c) 2009-2013 TechJoomla. All rights reserved.
 *  @license    GNU General Public License version 2, or later
 */
defined('_JEXEC') or die( 'Restricted access' );
$document = JFactory::getDocument();
$document->addScript(JUri::root().'components/com_socialads/js/socialads.js');
$userbill = $this->userbill;
$rootURL = JUri::root();

//$document->addStyleSheet(JUri::base().'components/com_quick2cart/css/quick2cart_style.css' );//aniket
if(version_compare(JVERSION, '3.0', 'lt'))
{ // require for pop up
	/*BS start*/
	//$document->addStyleSheet(JUri::base().'components/com_quick2cart/bootstrap/css/bootstrap.css' );//aniket
	$document->addStyleSheet(JUri::root().'media/techjoomla_strapper/css/bootstrap.min.css' );
	/*BS end*/
}

?>
<script type="text/javascript">

var root_url2="<?php echo JUri::root(); ?>";

	var statebackup;

	function ads_generateState(countryId,Dbvalue,selOptionMsg,root_url)
	{
		var country=techjoomla.jQuery('#'+countryId).val();
		if(country==undefined)
		{
			return (false);
		}
		techjoomla.jQuery.ajax({
			url: root_url2+'?option=com_socialads&controller=checkout&task=loadState&country='+country+'&tmpl=component&format=raw',
			type: 'GET',
			dataType: 'json',
			success: function(data)
			{
				if(countryId=='country')
				{
					statebackup=data;
				}
				generateoption(data,countryId,Dbvalue,selOptionMsg);
			}
		});
	}

	function generateoption(data,countryId,Dbvalue,selOptionMsg)
	{
		var country=techjoomla.jQuery('#'+countryId).val();
		var options, index, select, option;

		// add empty option according to billing or shipping
		select = techjoomla.jQuery('#state');
		default_opt = selOptionMsg; //"<?php echo JText::_('ADS_BILLIN_SELECT_STATE')?>";

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
				techjoomla.jQuery("#state").trigger("liszt:updated");  /* IMP : to update to chz-done selects*/
			}	 // end of for
		}
	}
</script>


		<div class="control-group">
			<label  for="fnam" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_FNAM')?></label>
			<div class="controls">
				<input id="fnam" class="input-medium bill inputbox required validate-name" type="text" value="<?php echo (isset($userbill->firstname))?$userbill->firstname:''; ?>" maxlength="250" size="32" name="bill[fnam]" title="<?php echo JText::_('ADS_BILLIN_FNAM_DESC')?>">
			</div>
		</div>

		<?php


		if(!empty($params) && $params->get( 'ads_middlenmae' )==1)
		{
		?>
		<div class="control-group">
			<label  for="fnam" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_MNAM')?></label>
			<div class="controls">
				<input id="mnam" class="input-medium bill inputbox required validate-name" type="text" value="<?php echo (isset($userbill->middlename))?$userbill->middlename:''; ?>" maxlength="250" size="32" name="bill[mnam]" title="<?php echo JText::_('ADS_BILLIN_MNAM_DESC')?>">
			</div>
		</div>
		<?php
		}

		?>

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
		//$enable_bill_vat = !empty($params) ? $params->get('enable_bill_vat') : 0;;
		if(0 && $enable_bill_vat=="1")
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

		$entered_numerics= "'".JText::_('ADS_ENTER_NUMERICS')."'";
		?>
		<div class="control-group">
			<label for="phon"  class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_PHON')?></label>
			<div class="controls">
			  <input id="phon" class="input-small bill inputbox required validate-integer" type="text" onkeyup="ad_checkforalpha(this,43,<?php echo $entered_numerics; ?>);" maxlength="12" value="<?php echo (isset($userbill->phone))?$userbill->phone:''; ?>" size="32" name="bill[phon]" title="<?php echo JText::_('ADS_BILLIN_PHON_DESC')?>">
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
			<label for="sa_bill_country"  class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_COUNTRY')?></label>
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

				echo $this->dropdown = JHtml::_('select.genericlist',$options,'bill[country]','class=" ads_ckout_select bill sa_Mediumselect required"  required="true" aria-invalid="false" size="1" onchange=\'ads_generateState(this.id,"","'.JText::_('ADS_BILLIN_SELECT_STATE').'","'.$rootURL.'")\' ','value','text',$default,'sa_bill_country');

			?>

			</div>
		</div>
		<div class="control-group">
			<label for="state" class="control-label"><?php echo "* ".JText::_('ADS_BILLIN_STATE')?></label>
			<div class="controls">
				<select name="bill[state]" id="state" class=" bill sa_Mediumselect required">
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

<script type="text/javascript">

//techjoomla.jQuery(document).ready(function()
{
	var DBuserbill="<?php echo (isset($userbill->state_code))?$userbill->state_code:''; ?>";
	ads_generateState("sa_bill_country",DBuserbill,"<?echo JText::_('ADS_BILLIN_SELECT_STATE') ?>","<?echo $rootURL ?>") ;
}
</script>
