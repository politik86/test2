 <?php
/*------------------------------------------------------------------------
# com_adagency
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com/forum/index/
-------------------------------------------------------------------------*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' ); 


	$data = $this->data;
	$type = $this->type;
	$lists = $this->lists;
	$package=$this->package;
	$the_zones_positions=$this->available_positions;
	$zones=$this->newzones;
	$oldzones=array();
	if (isset($package->zones)) $oldzones = explode('|',$package->zones);
	$nullDate = 0;
	$configs = $this->configs;
	if (!isset($type)) $type='cpm';
	if (!isset($package->type)) $package->type=$type;
	$document = JFactory::getDocument();
?>

<script type="text/javascript" src="<?php echo JURI::base().'components/com_adagency/helpers/prototype-1.6.0.2.js' ?>"></script>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/package_box.php"); ?>
<div style="float:right">
<?php
	$link = "ajax_addpackage(); ";
	?>
</div>

 <form action="index.php" method="post" name="adminForm" id="adminForm" style="margin-left:10px;">
	<fieldset class="adminform">
	<legend><?php echo ($package->tid > 0)?JText::_('VIEWPACKAGEEDIT'):JText::_('VIEWPACKAGENEW'); ?></legend>     
	<table class="admintable" border="0">
		<tr>
			<td width="15%">
			 <?php echo JText::_('VIEWPACKAGEDESC');?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" name="description" size="40" maxlength="255" value="<?php echo $package->description; ?>" />
			</td>
		</tr>
		
		<tr>
			<td width="15%" valign="top">
			 <?php echo JText::_('VIEWPACKAGE_DESCRIPTION');?>:
			</td>
			<td>
			<textarea name="pack_description" id="pack_description" wrap="physical" cols="40" rows="4"><?php echo stripslashes($package->pack_description);?></textarea>
			</td>
		</tr>		
		
		<tr>
			<td>
			<?php echo JText::_('VIEWPACKAGETYPE');?>:<font color="#ff0000">*</font> 
			</td>
			<td><?php echo $lists['type']; ?>
			</td>
		</tr>
		
		<tr id="typefr" <?php if ($package->type=="fr") echo 'style=""'; else echo 'style="display:none"'; ?> >
			<td>
			<?php echo JText::_('VIEWPACKAGETERMS');?>:<font color="#ff0000">*</font> 
			</td>
			<td><?php echo $lists['amount']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lists['duration'] ?>
			</td>
		</tr>

		<tr id="typepc" <?php if ($package->type=="pc") echo 'style=""'; else echo 'style="display:none"'; ?>>
			<td>
			<?php echo JText::_('VIEWPACKAGETERMS');?>:<font color="#ff0000">*</font> 
			</td>
			<td>
			<input <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" name="quantity" size="8" maxlength="8" value="<?php echo $package->quantity; ?>" />&nbsp;<?php echo JText::_('AGENCYCLICKS');?>
			</td>
		</tr>

		<tr  id="typecpm" <?php if ($package->type=="cpm") echo 'style=""'; else echo 'style="display:none"'; ?>>	
			<td>
			<?php echo JText::_('VIEWPACKAGETERMS');?>:<font color="#ff0000">*</font> 
			</td>
			<td>
			<input <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" name="quantity" id="quantity" size="8" maxlength="10" value="<?php if ($package->quantity) {echo $package->quantity;} else {echo "10000";} ?>" />
			<?php //echo $lists['quantity']; ?>&nbsp;<?php echo JText::_('AGENCYIMPRESSIONS');?>
			</td>
		</tr>	

		
		<tr>
			<td>
			 <?php echo JText::_('VIEWPACKAGEPRICE');?>:<font color="#ff0000">*</font>
			</td>
			<td>
			<input type="radio" value="1" id="not_free" name="free" <?php if ($package->cost > 0) echo 'checked="checked"'; ?>>
			<input onKeyDown="javascript:document.getElementById('not_free').checked=true;" <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" id="cost" name="cost" style="width:40px;" maxlength="8" value="<?php echo $package->cost; ?>" />&nbsp;<?php echo $configs->currencydef; ?>
			<input type="radio" value="0" name="free" id="is_free" <?php if ($package->cost == 0) echo 'checked="checked"'; ?> onClick="javascript:document.getElementById('cost').value=0;"> <?php echo JText::_('VIEWPACKAGE_PRICE_FREE');?>
			<br>
			</td>
		</tr>
		<tr>
		<td valign="top">
		   <?php echo JText::_('VIEWPACKAGE_ZONES_INCLUDED');?>:<font color="#ff0000">*</font><br />
		</td>
		<td>
		<input type="radio" name="selzone" id="selzone1" value="1" <?php if ($package->tid == 0 || $package->zones == 'All Zones') echo 'checked="checked"'; ?> ><?php echo JText::_('VIEWPACKAGE_ZONES_ALL_ZONES');?></input><br/>
		<input type="radio" name="selzone" id="selzone2" value="0" <?php if ($package->tid > 0 && $package->zones != 'All Zones') echo 'checked="checked"'; ?> ><?php echo JText::_('VIEWPACKAGE_ZONES_SELECTED_ZONES');?></input><br/>
			<?php foreach ($zones as $zone) { 
					$checked='';
					if(in_array($zone,$oldzones)) $checked='checked="checked"';?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input style="float:left;" onClick="javascript:document.getElementById('selzone2').checked=true; isChecked(this.value, this.checked); " type="checkbox" id="<?php echo $zone;?>" value="<?php echo $zone;?>" name="<?php echo $zone;?>" <?php echo $checked;?>/><label style="float:left; margin-left:5px;" for="<?php echo $zone;?>">
			<?php if(@in_array($zone, $the_zones_positions)) { ?>
			<?php echo $zone;?>
			<?php } else {echo "<span style='color: #FF0000;'>".$zone."</span>";}?>
			</label><div class="clearfix"></div>
		<?php } ?>
		</td>
		</tr>
		
		</table>
		<input type="button" onclick="<?php echo $link;?>" value="<?php echo JText::_("SAVE_BUTTON_GRBX");?>" class="btn btn-primary" name="save_this"/>
		
		<input type="hidden" name="tid" value="<?php echo $package->tid;?>" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="allzones" id="allzones" value="All Zones" />
		
		<!-- here we keep what ZONES are selected -->
		<input type="hidden" name="boxchecked_id" id="boxchecked_id" value="" />
		
		<input type="hidden" name="published" value="1<?php //echo $published?>" />
		<input type="hidden" name="controller" value="adagencyPackages" />
        </form>