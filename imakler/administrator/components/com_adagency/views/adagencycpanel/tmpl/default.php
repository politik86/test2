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

	$rezultat = $this->rezultat;
	$total_o = $this->total_o;
	$total_b = $this->total_b;
	$total_c = $this->total_c;
	$my	= JFactory::getUser();
	$document = JFactory::getDocument();
	$document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/ad_agency.css');
    $document->addStyleSheet(JURI::root().'components/com_adagency/includes/css/adagency_template.css');
	$itemid = $this->itemid;
	if($itemid->cpn != 0) { $Itemid = "&Itemid=" . intval($itemid->cpn); } else { $Itemid = NULL; }
    if($itemid->ads != 0) { $Itemid_ads = "&Itemid=" . intval($itemid->ads); } else { $Itemid_ads = NULL; }
    if($itemid->adv != 0) { $Itemid_adv = "&Itemid=" . intval($itemid->adv); } else { $Itemid_adv = NULL; }
    if($itemid->cmp != 0) { $Itemid_cmp = "&Itemid=" . intval($itemid->cmp); } else { $Itemid_cmp = NULL; }
    if($itemid->pkg != 0) { $Itemid_pkg = "&Itemid=" . intval($itemid->pkg); } else { $Itemid_pkg = NULL; }
    if($itemid->ord != 0) { $Itemid_ord = "&Itemid=" . intval($itemid->ord); } else { $Itemid_ord = NULL; }
    if($itemid->rep != 0) { $Itemid_rep = "&Itemid=" . intval($itemid->rep); } else { $Itemid_rep = NULL; }
?>
<div id="dashboard">
<div class="page-title">
<h2><?php echo JText::_("AD_ADV_CPANEL"); ?></h2><!--end page title-->
</div>
<div class="adg_row">
	<div class="adg_cell span12">
		<div><div>
		<form class="form-horizontal adg_controlpanel" method="post" name="adminForm" id="adminForm">
			<div class="adg_row">
				<div class="adg_cell span3">
					<div><div>
						<a class="items_adg"  href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid[]=<?php echo intval($my->id) . $Itemid_adv;?>">
			                <i class="fa fa-user"></i>
			                <span><?php echo JText::_('AD_CP_PROFILE'); ?></span>
			            </a>
		            </div></div>
	            </div>
	            <div class="adg_cell span3">
	            	<div><div>
			            <a class="items_adg" href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>">
			                <i class="fa fa-bars"></i>
			                <span><?php echo JText::_('AD_CP_ADS'); ?> (<?php echo $total_b; ?>)</span>
			            </a>
		            </div></div>
				 </div>
	            <div class="adg_cell span3">
	            	<div><div>
						<a class="items_adg" href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>">
			                <i class="fa fa-shopping-cart"></i>
			                <span><?php echo JText::_('AD_CP_ORDERS'); ?> (<?php echo $total_o; ?>)</span>
			            </a>
		            </div></div>
	            </div>
	            <div class="adg_cell span3">
	            	<div><div> 
			            <a class="items_adg" href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners<?php echo $Itemid_ads; ?>">
			                <i class="fa fa-plus"></i>
			                <span><?php echo JText::_('ADAG_ADD_NB'); ?></span>
			            </a>
		            </div></div>
	            </div>
            </div>
            <div class="adg_row">
	            <div class="adg_cell span3">
	            	<div><div>
			            <a class="items_adg" href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>">
			                <i class="fa fa-bar-chart-o"></i>
			                <span><?php echo JText::_('AD_CP_REPORTS'); ?></span>
			            </a>
		            </div></div>
	            </div>
	            <div class="adg_cell span3">
	            	<div><div>
			            <a class="items_adg" href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>">
			                <i class="fa fa-calendar-o"></i>
			                <span><?php echo JText::_('AD_CP_CMPS'); ?> (<?php echo $total_c; ?>)</span>
			            </a>
		            </div></div>
	            </div>
	            <div class="adg_cell span3">
	            	<div><div>
			            <a class="items_adg" href="index.php?option=com_adagency&controller=adagencyPackages<?php echo $Itemid_pkg; ?>">
			                <i class="fa fa-gear"></i>
			                <span><?php echo JText::_('VIEWDSADMINPACKAGES'); ?></span>
			            </a>
		            </div></div>
	            </div>
	            <div class="adg_cell span3">
	            	<div><div>
			            <a class="items_adg" href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0<?php echo $Itemid_cmp; ?>">
			                <i class="fa fa-plus-square-o"></i>
			                <span><?php echo JText::_('ADAG_ADD_NC'); ?></span>
			            </a>
		            </div></div>
	            </div>
	         </div>
		</form>
		</div></div>
	</div>
</div>
</div>