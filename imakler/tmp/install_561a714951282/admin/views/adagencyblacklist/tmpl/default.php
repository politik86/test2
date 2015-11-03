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

$black_list = $this->black_list;
$black_list = str_replace("||", "\n", $black_list);
?>

<form class="form-horizontal" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
	
    <div class="control-group">
        <label class="control-label">
            <?php echo JText::_('AD_BLCKLIST');?>
            <br/>
            <span style="font-size:11px; color:#999999;"><?php echo JText::_("AD_EACH_IP_LINE"); ?></span>
        </label>
        <div class="controls">
            <textarea name="blacklist" rows="10"><?php echo $black_list; ?></textarea>
            &nbsp;
            <span class="editlinktip hasTip" title="<?php echo JText::_('AD_EACH_IP_LINE_TIP'); ?>" >
            <img src="components/com_adagency/images/tooltip.png" border="0"/></span>
        </div>
    </div>
    
    <input type="hidden" name="option" value="com_adagency" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="controller" value="adagencyBlacklist" />
</form>