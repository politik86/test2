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

	$zone = $this->zone;
	$modul = $this->modul; 
	$lists = $this->lists;
	$positions = $this->positions;
	$orders2 = $this->orders2;
	$configs = $this->configs;
	$nullDate = 0;
	$sel_packages=$this->sel_packALL;
	$count_packs=count($this->sel_packALL);
	$advertiser_id = JRequest::getVar( 'advertiser_id', 0,'get','int' );
	$mosConfig_live_site  = str_replace("/administrator","",JURI::base());
	if (!isset($modul->position)) $modul->position = 'left';
	JHTML::_('behavior.combobox');
	$document = JFactory::getDocument();
	$document->addScript(JURI::base()."components/com_adagency/js/modal.js");
	$document->addStyleSheet(JURI::base()."components/com_adagency/css/modal.css");
?>

<script type="text/javascript" src="<?php echo JURI::base().'components/com_adagency/helpers/prototype-1.6.0.2.js' ?>"></script>
<?php include(JPATH_BASE."/components/com_adagency/includes/js/zone_box.php"); ?>	

<div style="float:right">
<?php 
	$link = "ajax_addzone(); ";
	?>
	<!-- <a onclick="<?php echo $link;?>" href="#">SAVE</a> -->
	<input type="button" onclick="<?php echo $link;?>" value="<?php echo JText::_('SAVE_BUTTON_GRBX'); ?>" class="button" name="save_this"/>
</div>		
<br /><br />				
	
<div>
 <form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset class="adminform">
	<legend><?php echo ($zone->zoneid > 0)?JText::_('VIEWZONEEDIT'):JText::_('VIEWZONENEW'); ?></legend>     
	<table class="admintable" border="0">
		<tr>
					<th colspan="2">
     				<?php echo JText::_('EDITZONEDETAILS'); ?>
					</th>
				<tr>
				<tr>
					<td width="100" align="left">
					<?php echo JText::_('EDITZONETITLE');?>:<font color="#ff0000">*</font>
					</td>
					<td>
					<input class="text_area" type="text" name="title" size="35" value="<?php if (@$_REQUEST['title']!="") { echo @$_REQUEST['title']; } else { echo $modul->title; } ?>" />
					</td>
				</tr>
				<tr>
					<td valign="top" align="left">
					<?php echo JText::_('ZONEPOSITION'); ?>:<font color="#ff0000">*</font>
					</td>
					<td valign="top">
					<div align="left">
					<select style="position:absolute; width:135px" id="fakeposition" class="combobox" name="fakeposition" onChange="document.getElementById('position').value = this.value;">
				  	<?php
						for ($i=0,$n=count($positions);$i<$n;$i++) {
							if (isset($modul->position) && $modul->position!=$positions[$i]) $sel='';
								else $sel='selected="selected"';
								echo '<option value="'.$positions[$i].'" '.$sel.'>'.$positions[$i].'</option>';
							}
							?>
					</select>
					
					</div>

					<div align="left">
					<input type="text" style="position:absolute;  width:115px"  id="position" name="position" value="<?php echo $modul->position;?>" >						
					</div>
					
					

					</td>
				</tr>
				<tr>
					<td valign="top" align="left">
					<?php echo JText::_('EDITZONEMORDER'); ?>:
					</td>
					<td>
					<script language="javascript" type="text/javascript"> 
					<!--
					writeDynaList( 'class="inputbox" name="ordering" id="ordering" size="1"', orders, originalPos, originalPos, originalOrder ); 
					//-->
					</script>
					</td>
				</tr>
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONEPUB'); ?>:
					</td>
					<td>
					<?php echo $lists['published']; ?>
					</td>
				</tr>
				<td valign="top">
					<?php echo JText::_('EDITZONESTITLE'); ?>:
					</td>
					<td>
					<?php echo $lists['showtitle']; ?>
					</td>
				</tr>
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONEADSZONE'); ?>:
					</td>
					<td>
					<?php echo $lists['adsinzone'].' '.JText::_('ZONEADS_ROWS');?> 
					<?php echo $lists['adsinzone_cols'].' '.JText::_('ZONEADS_COLS');?>
					</td>
				<tr>
					<td valign="top">
						<?php echo JText::_("ZONEPADDING");?>
					</td>
					<td>
						<?php echo $lists['cellpadding'];?>
					</td>
				</tr>
				
				</tr>
				<?php if ($modul->id) { ?>
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONEID'); ?>:
					</td>
					<td><span style="font-weight: bold;">
					<?php echo $modul->id; ?>
					</span>
					</td>
				</tr>
				<?php } ?>
		
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONE_ROTATE_BANNERS'); ?>:
					</td>
					<td>
					<?php echo $lists['rotatebanners']; ?>
					</td>
				</tr>	
				<tr>
					<td width="100" align="left">
					<?php echo JText::_('ZONE_ROTATING_TIME');?>:
					</td>
					<td>
					<input class="text_area" type="text" name="rotating_time" size="8" value="<?php if ($zone->rotating_time!=NULL) echo $zone->rotating_time; else echo "10000";?>" /> ms
					</td>
				</tr>	
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONE_ROTATE_RANDOMIZE'); ?>:
					</td>
					<td>
					<?php echo $lists['rotaterandomize']; ?>
					</td>
				</tr>											
				
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONE_SHOW_ADVERTISE_LINK'); ?>:
					</td>
					<td>
						<select name="show_adv_link" id="show_adv_link">
							<option value="0" <?php if ($zone->show_adv_link=='0') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_DONT'); ?></option>
							<option value="1" <?php if ($zone->zoneid == 0 || $zone->show_adv_link=='1') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_BOTTOM'); ?></option>
							<option value="2" <?php if ($zone->show_adv_link=='2') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_TOP'); ?></option>
							<option value="3" <?php if ($zone->show_adv_link=='3') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_SHOW_ADV_LINK_BOTTOMANDTOP'); ?></option>
						</select>
					</td>
				</tr>					
				
				<tr>
					<td valign="top">
					<?php echo JText::_('ZONE_LINK_SHOULD_TAKE_TO'); ?>:
					</td>
					<td>
						<select onChange="show_hide_url(this.value)" name="link_taketo" id="link_taketo">
							<option value="0" <?php if ($zone->link_taketo=='0') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_PACKAGES'); ?></option>
							<option value="1" <?php if ($zone->link_taketo=='1') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_REGISTRATION'); ?></option>
							<option value="2" <?php if ($zone->link_taketo=='2') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_URL'); ?></option>
							<option value="3" <?php if ($zone->zoneid == 0 || $zone->link_taketo=='3') echo 'selected="selected"'; ?>><?php echo JText::_('ZONE_LINK_TAKE_TO_OVERVIEW'); ?></option>
						</select>
						<?php 
								if ($zone->link_taketo != 2) 
									$style = 'style="display:none"'; 
								else
									$style = '';	
						?>
							<input id="taketo_url" name="taketo_url" <?php echo $style; ?> value="<?php if ($zone->taketo_url == '' || $zone->taketo_url == 'http://') echo 'http://' ; else echo $zone->taketo_url; ?>" size="50">
					</td>
				</tr>
				<tr>
					<td><?php echo JText::_('AD_CHECK_ZONES_TO_PACKS'); ?></td>
					<td><?php 
				 			$i=0;
				 			foreach($sel_packages as $pks) { 
				 		?>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="hidden" name="<?php echo "packs[".$pks->tid."]";?>" value="0" /><input id="selzone<?php echo $i;?>" onClick="javascript:document.getElementById('selzone<?php echo $i;?>').value=1;" type="checkbox" value="0" name="<?php echo "packs[".$pks->tid."]";?>" <?php if(isset($checked)) {echo $checked;}?>/><label for="<?php echo $pks->description;?>">
			<?php echo $pks->description;?>
			</label><br/>
		<?php $i++;} ?>
				 </td>
				</tr>
				<tr>				
				</table><br />
			<input type="button" onclick="<?php echo $link;?>" value="<?php echo JText::_("SAVE_BUTTON_GRBX");?>" class="button" name="save_this"/>
			</td>
						
			
			<td width="1%" valign="top">
		
			
		<input type="hidden" name="zoneid" value="<?php echo $modul->id;?>" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="controller" value="adagencyZones" />
				
        </form>
        </div>
        </td></tr>
        <tr>
        <td colspan="3">
        <br clear="all" />