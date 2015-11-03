<?php
defined( '_JEXEC' ) or die( ';)' );
require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

jimport( 'joomla.form.formvalidator' );
//jimport('joomla.html.pane');
JHTML::_('behavior.formvalidation');
//JHTML::_('behavior.tooltip');
//JHTML::_('behavior.mootools');
JHtmlBehavior::framework();
jimport( 'joomla.html.parameter' );
JToolBarHelper::back( JText::_('CC_HOME') , 'index.php?option=com_socialads');
JToolBarHelper::save($task = 'save', $alt = JText::_('SAVE'));
JToolBarHelper::cancel( $task = 'cancelcoupon', $alt =JText::_('CLOSE')  );
?>

<?php
	$document = JFactory::getDocument();


	$document->addScript(JURI::base().'components/com_socialads/js/adminsocialads.js');
	$document->addStyleSheet(JURI::base().'components/com_socialads/css/socialads.css');
	$input=JFactory::getApplication()->input;
	$cid	= $input->get( 'cid','','ARRAY' );
	//print_r($cid); die('view');
	//$layout_type_param = "";
	//$pluginParams ="";
	//$plugin = JPluginHelper::getPlugin('socialadslayout', 'plug_layout2');
	//$pluginParams = json_decode( $plugin->params );
	//print_r($pluginParams); die('coupon');
	//$layout_type_param = $pluginParams->layout_type;

	if(!$cid)
	{
		$this->coupons=array();
	}

$js_key="
	function checkforalpha(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
			if((el.value.charCodeAt(i) > 64 && el.value.charCodeAt(i) < 92) || (el.value.charCodeAt(i) > 96 && el.value.charCodeAt(i) < 123))
			{
				alert('Please Enter Numerics');
				el.value = el.value.substring(0,i); break;
				}
			}
	}

	function checkfornum(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
			if(el.value.charCodeAt(i) > 47 && el.value.charCodeAt(i) < 58) {
				alert('Numerics Not Allowed');
				el.value = el.value.substring(0,i); break;
			}
		}
	}
	";
	$document->addScriptDeclaration($js_key);


?>


<script type="text/javascript">


window.addEvent("domready", function(){
    document.formvalidator.setHandler('name', function (value) {
		if(value<=0){
			alert( '<?php echo JText::_( "VAL_GRT")?>' );
			return false;
		}
		else if(value == ' '){
			alert('<?php echo JText::_( "NO_BLANK")?>' );
			return false;
		}
		else{
			return true;
		}
	});
});


	/* vm:this function allow only numberic and specified char (at 0th position)
	// ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
				(code 46 for dot/full stop .)
		@param el :: html element
		@param allowed_ascii::ascii code that shold allow

	*/
	function ad_checkforalpha(el, allowed_ascii,enter_numerics )
	{
		allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
		var i =0 ;
		for(i=0;i<el.value.length;i++){
		  if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		  {

		  		if(allowed_ascii ==el.value.charCodeAt(i) )  //&& i==0)  // + allowing for phone no at first char
					{
						var temp=1;
					}
					else
					{
							alert(enter_numerics);
							el.value = el.value.substring(0,i);
							return false;
					}


		  }
		}
		return true;
	}


window.addEvent("domready", function(){
   document.formvalidator.setHandler('verifydate', function(value) {
      regex=/^\d{4}(-\d{2}){2}$/;
      return regex.test(value);
   })

})

var validcode1=0;
	function checkcode()
	{

		var selectedcode=document.getElementById('code').value;
		var cid=<?php if($cid) echo $cid[0];else echo "0"; ?>;

		if(parseInt(cid)==0)
			var url = "index.php?option=com_socialads&task=getcode&controller=managecoupon&selectedcode="+selectedcode;
		else
			var url = "index.php?option=com_socialads&task=getselectcode&controller=managecoupon&couponid="+cid+"&selectedcode="+selectedcode;

		jQuery.ajax({
		url:url,
		type: 'GET',
		success: function(response) {
				var cid=<?php if($cid) echo $cid[0];else echo "0"; ?>;

				if(parseInt(cid)==0)
				{
					if(parseInt(response)!=0)
					{
						alert('<?php echo JText::_( "COP_EXIST")?>');
						validcode1=0;
						return 0;
					}
					else
					{
						validcode1=1;
						return 1;
					}
				}
				else
				{
					if(parseInt(response)!=0)
					{
						alert('<?php echo JText::_( "COP_EXIST")?>');
						validcode1=0;
						return 0;
					}
					else
					{
						validcode1=1;
						return 1;
					}
				}
			}
		});

	}	//end function check code


	<?php if(JVERSION >= '1.6.0') { ?>
		Joomla.submitbutton = function(action){
	<?php } else {?>
		function submitbutton( action ) {
	<?php } ?>

	var form = document.adminForm;
	if(action=='save')
	{
		var validateflag = document.formvalidator.isValid(document.adminForm);
		if(validateflag)
		{


			//jQuery(document).ready(function() {

				var cid=<?php if($cid) echo $cid[0];else echo "0"; ?>;
				if(parseInt(cid)==0)
				{
					var selectedcode=document.getElementById('code').value;
					//selectedcode=addslashes(selectedcode);
					var url = "index.php?option=com_socialads&task=getcode&controller=managecoupon&selectedcode="+selectedcode;
				}
				else
				{
					var selectedcode=document.getElementById('code').value;
					//selectedcode=addslashes(selectedcode);
					var url = "index.php?option=com_socialads&task=getselectcode&controller=managecoupon&couponid="+cid+"&selectedcode="+selectedcode;
				}

				//for date validation
				var from_date = document.getElementById('from_date').value;
				var exp_date = document.getElementById('exp_date').value;
				if(from_date > exp_date)
				{
				alert('The expiry date is set incorrectly');
				return false;
				}

				<?php if(JVERSION >= '1.6.0') { ?>
						var a = new Request({url:url,
				<?php } else {?>
						new Ajax(url, {
				<?php } ?>
					method: 'get',
					onComplete: function(response) {
						var cid=<?php if(isset($cid[0])) echo $cid[0];else echo "0"; ?>;

						if(parseInt(cid)==0)
						{
							if(parseInt(response)!=0)
							{
								alert('<?php echo JText::_( "COP_EXIST")?>');
								validcode1=0;
								return false;
							}
							else
							{
								submitform( action );
								return true;
							}
						}
						else
						{
							if(parseInt(response)!=0)
							{
								alert('<?php echo JText::_( "COP_EXIST")?>');
								validcode1=0;
								return false;
							}
							else
							{
								submitform( action );
								return true;
							}
						}
					}
			<?php if(JVERSION >= '1.6.0') { ?>
				}).send();
			<?php } else {?>
				}).request();
			<?php } ?>
		//	});


		}//if validate flag
		else
		return false;
	}//if action=save
	else
	submitform( action );
	}

</script>
<div class="techjoomla-bootstrap">
<form action="index.php" name="adminForm" id="adminForm" class="form-validate" method="post" >
	<input type="hidden" name="check" value="post"/>
	<fieldset>
	<legend><?php echo JText::_( "COP_INFO"); ?></legend>
	<table width="60%" cellspacing="8px">
	<tr>
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_NAME_TOOLTIP'), JText::_('COUPAN_NAME'), '', '* '.JText::_('COUPAN_NAME'));?></td>
			<td class="setting-td">

				<input type="text" name="coupon_name" id="coupon_name" class="inputbox required validate-name"   size="20" value="<?php if($this->coupons){  echo stripslashes($this->coupons[0]->name); } ?>" autocomplete="off" />
				<label for="coupon_name" ></label>

			</td>
		</tr>

		<tr>
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_CODE_TOOLTIP'), JText::_('COUPAN_CODE'), '', '* '.JText::_('COUPAN_CODE'));?></td>
			<td><input type="text" name="code" id="code" class="inputbox required validate-name"    size="20" value="<?php if($this->coupons){ echo $this->escape( stripslashes( $this->coupons[0]->code ) ); } ?>" 	 autocomplete="off" />
				<label for="code" ></label>
			</td>

		</tr>

		<tr>
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_ENABLED_TOOLTIP'), JText::_('COUPAN_ENABLED'), '', JText::_('COUPAN_ENABLED'));?></td>
			<td>
			<?php
				if($this->coupons)
					$published 	= $this->coupons[0]->published;
			else
					$published 	= 0;
			?>


				<div id="enable_coupon" class="input-append yes_no_toggle">
					<input type="radio" name="published" id="published1" value="1" <?php echo ($published == 1 )? 'checked="checked"' :'';?> />
					<label class="first btn <?php echo ($published  == 1 )? 'btn-success' :'';?>" for="published1"><?php echo JText::_('SA_YES');?></label>
					<input type="radio" name="published" id="published0" value="0" <?php echo ($published == 0)? 'checked="checked"' :'';?>  />
					<label class="last btn <?php echo ($published  == 0 )? 'btn-danger' :'';?>" for="published0"><?php echo JText::_('SA_NO'); ?></label>
				</div>

			</td>
		</tr>
		<tr id="img_width_row" name="img_width_row" >
				<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_VALUE_TOOLTIP'), JText::_('COUPAN_VALUE'), '','* '. JText::_('COUPAN_VALUE'));?></td>

				<td class="setting-td">
					<?php
					$saCop_fn ='ad_checkforalpha(this,46, "'.JText::_("COM_SOCIALADS_ENTER_NUMERICS").'" );';
					?>
				<input  class="inputbox required validate-name" type="text" name="value" id="value" Onkeyup= '<?php echo $saCop_fn; ?>' size="20" value="<?php if($this->coupons){ echo $this->coupons[0]->value; } ?>" autocomplete="off" />
					<label for="value" ></label>
				</td>
		</tr>
		<tr id="img_width_row" name="img_width_row" >
				<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_VALUE_TYPE_TOOLTIP'), JText::_('COUPAN_VALUE_TYPE'), '', JText::_('COUPAN_VALUE_TYPE'));?></td>

				<td class="setting-td">
					<?php
					if($this->coupons)
					$val_type 	= $this->coupons[0]->val_type;
					else
					$val_type 	= 0;
					$val_type1[] = JHTML::_( 'select.option', '0', JText::_("COP_FLAT"));
					$val_type1[] = JHTML::_( 'select.option', '1', JText::_("COP_PER")); // first parameter is value, second is text
					$lists['val_type'] = JHTML::_('select.radiolist', $val_type1, 'val_type', 'class="inputbox sa_setting_radio" ', 'value', 'text', $val_type, 'val_type' );

					 echo $lists['val_type'];
					 ?>
					<label for="val_type" ></label>
				</td>
		</tr>
		<tr id="img_height_row" name="img_height_row" >
				<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_MAXUSES_TOOLTIP'), JText::_('COUPAN_MAXUSES'), '', JText::_('COUPAN_MAXUSES'));?>
				</td>

				<td class="setting-td">
					<input type="text" name="max_use" id="max_use" class="inputbox" Onkeyup= "checkforalpha(this);" size="20" value="<?php if($this->coupons){ echo $this->coupons[0]->max_use; } ?>" autocomplete="off" />
					<label for="max_use" ></label>
				</td>

		</tr>

		<tr id="max_title_char_row">
		<td  width="25%"><?php echo JHTML::tooltip(JText::_('COUPAN_MAXUSES_PERUSER_TOOLTIP'), JText::_('COUPAN_MAXUSES_PERUSER'), '', JText::_('COUPAN_MAXUSES_PERUSER'));?>
		</td>


				<td class="setting-td">
					<input type="text" name="max_per_user" id="max_per_user" class="inputbox" Onkeyup= "checkforalpha(this);" size="20" value="<?php if($this->coupons){  echo $this->coupons[0]->max_per_user; } ?>" autocomplete="off" />
					<label for="max_per_user" ></label>
				</td>
		</tr>

		<tr id="max_desc_char_row">
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('VALID_FROM_TOOLTIP'), JText::_('VALID_FROM'), '', JText::_('VALID_FROM'));?></td>
			<td class="setting-td">
				<?php
				if($this->coupons)
				{
					if($this->coupons[0]->from_date != '0000-00-00 00:00:00')
						$date_from=date("Y-m-d",strtotime($this->coupons[0]->from_date));
					else
						$date_from='';
				}
				else
					$date_from='';

				 echo JHTML::_("calendar", "$date_from", "from_date", "from_date", "%Y-%m-%d"); ?>

				<label for="from_date" ></label>
			</td>
		</tr>

      	<tr id="layout_row">
						<td  width="25%"><?php echo JHTML::tooltip(JText::_('EXPIRES_ON_TOOLTIP'), JText::_('EXPIRES_ON'), '', JText::_('EXPIRES_ON'));?></td>
			<td class="setting-td">
				<?php
				if($this->coupons)
				{
					if($this->coupons[0]->exp_date != '0000-00-00 00:00:00')
						$date_exp=trim(date("Y-m-d",strtotime($this->coupons[0]->exp_date)));
					else
						$date_exp='';
				}
				else
					$date_exp='';
				  echo JHTML::_("calendar",  "$date_exp", "exp_date", "exp_date", "%Y-%m-%d");

				?>
				<label for="exp_date" ></label>
			</td>
		</tr>
		<tr id="max_desc_char_row">
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('DESCRIPTION_TOOLTIP'), JText::_('DESCRIPTION'), '', JText::_('DESCRIPTION'));?></td>

			<td class="setting-td">
				<textarea   size="28" rows="3" name="description" id="description" class="inputbox" ><?php if($this->coupons){  echo trim($this->coupons[0]->description); } ?></textarea>
				<label for="description" ></label>
			</td>
		</tr>
		<tr id="max_desc_char_row">
			<td  width="25%"><?php echo JHTML::tooltip(JText::_('PARAMETERS_TOOLTIP'), JText::_('PARAMETERS'), '', JText::_('PARAMETERS'));?></td>
			<td class="setting-td">
				<textarea  size="28" rows="3" name="params" id="params" class="inputbox" ><?php if($this->coupons){  echo trim($this->coupons[0]->params); } ?></textarea>
				<label for="params" ></label>

			</td>
		</tr>
		</table>
		</fieldset>
<input type="hidden" name="id1" id="id1" value="<?php if($this->coupons){ echo $this->coupons[0]->id; } ?>" />
<label for="id1" ></label>
	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="managecoupon" />
	<input type="hidden" name="controller" value="managecoupon" />
		<?php echo JHTML::_( 'form.token' ); ?>

</form>
</div>
