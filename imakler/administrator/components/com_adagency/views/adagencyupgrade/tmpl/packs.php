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

?>

<?php
	$packs = $this->packs;
	$zones = $this->zones;
	$lists = $this->lists;
	$configs = $this->configs;
	$document = & JFactory::getDocument();
	$document->addStyleSheet('components/com_adagency/css/upgrade.css');
	$document->addStyleSheet('components/com_adagency/css/zone.css');
	include_once('components/com_adagency/includes/js/upgrade2.php');
?>
<style>
.upgrade_table tbody td { border:none; }
</style>
<div class="title_one"><?php echo JText::_('ADAG_UPG_WIZ'); ?></div><br />
<div class="title_two"><?php echo JText::_('ADAG_UPG_WIZ_S2'); ?></div>

<form name="adminForm" id="adminForm" method="post">
	<?php
		if(!isset($packs)||($packs == NULL)||(count($packs)==0)){
			echo "<span style='font-weight:bold;font-size:14px;'>".JText::_('ADAG_NO_PACKS')."</span>";
			echo '<p /><input id="upgrade_button" type="button" class="upgrade_button" value="'.JText::_('ADAG_CONTINUE').' >>" />';
		} else {
	?>
    <table class="upgrade_table" style="width:45%;" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?php echo JText::_('BUY_PACKNAME'); ?></th>
                <th><?php echo JText::_('ADAG_CHOOSEZONES'); ?></th>
            </tr>
        </thead>
        <tbody>
        	<?php
				foreach($packs as $pack){
					$new_z = str_replace("replaceable","pack[".$pack->tid."][]",$lists['zones']);
					echo "<tr><td valign='top'>".$pack->description."</td><td valign='top' class='selectable'>".$new_z."</td></tr>";
					echo "<tr><td colspan='2'><hr /></td></tr>";
				}
			?>
            <tr id="upgrade_button">
                <td colspan="4" align="right"><input type="button" class="upgrade_button" value="<?php echo JText::_('ADAG_CONTINUE'); ?> >>" /></td>
            </tr>
        </tbody>
    </table>
    <?php } ?>
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="controller" value="adagencyUpgrade" />
	<input type="hidden" name="task" value="upgradepack" />
</form>
