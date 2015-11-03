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


	$camps = $this->camps;
	JHtml::_('behavior.framework',true);	
	JHTML::_('behavior.modal');

	$document = & JFactory::getDocument();
	include_once('components/com_adagency/includes/js/upgrade3.php');
	$document->addStyleSheet('components/com_adagency/css/upgrade.css');
	$document->addStyleSheet('components/com_adagency/css/zone.css');
?>
<style>
.upgrade_table tbody td { border:none; }
</style>
<div class="title_one"><?php echo JText::_('ADAG_UPG_WIZ'); ?></div><br />
<div class="title_two"><?php echo JText::_('ADAG_UPG_WIZ_S3'); ?></div>

<form name="adminForm" id="adminForm" method="post">
	<div id='camps'>
    	<?php
			if(!isset($camps)||($camps == NULL)||(count($camps)==0)){
				echo "<span style='font-weight:bold;font-size:14px;'>".JText::_('ADAG_NO_CAMPS')."</span>";
				//echo '<p /><input id="upgrade_button" type="button" class="upgrade_button" value="'.JText::_('ADAG_CONTINUE').' >>" />';
			} else {
				foreach($camps as $camp){
					echo "<strong>".JText::_("AD_CMP_CMPNAME")."</strong>: ".$camp->description."<br />";
					echo "<strong>".JText::_("NEWADADVERTISER")."</strong>: ".$camp->advertiser."<br />";
					echo "<input type='hidden' name='camp_ids[]' value='".$camp->id."' />";
					echo "<input type='hidden' name='cbrw[".$camp->id."]' value='".$camp->cbrw."' />";
					if(isset($camp->banners)&&(count($camp->banners)>0)){
						echo "<table class='adminform adsTable' style='width:700px;'><tbody>";
						echo '<tr><th style="text-align:center;">'.JText::_("AD_BANNER_ID").'</th>';
						echo '<th>'.JText::_("AD_BANNER_NAME").'</th><th>'.JText::_("VIEWADPREVIEW").'</th><th>'.JText::_('NEWADZONE').'</th><th style="text-align:center">'.JText::_("AD_BANNER_APPROVED").'</th><th style="text-align:center">'.JText::_("Add").'</th><th style="text-align:center">'.JText::_("Delete").'</th><th style="text-align:center;">'.JText::_("AD_BANNER_RW").'</th></tr>';
						$counter = 0;
						foreach($camp->banners as $banner){
							if($counter % 2 == 0){ $row_class = 'row0'; } else { $row_class = 'row1'; }
							$counter++;
							$this_add = NULL; $this_del = NULL;
							if(!isset($banner->relative_weighting)||($banner->relative_weighting == NULL )) {
								$banner->relative_weighting = 100;
								$this_add = '<input type="checkbox" value="'.$banner->id.'" name="bnrs[add]['.$camp->id.'][]" />';
							} else {
								$this_del = '<input type="checkbox" value="'.$banner->id.'" name="bnrs[del]['.$camp->id.'][]" />';
							}
							echo "<tr class='".$row_class."'><td style='text-align:center'>".$banner->id."</td><td>".$banner->title."</td><td><a class='modal' href='".JURI::root()."index.php?option=com_adagency&controller=adagencyAds&task=preview&tmpl=component&no_html=1&adid=".$banner->id."'>".JText::_('VIEWADPREVIEW')."</a></td><td>".$banner->zones."</td><td style='text-align:center'>".$banner->approved."</td><td style='text-align:center' class='addColumn'>".$this_add."</td><td style='text-align:center'>".$this_del."</td><td style='text-align:center'><input type='text' value='".$banner->relative_weighting."' maxlength='6' size='5' name='cmps[".$camp->id."][".$banner->id."][rw]' /></td></tr>";
						}
						echo "</tbody></table>";
					} else {
						echo JText::_("ADAG_NO_AV_ADS")."<br /><br />";
					}
				}
				echo '<p><input id="upgrade_button" type="button" class="upgrade_button" value="'.JText::_('ADAG_CONTINUE').' >>" /></p>';
			}
		?>
    </div>

	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="controller" value="adagencyUpgrade" />
	<input type="hidden" name="task" value="upgradecamp" />
</form>
