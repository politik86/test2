<?php
// no direct access
defined( '_JEXEC' ) or die( ';)' );
JHTML::_('behavior.formvalidation');
JHTML::_('behavior.tooltip');
//jimport('joomla.html.pane');
jimport( 'joomla.filesystem.folder');
$document =JFactory::getDocument();
$document->addStyleSheet(JURI::base().'components/com_socialads/css/socialads.css');
if(JVERSION < 3.0)
{
	$document->addScript( JURI::root().'components/com_socialads/js/jquery-1.7.1.min.js' );
}

/*
 * Integration reference
 * 0 = CB
 * 1 = JS
 * 2 = None
 * 3 = ES
 * */

require(JPATH_SITE.DS."administrator".DS."components".DS."com_socialads".DS."config".DS."config.php");

//load CB language file
if($socialads_config['integration'] == 0)
{
	$cbpath = JPATH_SITE.DS."administrator".DS."components".DS."com_comprofiler";

	if(JFolder::exists($cbpath))
	{
		global $_CB_framework, $_CB_database, $ueConfig, $mainframe;
		include_once( JPATH_ADMINISTRATOR . '/components/com_comprofiler/plugin.foundation.php' );
		require(JPATH_SITE.DS."components".DS."com_comprofiler".DS."plugin".DS."language".DS."default_language".DS."language.php");
	}
}

//load easy social language file
if($socialads_config['integration'] == 3)
{
	$lang = JFactory::getLanguage();
	$extension = 'com_easysocial';
	$base_dir = JPATH_ADMINISTRATOR;
	$language_tag = 'en-GB';
	$reload = true;
	$lang->load($extension, $base_dir, $language_tag, $reload);
}

?>

<script type="text/javascript">

function chkRange(field, id, rowid)
{

	if(field.value == "numericrange") {
	//document.getElementById('match['+id+']').disabled=true;
			document.getElementById('row'+rowid+'[5]radios').style.display="none";
			document.getElementById('row'+rowid+'[5]noradios').style.display="block";
			document.getElementById('match['+id+']').disabled = false;
	}
	else
	{
	  document.getElementById('row'+rowid+'[5]noradios').style.display="none";
		document.getElementById('row'+rowid+'[5]radios').style.display="block";
		document.getElementById('match['+id+']').disabled = true;
	}

}

function myValidate(f)
{
	if(document.formvalidator.isValid(f))
	{
		f.check.value='<?php echo  JSession::getFormToken(); ?>'; //send token
    return true;
  }
	else
  {
		return false;
  }
}


function saveTargeting()
{
	var r=confirm("<?php echo JText::_('CONFIG_JSMESSAGE'); ?>");
	if(r==true)
  	{
		document.adminForm.task.value="save";
		document.adminForm.onsubmit();
		document.adminForm.submit();
		return true;
	}
	return false;
}

function resetTargeting()
{
	var r=confirm("<?php echo JText::_('CONFIG_JSMESSAGE1'); ?>");
	if(r==true)
  {
		document.adminForm.resetall.value="1";
		document.adminForm.task.value="save";
		document.adminForm.onsubmit();
		document.adminForm.submit();
		return true;
	}
	else
	{
		return false;
	}
}

function removeOptions(mappinglist)
{
	var mapplen = mappinglist.length;
	var selectedCount = 0;
	var selectedValues = new Array();
	var i;

	for (var i = 0; i < mapplen; i++)
	{
		if (mappinglist.options[ i ].selected)
		{
			var val = mappinglist.options[ i ].value;
			if (mappinglist.options[ i ].value.selected == true){
			}
		}
	}
	return x;
}

function deleteOption(mappinglist, i)
{
  var mapplen = mappinglist.length;
  if(mapplen>0)
  {
    mappinglist.options[i] = null;
  }
}

function plginstall(namep)
{
	var check="#chk"+namep.name;
	//var chkname="chk"+namep.name;
	jQuery.ajax({
	url: '?option=com_socialads&controller=importfields&task=addcolumn&col_name='+namep.name,
	type: 'GET',
	dataType: "json",
	success: function(data) {
		if(data) {
					if(data.inmessage == "true")
					{
						jQuery(namep).attr('disabled',true)
						jQuery("#message1"+namep.name).append(data.smessage);
						jQuery(check).show('fast');
						jQuery(check+" input:checkbox").each(function() {
            		this.checked = "checked";
        				});

					}
					if(data.inmessage == "false")
					{
						jQuery(namep).attr('disabled',true)
						jQuery("#message1"+namep.name).append(data.smessage);
						jQuery(check).hide('fast');
						jQuery(check+" input:checkbox").each(function() {
            		this.checked = "";
        				});

					}
				}

			}
	});

}

</script>

<!-- form for showing admin view of importfields -->
<div class="techjoomla-bootstrap">
<form action="" class="form-validate" method="post" name="adminForm" onSubmit="return myValidate(this);">
	<?php
	// @ sice version 3.0 Jhtmlsidebar for menu
	if(JVERSION>=3.0)
	{
		if (!empty( $this->sidebar))
		{?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php
		}
		else
		{ ?>
			<div id="j-main-container">
		<?php
		}
	}
	?>
<?php
		$k=0;
		$flag=0;
		$i = 0;
		$model = $this->getModel();
		if(empty($this->fields)  && ($socialads_config['integration'] == 1))
		{?>
			<div class="alert alert-info">
				<span ><?php echo JText::_('JSINSTALL'); ?> </span>
			</div>

		<?php
		}
		else if(empty($this->fields)  && ($socialads_config['integration'] == 0))
		{
				if(!JFolder::exists($cbpath))
				{?>
				<div class="alert alert-info">
					<span ><?php echo JText::_('CBINSTALL'); ?> </span>
				</div>
			<?php
				}	else{?>
				<div class="alert alert-info">
					<span ><?php echo JText::_('CBINSTALL_FIELDS'); ?> </span>
				</div>
			<?php
				}
		}
		else if(empty($this->fields)  && ($socialads_config['integration'] == 3))
		{
				if(!JFolder::exists($cbpath))
				{?>
				<div class="alert alert-info">
					<span ><?php echo JText::_('ESINSTALL'); ?> </span>
				</div>
			<?php
				}	else{?>
				<div class="alert alert-info">
					<span ><?php echo JText::_('ESINSTALL_FIELDS'); ?> </span>
				</div>
			<?php
				}
		}
		else if( ($socialads_config['integration'] == 2))
		{?>
			<div class="alert alert-info">
				<span ><?php echo JText::_('NO_SOCIAL_TAR'); ?> </span>
			</div>
		<?php
		}
		else
		{
?>
		<table class="adminlist table table-striped">
		<tr>
			<th><?php echo JText::_('IMPORT_FIELD_LABEL') ?></th>
			<th><?php echo JHTML::tooltip(JText::_('IMPORT_LABLE_TOOLTIP'), JText::_('IMPORT_LABLE_HEAD'),  '', JText::_('IMPORT_LABLE'));?></th>
			<?php
			if($socialads_config['integration'] == 1)
			{
					$js = JText::_('SA_JS');?>
				<th style="display:none;"><?php echo JHTML::tooltip(JText::_('IMPORT_FIELD_TOOLTIP'), JText::_('IMPORT_FIELD_HEAD'),  '', JText::sprintf('IMPORT_NAME', $js));?>
				</th>
			<?php
			}
			else if($socialads_config['integration'] == 0)
			{
				$cb =JText::_('SA_CB');?>
				<th style="display:none;"><?php echo JHTML::tooltip(JText::_('IMPORT_FIELD_TOOLTIP'), JText::_('IMPORT_FIELD_HEAD'),  '', JText::sprintf('IMPORT_NAME', $cb));?></th>
				<?php
			}//for easysocial
			else if($socialads_config['integration'] == 3)
			{
				$es =JText::_('SA_ES');?>
				<th style="display:none;"><?php echo JHTML::tooltip(JText::_('IMPORT_FIELD_TOOLTIP'), JText::_('IMPORT_FIELD_HEAD'),  '', JText::sprintf('IMPORT_NAME', $es));?>
				</th>
			<?php
			}
			if($socialads_config['integration'] == 1)
			{?>
				<th><?php echo JHTML::tooltip(JText::sprintf('IMPORT_TYPE_TOOLTIP', $js), JText::_('IMPORT_TYPE_HEAD'),  '', JText::_('IMPORT_SELECT'));?></th>
			<?php
			}
			else if($socialads_config['integration'] == 0)
			{?>
				<th><?php echo JHTML::tooltip(JText::sprintf('IMPORT_TYPE_TOOLTIP', $cb), JText::_('IMPORT_TYPE_HEAD'),  '', JText::_('IMPORT_SELECT'));?></th>
			<?php
			}
			else if($socialads_config['integration'] == 3)
			{?>
				<th><?php echo JHTML::tooltip(JText::sprintf('IMPORT_TYPE_TOOLTIP', $es), JText::_('IMPORT_TYPE_HEAD'),  '', JText::_('IMPORT_SELECT'));?></th>
			<?php } ?>

			<th><?php echo JHTML::tooltip(JText::_('FUZZY-EXACT_TOOLTIP'), JText::_('FUZZY-EXACT_HEAD'),  '', JText::_('FUZZY-EXACT'));?></th>
		</tr>
		<?php

		//print_r($this->fields);die;

		$count = 0;
		foreach ($this->fields as $row)
		{
			if ($row->mapping_fieldid)
			{
				$disabled = 'disabled="true"';
			}
			else
			{
				$disabled = '';
			}
			$flag++;
			?>
			 <tr class="<?php echo 'row'.$k; ?>" id="<?php echo 'row'.$count; ?>">
			 <?php
			if($socialads_config['integration'] == 0)
			{
				$row->field_label = htmlspecialchars( getLangDefinition( $row->field_label));
			}
			else{
				$row->field_label = JText::_("$row->field_label");
			}

			?>

				<td id="<?php echo 'row'.$count.'[1]'; ?>" ><?php echo $row->field_label; ?></td>
				<td id="<?php echo 'row'.$count.'[2]'; ?>">
						<?php // Commented & Added By - Deepak
							$mapval = JText::_("$row->mapping_label") ? JText::_("$row->mapping_label") : JText::_("$row->field_label");
						?>
						<!--<input type="text" name="mappinglist[<?php echo $row->id; ?>][label]" <?php echo $disabled; ?> value="<?php echo $row->mapping_label ? $row->mapping_label : $row->field_label;?>" /> -->
						<input type="text" name="mappinglist[<?php echo $row->id; ?>][label]" <?php echo $disabled; ?> value="<?php echo $mapval; ?>" />
				</td>

				<td style="display:none;" id="<?php echo 'row'.$count.'[3]'; ?>">
						<?php echo JHTML::_('select.genericlist', $this->fields, 'mappinglist['.$row->id.'][fieldid]',
						'class="inputbox" '.$disabled, 'id', 'field_label', $row->id ); ?>
				</td>
				<td id="<?php echo 'row'.$count.'[4]'; ?>">
					<?php
					//validating fields depends upon field type
					if($row->type=='text'  || $row->type=='lable' || $row->type=='email' || $row->type=='textbox' || $row->type=='joomla_fullname' || $row->type=='joomla_username' || $row->type=='joomla_email' || $row->type=='permalink'  )
						$list	= $this->mappinglistt;
					else if($row->type=='textarea' || $row->type=='url' ||  $row->type=='address')
						$list	= $this->mappinglista;
					else if($row->type=='date' || $row->type=='time' || $row->type=='birthdate'|| $row->type=='joomla_timezone' || $row->type=='birthday')
						$list	= $this->mappinglistd;
					else	//					select,singleselect,list,radio,checkbox,country,,,,
						$list	= $this->mappinglists;

					echo JHTML::_('select.genericlist', $list, 'mappinglist['.$row->id.'][fieldtype]', 'class="inputbox fieldlist" onchange=chkRange(this,'.$row->id.','.$count.'); ' .$disabled, 'value', 'text', $row->mapping_fieldtype); ?>
				</td>

				<?php
				$match = array(0=>JText::_("FUZZY"), 1=>JText::_("EXACT"));
				$field_array=array();
				if(!empty($match))
				{
					$options= array();
					//$default=0;
					foreach($match as $key=>$value) {
					 $options[] = JHTML::_('select.option', $key, $value);
					 }
					?>
					<input type="hidden" name="mappinglist[<?php echo $row->id; ?>][fieldcode]" value="<?php echo $row->mapping_fieldname; ?>" <?php echo $disabled; ?>  />
					<td id="<?php echo 'row'.$count.'[5]'; ?>">
						<span id="<?php echo 'row'.$count.'[5]radios'; ?>" style="display:block">
									<?php
							if($row->type == "list" ||  $row->type=='textarea' ||  $row->type=='checkbox'  || $row->type=='multiselect' || $row->type=='multicheckbox' || $row->type=='address' || $row->type=='multilist' || $row->type=='dropdown')
								{
									echo JText::_("FUZZY");
									echo '<input type="hidden"  name="match['.$row->id.']" value="0" />';
								}
							else if($row->type == "select" || $row->type == "singleselect" || $row->type=='country' || $row->type == 'radio' || $row->type == 'boolean' || $row->type == 'gender')
								{

										echo JText::_("EXACT");
										echo '<input type="hidden" name="match['.$row->id.']" value="1" />';
								}
							else if($row->type=='text' || $row->type=='textbox')
								{
										if($row->mapping_fieldtype != "numericrange"){
										echo $radiolist = JHTML::_('select.radiolist', $options, 'match['.$row->id.']', 'class="inputbox fieldlist"' .$disabled,
										'value', 'text', $row->mapping_match);
										}
											else{
													echo JText::_("DOES_NOT_AAPLY");
											}
								}
							else
							{
									echo JText::_("DOES_NOT_AAPLY");
									echo '<input type="hidden" name="match['.$row->id.']" value="2" />';
							}

								?>
						</span>
						<span id="<?php echo 'row'.$count.'[5]noradios' ?>" style="display:none">
							<?php
									echo JText::_("DOES_NOT_AAPLY");
									echo '<input type="hidden" name="match['.$row->id.']" id="match['.$row->id.']" value="2"  />';
							?>
						</span>

					</td>
				 <?php
				} ?>

			 </tr>
			<?php
			$k = 1 - $k;
			$i ++;
			$count ++;
		 } //foreach ends

/*Start Vaibhav*/
$count=0;
	foreach ($this->pluginresult as $rowplugin)
	{
	$countbutton=0;

		//$xml = JFactory::getXML('Simple');
		$currentversion='';
		//Load the xml file
		if(JVERSION >= '1.6.0')
		{
			$vfile1=JPATH_SITE."/plugins/socialadstargeting/$rowplugin->element/$rowplugin->element.xml";
		}
		else
		{
			$vfile1=JPATH_SITE."/plugins/socialadstargeting/$rowplugin->element.xml";
		}

		$xml=JFactory::getXML($vfile1);
		$currentversion=(string)$xml->version;
		//$xml->loadFile($vfile1);
		$col_value = array();
		if($xml)
		{
			//FIXED FOR #29045..BUT NEEDS TO BE VERIFY
			$xml = json_decode(json_encode((array)$xml), TRUE);

			foreach($xml as $key=>$var)
			{
				//print_r($var->name); die('asdasdasd');
				if($key == 'satargeting')
				{
					//print_r($var); die('adasd');
					foreach ($var as $minikey=>$val)
					{
						if($minikey =='plgfield')
						{
							//$val1=(array)($val);
							$col_value[] = $val; //FIXED FOR #29045..BUT NEEDS TO BE VERIFY
						}
					}
				}
			}
		}

		if(!empty($col_value))
		{
		$field_array = array();
		if(!empty($this->colfields)){
			for ($i = 0; $i < count($this->colfields); $i++) {
						$field_array[] = $this->colfields[$i]->Field;
			}
		}
			foreach ($col_value as $field_value)
			{

				if (!in_array($field_value, $field_array)) {
				 $countbutton=1;
					}
			}
		}

 ?>
<!--table class="adminlist"-->
		<tr class="<?php echo 'row'.$k; ?>">
			<td>
				<?php echo $rowplugin->name;?><input type="hidden" name="plugin[<?php echo $count;?>]" id="plugin[<?php echo $count;?>]" value="plugin<?php echo $rowplugin->id;?>">
			</td>
			<td colspan="3">
			<?php
				$chechvalue="";

				if($rowplugin->enabled =="1")
				{
					$chechvalue='checked';
					$check_display="block";

				}
				else
				{
					if($countbutton == 1)
					$check_display="none";
					else
					$check_display="block";
					$chechvalue='';
				}
			//	echo "plugin".$rowplugin->enabled;
			//	echo "cb".$countbutton;
			//	echo "cd".$check_display;
			?>
				<span name="chk<?php echo $rowplugin->element; ?>" id="chk<?php echo $rowplugin->element; ?>"
					style="display:<?php echo $check_display;?>">
				<input type="checkbox" name="pluginchk[<?php echo $count;?>]" id="plugin[chk<?php echo $count;?>]" <?php echo $chechvalue;?> ></span>
				<?php if($countbutton == 1)
					{?>
				<input type="button" onclick="plginstall(this);" name="<?php echo $rowplugin->element;?>" id="<?php echo $rowplugin->element;?>" value="<?php echo JText::_('PLGINSTALL_CLK');?>" >
				<?php } ?>
				<span id="message1<?php echo $rowplugin->element;?>"  ></span>
			</td>
		</tr>
<?php
$k = 1 - $k;
$count++;
	}
/*End Vaibhav*/
?>
	</table>
	<!--/table-->
<?php 	}//install condition ends
?>

	<input type="hidden" name="check" value="post"/>
	<input type="hidden" name="resetall" value="0"/>
	<input type="hidden" name="option" value="com_socialads" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="importfields" />
	<?php if(!empty($this->fields))
	{
		if(count($this->fields)==$flag)
		{  ?>
			<input type="hidden" name="boxchecked" value="0" />
			<?php
		}
		elseif($this->adcount==0)
		{ ?>
			<input type="hidden" name="boxchecked" value="1" />
			<?php
		}
		else
		{ ?>
			<input type="hidden" name="boxchecked" value="2" />
			<?php
		}
	}?>
	<input type="hidden" name="controller" value="importfields" />

	<?php
	if (!empty( $this->sidebar))
	{ ?>
		</div>
	<?php
	} ?>
</form><!-- form for showing admin view of importfields ends here -->
</div>
