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


	$zones = $this->zones;
	$configs = $this->configs;
	$document = & JFactory::getDocument();
	$document->addStyleSheet('components/com_adagency/css/upgrade.css');
	$document->addStyleSheet('components/com_adagency/css/zone.css');
	include_once('components/com_adagency/includes/js/upgrade.php');
?>
<div class="title_one"><?php echo JText::_('ADAG_UPG_WIZ'); ?></div><br />
<div class="title_two"><?php echo JText::_('ADAG_UPG_WIZ_S1'); ?></div>

<form name="adminForm" id="adminForm" method="post">
    <table class="upgrade_table" cellpadding="0" cellspacing="0">
        <thead>
            <tr>
                <th><?php echo JText::_('ADAG_ZNAME'); ?></th>
                <th><?php echo JText::_('ZONEPOSITION'); ?></th>
                <th><?php echo JText::_('ADAG_SUP_AD_TYPES'); ?></th>
                <th><?php echo JText::_('ADAG_ADSIZE'); //."(".JText::_('ADAG_LEAVE_BLANK_FAS')." )" ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
                if(isset($zones)&&(is_array($zones))) {
                    foreach($zones as $zone) {
                        echo "<tr>";
                        echo "<td valign='top' class='ztitle'>".$zone->title."</td>";
                        echo "<td valign='top' class='zposition'>".$zone->position."</td>";
                        echo "<td>";
            ?>
                    <div class='sup_title'>
                        <?php echo JText::_('ADAG_SUP_TYPES'); ?>:
                    </div>
                    <div class='sup_banners'>
                            <input type="radio" name="bx_<?php echo $zone->id; ?>" value="1" /><?php echo JText::_('ADAG_BANNERS');?>
                            <div class="cBanners embed_container2">
                            	<input type="checkbox" class="standard" <?php if($configs->allowstand == '1') { echo "name='zone[".$zone->id."][adparams][standard]'"; } else { echo "readyonly=\"readonly\" disabled";} ?> value="1" /><?php echo JText::_('JAS_STANDART');?> <br />
                            	<input type="checkbox" class="affiliate" <?php if($configs->allowadcode == '1') { echo "name='zone[".$zone->id."][adparams][affiliate]'"; } else { echo "readyonly=\"readonly\" disabled";} ?> value="1" /><?php echo JText::_('JAS_BANNER_CODE');?> <br />
                            	<input type="checkbox" class="flash" <?php if($configs->allowswf == '1') { echo "name='zone[".$zone->id."][adparams][flash]'"; } else { echo "readyonly=\"readonly\" disabled";} ?> value="1" /><?php echo JText::_('JAS_FLASH');?> <br />
                            </div>
                            <input type="radio" <?php if($configs->allowtxtlink == '1') { echo "name='bx_".$zone->id."'";} else { echo "readyonly=\"readonly\" disabled";} ?> value="2" class="textad" /><?php echo JText::_('JAS_TEXT_LINK');?><br />
                            <input type="checkbox" class="textad2" style="display:none" name="zone[<?php echo $zone->id; ?>][adparams][textad]" value="1" />
                            <input type="radio" name="bx_<?php echo $zone->id; ?>" value="3" /><?php echo JText::_('ADAG_SPECIAL_BANNERS');?>
                            <div class="cSpecial embed_container2">
                                <input type="checkbox" class="popup" <?php if($configs->allowpopup == '1') { echo "name='zone[".$zone->id."][adparams][popup]'"; } else { echo "readyonly=\"readonly\" disabled";} ?> value="1" /><?php echo JText::_('JAS_POPUP'); ?> <br />
                                <input type="checkbox" class="transition" <?php if($configs->allowtrans == '1') { echo "name='zone[".$zone->id."][adparams][transition]'"; } else { echo "readyonly=\"readonly\" disabled";} ?> value="1" /><?php echo JText::_('JAS_TRANSITION'); ?> <br />
                                <input type="checkbox" class="floating" <?php if($configs->allowfloat == '1') { echo "name='zone[".$zone->id."][adparams][floating]'"; } else { echo "readyonly=\"readonly\" disabled";} ?> value="1" /><?php echo JText::_('JAS_FLOATING'); ?> <br />
                            </div>
                    </div>
            <?php
                        echo "</td>";
                        echo "<td valign='top'><div class='adsize_container'><input id='adsize_".$zone->id."' type='text' size='3' name='zone[".$zone->id."][adparams][width]' /> x <input type='text' size='3' name='zone[".$zone->id."][adparams][height]' />".JText::_('ADAG_ADSIZE_WHPX')."</div>
						<input type='checkbox' style='padding-left:0px;margin-left:0px;' class='any_size_check' />".JText::_('ADAG_ANYSIZE')."</td>";
                        echo "</tr>";
                    }
                }
            ?>
            <tr id="upgrade_button">
                <td colspan="4" align="right"><input type="button" class="upgrade_button" value="<?php echo JText::_('ADAG_CONTINUE'); ?> >>" /></td>
            </tr>
        </tbody>
    </table>
	<input type="hidden" name="option" value="com_adagency" />
	<input type="hidden" name="controller" value="adagencyUpgrade" />
	<input type="hidden" name="task" value="upgradezone" />
</form>
