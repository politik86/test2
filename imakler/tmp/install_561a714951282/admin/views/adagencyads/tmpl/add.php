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

$camp_id = JRequest::getVar("camp_id", "");
if(intval($camp_id) != 0){
	$camp_id = "&camp_id=".intval($camp_id);
}
else{
	$camp_id = "";
}

?>
<div id="add_ads_back">
	
<div class="page-title">
<h2><?php echo JText::_("ADAG_WHAT_BANNER"); ?></h2>
</div>

<div class="adg_row">
	<div class="adg_cell span12">	
		<ul id="backend_ads">
			<li><a href="index.php?option=com_adagency&controller=adagencyStandard&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-picture-o adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDSTANDARD') ?></a></li>
			<li><a href="index.php?option=com_adagency&controller=adagencyAdcode&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-building-o adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDADCODE') ?></a></li>
			<li><a href="index.php?option=com_adagency&controller=adagencyTextlink&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-font adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDTEXTLINK') ?></a></li>
			<li><a href="index.php?option=com_adagency&controller=adagencyFlash&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-flash adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDFLASH') ?></a></li>
			<li><a href="index.php?option=com_adagency&controller=adagencyTransition&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-arrows-h adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDTRANSITION') ?></a></li>
			<li><a href="index.php?option=com_adagency&controller=adagencyFloating&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-chain-broken adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDFLOATING') ?></a></li>
			<li><a href="index.php?option=com_adagency&controller=adagencyPopup&task=edit&cid[]=0<?php echo $camp_id; ?>"><span><i class="fa fa-square adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDPOPUP') ?></a></li>
            <li><a href="#" style="color:#CCCCCC;"><span><i class="fa fa-square adg_ico_img"></i></span><?php echo JText::_('VIEWTREEADDJOMSOCIAL') ?></a></li>
		</ul>	
	</div>
</div>
</div>