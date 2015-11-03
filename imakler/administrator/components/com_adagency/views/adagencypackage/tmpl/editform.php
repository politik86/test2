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

JHtml::_('behavior.modal');
JHtml::_('behavior.tooltip');
$editable=NULL;
$data = $this->data;
$type = $this->type;
$lists = $this->lists;
$package=$this->package;
$the_zones_positions=$this->available_positions;
$zones=$this->newzones;
//$hide = implode('|',$zones);

$oldzones=array();
if (isset($package->zones)) $oldzones = explode('|',$package->zones);

$nullDate = 0;
$configs = $this->configs;
if (!isset($type)) $type='cpm';
if (!isset($package->type)) $package->type=$type;

$document = JFactory::getDocument();
// $document->addScript(JURI::base()."components/com_adagency/js/modal.js");
// $document->addStyleSheet(JURI::base()."components/com_adagency/css/modal.css");
$document->addStyleSheet(JURI::base()."components/com_adagency/css/joomla16.css");
$document->addStyleSheet(JURI::root()."components/com_adagency/includes/css/adag_tip.css");

?>

<?php include(JPATH_BASE."/components/com_adagency/includes/js/package.php"); ?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
            <div class="span12 pull-right">
            	<h2 class="pub-page-title">
					<?php echo ($package->tid > 0)?JText::_('VIEWPACKAGEEDIT'):JText::_('VIEWPACKAGENEW'); ?>
				</h2>
            </div>
            <a  class="modal adagency-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69673990">
            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
            <?php echo JText::_("COM_ADAGENCY_VIDEO_FREE_PACKAGE_EDIT"); ?>   
        </a>
        <br />
        <a class="modal adagency-video pull-right" rel="{handler: 'iframe', size: {x: 750, y: 435}}" href="index.php?option=com_adagency&controller=adagencyAbout&task=vimeo&id=69674001">
            <img src="<?php echo JURI::base(); ?>components/com_adagency/images/icon_video.gif" class="video_img" />
            <?php echo JText::_("COM_ADAGENCY_VIDEO_PACKAGE_EDIT"); ?>   
        </a>
     </div>
	</br>
	
     <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGEDESC'); ?> </label>
			<div class="controls">
			<input <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" name="description" size="40" maxlength="255" value="<?php echo $package->description; ?>" />
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWPACKAGEDESC_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	 </div>
	 
     <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGE_DESCRIPTION'); ?> </label>
			<div class="controls">
			<textarea name="pack_description" id="pack_description" wrap="physical" cols="40" rows="4"><?php echo stripslashes($package->pack_description);?></textarea>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWPACKAGE_DESCRIPTION_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	 </div>
   
   	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGETYPE'); ?> </label>
			<div class="controls">
			<?php echo $lists['type']; ?>
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWPACKAGETYPE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	 </div>
<?php if ($package->type=="fr" || $package->type=="in") { ?>	 
	 <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGETERMS'); ?> </label>
			<div class="controls">
			<?php echo $lists['amount']; ?>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $lists['duration'] ?>
			</div>
	 </div>
<?php } elseif ($package->type=="pc") { ?>
	 <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGETERMS'); ?> </label>
			<div class="controls">
			<input <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" name="quantity" <?php echo $this->disabled; ?> size="8" maxlength="8" value="<?php echo $package->quantity; ?>" />&nbsp;<?php echo JText::_('AGENCYCLICKS');?>
			</div>
	 </div>
<?php } else if ($package->type=="cpm") { ?>
	 <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGETERMS'); ?> </label>
			<div class="controls">
			<input <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" <?php echo $this->disabled; ?> name="quantity" size="8" maxlength="10" value="<?php if ($package->quantity) {echo $package->quantity;} else {echo "10000";} ?>" />
			<?php echo @$lists['quantity']; ?>&nbsp;<?php echo JText::_('AGENCYIMPRESSIONS');?>
			</div>
	 </div>
<?php } ?>
<div id="package_price">
	<div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGEPRICE'); ?> </label>
			<div class="controls">
			 <input type="radio" value="1" id="not_free" name="free" <?php if ($package->cost > 0) echo 'checked="checked"'; ?>>
                            <input onKeyDown="javascript:document.getElementById('not_free').checked=true;" <?php if ($package->tid && $editable) echo 'readonly="readonly" style="background-color:transparent;border:0px solid white;"';?> class="inputbox" type="text" id="cost" name="cost" style="width:40px;" maxlength="8" value="<?php echo $package->cost; ?>" />&nbsp;<?php echo $configs->currencydef; ?>&nbsp;&nbsp;
				<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWPACKAGEPRICE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	 </div>
	 <div class="control-group">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGE_PRICE_FREE'); ?> </label>
			<div class="controls">
			<input type="radio" value="0" name="free" id="is_free" <?php if ($package->cost == 0) echo 'checked="checked"'; ?> onClick="javascript:document.getElementById('cost').value=0;">
			<span class="lbl"></span>
			<span class="editlinktip hasTip" title="<?php echo JText::_('VIEWPACKAGE_PRICE_FREE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	 </div>
	  <div class="control-group">
			<label class="control-label"> <?php echo JText::_('ADAG_HIDE_AFTER'); ?> </label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="hide_after">
					<?php
						$no_checked = "";
						$yes_cheched = "";
						
						if($package->hide_after==1) {
							$yes_cheched = 'checked="checked"';
						}
						else{
							$no_checked = 'checked="checked"';
						}
					?>
					<input type="hidden" name="hide_after" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="hide_after">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_HIDE_AFTER_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
	 </div>
</div>
<div class="control-group">
			<label class="control-label"> <?php echo JText::_('AD_PACK_VIS'); ?> </label>
			<div class="controls">
				 <fieldset class="radio btn-group" id="visible">
					<?php
						$yes_cheched = "";
						
						if($package->visibility == 1)
							$yes_cheched = 'checked="checked"';
					?>
					<input type="hidden" name="visible" value="0">
					<input type="checkbox" <?php echo $yes_cheched; ?> value="1" class="ace-switch ace-switch-5" name="visible">
					<span class="lbl"></span>
				</fieldset>
				<span class="editlinktip hasTip" title="<?php echo JText::_('ADAG_PACK_VISIBLE_TIP'); ?>" >
				<img src="components/com_adagency/images/tooltip.png" border="0"/></span>
			</div>
</div>

<div class="control-group" id="all-zones">
			<label class="control-label"> <?php echo JText::_('VIEWPACKAGE_ZONES_INCLUDED'); ?> </label>
			<div class="controls">
				  <?php
            if(isset($zones)&&(is_array($zones)) && count($zones) > 0) {
                echo "<table width='100%' cellpadding='0' cellspacing='0' id='package_zones'>";
                foreach($zones as $zone){
                    if(in_array($zone->zoneid,$this->sel_zones)) { $chk = " checked=\"checked\" ";} else { $chk = NULL; }
                        echo "<tr><td>";
                        echo "<div class='pull-left'><input class=\"zlocations\" type=\"checkbox\" name=\"packz[]\" ".$chk." value=\"".$zone->zoneid."\" /><span class='lbl'></span></div>&nbsp;&nbsp;<label class='span9'>".$zone->z_title."</label><br />";
					    echo "</tr></td>";
                }
                echo "</table>";
            }
			elseif($package->type == "in"){
				echo '<div id="system-message-container">
						<div class="label label-important">
							<p>'.JText::_("ADAG_NOT_INVENTORY_ZONES_1")." ".'<a href="index.php?option=com_adagency&controller=adagencyZones&task=edit&cid[]=0">'.JText::_("ADAG_NOT_INVENTORY_ZONES_2").'</a>'." ".JText::_("ADAG_NOT_INVENTORY_ZONES_3")." ".'"'.JText::_("ADAG_NOT_INVENTORY_ZONES_6").'"'." ".JText::_("ADAG_NOT_INVENTORY_ZONES_7")." ".'<a href="index.php?option=com_adagency&controller=adagencyZones">'.JText::_("ADAG_NOT_INVENTORY_ZONES_4").'</a>'." ".JText::_("ADAG_NOT_INVENTORY_ZONES_5").'</p>
						</div>
					  </div>';
			}
        ?>
			</div>
</div>
		<input type="hidden" name="tid" value="<?php echo $package->tid;?>" />
		<input type="hidden" name="option" value="com_adagency" />
		<input type="hidden" name="task" value="edit" />
		<input type="hidden" name="allzones" value="<?php //echo $hide; ?>" />
		<input type="hidden" name="published" value="1<?php //echo $published?>" />
		<input type="hidden" name="controller" value="adagencyPackages" />
        </form>
