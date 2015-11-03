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
$my = JFactory::getUser();
$itemid = $this->itemid;
if($itemid->cpn != 0) { $Itemid = "&Itemid=" . intval($itemid->cpn); } else { $Itemid = NULL; }
if($itemid->ads != 0) { $Itemid_ads = "&Itemid=" . intval($itemid->ads); } else { $Itemid_ads = NULL; }
if($itemid->adv != 0) { $Itemid_adv = "&Itemid=" . intval($itemid->adv); } else { $Itemid_adv = NULL; }
if($itemid->cmp != 0) { $Itemid_cmp = "&Itemid=" . intval($itemid->cmp); } else { $Itemid_cmp = NULL; }
if($itemid->pkg != 0) { $Itemid_pkg = "&Itemid=" . intval($itemid->pkg); } else { $Itemid_pkg = NULL; }
if($itemid->ord != 0) { $Itemid_ord = "&Itemid=" . intval($itemid->ord); } else { $Itemid_ord = NULL; }
if($itemid->rep != 0) { $Itemid_rep = "&Itemid=" . intval($itemid->rep); } else { $Itemid_rep = NULL; }

?>

<!-- Dashboard Container -->
<div class="ada-dashboard">
  <div class="ada-dashboard-heading">
    <!-- Dashboard title -->
    <h2 class="ada-dashboard-title uk-h2"><?php echo JText::_("AD_ADV_CPANEL"); ?></h2>
  </div>
  <form class="uk-form ada-dashboard-form" method="post" name="adminForm" id="adminForm">
    <!-- Grid -->
    <ul class="uk-grid uk-grid-match uk-grid-medium uk-grid-width-medium-1-4 uk-grid-width-small-1-2" data-uk-grid-match="{target:'.uk-panel'}">
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- PROFILE -->
          <a href="index.php?option=com_adagency&controller=adagencyAdvertisers&task=edit&cid=<?php echo intval($my->id) . $Itemid_adv;?>">
            <i class="uk-icon-user uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('AD_CP_PROFILE'); ?>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- MY ADS -->
          <a href="index.php?option=com_adagency&controller=adagencyAds<?php echo $Itemid_ads; ?>">
            <i class="uk-icon-bars uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('AD_CP_ADS'); ?> <span class="ada-dashboard-box-badge"><?php echo $total_b; ?></span>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- MY ORDERS -->
          <a href="index.php?option=com_adagency&controller=adagencyOrders<?php echo $Itemid_ord; ?>">
            <i class="uk-icon-cart-arrow-down uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('AD_CP_ORDERS'); ?> <span class="ada-dashboard-box-badge"><?php echo $total_o; ?></span>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- ADD BANNERS & ADS -->
          <a href="index.php?option=com_adagency&controller=adagencyAds&task=addbanners<?php echo $Itemid_ads; ?>">
            <i class="uk-icon-plus-square uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('ADAG_ADD_NB'); ?>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- REPORTS -->
          <a href="index.php?option=com_adagency&controller=adagencyReports<?php echo $Itemid_rep; ?>">
            <i class="uk-icon-area-chart uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('AD_CP_REPORTS'); ?>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- MY CAMPAIGNS -->
          <a href="index.php?option=com_adagency&controller=adagencyCampaigns<?php echo $Itemid_cmp; ?>">
            <i class="uk-icon-calendar uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('AD_CP_CMPS'); ?> <span class="ada-dashboard-box-badge"><?php echo $total_c; ?></span>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- PACKAGES -->
          <a href="index.php?option=com_adagency&controller=adagencyPackages<?php echo $Itemid_pkg; ?>">
            <i class="uk-icon-gear uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('VIEWDSADMINPACKAGES'); ?>
            </h3>
          </a>
        </div>
      </li>
      <li>
        <div class="uk-panel uk-panel-box ada-dashboard-box">
          <!-- ADD CAMPAIGN -->
          <a href="index.php?option=com_adagency&controller=adagencyCampaigns&task=edit&cid=0<?php echo $Itemid_cmp; ?>">
            <i class="uk-icon-plus uk-icon-large"></i>
            <h3 class="uk-panel-title">
              <?php echo JText::_('ADAG_ADD_NC'); ?>
            </h3>
          </a>
        </div>
      </li>
    </ul>
  </form>
</div>
